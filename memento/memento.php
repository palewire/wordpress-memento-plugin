<?php # -*- coding: utf-8 -*-
/*
Plugin Name: Memento
Plugin URI:  http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A plugin for Wordpress web sites to enable the Memento framework for time-based access
Version:     0.0.1
Author:      Ben Welsh
Author URI:  http://palewi.re/who-is-ben-welsh/
License:     MIT
License URI: http://opensource.org/licenses/MIT

The MIT License (MIT)

Copyright (c) 2015 Ben Welsh

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

include "functions.php";


/**
 * Add a set of custom URLs to provide custom endpoints, redirects
 * and headers for the core memento features
 *
 * @since 0.0.1
 */
add_action( 'init', 'wp_memento_add_rewrites' );
function wp_memento_add_rewrites()
{
    // 1. The timemap list page
    add_rewrite_rule(
        '^timemap/(.*)',
        'index.php?timemap_url=$matches[1]',
        'top'
    );
    // 2. The timegate request portal
    add_rewrite_rule(
        '^timegate/?(.*)',
        'index.php?timegate_url=$matches[1]',
        'top'
    );
    // 3. The post revision detail page
    add_rewrite_endpoint('revision', EP_PERMALINK);
}


/**
 * Add variables introduced by the custom URLs to the Wordpress environment
 *
 * @since 0.0.1
 *
 * @param array $vars List of Wordpress variables
 * @return array The submitted list with custom memento variables appended
 */
add_filter( 'query_vars', 'wp_memento_rewrite_add_vars' );
function wp_memento_rewrite_add_vars( $vars )
{
    $vars[] = 'timemap_url';
    $vars[] = 'timegate_url';
    return $vars;
}


/**
 * Processes the custom memento variables to return timegate and timemap
 * responses
 *
 * @since 0.0.1
 */
add_action( 'template_redirect', 'wp_memento_catch_vars' );
function wp_memento_catch_vars()
{
    // Handle a timegate request
    if( get_query_var( 'timegate_url' ) )
    {
        // Get the requested URL and clean it up
        $timegate_url = get_query_var( 'timegate_url' );
        $timegate_url = str_replace( "http:/", "http://", $timegate_url );
        $timegate_url .= "/";

        // Pull the original post from the database
        $post_id = url_to_postid( $timegate_url );

        // If it doesn't exist, throw a 404 error
        if( $post_id == 0 )
        {
           include( get_query_template( '404' ) );
           exit;
        }

        // Get the requested memento datetime
        $headers = getallheaders();
        $accept_datetime = $headers["Accept-Datetime"];

        // If no datetime is provided, redirect to the most recent version
        if( $accept_datetime == '' )
        {
            // Do that here
            wp_redirect( $timegate_url );
            exit();
        }

        // Verify that the provided datetime is valid
        if(count(date_parse($accept_datetime)['errors']) > 0)
        {
            header( 'HTTP/1.1 400 BAD REQUEST' );
            exit();
        }

        // Parse the datetime string into a date aware object
        $accept_datetime = new DateTime($accept_datetime);

        // Query all of the revisions for this post
        $revisions = get_post_revisions($post_id);

        // Loop through all of the posts ...
        $revision_array = Array();
        foreach ($revisions as &$r) {
            // And for each one pull the datetime
            $date = new DateTime($r->post_date_gmt);
            // And compare it against the submitted memento request
            $diff = abs($date->getTimestamp() - $accept_datetime->getTimestamp());
            // Key it into a new array using that difference
            $revision_array[$diff] = $r;
        }
        // Resort the array so the one with the smallest difference is first
        ksort($revision_array);
        // Pull out that closest revision from array
        $nearest_revision = array_values($revision_array)[0];
        // Redirect the request to the detail page for that revision
        $permalink = get_revision_permalink(
            $nearest_revision->parent_post,
            $nearest_revision
        );
        wp_redirect( $permalink );
        exit();
    }
    // Handle a timemap list request
    if( get_query_var( 'timemap_url' ) )
    {
        // Get the timemap URL and clean it up
        $timemap_url = get_query_var( 'timemap_url' );
        $timemap_url = str_replace( "http:/", "http://", $timemap_url );
        $timemap_url .= "/";

        // Pull the original post from the database
        $post_id = url_to_postid( $timemap_url );

        // If it doesn't exist, throw a 404 error
        if ( $post_id == 0 )
        {
           include( get_query_template( '404' ) );
           exit;
        }

        // Render the timemap response
        $charset = get_option( 'blog_charset' );
        header( 'Content-Type: application/link-format; charset=' . $charset );
        $post = get_post( $post_id );
        $revision_list = get_post_revisions( $post_id );
        array_unshift( $revision_list, $post );
        include( 'timemap-list.php' );

        // Finish
        exit;
    }
}


/**
 * Allows the creation of detail pages for each post revision and
 * adds headers that signal the site supports the memento system.
 *
 * @since 0.0.1
 */
add_action( 'wp_head', 'wp_memento_add_headers' );
function wp_memento_add_headers() {
    if( get_query_var( 'revision' ) )
    {
        $revision_id = get_query_var( 'revision' );
        if ( wp_is_post_revision( $revision_id ) )
        {
            // Add Memento-Datetime header
            $revision = wp_get_post_revision( $revision_id );
            header( 'Momento-Datetime: ' . $revision->post_date_gmt . " GMT;" );
            // Add Link header that provides directions to the timemap
            // This still needs to include timegate directions as well
            $original_post = get_post( $revision->post_parent );
            $original_url = get_permalink( $original_post );
            $timemap_url = get_timemap_list_permalink( $original_url );
            $link_header = '<' . $original_url . '>; rel="original",';
            $link_header .= '<' . $timemap_url . '>; rel="timemap"; type="application/link-format"';
            header( 'Link: ' . $link_header, false );
        } else {
            if ( is_single( $revision_id ) )
            {
                // Do nothing
            } else {
               include( get_query_template( '404' ) );
               exit;
            }
        }
    }
}


/**
 * Allows the creation of detail pages for each post revision and
 * reformats elements of the template to publish content from older revisions
 *
 * @since 0.0.1
 *
 * @param string $content The post content prior to being published
 * @return string The post content that will actually be published, potentially from a past revision
 */
add_filter( 'the_content', 'wp_momento_content_filter' );
//add_filter('single_post_title', 'wp_momento_content_filter');
//add_filter('the_title', 'wp_momento_content_filter');
function wp_momento_content_filter( $content )
{
    if( is_singular() && get_query_var( 'revision' ) )
    {
        // Get the revision id
        $revision_id = get_query_var( 'revision' );
        // Verify that it is a revision
        if( wp_is_post_revision( $revision_id ) )
        {
            // Remove the filer to avoid triggering an infinite loop
            remove_filter( 'the_content', 'wp_momento_content_filter' );
            // Query this revision from the database
            $revision_id = get_query_var( 'revision' );
            $revision = wp_get_post_revision( $revision_id );
            // Render the content using this older data
            $rev_content = apply_filters( 'the_content', $revision->post_content );
            // Put the filter override back on so we can use it again
            add_filter( 'the_content', 'wp_momento_content_filter' );
            // Return the revision content
            return $rev_content;
        }
        // If this a normal post and not a revision
        // then nothing special should happen
        if( is_single( $revision_id ) )
        {
            return $content;
        }
        // If it's none of the above just return the normal content.
        // Through perhaps we should have this raise a 404 or something.
        return $content;
    } else {
        return $content;
    }
}
