<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FDBS_Plugin {

	protected $admin;
	protected $public;
	protected $metaboxes;
	protected $save_feed;
	protected $admin_columns;

	public function __construct() {
		$this->admin         = new FDBS_Admin();
		$this->public        = new FDBS_Public();
		$this->metaboxes     = new FDBS_Metaboxes();
		$this->save_feed     = new FDBS_Save_Feed();
		$this->admin_columns = new FDBS_Admin_Columns();
	}

	public function run() {
		add_action( 'init', array( 'FDBS_Post_Type', 'register' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		if ( is_admin() ) {
			$this->admin->init();
			$this->metaboxes->init();
			$this->save_feed->init();
			$this->admin_columns->init();
		}

		$this->public->init();
	}

	public function load_textdomain() {
		load_plugin_textdomain(
			'feed-de-botones-simple',
			false,
			dirname( FDBS_PLUGIN_BASENAME ) . '/languages'
		);
	}
}