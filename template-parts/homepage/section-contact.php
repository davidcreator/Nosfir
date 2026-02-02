<?php
/**
 * Section: Portfolio
 * 
 * @package Nosfir
 */

if (!defined('ABSPATH')) {
    exit;
}

// Configurações
$section_title    = get_theme_mod('nosfir_portfolio_title', __('Nosso Portfolio', 'nosfir'));
$section_subtitle = get_theme_mod('nosfir_portfolio_subtitle', __('Trabalhos Recentes', 'nosfir'));
$posts_per_page   = get_theme_mod('nosfir_portfolio_count', 6);
$show_filter      = get_theme_mod('nosfir_portfolio_show_filter', true);

// Query para portfolio (custom post type ou posts com categoria)
$portfolio_args = array(
    'post_type'      => 'portfolio', // ou 'post' se não tiver CPT
    'posts_per_page' => $posts_per_page,
    'post_status'    => 'publish',
);

// Fallback para posts se CPT não existir
if (!post_type_exists('portfolio')) {
    $portfolio_args['post_type'] = 'post';
    $portfolio_args['category_name'] = 'portfolio';
}

$portfolio_query = new WP_Query($portfolio_args);

// Obter categorias para filtro
$portfolio_categories = array();
if ($show_filter && $portfolio_query->have_posts()) {
    while ($portfolio_query->have_posts()) {
        $portfolio_query->the_post();
        $cats = get_the_terms(get_the_ID(), 'portfolio_category') ?: get_the_category();
        if ($cats) {
            foreach ($cats as $cat) {
                $portfolio_categories[$cat->slug] = $cat->name;
            }
        }
    }
    wp_reset_postdata();
}
?>

<section id="portfolio" class="nosfir-section nosfir-portfolio" data-section="portfolio">
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
        
        <?php if ($show_filter && !empty($portfolio_categories)) : ?>
            <nav class="nosfir-portfolio__filter animate-on-scroll" data-animation="fadeInUp" data-delay="100">
                <ul class="nosfir-filter-list">
                    <li>
                        <button class="nosfir-filter-btn is-active" data-filter="*">
                            <?php esc_html_e('Todos', 'nosfir'); ?>
                        </button>
                    </li>
                    <?php foreach ($portfolio_categories as $slug => $name) : ?>
                        <li>
                            <button class="nosfir-filter-btn" data-filter=".<?php echo esc_attr($slug); ?>">
                                <?php echo esc_html($name); ?>
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        <?php endif; ?>
        
        <?php if ($portfolio_query->have_posts()) : ?>
            
            <div class="nosfir-portfolio__grid" id="portfolio-grid">
                
                <?php 
                $index = 0;
                while ($portfolio_query->have_posts()) : 
                    $portfolio_query->the_post();
                    
                    // Classes de categoria para filtro
                    $item_classes = array('nosfir-portfolio__item');
                    $cats = get_the_terms(get_the_ID(), 'portfolio_category') ?: get_the_category();
                    if ($cats) {
                        foreach ($cats as $cat) {
                            $item_classes[] = $cat->slug;
                        }
                    }
                    
                    $delay = (($index % 3) + 1) * 100;
                    $index++;
                ?>
                    
                    <article class="<?php echo esc_attr(implode(' ', $item_classes)); ?> animate-on-scroll" 
                             data-animation="fadeInUp" 
                             data-delay="<?php echo esc_attr($delay); ?>">
                        
                        <div class="nosfir-portfolio__item-inner">
                            
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="nosfir-portfolio__thumbnail">
                                    <?php the_post_thumbnail('large', array(
                                        'class' => 'nosfir-portfolio__image',
                                        'loading' => 'lazy',
                                    )); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="nosfir-portfolio__overlay">
                                <div class="nosfir-portfolio__content">
                                    
                                    <h3 class="nosfir-portfolio__title">
                                        <?php the_title(); ?>
                                    </h3>
                                    
                                    <?php if ($cats) : ?>
                                        <span class="nosfir-portfolio__category">
                                            <?php echo esc_html($cats[0]->name); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <div class="nosfir-portfolio__actions">
                                        <a href="<?php the_permalink(); ?>" class="nosfir-portfolio__link" aria-label="<?php esc_attr_e('Ver projeto', 'nosfir'); ?>">
                                            <i class="fas fa-link"></i>
                                        </a>
                                        
                                        <?php if (has_post_thumbnail()) : ?>
                                            <a href="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'full')); ?>" 
                                               class="nosfir-portfolio__lightbox" 
                                               data-lightbox="portfolio"
                                               aria-label="<?php esc_attr_e('Ampliar imagem', 'nosfir'); ?>">
                                                <i class="fas fa-search-plus"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    
                                </div>
                            </div>
                            
                        </div>
                        
                    </article>
                    
                <?php endwhile; ?>
                
            </div>
            
            <?php wp_reset_postdata(); ?>
            
        <?php else : ?>
            
            <p class="nosfir-portfolio__empty">
                <?php esc_html_e('Nenhum projeto encontrado.', 'nosfir'); ?>
            </p>
            
        <?php endif; ?>
        
    </div>
</section>