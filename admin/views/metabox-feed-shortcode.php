<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shortcode = FDBS_Helpers::get_shortcode( $post->ID );
?>

<div class="fdbs-metabox">
	<p>Usa este shortcode para mostrar el feed en cualquier página o entrada.</p>

	<input
		type="text"
		class="widefat"
		readonly
		value="<?php echo esc_attr( $shortcode ); ?>"
		onclick="this.select();"
	>

	<p class="description">Ejemplo: pégalo en una página, entrada o widget compatible con shortcodes.</p>
</div>