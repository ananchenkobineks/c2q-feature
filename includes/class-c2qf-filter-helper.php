<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class C2QF_Filter_Helper {

	/**
	 * C2QF order option data
	 *
	 */
	public $_c2qf_order_option;

	public static function generate_filter_settings_fields() {

		register_setting( 'filter_id_for_quote_and_order', 'c2qf_order_prefix', array( __CLASS__, 'update_c2qf_filter' ) );

		add_settings_section(
			'c2qf_filter_section',
			__( 'Filter for Orders and Quotes ID' ),
			'__return_false',
			'filter_id_for_quote_and_order'
		);
		add_settings_field(
			'c2qf_order_prefix',
			__( 'Order Prefix' ),
			array( __CLASS__, 'order_prefix_func' ),
			'filter_id_for_quote_and_order',
			'c2qf_filter_section'
		);
		add_settings_field(
			'c2qf_order_id',
			__( 'Order ID' ),
			array( __CLASS__, 'c2qf_order_id_func' ),
			'filter_id_for_quote_and_order',
			'c2qf_filter_section'
		);
		add_settings_field(
			'c2qf_quote_prefix',
			__( 'Quote Prefix' ),
			array( __CLASS__, 'c2qf_quote_prefix_func' ),
			'filter_id_for_quote_and_order',
			'c2qf_filter_section'
		);
		add_settings_field(
			'c2qf_quote_id',
			__( 'Quote ID' ),
			array( __CLASS__, 'c2qf_quote_id_func' ),
			'filter_id_for_quote_and_order',
			'c2qf_filter_section'
		);

		register_setting( 'discount_for_quote', 'c2qf_discount' );

		add_settings_section(
			'c2qf_discount_section',
			__( 'Discount for Quote Items' ),
			'__return_false',
			'discount_for_quote'
		);
		add_settings_field(
			'c2qf_discount',
			__( 'Quote Discount' ),
			array( __CLASS__, 'discount_quote_func' ),
			'discount_for_quote',
			'c2qf_discount_section'
		);

	}

	public static function order_prefix_func() { ?>

		<?php $c2qf_order = get_option('c2qf_order'); ?>

		<input type="text" name="c2qf_order_prefix" value="<?php echo $c2qf_order['prefix'] ?>" /> <em><?php _e( 'Orders will begin with this prefix.' ); ?></em>
	<?php
	}

	public function c2qf_order_id_func() { ?>

		<?php $c2qf_order = get_option('c2qf_order'); ?>

		<input type="number" name="c2qf_order_id" value="<?php echo $c2qf_order['id'] ?>" /> <em><?php _e( 'Orders will be incremented starting with this ID.' ); ?></em>
	<?php
	}

	public function c2qf_quote_prefix_func() { ?>

		<?php $c2qf_quote = get_option('c2qf_quote'); ?>

		<input type="text" name="c2qf_quote_prefix" value="<?php echo $c2qf_quote['prefix'] ?>" /> <em><?php _e( 'Quotes will begin with this prefix.' ); ?></em>
	<?php
	}

	public function c2qf_quote_id_func() { ?>

		<?php $c2qf_quote = get_option('c2qf_quote'); ?>

		<input type="number" name="c2qf_quote_id" value="<?php echo $c2qf_quote['id'] ?>" /> <em><?php _e( 'Quotes will be incremented starting with this ID.' ); ?></em>
	<?php
	}

	public static function output_filter_fields() {

		include( dirname( __FILE__ ) . '/admin/views/html-admin-filter-settings.php' );
	}

	public static function update_c2qf_filter() {

		$c2qf_order = get_option('c2qf_order');
		$c2qf_quote = get_option('c2qf_quote');

		$c2qf_order['prefix']	= $_POST['c2qf_order_prefix'];
		$c2qf_quote['prefix']	= $_POST['c2qf_quote_prefix'];

		if( $c2qf_order['id'] != $_POST['c2qf_order_id'] ) {
			$c2qf_order['id'] = $_POST['c2qf_order_id'];
			$c2qf_order['next_id'] = $_POST['c2qf_order_id'];
		}

		if( $c2qf_quote['id'] != $_POST['c2qf_quote_id'] ) {
			$c2qf_quote['id'] = $_POST['c2qf_quote_id'];
			$c2qf_quote['next_id'] = $_POST['c2qf_quote_id'];
		}

		update_option( 'c2qf_order', $c2qf_order );
		update_option( 'c2qf_quote', $c2qf_quote );

		add_settings_error( 'c2qf', '', __( 'ID Filter updated' ), 'updated' );

		return false;
	}

	public static function output_quote_discount_fields() {

		include( dirname( __FILE__ ) . '/admin/views/html-admin-quote-discount-settings.php' );
	}

	public function discount_quote_func() { ?>

		<?php $c2qf_discount = get_option('c2qf_discount'); ?>

		<input type="number" name="c2qf_discount" min="0" value="<?php echo $c2qf_discount ?>" /> <em><?php _e( 'Discount for Quote after auto approve.' ); ?></em>
	<?php
	}

}