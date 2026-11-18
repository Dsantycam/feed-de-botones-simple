<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FDBS_Helpers {

	public static function get_shortcode( $post_id ) {
		return '[fdbs_feed id="' . absint( $post_id ) . '"]';
	}

	public static function get_shortcode_by_slug( $post_id ) {
		$post = get_post( $post_id );

		if ( ! $post instanceof WP_Post || empty( $post->post_name ) ) {
			return '';
		}

		return '[fdbs_feed slug="' . esc_attr( $post->post_name ) . '"]';
	}

	public static function sanitize_hex_color( $color ) {
		$color = is_string( $color ) ? trim( $color ) : '';

		if ( '' === $color ) {
			return '';
		}

		if ( preg_match( '/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $color ) ) {
			return $color;
		}

		return '';
	}

	public static function get_active_buttons( $post_id ) {
		$buttons = get_post_meta( $post_id, '_fdbs_buttons', true );

		if ( ! is_array( $buttons ) ) {
			return array();
		}

		return array_values(
			array_filter(
				$buttons,
				function ( $button ) {
					return is_array( $button ) && ! empty( $button['enabled'] ) && ! empty( $button['title'] );
				}
			)
		);
	}

	public static function get_feed_layout( $post_id ) {
		$layout = get_post_meta( $post_id, '_fdbs_layout', true );

		$defaults = array(
			'columns'       => 1,
			'max_width'     => 760,
			'button_gap'    => 14,
			'button_radius' => 16,
			'show_title'    => true,
		);

		if ( ! is_array( $layout ) ) {
			return $defaults;
		}

		$layout = wp_parse_args( $layout, $defaults );

		$layout['columns']       = max( 1, min( 3, absint( $layout['columns'] ) ) );
		$layout['max_width']     = max( 280, min( 1600, absint( $layout['max_width'] ) ) );
		$layout['button_gap']    = max( 0, min( 80, absint( $layout['button_gap'] ) ) );
		$layout['button_radius'] = max( 0, min( 60, absint( $layout['button_radius'] ) ) );
		$layout['show_title']    = ! empty( $layout['show_title'] );

		return $layout;
	}

	public static function get_button_icon_class( $icon ) {
		$icon = is_string( $icon ) ? trim( $icon ) : '';

		if ( '' === $icon ) {
			return '';
		}

		$icon = sanitize_html_class( $icon );

		if ( '' === $icon ) {
			return '';
		}

		if ( 0 !== strpos( $icon, 'dashicons-' ) ) {
			$icon = 'dashicons-' . $icon;
		}

		return $icon;
	}

	public static function get_available_icons() {
    	return array(
    		'phone'         => 'Teléfono',
    		'phone-call'    => 'Llamada',
    		'mail'          => 'Correo',
    		'send'          => 'Enviar',
    		'message-circle'=> 'Mensaje',
    		'messages-square'=> 'Chat',
    		'link'          => 'Enlace',
    		'external-link' => 'Enlace externo',
    		'globe'         => 'Web',
    		'monitor'       => 'Pantalla',
    		'laptop'        => 'Laptop',
    		'smartphone'    => 'Celular',
    		'instagram'     => 'Instagram',
    		'facebook'      => 'Facebook',
    		'twitter'       => 'Twitter',
    		'youtube'       => 'YouTube',
    		'twitch'        => 'Twitch',
    		'linkedin'      => 'LinkedIn',
    		'github'        => 'GitHub',
    		'music'         => 'Música',
    		'headphones'    => 'Audífonos',
    		'video'         => 'Video',
    		'camera'        => 'Cámara',
    		'image'         => 'Imagen',
    		'film'          => 'Película',
    		'play'          => 'Play',
    		'pause'         => 'Pause',
    		'download'      => 'Descarga',
    		'upload'        => 'Subida',
    		'file'          => 'Archivo',
    		'file-text'     => 'Documento',
    		'folder'        => 'Carpeta',
    		'folder-open'   => 'Carpeta abierta',
    		'calendar'      => 'Calendario',
    		'clock'         => 'Reloj',
    		'map-pin'       => 'Ubicación',
    		'map'           => 'Mapa',
    		'navigation'    => 'Navegación',
    		'compass'       => 'Brújula',
    		'home'          => 'Inicio',
    		'building'      => 'Edificio',
    		'store'         => 'Tienda',
    		'shopping-cart' => 'Carrito',
    		'credit-card'   => 'Tarjeta',
    		'wallet'        => 'Billetera',
    		'badge-dollar-sign' => 'Dinero',
    		'tag'           => 'Etiqueta',
    		'percent'       => 'Porcentaje',
    		'gift'          => 'Regalo',
    		'star'          => 'Estrella',
    		'heart'         => 'Corazón',
    		'thumbs-up'     => 'Like',
    		'bookmark'      => 'Guardar',
    		'check'         => 'Check',
    		'check-circle'  => 'Check círculo',
    		'alert-circle'  => 'Alerta',
    		'info'          => 'Info',
    		'help-circle'   => 'Ayuda',
    		'shield'        => 'Escudo',
    		'lock'          => 'Candado',
    		'unlock'        => 'Desbloquear',
    		'user'          => 'Usuario',
    		'users'         => 'Usuarios',
    		'badge-check'   => 'Verificado',
    		'briefcase'     => 'Portafolio',
    		'graduation-cap'=> 'Educación',
    		'book'          => 'Libro',
    		'pen-square'    => 'Escribir',
    		'edit'          => 'Editar',
    		'palette'       => 'Diseño',
    		'brush'         => 'Brocha',
    		'wand-sparkles' => 'Creativo',
    		'rocket'        => 'Lanzamiento',
    		'target'        => 'Objetivo',
    		'bar-chart-3'   => 'Estadísticas',
    		'trending-up'   => 'Crecimiento',
    		'megaphone'     => 'Marketing',
    		'share-2'       => 'Compartir',
    		'qr-code'       => 'QR',
    		'coffee'        => 'Café',
    		'utensils'      => 'Comida',
    		'truck'         => 'Envío',
    		'car'           => 'Carro',
    		'plane'         => 'Avión',
    		'bed'           => 'Hotel',
    		'sun'           => 'Sol',
    		'moon'          => 'Luna',
    		'cloud'         => 'Nube',
    		'leaf'          => 'Hoja',
    		'flame'         => 'Fuego',
    		'sparkles'      => 'Brillo'
    	);
    }
}