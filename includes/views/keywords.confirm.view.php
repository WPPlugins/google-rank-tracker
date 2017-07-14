About to add <?php echo $keywords_to_add; ?> keywords:
<?php if ( is_array( $keywords) ) : ?>
<ul class="new-keywords">
	<?php foreach ( $keywords as $keyword ) : ?>
	<li><?php echo $keyword; ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if ($price_increase != 0) : ?>
	<div class="price-increase-wrap">
		Adding <strong><?php echo $keywords_to_add; ?></strong> keywords will increase your <em>next</em> monthly bill by
		<span class="price-increase">$<?php echo number_format((float)$price_increase, 2, '.', ''); ?></span>
	</div>
	<div class="before-after-plan">
		<span class="plan-before"><?php echo $current_total_keywords; ?> keyword plan</span> &rarr; 
		<span class="plan-after"><?php echo $new_total_keywords; ?> keyword plan</span>
		<span class="plan-before">$<?php echo number_format((float)$current_price, 2, '.', ''); ?></span> &rarr; 
		<span class="plan-after">$<?php echo number_format((float)$new_price, 2, '.', ''); ?></span>
	</div>
<?php endif; ?>