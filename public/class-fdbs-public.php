<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FDBS_Public {

	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
		add_shortcode( 'fdbs_feed', array( $this, 'render_shortcode' ) );
	}

	public function register_assets() {
		wp_register_style(
			'fdbs-frontend',
			FDBS_PLUGIN_URL . 'assets/css/frontend.css',
			array(),
			FDBS_VERSION
		);

		wp_register_script(
			'fdbs-frontend',
			FDBS_PLUGIN_URL . 'assets/js/frontend.js',
			array(),
			FDBS_VERSION,
			true
		);
		
		wp_register_script(
        	'fdbs-lucide',
        	'https://unpkg.com/lucide@latest',
        	array(),
        	null,
        	true
        );

    
	}

	public function render_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'id'         => 0,
				'slug'       => '',
				'columns'    => 0,
				'show_title' => '',
			),
			$atts,
			'fdbs_feed'
		);

		$post_id = $this->resolve_feed_id( $atts );

		if ( ! $post_id || 'fdbs_feed' !== get_post_type( $post_id ) ) {
			return '';
		}

		$buttons = FDBS_Helpers::get_active_buttons( $post_id );

		if ( 0 === FDBS_Helpers::count_buttons( $buttons ) ) {
			return '';
		}

		$layout = FDBS_Helpers::get_feed_layout( $post_id );

		$columns = absint( $atts['columns'] );
		if ( $columns < 1 || $columns > 3 ) {
			$columns = (int) $layout['columns'];
		}

		$show_title_att = trim( (string) $atts['show_title'] );
		if ( '' === $show_title_att ) {
			$show_title = ! empty( $layout['show_title'] );
		} else {
			$show_title = in_array( strtolower( $show_title_att ), array( 'yes', 'true', '1', 'si', 'sí' ), true );
		}

		wp_enqueue_style( 'fdbs-frontend' );
		wp_enqueue_script( 'fdbs-frontend' );
		wp_enqueue_script( 'fdbs-lucide' );

		$feed_title = $show_title ? get_the_title( $post_id ) : '';

		$wrapper_style = sprintf(
			'--fdbs-max-width:%dpx;--fdbs-button-gap:%dpx;--fdbs-button-radius:%dpx;',
			(int) $layout['max_width'],
			(int) $layout['button_gap'],
			(int) $layout['button_radius']
		);

		ob_start();
		include FDBS_PLUGIN_PATH . 'public/views/feed-wrapper.php';
		return ob_get_clean();
	
	}

	private function resolve_feed_id( $atts ) {
		$id = isset( $atts['id'] ) ? absint( $atts['id'] ) : 0;

		if ( $id > 0 ) {
			return $id;
		}

		$slug = isset( $atts['slug'] ) ? sanitize_title( $atts['slug'] ) : '';

		if ( '' === $slug ) {
			return 0;
		}

		$feed = get_page_by_path( $slug, OBJECT, 'fdbs_feed' );

		if ( ! $feed instanceof WP_Post ) {
			return 0;
		}

		return (int) $feed->ID;
	}
}