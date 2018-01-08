<?php
/**
 * Plugin Name: Cart to Quote Feature
 * Description: Extension for the <strong>Cart to Quote for Woocommerce</strong> plugin.
 * Version: 1.0
 * Author: Jack Ananchenko
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'C2Q_Feature' ) ) :

final class C2Q_Feature {

	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	private function define_constants() {
		$this->define( 'C2QF_ABSPATH', dirname( __FILE__ ) . '/' );
		$this->define( 'C2QF_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
	}

	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	public function includes() {

		include_once( C2QF_ABSPATH . 'includes/class-c2qf-install.php' );
		include_once( C2QF_ABSPATH . 'includes/class-c2qf-filter-helper.php' );
		include_once( C2QF_ABSPATH . 'includes/class-c2qf-widget-helper.php' );
		include_once( C2QF_ABSPATH . 'includes/class-c2qf-functions.php' );
		include_once( C2QF_ABSPATH . 'includes/class-c2qf-woo-c2q.php' );
		include_once( C2QF_ABSPATH . 'includes/class-c2qf-menu-quotelist.php' );
		include_once( C2QF_ABSPATH . 'includes/class-c2qf-quote-pdf.php' );
		
		include_once( C2QF_ABSPATH . 'includes/class-c2qf-rewrite-wpforms.php' );
		
		include_once( C2QF_ABSPATH . 'includes/admin/class-c2qf-admin.php' );
	}

	private function init_hooks() {

		register_activation_hook( __FILE__, array( 'C2QF_Install', 'install' ) );

		add_action( 'init', array( $this, 'init' ) );

		// Remove scripts from footer
		add_action( 'wp_footer', array( $this, 'remove_scripts_footer' ) );
	}

	public function init() {

		// insert some JS and CSS
    	add_action('wp_enqueue_scripts', array ($this, 'c2qf_scripts') );
	}

	public function c2qf_scripts() {
		global $woocommerce;

		if ( $this->is_request( 'frontend' ) ) {
			// Css
			wp_enqueue_style( 'с2q-front-style', C2QF_PLUGIN_DIR_URL . 'assets/css/c2q-feature.css' );

			// Js
			wp_register_script( 'с2q-front-js', C2QF_PLUGIN_DIR_URL . 'assets/js/c2q-widget.js', array( 'jquery' ), null, true);
			wp_enqueue_script( 'с2q-front-js' );

			$vars = array( 
				'ajax_url' => admin_url( 'admin-ajax.php' ), 
				'cart_url' => $woocommerce->cart->get_cart_url(),
				'woo_ajax' => get_option('woocommerce_enable_ajax_add_to_cart'),
				'cartredirect' => get_option('woocommerce_cart_redirect_after_add'),
				'items_in_quote' => C2QF_Widget_Helper::get_items_count()
			);
		    
		    wp_localize_script( 'с2q-front-js', 'c2qvars', $vars );
		}
	}

	public function remove_scripts_footer() {
		wp_deregister_script( 'c2q_js' );
	}

}

endif;

C2Q_Feature::instance();