<?php 

// Get URLs
$urls_response = $this->kmr->get_urls();

if ( $urls_response['status'] == "OK" ) {
	$urls = $urls_response['body']['urls'];
} 

?>
<div class="wrap">
	<div>
		<h2><?php _e( 'Settings', 'know-my-rankings' ) ?></h2>

		<?php 
		
		do_action("kmr_settings_notice");

		if ( $response['status'] == "ERR" ) {
			
			$this->notices->show_notice( $response['body'], 'error' );

		} ?>
		
		<h3><?php _e( 'Dashboard Widget', 'know-my-rankings' ) ?></h3>

		<form action="<?php echo $this->get_url( "settings" ); ?>" method="post">
			<p>
				<label for="default_report"><?php _e( 'Default Report URL (shown in Dashboard widget)', 'know-my-rankings' ) ?></label><br />
				<?php if ( ! empty( $urls ) ) : ?>
				<select id="default_report" name="default_report" class='kmr-default-report'>
				<?php foreach ( $urls as $key => $url ) : ?>
					<option value="<?php echo $url['id']; ?>" <?php selected( $this->user['default_report_id'], $url['id'] ); ?>><?php echo $url['url']; ?></option>
				<?php endforeach;?>
				</select>
				<?php else :  ?>
				<a href="<?php echo $this->get_url("add_new"); ?>"><?php _e( "Create Report", "know-my-rankings" ); ?></a>
				<?php endif; ?>
				<br /><button class="button button-primary kmr-submit-button" type="submit" value="submit"><?php _e( 'Update Widget', 'know-my-rankings' ) ?></button>
			</p>
			<?php wp_nonce_field( 'settings', 'update_widget' ); ?>
		</form>

		<br />
		<h3><?php _e( 'Change Account', 'know-my-rankings' ) ?></h3>

		To change your account password or otherwise edit your account, <a target="_blank" href="<?php echo $this->get_kmr_url("dashboard"); ?>"><?php _e( "login at KnowMyRankings.com", "know-my-rankings" ); ?></a>

		<p>To link the plugin to a different KnowMyRankings.com account, enter the account e-mail address and password below.</p>

		<form action="<?php echo $this->get_url( "settings" ); ?>" method="post">
			<p>
				<label for="email"><?php _e( 'Account Email Address', 'know-my-rankings' ) ?></label><br />
				<input id="email" name="email" type="text" class='kmr-text-input' value=""/>
			</p>
			<p>
				<label for="password"><?php _e( 'Account Password', 'know-my-rankings' ) ?></label><br />
				<input id="password" name="password" type="password" class='kmr-text-input' value=""/>
			</p>
			<button class="button button-primary kmr-submit-button" type="submit" value="submit"><?php _e( 'Link Plugin to Account', 'know-my-rankings' ) ?></button>
			<?php wp_nonce_field( 'settings', 'update_user' ); ?>
		</form>

	</div>
</div>