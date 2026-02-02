<?php
/**
 * Template: Front Page
 * 
 * @package Nosfir
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="primary" class="site-main front-page">
    
    <?php
    /**
     * Seções da Homepage
     * Ordem e ativação controladas pelo Customizer
     */
    
    $sections = array(
        'hero'         => get_theme_mod('nosfir_section_hero_enable', true),
        'features'     => get_theme_mod('nosfir_section_features_enable', true),
        'about'        => get_theme_mod('nosfir_section_about_enable', true),
        'services'     => get_theme_mod('nosfir_section_services_enable', true),
        'portfolio'    => get_theme_mod('nosfir_section_portfolio_enable', true),
        'testimonials' => get_theme_mod('nosfir_section_testimonials_enable', true),
        'team'         => get_theme_mod('nosfir_section_team_enable', false),
        'blog'         => get_theme_mod('nosfir_section_blog_enable', true),
        'cta'          => get_theme_mod('nosfir_section_cta_enable', true),
        'contact'      => get_theme_mod('nosfir_section_contact_enable', true),
    );
    
    // Hook antes das seções
    do_action('nosfir_before_homepage_sections');
    
    foreach ($sections as $section => $enabled) {
        if ($enabled) {
            $template_path = 'template-parts/homepage/section-' . $section;
            
            // Verificar se template existe
            if (locate_template($template_path . '.php')) {
                
                // Hook antes de cada seção
                do_action('nosfir_before_section_' . $section);
                
                get_template_part($template_path);
                
                // Hook depois de cada seção
                do_action('nosfir_after_section_' . $section);
            }
        }
    }
    
    // Hook depois das seções
    do_action('nosfir_after_homepage_sections');
    ?>
    
</main>

<?php
get_footer();