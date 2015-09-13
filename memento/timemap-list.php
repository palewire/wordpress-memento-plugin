<?php # -*- coding: utf-8 -*-
/**
 * The template for displaying timemap lists.
 */
?>
<<?php echo $timemap_url ?>>;rel="original",
 <<?php echo get_timemap_list_permalink($timemap_url) ?>>
   ; rel="self";type="application/link-format"
   ; from="<?php echo get_min_post_date_gmt($revision_list) ?> GMT"
   ; until="<?php echo get_max_post_date_gmt($revision_list) ?> GMT",
<?php $i=0; $len=count($revision_list); foreach($revision_list as $revision) :?>
   <<?php echo get_revision_permalink($post, $revision) ?>>
   ; rel="<?php if ($i == 0) echo 'first '; if ($i == $len-1) echo 'last ' ?>memento"; datetime="<?php echo $revision->post_date_gmt ?> GMT"<?php if ($i != $len-1) echo ",\r\n" ?>
<?php $i++; ?>
<?php endforeach; ?>
