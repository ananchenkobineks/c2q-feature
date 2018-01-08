<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class C2QF_Rewrite_WPForms {

	public function __construct() {
		
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {

		add_action( 'wpforms_process_complete', array( $this, 'save_info_to_wpuser' ), 10, 4 );
	}

	public function save_info_to_wpuser( $fields, $entry, $form_data, $entry_id ) {

		$user_data = array();

		foreach($fields as $field) {
			if( $field['name'] == 'E-mail' ) {
				$user_data['email'] = $field['value'];
			}
			if( $field['name'] == 'Your Name' ) {
				$user_data['first_name'] = $field['first'];
				$user_data['last_name'] = $field['last'];
			}
			if( $field['name'] == 'Agency Name' ) {
				$user_data['agency_name'] = $field['value'];
			}
			if( $field['name'] == 'Agency Address' ) {
				$user_data['address1'] = $field['address1'];
				$user_data['address2'] = $field['address2'];
				$user_data['city'] = $field['city'];
				$user_data['state'] = $field['state'];
				$user_data['postal'] = $field['postal'];
				$user_data['country'] = $field['country'];
			}
			if( $field['name'] == 'Agency Phone' ) {
				$user_data['phone'] = $field['value'];
			}
		}

		$country_states = WC()->countries->get_states();
		$country_states = $country_states[ $user_data['country'] ];

		if( !empty($country_states) ) {
			foreach( $country_states as $state_key => $state_name ) {

				if( $user_data['state'] == $state_name ) {
					$user_data['state'] = $state_key;
					break;
				}
				
			}
		}

		$user = get_user_by( 'email', $user_data['email'] );

		wp_update_user( array( 
			'ID' => $user->ID,
			'first_name' => $user_data['first_name'],
			'last_name' => $user_data['last_name']
		) );

		add_user_meta( $user->ID, 'billing_first_name', $user_data['first_name'], true );
		add_user_meta( $user->ID, 'billing_last_name', $user_data['last_name'], true );
		add_user_meta( $user->ID, 'billing_company', $user_data['agency_name'], true );
		add_user_meta( $user->ID, 'billing_address_1', $user_data['address1'], true );
		add_user_meta( $user->ID, 'billing_address_2', $user_data['address2'], true );
		add_user_meta( $user->ID, 'billing_city', $user_data['city'], true );
		add_user_meta( $user->ID, 'billing_postcode', $user_data['postal'], true );
		add_user_meta( $user->ID, 'billing_country', $user_data['country'], true );
		add_user_meta( $user->ID, 'billing_state', $user_data['state'], true );
		add_user_meta( $user->ID, 'billing_phone', $user_data['phone'], true );
	}
	
}

new C2QF_Rewrite_WPForms();