<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FDBS_Deactivator {

	public static function deactivate() {
		flush_rewrite_rules();
	}
}