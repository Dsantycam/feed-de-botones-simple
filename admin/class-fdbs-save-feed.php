<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FDBS_Save_Feed {

	public function init() {
		add_action( 'save_post_fdbs_feed', array( $this, 'save' ) );
	}

	public function save( $post_id ) {
		if ( ! isset( $_POST['fdbs_feed_settings_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fdbs_feed_settings_nonce'] ) ), 'fdbs_save_feed_settings' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$this->save_enabled_fields( $post_id );
		$this->save_layout( $post_id );
		$this->save_buttons( $post_id );
	}

	private function save_enabled_fields( $post_id ) {
		$allowed_keys = array(
			'image',
			'description',
			'link',
			'bg_color',
			'text_color',
			'border_color',
			'icon',
		);

		$submitted = isset( $_POST['fdbs_enabled_fields'] )
			? (array) wp_unslash( $_POST['fdbs_enabled_fields'] )
			: array();

		$clean = array();

		foreach ( $allowed_keys as $key ) {
			$clean[ $key ] = in_array( $key, $submitted, true );
		}

		update_post_meta( $post_id, '_fdbs_enabled_fields', $clean );
	}

	private function save_layout( $post_id ) {
		$layout = isset( $_POST['fdbs_layout'] )
			? (array) wp_unslash( $_POST['fdbs_layout'] )
			: array();

		$clean_layout = array(
			'columns'       => isset( $layout['columns'] ) ? max( 1, min( 3, absint( $layout['columns'] ) ) ) : 1,
			'max_width'     => isset( $layout['max_width'] ) ? max( 280, min( 1600, absint( $layout['max_width'] ) ) ) : 760,
			'button_gap'    => isset( $layout['button_gap'] ) ? max( 0, min( 80, absint( $layout['button_gap'] ) ) ) : 14,
			'button_radius' => isset( $layout['button_radius'] ) ? max( 0, min( 60, absint( $layout['button_radius'] ) ) ) : 16,
			'show_title'    => ! empty( $layout['show_title'] ),
		);

		update_post_meta( $post_id, '_fdbs_layout', $clean_layout );
	}

	private function save_buttons( $post_id ) {
		$submitted_buttons = isset( $_POST['fdbs_buttons'] )
			? (array) wp_unslash( $_POST['fdbs_buttons'] )
			: array();

		$clean_buttons = array();

		foreach ( $submitted_buttons as $button ) {
			if ( ! is_array( $button ) ) {
				continue;
			}

			if ( FDBS_Helpers::is_separator( $button ) ) {
				// Un checkbox desmarcado no se envía: hay que fijarlo antes de aplicar
				// los valores por defecto, o un separador desactivado volvería a activarse.
				$button['enabled']   = ! empty( $button['enabled'] );
				$button['collapsed'] = ! empty( $button['collapsed'] );

				$clean_buttons[] = FDBS_Helpers::sanitize_separator( $button );
				continue;
			}

			$title = isset( $button['title'] ) ? sanitize_text_field( $button['title'] ) : '';

			if ( '' === $title ) {
				continue;
			}

			$clean_buttons[] = array(
				'title'        => $title,
				'description'  => isset( $button['description'] ) ? sanitize_textarea_field( $button['description'] ) : '',
				'link'         => isset( $button['link'] ) ? esc_url_raw( $button['link'] ) : '',
				'image_id'     => isset( $button['image_id'] ) ? absint( $button['image_id'] ) : 0,
				'image_url'    => isset( $button['image_url'] ) ? esc_url_raw( $button['image_url'] ) : '',
				'icon'         => isset( $button['icon'] ) ? sanitize_text_field( $button['icon'] ) : '',
				'bg_color'     => isset( $button['bg_color'] ) ? FDBS_Helpers::sanitize_hex_color( $button['bg_color'] ) : '',
				'text_color'   => isset( $button['text_color'] ) ? FDBS_Helpers::sanitize_hex_color( $button['text_color'] ) : '',
				'border_color' => isset( $button['border_color'] ) ? FDBS_Helpers::sanitize_hex_color( $button['border_color'] ) : '',
				'new_tab'      => ! empty( $button['new_tab'] ),
				'nofollow'     => ! empty( $button['nofollow'] ),
				'enabled'      => ! empty( $button['enabled'] ),
				'collapsed'    => ! empty( $button['collapsed'] ),
			);
		}

		update_post_meta( $post_id, '_fdbs_buttons', $clean_buttons );
	}
}