<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class C2QF_Functions {

	public function __construct() {
		
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {

		// Add metadata to the created order
		add_action( 'woocommerce_payment_complete_order_status', array( $this, 'c2qf_order_payment_complete' ), 10, 2 );
		// Filtering the order ID
		add_filter( 'woocommerce_order_number', array( $this, 'c2qf_order_number' ), 10, 2 );
		// Show Quote number for Order Item 
		add_action( 'woocommerce_add_order_item_meta', array( $this, 'add_quote_for_order_item' ), 10, 3 );
		// Show a formatted Quote number in the Admin Orders page
		add_filter( 'woocommerce_order_item_get_formatted_meta_data', array( $this, 'c2qf_display_item_meta_filter' ), 10, 2 );
	}

	public function c2qf_display_item_meta_filter( $formatted_meta, $data ) {

		foreach( $formatted_meta as $key => $obj ) {

			if( $obj->key == '_quotelist' ) {
				$formatted_meta[$key]->display_key = __( 'Quotelist' );
			}
		}

		return $formatted_meta;
	}

	public function add_quote_for_order_item( $item_id, $values, $cart_item_key ) {

		if( isset($values['c2q']) ) {
			wc_add_order_item_meta( $item_id, '_quotelist', get_the_title( $values['c2q'] ) );	
		}
	}

	public function c2qf_order_payment_complete( $order_status, $order_id ) {

		self::add_filter( 'order', $order_id );

		return $order_status;
	}

	public static function add_filter( $post_type, $post_id ) {

		$c2qf_filter = get_option("c2qf_{$post_type}");

		if( !empty($c2qf_filter) ) {

			$filter_prefix = $c2qf_filter["prefix"];
			$filter_id = $c2qf_filter["next_id"];

			if( $post_type == 'order' ) {

				add_post_meta( $post_id, 'c2qf_filter',
					array(
						'prefix' => $filter_prefix,
						'id' => $filter_id
					), true
				);

			} elseif( $post_type == 'quote' ) {

				wp_update_post( array(
					'ID' => $post_id,
					'post_title' => $filter_prefix.$filter_id
				) );

			}

			++$c2qf_filter['next_id'];
			update_option( "c2qf_{$post_type}", $c2qf_filter );
		}

	}
	
	function c2qf_order_number( $order_id, $order ) {

		$filter = get_post_meta( $order_id, 'c2qf_filter', true );

		if( $filter ) {
			$order_id = $filter['prefix'].$filter['id'];
		}

		return $order_id;
	}

}

return new C2QF_Functions();