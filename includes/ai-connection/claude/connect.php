<?php

	defined('ABSPATH') || exit;

	
	function easytab_claude_connection($request_type, $prompt = null, $tab = null) {

		$options = get_option('easytab_settings');

		$api_key = array_key_exists('claude_ai_api_key', $options) ? $options['claude_ai_api_key'] : null;
		$model = array_key_exists('claude_ai_model', $options) ? $options['claude_ai_model'] : null;
		$output_preset_type = array_key_exists('output_preset_for_' . str_replace('-', '_', $tab) . '_tab_target_and_claude_ai'
			, $options) ? (intval($options['output_preset_for_' . str_replace('-', '_', $tab) . '_tab_target_and_claude_ai'
			])) : 0;
		$output_preset_params = get_option('easytab_claude_ai_output_preset');

		if (empty($request_type) || empty($prompt) || empty($api_key) || empty($model)) return;

		$headers = array(
			'X-Api-Key' => $api_key,
			'anthropic-version' => '2023-06-01',
			'Content-Type' => 'application/json'
		);

		if ($request_type == 'chat') {

			$url = 'https://api.anthropic.com/v1/messages';

			$data = array(
				'model' => $model,
				'messages' => array(array('role' => 'user', 'content' => $prompt)),
				'max_tokens' => $output_preset_params[$output_preset_type]['max_tokens'],
				'temperature' => $output_preset_params[$output_preset_type]['temperature'],
				'top_p' => $output_preset_params[$output_preset_type]['top_p'],
				'top_k' => $output_preset_params[$output_preset_type]['top_k'],
			);

			$response = wp_remote_post($url, array(
				'headers' => $headers,
				'body' => wp_json_encode($data),
				'timeout' => 30,
			));

			if (is_wp_error($response)) {
				easytab_helper_log('Wordpress Error');
				return null;
			}

			$body = wp_remote_retrieve_body($response);
			$json_response = json_decode($body, true);

			if (!empty($json_response) && !empty($json_response['error'])) {
				easytab_helper_log('API Request Error: ' . $json_response['error']['message']);
				return false;
			}

			return $json_response['content'][0]['text'];

		}

		return false;

	}