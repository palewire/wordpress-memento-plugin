<?php # -*- coding: utf-8 -*-


/**
 * Returns a link to the timemap link list given a post URL
 */
function get_timemap_list_permalink($post_url)
{
    $base_url = get_site_url() . "/timemap/";
    return $base_url . $post_url;
}

/**
 * Returns a list of all the published revisions of a post.
 */
function get_post_revisions($post_id)
{
    return wp_get_post_revisions($post_id);
}

/**
 * Returns a the timegate URL where the previous version of the
 * post is hosted.
 */
function get_revision_permalink($post, $revision) {
    $post_url = get_post_permalink($post);
    return $post_url . '&revision=' . $revision->ID;
}