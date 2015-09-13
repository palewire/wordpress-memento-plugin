<?php # -*- coding: utf-8 -*-


/**
 * Returns a link to the timemap link list given a post URL
 */
function get_timemap_list_url($post_url)
{
    $base_url = get_site_url() . "/timemap/";
    return $base_url . $post_url;
}