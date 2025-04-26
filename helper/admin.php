<?php

	defined('ABSPATH') || exit;


	function easytab_helper_sanitize_options($options) {

		foreach ($options as $key => $option) {

			if (is_array($options[$key])) {
				foreach ($options[$key] as $k => $opt) {
					$options[$key][$k]['list'] = sanitize_text_field($options[$key][$k]['list']);
					$options[$key][$k]['info'] = sanitize_textarea_field($options[$key][$k]['info']);
				}
			} else $options[$key] = sanitize_text_field($options[$key]);

		}

		return $options;

	}

	function easytab_helper_help_link($ai = null, $info = null) {

		if (($ai === null) || ($info === null)) return;

		if ($ai === 'chat_gpt') {

			switch ($info) {

				case 'ai-models':
					return 'https://platform.openai.com/docs/models';

				case 'how-generate-api-key':
					return 'https://platform.openai.com/api-keys';

				default:
					break;

			}

		}

		if ($ai === 'claude') {

			switch ($info) {

				case 'ai-models':
					return 'https://docs.anthropic.com/en/docs/about-claude/models';

				case 'how-generate-api-key':
					return 'https://console.anthropic.com/login?selectAccount=true&returnTo=%2Fsettings%2Fkeys%3F';

				default:
					break;

			}

		}

	}


	function easytab_helper_log($txt = '') {

		$option_name = 'easytab_debug_log';
		$log = get_option($option_name, '');
		$new_log_entry = gmdate('Y-m-d H:i:s') . ' - ' . $txt . "<br><br>" . $log;

		if (strlen($new_log_entry) > 512000) {
			$new_log_entry = gmdate('Y-m-d H:i:s') . ' - ' . $txt;
		}

		update_option($option_name, $new_log_entry);

	}