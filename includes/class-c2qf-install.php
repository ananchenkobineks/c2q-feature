<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class C2QF_Install {

	public static function install() {

		if ( ! is_blog_installed() ) {
			return;
		}

		self::set_options();
	}

	private static function set_options() {
		// Set new WooCommerce Order ID
		add_option( 
			'c2qf_order', array(
				'prefix' => 'LEO-',
				'id' => 2000,
				'next_id' => 2000,
			), '', 'no'
		);
		// Set new Quote ID
		add_option( 
			'c2qf_quote', array(
				'prefix' => 'LEQ-',
				'id' => 1000,
				'next_id' => 1000,
			), '', 'no'
		);
		// Set Discount for Quote
		add_option( 'c2qf_discount', 0, '', 'no' );
	}
}