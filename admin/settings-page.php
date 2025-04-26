<?php

	defined('ABSPATH') || exit;


	function easytab_options_page() {

		add_menu_page(
			__('EasyTab', EASYTAB_DOMAIN),
			__('EasyTab', EASYTAB_DOMAIN),
			'manage_options',
			'easytab',
			'easytab_option_page',
			'dashicons-admin-generic',
			50
		);

	}

	add_action('admin_menu', 'easytab_options_page');


	function easytab_option_page() {

		if (!current_user_can('manage_options')) {
			return;
		}

		?>

		<img class="easytab-logo" src="<?php echo EASYTAB_URL . 'assets/img/easytab-logo.png' ?>" style="margin-left: -18px;">

		<div class="wrap">
			
			<h1><?php esc_html(get_admin_page_title()); ?></h1>
			
			<form action="options.php" method="post" id="easytab-ai-prompt">

		<?php

		// Security field
		settings_fields('easytab_options');

		do_settings_sections('easytab');

		$ai = (get_option('easytab_ai')) ? get_option('easytab_ai') : null;

		if (empty($ai)) return;

		$wc_tabs = easytab_helper_get_woo_product_tabs();

		foreach ($ai as $ai_key => $ai_value) {

		?>

				<div class="ai-section <?php echo esc_html(str_replace('_', '-', $ai_key)); ?>" style="display: none;">

		<?php

			do_settings_sections('easytab_' . str_replace('-', '_', $ai_key));

		?>

					<div class="wc-tabs" style="margin-top: 50px;">

		<?php

			$default_open = true;

			foreach ($wc_tabs as $wc_tab_key => $wc_tab_value) {

		?>

						<button
							class="wc-tabs-link<?php echo esc_html($default_open) ? ' active' : ''; ?>"
							data-wctab="<?php echo esc_html(str_replace('_', '-', $wc_tab_key)); ?>">
							<?php echo esc_html($wc_tab_value['title']); ?>
						</button>

		<?php

				$default_open = false;

			}

		?>

					</div>

		<?php

			$default_open = true;

			foreach ($wc_tabs as $wc_tab_key => $wc_tab_value) {

		?>

					<div
						id="wc-tab-section-<?php echo esc_html(str_replace('_', '-', $wc_tab_key)); ?>-<?php echo esc_html(str_replace('_', '-', $ai_key)); ?>-ai"
						class="wc-tab-section <?php echo esc_html(str_replace('_', '-', $ai_key . ' ' . $wc_tab_key)); ?>"
						<?php echo (!$default_open) ? ' style="display: none;"' : '';?>>

		<?php

						do_settings_sections('easytab_' . str_replace('-', '_', $ai_key) . '_wc_' . str_replace('-', '_', $wc_tab_key) . '_tab');

		?>

					</div>

		<?php

				$default_open = false;

			}

		?>

				</div>

		<?php

		}

		submit_button(__('Save Settings', EASYTAB_DOMAIN));

		?>

			</form>
		
		</div>

		<?php

	}


	function easytab_settings_init() {

		register_setting('easytab_options', 'easytab_settings', 'easytab_helper_sanitize_options');

		$ai = (get_option('easytab_ai')) ? get_option('easytab_ai') : null;

		if (empty($ai)) return;

		$wc_tabs = easytab_helper_get_woo_product_tabs();

		add_settings_section(
			'easytab_section',
			__('AI Prompt Wizard', EASYTAB_DOMAIN),
			null,
			'easytab'
		);

		add_settings_field(
			'easytab_ai_field',
			__('Select AI', EASYTAB_DOMAIN),
			'easytab_ai_field_cb',
			'easytab',
			'easytab_section',
			array('ai' => $ai)
		);

		add_settings_field(
			'easytab_prompt_language_output_response_field',
			__('Select language', EASYTAB_DOMAIN),
			'easytab_prompt_language_output_response_field_cb',
			'easytab',
			'easytab_section',
			array('ai' => $ai)
		);

		foreach ($ai as $ai_key => $ai_value) {

			$ai_key = str_replace('-', '_', $ai_key);

			add_settings_section(
				'easytab_' . $ai_key . '_section',
				__('Generating the prompt for ', EASYTAB_DOMAIN) . $ai_value,
				null,
				'easytab_' . $ai_key
			);

			add_settings_field(
				'easytab_ai_' . $ai_key . '_api_key_field',
				__('Provide the API KEY', EASYTAB_DOMAIN),
				'easytab_ai_api_key_field_cb',
				'easytab_' . $ai_key,
				'easytab_' . $ai_key . '_section',
				array('ai' => $ai_key)
			);

			add_settings_field(
				'easytab_ai_' . $ai_key . '_model_field',
				__('AI Model Selection', EASYTAB_DOMAIN),
				'easytab_ai_model_field_cb',
				'easytab_' . $ai_key,
				'easytab_' . $ai_key . '_section',
				array('ai' => $ai_key)
			);

			add_settings_field(
				'easytab_max_words_for_' . $ai_key . '_ai_field',
				__('Max. words number', EASYTAB_DOMAIN),
				'easytab_generate_output_max_words_field_cb',
				'easytab_' . $ai_key,
				'easytab_' . $ai_key . '_section',
				array('ai' => $ai_key)
			);

			add_settings_field(
				'easytab_shop_target_description_for_' . $ai_key . '_ai_field',
				__('Shop Description', EASYTAB_DOMAIN),
				'easytab_shop_target_description_field_cb',
				'easytab_' . $ai_key,
				'easytab_' . $ai_key . '_section',
				array('ai' => $ai_key)
			);

			add_settings_field(
				'easytab_generate_semantic_html5_formatted_output_for_' . $ai_key . '_ai_field',
				__('HTML5 Output?', EASYTAB_DOMAIN),
				'easytab_generate_semantic_html5_formatted_output_field_cb',
				'easytab_' . $ai_key,
				'easytab_' . $ai_key . '_section',
				array('ai' => $ai_key)
			);

			add_settings_field(
				'easytab_product_taxonomies_list_for_' . $ai_key . '_ai_field',
				__('Taxonomies Selection', EASYTAB_DOMAIN),
				'easytab_product_taxonomies_list_field_cb',
				'easytab_' . $ai_key,
				'easytab_' . $ai_key . '_section',
				array('ai' => $ai_key)
			);

			foreach ($wc_tabs as $wc_tab_key => $wc_tab_value) {

				$wc_tab_key = str_replace('-', '_', $wc_tab_key);

				add_settings_section(
					'easytab_' . $ai_key . '_wc_' . $wc_tab_key . '_tab_section',
					'<span style="font-size: 16px;">' . __('Specific information for the Woocommerce Tab ', EASYTAB_DOMAIN) . '"' . $wc_tab_value['title'] . '"</span>',
					null,
					'easytab_' . $ai_key . '_wc_' . $wc_tab_key . '_tab'
				);

				add_settings_field(
					'easytab_specific_wc_' . $wc_tab_key . '_tab_instructions_for_' . $ai_key . '_ai_field',
					__('Additional Instructions', EASYTAB_DOMAIN),
					'easytab_specific_wc_tab_instructions_field_cb',
					'easytab_' . $ai_key . '_wc_' . $wc_tab_key . '_tab',
					'easytab_' . $ai_key . '_wc_' . $wc_tab_key . '_tab_section',
					array('ai' => $ai_key, 'wc_tab' => ['key' => $wc_tab_key, 'value' => $wc_tab_value['title']])
				);

				add_settings_field(
					'easytab_specific_wc_' . $wc_tab_key . '_tab_output_preset_for_' . $ai_key . '_ai_field',
					__('Output preset', EASYTAB_DOMAIN),
					'easytab_specific_wc_tab_output_preset_field_cb',
					'easytab_' . $ai_key . '_wc_' . $wc_tab_key . '_tab',
					'easytab_' . $ai_key . '_wc_' . $wc_tab_key . '_tab_section',
					array('ai' => $ai_key, 'wc_tab' => ['key' => $wc_tab_key, 'value' => $wc_tab_value['title']])
				);

				add_settings_field(
					'easytab_product_meta_list_for_' . $wc_tab_key . '_tab_target_and_' . $ai_key . '_ai_field',
					__('Product Meta Fields', EASYTAB_DOMAIN),
					'easytab_product_meta_list_field_cb',
					'easytab_' . $ai_key . '_wc_' . $wc_tab_key . '_tab',
					'easytab_' . $ai_key . '_wc_' . $wc_tab_key . '_tab_section',
					array('ai' => $ai_key, 'wc_tab' => ['key' => $wc_tab_key, 'value' => $wc_tab_value['title']])
				);

			}

		}

	}

	add_action('admin_init', 'easytab_settings_init');


	function easytab_ai_field_cb($args) {

		$ai = $args['ai'];

		$options = get_option('easytab_settings');

		?>

		<div>

			<select name="easytab_settings[ai]" id="ai-select">
				<option value="">-- <?php esc_html_e('AI available', EASYTAB_DOMAIN); ?> --</option>
				<?php foreach ($ai as $key => $value) { ?>
				<option value="<?php echo esc_html($key); ?>" <?php echo ((!empty($options['ai'])) && ($options['ai'] === $key)) ? 'selected' : ''; ?>>
					<?php echo esc_html($value); ?>
				</option>
				<?php } ?>
			</select>

		</div>

		<br>

		<details>
			<summary><?php esc_html_e('Choose your AI. ', EASYTAB_DOMAIN); ?></summary>
			<div class="info">
				<p><?php esc_html_e('Chat-GPT is a chat bot based on artificial intelligence and machine learning, developed by OpenAI and specialized in conversation with a human user. ', EASYTAB_DOMAIN); ?></p>
				<p><?php esc_html_e('Claude is a conversational AI assistant developed by Anthropic, which leverages advanced natural language processing and deep learning techniques. ', EASYTAB_DOMAIN); ?></p>
			</div>
		</details>

		<?php

	}


	function easytab_prompt_language_output_response_field_cb($args) {

		$options = get_option('easytab_settings');

		$languages = include_once(EASYTAB_PATH . '/vendor/umpirsky/language-list/data/' . get_locale() . '/language.php');

		if ($languages === false) $languages = include_once(EASYTAB_PATH . '/vendor/umpirsky/language-list/data/en_US/language.php');

		?>

		<div>

			<select name="easytab_settings[prompt_language_output_response]" id="prompt-language-output-response">
				<option value="">-- <?php esc_html_e('Languages', EASYTAB_DOMAIN); ?> --</option>
				<?php foreach ($languages as $language_key => $language_value) { ?>
				<option value="<?php echo esc_html($language_key); ?>" <?php echo ((!empty($options['prompt_language_output_response'])) && ($options['prompt_language_output_response'] === $language_key)) ? 'selected' : ''; ?>>
					<?php echo esc_html(ucfirst($language_value)); ?>
				</option>
				<?php } ?>
			</select>

		</div>

		<br>

		<details>
			<summary><?php esc_html_e('Select a language. ', EASYTAB_DOMAIN); ?></summary>
			<div class="info">
				<p><?php esc_html_e('The content will be generated by the AI in the selected language. ', EASYTAB_DOMAIN); ?></p>
			</div>
		</details>

		<?php

	}


	function easytab_ai_api_key_field_cb($args) {

		$options = get_option('easytab_settings');

		?>

		<div class="<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>">

			<input
				type="password"
				name="easytab_settings[<?php echo esc_html(str_replace('-', '_', $args['ai'])); ?>_ai_api_key]"
				id="<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>-ai-api-key"
				value="<?php echo !empty($options[str_replace('-', '_', $args['ai']) . '_ai_api_key']) ? esc_html($options[str_replace('-', '_', $args['ai']) . '_ai_api_key']) : ''; ?>"
				autocomplete="off"
				placeholder="<?php esc_html_e('AI API KEY', EASYTAB_DOMAIN); ?>"
			>

		</div>

		<br>

		<details>
			<summary><?php esc_html_e('Enter the API KEY for the chosen AI. ', EASYTAB_DOMAIN); ?></summary>
			<div class="info">
				<p><?php esc_html_e('An API KEY is a unique code used to authenticate and authorize access to a software API. It functions as a "password" that allows one to connect to the chosen service and interact with the AI model, ensuring that only authorized users can send requests and receive responses. ', EASYTAB_DOMAIN); ?></p>
				<p>
					<?php esc_html_e('To generate an API Key ', EASYTAB_DOMAIN); ?>
					<u>
						<a
							href="<?php echo esc_html(easytab_helper_help_link(str_replace('-', '_', $args['ai']), 'how-generate-api-key')); ?>"
							rel="nofollow"
							target="_blank">
							<?php esc_html_e('click here!', EASYTAB_DOMAIN); ?>
						</a>
					</u>
				</p>
			</div>
		</details>

		<?php

	}


	function easytab_ai_model_field_cb($args) {

		$options = get_option('easytab_settings');

		$ai_models = (get_option('easytab_ai_models')) ? get_option('easytab_ai_models') : null;

		if (empty($ai_models)) return;

		?>

		<div class="<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>">

			<select
				name="easytab_settings[<?php echo esc_html(str_replace('-', '_', $args['ai'])); ?>_ai_model]"
				id="<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>-ai-model-select"
			>
				<option value="">-- <?php esc_html_e('Models', EASYTAB_DOMAIN); ?> --</option>
				<?php foreach ($ai_models[$args['ai']] as $key => $value) { ?>
				<option value="<?php echo esc_html($key); ?>" <?php echo ((!empty($options[esc_html(str_replace('-', '_', $args['ai'])) . '_ai_model'])) && ($options[esc_html(str_replace('-', '_', $args['ai'])) . '_ai_model'] === $key)) ? 'selected' : ''; ?>><?php echo esc_html($value); ?></option>
				<?php } ?>
			</select>

		</div>

		<br>

		<details>
			<summary><?php esc_html_e('Select one of the available AI Models. ', EASYTAB_DOMAIN); ?></summary>
			<div class="info">
				<p><?php esc_html_e('An artificial intelligence can offer different models, optimized to solve specific tasks, such as natural language understanding, image processing or speech recognition. ', EASYTAB_DOMAIN); ?></p>
				<p>
					<?php esc_html_e('If you want to learn more ', EASYTAB_DOMAIN); ?>
					<u>
						<a
							href="<?php echo esc_html(easytab_helper_help_link(str_replace('-', '_', $args['ai']), 'ai-models')); ?>"
							rel="nofollow"
							target="_blank"
						>
							<?php esc_html_e('click here!', EASYTAB_DOMAIN); ?>
						</a>
					</u>
				</p>
			</div>
		</details>

		<?php

	}


	function easytab_generate_output_max_words_field_cb($args) {

		$options = get_option('easytab_settings');

		?>

		<div class="<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>">

			<input
				type="number"
				name="easytab_settings[generate_output_max_words_for_<?php echo esc_html(str_replace('-', '_', $args['ai'])); ?>_ai]"
				id="<?php echo 'generate-output-max-words-for-' . esc_html(str_replace('_', '-', $args['ai'])); ?>-ai"
				min="50"
				max="500"
				step="5"
				value="<?php echo (!empty($options['generate_output_max_words_for_' . str_replace('-', '_', $args['ai']) . '_ai'])) ? esc_html($options['generate_output_max_words_for_' . str_replace('-', '_', $args['ai']) . '_ai']) : 50; ?>"
			>

		</div>

		<br>

		<details>
			<summary><?php esc_html_e('Set the max words number to be generated. ', EASYTAB_DOMAIN); ?></summary>
			<div class="info">
				<p><?php esc_html_e('The greater the number of words of text to be generated the greater the cost per single call to the AI API. ', EASYTAB_DOMAIN); ?></p>
			</div>
		</details>

		<?php

	}


	function easytab_generate_semantic_html5_formatted_output_field_cb($args) {

		$options = get_option('easytab_settings');

		?>

		<div class="<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>">

			<input
				type="checkbox"
				name="easytab_settings[generate_semantic_html5_formatted_output_for_<?php echo esc_html(str_replace('-', '_', $args['ai'])); ?>_ai]"
				id="<?php echo 'generate-semantic-html5-formatted-output-for-' . esc_html(str_replace('_', '-', $args['ai'])); ?>-ai"
				value="true"
				<?php echo (!empty($options['generate_semantic_html5_formatted_output_for_' . str_replace('-', '_', $args['ai']) . '_ai'])) ? 'checked' : ''; ?>
			>

			<label for="<?php echo 'generate-semantic-html5-formatted-output-for-' . esc_html(str_replace('_', '-', $args['ai'])); ?>-ai">
				<?php esc_html_e('Yes', EASYTAB_DOMAIN); ?>
			</label>

		</div>

		<br>

		<details>
			<summary><?php esc_html_e('Generate formatted text according to HTML5 semantic standards? ', EASYTAB_DOMAIN); ?></summary>
			<div class="info">
				<p><?php esc_html_e('Well-formatted text, in addition to making it easier for users to read, could have SEO benefits. ', EASYTAB_DOMAIN); ?></p>
			</div>
		</details>

		<?php

	}


	function easytab_shop_target_description_field_cb($args) {

		$options = get_option('easytab_settings');

		?>

		<div class="<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>">

			<textarea
				name="easytab_settings[shop_target_description_for_<?php echo esc_html(str_replace('-', '_', $args['ai'])); ?>_ai]"
				id="<?php echo 'shop-target-description-for-' . esc_html(str_replace('_', '-', $args['ai'])) . '-ai'; ?>"
				autocomplete="off"
				placeholder="<?php esc_html_e('Description', EASYTAB_DOMAIN); ?>"
				rows="4" cols="50"
			><?php echo (!empty($options['shop_target_description_for_' . str_replace('-', '_', $args['ai']) . '_ai'])) ? esc_html($options['shop_target_description_for_' . str_replace('-', '_', $args['ai']) . '_ai']) : ''; ?></textarea>

		</div>

		<br>

		<details>
			<summary><?php esc_html_e('Describe the type of products your store sells. ', EASYTAB_DOMAIN); ?></summary>
			<div class="info">
				<p><?php esc_html_e('In the description, try to be as clear and comprehensive as possible, to achieve greater accuracy on the content generated. ', EASYTAB_DOMAIN); ?></p>
			</div>
		</details>

		<?php

	}


	function easytab_product_taxonomies_list_field_cb($args) {

		$options = get_option('easytab_settings');

		$product_taxonomies = easytab_helper_product_taxonomies_list();

		if (!$product_taxonomies) return;

		?>

		<div class="<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>">

		<?php

			if (!empty($options['product_taxonomies_for_' . str_replace('-', '_', $args['ai']) . '_ai'])) {

				$i = 0;

				foreach ($options['product_taxonomies_for_' . str_replace('-', '_', $args['ai']) . '_ai'] as $key => $value) {

					if ($key === "%d") continue;

					$product_taxonomy = $value;

		?>

			<div class="product-taxonomies-list repeater-field" style="margin-bottom: 30px;">

				<select
					name="easytab_settings[product_taxonomies_for_<?php echo esc_html(str_replace('-', '_', $args['ai'])); ?>_ai][<?php echo $i; ?>][list]"
					id="product-taxonomy-<?php echo $i; ?>-list-<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>-ai" style="margin-right: 20px;"
					class="not-require"
				>
					<option value="">-- <?php esc_html_e('Taxonomies', EASYTAB_DOMAIN); ?> --</option>
					<?php foreach ($product_taxonomies as $product_taxonomy_key => $product_taxonomy_value) { ?>
					<option value="<?php echo esc_html($product_taxonomy_key); ?>" <?php echo ($product_taxonomy['list'] === $product_taxonomy_key) ? 'selected' : ''; ?>>
						<?php echo esc_html($product_taxonomy_value); ?>
					</option>
					<?php } ?>
				</select>

				<textarea
					name="easytab_settings[product_taxonomies_for_<?php echo esc_html(str_replace('-', '_', $args['ai'])); ?>_ai][<?php echo $i; ?>][info]"
					id="product-taxonomy-<?php echo $i; ?>-info-for-<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>-ai"
					class="not-require"
					placeholder="<?php esc_html_e('Information and specific instructions to this taxonomy. ', EASYTAB_DOMAIN); ?>"
					autocomplete="off"
					rows="4"
					cols="50"
				><?php echo (!empty($product_taxonomy['info'])) ? esc_html($product_taxonomy['info']) : ''; ?></textarea>

				<div class="repeater-field-controls">
					<span class="add"></span>
					<span class="remove"></span>
				</div>

			</div>

		<?php

					$i++;

				}

			} else {

		?>

			<div class="product-taxonomies-list repeater-field" style="margin-bottom: 30px;">

				<select
					name="easytab_settings[product_taxonomies_for_<?php echo esc_html(str_replace('-', '_', $args['ai'])); ?>_ai][0][list]"
					id="product-taxonomy-0-list-<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>-ai"
					style="margin-right: 20px;"
					class="not-require"
				>
					<option value="">-- <?php esc_html_e('Taxonomies', EASYTAB_DOMAIN); ?> --</option>
					<?php foreach ($product_taxonomies as $product_taxonomy_key => $product_taxonomy_value) { ?>
					<option value="<?php echo esc_html($product_taxonomy_key); ?>">
						<?php echo esc_html($product_taxonomy_value); ?>
					</option>
					<?php } ?>
				</select>

				<textarea
					name="easytab_settings[product_taxonomies_for_<?php echo esc_html(str_replace('-', '_', $args['ai'])); ?>_ai][0][info]"
					id="product-taxonomy-0-info-for-<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>-ai"
					class="not-require"
					placeholder="<?php esc_html_e('Information and specific instructions for this taxonomy. ', EASYTAB_DOMAIN); ?>"
					autocomplete="off"
					rows="4"
					cols="50"
				><?php echo (!empty($options['product_taxonomies_for_' . str_replace('-', '_', $args['ai']) . '_ai'][0]['info'])) ? esc_html($options['product_taxonomies_for_' . str_replace('-', '_', $args['ai']) . '_ai'][0]['info']) : ''; ?></textarea>

				<div class="repeater-field-controls">
					<span class="add"></span>
					<span class="remove"></span>
				</div>

			</div>

		<?php

			}

		?>

			<div class="to-clone" style="margin-bottom: 30px; display: none;">

				<select
					name="easytab_settings[product_taxonomies_for_<?php echo esc_html(str_replace('-', '_', $args['ai'])); ?>_ai][%d][list]"
					id="product-taxonomies-%d-list-for-<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>-ai"
					style="margin-right: 20px;"
					class="not-require"
				>
					<option value="">-- <?php esc_html_e('Taxonomies', EASYTAB_DOMAIN); ?> --</option>
					<?php foreach ($product_taxonomies as $product_taxonomy_key => $product_taxonomy_value) { ?>
					<option value="<?php echo esc_html($product_taxonomy_key); ?>">
						<?php echo esc_html($product_taxonomy_value); ?>
					</option>
					<?php } ?>
				</select>

				<textarea
					name="easytab_settings[product_taxonomies_for_<?php echo esc_html(str_replace('-', '_', $args['ai'])); ?>_ai][%d][info]"
					id="product-taxonomies-%d-info-for-<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>-ai"
					class="not-require"
					placeholder="<?php esc_html_e('Information and specific instructions for this taxonomy', EASYTAB_DOMAIN); ?>"
					autocomplete="off"
					rows="4" cols="50"
				></textarea>

				<div class="repeater-field-controls">
					<span class="add"></span>
					<span class="remove"></span>
				</div>

			</div>

			<input
				type="hidden"
				name="easytab_settings[number_repeater_fields_for_<?php echo esc_html(str_replace('-', '_', $args['ai'])); ?>_ai]"
				class="number-repeater-fields"
				value="<?php echo (!empty($options['number_repeater_fields_for_' . str_replace('-', '_', $args['ai']) . '_ai'])) ? esc_html($options['number_repeater_fields_for_' . str_replace('-', '_', $args['ai']) . '_ai']) : 0; ?>"
			>

		</div>

		<br>

		<details>
			<summary><?php esc_html_e('Taxonomies containing useful information for generation tabs content. ', EASYTAB_DOMAIN); ?></summary>
			<div class="info">
				<p><?php esc_html_e('A taxonomy, in WordPress, is a classification system used to organize content. Taxonomies can be hierarchical, similar to a tree structure such as "Categories", or non-hierarchical, such as "Tags."', EASYTAB_DOMAIN) ?></p>
				<p><?php esc_html_e('Choose one or more taxonomies from the list if you think it would be useful to provide IA with the information contained in it. This will be used to improve the generation of Woocommerce Tab content for each product page. ', EASYTAB_DOMAIN) ?></p>
			</div>
		</details>

		<?php

	}


	function easytab_specific_wc_tab_instructions_field_cb($args) {

		$options = get_option('easytab_settings');

		?>

		<div class="<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>">

			<textarea
				name="easytab_settings[specific_wc_<?php echo esc_html(str_replace('-', '_', $args['wc_tab']['key'])); ?>_tab_instructions_for_<?php echo esc_html(str_replace('-', '_', $args['ai'])); ?>_ai]"
				id="<?php echo 'specific-wc-' . esc_html(str_replace('_', '-', $args['wc_tab']['key'])) . '-tab-instructions-for-' . esc_html(str_replace('_', '-', $args['ai'])) . '-ai'; ?>"
				class="not-require"
				placeholder="<?php esc_html_e('Description', EASYTAB_DOMAIN); ?>"
				autocomplete="off"
				rows="4" cols="50"
			><?php echo (!empty($options['specific_wc_' . str_replace('-', '_', $args['wc_tab']['key']) . '_tab_instructions_for_' . str_replace('-', '_', $args['ai']) . '_ai'])) ? esc_html($options['specific_wc_' . str_replace('-', '_', $args['wc_tab']['key']) . '_tab_instructions_for_' . str_replace('-', '_', $args['ai']) . '_ai']) : ''; ?></textarea>

		</div>

		<br>

		<details>
			<summary><?php esc_html_e('Provide specific instructions for this tab to be incorporated into the prompt intended for IA. ', EASYTAB_DOMAIN); ?></summary>
			<div class="info">
				<p><?php esc_html_e('Additional instructions to consider for this specific Woocommerce tab. ', EASYTAB_DOMAIN); ?></p>
			</div>
		</details>

		<?php

	}


	function easytab_specific_wc_tab_output_preset_field_cb($args) {

		$options = get_option('easytab_settings');

		if ((!empty($options)) && (array_key_exists('output_preset_for_' . str_replace('-', '_', $args['wc_tab']['key']) . '_tab_target_and_' . $args['ai'] . '_ai', $options)))
			$preset = $options['output_preset_for_' . str_replace('-', '_', $args['wc_tab']['key']) . '_tab_target_and_' . $args['ai'] . '_ai'];
		else
			$preset = null;

		?>

		<div class="<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>">

			<select
				name="easytab_settings[output_preset_for_<?php echo esc_html(str_replace('-', '_', $args['wc_tab']['key'])); ?>_tab_target_and_<?php echo esc_html($args['ai']); ?>_ai]"
				id="output-preset-for-<?php echo esc_html(str_replace('_', '-', $args['wc_tab']['key'])); ?>-tab-target-and-<?php echo esc_html($args['ai']); ?>-ai"
				class="not-require"
				style="margin-right: 20px;"
			>
				<option value="">-- <?php esc_html_e('Output Preset', EASYTAB_DOMAIN); ?> --</option>
				<option value="0" <?php echo ((isset($preset)) && ($preset === '0')) ? 'selected' : ''; ?>>
					<?php esc_html_e('Creative and concise', EASYTAB_DOMAIN); ?>
				</option>
				<option value="1" <?php echo ((isset($preset)) && ($preset === '1')) ? 'selected' : ''; ?>>
					<?php esc_html_e('Accurate and consistent', EASYTAB_DOMAIN); ?>
				</option>
				<option value="2" <?php echo ((isset($preset)) && ($preset === '2')) ? 'selected' : ''; ?>>
					<?php esc_html_e('Creative and Accurate', EASYTAB_DOMAIN); ?>
				</option>
			</select>

		</div>

		<br>

		<details>
			<summary><?php esc_html_e('An indication of the type of description you would like to get for this tab. ', EASYTAB_DOMAIN); ?></summary>
			<div class="info">
				<p><?php esc_html_e('A rough guideline useful in maximizing the quality of the generated result. ', EASYTAB_DOMAIN) ?></p>
			</div>
		</details>

		<?php

	}

	function easytab_product_meta_list_field_cb($args) {

		$options = get_option('easytab_settings');

		$product_meta_fields = easytab_helper_product_meta_fields_list();

		if (!$product_meta_fields) return;

		?>

		<div class="<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>">

		<?php

		if (!empty($options['product_meta_for_' . str_replace('-', '_', $args['wc_tab']['key']) . '_tab_target_and_' . str_replace('-', '_', $args['ai']) . '_ai'])) {

			$i = 0;

			foreach ($options['product_meta_for_' . str_replace('-', '_', $args['wc_tab']['key']) . '_tab_target_and_' . str_replace('-', '_', $args['ai']) . '_ai'] as $key => $value) {

				if ($key === "%d") continue;

				$product_meta = $value;

		?>

			<div class="product-meta-list repeater-field" style="margin-bottom: 30px;">

				<select
					name="easytab_settings[product_meta_for_<?php echo esc_html(str_replace('-', '_', $args['wc_tab']['key'])); ?>_tab_target_and_<?php echo esc_html(str_replace('-', '_', $args['ai'])); ?>_ai][<?php echo $i; ?>][list]"
					id="product-meta-<?php echo $i; ?>-list-for-<?php echo esc_html(str_replace('_', '-', $args['wc_tab']['key'])); ?>-tab-target-and-<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>-ai"
					style="margin-right: 20px;"
					class="not-require"
				>
					<option value="">-- <?php esc_html_e('Product Meta Fields', EASYTAB_DOMAIN); ?> --</option>
					<?php foreach ($product_meta_fields as $product_meta_field_key => $product_meta_field_value) { ?>
					<option value="<?php echo esc_html($product_meta_field_key); ?>" <?php echo ($product_meta['list'] === $product_meta_field_key) ? 'selected' : ''; ?>>
						<?php echo esc_html($product_meta_field_value); ?>
					</option>
					<?php } ?>
				</select>

				<textarea
					name="easytab_settings[product_meta_for_<?php echo esc_html(str_replace('-', '_', $args['wc_tab']['key'])); ?>_tab_target_and_<?php echo esc_html(str_replace('-', '_', $args['ai'])); ?>_ai][<?php echo $i; ?>][info]"
					id="product-meta-<?php echo $i; ?>-info-for-<?php echo esc_html(str_replace('_', '-', $args['wc_tab']['key'])); ?>-tab-target-and-<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>-ai"
					class="not-require"
					placeholder="<?php esc_html_e('Description', EASYTAB_DOMAIN); ?>"
					autocomplete="off"
					rows="4"
					cols="50"
				><?php echo (!empty($product_meta['info'])) ? esc_html($product_meta['info']) : ''; ?></textarea>

				<div class="repeater-field-controls">
					<span class="add"></span>
					<span class="remove"></span>
				</div>

			</div>

		<?php
				$i++;

			}

		} else {

		?>

			<div class="product-meta-list repeater-field" style="margin-bottom: 30px;">

				<select
					name="easytab_settings[product_meta_for_<?php echo esc_html(str_replace('-', '_', $args['wc_tab']['key'])); ?>_tab_target_and_<?php echo esc_html(str_replace('-', '_', $args['ai'])); ?>_ai][0][list]"
					id="product-meta-0-list-for-<?php echo esc_html(str_replace('_', '-', $args['wc_tab']['key'])); ?>-tab-target-and-<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>-ai"
					style="margin-right: 20px;"
					class="not-require">
					<option value="">-- <?php esc_html_e('Product Meta Fields', EASYTAB_DOMAIN); ?> --</option>
					<?php foreach ($product_meta_fields as $product_meta_field_key => $product_meta_field_value) { ?>
					<option value="<?php echo esc_html($product_meta_field_key); ?>">
						<?php echo esc_html($product_meta_field_value); ?>
					</option>
					<?php } ?>
				</select>

				<textarea
					name="easytab_settings[product_meta_for_<?php echo esc_html(str_replace('-', '_', $args['wc_tab']['key'])); ?>_tab_target_and_<?php echo esc_html(str_replace('-', '_', $args['ai'])); ?>_ai][0][info]"
					id="product-meta-0-info-for-<?php echo esc_html(str_replace('_', '-', $args['wc_tab']['key'])); ?>-tab-target-and-<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>-ai][0]"
					class="not-require"
					placeholder="<?php esc_html_e('Description', EASYTAB_DOMAIN); ?>"
					autocomplete="off"
					rows="4" cols="50"><?php
						echo (!empty($options['product_meta_for_' . $args['wc_tab']['key'] . '_tab_target_and_' . str_replace('-', '_', $args['ai']) . '_ai'][0]['info'])) ? esc_html($options['product_meta_for_' . $args['wc_tab']['key'] . '_tab_target_and_' . str_replace('-', '_', $args['ai']) . '_ai'][0]['info']) : '';
					?></textarea>

				<div class="repeater-field-controls">
					<span class="add"></span>
					<span class="remove"></span>
				</div>

			</div>

		<?php

		}

		?>

			<div class="to-clone" style="margin-bottom: 30px; display: none;">

				<select
					name="easytab_settings[product_meta_for_<?php echo esc_html(str_replace('-', '_', $args['wc_tab']['key'])); ?>_tab_target_and_<?php echo esc_html(str_replace('-', '_', $args['ai'])); ?>_ai][%d][list]"
					id="product-meta-%d-list-for-<?php echo esc_html(str_replace('_', '-', $args['wc_tab']['key'])); ?>-tab-target-and-<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>-ai"
					style="margin-right: 20px;"
					class="not-require"
				>
					<option value="">-- <?php esc_html_e('Product Meta Fields', EASYTAB_DOMAIN); ?> --</option>
					<?php foreach ($product_meta_fields as $product_meta_field_key => $product_meta_field_value) { ?>
					<option value="<?php echo esc_html($product_meta_field_key); ?>">
						<?php echo esc_html($product_meta_field_value); ?>
					</option>
					<?php } ?>
				</select>

				<textarea
					name="easytab_settings[product_meta_for_<?php echo esc_html(str_replace('-', '_', $args['wc_tab']['key'])); ?>_tab_target_and_<?php echo esc_html(str_replace('-', '_', $args['ai'])); ?>_ai][%d][info]"
					id="product-meta-%d-info-for-<?php echo esc_html(str_replace('_', '-', $args['wc_tab']['key'])); ?>-tab-target-and-<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>-ai"
					class="not-require"
					placeholder="<?php esc_html_e('Description', EASYTAB_DOMAIN); ?>"
					autocomplete="off"
					rows="4" cols="50"></textarea>

				<div class="repeater-field-controls">
					<span class="add"></span>
					<span class="remove"></span>
				</div>

			</div>

			<input
				type="hidden"
				name="easytab_settings[number_repeater_fields_for_<?php echo esc_html(str_replace('-', '_', $args['wc_tab']['key'])); ?>_tab_target_and_<?php echo esc_html(str_replace('-', '_', $args['ai'])); ?>_ai]"
				class="number-repeater-fields"
				value="<?php echo (!empty($options['number_repeater_fields_for_' . $args['wc_tab']['key'] . '_tab_target_and_' . str_replace('-', '_', $args['ai']) . '_ai'])) ? esc_html($options['number_repeater_fields_for_' . $args['wc_tab']['key'] . '_tab_target_and_' . str_replace('-', '_', $args['ai']) . '_ai']) : 0; ?>"
			>

			</div>

			<br>

			<details>
				<summary><?php esc_html_e('Product Meta Fields containing useful information for the selected tab. ', EASYTAB_DOMAIN); ?></summary>
				<div class="info">
					<p><?php esc_html_e('In WordPress, a meta field is a data element associated with a post, page or, in the case of the WooCommerce plugin, a product. Each meta field stores additional information related to a specific piece of information, such as the SKU, a discounted price, dimensions, or other technical specifications of the product. ', EASYTAB_DOMAIN) ?></p>
					<p><?php esc_html_e('Choose one or more meta fields from the list of meta fields found, if you think it would be useful to provide the IA with the information contained in them. They will be used to improve the generation of content for the selected Woocommerce Tab, on each product page. ', EASYTAB_DOMAIN) ?></p>
				</div>
			</details>

		<?php

	}