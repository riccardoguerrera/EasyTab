<?php

	/**
	 * Plugin Name: EasyTab
	 * Plugin URI: https://easytab.pro
	 * Description: EasyTab helps you optimize Woocommerce tabs with the support of artificial intelligence.
	 * Version: 1.0.0
	 * Author: rgwebdev
	 * Author URI: https://riccardoguerrera.dev
	 * Text Domain: easytab
	 * Domain Path: /languages
	 * Requires at least: 6.0
	 * Requires PHP: 7.4
	 * Copyright: Riccardo Guerrera - EasyTab
	 * License: GNU General Public License v3.0
	 * License URI: https://www.gnu.org/licenses/gpl.html
	 */


    defined('ABSPATH') || exit;


    if (!function_exists('is_plugin_active')) {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }


    define('EASYTAB_VERSION', '1.0.0');


    function easytab_check_woocommerce_dependency() {

        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            add_action('admin_notices', 'easytab_show_woocommerce_dependency_notice');
            deactivate_plugins(plugin_basename(__FILE__));
        }

    }

    function easytab_show_woocommerce_dependency_notice() {

    ?>
        <div class="error notice">
            <p><?php esc_html_e('The "EasyTab" plugin requires WooCommerce to work. WooCommerce is not active, so the plugin has been disabled.', 'easytab'); ?></p>
        </div>
    <?php

    }

    add_action('admin_init', 'easytab_check_woocommerce_dependency');


	function easytab_load_textdomain() {

		load_plugin_textdomain('easytab', false, dirname(plugin_basename(__FILE__ )). '/languages/');

	}

	add_action('plugins_loaded', 'easytab_load_textdomain');


	function easytab_set_english_language_as_fallback($mofile, $domain) {

		if ($domain !== 'easytab') {
			return $mofile;
		}

		$en_mofile = dirname($mofile) . '/' . $domain . '-en_US.mo';

		if (!file_exists($mofile) && file_exists($en_mofile)) {

			return $en_mofile;

		}

		return $mofile;

	}

	add_filter('load_textdomain_mofile', 'easytab_set_english_language_as_fallback', 10, 2);


	function easytab_check_and_register_option() {

        $ai = array(
            'chat_gpt'  => 'ChatGPT',
        );

        if (!get_option('easytab_ai')) {
            add_option('easytab_ai', $ai);
        }

    }

    register_activation_hook(__FILE__, 'easytab_check_and_register_option');


    function easytab_init_plugin() {

        $installed_version = get_option('easytab_version');

        if (!$installed_version) {
			easytab_install();
        } elseif (version_compare($installed_version, EASYTAB_VERSION, '<')) {
			easytab_update_plugin($installed_version);
        }

    }

    function easytab_install() {

        $default_ai = array(
            'chat_gpt'  => 'ChatGPT',
        );

        add_option('easytab_ai', $default_ai);

        add_option('easytab_version', EASYTAB_VERSION);

    }

    function easytab_update_plugin($installed_version) {

        if (version_compare($installed_version, EASYTAB_VERSION, '<')) {

            $updated_ai = array(
                'chat_gpt'  => 'ChatGPT',
            );

            update_option('easytab_ai', $updated_ai);

        }

        update_option('easytab_version', EASYTAB_VERSION);

    }

    add_action('init', 'easytab_init_plugin');


    /**/

	function easytab_crb_load() {

        define('EASYTAB_PATH', plugin_dir_path(__FILE__));
        define('EASYTAB_URL', plugin_dir_url(__FILE__));

        require_once('vendor/autoload.php');

        require_once 'helper/log.php';
        require_once 'helper/woocommerce.php';
        require_once 'helper/admin.php';
        require_once 'admin/settings-page.php';
        require_once 'admin/meta-box.php';
        require_once 'admin/debug-log.php';
        require_once 'includes/easytab-content.php';
        require_once 'includes/ai-connection/chat-gpt/connect.php';

	}

	add_action('plugins_loaded', 'easytab_crb_load');


	function easytab_script($hook) {

		if ($hook !== 'toplevel_page_easytab') return;

		wp_enqueue_script('easytab', plugins_url('assets/js/easytab.js', __FILE__), array('jquery'), '1.0', true);
		wp_enqueue_style('easytab', plugins_url('assets/css/easytab.css', __FILE__), null, '1.0');

	}

	add_action('admin_enqueue_scripts', 'easytab_script');