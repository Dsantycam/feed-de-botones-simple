<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="fdbs-metabox">
	<div class="fdbs-items-toolbar">
		<p class="fdbs-items-toolbar__intro">Agrega los botones y separadores que se mostrarán en este feed.</p>

		<div class="fdbs-items-toolbar__actions">
			<span class="fdbs-items-count" aria-live="polite"></span>
			<button type="button" class="button button-small" id="fdbs-collapse-all">
				<span class="dashicons dashicons-arrow-up-alt2" aria-hidden="true"></span>
				Minimizar todos
			</button>
			<button type="button" class="button button-small" id="fdbs-expand-all">
				<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
				Expandir todos
			</button>
		</div>
	</div>

	<div id="fdbs-buttons-container" class="fdbs-buttons-container">
		<?php if ( ! empty( $buttons ) ) : ?>
			<?php foreach ( $buttons as $index => $button ) : ?>
				<?php
				$current_button = is_array( $button ) ? $button : array();
				$current_index  = (int) $index;

				if ( FDBS_Helpers::is_separator( $current_button ) ) {
					include FDBS_PLUGIN_PATH . 'admin/views/admin-separator-row.php';
				} else {
					include FDBS_PLUGIN_PATH . 'admin/views/admin-button-row.php';
				}
				?>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<div class="fdbs-add-actions">
		<button type="button" class="button button-primary" id="fdbs-add-button">
			Agregar botón
		</button>
		<button type="button" class="button" id="fdbs-add-separator">
			<span class="dashicons dashicons-editor-insertmore" aria-hidden="true"></span>
			Agregar separador
		</button>
	</div>

	<script type="text/template" id="fdbs-button-template">
		<?php
		$current_button = array();
		$current_index  = '__INDEX__';
		include FDBS_PLUGIN_PATH . 'admin/views/admin-button-row.php';
		?>
	</script>

	<script type="text/template" id="fdbs-separator-template">
		<?php
		$current_button = array();
		$current_index  = '__INDEX__';
		include FDBS_PLUGIN_PATH . 'admin/views/admin-separator-row.php';
		?>
	</script>
</div>
