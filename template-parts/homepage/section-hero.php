<?php
/**
 * Section: Hero
 * 
 * @package Nosfir
 */

if (!defined('ABSPATH')) {
    exit;
}

// Dados do Customizer
$hero_title      = get_theme_mod('nosfir_hero_title', __('Bem-vindo ao Nosfir', 'nosfir'));
$hero_subtitle   = get_theme_mod('nosfir_hero_subtitle', __('Tema WordPress Moderno e Profissional', 'nosfir'));
$hero_btn_text   = get_theme_mod('nosfir_hero_btn_text', __('Saiba Mais', 'nosfir'));
$hero_btn_url    = get_theme_mod('nosfir_hero_btn_url', '#about');
$hero_btn2_text  = get_theme_mod('nosfir_hero_btn2_text', __('Contato', 'nosfir'));
$hero_btn2_url   = get_theme_mod('nosfir_hero_btn2_url', '#contact');
$hero_bg_image   = get_theme_mod('nosfir_hero_bg_image', '');
$hero_bg_overlay = get_theme_mod('nosfir_hero_bg_overlay', 'rgba(0,0,0,0.5)');

// Classes CSS dinâmicas
$hero_classes = array('nosfir-section', 'nosfir-hero');
if ($hero_bg_image) {
    $hero_classes[] = 'has-bg-image';
}
?>

<section id="hero" class="<?php echo esc_attr(implode(' ', $hero_classes)); ?>"
    <?php if ($hero_bg_image) : ?>
        style="background-image: url('<?php echo esc_url($hero_bg_image); ?>');"
    <?php endif; ?>
    data-section="hero"
>
    
    <?php if ($hero_bg_image) : ?>
        <div class="nosfir-hero__overlay" style="background-color: <?php echo esc_attr($hero_bg_overlay); ?>;"></div>
    <?php endif; ?>
    
    <div class="nosfir-container">
        <div class="nosfir-hero__content">
            
            <?php if ($hero_title) : ?>
                <h1 class="nosfir-hero__title animate-on-scroll" data-animation="fadeInUp">
                    <?php echo wp_kses_post($hero_title); ?>
                </h1>
            <?php endif; ?>
            
            <?php if ($hero_subtitle) : ?>
                <p class="nosfir-hero__subtitle animate-on-scroll" data-animation="fadeInUp" data-delay="200">
                    <?php echo wp_kses_post($hero_subtitle); ?>
                </p>
            <?php endif; ?>
            
            <div class="nosfir-hero__buttons animate-on-scroll" data-animation="fadeInUp" data-delay="400">
                
                <?php if ($hero_btn_text && $hero_btn_url) : ?>
                    <a href="<?php echo esc_url($hero_btn_url); ?>" class="nosfir-btn nosfir-btn--primary">
                        <?php echo esc_html($hero_btn_text); ?>
                    </a>
                <?php endif; ?>
                
                <?php if ($hero_btn2_text && $hero_btn2_url) : ?>
                    <a href="<?php echo esc_url($hero_btn2_url); ?>" class="nosfir-btn nosfir-btn--secondary">
                        <?php echo esc_html($hero_btn2_text); ?>
                    </a>
                <?php endif; ?>
                
            </div>
            
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="nosfir-hero__scroll-indicator">
        <a href="#features" class="scroll-down" aria-label="<?php esc_attr_e('Rolar para baixo', 'nosfir'); ?>">
            <span class="scroll-down__arrow"></span>
        </a>
    </div>
    
</section>