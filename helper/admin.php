<?php

	defined('ABSPATH') || exit;


	function easytab_sanitize_options($options) {

		foreach ($options as $key => $option) {
			$options[$key] = sanitize_text_field($options[$key]);
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

	}