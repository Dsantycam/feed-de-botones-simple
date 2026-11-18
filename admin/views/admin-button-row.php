<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$button = wp_parse_args(
	$current_button,
	array(
		'title'        => '',
		'description'  => '',
		'link'         => '',
		'image_id'     => '',
		'image_url'    => '',
		'icon'         => '',
		'bg_color'     => '',
		'text_color'   => '',
		'border_color' => '',
		'new_tab'      => false,
		'nofollow'     => false,
		'enabled'      => true,
	)
);

$field_name = 'fdbs_buttons[' . $current_index . ']';
?>

<div class="fdbs-button-row">
	<div class="fdbs-button-row__header">
		<div class="fdbs-button-row__header-left">
			<span class="fdbs-sort-handle" title="Arrastrar para ordenar">☰</span>
			<strong class="fdbs-button-row__title">Botón</strong>
		</div>

		<div class="fdbs-button-row__actions">
			<button type="button" class="button-link-delete fdbs-remove-button">
				Eliminar
			</button>
		</div>
	</div>

	<div class="fdbs-form-grid">
		<div class="fdbs-form-field">
			<label>
				<strong>Título del botón</strong>
			</label>
			<input
				type="text"
				class="widefat fdbs-button-title-input"
				name="<?php echo esc_attr( $field_name . '[title]' ); ?>"
				value="<?php echo esc_attr( $button['title'] ); ?>"
				placeholder="Ej. WhatsApp"
			>
		</div>

		<?php if ( ! empty( $enabled_fields['description'] ) ) : ?>
			<div class="fdbs-form-field">
				<label>
					<strong>Descripción</strong>
				</label>
				<textarea
					class="widefat"
					name="<?php echo esc_attr( $field_name . '[description]' ); ?>"
					rows="3"
					placeholder="Texto opcional del botón"
				><?php echo esc_textarea( $button['description'] ); ?></textarea>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $enabled_fields['link'] ) ) : ?>
			<div class="fdbs-form-field">
				<label>
					<strong>Link</strong>
				</label>
				<input
					type="url"
					class="widefat"
					name="<?php echo esc_attr( $field_name . '[link]' ); ?>"
					value="<?php echo esc_attr( $button['link'] ); ?>"
					placeholder="https://"
				>
			</div>

			<div class="fdbs-form-field fdbs-inline-checks">
				<label class="fdbs-checkbox-row">
					<input
						type="checkbox"
						name="<?php echo esc_attr( $field_name . '[new_tab]' ); ?>"
						value="1"
						<?php checked( ! empty( $button['new_tab'] ) ); ?>
					>
					<span>Abrir en nueva pestaña</span>
				</label>

				<label class="fdbs-checkbox-row">
					<input
						type="checkbox"
						name="<?php echo esc_attr( $field_name . '[nofollow]' ); ?>"
						value="1"
						<?php checked( ! empty( $button['nofollow'] ) ); ?>
					>
					<span>Agregar nofollow</span>
				</label>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $enabled_fields['image'] ) ) : ?>
			<div class="fdbs-form-field">
				<label>
					<strong>Imagen</strong>
				</label>

				<input
					type="hidden"
					class="fdbs-image-id"
					name="<?php echo esc_attr( $field_name . '[image_id]' ); ?>"
					value="<?php echo esc_attr( $button['image_id'] ); ?>"
				>

				<input
					type="text"
					class="widefat fdbs-image-url"
					name="<?php echo esc_attr( $field_name . '[image_url]' ); ?>"
					value="<?php echo esc_attr( $button['image_url'] ); ?>"
					placeholder="URL de imagen"
					readonly
				>

				<div class="fdbs-image-actions">
					<button type="button" class="button fdbs-upload-image">
						Seleccionar imagen
					</button>

					<button type="button" class="button fdbs-remove-image">
						Quitar imagen
					</button>
				</div>

				<div class="fdbs-image-preview-wrap <?php echo empty( $button['image_url'] ) ? 'is-empty' : ''; ?>">
					<img
						class="fdbs-image-preview"
						src="<?php echo esc_url( $button['image_url'] ); ?>"
						alt=""
					>
				</div>
			</div>
		<?php endif; ?>

        <?php if ( ! empty( $enabled_fields['icon'] ) ) : ?>
        	<?php $available_icons = FDBS_Helpers::get_available_icons(); ?>
        	<div class="fdbs-form-field">
        		<label>
        			<strong>Icono</strong>
        		</label>
        
        		<input
        			type="hidden"
        			class="fdbs-icon-input"
        			name="<?php echo esc_attr( $field_name . '[icon]' ); ?>"
        			value="<?php echo esc_attr( $button['icon'] ); ?>"
        		>
        
        		<div class="fdbs-icon-selector">
        			<div class="fdbs-icon-selector__top">
        				<div class="fdbs-icon-current">
        					<span class="fdbs-icon-current__preview" data-lucide="<?php echo esc_attr( $button['icon'] ? $button['icon'] : 'star' ); ?>"></span>
        					<span class="fdbs-icon-current__label">
        						<?php echo esc_html( $button['icon'] ? $button['icon'] : 'Sin icono seleccionado' ); ?>
        					</span>
        				</div>
        
        				<div class="fdbs-icon-selector__actions">
        					<button type="button" class="button fdbs-toggle-icon-picker">Elegir icono</button>
        					<button type="button" class="button fdbs-icon-clear">Quitar icono</button>
        				</div>
        			</div>
        
        			<div class="fdbs-icon-picker-panel" hidden>
        				<input
        					type="text"
        					class="widefat fdbs-icon-search"
        					placeholder="Buscar icono..."
        				>
        
        				<div class="fdbs-icon-grid">
        					<?php foreach ( $available_icons as $icon_key => $icon_label ) : ?>
        						<?php $is_active = ( $button['icon'] === $icon_key ); ?>
        						<button
        							type="button"
        							class="fdbs-icon-option <?php echo $is_active ? 'is-active' : ''; ?>"
        							data-icon="<?php echo esc_attr( $icon_key ); ?>"
        							data-label="<?php echo esc_attr( $icon_label ); ?>"
        							title="<?php echo esc_attr( $icon_label ); ?>"
        						>
        							<span class="fdbs-icon-option__icon" data-lucide="<?php echo esc_attr( $icon_key ); ?>"></span>
        							<span class="fdbs-icon-option__text"><?php echo esc_html( $icon_label ); ?></span>
        						</button>
        					<?php endforeach; ?>
        				</div>
        			</div>
        		</div>
        
        		<p class="description">Busca y selecciona un icono visual para este botón.</p>
        	</div>
        <?php endif; ?>

		<?php if ( ! empty( $enabled_fields['bg_color'] ) ) : ?>
        	<div class="fdbs-form-field">
        		<label>
        			<strong>Color de fondo</strong>
        		</label>
        		<input
        			type="text"
        			class="widefat fdbs-color-field"
        			name="<?php echo esc_attr( $field_name . '[bg_color]' ); ?>"
        			value="<?php echo esc_attr( $button['bg_color'] ); ?>"
        			placeholder="#000000"
        		>
        	</div>
        <?php endif; ?>

		<?php if ( ! empty( $enabled_fields['text_color'] ) ) : ?>
        	<div class="fdbs-form-field">
        		<label>
        			<strong>Color de texto</strong>
        		</label>
        		<input
        			type="text"
        			class="widefat fdbs-color-field"
        			name="<?php echo esc_attr( $field_name . '[text_color]' ); ?>"
        			value="<?php echo esc_attr( $button['text_color'] ); ?>"
        			placeholder="#FFFFFF"
        		>
        	</div>
        <?php endif; ?>

		<?php if ( ! empty( $enabled_fields['border_color'] ) ) : ?>
        	<div class="fdbs-form-field">
        		<label>
        			<strong>Color de borde</strong>
        		</label>
        		<input
        			type="text"
        			class="widefat fdbs-color-field"
        			name="<?php echo esc_attr( $field_name . '[border_color]' ); ?>"
        			value="<?php echo esc_attr( $button['border_color'] ); ?>"
        			placeholder="#DDDDDD"
        		>
        	</div>
        <?php endif; ?>

		<div class="fdbs-form-field">
			<label class="fdbs-checkbox-row">
				<input
					type="checkbox"
					name="<?php echo esc_attr( $field_name . '[enabled]' ); ?>"
					value="1"
					<?php checked( ! empty( $button['enabled'] ) ); ?>
				>
				<span>Botón activo</span>
			</label>
		</div>
	</div>
</div>