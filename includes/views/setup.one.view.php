<div class="wrap">
	<h2><?php _e( "KnowMyRankings: Installation - Step 1", 'know-my-rankings');?></h2>
	<div class="setup-body">
		<div class="setup-box-left">
			<form action="<?php echo $this->get_url("settings"); ?>" method="post">
				<p>
					<label for="url"><?php _e( 'Track Google Rankings for this URL', 'know-my-rankings' ) ?></label><br />
					<input class="widefat" id="url" name="url" type="text" value="<?php echo get_bloginfo("url"); ?>"/>
				</p>
				<p>
					<label for="keywords"><?php _e( 'Up to 10 Keyword Phrases <small>(1 per line)</small>', 'know-my-rankings' ) ?></label><br />
					<textarea class="widefat" name="keywords" id="keywords" cols="30" rows="10"></textarea>
					<button class="button button-primary kmr-install-button" type="submit" value="submit"><?php _e( 'Continue to Step 2', 'know-my-rankings' ) ?></button>
				</p>
				<?php wp_nonce_field( 'setup', 'step_one_nonce' ); ?>
			</form>
		</div>
		<div class="setup-box-right">
			<h3><?php _e( 'About KnowMyRankings', 'know-my-rankings' ) ?></h3>
			<div class="inside">
				<ul>
					<li><?php _e( "This plugin displays ranking data from the KnowMyRankings.com service inside your WordPress admin.", 'know-my-rankings' ) ?></li>
					<li><?php _e( "Installing this plugin will automatically create an account for you on KnowMyRankings.com.", 'know-my-rankings' ) ?></li>
					<li><?php _e( "This account will enable you to track the Google rankings of up to 10 keywords, for free.", 'know-my-rankings' ) ?></li>
					<li><?php _e( "All tracking is done by KnowMyRankings.com servers, not your own server. You can login to your account on KnowMyRankings.com any time to change, update, upgrade, or cancel your account.", 'know-my-rankings' ) ?></li>
				</ul>
			</div>
		</div>
	</div>
</div>


