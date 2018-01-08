<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class C2QF_Admin {

	public function __construct() {
		
		add_action( 'init', array( $this, 'includes' ) );
	}

	public function includes() {
		
		include_once( dirname( __FILE__ ) . '/class-c2qf-admin-menu.php' );
	}

}

return new C2QF_Admin();