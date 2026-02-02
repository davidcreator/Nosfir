<?php
/**
 * Custom Gutenberg Blocks for this theme
 *
 * @package Nosfir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Nosfir_Blocks' ) ) {
	class Nosfir_Blocks {
		public function __construct() {
			// Init blocks
		}
	}
}

new Nosfir_Blocks();
