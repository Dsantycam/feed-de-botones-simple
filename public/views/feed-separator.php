<?php
/**
 * Separador entre secciones del feed.
 *
 * Espera $current_separator (array). Se usa en el frontend y en la vista previa del admin.
 * Si cambias este marcado, actualiza también buildSeparatorElement() en assets/js/admin.js.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fdbs_sep = FDBS_Helpers::sanitize_separator( isset( $current_separator ) ? $current_separator : array() );

$fdbs_sep_style = $fdbs_sep['style'];
$fdbs_sep_label = 'dots' === $fdbs_sep_style ? '' : $fdbs_sep['label'];

$fdbs_sep_classes = array(
	'fdbs-separator',
	'fdbs-separator--' . $fdbs_sep_style,
	'fdbs-separator--align-' . $fdbs_sep['align'],
);

if ( '' !== $fdbs_sep['color'] ) {
	$fdbs_sep_classes[] = 'has-color';
}

if ( '' !== $fdbs_sep_label ) {
	$fdbs_sep_classes[] = 'has-label';
}

$fdbs_sep_vars = sprintf(
	'--fdbs-sep-thickness:%dpx;--fdbs-sep-width:%d%%;--fdbs-sep-top:%dpx;--fdbs-sep-bottom:%dpx;',
	$fdbs_sep['thickness'],
	$fdbs_sep['width'],
	$fdbs_sep['spacing_top'],
	$fdbs_sep['spacing_bottom']
);

if ( '' !== $fdbs_sep['color'] ) {
	$fdbs_sep_vars .= '--fdbs-sep-color:' . $fdbs_sep['color'] . ';';
}
?>
<div
	class="<?php echo esc_attr( implode( ' ', $fdbs_sep_classes ) ); ?>"
	style="<?php echo esc_attr( $fdbs_sep_vars ); ?>"
	<?php echo '' === $fdbs_sep_label ? 'role="separator"' : ''; ?>
>
	<span class="fdbs-separator__line" aria-hidden="true"></span>
	<?php if ( '' !== $fdbs_sep_label ) : ?>
		<span class="fdbs-separator__label"><?php echo esc_html( $fdbs_sep_label ); ?></span>
	<?php elseif ( 'diamond' === $fdbs_sep_style || 'dots' === $fdbs_sep_style ) : ?>
		<span class="fdbs-separator__mark" aria-hidden="true"></span>
	<?php endif; ?>
	<span class="fdbs-separator__line" aria-hidden="true"></span>
</div>
