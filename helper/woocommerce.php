<?php

	defined('ABSPATH') || exit;


	function easytab_helper_get_woo_product_tabs() {

		global $post, $product;

		if ((!is_a($post, 'WC_Product')) && (!is_a($product, 'WC_Product'))) {
			$product = get_posts(['numberposts' => 1, 'post_type' => 'product']);
			if (!empty($product)) {
				$product = $product[0];
			} else {
				return [];
			}
		}

		$wc_tabs = !empty(get_option('easytab_pro_third_parties_tabs')) ? get_option('easytab_pro_third_parties_tabs') : [];

		$product = null;

		if (function_exists('is_product') && is_product() && !is_admin()) {
			$product = new WC_Product(get_the_ID());
		}

		$wc_tabs['description'] = array(
			'title' => __('Description', EASYTAB_DOMAIN),
			'priority' => 10,
			'callback' => 'woocommerce_product_description_tab'
		);

		if (is_admin() || (isset($product) && (($product->has_weight()) || $product->has_dimensions()))) {

			$title = apply_filters('woocommerce_product_additional_information_heading', __('Additional information', EASYTAB_DOMAIN));

			$wc_tabs['additional_information'] = array(
				'title' => ($title !== "") ? $title : __('Additional information', EASYTAB_DOMAIN),
				'priority' => 20,
				'callback' => 'woocommerce_product_additional_information_tab'
			);

		}

		unset($wc_tabs['reviews']);

		return $wc_tabs;

	}


	function easytab_helper_product_meta_fields_list() {

		global $post, $wpdb;

		$cpts = ['product', 'product_variation'];

		$all_product_meta_fields = [];

		foreach ($cpts as $cpt) {

			$args = array(
				'post_type' => $cpt,
				'posts_per_page' => -1,
				'post_status' => 'publish',
			);

			$loop = new WP_Query($args);

			if ($loop->have_posts()) {

				while ($loop->have_posts()) {

					$loop->the_post();

					$post = get_post();

					$query = $wpdb->prepare("SELECT meta_key FROM {$wpdb->postmeta} WHERE post_id = %d", $post->ID);

					$product_meta_fields = $wpdb->get_results($query);

					foreach ($product_meta_fields as $product_meta_field) {
						$all_product_meta_fields[$product_meta_field->meta_key] = esc_html(preg_replace('/[^a-zA-Z\s]/', '', $product_meta_field->meta_key));
					}

				}

			}

		}

		wp_reset_postdata();

		return $all_product_meta_fields;

	}


	function easytab_helper_product_taxonomies_list() {

		$taxonomies = null;

		foreach (get_object_taxonomies('product', 'objects') as $taxonomy) {

			$taxonomies[$taxonomy->name] = esc_attr($taxonomy->label);

		}

		return $taxonomies;

	}

	
	function easytab_pro_helper_save_third_parties_tabs($tabs) {

		$current_value = get_option('easytab_pro_third_parties_tabs');

		if (!empty($current_value)) {

			$check = true;

			$third_parties_tab = $current_value;

			foreach ($tabs as $tab_key => $tab_values) {

				if (array_key_exists($tab_key, $current_value)) {
					continue;
				} else {
					$third_parties_tab[$tab_key] = $tab_values;
					$check = false;
				}

			}

			if (!$check) {
				update_option('easytab_pro_third_parties_tabs', $third_parties_tab);
			}

		} else {

			$third_parties_tab = [];

			foreach ($tabs as $tab_key => $tab_values) {
				$third_parties_tab[$tab_key] = $tab_values;
			}

			update_option('easytab_pro_third_parties_tabs', $third_parties_tab);

		}

		return $third_parties_tab;

	}

	add_filter('woocommerce_product_tabs', 'easytab_pro_helper_save_third_parties_tabs', 999, 1);


	function easytab_helper_get_woo_default_product_tabs() {

		$product = null;

		if (function_exists('is_product') && is_product() && !is_admin()) {
			$product = new WC_Product(get_the_ID());
		}

		$wc_tabs['description'] = array(
			'title' => __('Description', EASYTAB_DOMAIN),
			'priority' => 10,
			'callback' => 'woocommerce_product_description_tab'
		);

		if (is_admin() || (isset($product) && (($product->has_weight()) || $product->has_dimensions()))) {

			$wc_tabs['additional_information'] = array(
				'title'    => apply_filters('woocommerce_product_additional_information_heading', __('Additional information', EASYTAB_DOMAIN)),
				'priority' => 20,
				'callback' => 'woocommerce_product_additional_information_tab'
			);

		}

		return $wc_tabs;

	}

	
	function easytab_helper_tabs_templates_override($template, $template_name, $template_path) {

		$base_dir = EASYTAB_PATH . 'wc-templates-override/';

		$plugin_template = $base_dir . $template_name;

		if (file_exists($plugin_template)) {
			return $plugin_template;
		}

		return $template;

	}

	add_filter('woocommerce_locate_template', 'easytab_helper_tabs_templates_override', 10, 3);