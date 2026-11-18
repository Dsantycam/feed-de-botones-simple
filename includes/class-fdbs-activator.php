<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FDBS_Activator {

	public static function activate() {
		FDBS_Post_Type::register();
		flush_rewrite_rules();
	}
}