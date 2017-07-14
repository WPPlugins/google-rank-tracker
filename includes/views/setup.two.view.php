<div class="wrap">
	<h2><?php _e( "KnowMyRankings: Installation - Step 2", 'know-my-rankings');?></h2>

	<?php 
		if ( $response['status'] == "ERR" ) {
			$this->notices->show_notice( $response['body'], 'error' );
			return;
		}
	?>

	<div class='kmr-report-box'>

		<h3><?php _e( 'Create Your Account', 'know-my-rankings' ) ?></h3>
		<p><?php _e( "Please create an account on KnowMyRankings.com to finalize your installation. KnowMyRankings.com will check your rankings daily, and deliver it right to your WordPress dashboard. There's no work required from you - and your server will never make any requests to Google.", "know-my-rankings");?></p>
		<form action="?page=<?php echo $this->page_slug; ?>_settings" method="post">
			<p>
				<label for="email"><?php _e( 'Your E-mail Address', 'know-my-rankings' ) ?></label><br />
				<input id="email" name="email" type="text" class='kmr-text-input' value=""/>
			</p>
			<p>
				<label for="password"><?php _e( 'Desired Password', 'know-my-rankings' ) ?></label><br />
				<input id="password" name="password" type="password" class='kmr-text-input' value=""/>
			</p>
			<p class="password-confirm">
				<label for="password_confirm"><?php _e( 'Confirm Password', 'know-my-rankings' ) ?></label><br />
				<input id="password_confirm" name="password_confirm" type="password" class='kmr-text-input' value=""/>
			</p>
			<p>
				<input id="have_account" name="have_account" type="checkbox" value="true"/>
				<label for="have_account"><?php _e( 'I already have an account on KnowMyRankings.com', 'know-my-rankings' ) ?></label>
			</p>
			<p class="submit">
				<button class="button button-primary kmr-install-button" type="submit" value="submit"><?php _e( 'Complete Installation', 'know-my-rankings' ) ?></button>
			</p>

			<p><?php _e( "Click the button above to finalize the installation. You'll then be able to view your rank reports on your Dashboard or from the KnowMyRankings menu in WordPress.", 'know-my-rankings' ) ?></p>

			<input name="url" type="hidden" value="<?php echo $args['url']; ?>"/>
			<input name="keywords" type="hidden" value="<?php echo $args['keywords']; ?>"/>
			<?php wp_nonce_field( 'setup', 'step_two_nonce' ); ?>
		</form>

		<h3><?php _e( 'Here are your current rankings:', 'know-my-rankings' ) ?></h3>
		<?php $this->show_report( $response, "burst" ); ?>
	</div>
</div>