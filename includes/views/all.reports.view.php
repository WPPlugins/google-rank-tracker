<div class="wrap">
	<h2>
		<?php _e( "Reports", "know-my-rankings");?>
		<a href="<?php echo $this->get_url("add_new"); ?>" class="add-new-h2"><?php _e( "Add new", "know-my-rankings");?></a><br/>		
	</h2>

	<?php 
		if ( $response['status'] == "ERR" ) {
			
			$this->notices->show_notice( $response['body'], 'error' );
			return;
		}
		if ( ! $urls ) {
			
			$this->notices->show_notice( __( "You're not tracking any URLs!" , "know-my-rankings") );
			return;
		}

		do_action( "kmr_all_reports_notice" );
	?>

	<div class="tablenav top">
		<?php $this->pagination( $total_urls, $per_page, $paged	); ?>
	</div>
	
	<table class="all-reports widefat" cellspacing="0">
		<thead>
			<tr>
				<th scope="col"><?php _e( "URL", "know-my-rankings" );?></th>
				<th scope="col"><?php _e( "Top Keyword", "know-my-rankings" );?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col"><?php _e( "URL", "know-my-rankings" );?></th>
				<th scope="col"><?php _e( "Top Keyword", "know-my-rankings" );?></th>
			</tr>
		</tfoot>

		<tbody id="the-list">
		<?php 
		foreach ( $urls as $url ) : 

			// Sort keywords by rank
			$keywords 	= $url['keywords'];
			$rank 		= array();
			
			foreach ( $keywords as $key => $keyword ){
				$rank[$key] = $keyword['current_rank'];
			}
			array_multisort( $rank, SORT_ASC, $keywords );

			// Move 0 values to the end
			foreach ( $keywords as $key => $keyword ) {
				if ( $keyword['current_rank'] == 0 ) {
					$keywords[] = $keywords[$key];
					unset($keywords[$key]);
				};
			}
			reset( $keywords );
			$top_keyword = current( $keywords ); ?>
			<tr>
				<td>
					<strong>
						<a href="<?php echo $this->get_url( "view_report", array ( "report" => $url['id'] ) ); ?>" 
							title="<?php _e( "View this report", "know-my-rankings" ); ?>">
							<?php echo $url['url']; ?>
						</a>
					</strong>
					<div class="row-actions">
						<a class="kmr-delete" 
							href="<?php echo $this->get_url( "all_reports", array ( "delete_url" => $url['id'] ) ); ?>"
							data-report="<?php echo $url['url']; ?>">
							<?php _e( "delete", "know-my-rankings" ); ?>
						</a>
					</div>
				</td>
				<td>
					<span class="indicator <?php echo $this->get_rank_classes( $top_keyword['current_rank'] ); ?>"></span>
					<?php echo $top_keyword['current_rank'] === 0 ? "100+" : $top_keyword['current_rank']; ?> - <?php echo $top_keyword['kw']; ?>
					<?php $this->position( $top_keyword ); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<div class="tablenav bottom">
		<?php $this->pagination( $total_urls, $per_page, $paged	); ?>
	</div>
	<div>
		<a target="_blank" 
			href="<?php echo $this->get_kmr_url("dashboard"); ?>">
			<?php _e( "Manage reports on KnowMyRankings.com", "know-my-rankings" ); ?>
		</a>
	</div>
</div>