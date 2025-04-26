<?php

	defined('ABSPATH') || exit;


	$tab_content = get_post_meta(get_the_ID(), 'easytab_description_tab', true);


	if (empty($tab_content)) {
		the_content();
	} else {
		echo wp_kses_post($tab_content);
	}