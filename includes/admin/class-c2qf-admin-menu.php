<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class C2QF_Admin_Menu {

	private $login_greeting_menu_item_id;

	public function __construct() {

		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_init', array( $this, 'admin_register_filter_settings' ) );
			add_action( 'admin_init', array( $this, 'add_nav_menu_meta_boxes' ) );

			$this->add_menu_location();
		}

		add_filter( 'nav_menu_link_attributes', array( $this, 'change_menu_item_url' ), 10, 3 );
		add_filter( 'the_title', array( $this, 'change_menu_item_title' ), 10, 2 );
	}

	public function add_menu_location() {

		register_nav_menu( 'top-head-menu', __('Top Head Menu') );
	}

	public function admin_menu() {

		add_menu_page( __( 'ID for Order & Quote' ), __( 'ID for Order & Quote' ), 'manage_options', 'c2qf_id_filter', array( $this, 'c2qf_id_filter_settings' ), 'dashicons-image-filter' );
		add_submenu_page( 'c2qf_id_filter', 'Quote Discount', 'Quote Discount', 'manage_options', 'c2qf_quote_discount', array( $this, 'c2qf_quote_discount_settings' ) );
	}

	public function c2qf_id_filter_settings() {

		C2QF_Filter_Helper::output_filter_fields();
	}

	public function c2qf_quote_discount_settings() {

		C2QF_Filter_Helper::output_quote_discount_fields();
	}

	public function admin_register_filter_settings() {

		C2QF_Filter_Helper::generate_filter_settings_fields();
	}

	public function add_nav_menu_meta_boxes() {
		add_meta_box( 'login-greeting_nav_link', __( 'Login and Greeting' ), array( $this, 'nav_menu_links' ), 'nav-menus', 'side', 'low' );
	}

	public function nav_menu_links() {

		?>
		<div id="posttype-login-greeting" class="posttypediv">
			<div id="tabs-panel-login-greeting" class="tabs-panel tabs-panel-active">
				<ul id="login-greeting-checklist" class="categorychecklist form-no-clear">
					<li>
						<label class="menu-item-title">
							<input type="checkbox" class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]" value="-1" />
							<?php _e( 'Login & Greeting' ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="custom">
						<input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="Login / Register">
						<input type="hidden" class="menu-item-url" name="menu-item[-1][menu-item-url]" value="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" >
						<input type="hidden" class="menu-item-classes" name="menu-item[-1][menu-item-classes]" value="c2qf-login">
					</li>
				</ul>
			</div>
			<p class="button-controls wp-clearfix">
				<span class="add-to-menu">
					<input type="submit" class="button submit-add-to-menu right" value="<?php esc_attr_e( 'Add to menu' ); ?>" name="add-post-type-menu-item" id="submit-posttype-login-greeting">
					<span class="spinner"></span>
				</span>
			</p>
		</div>
		<?php
	}

	public function change_menu_item_url( $atts, $item, $args ) {

		if( $item->classes[0] == "c2qf-login" ) {
			$this->login_greeting_menu_item_id = $item->ID;
		}

		return $atts;
	}

	public function change_menu_item_title( $title, $item_id ){
		if($item_id == $this->login_greeting_menu_item_id) {

			if( is_user_logged_in() ) {
				$firstname = wp_get_current_user()->user_firstname;
				$title = "Hi, $firstname";
			}
		}

		return $title;
	}

}

return new C2QF_Admin_Menu();