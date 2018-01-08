<?php settings_errors('c2qf'); ?>
<div class="wrap">
	<form action="options.php" method="post">
		<?php settings_fields( 'filter_id_for_quote_and_order' ); ?>
		<?php do_settings_sections( 'filter_id_for_quote_and_order' ); ?>
		<?php submit_button( __( 'Save' ) ); ?>
	</form>
</div>