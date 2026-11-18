<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="fdbs-metabox">
	<p>Agrega los botones que se mostrarán en este feed.</p>

	<div id="fdbs-buttons-container" class="fdbs-buttons-container">
		<?php if ( ! empty( $buttons ) ) : ?>
			<?php foreach ( $buttons as $index => $button ) : ?>
				<?php
				$current_button = is_array( $button ) ? $button : array();
				$current_index  = (int) $index;
				include FDBS_PLUGIN_PATH . 'admin/views/admin-button-row.php';
				?>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<button type="button" class="button button-primary" id="fdbs-add-button">
		Agregar botón
	</button>

	<script type="text/template" id="fdbs-button-template">
		<?php
		$current_button = array();
		$current_index  = '__INDEX__';
		include FDBS_PLUGIN_PATH . 'admin/views/admin-button-row.php';
		?>
	</script>
</div>