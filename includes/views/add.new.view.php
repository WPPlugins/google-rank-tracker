<div class="wrap">
	<h2><?php _e( "Add Report", "know-my-rankings");?> <a href="<?php echo $this->get_url("all_reports"); ?>" class="add-new-h2"><?php _e( "Back to all reports", "know-my-rankings");?></a></h2>

	<?php
		if ( ! empty ( $validation ) ) {
			$this->notices->show_notice( $validation, "error" );
		}

		do_action("kmr_add_new_view_notice");
	?>
	<form id="add_url_form" action="" method="post">
		<p>
			<label for="url"><?php _e( 'Track Google Rankings for this URL', 'know-my-rankings' ) ?></label><br />
			<input class="widefat" id="kmr_url" name="url" type="text" value="<?php echo get_bloginfo("url"); ?>"/>
		</p>
		<p>
			<label for="keywords"><?php _e( 'Keyword Phrases <small>(1 per line)</small>', 'know-my-rankings' ) ?></label><br />
			<textarea class="widefat" name="keywords" id="kmr_keywords" cols="30" rows="10"></textarea>
			<button class="button button-primary kmr-install-button" type="submit" value="submit"><?php _e( 'Add report', 'know-my-rankings' ) ?></button>
		</p>
		<?php wp_nonce_field( 'reports', 'add_new_report' ); ?>
	</form>
</div>