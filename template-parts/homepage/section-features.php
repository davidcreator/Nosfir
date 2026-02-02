<?php
/**
 * Section: Features
 * 
 * @package Nosfir
 */

if (!defined('ABSPATH')) {
    exit;
}

// Dados do Customizer
$section_title    = get_theme_mod('nosfir_features_title', __('Nossos Recursos', 'nosfir'));
$section_subtitle = get_theme_mod('nosfir_features_subtitle', __('O que oferecemos', 'nosfir'));
$features_count   = get_theme_mod('nosfir_features_count', 6);

// Features padrão
$default_features = array(
    array(
        'icon'  => 'fas fa-rocket',
        'title' => __('Rápido', 'nosfir'),
        'desc'  => __('Performance otimizada para carregamento rápido.', 'nosfir'),
    ),
    array(
        'icon'  => 'fas fa-mobile-alt',
        'title' => __('Responsivo', 'nosfir'),
        'desc'  => __('Adapta-se perfeitamente a todos os dispositivos.', 'nosfir'),
    ),
    array(
        'icon'  => 'fas fa-shield-alt',
        'title' => __('Seguro', 'nosfir'),
        'desc'  => __('Código limpo seguindo melhores práticas.', 'nosfir'),
    ),
    array(
        'icon'  => 'fas fa-cogs',
        'title' => __('Customizável', 'nosfir'),
        'desc'  => __('Fácil personalização via Customizer.', 'nosfir'),
    ),
    array(
        'icon'  => 'fas fa-headset',
        'title' => __('Suporte', 'nosfir'),
        'desc'  => __('Suporte técnico dedicado.', 'nosfir'),
    ),
    array(
        'icon'  => 'fas fa-sync',
        'title' => __('Atualizações', 'nosfir'),
        'desc'  => __('Atualizações regulares com novidades.', 'nosfir'),
    ),
);
?>

<section id="features" class="nosfir-section nosfir-features" data-section="features">
    <div class="nosfir-container">
        
        <?php if ($section_title || $section_subtitle) : ?>
            <header class="nosfir-section__header animate-on-scroll" data-animation="fadeInUp">
                
                <?php if ($section_subtitle) : ?>
                    <span class="nosfir-section__subtitle">
                        <?php echo esc_html($section_subtitle); ?>
                    </span>
                <?php endif; ?>
                
                <?php if ($section_title) : ?>
                    <h2 class="nosfir-section__title">
                        <?php echo wp_kses_post($section_title); ?>
                    </h2>
                <?php endif; ?>
                
            </header>
        <?php endif; ?>
        
        <div class="nosfir-features__grid">
            
            <?php 
            for ($i = 0; $i < min($features_count, count($default_features)); $i++) :
                $feature = $default_features[$i];
                
                // Pegar dados customizados se existirem
                $feature_icon  = get_theme_mod("nosfir_feature_{$i}_icon", $feature['icon']);
                $feature_title = get_theme_mod("nosfir_feature_{$i}_title", $feature['title']);
                $feature_desc  = get_theme_mod("nosfir_feature_{$i}_desc", $feature['desc']);
                
                $delay = ($i + 1) * 100;
            ?>
                
                <div class="nosfir-feature animate-on-scroll" data-animation="fadeInUp" data-delay="<?php echo esc_attr($delay); ?>">
                    
                    <?php if ($feature_icon) : ?>
                        <div class="nosfir-feature__icon">
                            <i class="<?php echo esc_attr($feature_icon); ?>"></i>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($feature_title) : ?>
                        <h3 class="nosfir-feature__title">
                            <?php echo esc_html($feature_title); ?>
                        </h3>
                    <?php endif; ?>
                    
                    <?php if ($feature_desc) : ?>
                        <p class="nosfir-feature__description">
                            <?php echo esc_html($feature_desc); ?>
                        </p>
                    <?php endif; ?>
                    
                </div>
                
            <?php endfor; ?>
            
        </div>
        
    </div>
</section>