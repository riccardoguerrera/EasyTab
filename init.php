<?php

	defined('ABSPATH') || exit;


	function easytab_load() {

		require_once 'helper/prompt.php';
		require_once 'helper/woocommerce.php';
		require_once 'helper/admin.php';
		require_once 'admin/settings-page.php';
		require_once 'admin/meta-box.php';
		require_once 'admin/debug-log.php';
		require_once 'includes/prompt.php';
		require_once 'includes/ai-connection/chat-gpt/connect.php';
		require_once 'includes/ai-connection/claude/connect.php';

	}

	add_action('plugins_loaded', 'easytab_load');


	function easytab_load_textdomain() {

		load_plugin_textdomain(EASYTAB_DOMAIN, false, basename(dirname(EASYTAB_FILE)) . '/languages');

	}

	add_action('plugins_loaded', 'easytab_load_textdomain');


	function easytab_set_english_language_as_fallback($mofile, $domain) {

		if ($domain !== EASYTAB_DOMAIN) {
			return $mofile;
		}

		$en_mofile = EASYTAB_PATH . 'languages/' . $domain . '-en_US.mo';

		if (!file_exists($mofile) && file_exists($en_mofile)) {
			return $en_mofile;
		}

		return $mofile;

	}

	add_filter('load_textdomain_mofile', 'easytab_set_english_language_as_fallback', 10, 2);


	function easytab_check_and_register_option() {

		$ai = array(
			'chat_gpt' => 'ChatGPT',
			'claude' => 'Claude'
		);

		$ai_models = array(
			'chat_gpt' => array(
				'gpt-4o' => 'GPT-4o',
				'gpt-4o-mini' => 'GPT-4o-mini',
				'gpt-4-turbo' => 'GPT-4-turbo',
			),
			'claude' => array(
				'claude-3-5-sonnet-20241022' => 'Claude 3.5 Sonnet'
			)
		);

		$chat_gpt_ai_output_preset = array(
			array(
				'temperature' => 0.8,
				'top_p' => 0.9,
				'frequency_penalty' => 0.3,
				'presence_penalty' => 0.6
			),
			array(
				'temperature' => 0.3,
				'top_p' => 0.7,
				'frequency_penalty' => 0.5,
				'presence_penalty' => 0.2
			),
			array(
				'temperature' => 0.6,
				'top_p' => 0.85,
				'frequency_penalty' => 0.4,
				'presence_penalty' => 0.4
			)
		);

		$claude_ai_output_preset = array(
			array(
				'max_tokens' => 8192,
				'temperature' => 0.9,
				'top_p' => 0.95,
				'top_k' => 50
			),
			array(
				'max_tokens' => 8192,
				'temperature' => 0.3,
				'top_p' => 0.7,
				'top_k' => 25
			),
			array(
				'max_tokens' => 8192,
				'temperature' => 0.7,
				'top_p' => 0.85,
				'top_k' => 40
			)
		);

		if (!get_option('easytab_ai')) {
			add_option('easytab_ai', $ai);
		} else {
			update_option('easytab_ai', $ai);
		}

		if (!get_option('easytab_ai_models')) {
			add_option('easytab_ai_models', $ai_models);
		} else {
			update_option('easytab_ai_models', $ai_models);
		}

		if (!get_option('easytab_chat_gpt_ai_output_preset')) {
			add_option('easytab_chat_gpt_ai_output_preset', $chat_gpt_ai_output_preset);
			add_option('easytab_claude_ai_output_preset', $claude_ai_output_preset);
		} else {
			update_option('easytab_chat_gpt_ai_output_preset', $chat_gpt_ai_output_preset);
			update_option('easytab_claude_ai_output_preset', $claude_ai_output_preset);
		}
		
	}

	register_activation_hook(EASYTAB_FILE, 'easytab_check_and_register_option');


	function easytab_set_register_option() {

		if (isset($_GET['page']) && $_GET['page'] === 'easytab') {

			easytab_check_and_register_option();

		}

	}

	add_action('admin_menu', 'easytab_set_register_option');


	function easytab_init_plugin() {

		$installed_version = get_option('easytab_version');

		if (!$installed_version) {
			easytab_install();
		} elseif (version_compare($installed_version, EASYTAB_VERSION, '<')) {
			easytab_update_plugin($installed_version);
		}

	}

	add_action('init', 'easytab_init_plugin');


	function easytab_install() {

		$default_ai = array(
			'chat_gpt' => 'ChatGPT',
			'claude' => 'Claude',
		);

		$default_ai_models = array(
			'chat_gpt' => array(
				'gpt-4o' => 'GPT-4o',
				'gpt-4o-mini' => 'GPT-4o-mini',
				'gpt-4-turbo' => 'GPT-4-turbo',
			),
			'claude' => array(
				'claude-3-5-sonnet-20241022' => 'Claude 3.5 Sonnet'
			)
		);

		$default_chat_gpt_ai_output_preset = array(
			array(
				'temperature' => 0.8,
				'top_p' => 0.9,
				'max_completion_tokens' => 1.5,
				'frequency_penalty' => 0.3,
				'presence_penalty' => 0.6,
			),
			array(
				'temperature' => 0.3,
				'top_p' => 0.7,
				'max_completion_tokens' => 3.5,
				'frequency_penalty' => 0.5,
				'presence_penalty' => 0.2,
			),
			array(
				'temperature' => 0.6,
				'top_p' => 0.85,
				'max_completion_tokens' => 2,
				'frequency_penalty' => 0.4,
				'presence_penalty' => 0.4,
			)
		);

		$default_claude_ai_output_preset = array(
			array(
				'max_tokens' => 8192,
				"temperature" => 0.9,
				"top_p" => 0.95,
				"top_k" => 50
			),
			array(
				'max_tokens' => 8192,
				"temperature" => 0.3,
				"top_p" => 0.7,
				"top_k" => 25
			),
			array(
				'max_tokens' => 8192,
				"temperature" => 0.7,
				"top_p" => 0.85,
				"top_k" => 40
			)
		);

		add_option('easytab_ai', $default_ai);

		add_option('easytab_ai_models', $default_ai_models);
		add_option('easytab_chat_gpt_ai_output_preset', $default_chat_gpt_ai_output_preset);
		add_option('easytab_claude_ai_output_preset', $default_claude_ai_output_preset);

		add_option('easytab_version', EASYTAB_VERSION);

	}


	function easytab_update_plugin($installed_version) {

		$updated_ai = array(
			'chat_gpt' => 'ChatGPT',
			'claude' => 'Claude'
		);

		$updated_ai_models = array(
			'chat_gpt' => array(
				'gpt-4o' => 'GPT-4o',
				'gpt-4o-mini' => 'GPT-4o-mini',
				'gpt-4-turbo' => 'GPT-4-turbo',
			),
			'claude' => array(
				'claude-3-5-sonnet-20241022' => 'Claude 3.5 Sonnet'
			),
		);

		$updated_chat_gpt_ai_output_preset = array(
			array(
				'temperature' => 0.8,
				'top_p' => 0.9,
				'frequency_penalty' => 0.3,
				'presence_penalty' => 0.6,
			),
			array(
				'temperature' => 0.3,
				'top_p' => 0.7,
				'frequency_penalty' => 0.5,
				'presence_penalty' => 0.2,
			),
			array(
				'temperature' => 0.6,
				'top_p' => 0.85,
				'frequency_penalty' => 0.4,
				'presence_penalty' => 0.4,
			)
		);

		$updated_claude_ai_output_preset = array(
			array(
				'max_tokens' => 8192,
				'temperature' => 0.9,
				'top_p' => 0.95,
				'top_k' => 50
			),
			array(
				'max_tokens' => 8192,
				'temperature' => 0.3,
				'top_p' => 0.7,
				'top_k' => 25
			),
			array(
				'max_tokens' => 8192,
				'temperature' => 0.7,
				'top_p' => 0.85,
				'top_k' => 40
			)
		);

		update_option('easytab_ai', $updated_ai);

		update_option('easytab_ai_models', $updated_ai_models);
		update_option('easytab_chat_gpt_ai_output_preset', $updated_chat_gpt_ai_output_preset);
		update_option('easytab_claude_ai_output_preset', $updated_claude_ai_output_preset);

		update_option('easytab_version', EASYTAB_VERSION);

	}


	function easytab_script($hook) {

		if ($hook === 'toplevel_page_easytab') {

			wp_enqueue_script('easytab', plugins_url('assets/js/easytab.js', EASYTAB_FILE), array('jquery'), EASYTAB_VERSION, true);
			wp_enqueue_style('easytab', plugins_url('assets/css/easytab.css', EASYTAB_FILE), null, EASYTAB_VERSION);

			wp_enqueue_script('easytab-pro', plugins_url('assets/js/easytab-pro.js', EASYTAB_FILE), array('jquery'), EASYTAB_VERSION, true);
			wp_enqueue_style('easytab-pro', plugins_url('assets/css/easytab-pro.css', EASYTAB_FILE), null, EASYTAB_VERSION);

		}

		if ($hook === 'easytab_page_easytab_debug_log') {

			wp_enqueue_style('easytab-dl', plugins_url('assets/css/easytab-dl.css', EASYTAB_FILE), null, EASYTAB_VERSION);

		}

	}

	add_action('admin_enqueue_scripts', 'easytab_script');