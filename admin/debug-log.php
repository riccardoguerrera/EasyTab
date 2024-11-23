<?php

    defined('ABSPATH') || exit;

	function easytab_dl_page() {

		add_submenu_page(
			'easytab',
			__('Activity Log', 'easytab'),
			__('Activity Log', 'easytab'),
			'manage_options',
			'easytab_debug_log',
			'easytab_debug_log_page'
		);

	}

	add_action('admin_menu', 'easytab_dl_page');


	function easytab_debug_log_page() {

		$log = get_option('easytab_debug_log', '');

		?>

		<h2>Debug Log</h2>

		<div id="easytab-debug-log"><?php echo wp_kses_post($log); ?></div>

		<?php

	}