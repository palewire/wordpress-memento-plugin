<?php # -*- coding: utf-8 -*-


/**
 * Returns a link to the timemap link list given a post URL
 *
 * @since 0.1
 *
 * @param string The URL for a post
 * @return string The URL for a timemap linking to past revisions of a post
 */
function get_timemap_list_permalink($post_url)
{
    $base_url = get_site_url() . "/timemap/";
    return $base_url . $post_url;
}

/**
 * Returns a list of all the published revisions of a post.
 *
 * @since 0.1
 *
 * @param integer The unique database identifier of a post
 * @return array A list of all revisions of the submitted post fit for republication
 */
function get_post_revisions($post_id)
{
    // Ultimately we may want to do more filtering here, excluding auto saved
    // posts for instance
    return wp_get_post_revisions($post_id);
}

/**
 * Returns a the timegate URL where the previous version of the
 * post is hosted.
 *
 * @since 0.1
 *
 * @param Post A post object from the database
 * @param Revision A revision object from the database
 * @return string The URL where the revision is available
 */
function get_revision_permalink($post, $revision)
{
    $post_url = get_post_permalink($post);
    return $post_url . '&revision=' . $revision->ID;
}

/**
 * Returns the maximum post_date_gmt from a list of posts or revisions
 *
 * @since 0.1
 *
 * @param array A list of post revisions
 * @return datetime The maximum datetime from the list
 */
function get_max_post_date_gmt($revision_list)
{
    $datetimes = array_map(function($revision) {
        return $revision->post_date_gmt;
    }, $revision_list);
    return max($datetimes);
}

/**
 * Returns the minimum post_date_gmt from a list of posts or revisions
 *
 * @since 0.1
 *
 * @param array A list of post revisions
 * @return datetime The minimum datetime from the list
 */
function get_min_post_date_gmt($revision_list)
{
    $datetimes = array_map(function($revision) {
        return $revision->post_date_gmt;
    }, $revision_list);
    return min($datetimes);
}