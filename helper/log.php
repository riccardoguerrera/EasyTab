<?php

	defined('ABSPATH') || exit;


	function easytab_helper_log($txt = '') {

		$option_name = 'easytab_debug_log';
		$log = get_option($option_name, '');
		$new_log_entry = gmdate('Y-m-d H:i:s') . ' - ' . $txt . "<br><br>" . $log;

		if (strlen($new_log_entry) > 512000) {
			$new_log_entry = gmdate('Y-m-d H:i:s') . ' - ' . $txt;
		}

		update_option($option_name, $new_log_entry);

	}