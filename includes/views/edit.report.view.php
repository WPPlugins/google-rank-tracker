<div class="wrap">
	<h2><?php _e( "View Report", "know-my-rankings");?> <a href="<?php echo $this->get_url("all_reports"); ?>" class="add-new-h2"><?php _e( "Back to all reports", "know-my-rankings" );?></a></h2>

	<?php 
		
		if ( ! empty ( $validation ) ) {

			$this->notices->show_notice ( $validation, 'error' );
		}

		if ( $response['status'] == "ERR" ) {
			
			$this->notices->show_notice( $response['body'], 'error' );

			return;
		}

		do_action("kmr_edit_report_notice");

	?>
	
	<div id="titlediv">
		<p><input disabled type="text" size="30" value="<?php echo $response['body']['url']; ?>" id="title" autocomplete="off"></p>
	</div>
	
	<div id="kmr_report">
	<?php $this->show_report( $response ); ?>
	</div>
	
	<p>
		<button class="button button-primary" type="button" data-toggle="modal" data-target="#add_url_modal"><?php _e( 'Add keywords', 'know-my-rankings' ) ?></button>
	</p>
	
	<div id="add_url_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form id="add_keywords_form" action="<?php echo $this->get_url( "view_report", array ( "report" => $response['body']['id'] ) ); ?>" method="post">
					<div class="modal-header">
						<h3 class="modal-title"><?php _e( "Add Keywords", "know-my-rankings"); ?></h3>
					</div>
					<div class="modal-body">
						<p>
							<label for="keywords"><?php _e( 'Enter keywords, 1 per line', 'know-my-rankings' ) ?></label><br />
							<textarea name="keywords" id="kmr_keywords" cols="50" rows="10"></textarea>
						</p>
						<input id="kmr_url" type="hidden" name="url" value="<?php echo $response['body']['url']; ?>">
						<input id="kmr_url_id" type="hidden" name="url_id" value="<?php echo $response['body']['id']; ?>">
						<?php wp_nonce_field( 'reports', 'add_new_report' ); ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="button" data-dismiss="modal">Close</button>
						<button type="submit" class="button button-primary" value="submit"><?php _e( 'Add to report', 'know-my-rankings' ); ?></button>
					</div>
				</form>
	    	</div>
		</div>
	</div>
	
	<a target="_blank" href="<?php echo $this->get_kmr_url( "view_url", $response['body']['id'] ); ?>">
		<?php _e( "View / Edit on KnowMyRankings.com", "know-my-rankings" ); ?>
	</a>
</div>