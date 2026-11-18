<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fields = array(
	'image'        => 'Usar imagen',
	'description'  => 'Descripción',
	'link'         => 'Link',
	'bg_color'     => 'Color de fondo',
	'text_color'   => 'Color de texto',
	'border_color' => 'Color de borde',
	'icon'         => 'Usar icono',
);
?>

<div class="fdbs-metabox">
	<p>Selecciona qué opciones estarán disponibles para los botones de este feed.</p>

	<div class="fdbs-field-group">
		<?php foreach ( $fields as $key => $label ) : ?>
			<label class="fdbs-checkbox-row">
				<input
					type="checkbox"
					name="fdbs_enabled_fields[]"
					value="<?php echo esc_attr( $key ); ?>"
					<?php checked( ! empty( $enabled_fields[ $key ] ) ); ?>
				>
				<span><?php echo esc_html( $label ); ?></span>
			</label>
		<?php endforeach; ?>
	</div>

	<hr>

	<h4 style="margin:0;">Estilo global del feed</h4>

	<div class="fdbs-form-grid">
		<div class="fdbs-form-field">
			<label><strong>Columnas por defecto</strong></label>
			<select name="fdbs_layout[columns]" class="widefat">
				<option value="1" <?php selected( $layout['columns'], 1 ); ?>>1</option>
				<option value="2" <?php selected( $layout['columns'], 2 ); ?>>2</option>
				<option value="3" <?php selected( $layout['columns'], 3 ); ?>>3</option>
			</select>
		</div>

		<div class="fdbs-form-field">
			<label><strong>Ancho máximo del feed (px)</strong></label>
			<input type="number" min="280" max="1600" step="1" class="widefat" name="fdbs_layout[max_width]" value="<?php echo esc_attr( $layout['max_width'] ); ?>">
		</div>

		<div class="fdbs-form-field">
			<label><strong>Separación entre botones (px)</strong></label>
			<input type="number" min="0" max="80" step="1" class="widefat" name="fdbs_layout[button_gap]" value="<?php echo esc_attr( $layout['button_gap'] ); ?>">
		</div>

		<div class="fdbs-form-field">
			<label><strong>Radio de borde por defecto (px)</strong></label>
			<input type="number" min="0" max="60" step="1" class="widefat" name="fdbs_layout[button_radius]" value="<?php echo esc_attr( $layout['button_radius'] ); ?>">
		</div>

		<div class="fdbs-form-field">
			<label class="fdbs-checkbox-row">
				<input
					type="checkbox"
					name="fdbs_layout[show_title]"
					value="1"
					<?php checked( ! empty( $layout['show_title'] ) ); ?>
				>
				<span>Mostrar título por defecto</span>
			</label>
		</div>
	</div>
</div>