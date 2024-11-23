<?php

    defined('ABSPATH') || exit;


    function easytab_options_page() {

        add_menu_page(
            __('EasyTab', 'easytab'),
            __('EasyTab', 'easytab'),
            'manage_options',
            'easytab',
            'easytab_option_page',
            'dashicons-admin-generic',
            50
        );

	}

    add_action('woocommerce_init', 'easytab_options_page');


    function easytab_option_page() {

        if (!current_user_can('manage_options')) {
            return;
        }

	?>

        <div id="easytab-pro-banner">

            <img src="<?php echo EASYTAB_URL; ?>assets/img/easytab-logo.png">

            <a href="https://easytab.pro" rel="nofollow" target="_blank"><button><?php esc_html_e('GET PRO VERSION!', 'easytab'); ?></button></a>
            <a href="https://youtu.be/rS05e_R8KdM?si=6mQn3JLoaowy88Qz" rel="nofollow" target="_blank"><button><?php esc_html_e('EASYTAB PRO PREVIEW', 'easytab'); ?></button></a>

            <div>
                <h4><b><?php esc_html_e('EasyTab Pro', 'easytab'); ?></b><?php esc_html_e(' features', 'easytab'); ?></h4>
                <ul>
                    <li><?php esc_html_e('Anthropic\'s Claude AI', 'easytab'); ?></li>
                    <li><?php esc_html_e('Powered AI Prompt Wizard', 'easytab'); ?></li>
                    <li><?php esc_html_e('More efficient and configurable', 'easytab'); ?></li>
                    <li><?php esc_html_e('Continuous assistance and support', 'easytab'); ?></li>
                </ul>
            </div>

        </div>

        <div class="wrap">
            <h1><?php esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post" id="easytab-ai-prompt">

	<?php

        // Security field
        settings_fields('easytab_options');

		do_settings_sections('easytab');

		$ai = (get_option('easytab_ai')) ? get_option('easytab_ai') : null;

        if (empty($ai)) return;

    	$wc_tabs = easytab_helper_get_woo_default_product_tabs();

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

						<button class="wc-tabs-link<?php echo esc_html($default_open) ? ' active' : ''; ?>" data-wctab="<?php echo esc_html(str_replace('_', '-', $wc_tab_key)); ?>"><?php echo esc_html($wc_tab_value['title']); ?></button>

	<?php

				$default_open = false;

			}

	?>

					</div>

	<?php

			$default_open = true;

			foreach ($wc_tabs as $wc_tab_key => $wc_tab_value) {

	?>

					<div id="wc-tab-section-<?php echo esc_html(str_replace('_', '-', $wc_tab_key)); ?>-<?php echo esc_html(str_replace('_', '-', $ai_key)); ?>-ai" class="wc-tab-section <?php echo esc_html(str_replace('_', '-', $ai_key . ' ' . $wc_tab_key)); ?>"<?php echo (!$default_open) ? ' style="display: none;"' : '';?>>

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

        submit_button(__('Save Settings', 'easytab'));

	?>

            </form>
        </div>

	<?php

    }


    function easytab_settings_init() {

        register_setting('easytab_options', 'easytab_settings', 'easytab_sanitize_options');

        $ai = (get_option('easytab_ai')) ? get_option('easytab_ai') : null;

        if (empty($ai)) return;

		$wc_tabs = easytab_helper_get_woo_default_product_tabs();

	    add_settings_section(
		    'easytab_section',
		    __('Prompt Configuration Wizard for AI', 'easytab'),
		    null,
		    'easytab'
	    );

	    add_settings_field(
		    'easytab_ai_field',
		    __('Select AI', 'easytab'),
		    'easytab_ai_field_cb',
		    'easytab',
		    'easytab_section',
		    array('ai' => $ai)
	    );

        add_settings_field(
		    'easytab_prompt_language_output_response_field',
		    __('Select a language', 'easytab'),
		    'easytab_prompt_language_output_response_field_cb',
		    'easytab',
		    'easytab_section',
		    array('ai' => $ai)
	    );

        foreach ($ai as $ai_key => $ai_value) {

	        $ai_key = str_replace('-', '_', $ai_key);

	        add_settings_section(
		        'easytab_' . $ai_key . '_section',
		        __('Generating the prompt for ', 'easytab') . $ai_value,
		        null,
		        'easytab_' . $ai_key
	        );

			/**/

            add_settings_field(
                'easytab_ai_' . $ai_key . '_api_key_field',
                __('Enter the API KEY', 'easytab'),
                'easytab_ai_api_key_field_cb',
                'easytab_' . $ai_key,
	            'easytab_' . $ai_key . '_section',
                array('ai' => $ai_key)
            );

            add_settings_field(
                'easytab_max_chars_for_' . $ai_key . '_ai_field',
                __('Max. number of characters', 'easytab'),
                'easytab_generate_output_max_chars_field_cb',
                'easytab_' . $ai_key,
                'easytab_' . $ai_key . '_section',
                array('ai' => $ai_key)
            );

	        add_settings_field(
		        'easytab_shop_target_description_for_' . $ai_key . '_ai_field',
		        __('Shop Description', 'easytab'),
		        'easytab_shop_target_description_field_cb',
		        'easytab_' . $ai_key,
		        'easytab_' . $ai_key . '_section',
		        array('ai' => $ai_key)
	        );

			foreach ($wc_tabs as $wc_tab_key => $wc_tab_value) {

				$wc_tab_key = str_replace('-', '_', $wc_tab_key);

                add_settings_section(
                    'easytab_' . $ai_key . '_wc_' . $wc_tab_key . '_tab_section',
	                '<span style="font-size: 16px;">' . __('Specific information for the Woocommerce Tab ', 'easytab') . '"' . $wc_tab_value['title'] . '"</span>',
                    null,
                    'easytab_' . $ai_key . '_wc_' . $wc_tab_key . '_tab'
                );

                add_settings_field(
                    'easytab_specific_wc_' . $wc_tab_key . '_tab_instructions_for_' . $ai_key . '_ai_field',
                    __('Additional Instructions', 'easytab'),
                    'easytab_specific_wc_tab_instructions_field_cb',
                    'easytab_' . $ai_key . '_wc_' . $wc_tab_key . '_tab',
                    'easytab_' . $ai_key . '_wc_' . $wc_tab_key . '_tab_section',
                    array('ai' => $ai_key, 'wc_tab' => ['key' => $wc_tab_key, 'value' => $wc_tab_value['title']])
                );

            }

        }

    }

    add_action('admin_init', 'easytab_settings_init');


	/**/


    function easytab_ai_field_cb($args) {

        $ai = $args['ai'];

        $options = get_option('easytab_settings');

	?>

		<div>

			<select name="easytab_settings[ai]" id="ai-select">
				<option value="">-- <?php esc_html_e('AI available', 'easytab'); ?> --</option>
			<?php foreach ($ai as $key => $value) { ?>
				<option value="<?php echo esc_html($key); ?>" <?php echo $options['ai'] === $key ? 'selected' : ''; ?>><?php echo esc_html($value); ?></option>
			<?php } ?>
			</select>

		</div>

        <br>

        <details>
            <summary><?php esc_html_e('Select an AI', 'easytab'); ?></summary>
            <div class="info">
                <p><?php esc_html_e('Chat-GPT is a chat bot based on artificial intelligence and machine learning, developed by OpenAI and specialized in conversation with a human user. ', 'easytab'); ?></p>
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
                <option value="">-- <?php esc_html_e('Select a language', 'easytab'); ?> --</option>
                <?php foreach ($languages as $language_key => $language_value) { ?>
                    <option value="<?php echo esc_html($language_key); ?>" <?php echo $options['prompt_language_output_response'] === $language_key ? 'selected' : ''; ?>><?php echo esc_html(ucfirst($language_value)); ?></option>
                <?php } ?>
            </select>

        </div>

        <br>

        <details>
            <summary><?php esc_html_e('Select a language', 'easytab'); ?></summary>
			<div class="info">
				<p><?php esc_html_e('The content will be generated by the AI in the selected language. ', 'easytab'); ?></p>
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
				placeholder="<?php esc_html_e('AI API KEY', 'easytab'); ?>">

		</div>

        <br>

        <details>
            <summary><?php esc_html_e('Enter the API key for the chosen AI', 'easytab'); ?></summary>
			<div class="info">
				<p><?php esc_html_e('An API Key is a unique code used to authenticate and authorize access to a software API. It functions as a "password" that allows one to connect to the chosen service and interact with the AI model, ensuring that only authorized users can send requests and receive responses. ', 'easytab'); ?></p>
				<p><?php esc_html_e('To generate an API Key ', 'easytab'); ?><u><a href="<?php echo esc_html(easytab_helper_help_link(str_replace('-', '_', $args['ai']), 'how-generate-api-key')); ?>" rel="nofollow" target="_blank"><?php esc_html_e('click here!', 'easytab'); ?></a></u></p>
			</div>
        </details>

	<?php

    }


    function easytab_generate_output_max_chars_field_cb($args) {

        $options = get_option('easytab_settings');

    ?>

        <div class="<?php echo esc_html(str_replace('_', '-', $args['ai'])); ?>">

            <input
                type="number"
                name="easytab_settings[generate_output_max_chars_for_<?php echo esc_html(str_replace('-', '_', $args['ai'])); ?>_ai]"
                id="<?php echo 'generate-output-max-chars-for-' . esc_html(str_replace('_', '-', $args['ai'])); ?>-ai"
                min="512"
                max="2048"
                step="8"
                value="<?php echo (!empty($options['generate_output_max_chars_for_' . str_replace('-', '_', $args['ai']) . '_ai'])) ? esc_html($options['generate_output_max_chars_for_' . str_replace('-', '_', $args['ai']) . '_ai']) : 512; ?>">

        </div>

        <br>

        <details>
            <summary><?php esc_html_e('Set the length of the content to be generated', 'easytab'); ?></summary>
            <div class="info">
                <p><?php esc_html_e('The greater the number of characters of text to be generated the greater the cost per single call to the AI API.', 'easytab'); ?></p>
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
				placeholder="<?php esc_html_e('Description', 'easytab'); ?>"
				rows="4" cols="50"><?php echo (!empty($options['shop_target_description_for_' . str_replace('-', '_', $args['ai']) . '_ai'])) ? esc_html($options['shop_target_description_for_' . str_replace('-', '_', $args['ai']) . '_ai']) : ''; ?></textarea>

		</div>

        <br>

        <details>
            <summary><?php esc_html_e('Describe the type of products your store sells and the target users', 'easytab'); ?></summary>
			<div class="info">
                <p><?php esc_html_e('In the description, try to be as clear and comprehensive as possible, to achieve greater accuracy on the content generated.', 'easytab'); ?></p>
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
				placeholder="<?php esc_html_e('Description', 'easytab'); ?>"
				autocomplete="off"
				rows="4" cols="50"><?php echo (!empty($options['specific_wc_' . str_replace('-', '_', $args['wc_tab']['key']) . '_tab_instructions_for_' . str_replace('-', '_', $args['ai']) . '_ai'])) ? esc_html($options['specific_wc_' . str_replace('-', '_', $args['wc_tab']['key']) . '_tab_instructions_for_' . str_replace('-', '_', $args['ai']) . '_ai']) : ''; ?></textarea>

		</div>

        <br>

        <details>
            <summary><?php esc_html_e('Insert specific instructions for this tab to be incorporated into the prompt intended for AI', 'easytab'); ?></summary>
			<div class="info">
                <p><?php esc_html_e('Additional instructions to consider for this specific Woocommerce tab.', 'easytab'); ?></p>
			</div>
        </details>

    <?php

	}