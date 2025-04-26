<?php

	defined('ABSPATH') || exit;


	function easytab_helper_get_all_hierarchical_terms_data($product_taxonomy = null, $product_taxonomy_term = null) {

		if (empty($product_taxonomy) || empty($product_taxonomy_term)) return null;

		$product_taxonomy_terms_info = null;

		if ($product_taxonomy->hierarchical) {

			$tmp = $product_taxonomy_term->term_id;

			$product_taxonomy_child_terms = get_terms(array(
				'taxonomy' => $product_taxonomy->name,
				'parent' => $tmp,
				'hide_empty' => true
			));

			if (!empty($product_taxonomy_child_terms)) {
				foreach ($product_taxonomy_child_terms as $key => $child_term) {
					$separator_1 = !empty($child_term->description) ? ' <-> ' : '';
					$separator_2 = !empty($child_term->name) ? ' -> ' : '';
					$product_taxonomy_terms_info[$key] = $child_term->name . $separator_1 . $child_term->description;
					$tmp = easytab_helper_get_all_hierarchical_terms_data($product_taxonomy, $child_term);
					$product_taxonomy_terms_info[$key] .= (!empty($tmp)) ? $separator_2 . $tmp : $tmp;
				}
				$product_taxonomy_terms_info = implode(' ', $product_taxonomy_terms_info);
			}


		}

		return $product_taxonomy_terms_info;

	}


	function easytab_helper_clean_html_code_in_output_response($output) {

		$re = '/^```html\s([\s\S]*?)\s```$/m';

		preg_match_all($re, $output, $matches, PREG_SET_ORDER, 0);

		return $matches;

	}