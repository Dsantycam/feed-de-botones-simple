<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="fdbs-feed" style="<?php echo esc_attr( $wrapper_style ); ?>">
	<?php if ( '' !== $feed_title ) : ?>
		<h3 class="fdbs-feed__title"><?php echo esc_html( $feed_title ); ?></h3>
	<?php endif; ?>

	<div class="fdbs-feed__buttons fdbs-feed__buttons--columns-<?php echo esc_attr( $columns ); ?>">
		<?php foreach ( $buttons as $button ) : ?>
			<?php
			if ( FDBS_Helpers::is_separator( $button ) ) {
				$current_separator = $button;
				include FDBS_PLUGIN_PATH . 'public/views/feed-separator.php';
			} else {
				$current_button = $button;
				include FDBS_PLUGIN_PATH . 'public/views/feed-button.php';
			}
			?>
		<?php endforeach; ?>
	</div>
</div>
