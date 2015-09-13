<?php # -*- coding: utf-8 -*-
/**
 * The template for displaying timemap lists.
 */
?>
<<?php echo $timemap_url ?>>;rel="original",
 <<?php echo get_timemap_list_url($timemap_url) ?>>
   ; rel="self";type="application/link-format"
   ; from="{{ minimum_datetime|httpdate }}"
   ; until="{{ maximum_datetime|httpdate }}",{% for item in items %}
 <{{ item.link }}>
   ; rel="{% if item.first %}first {% endif %}{% if item.last %}last {% endif %}memento"; datetime="{{ item.datetime|httpdate }}"{% if not forloop.last %},{% endif %}{% endfor %}