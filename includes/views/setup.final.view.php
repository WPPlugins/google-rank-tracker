<div class="wrap">
	<div>
		<h2><?php _e( 'Success', 'know-my-rankings' ) ?></h2>
		<?php 
		
		do_action("kmr_settings_notice");

		if ( $response['status'] == "ERR" ) 
			$this->notices->show_notice( $response['body'], 'error' );

		?>
	<?php if ( $response['status'] == "OK" ) : ?>
		<a target="_blank" href="<?php echo $this->get_kmr_url("dashboard"); ?>"><?php _e( "View your account at KnowMyRankings.com", "know-my-rankings" ); ?></a><br/>
		<a href="<?php echo $this->get_url("view_report", array ( "report" => $default_report_id ) ); ?>"><?php _e( "View your rank report", "know-my-rankings" ); ?></a>
	<?php endif; ?>
	</div>
</div>