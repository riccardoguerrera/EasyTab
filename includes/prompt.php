<?php

	defined('ABSPATH') || exit;
	

	function easytab_generate_tab_content() {

		if (is_singular('product') && (!is_admin())) {

			$options = get_option('easytab_settings');

			$product_id = get_the_ID();

			$wc_tabs = easytab_helper_get_woo_product_tabs();

			$selected_ai = str_replace('-', '_', $options['ai']);

			if (empty($selected_ai)) return null;

			$iso_639_1_code_output = $options['prompt_language_output_response'];

			foreach ($wc_tabs as $wc_tab_key => $wc_tab_data) {

				$wc_tab_key = str_replace('-', '_', $wc_tab_key);

				$tab_content = get_post_meta($product_id, 'easytab_' . $wc_tab_key . '_tab', true);

				if (empty($tab_content)) {

					$product = new WC_Product($product_id);
					
					$product_name = $product->get_name();
					
					$product_info = '';

					if ($wc_tab_key === 'description') {
						$product_info = $product->get_description();
						$product_info = apply_filters('the_content', $product_info);
					}

					$product_excerpt = $product->get_short_description();

					$product_taxonomies_info = array();

					if (array_key_exists('product_taxonomies_for_' . $selected_ai . '_ai', $options)) {

						foreach ($options['product_taxonomies_for_' . $selected_ai . '_ai'] as $key1 => $value) {

							$product_taxonomy_name = $value['list'];

							if (!empty($product_taxonomy_name)) {

								$product_taxonomy = get_taxonomy($product_taxonomy_name);

								$product_taxonomy_info = $value['info'];
								$product_taxonomy_terms = wp_get_post_terms($product_id, $product_taxonomy_name, array('parent' => 0));

								$product_taxonomy_terms_info = array();

								if (!is_wp_error($product_taxonomy_terms) && !empty($product_taxonomy_terms)) {
									foreach ($product_taxonomy_terms as $key2 => $product_taxonomy_term) {
										$separator_1 = !empty($product_taxonomy_term->description) ? ' <-> ' : '';
										$separator_2 = !empty($product_taxonomy_term->name) ? ' -> ' : '';
										$product_taxonomy_terms_info[$key2] = $product_taxonomy_term->name . $separator_1 . $product_taxonomy_term->description . $separator_2;
										$product_taxonomy_terms_info[$key2] .= easytab_helper_get_all_hierarchical_terms_data($product_taxonomy, $product_taxonomy_term);
									}
								}

								$separator = !empty($product_taxonomy_info) ? ' <-> ' : '';

								$product_taxonomies_info[] = $product_taxonomy->labels->name . $separator . $product_taxonomy_info . ": " . implode(" || ", $product_taxonomy_terms_info);

							} else {

								$product_taxonomies_info[] = '';

							}

						}

					}

					$product_weight = null;
					$product_dimensions = null;

					if (($product->has_weight()) || ($product->has_dimensions())) {
						$product_weight = wc_format_weight($product->get_weight());
						$product_dimensions = wc_format_dimensions($product->get_dimensions(false));
					} elseif ($wc_tab_key === 'additional_information') continue;

					$ecommerce_target = array_key_exists('shop_target_description_for_' . $selected_ai . '_ai', $options) ? $options[ 'shop_target_description_for_' . $selected_ai . '_ai' ] : null;
					$prompt_focus = $wc_tab_data['title'];

					$product_meta_info = array();

					if (array_key_exists('product_meta_for_' . $wc_tab_key . '_tab_target_and_' . $selected_ai . '_ai', $options)) {

						foreach ($options['product_meta_for_' . $wc_tab_key . '_tab_target_and_' . $selected_ai . '_ai'] as $key => $value) {

							$product_meta = $value;

							$product_meta_field_name = $product_meta['list'];

							if (!empty($product_meta_field_name)) {
								$product_meta_field_value = array();
								$product = wc_get_product($product_id);
								if ($product && $product->is_type('variable')) {
									$variations = $product->get_children();
									foreach ($variations as $variation_id) {
										$tmp = is_array(get_metadata('post', $variation_id, $product_meta_field_name)) ? implode(", ", get_metadata('post', $variation_id, $product_meta_field_name)) : get_metadata('post', $variation_id, $product_meta_field_name);
										if (!empty($tmp)) $product_meta_field_value[] = $tmp;
									}
								}
								if (empty($product_meta_field_value)) $product_meta_field_value = get_metadata('post', $product_id, $product_meta_field_name);
								$separator = !empty($product_meta['info']) ? ' <-> ' : '';
								$product_meta_info[] = $product_meta_field_name . $separator . $product_meta['info'] . ": " . ((is_array($product_meta_field_value)) ? implode(", ", $product_meta_field_value) : $product_meta_field_value);
							} else {
								$product_meta_info[] = '';
							}

						}

					}

					$custom_prompt = array_key_exists('specific_wc_' . $wc_tab_key . '_tab_instructions_for_' . $selected_ai . '_ai', $options) ? $options['specific_wc_' . $wc_tab_key . '_tab_instructions_for_' . $selected_ai . '_ai']: null;

					$max_words = $options['generate_output_max_words_for_' . $selected_ai . '_ai'];

					$request = "Generare una descrizione per un prodotto in vendita su un ecommerce, in lingua \"" . $iso_639_1_code_output . "\" (ISO-639-1-code), il cui focus sia relativo a \"$prompt_focus\". ";

					$seo = "Il processo di generazione deve avvenire in ottica SEO, attenzionando in particolare che: 
							a) La descrizione sia originale e univoca. 
							b) La descrizione non sembri generata da una IA. 
							c) La descrizione non sia identificabile come contenuto duplicato. ";

					$html5_formatted = (!empty($options['generate_semantic_html5_formatted_output_for_' . $selected_ai . '_ai'])) ? $options['generate_semantic_html5_formatted_output_for_' . $selected_ai . '_ai'] : null;

					$html = ((!empty($html5_formatted) && $html5_formatted === 'true') ? "
						e) La descrizione sia strutturata in HTML5 con tag semantici da scegliere nel modo più opportuno tra \"h2 - h4 - p - strong - u\". " : "  
						e) La descrizione sia strutturata come unico paragrafo. ") . "
						f) Nel caso di utilizzo di codice HTML nella formattazione del testo generato, questo riporti solo i tag necessari, evitando tag non necessari, come ad esempio \"html - head - body - footer\" 
						g) Se utilizzi codice HTML5, non utilizzare mai backtick (`) o virgolette tipografiche (come “ o ”) per delimitare il codice. 
						h) Non utilizzare mai i delimitatori di codice @- ```html -@ e @- ``` -@. ";

					$seo = $seo . $html;

					$no_ia = "Nel processo di generazione, evitare in modo assoluto: 
							a) Riferimenti al fatto che la descrizione sia stata generata da una IA, sia direttamente che indirettamente. 
							b) Di utilizzare un linguaggio e una sintassi grammaticale troppo formale e/o innaturale. 
							c) Di utilizzare emoticons. 
							d) Di riportare problemi e/o errori, magari per via di informazioni mancanti nel prompt che stai \"leggendo\" ed elaborando. 
							e) Di chiedere maggiori informazioni
							f) Se non riesci a generare nulla per carenza o assenza di informazioni, restituisci il codice \"ERROR 001\". 
							g) Di improvvisare e non rispettare le indicazioni date. ";

					$prompt_rules = "Leggi il seguente prompt tenendo presente che: 
									a) I delimitatori di apertura e chiusura indicati nel prompt con i simboli @- -@ evidenziano informazioni particolarmente importanti da usare nella generazione di quanto richiesto. 
									b) I delimitatori di apertura e chiusura indicati @- -@ non devono essere presenti nel testo generato. 
									c) Il testo da generare non dovrebbe superare le $max_words parole, entro questo limite di massima, usa il numero di parole che ritieni più opportuno. 
									d) Cerca di non troncare mai in modo brusco la generazione del testo; dai priorità alla sua finalizzazione a discapito dei tokens da utilizzare. ";
				
					$prompt = $prompt_rules . " " . $request . " Le indicazioni sono: 
						1) Il target e il settore di vendita dell'ecommerce sono @- " . $ecommerce_target . "-@. 
						2) Il nome del prodotto è @- $product_name -@. 
						3) Le informazioni di base disponibili per il prodotto sono @- " . $product_excerpt . " -@ @- " . implode(' | ', $product_meta_info) . " -@ @- " . implode(' ||| ', $product_taxonomies_info) . " -@ @- " . $product_info . " -@. 
						4) Alcune indicazioni aggiuntive da seguire sono @- " . $custom_prompt . " -@. 
						5) Cerca sul web informazioni utili per il processo di generazione, che siano attinenti al prodotto e ai dati (correlati ad esso) sin qui ricevuti.  
						6) $seo 
						7) $no_ia";

					if (($wc_tab_key === 'additional_information') && (($product_weight) || ($product_dimensions))) {
						$prompt .= "7) Questi dati del prodotto sono relativi a: ";
						if ($product_weight) {
							$prompt .= "a) dimensioni --> @- " . $product_dimensions . " -@. ";
						}
						if ($product_dimensions) {
							$prompt .= "b) peso --> @- " . $product_weight . " -@. ";
						}
						$prompt .= "In base a questi valori, trai una considerazione positiva del prodotto, enfatizzandone le caratteristiche.";
					}

					$ia_prompt_result = null;

					switch ($selected_ai) {

						case 'chat_gpt':
							$ia_prompt_result = easytab_chatgpt_connection('chat', $prompt, $wc_tab_key);
							break;
						case 'claude':
							$ia_prompt_result = easytab_claude_connection('chat', $prompt, $wc_tab_key);
							break;

					}

					if (!$ia_prompt_result) return;

					$tmp = easytab_helper_clean_html_code_in_output_response($ia_prompt_result);

					$ia_prompt_result = ((!empty($tmp[0])) && (!empty($tmp[0][1]))) ? $tmp[0][1] : $ia_prompt_result;

					update_post_meta($product_id, 'easytab_' . $wc_tab_key . '_tab', $ia_prompt_result);

					if ($wc_tab_key === 'description' && empty(get_the_content($product_id))) {
						$product = array(
							'ID' => $product_id,
							'post_content' => $ia_prompt_result,
						);
						wp_update_post($product);
					}

					break;

				}

			}

		}

	}

	add_action('wp', 'easytab_generate_tab_content', 1);


	function easytab_get_product_categories_data($product) {

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

		return $product_categories;

	}
	

	function easytab_get_product_tags_data($product) {

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

		return $product_tags;

	}