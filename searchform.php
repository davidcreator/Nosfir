<?php
/**
 * Template for displaying search forms
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$unique_id = wp_unique_id( 'search-form-' );
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label for="<?php echo esc_attr( $unique_id ); ?>" class="screen-reader-text">
        <?php esc_html_e( 'Search for:', 'nosfir' ); ?>
    </label>
    <input type="search" 
           id="<?php echo esc_attr( $unique_id ); ?>" 
           class="search-field" 
           placeholder="<?php esc_attr_e( 'Search...', 'nosfir' ); ?>" 
           value="<?php echo get_search_query(); ?>" 
           name="s" />
    <button type="submit" class="search-submit">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="9" cy="9" r="7"/>
            <path d="M14 14l4 4"/>
        </svg>
        <span class="screen-reader-text"><?php esc_html_e( 'Search', 'nosfir' ); ?></span>
    </button>
</form>