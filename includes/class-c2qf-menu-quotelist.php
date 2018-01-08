<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class C2QF_Menu_Quotelist {

	public function __construct() {

		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {

		add_filter( 'wp_nav_menu_items', array( $this, 'quotelist_menu_item' ), 10, 2 );
	}

	function quotelist_menu_item( $items, $args ) {

	    if ($args->theme_location == 'top-head-menu') {

	    	$c2q_quotelist_page = get_the_permalink(get_option('c2q_quotelist_page'));
		    if (function_exists('icl_object_id')) {
				$c2q_quotelist_page = get_the_permalink(icl_object_id(get_option('c2q_quotelist_page'), 'post', true));
		    }

		    $items_quantity = C2QF_Widget_Helper::get_items_count();

			if($items_quantity != 1) {
				$letter = 's';
			}

			$title = sprintf( '<span class="title-quantity">%d</span> ' . __("Item%s in Quote"), $items_quantity, $letter );
			$quotelist_mini = '<div class="mini_quotelist_wrap quotelist-menu-item woocommerce">'. get_quotelist_mini() . '</div>';

			$items .=
			'<li class="ubermenu-item ubermenu-item-has-children ubermenu-item-level-0 ubermenu-has-submenu-drop">
				<a class="ubermenu-target ubermenu-noindicator" href="'. $c2q_quotelist_page .'">
					<span class="ubermenu-target-title ubermenu-target-text">'. $title .'</span>
				</a>
				<ul class="ubermenu-submenu ubermenu-submenu-id-5072 ubermenu-submenu-type-flyout ubermenu-submenu-drop ubermenu-submenu-align-left_edge_item quotelist-submenu">
					<li class="ubermenu-item ubermenu-item-type-post_type ubermenu-item-object-page ubermenu-item-5081 ubermenu-item-auto ubermenu-item-normal ubermenu-item-level-1">'. $quotelist_mini .'</li>
					<li class="waiting">
						<img src="'. includes_url('images/spinner.gif') .'" class="icon">
					</li>
					<li class="ubermenu-retractor ubermenu-retractor-mobile"><i class="fa fa-times"></i> Close</li>
				</ul>
			</li>';
	    }
	    return $items;
	}

}

return new C2QF_Menu_Quotelist();