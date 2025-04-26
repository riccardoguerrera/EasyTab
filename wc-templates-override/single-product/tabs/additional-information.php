<?php

	defined('ABSPATH') || exit;


	global $product;

	if ($product->has_weight() || $product->has_dimensions()) {
		
		$tab_content = get_post_meta(get_the_ID(), 'easytab_additional_information_tab', true);

		if (!empty($tab_content)) {

			echo wp_kses_post($tab_content);

			do_action('woocommerce_product_additional_information', $product);

		}

	}