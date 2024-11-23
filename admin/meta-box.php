<?php

    defined('ABSPATH') || exit;


	function easytab_add_metabox() {

		add_meta_box(
			'easytab_product_tabs',
			'EasyTab',
			'easytab_product_tabs_metabox',
			'product',
			'normal'
		);

	}

	add_action('add_meta_boxes', 'easytab_add_metabox');


	function easytab_product_tabs_metabox($post) {

		$wc_tabs = easytab_helper_get_woo_default_product_tabs();

		foreach ($wc_tabs as $wc_tab_key => $wc_tab_data) {

			$wc_tab = get_post_meta($post->ID, 'easytab_' . str_replace('-', '_', $wc_tab_key) . '_tab', true);

	?>

		<div style="margin: 10px 0;">
			<label for="easytab-<?php echo esc_html(str_replace('_', '-', $wc_tab_key)); ?>-tab"><strong><?php echo esc_html($wc_tab_data['title']); ?></strong></label>
			<br>
			<textarea
				name="easytab_<?php echo esc_html(str_replace('-', '_', $wc_tab_key)); ?>_tab"
				id="easytab-<?php echo esc_html(str_replace('_', '-', $wc_tab_key)); ?>-tab" rows="4" style="width: 100%;"><?php echo esc_textarea($wc_tab); ?></textarea>
			<small><i><?php esc_html_e('Remove the text to regenerate the content!', 'easytab'); ?></i></small>
		</div>

	<?php

		}

		wp_nonce_field('easytab_product_tabs_nonce', 'easytab_product_tabs_nonce');

	}


	function easytab_product_tabs_save_metabox($post_id, $post, $update) {

		$nonce = !empty($_POST['easytab_product_tabs_nonce']) ? sanitize_text_field(wp_unslash($_POST['easytab_product_tabs_nonce'])) : null;

		if (!isset($nonce) ||
			!wp_verify_nonce($nonce, 'easytab_product_tabs_nonce')) {
			return;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		if (!current_user_can('edit_post', $post_id)) {
			return;
		}

		if (get_post_type($post_id) !== 'product') return;

		$wc_tabs = easytab_helper_get_woo_default_product_tabs();

		foreach ($wc_tabs as $wc_tab_key => $wc_tab_data) {
			$tab = !empty($_POST['easytab_' . str_replace('-', '_', $wc_tab_key) . '_tab']) ? sanitize_textarea_field(wp_unslash($_POST['easytab_' . str_replace('-', '_', $wc_tab_key) . '_tab'])) : null;
			update_post_meta($post_id, 'easytab_' . str_replace('-', '_', $wc_tab_key) . '_tab', $tab);
		}

	}

	add_action('save_post_product', 'easytab_product_tabs_save_metabox', 20, 3);