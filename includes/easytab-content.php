<?php

	function easytab_generate_tab_content() {

		if (is_singular('product') && (!is_admin())) {
	
			$options = get_option('easytab_settings');

			$product_id = get_the_ID();

			$check = false;

			if (empty(get_the_content($product_id))) {
				$product = array(
					'ID'           => $product_id,
					'post_content' => '...',
				);
				wp_update_post($product);
				$check = true;
			}

			$wc_tabs = easytab_helper_get_woo_default_product_tabs();

            $selected_ai = str_replace('-', '_', $options['ai']);

            if (empty($selected_ai)) return null;

            $iso_639_1_code_output = $options['prompt_language_output_response'];

			foreach ($wc_tabs as $wc_tab_key => $wc_tab_data) {

				$wc_tab_key = str_replace('-', '_', $wc_tab_key);

				$tab_content = get_post_meta($product_id, 'easytab_' . $wc_tab_key . '_tab', true);

				if (empty($tab_content)) {

					$product = new WC_Product($product_id);
					$product_name = $product->get_name();
					$product_info = $product->get_description();
					$product_info = apply_filters('the_content', $product_info);
					$product_excerpt = $product->get_short_description();

					$product_categories = wp_get_post_terms($product->get_id(), 'product_cat');
					$tmp = '';
					$separator = ', ';
					$counter = 0;
					$total = count($product_categories);

					foreach ($product_categories as $product_category) {
						$counter++;
						if ($counter === $total) $separator = '';
						$tmp .= $product_category->name . $separator;
					}

					$product_categories = $tmp;

					$product_tags = wp_get_post_terms($product->get_id(), 'product_tag');
					$tmp = '';
					$separator = ', ';
					$counter = 0;
					$total = count($product_tags);

					foreach ($product_tags as $product_tag) {
						$counter++;
						if ($counter === $total) $separator = '';
						$tmp .= $product_tag->name . $separator;
					}

					$product_tags = $tmp;

					$product_weight = null;
					$product_dimensions = null;

					if (($product->has_weight()) || ($product->has_dimensions())) {
						$product_weight = wc_format_weight($product->get_weight());
						$product_dimensions = wc_format_dimensions($product->get_dimensions(false));
					} elseif ($wc_tab_key === 'additional_information') continue;

					$ecommerce_target = array_key_exists('shop_target_description_for_' . $selected_ai . '_ai', $options) ? $options[ 'shop_target_description_for_' . $selected_ai . '_ai' ] : null;
					$prompt_focus = $wc_tab_data['title'];

					$custom_prompt = array_key_exists('specific_wc_' . $wc_tab_key . '_tab_instructions_for_' . $selected_ai . '_ai', $options) ? $options['specific_wc_' . $wc_tab_key . '_tab_instructions_for_' . $selected_ai . '_ai']: null;

                    $max_chars = $options['generate_output_max_chars_for_' . $selected_ai . '_ai'];

					$request = "Generare una descrizione per un prodotto in vendita su un ecommerce, in lingua \"" . $iso_639_1_code_output . "\" (ISO-639-1-code), il cui focus sia relativo a \"$prompt_focus\".";

					$seo = "Il processo di generazione deve avvenire in ottica SEO, attenzionando in particolare che: 
							a) La descrizione sia originale e univoca.  
							b) La descrizione non sembri generata da una IA.  
							c) La descrizione non sia identificabile come contenuto duplicato. ";

					$no_ia = "Nel processo di generazione, evitare in modo assoluto: 
							a) Riferimenti al fatto che la descrizione sia stata generata da una IA, sia direttamente che indirettamente.  
							b) Di utilizzare un linguaggio e una sintassi grammaticale formale e/o innaturale.  
							c) Di utilizzare emoticons.  
							d) Di riportare problemi e/o errori, magari per via di informazioni mancanti nel prompt che stai \"leggendo\" ed elaborando.
							e) Di chiedere maggiori informazioni; se proprio non riesci a generare nulla per carenza di informazioni, non generare nulla.   
							f) Di improvvisare e non rispettare queste indicazioni; genera sempre e solo quanto richiesto secondo le indicazioni fornite. ";

					$prompt_rules = "Leggi il seguente prompt tenendo presente che: 
									a) I delimitatori di apertura e chiusura indicati nel prompt con i simboli @- -@ evidenziano informazioni particolarmente importanti da usare nella generazione di quanto richiesto.  
									b) I delimitatori di apertura e chiusura indicati @- -@ non devono essere presenti nel testo generato. 
									c) Il testo da generare non deve superare i $max_chars caratteri. ";

					$prompt = $prompt_rules . " " . $request . " Le indicazioni sono: 
						1) Il target e il settore di vendita dell'ecommerce sono @- " . $ecommerce_target . "-@. 
						2) Il nome del prodotto è @- $product_name -@. 
						3) Le informazioni di base disponibili per il prodotto sono @- " . $product_excerpt . " -@ @- " . $product_info . " -@. 
						4) Il prodotto è stato associato a queste categorie @- " . $product_categories . " -@ e a questi tags @-" . $product_tags . "-@, ma evita di riportare l'informazione nella forma \"è stata assegnata a queste categorie\", limitati ad usare questi dati per capire di più del prodotto. 
						5) Alcune indicazioni aggiuntive da seguire sono @- " . $custom_prompt . " -@. 
						6) $seo 
						7) $no_ia";

					if (($wc_tab_key === 'additional_information') && (($product_weight) || ($product_dimensions))) {
						$prompt .= "8) Questi dati del prodotto sono relativi a: ";
						if ($product_weight) {
							$prompt .= "a) dimensioni --> @- " . $product_dimensions . " -@. ";
						}
						if ($product_dimensions) {
							$prompt .= "b) peso --> @- " . $product_weight . " -@. ";
						}
						$prompt .= "Struttura questi dati in una tabella (usando tags HTML5), facendola seguire ad una breve considerazione (sempre positiva)";
					}

					$ia_prompt_result = null;

                    $ia_prompt_result = easytab_chatgpt_connection('chat', $prompt);

                    if (!$ia_prompt_result) return;

					update_post_meta($product_id, 'easytab_' . $wc_tab_key . '_tab', $ia_prompt_result);

					if (($check === true) && ($wc_tab_key === 'description')) {
						$product = array(
							'ID'           => $product_id,
							'post_content' => $ia_prompt_result,
						);
						wp_update_post($product);
						$check = false;
					}

				}

			}

		}

	}

	add_action('wp', 'easytab_generate_tab_content');


	function easytab_tabs_templates_override($template, $template_name, $template_path) {

		$base_dir = WP_PLUGIN_DIR . '/easytab/wc-templates-override/';

		$plugin_template = $base_dir . $template_name;

		if (file_exists($plugin_template)) {
			return $plugin_template;
		}

		return $template;

	}

	add_filter('woocommerce_locate_template', 'easytab_tabs_templates_override', 10, 3);