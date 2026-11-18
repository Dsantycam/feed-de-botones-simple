<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title        = isset( $current_button['title'] ) ? $current_button['title'] : '';
$description  = isset( $current_button['description'] ) ? $current_button['description'] : '';
$link         = isset( $current_button['link'] ) ? $current_button['link'] : '';
$image_id     = isset( $current_button['image_id'] ) ? absint( $current_button['image_id'] ) : 0;
$image_url    = isset( $current_button['image_url'] ) ? $current_button['image_url'] : '';
$icon         = isset( $current_button['icon'] ) ? $current_button['icon'] : '';
$bg_color     = isset( $current_button['bg_color'] ) ? $current_button['bg_color'] : '';
$text_color   = isset( $current_button['text_color'] ) ? $current_button['text_color'] : '';
$border_color = isset( $current_button['border_color'] ) ? $current_button['border_color'] : '';
$new_tab      = ! empty( $current_button['new_tab'] );
$nofollow     = ! empty( $current_button['nofollow'] );

if ( $image_id > 0 ) {
	$resolved_image = wp_get_attachment_image_url( $image_id, 'thumbnail' );

	if ( $resolved_image ) {
		$image_url = $resolved_image;
	}
}

$styles = array();

if ( ! empty( $bg_color ) ) {
	$styles[] = 'background-color:' . esc_attr( $bg_color );
}

if ( ! empty( $text_color ) ) {
	$styles[] = 'color:' . esc_attr( $text_color );
}

if ( ! empty( $border_color ) ) {
	$styles[] = 'border-color:' . esc_attr( $border_color );
}

$style_attr = implode( ';', $styles );

$rel = array();

if ( $nofollow ) {
	$rel[] = 'nofollow';
}

if ( $new_tab ) {
	$rel[] = 'noopener';
	$rel[] = 'noreferrer';
}
?>

<?php if ( ! empty( $link ) ) : ?>
	<a
		class="fdbs-button"
		href="<?php echo esc_url( $link ); ?>"
		<?php echo $new_tab ? ' target="_blank"' : ''; ?>
		<?php echo ! empty( $rel ) ? ' rel="' . esc_attr( implode( ' ', array_unique( $rel ) ) ) . '"' : ''; ?>
		<?php echo ! empty( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>
	>
		<?php if ( ! empty( $image_url ) ) : ?>
			<span class="fdbs-button__media">
				<img src="<?php echo esc_url( $image_url ); ?>" alt="" class="fdbs-button__image">
			</span>
		<?php elseif ( ! empty( $icon ) ) : ?>
			<span class="fdbs-button__media fdbs-button__media--icon">
				<span data-lucide="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></span>
			</span>
		<?php endif; ?>

		<span class="fdbs-button__content">
			<span class="fdbs-button__title"><?php echo esc_html( $title ); ?></span>

			<?php if ( ! empty( $description ) ) : ?>
				<span class="fdbs-button__description"><?php echo esc_html( $description ); ?></span>
			<?php endif; ?>
		</span>
	</a>
<?php else : ?>
	<div
		class="fdbs-button fdbs-button--nolink"
		<?php echo ! empty( $style_attr ) ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>
	>
		<?php if ( ! empty( $image_url ) ) : ?>
			<span class="fdbs-button__media">
				<img src="<?php echo esc_url( $image_url ); ?>" alt="" class="fdbs-button__image">
			</span>
		<?php elseif ( ! empty( $icon ) ) : ?>
			<span class="fdbs-button__media fdbs-button__media--icon">
				<span data-lucide="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></span>
			</span>
		<?php endif; ?>

		<span class="fdbs-button__content">
			<span class="fdbs-button__title"><?php echo esc_html( $title ); ?></span>

			<?php if ( ! empty( $description ) ) : ?>
				<span class="fdbs-button__description"><?php echo esc_html( $description ); ?></span>
			<?php endif; ?>
		</span>
	</div>
<?php endif; ?>
