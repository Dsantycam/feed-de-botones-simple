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

	/**
	 * Devuelve los elementos activos del feed (botones y separadores) en orden.
	 */
	public static function get_active_buttons( $post_id ) {
		$buttons = get_post_meta( $post_id, '_fdbs_buttons', true );

		if ( ! is_array( $buttons ) ) {
			return array();
		}

		return array_values(
			array_filter(
				$buttons,
				function ( $button ) {
					if ( ! is_array( $button ) || empty( $button['enabled'] ) ) {
						return false;
					}

					if ( self::is_separator( $button ) ) {
						return true;
					}

					return ! empty( $button['title'] );
				}
			)
		);
	}

	public static function is_separator( $item ) {
		return is_array( $item ) && isset( $item['type'] ) && 'separator' === $item['type'];
	}

	/**
	 * Cuenta solo botones (no separadores).
	 */
	public static function count_buttons( $items ) {
		if ( ! is_array( $items ) ) {
			return 0;
		}

		return count(
			array_filter(
				$items,
				function ( $item ) {
					return is_array( $item ) && ! self::is_separator( $item ) && ! empty( $item['title'] );
				}
			)
		);
	}

	public static function get_separator_styles() {
		return array(
			'line'     => 'Línea simple',
			'gradient' => 'Degradado',
			'dashed'   => 'Guiones',
			'dotted'   => 'Punteado',
			'double'   => 'Doble línea',
			'wave'     => 'Onda',
			'diamond'  => 'Ornamento',
			'dots'     => 'Tres puntos',
			'pill'     => 'Etiqueta',
			'space'    => 'Solo espacio',
		);
	}

	public static function get_separator_defaults() {
		return array(
			'type'           => 'separator',
			'style'          => 'gradient',
			'label'          => '',
			'align'          => 'center',
			'color'          => '',
			'thickness'      => 1,
			'width'          => 100,
			'spacing_top'    => 24,
			'spacing_bottom' => 24,
			'enabled'        => true,
			'collapsed'      => false,
		);
	}

	/**
	 * Normaliza y sanea un separador (sirve tanto para guardar como para renderizar).
	 */
	public static function sanitize_separator( $separator ) {
		$separator = wp_parse_args( is_array( $separator ) ? $separator : array(), self::get_separator_defaults() );
		$styles    = self::get_separator_styles();

		return array(
			'type'           => 'separator',
			'style'          => isset( $styles[ $separator['style'] ] ) ? $separator['style'] : 'gradient',
			'label'          => sanitize_text_field( $separator['label'] ),
			'align'          => 'left' === $separator['align'] ? 'left' : 'center',
			'color'          => self::sanitize_hex_color( $separator['color'] ),
			'thickness'      => max( 1, min( 8, absint( $separator['thickness'] ) ) ),
			'width'          => in_array( absint( $separator['width'] ), array( 25, 50, 75, 100 ), true ) ? absint( $separator['width'] ) : 100,
			'spacing_top'    => max( 0, min( 160, absint( $separator['spacing_top'] ) ) ),
			'spacing_bottom' => max( 0, min( 160, absint( $separator['spacing_bottom'] ) ) ),
			'enabled'        => ! empty( $separator['enabled'] ),
			'collapsed'      => ! empty( $separator['collapsed'] ),
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