<?php

	defined('ABSPATH') || exit;


	function easytab_dl_page() {

		add_submenu_page(
			'easytab',
			__('Activity Log', EASYTAB_DOMAIN),
			__('Activity Log', EASYTAB_DOMAIN),
			'manage_options',
			'easytab_debug_log',
			'easytab_debug_log_page'
		);

	}

	add_action('admin_menu', 'easytab_dl_page');

	
	function easytab_debug_log_page() {

		$log = get_option('easytab_debug_log', '');

		?>

		<img class="easytab-logo" src="<?php echo EASYTAB_URL . 'assets/img/easytab-logo.png' ?>" style="margin-left: -18px;">

		<h2>Debug Log</h2>

		<div id="easytab-debug-log"><?php echo wp_kses_post(wp_unslash($log)); ?></div>

		<?php

	}