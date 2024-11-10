<?php

	/**
	 * EasyTab
	 */

	defined('ABSPATH') || exit;

	global $product;

	if ($product->has_weight() || $product->has_dimensions()) {

		$heading = apply_filters('woocommerce_product_additional_information_heading', __('Additional information', 'easytab'));
		$tab_content = get_post_meta(get_the_ID(), 'easytab_additional_information_tab', true);

		if ($heading) {
			echo "<h2>" . esc_html($heading) . "</h2>";
		}

		if (empty($tab_content)) {
			do_action('woocommerce_product_additional_information', $product);
		} else {
			echo wp_kses_post($tab_content);
		}

	}