<?php

	function easytab_helper_log($txt = '') {

		global $wp_filesystem;

		require_once (ABSPATH . '/wp-admin/includes/file.php');
		WP_Filesystem();

		$log_file_path = EASYTAB_PATH . 'easytab-debug.log';

		$file_size = filesize($log_file_path) / 1024;

		if ($file_size > 500) {
			$wp_filesystem->delete($log_file_path);
			$wp_filesystem->put_contents($log_file_path, '', FS_CHMOD_FILE);
		}

		if (!$wp_filesystem->exists($log_file_path)) $wp_filesystem->put_contents($log_file_path, '', FS_CHMOD_FILE);

		$log = $wp_filesystem->get_contents($log_file_path);
		$new_log_entry = gmdate('Y-m-d H:i:s') . ' - ' . $txt . "<br><br>" . $log;
		$wp_filesystem->put_contents($log_file_path, $new_log_entry, FS_CHMOD_FILE);

	}