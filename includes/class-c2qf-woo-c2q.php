<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class C2QF_Rewrite_Woo_C2Q {

	public function __construct() {
		
		add_action( 'init', array( $this, 'init' ) );

		// AJAX - check the product quantity before adding to the quotelist
		add_action( 'wp_ajax_check_product_quantity', array( $this, 'c2q_ajax_check_product_quantity' ) );
		add_action( 'wp_ajax_nopriv_check_product_quantity', array( $this, 'c2q_ajax_check_product_quantity' ) );

		// AJAX - remove item from quotelist
		add_action( 'wp_ajax_remove_item_from_quotelist', array( $this, 'c2q_ajax_remove_item_from_quotelist' ) );
		add_action( 'wp_ajax_nopriv_remove_item_from_quotelist', array( $this, 'c2q_ajax_remove_item_from_quotelist' ) );
	}

	public function init() {

		// Image size for products in Quotelist 
		add_image_size( 'size-32x32', 32, 32 );

		// Add auto approve for Quote
		add_action( 'c2q_submit_quote_save', array( $this, 'auto_approve_quote' ), 10, 4 );
		// Form for adding Quotes to the cart
		add_filter( 'c2q_quotes_table_subfooter', array( $this, 'c2q_table_subfooter' ) );
		// Change quotelist product table
		add_filter( 'c2q_quotelist_body', array( $this, 'c2q_change_quotelist' ) );
		// Change quotelist widget
		add_filter( 'quotelist_mini_output', array( $this, 'c2q_quotelist_mini_output' ), 10, 2 );
		// Add quote to cart
		add_action( 'template_redirect', array( $this,'c2q_add_to_cart_quote' ), 10 );
		// Change product qty
		add_action( 'template_redirect', array( $this,'c2q_add_to_quotelist' ), 30 );
		// Add pdf button after single quote table
		add_filter('c2q_after_quote_table', array( $this, 'add_button_after_single_quote' ) );

		// Translate text for C2Q
		add_filter( 'gettext', array( $this, 'aad_translate_words_array' ) );
		add_filter( 'ngettext', array( $this, 'aad_translate_words_array' ) );
	}

	public function auto_approve_quote( $id, $c2q_quoted_products, $c2q_quoted_user, $post_request ) {

		// Change quote ID
		C2QF_Functions::add_filter( 'quote', $id );
		update_post_meta( $id, 'c2q_quoted_status', 'approved' );
		
		$discount = get_option('c2qf_discount');

		if( $discount > 0 ) {
			foreach($c2q_quoted_products as $key => $product) {

				$price = $product['price'];
				$price = $price * (1 - $discount/100);
				$c2q_quoted_products[ $key ]['price'] = $price;
			}
			update_post_meta($id, 'c2q_quoted_products', $c2q_quoted_products);
		}

		c2q_empty_quotelist($c2q_quoted_user);

		wp_redirect( get_permalink($id) );
		exit;
	}

	public function c2q_table_subfooter( $subfooter_array ) {
		global $post;

		$status = get_post_meta($post->ID, 'c2q_quoted_status', true);

		$hide_buttons = apply_filters('c2q_hide_buttons_statuses', array(
			'rejected',
			'completed',
			'pending',
			'onhold',
			'cancelled'
		));

	    if (!in_array($status, $hide_buttons)) {
	    	$subfooter_array['price'] = '
			<td class="c2q-action-td">
				<form class="q2c q2c_reject woocommerce" name="c2q_reject_form" method="post">
					<input type="hidden" name="post_id" value="'. $post->ID .'"/>
					<input type="hidden" name="action" value="reject"/>
					<button type="submit" class="c2q_button button btn btn-danger">'. __('Reject offer', 'c2q') .'</button>
				</form>
				<form class="q2c q2c_add_to_cart woocommerce" name="c2q_add_to_cart_form" method="post">
					<input type="hidden" name="post_id" value="'. $post->ID .'"/>
					<input type="hidden" name="action" value="c2q_add_to_cart"/>
					<button type="submit" class="c2q_button button btn btn-success alt">'. __('Add to cart', 'c2q-feature') .'</button>
				</form>
			</td>
			';
	    }

		return $subfooter_array;
	}

	public function c2q_change_quotelist( $body_array ) {

    	$user_id = get_current_user_id();
  		$data = get_quotelist_data( $user_id );

  		foreach( $data as $row ) {

  			$post_id = $row->prod_id;

			$over  = get_post_meta( $post_id, '_wpbo_override', true );

			if( $over == 'on' ) {

				$min   = get_post_meta( $post_id, '_wpbo_minimum',  true );
				$max   = get_post_meta( $post_id, '_wpbo_maximum',  true );
				$step  = get_post_meta( $post_id, '_wpbo_step',     true );

				$body_array[$row->ID]['quantity'] = '<input class="input-text qty text" type="number" min="'. $min .'" step="'. $step .'" max="'. $max .'" name="quotelist_quantity['. $row->ID .']" id="quotelist_quantity_'. $row->ID .'" value="'. $row->quantity .'" />';
			}
  		}

		return $body_array;
	}

	public function c2q_quotelist_mini_output( $out, $data ) {

		if (!empty($data) && c2q_can_user_quotebutton() === true) {

			foreach ( $data as $row ) {

				$variation_link = $attr_output = '';

				if (isset($row->variation_id) && intval($row->variation_id) > 0) {
					$product_id = $row->variation_id;
					$variation_link = '?'.str_replace(array(',', '||'), array('&amp;', '='), htmlentities(stripslashes($row->prod_attr)));


					$attr = c2q_get_attributes_from_db($row);
					$attr_output = c2q_attributes_ouput($attr, $product_id);

				} else {
					$product_id = $row->prod_id;
				}

				$_pf = new WC_Product_Factory();
				$product = $_pf->get_product($product_id);
				if (get_option('wc_settings_tab_c2q_remove_prices') == 'yes') {
					$price = '';
				} else {
					$price = apply_filters('c2q_mini_row_price', $product->get_price(), $row);
					$price = wc_price($price * $row->quantity);
				}

				ob_start();
				do_action('c2q_after_mini_line', $row);
				$c2q_after_mini_line = ob_get_contents();
				ob_end_clean();

				$item_removed = C2QF_Widget_Helper::is_item_removed();

				$c2q_quotelist_page = get_the_permalink(get_option('c2q_quotelist_page'));
			    if (function_exists('icl_object_id')) {
					$c2q_quotelist_page = get_the_permalink(icl_object_id(get_option('c2q_quotelist_page'), 'post', true));
			    }

				if( $item_removed != $row->ID ) {
					$new_out .= '
					<div class="clearfix mini_quotelist_row product_'.$row->ID.'">
						<ul class="mini_quotelist_line">
							<li>
								<a href="'. $c2q_quotelist_page .'?remove='. $row->ID .'&_wpnonce='. wp_create_nonce( 'quotelist' ) .'" class="remove" title="Remove this item">×</a>
								<a href="'. get_the_permalink($row->prod_id) .'" class="title">'.get_the_title($row->prod_id). get_the_post_thumbnail($row->prod_id, 'size-32x32') .'</a>
								<span class="price"> '.$row->quantity .' - '. $price .'</span>
								'. $attr_output .$c2q_after_mini_line .'
							</li>
						</ul>
					</div>';
				}
			}

			if( !empty($new_out) ) {
				$new_out .= '<a href="'. $c2q_quotelist_page .'" id="request-quote-page-button" class="button button_theme btn btn-primary add-request-quote-button wc-forward" title="'. __('View List', 'c2q') .'">'. __('View List', 'c2q') .'</a>';
			} else {
				$new_out = __('Your quote list is empty!', 'c2q');
			}

		} else {
			$new_out = __('Your quote list is empty!', 'c2q');
		}

		return $new_out;
	}

	public function c2q_add_to_cart_quote() {
		global $post;

		// nothing to do if the post values are not there
		if (!isset($_POST['post_id']) || !isset($_POST['action']) || !(is_singular() && get_post_type( $post ) == 'quote')) {
			return;
		}

		// if we have an accept action and post_id we should change the status of the quote
		if (intval($_POST['post_id']) > 0 && $_POST['action'] == 'c2q_add_to_cart') {

			global $woocommerce;

			// check if we should empty cart first
			if (get_option('wc_settings_tab_c2q_emptycart') == 'yes') {
				WC()->cart->empty_cart( true );
			}

			// Use get_post_meta to retrieve an existing value from the database.
			$c2q_quoted_products = get_post_meta( $post->ID, 'c2q_quoted_products', true );

			if (!empty($c2q_quoted_products)) {
				foreach ($c2q_quoted_products as $c2q_product) {

					// get rest of data that may have changed
					$product = get_post($c2q_product['id']);

					// check if the product no longer exists
					if (!$product) {
						continue; // if it no longer exists or is not longer available, it should not be added to the cart
					} else {

						$cart_item_data = array(
							'c2q' => $post->ID
						);

						$variation = '';
						$variation = c2q_string_attr_to_array($c2q_product['variations']);

						// add products to cart
						WC()->cart->add_to_cart($c2q_product['id'], $c2q_product['qty'], $c2q_product['id'], $variation, $cart_item_data);

					}
				}

				wc_add_notice('<a href="'. $woocommerce->cart->get_cart_url() .'" class="wc-forward" title="'. __('Cart', 'c2q') .'">'.__('All items have been added to your cart!', 'c2q').'</a>');
				$cart_url = $woocommerce->cart->get_cart_url();
				wp_safe_redirect( $cart_url );
				exit;
			}      
		}
	}

	public function c2q_add_to_quotelist() {

		if( isset($_POST['c2q_product_id'],$_POST['c2q_quantity']) ) {

			$_POST['c2q_quantity'] = C2QF_Widget_Helper::get_item_quantity_minimum( $_POST['c2q_product_id'] );
		}
	}

	function c2q_ajax_check_product_quantity() {

		wp_send_json_success( C2QF_Widget_Helper::get_item_quantity_minimum( $_POST['c2q_product_id'] ) );
    	die();
	}

	function c2q_ajax_remove_item_from_quotelist() {

		$parts = parse_url( $_POST['c2q_remove_link'] );
		parse_str( $parts['query'], $query );

		$result = C2QF_Widget_Helper::remove_item_from_quotelist( $query['_wpnonce'], $query['remove'] );

		if( $result != false ) {
			wp_send_json_success( $result );
		} else {
			wp_send_json_error();
		}
    	die();
	}

	public function add_button_after_single_quote( $post_id ) {

		$url = wp_nonce_url( get_site_url()."/?c2qf_action=print_pdf&quote_id={$post_id}", 'c2qf_pdf_action' );
		echo '<a href="'. $url .'" class="button btn" target="_blank">'. __("Click here to View your Quote") .'</a>';
	}

	public function aad_translate_words_array( $translated ) {

		$words = array(
			'Submit Quote List for Review' => 'Generate Quote',
			'Quotes are only available for logged in users' => 'Login to see prices'
		);

		$translated = str_ireplace(  array_keys($words),  $words,  $translated );
		return $translated;
	}

}

return new C2QF_Rewrite_Woo_C2Q();