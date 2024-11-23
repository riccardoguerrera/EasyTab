<?php

	defined('ABSPATH') || exit;


	function easytab_chatgpt_connection($request_type, $prompt = null) {

		$options = get_option('easytab_settings');
		$api_key = array_key_exists('chat_gpt_ai_api_key', $options) ? $options['chat_gpt_ai_api_key'] : null;

		if (empty($request_type) || empty($prompt) || empty($api_key)) return;

		$headers = array(
			'Authorization' => 'Bearer ' . $api_key,
			'Content-Type' => 'application/json',
		);

		if ($request_type == 'chat') {

			$url = 'https://api.openai.com/v1/chat/completions';
			$model = "gpt-4o";

			$data = array(
				'model' => $model,
				'messages' => array(array('role' => 'user', 'content' => $prompt)),
				'max_tokens' => 516,
				'temperature' => 1,
			);

			$response = wp_remote_post($url, array(
				'headers' => $headers,
				'body' => wp_json_encode($data),
				'timeout' => 30,
			));


			if (is_wp_error($response)) {
				easytab_helper_log('Error');
				return null;
			}

			$body = wp_remote_retrieve_body($response);
			$json_response = json_decode($body, true);

			if (!empty($json_response) && !empty($json_response['error'])) {
				easytab_helper_log('Error');
				return false;
			}

			easytab_helper_log($body);

			return $json_response["choices"][0]["message"]["content"];

		}

		return false;

	}