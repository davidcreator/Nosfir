<?php
/**
 * Nosfir Admin Class
 *
 * @package nosfir
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Nosfir_Admin' ) ) :

    class Nosfir_Admin {

        /**
         * Construtor
         */
        public function __construct() {
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
        }

        /**
         * Enfileirar estilos do admin
         */
        public function admin_styles() {
            $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            
            wp_enqueue_style(
                'nosfir-admin',
                get_template_directory_uri() . '/assets/css/admin/admin' . $suffix . '.css',
                array(),
                NOSFIR_VERSION
            );
        }

        /**
         * Enfileirar scripts do admin
         */
        public function admin_scripts() {
            $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            
            wp_enqueue_script(
                'nosfir-admin',
                get_template_directory_uri() . '/assets/js/admin/admin' . $suffix . '.js',
                array( 'jquery' ),
                NOSFIR_VERSION,
                true
            );

            wp_localize_script(
                'nosfir-admin',
                'nosfirAdmin',
                array(
                    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                    'nonce'   => wp_create_nonce( 'nosfir-admin-nonce' ),
                )
            );
        }
    }

endif;

return new Nosfir_Admin();