<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class C2QF_Quote_PDF {
	
	public function __construct() {

		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {

		add_filter('c2q_quotes_overview_body', array( $this, 'add_print_button_to_body' ) );

		add_filter( 'template_include',  array( $this, 'quote_pdf_template' ) );
	}

	public function add_print_button_to_body( $body_array ) {

		foreach( $body_array as $key => $items ) {

			if( empty($items['checkout']) ) {
				$value = 'checkout';
			} else {
				$value = 'print';
			}

			$body_array[ $key ][ $value ] = '<a href="'.wp_nonce_url("/?c2qf_action=print_pdf&quote_id={$key}", 'c2qf_pdf_action' ).'" class="button" title="'. __("View/Print Quote PDF ") .'" target="_blank">PDF</a>';
		}

		return $body_array;
	}

	public function quote_pdf_template( $original_template ) {

		if( isset($_GET['c2qf_action'], $_GET['quote_id'], $_GET['_wpnonce']) ) {
			if( wp_verify_nonce( $_GET['_wpnonce'], 'c2qf_pdf_action' ) ) {

				$this->submit_pdf_to_user();

				return C2QF_ABSPATH.'templates/pdf/page.php';
			}
		}

		return $original_template;
	}

	private function submit_pdf_to_user() {

		if( !empty($_FILES) ) {
			
			$file = get_temp_dir().'Quote #'.get_the_title($_GET['quote_id']).'.pdf';
			move_uploaded_file($_FILES['pdfFile']['tmp_name'], $file);

   			$headers = array(
   				'Content-Type: text/html; charset=UTF-8',
   				'From: '. get_bloginfo( 'name' ) .' <'. get_bloginfo( 'admin_email' ) .'>',
   			);
   			$message = "
   			<p>Hello,</p>
			<p>Thank you for your interest in SDI. Please find your quote attached.</p>
			<p>Sincerely,</p>
			<p>Security Devices International<br>
			107 Audubon Road<br>
			Building 2, Suite 201<br>
			Wakefield, MA 01880</p>
			<p>Tel 978-868-5011<br>
			Mail  info@securitydii.com</p>";

   			wp_mail(wp_get_current_user()->user_email, 'Quote Invoice', $message, $headers, $file);

   			unlink($file);
	        exit;
	    }

	}
}

return new C2QF_Quote_PDF();