<?php

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

		$log = file_get_contents(EASYTAB_PATH . 'easytab-debug.log', true);

		?>

		<h2>Debug Log</h2>

		<div id="easytab-debug-log"><?php echo wp_kses_post($log); ?></div>

		<style>
            #easytab-debug-log {
				width: 50%;
				height: 400px;
				max-height: 400px;
				background-color: #fff;
				border: 1px solid lightgrey;
				overflow-y: scroll;
				margin-top: 30px;
			}
		</style>

		<?php

	}