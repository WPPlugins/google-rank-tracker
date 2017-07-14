<td>
	<strong><?php echo $keyword['kw']; ?></strong> 
	<a class="kmr-delete" 
		href="<?php echo $this->get_url( "view_report", array ( "delete_keyword" => $keyword['id'], "report" => $url_id ) ); ?>"
		data-kw="<?php echo $keyword['kw']; ?>">
		<?php _e( "delete", "know-my-rankings" ); ?>
	</a>
	<?php $this->position( $keyword ); ?>
</td>
<?php if ( $keyword['current_rank'] === null ) : ?>
<td colspan="5" class="load-spinner"></td>
<?php else : ?>	
<td>
	<span class="indicator <?php echo $this->get_rank_classes( $keyword['current_rank'] ); ?>"></span>
	<?php echo $this->get_rank_value( $keyword['current_rank'] ); ?>&nbsp;&nbsp;&nbsp;&nbsp;
</td>
<td>
	<span class="indicator small <?php echo $this->get_rank_classes( $keyword['last_7day'] ); ?>"></span>
	<?php echo $this->get_rank_value( $keyword['last_7day'] ); ?>
</td>
<td>
	<span class="indicator small <?php echo $this->get_rank_classes( $keyword['rolling_1month_ranking'] ); ?>"></span>
	<?php echo $this->get_rank_value( $keyword['rolling_1month_ranking'] ); ?>
</td>
<td>
	<span class="indicator small <?php echo $this->get_rank_classes( $keyword['rolling_6month_ranking'] ); ?>"></span>
	<?php echo $this->get_rank_value( $keyword['rolling_6month_ranking'] ); ?>
</td>
<td>
	<a title="<?php _e("View Keyword History on KnowMyRatings.com"); ?>" target="_blank" href="<?php echo $this->get_kmr_url( 'keywords', $keyword['id'] ); ?>" class="">
		<span class="dashicons dashicons-chart-bar"></span>
	</a>
</td>
<?php endif; ?>