<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class C2QF_Widget_Helper {

	/**
	 * Get count of items in the current quotelist
	*/
	public static function get_items_count() {
		
		$items_quantity = 0;
		
		$user_id = get_current_user_id();
		$data = get_quotelist_data($user_id);

		$item_removed = self::is_item_removed();

		if( !empty($data) ) {
			foreach ($data as $value) {
			
				if( $item_removed != $value->ID ) {
					$items_quantity += $value->quantity;	
				}

			}	
		}
		
		return $items_quantity;
	}

	/**
	 * Get count of items in the current quotelist
	 *
	 * @param int $post_id
	*/
	public static function get_item_quantity_minimum( $post_id ) {

		$over  = get_post_meta( $post_id, '_wpbo_override', true );
		$min   = get_post_meta( $post_id, '_wpbo_minimum',  true );

		if( $over != 'on' ) {
			$min = 1;
		}

		return $min;
	}

	/**
	 * Сheck whether the item was removed from quotelist
	 *
	*/
	public static function is_item_removed() {

		$item_removed = -1;

		if (isset($_GET['_wpnonce'])) {
			$nonce = $_GET['_wpnonce'];
		}
		 
		if (isset($_GET['remove'])) {
			if ( wp_verify_nonce( $nonce, 'quotelist' ) ) {
				$item_removed = intval($_GET['remove']);
			}
		}

		return $item_removed;
	}

	public static function remove_item_from_quotelist( $nonce, $item_id ) {
		global $wpdb;

		if ( wp_verify_nonce( $nonce, 'quotelist' ) ) {

			$user_id = get_current_user_id();
			$data = get_quotelist_data($user_id);

			// if there is no user and no cookie
			if ($user_id == 0 && (!isset($_COOKIE['c2q_user_token']) || esc_attr($_COOKIE['c2q_user_token']) == '')) {
				$data_user_where_query = '( 1 == 2 )';
			} else {
				// fixed user query WHERE section
				if ($user_id > 0) {
					$data_user_where_query = 
					"(user_id = ". $user_id ." OR user_token = '". esc_attr($_COOKIE['c2q_user_token']) ."')";
				} else {
					$data_user_where_query =
					"(user_token = '". esc_attr($_COOKIE['c2q_user_token']) ."')";
				}
			}

			$data_delete_query = "DELETE FROM ". $wpdb->prefix.'c2q' ." WHERE ";
			$data_delete_query .= $data_user_where_query;
			$data_delete_query .= " AND ID = ". intval($item_id) ." ";

			$execute_delete = $wpdb->query($data_delete_query);

			if ($execute_delete !== NULL || $execute_delete != 0) {

				foreach( $data as $item ) {

					if( $item->ID == $item_id ) {
						return array(
							'quantity' => $item->quantity,
							'item_id' => $item_id
						);
						break;
					}
				}
				
			}
		}

		return false;
	}

}