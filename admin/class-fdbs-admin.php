<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FDBS_Admin {

	public function init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public function enqueue_assets( $hook ) {
		$screen = get_current_screen();

		if ( ! $screen || 'fdbs_feed' !== $screen->post_type ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_style(
			'fdbs-admin',
			FDBS_PLUGIN_URL . 'assets/css/admin.css',
			array( 'wp-color-picker' ),
			FDBS_VERSION
		);

		wp_enqueue_script(
			'fdbs-lucide',
			'https://unpkg.com/lucide@latest',
			array(),
			FDBS_VERSION,
			true
		);

		wp_enqueue_script(
			'fdbs-admin',
			FDBS_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery', 'jquery-ui-sortable', 'wp-color-picker', 'fdbs-lucide' ),
			FDBS_VERSION,
			true
		);
	}
}
