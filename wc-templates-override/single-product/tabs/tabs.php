<?php

	/**
	 * EasyTab Pro
	 */
    
	defined('ABSPATH') || exit;


    
	$product_tabs = apply_filters('woocommerce_product_tabs', array());

	if (!empty($product_tabs)) {

?>

    <div class="woocommerce-tabs wc-tabs-wrapper">

        <ul class="tabs wc-tabs" role="tablist">

<?php

		foreach ($product_tabs as $key => $product_tab) {

			$key = str_replace('-', '_', $key);
			$tab_content = get_post_meta(get_the_ID(), 'easytab_' . $key . '_tab', true);

			if (empty($tab_content)) {
				$tab_content = get_post_meta(get_the_ID(), 'easytab_' . $key . '_tab', true);
			}

			if (!empty($tab_content) || ($key === 'reviews')) {

?>

			<li class="<?php echo esc_attr($key); ?>_tab" id="tab-title-<?php echo esc_attr($key); ?>" role="tab" aria-controls="tab-<?php echo esc_attr($key); ?>"<?php echo (empty($tab_content) && ($key !== 'reviews')) ? ' style="display: none"' : ''; ?>>
				<a href="#tab-<?php echo esc_attr($key); ?>">
				<?php echo wp_kses_post(apply_filters('woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key)); ?>
				</a>
			</li>

<?php

			}

		}

?>

	    </ul>

<?php

		foreach ($product_tabs as $key => $product_tab) {

			$key = str_replace('-', '_', $key);
			$tab_content = get_post_meta(get_the_ID(), 'easytab_' . $key . '_tab', true);

			if (!empty($tab_content) || ($key === 'reviews')) {

?>

		<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr($key); ?> panel entry-content wc-tab" id="tab-<?php echo esc_attr($key); ?>" role="tabpanel" aria-labelledby="tab-title-<?php echo esc_attr($key); ?>">

<?php

				if (empty($tab_content)) {
					if ( isset( $product_tab['callback'] ) ) {
						call_user_func($product_tab['callback'], $key, $product_tab);
					}
				} else {
					echo wp_kses_post($tab_content);
				}

?>

    	</div>

<?php

			}

			do_action('woocommerce_product_after_tabs');

		}

?>

	</div>

<?php

	}


	function easytab_default_view($product_tabs) {

		ob_start();

	?>

		<ul class="tabs wc-tabs" role="tablist">

	<?php 

		foreach ($product_tabs as $key => $product_tab) {

			if ($key === 'additional_information') {
				
				$tab_content = get_post_meta(get_the_ID(), 'easytab_additional_information_tab', true);

				if (empty($tab_content)) continue; 

			}

	?>

			<li class="<?php echo esc_attr($key); ?>_tab" id="tab-title-<?php echo esc_attr($key); ?>" role="tab" aria-controls="tab-<?php echo esc_attr($key); ?>"><a href="#tab-<?php echo esc_attr($key); ?>"><?php echo wp_kses_post(apply_filters('woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key)); ?></a></li>

	<?php 

		}

	?>

		</ul>

	<?php 

		foreach ($product_tabs as $key => $product_tab) {

			if ($key === 'additional_information') {
				
				$tab_content = get_post_meta(get_the_ID(), 'easytab_additional_information_tab', true);

				if (empty($tab_content)) continue; 

			}

	?>

		<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr($key); ?> panel entry-content wc-tab" id="tab-<?php echo esc_attr($key); ?>" role="tabpanel" aria-labelledby="tab-title-<?php echo esc_attr($key); ?>">

	<?php

			if (isset($product_tab['callback'])) {

				call_user_func($product_tab['callback'], $key, $product_tab);

			}

	?>

		</div>

	<?php

		}
		
		do_action( 'woocommerce_product_after_tabs' ); 

		return ob_get_clean();

	}