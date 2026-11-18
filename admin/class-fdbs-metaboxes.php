<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FDBS_Metaboxes {

	public function init() {
		add_action( 'add_meta_boxes', array( $this, 'register_metaboxes' ) );
	}

	public function register_metaboxes() {
		add_meta_box(
			'fdbs_feed_settings',
			__( 'Configuración del feed', 'feed-de-botones-simple' ),
			array( $this, 'render_feed_settings' ),
			'fdbs_feed',
			'normal',
			'high'
		);

		add_meta_box(
			'fdbs_feed_buttons',
			__( 'Botones del feed', 'feed-de-botones-simple' ),
			array( $this, 'render_feed_buttons' ),
			'fdbs_feed',
			'normal',
			'default'
		);

		add_meta_box(
			'fdbs_feed_preview',
			__( 'Vista previa', 'feed-de-botones-simple' ),
			array( $this, 'render_feed_preview' ),
			'fdbs_feed',
			'side',
			'default'
		);

		add_meta_box(
			'fdbs_feed_shortcode',
			__( 'Shortcode', 'feed-de-botones-simple' ),
			array( $this, 'render_feed_shortcode' ),
			'fdbs_feed',
			'side',
			'high'
		);
	}

	public function render_feed_settings( $post ) {
		$enabled_fields = get_post_meta( $post->ID, '_fdbs_enabled_fields', true );
		$layout         = FDBS_Helpers::get_feed_layout( $post->ID );

		if ( ! is_array( $enabled_fields ) ) {
			$enabled_fields = array();
		}

		wp_nonce_field( 'fdbs_save_feed_settings', 'fdbs_feed_settings_nonce' );

		include FDBS_PLUGIN_PATH . 'admin/views/metabox-feed-settings.php';
	}

	public function render_feed_buttons( $post ) {
		$enabled_fields = get_post_meta( $post->ID, '_fdbs_enabled_fields', true );
		$buttons        = get_post_meta( $post->ID, '_fdbs_buttons', true );

		if ( ! is_array( $enabled_fields ) ) {
			$enabled_fields = array();
		}

		if ( ! is_array( $buttons ) ) {
			$buttons = array();
		}

		include FDBS_PLUGIN_PATH . 'admin/views/metabox-feed-buttons.php';
	}

	public function render_feed_preview( $post ) {
		$preview_buttons = FDBS_Helpers::get_active_buttons( $post->ID );
		$feed_title      = get_the_title( $post->ID );
		$layout          = FDBS_Helpers::get_feed_layout( $post->ID );

		include FDBS_PLUGIN_PATH . 'admin/views/metabox-feed-preview.php';
	}

	public function render_feed_shortcode( $post ) {
		include FDBS_PLUGIN_PATH . 'admin/views/metabox-feed-shortcode.php';
	}
}