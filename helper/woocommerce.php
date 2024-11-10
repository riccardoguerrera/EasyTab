<?php

    function easytab_helper_get_woo_default_product_tabs() {

		$product = null;

	    if (function_exists('is_product') && is_product() && !is_admin()) {
		    $product = new WC_Product(get_the_ID());
	    }

	    $wc_tabs["description"] = array(
			"title" => __("Description", "easytab"),
			"priority" => 10,
			"callback" => "woocommerce_product_description_tab"
		);

	    if (is_admin() || (isset($product) && (($product->has_weight()) || $product->has_dimensions()))) {

		    $wc_tabs["additional_information"] = array(
			    "title"    => apply_filters( 'woocommerce_product_additional_information_heading', __( 'Additional information', 'easytab' ) ),
			    "priority" => 20,
			    "callback" => "woocommerce_product_additional_information_tab"
		    );

	    }

	    return $wc_tabs;

    }

	add_filter('woocommerce_product_tabs', 'easytab_helper_get_woo_default_product_tabs');
