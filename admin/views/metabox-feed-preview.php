<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$preview_style = sprintf(
	'--fdbs-preview-gap:%dpx;--fdbs-preview-radius:%dpx;',
	(int) $layout['button_gap'],
	(int) $layout['button_radius']
);
?>

<div class="fdbs-admin-preview" style="<?php echo esc_attr( $preview_style ); ?>">
	<h4 class="fdbs-admin-preview__title" <?php echo empty( $feed_title ) || empty( $layout['show_title'] ) ? 'style="display:none;"' : ''; ?>>
		<?php echo esc_html( $feed_title ); ?>
	</h4>

	<p class="description" <?php echo ! empty( $preview_buttons ) ? 'style="display:none;"' : ''; ?>>
		Aún no hay botones activos para mostrar.
	</p>

	<div class="fdbs-admin-preview__list">
		<?php foreach ( $preview_buttons as $button ) : ?>
			<?php
			$title        = isset( $button['title'] ) ? $button['title'] : '';
			$description  = isset( $button['description'] ) ? $button['description'] : '';
			$image_id     = isset( $button['image_id'] ) ? absint( $button['image_id'] ) : 0;
			$image_url    = isset( $button['image_url'] ) ? $button['image_url'] : '';
			$icon         = isset( $button['icon'] ) ? $button['icon'] : '';
			$bg_color     = isset( $button['bg_color'] ) ? $button['bg_color'] : '';
			$text_color   = isset( $button['text_color'] ) ? $button['text_color'] : '';
			$border_color = isset( $button['border_color'] ) ? $button['border_color'] : '';

			if ( $image_id > 0 ) {
				$resolved_image = wp_get_attachment_image_url( $image_id, 'thumbnail' );
				if ( $resolved_image ) {
					$image_url = $resolved_image;
				}
			}

			$styles = array();

			if ( ! empty( $bg_color ) ) {
				$styles[] = 'background-color:' . $bg_color;
			}

			if ( ! empty( $text_color ) ) {
				$styles[] = 'color:' . $text_color;
			}

			if ( ! empty( $border_color ) ) {
				$styles[] = 'border-color:' . $border_color;
			}
			?>
			<div
				class="fdbs-admin-preview__item"
				<?php echo ! empty( $styles ) ? 'style="' . esc_attr( implode( ';', $styles ) ) . '"' : ''; ?>
			>
				<?php if ( ! empty( $image_url ) ) : ?>
					<span class="fdbs-admin-preview__media">
						<img src="<?php echo esc_url( $image_url ); ?>" alt="">
					</span>
				<?php elseif ( ! empty( $icon ) ) : ?>
					<span class="fdbs-admin-preview__media fdbs-admin-preview__media--icon">
						<span data-lucide="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></span>
					</span>
				<?php endif; ?>

				<span class="fdbs-admin-preview__content">
					<span class="fdbs-admin-preview__item-title"><?php echo esc_html( $title ); ?></span>

					<?php if ( ! empty( $description ) ) : ?>
						<span class="fdbs-admin-preview__item-description"><?php echo esc_html( $description ); ?></span>
					<?php endif; ?>
				</span>
			</div>
		<?php endforeach; ?>
	</div>
</div>
