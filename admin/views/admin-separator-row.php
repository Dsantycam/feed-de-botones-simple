<?php
/**
 * Fila de separador en el editor del feed.
 *
 * Espera $current_button (array) y $current_index (int|'__INDEX__').
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$separator    = FDBS_Helpers::sanitize_separator( $current_button );
$sep_styles   = FDBS_Helpers::get_separator_styles();
$field_name   = 'fdbs_buttons[' . $current_index . ']';
$is_collapsed = ! empty( $separator['collapsed'] );
?>

<div class="fdbs-item-row fdbs-separator-row<?php echo $is_collapsed ? ' is-collapsed' : ''; ?>" data-type="separator">
	<input type="hidden" name="<?php echo esc_attr( $field_name . '[type]' ); ?>" value="separator">
	<input type="hidden" class="fdbs-collapsed-input" name="<?php echo esc_attr( $field_name . '[collapsed]' ); ?>" value="<?php echo $is_collapsed ? '1' : ''; ?>">

	<div class="fdbs-item-row__header">
		<div class="fdbs-item-row__header-left">
			<span class="fdbs-sort-handle" title="Arrastrar para ordenar">☰</span>
			<button type="button" class="fdbs-item-row__toggle fdbs-toggle-row" aria-expanded="<?php echo $is_collapsed ? 'false' : 'true'; ?>" title="Minimizar / expandir">
				<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
			</button>
			<span class="fdbs-item-row__thumb fdbs-item-row__thumb--separator" aria-hidden="true">
				<span class="dashicons dashicons-editor-insertmore"></span>
			</span>
			<span class="fdbs-item-row__heading">
				<strong class="fdbs-item-row__title">Separador</strong>
				<span class="fdbs-item-row__meta"></span>
			</span>
			<span class="fdbs-item-row__badge" hidden>Inactivo</span>
		</div>

		<div class="fdbs-item-row__actions">
			<button type="button" class="button button-small fdbs-duplicate-row" title="Duplicar debajo">
				<span class="dashicons dashicons-admin-page" aria-hidden="true"></span>
				Duplicar
			</button>
			<button type="button" class="button-link-delete fdbs-remove-button">
				Eliminar
			</button>
		</div>
	</div>

	<div class="fdbs-item-row__body">
		<div class="fdbs-form-field fdbs-form-field--full">
			<label><strong>Diseño</strong></label>

			<input
				type="hidden"
				class="fdbs-sep-style-input"
				name="<?php echo esc_attr( $field_name . '[style]' ); ?>"
				value="<?php echo esc_attr( $separator['style'] ); ?>"
			>

			<div class="fdbs-sep-styles">
				<?php foreach ( $sep_styles as $style_key => $style_label ) : ?>
					<button
						type="button"
						class="fdbs-sep-style-option<?php echo $separator['style'] === $style_key ? ' is-active' : ''; ?>"
						data-style="<?php echo esc_attr( $style_key ); ?>"
						data-label="<?php echo esc_attr( $style_label ); ?>"
					>
						<span class="fdbs-sep-style-option__sample">
							<?php
							$current_separator = array(
								'style'          => $style_key,
								'label'          => in_array( $style_key, array( 'pill', 'space' ), true ) ? 'Sección' : '',
								'thickness'      => 'double' === $style_key ? 1 : 2,
								'spacing_top'    => 0,
								'spacing_bottom' => 0,
							);
							include FDBS_PLUGIN_PATH . 'public/views/feed-separator.php';
							?>
						</span>
						<span class="fdbs-sep-style-option__name"><?php echo esc_html( $style_label ); ?></span>
					</button>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="fdbs-form-grid">
			<div class="fdbs-form-field fdbs-sep-label-field">
				<label><strong>Texto (opcional)</strong></label>
				<input
					type="text"
					class="widefat fdbs-sep-label-input"
					name="<?php echo esc_attr( $field_name . '[label]' ); ?>"
					value="<?php echo esc_attr( $separator['label'] ); ?>"
					placeholder="Ej. Redes sociales"
				>
				<p class="description">Aparece en el centro del separador como título de sección.</p>
			</div>

			<div class="fdbs-form-field">
				<label><strong>Alineación</strong></label>
				<select class="widefat" name="<?php echo esc_attr( $field_name . '[align]' ); ?>">
					<option value="center" <?php selected( $separator['align'], 'center' ); ?>>Centrado</option>
					<option value="left" <?php selected( $separator['align'], 'left' ); ?>>Izquierda</option>
				</select>
			</div>

			<div class="fdbs-form-field">
				<label><strong>Color</strong></label>
				<input
					type="text"
					class="widefat fdbs-color-field"
					name="<?php echo esc_attr( $field_name . '[color]' ); ?>"
					value="<?php echo esc_attr( $separator['color'] ); ?>"
					placeholder="Hereda del tema"
				>
			</div>

			<div class="fdbs-form-field">
				<label><strong>Ancho</strong></label>
				<select class="widefat" name="<?php echo esc_attr( $field_name . '[width]' ); ?>">
					<?php foreach ( array( 100, 75, 50, 25 ) as $width_option ) : ?>
						<option value="<?php echo esc_attr( $width_option ); ?>" <?php selected( $separator['width'], $width_option ); ?>><?php echo esc_html( $width_option ); ?>%</option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="fdbs-form-field">
				<label><strong>Grosor</strong> <span class="fdbs-range-value"><?php echo esc_html( $separator['thickness'] ); ?>px</span></label>
				<input
					type="range"
					class="fdbs-range"
					min="1"
					max="8"
					step="1"
					name="<?php echo esc_attr( $field_name . '[thickness]' ); ?>"
					value="<?php echo esc_attr( $separator['thickness'] ); ?>"
				>
			</div>

			<div class="fdbs-form-field"></div>

			<div class="fdbs-form-field">
				<label><strong>Espacio con el botón de arriba</strong> <span class="fdbs-range-value"><?php echo esc_html( $separator['spacing_top'] ); ?>px</span></label>
				<input
					type="range"
					class="fdbs-range"
					min="0"
					max="160"
					step="2"
					name="<?php echo esc_attr( $field_name . '[spacing_top]' ); ?>"
					value="<?php echo esc_attr( $separator['spacing_top'] ); ?>"
				>
			</div>

			<div class="fdbs-form-field">
				<label><strong>Espacio con el botón de abajo</strong> <span class="fdbs-range-value"><?php echo esc_html( $separator['spacing_bottom'] ); ?>px</span></label>
				<input
					type="range"
					class="fdbs-range"
					min="0"
					max="160"
					step="2"
					name="<?php echo esc_attr( $field_name . '[spacing_bottom]' ); ?>"
					value="<?php echo esc_attr( $separator['spacing_bottom'] ); ?>"
				>
			</div>

			<div class="fdbs-form-field">
				<label class="fdbs-checkbox-row">
					<input
						type="checkbox"
						name="<?php echo esc_attr( $field_name . '[enabled]' ); ?>"
						value="1"
						<?php checked( ! empty( $separator['enabled'] ) ); ?>
					>
					<span>Separador activo</span>
				</label>
			</div>
		</div>
	</div>
</div>
