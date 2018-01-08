<?php
	if ( isset( $_GET['settings-updated'] ) ) {
		add_settings_error( 'c2qf_discount', 'c2qf_discount', __( 'Settings Saved' ), 'updated' );
	}
 	settings_errors( 'c2qf_discount' );
?>
<div class="wrap">
	<form action="options.php" method="post">
		<?php settings_fields( 'discount_for_quote' ); ?>
		<?php do_settings_sections( 'discount_for_quote' ); ?>
		<?php submit_button( __( 'Save' ) ); ?>
	</form>
</div>