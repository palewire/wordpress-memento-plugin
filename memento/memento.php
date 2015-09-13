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

# defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
include "functions.php";

add_action( 'init', 'wp_memento_add_rewrites' );
function wp_memento_add_rewrites()
{
    add_rewrite_rule(
        '^timemap/(.*)',
        'index.php?timemap_url=$matches[1]',
        'top'
    );
}

add_filter( 'query_vars', 'wp_memento_rewrite_add_vars' );
function wp_memento_rewrite_add_vars( $vars )
{
    $vars[] = 'timemap_url';
    return $vars;
}

add_action( 'template_redirect', 'wp_memento_catch_vars' );
function wp_memento_catch_vars()
{
    if(get_query_var( 'timemap_url' ))
    {
        # Get the timemap URL and clean it up
        $timemap_url = get_query_var( 'timemap_url' );
        $timemap_url = str_replace("http:/", "http://", $timemap_url);
        $timemap_url .= "/";

        # Pull the original post from the database
        $post_id = url_to_postid($timemap_url);

        # If it doesn't exist, throw a 404 error
        if ($post_id == 0) {
           include(get_query_template( '404' ));
           exit;
        }

        # Render the timemap response
        header(
            'Content-Type: application/link-format; charset=' . get_option('blog_charset')
        );
        include('timemap-list.php');
        exit;
    }
}