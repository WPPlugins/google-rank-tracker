<?php 

$keywords = $response['body']['keywords'];

if ( $_GET['order'] == "asc" ) {
	$order = "desc";
	$sorted = "asc";
}
else {
	$order = "asc";
	$sorted = "desc";
}

if ( ! is_array( $keywords ) ) {
	_e( "No keywords", "know-my-rankings" );
	return;
}?>
<table class="report-list widefat" cellspacing="0">
	<thead>
		<tr>
			<th scope="col" class="manage-column column-title <?php echo ( $_GET['orderby'] == "kw" ) ? "sorted " . $sorted : "sortable $sorted"; ?>">
				<a href="<?php echo $this->get_url( "all_reports", array ( 
						"report"	=> $response['body']['id'],
						"orderby" 	=> "kw", 
						"order" 	=> $order ) ); ?>">
					<span><?php _e( "Keyword", "know-my-rankings");?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col" class="manage-column column-rank <?php echo ( $_GET['orderby'] == "current_rank" ) ? "sorted " . $sorted : "sortable $sorted"; ?>">
				<a href="<?php echo $this->get_url( "all_reports", array ( 
						"report"	=> $response['body']['id'],
						"orderby" 	=> "current_rank", 
						"order" 	=> $order ) ); ?>">
					<span><?php _e( "Current", "know-my-rankings");?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col"><?php _e( "Last Week", "know-my-rankings");?></th>
			<th scope="col"><?php _e( "Last Month", "know-my-rankings");?></th>
			<th scope="col"><?php _e( "-6 Months", "know-my-rankings");?></th>
			<th scope="col"><?php _e( "History", "know-my-rankings");?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th scope="col" class="manage-column column-title <?php echo ( $_GET['orderby'] == "kw" ) ? "sorted " . $sorted : "sortable $sorted"; ?>">
				<a href="<?php echo $this->get_url( "all_reports", array ( 
						"report"	=> $response['body']['id'],
						"orderby" 	=> "kw", 
						"order" 	=> $order ) ); ?>">
					<span><?php _e( "Keyword", "know-my-rankings");?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col" class="manage-column column-rank <?php echo ( $_GET['orderby'] == "current_rank" ) ? "sorted " . $sorted : "sortable $sorted"; ?>">
				<a href="<?php echo $this->get_url( "all_reports", array ( 
						"report"	=> $response['body']['id'],
						"orderby" 	=> "current_rank", 
						"order" 	=> $order ) ); ?>">
					<span><?php _e( "Current", "know-my-rankings");?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col"><?php _e( "Last Week", "know-my-rankings");?></th>
			<th scope="col"><?php _e( "Last Month", "know-my-rankings");?></th>
			<th scope="col"><?php _e( "-6 Months", "know-my-rankings");?></th>
			<th scope="col"><?php _e( "History", "know-my-rankings");?></th>
		</tr>
	</tfoot>

	<tbody>
		<?php foreach ( $keywords as $keyword ) : ?>
			<tr id="keyword_<?php echo $keyword['id']; ?>" 
			<?php echo $keyword['current_rank'] === null ? "class=\"update-keyword\"" : "" ;?>
			data-action="<?php echo $action; ?>" 
			data-id="<?php echo $keyword['id']; ?>"
			data-urlid="<?php echo $response['body']['id']; ?>">
			<?php $this->show_keyword( $keyword, $response['body']['id'] ); ?>
			</tr>
		<?php endforeach; ?>		
	</tbody>
</table>