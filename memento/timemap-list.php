<?php # -*- coding: utf-8 -*-
/**
 * The template for displaying timemap lists.
 */
?>
<<?php echo $timemap_url ?>>;rel="original",
 <<?php echo get_timemap_list_url($timemap_url) ?>>
   ; rel="self";type="application/link-format"
   ; from="{{ minimum_datetime|httpdate }}"
   ; until="{{ maximum_datetime|httpdate }}",
<?php $i=0; $len=count($revision_list); foreach($revision_list as $revision) :?>
   <{{ item.link }}>
   ; rel="<?php if ($i == 0) echo 'first '; if ($i == $len-1) echo 'last ' ?>memento"; datetime="{{ item.datetime|httpdate }}"<?php if ($i != $len-1) echo ",\r\n" ?>
<?php $i++; ?>
<?php endforeach; ?>
