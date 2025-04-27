<?php

	/**
	 * Plugin Name: EasyTab
	 * Description: EasyTab helps you optimize WooCommerce Tabs with the support of artificial intelligence.
	 * Version: 1.0.3
	 * Author: rgwebdev
	 * Author URI: https://riccardoguerrera.dev
	 * Text Domain: easytab
	 * Domain Path: /languages
	 * Requires at least: 6.0
	 * Requires PHP: 7.4
	 * Requires Plugins: woocommerce
	**/

	defined('ABSPATH') || exit;

	/**/

	if (!function_exists('is_plugin_active')) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$easytab_plugin_basename = plugin_basename(__FILE__);

	if ($easytab_plugin_basename === 'easytab/easytab.php') {

		if (!is_plugin_active('woocommerce/woocommerce.php')) {

			deactivate_plugins(plugin_basename(__FILE__));
			
			add_action('admin_notices', function () {

				?>

					<div class="error notice">
				
						<p>
					
							<?php 
				
								esc_html_e('The "EasyTab" plugin requires WooCommerce to work. WooCommerce is not active, so the plugin has been disabled.', 'easytab');
							?>
							
						</p>
				
					</div>
			
				<?php 
					
			});
	
		}

	}

	if (!defined('EASYTAB_VERSION')) {
		define('EASYTAB_VERSION', '1.0.3');
	}

	if (!defined('EASYTAB_FILE')) {
		define('EASYTAB_FILE', __FILE__);
	}
	
	if (!defined('EASYTAB_PATH')) {
		define('EASYTAB_PATH', plugin_dir_path(__FILE__));
	}
	
	if (!defined('EASYTAB_URL')) {
		define('EASYTAB_URL', plugin_dir_url(__FILE__));
	}
	
	if (!defined('EASYTAB_DOMAIN')) {
		define('EASYTAB_DOMAIN', 'easytab');
	}
	
	if (file_exists(EASYTAB_PATH . 'init.php')) {
		require_once EASYTAB_PATH . 'init.php';
	}
