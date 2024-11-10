<?php

	/**
	 * EasyTab
	 */

	defined('ABSPATH') || exit;

	global $post;

    $tab_content = get_post_meta(get_the_ID(), 'easytab_description_tab', true);

    if (empty($tab_content)) {
		the_content();
	} else {
		echo esc_html($tab_content);
	}