<?php
/**
 * Header Search
 */
if ( ! function_exists( 'nosfir_header_search' ) ) {
    function nosfir_header_search() {
        static $rendered = false;
        if ( $rendered ) return;
        $rendered = true;
        
        if ( ! get_theme_mod( 'nosfir_header_search', true ) ) {
            return;
        }
        ?>
        <div class="header-search" id="header-search">
            <button type="button" class="search-toggle" aria-expanded="false">
                <svg class="icon-search" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="9" r="7"/><path d="M14 14l4 4"/>
                </svg>
                <svg class="icon-close" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            
            <div class="search-dropdown" aria-hidden="true">
                <!-- FORMULÁRIO INLINE - NÃO USA get_search_form() -->
                <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search...', 'nosfir' ); ?>" value="<?php echo get_search_query(); ?>" name="s" required />
                    <button type="submit" class="search-submit">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="9" cy="9" r="7"/><path d="M14 14l4 4"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        <?php
    }
}