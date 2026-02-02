<?php
/**
 * Nosfir More Customizer Control
 *
 * Cria um controle customizado para exibir informações adicionais,
 * links úteis, promoções e recursos do tema no Customizer.
 *
 * @package  Nosfir
 * @since    1.0.0
 * @author   David Creator
 */

// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe de controle 'More' para o Customizer
 */
class Nosfir_Customizer_Control_More extends WP_Customize_Control {

    /**
     * Tipo do controle
     *
     * @var string
     */
    public $type = 'nosfir_more';

    /**
     * Subtipo do controle (info, premium, support, review, etc)
     *
     * @var string
     */
    public $subtype = 'info';

    /**
     * Título do card
     *
     * @var string
     */
    public $card_title = '';

    /**
     * Descrição do card
     *
     * @var string
     */
    public $card_description = '';

    /**
     * Ícone do card
     *
     * @var string
     */
    public $icon = '';

    /**
     * Imagem de destaque
     *
     * @var string
     */
    public $featured_image = '';

    /**
     * Lista de features/recursos
     *
     * @var array
     */
    public $features = array();

    /**
     * Botões de ação
     *
     * @var array
     */
    public $buttons = array();

    /**
     * Links úteis
     *
     * @var array
     */
    public $links = array();

    /**
     * Estatísticas
     *
     * @var array
     */
    public $stats = array();

    /**
     * Testimonials/depoimentos
     *
     * @var array
     */
    public $testimonials = array();

    /**
     * FAQ - Perguntas frequentes
     *
     * @var array
     */
    public $faqs = array();

    /**
     * Vídeo tutorial
     *
     * @var string
     */
    public $video_url = '';

    /**
     * Badge/etiqueta
     *
     * @var string
     */
    public $badge = '';

    /**
     * Cor do tema do card
     *
     * @var string
     */
    public $color_scheme = 'default';

    /**
     * Se deve mostrar rating
     *
     * @var bool
     */
    public $show_rating = false;

    /**
     * Rating atual
     *
     * @var float
     */
    public $rating = 5.0;

    /**
     * Número de reviews
     *
     * @var int
     */
    public $reviews_count = 0;

    /**
     * Versão do tema/plugin
     *
     * @var string
     */
    public $version = '';

    /**
     * Data de atualização
     *
     * @var string
     */
    public $last_updated = '';

    /**
     * Construtor
     */
    public function __construct($manager, $id, $args = array()) {
        parent::__construct($manager, $id, $args);
        
        // Define valores padrão
        $this->setup_defaults();
    }

    /**
     * Configura valores padrão
     */
    protected function setup_defaults() {
        $theme = wp_get_theme();
        
        if (empty($this->version)) {
            $this->version = $theme->get('Version');
        }
        
        if (empty($this->card_title) && $this->subtype === 'info') {
            $this->card_title = sprintf(__('About %s', 'nosfir'), $theme->get('Name'));
        }
    }

    /**
     * Enfileira scripts e estilos
     */
    public function enqueue() {
        // CSS do controle
        wp_enqueue_style(
            'nosfir-customizer-more-control',
            get_template_directory_uri() . '/assets/css/customizer/more-control.css',
            array(),
            wp_get_theme()->get('Version')
        );

        // JavaScript do controle
        wp_enqueue_script(
            'nosfir-customizer-more-control',
            get_template_directory_uri() . '/assets/js/customizer/more-control.js',
            array('jquery', 'customize-base'),
            wp_get_theme()->get('Version'),
            true
        );

        // Localização
        wp_localize_script('nosfir-customizer-more-control', 'nosfir_more_control', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nosfir-more-control'),
            'strings' => array(
                'learn_more' => __('Learn More', 'nosfir'),
                'get_started' => __('Get Started', 'nosfir'),
                'view_demo' => __('View Demo', 'nosfir'),
                'documentation' => __('Documentation', 'nosfir'),
                'support' => __('Support', 'nosfir'),
                'upgrade' => __('Upgrade to Pro', 'nosfir'),
                'installed' => __('Installed', 'nosfir'),
                'active' => __('Active', 'nosfir'),
                'loading' => __('Loading...', 'nosfir')
            )
        ));
    }

    /**
     * Renderiza o conteúdo do controle
     */
    public function render_content() {
        $wrapper_classes = array(
            'nosfir-more-control',
            'nosfir-more-' . esc_attr($this->subtype),
            'nosfir-scheme-' . esc_attr($this->color_scheme)
        );

        ?>
        <div class="<?php echo esc_attr(implode(' ', $wrapper_classes)); ?>">
            <?php
            switch ($this->subtype) {
                case 'premium':
                    $this->render_premium_upsell();
                    break;
                
                case 'extensions':
                    $this->render_extensions();
                    break;
                
                case 'review':
                    $this->render_review_request();
                    break;
                
                case 'support':
                    $this->render_support_info();
                    break;
                
                case 'documentation':
                    $this->render_documentation();
                    break;
                
                case 'changelog':
                    $this->render_changelog();
                    break;
                
                case 'newsletter':
                    $this->render_newsletter();
                    break;
                
                case 'social':
                    $this->render_social_links();
                    break;
                
                case 'video':
                    $this->render_video_tutorials();
                    break;
                
                case 'plugins':
                    $this->render_recommended_plugins();
                    break;
                
                case 'showcase':
                    $this->render_showcase();
                    break;
                
                case 'testimonials':
                    $this->render_testimonials();
                    break;
                
                case 'faq':
                    $this->render_faq();
                    break;
                
                case 'stats':
                    $this->render_stats();
                    break;
                
                case 'info':
                default:
                    $this->render_info();
                    break;
            }
            ?>
        </div>
        <?php
    }

    /**
     * Renderiza informações gerais
     */
    protected function render_info() {
        $theme = wp_get_theme();
        ?>
        <div class="nosfir-more-info-card">
            <?php if ($this->badge) : ?>
                <span class="nosfir-badge nosfir-badge-<?php echo esc_attr($this->color_scheme); ?>">
                    <?php echo esc_html($this->badge); ?>
                </span>
            <?php endif; ?>

            <?php if ($this->icon) : ?>
                <div class="nosfir-more-icon">
                    <span class="dashicons dashicons-<?php echo esc_attr($this->icon); ?>"></span>
                </div>
            <?php endif; ?>

            <h3 class="nosfir-more-title">
                <?php echo esc_html($this->card_title ?: sprintf(__('Welcome to %s', 'nosfir'), $theme->get('Name'))); ?>
            </h3>

            <?php if ($this->version || $this->last_updated) : ?>
                <div class="nosfir-more-meta">
                    <?php if ($this->version) : ?>
                        <span class="nosfir-version">
                            <?php echo esc_html__('Version:', 'nosfir'); ?> <?php echo esc_html($this->version); ?>
                        </span>
                    <?php endif; ?>
                    
                    <?php if ($this->last_updated) : ?>
                        <span class="nosfir-updated">
                            <?php echo esc_html__('Updated:', 'nosfir'); ?> <?php echo esc_html($this->last_updated); ?>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="nosfir-more-description">
                <?php if ($this->card_description) : ?>
                    <?php echo wp_kses_post($this->card_description); ?>
                <?php else : ?>
                    <p>
                        <?php
                        printf(
                            __('Thank you for choosing %1$s! This theme comes with powerful customization options and features to help you build amazing websites. Explore the %2$stheme dashboard%3$s for more information and resources.', 'nosfir'),
                            '<strong>' . esc_html($theme->get('Name')) . '</strong>',
                            '<a href="' . esc_url(admin_url('admin.php?page=nosfir-dashboard')) . '">',
                            '</a>'
                        );
                        ?>
                    </p>
                <?php endif; ?>
            </div>

            <?php if (!empty($this->features)) : ?>
                <div class="nosfir-more-features">
                    <h4><?php esc_html_e('Key Features:', 'nosfir'); ?></h4>
                    <ul class="nosfir-feature-list">
                        <?php foreach ($this->features as $feature) : ?>
                            <li>
                                <span class="dashicons dashicons-yes"></span>
                                <?php echo esc_html($feature); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php $this->render_buttons(); ?>
            <?php $this->render_links(); ?>
        </div>
        <?php
    }

    /**
     * Renderiza upsell premium
     */
    protected function render_premium_upsell() {
        ?>
        <div class="nosfir-more-premium-card">
            <?php if ($this->featured_image) : ?>
                <div class="nosfir-premium-image">
                    <img src="<?php echo esc_url($this->featured_image); ?>" alt="Premium Version">
                </div>
            <?php endif; ?>

            <div class="nosfir-premium-content">
                <span class="nosfir-premium-badge"><?php esc_html_e('PREMIUM', 'nosfir'); ?></span>
                
                <h3 class="nosfir-premium-title">
                    <?php echo esc_html($this->card_title ?: __('Unlock Premium Features', 'nosfir')); ?>
                </h3>

                <div class="nosfir-premium-description">
                    <?php if ($this->card_description) : ?>
                        <?php echo wp_kses_post($this->card_description); ?>
                    <?php else : ?>
                        <p><?php esc_html_e('Upgrade to the premium version and unlock powerful features to take your website to the next level.', 'nosfir'); ?></p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($this->features)) : ?>
                    <div class="nosfir-premium-features">
                        <div class="nosfir-features-grid">
                            <?php foreach ($this->features as $feature) : ?>
                                <div class="nosfir-feature-item">
                                    <span class="dashicons dashicons-star-filled"></span>
                                    <span><?php echo esc_html($feature); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($this->stats)) : ?>
                    <div class="nosfir-premium-stats">
                        <?php foreach ($this->stats as $stat) : ?>
                            <div class="nosfir-stat">
                                <span class="nosfir-stat-value"><?php echo esc_html($stat['value']); ?></span>
                                <span class="nosfir-stat-label"><?php echo esc_html($stat['label']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="nosfir-premium-pricing">
                    <?php if (!empty($this->buttons)) : ?>
                        <?php foreach ($this->buttons as $button) : ?>
                            <?php if ($button['primary'] ?? false) : ?>
                                <div class="nosfir-price-tag">
                                    <?php if (isset($button['price'])) : ?>
                                        <span class="nosfir-price"><?php echo esc_html($button['price']); ?></span>
                                    <?php endif; ?>
                                    <?php if (isset($button['discount'])) : ?>
                                        <span class="nosfir-discount"><?php echo esc_html($button['discount']); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php $this->render_buttons(); ?>

                <div class="nosfir-premium-guarantee">
                    <span class="dashicons dashicons-shield"></span>
                    <span><?php esc_html_e('30-Day Money Back Guarantee', 'nosfir'); ?></span>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Renderiza extensões
     */
    protected function render_extensions() {
        ?>
        <div class="nosfir-more-extensions-card">
            <h3 class="nosfir-extensions-title">
                <?php echo esc_html($this->card_title ?: __('Available Extensions', 'nosfir')); ?>
            </h3>

            <p class="nosfir-extensions-description">
                <?php echo wp_kses_post($this->card_description ?: __('Enhance your theme with these powerful extensions:', 'nosfir')); ?>
            </p>

            <?php if (!empty($this->features)) : ?>
                <div class="nosfir-extensions-grid">
                    <?php foreach ($this->features as $extension) : ?>
                        <div class="nosfir-extension-item">
                            <?php if (is_array($extension)) : ?>
                                <?php if (isset($extension['icon'])) : ?>
                                    <span class="dashicons dashicons-<?php echo esc_attr($extension['icon']); ?>"></span>
                                <?php endif; ?>
                                <h4><?php echo esc_html($extension['name']); ?></h4>
                                <?php if (isset($extension['description'])) : ?>
                                    <p><?php echo esc_html($extension['description']); ?></p>
                                <?php endif; ?>
                                <?php if (isset($extension['price'])) : ?>
                                    <span class="nosfir-extension-price"><?php echo esc_html($extension['price']); ?></span>
                                <?php endif; ?>
                            <?php else : ?>
                                <span class="dashicons dashicons-admin-plugins"></span>
                                <p><?php echo esc_html($extension); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php $this->render_buttons(); ?>
        </div>
        <?php
    }

    /**
     * Renderiza solicitação de review
     */
    protected function render_review_request() {
        $theme = wp_get_theme();
        ?>
        <div class="nosfir-more-review-card">
            <div class="nosfir-review-stars">
                <?php for ($i = 0; $i < 5; $i++) : ?>
                    <span class="dashicons dashicons-star-filled"></span>
                <?php endfor; ?>
            </div>

            <h3 class="nosfir-review-title">
                <?php
                echo esc_html($this->card_title ?: sprintf(__('Enjoying %s?', 'nosfir'), $theme->get('Name')));
                ?>
            </h3>

            <div class="nosfir-review-description">
                <?php if ($this->card_description) : ?>
                    <?php echo wp_kses_post($this->card_description); ?>
                <?php else : ?>
                    <p>
                        <?php
                        printf(
                            __('We\'d love to hear your feedback! If you\'re enjoying %s, please consider leaving us a 5-star review on WordPress.org. Your support helps us continue improving the theme!', 'nosfir'),
                            '<strong>' . esc_html($theme->get('Name')) . '</strong>'
                        );
                        ?>
                    </p>
                <?php endif; ?>
            </div>

            <?php if ($this->show_rating && $this->rating) : ?>
                <div class="nosfir-current-rating">
                    <div class="nosfir-rating-display">
                        <span class="nosfir-rating-value"><?php echo esc_html(number_format($this->rating, 1)); ?></span>
                        <div class="nosfir-rating-stars">
                            <?php $this->render_star_rating($this->rating); ?>
                        </div>
                    </div>
                    <?php if ($this->reviews_count) : ?>
                        <span class="nosfir-reviews-count">
                            <?php
                            printf(
                                _n('Based on %s review', 'Based on %s reviews', $this->reviews_count, 'nosfir'),
                                number_format_i18n($this->reviews_count)
                            );
                            ?>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="nosfir-review-buttons">
                <a href="<?php echo esc_url($this->get_review_url()); ?>" 
                   class="button button-primary" 
                   target="_blank">
                    <span class="dashicons dashicons-thumbs-up"></span>
                    <?php esc_html_e('Leave a Review', 'nosfir'); ?>
                </a>
                <button type="button" class="button button-link nosfir-maybe-later">
                    <?php esc_html_e('Maybe Later', 'nosfir'); ?>
                </button>
            </div>

            <div class="nosfir-review-testimonial">
                <?php if (!empty($this->testimonials) && isset($this->testimonials[0])) : ?>
                    <blockquote>
                        <p>"<?php echo esc_html($this->testimonials[0]['text']); ?>"</p>
                        <cite>— <?php echo esc_html($this->testimonials[0]['author']); ?></cite>
                    </blockquote>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Renderiza informações de suporte
     */
    protected function render_support_info() {
        ?>
        <div class="nosfir-more-support-card">
            <div class="nosfir-support-header">
                <span class="dashicons dashicons-sos"></span>
                <h3><?php echo esc_html($this->card_title ?: __('Need Help?', 'nosfir')); ?></h3>
            </div>

            <div class="nosfir-support-description">
                <?php echo wp_kses_post($this->card_description ?: __('We\'re here to help! Choose from the following support options:', 'nosfir')); ?>
            </div>

            <div class="nosfir-support-options">
                <div class="nosfir-support-option">
                    <span class="dashicons dashicons-book"></span>
                    <h4><?php esc_html_e('Documentation', 'nosfir'); ?></h4>
                    <p><?php esc_html_e('Comprehensive guides and tutorials', 'nosfir'); ?></p>
                    <a href="<?php echo esc_url($this->get_documentation_url()); ?>" target="_blank" class="button">
                        <?php esc_html_e('View Docs', 'nosfir'); ?>
                    </a>
                </div>

                <div class="nosfir-support-option">
                    <span class="dashicons dashicons-groups"></span>
                    <h4><?php esc_html_e('Community Forum', 'nosfir'); ?></h4>
                    <p><?php esc_html_e('Get help from our community', 'nosfir'); ?></p>
                    <a href="<?php echo esc_url($this->get_support_url()); ?>" target="_blank" class="button">
                        <?php esc_html_e('Visit Forum', 'nosfir'); ?>
                    </a>
                </div>

                <div class="nosfir-support-option">
                    <span class="dashicons dashicons-email"></span>
                    <h4><?php esc_html_e('Email Support', 'nosfir'); ?></h4>
                    <p><?php esc_html_e('Direct support from our team', 'nosfir'); ?></p>
                    <a href="<?php echo esc_url($this->get_contact_url()); ?>" target="_blank" class="button">
                        <?php esc_html_e('Contact Us', 'nosfir'); ?>
                    </a>
                </div>
            </div>

            <?php if (!empty($this->faqs)) : ?>
                <div class="nosfir-support-faq">
                    <h4><?php esc_html_e('Quick Answers', 'nosfir'); ?></h4>
                    <?php foreach (array_slice($this->faqs, 0, 3) as $faq) : ?>
                        <details class="nosfir-faq-item">
                            <summary><?php echo esc_html($faq['question']); ?></summary>
                            <p><?php echo wp_kses_post($faq['answer']); ?></p>
                        </details>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Renderiza documentação
     */
    protected function render_documentation() {
        ?>
        <div class="nosfir-more-docs-card">
            <div class="nosfir-docs-header">
                <span class="dashicons dashicons-book-alt"></span>
                <h3><?php echo esc_html($this->card_title ?: __('Documentation', 'nosfir')); ?></h3>
            </div>

            <p><?php echo wp_kses_post($this->card_description ?: __('Everything you need to know about using this theme:', 'nosfir')); ?></p>

            <?php if (!empty($this->links)) : ?>
                <div class="nosfir-docs-categories">
                    <?php foreach ($this->links as $category => $links) : ?>
                        <div class="nosfir-docs-category">
                            <h4><?php echo esc_html($category); ?></h4>
                            <ul>
                                <?php foreach ($links as $link) : ?>
                                    <li>
                                        <a href="<?php echo esc_url($link['url']); ?>" target="_blank">
                                            <?php echo esc_html($link['title']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php $this->render_buttons(); ?>
        </div>
        <?php
    }

    /**
     * Renderiza changelog
     */
    protected function render_changelog() {
        ?>
        <div class="nosfir-more-changelog-card">
            <h3><?php echo esc_html($this->card_title ?: __('Recent Updates', 'nosfir')); ?></h3>
            
            <?php if (!empty($this->features)) : ?>
                <div class="nosfir-changelog-entries">
                    <?php foreach ($this->features as $version => $changes) : ?>
                        <div class="nosfir-changelog-entry">
                            <h4>Version <?php echo esc_html($version); ?></h4>
                            <?php if (is_array($changes)) : ?>
                                <ul>
                                    <?php foreach ($changes as $change) : ?>
                                        <li><?php echo esc_html($change); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else : ?>
                                <p><?php echo esc_html($changes); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php $this->render_buttons(); ?>
        </div>
        <?php
    }

    /**
     * Renderiza newsletter
     */
    protected function render_newsletter() {
        ?>
        <div class="nosfir-more-newsletter-card">
            <div class="nosfir-newsletter-icon">
                <span class="dashicons dashicons-email-alt"></span>
            </div>

            <h3><?php echo esc_html($this->card_title ?: __('Stay Updated', 'nosfir')); ?></h3>
            
            <p><?php echo wp_kses_post($this->card_description ?: __('Subscribe to our newsletter for theme updates, tips, and exclusive offers.', 'nosfir')); ?></p>

            <form class="nosfir-newsletter-form" action="#" method="post">
                <input type="email" 
                       placeholder="<?php esc_attr_e('Your email address', 'nosfir'); ?>" 
                       required>
                <button type="submit" class="button button-primary">
                    <?php esc_html_e('Subscribe', 'nosfir'); ?>
                </button>
            </form>

            <p class="nosfir-newsletter-privacy">
                <span class="dashicons dashicons-lock"></span>
                <?php esc_html_e('We respect your privacy. Unsubscribe at any time.', 'nosfir'); ?>
            </p>
        </div>
        <?php
    }

    /**
     * Renderiza links sociais
     */
    protected function render_social_links() {
        ?>
        <div class="nosfir-more-social-card">
            <h3><?php echo esc_html($this->card_title ?: __('Connect With Us', 'nosfir')); ?></h3>
            
            <p><?php echo wp_kses_post($this->card_description ?: __('Follow us on social media for updates and inspiration:', 'nosfir')); ?></p>

            <?php if (!empty($this->links)) : ?>
                <div class="nosfir-social-links">
                    <?php foreach ($this->links as $network => $url) : ?>
                        <a href="<?php echo esc_url($url); ?>" 
                           class="nosfir-social-link nosfir-social-<?php echo esc_attr($network); ?>" 
                           target="_blank"
                           aria-label="<?php echo esc_attr(ucfirst($network)); ?>">
                            <span class="dashicons dashicons-<?php echo esc_attr($this->get_social_icon($network)); ?>"></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Renderiza tutoriais em vídeo
     */
    protected function render_video_tutorials() {
        ?>
        <div class="nosfir-more-video-card">
            <h3><?php echo esc_html($this->card_title ?: __('Video Tutorials', 'nosfir')); ?></h3>
            
            <?php if ($this->video_url) : ?>
                <div class="nosfir-video-embed">
                    <?php echo $this->get_video_embed($this->video_url); ?>
                </div>
            <?php endif; ?>

            <p><?php echo wp_kses_post($this->card_description ?: __('Learn how to use the theme with our video tutorials:', 'nosfir')); ?></p>

            <?php if (!empty($this->features)) : ?>
                <div class="nosfir-video-list">
                    <?php foreach ($this->features as $video) : ?>
                        <div class="nosfir-video-item">
                            <span class="dashicons dashicons-video-alt3"></span>
                            <?php if (is_array($video)) : ?>
                                <a href="<?php echo esc_url($video['url']); ?>" target="_blank">
                                    <?php echo esc_html($video['title']); ?>
                                </a>
                                <?php if (isset($video['duration'])) : ?>
                                    <span class="nosfir-video-duration"><?php echo esc_html($video['duration']); ?></span>
                                <?php endif; ?>
                            <?php else : ?>
                                <span><?php echo esc_html($video); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php $this->render_buttons(); ?>
        </div>
        <?php
    }

    /**
     * Renderiza plugins recomendados
     */
    protected function render_recommended_plugins() {
        ?>
        <div class="nosfir-more-plugins-card">
            <h3><?php echo esc_html($this->card_title ?: __('Recommended Plugins', 'nosfir')); ?></h3>
            
            <p><?php echo wp_kses_post($this->card_description ?: __('Enhance your website with these recommended plugins:', 'nosfir')); ?></p>

            <?php if (!empty($this->features)) : ?>
                <div class="nosfir-plugins-grid">
                    <?php foreach ($this->features as $plugin) : ?>
                        <div class="nosfir-plugin-item">
                            <?php if (is_array($plugin)) : ?>
                                <div class="nosfir-plugin-header">
                                    <?php if (isset($plugin['icon'])) : ?>
                                        <img src="<?php echo esc_url($plugin['icon']); ?>" alt="<?php echo esc_attr($plugin['name']); ?>">
                                    <?php endif; ?>
                                    <h4><?php echo esc_html($plugin['name']); ?></h4>
                                </div>
                                <?php if (isset($plugin['description'])) : ?>
                                    <p><?php echo esc_html($plugin['description']); ?></p>
                                <?php endif; ?>
                                <div class="nosfir-plugin-status" data-plugin="<?php echo esc_attr($plugin['slug'] ?? ''); ?>">
                                    <?php $this->render_plugin_status($plugin); ?>
                                </div>
                            <?php else : ?>
                                <p><?php echo esc_html($plugin); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php $this->render_buttons(); ?>
        </div>
        <?php
    }

    /**
     * Renderiza showcase
     */
    protected function render_showcase() {
        ?>
        <div class="nosfir-more-showcase-card">
            <h3><?php echo esc_html($this->card_title ?: __('Theme Showcase', 'nosfir')); ?></h3>
            
            <p><?php echo wp_kses_post($this->card_description ?: __('See what others have built with this theme:', 'nosfir')); ?></p>

            <?php if (!empty($this->features)) : ?>
                <div class="nosfir-showcase-grid">
                    <?php foreach ($this->features as $site) : ?>
                        <div class="nosfir-showcase-item">
                            <?php if (is_array($site) && isset($site['image'])) : ?>
                                <img src="<?php echo esc_url($site['image']); ?>" alt="<?php echo esc_attr($site['name'] ?? ''); ?>">
                            <?php endif; ?>
                            <?php if (is_array($site) && isset($site['name'])) : ?>
                                <h4><?php echo esc_html($site['name']); ?></h4>
                            <?php endif; ?>
                            <?php if (is_array($site) && isset($site['url'])) : ?>
                                <a href="<?php echo esc_url($site['url']); ?>" target="_blank" class="button">
                                    <?php esc_html_e('View Site', 'nosfir'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Renderiza testimonials
     */
    protected function render_testimonials() {
        if (empty($this->testimonials)) {
            return;
        }
        ?>
        <div class="nosfir-more-testimonials-card">
            <h3><?php echo esc_html($this->card_title ?: __('What Users Say', 'nosfir')); ?></h3>
            
            <div class="nosfir-testimonials-slider">
                <?php foreach ($this->testimonials as $testimonial) : ?>
                    <div class="nosfir-testimonial">
                        <div class="nosfir-testimonial-rating">
                            <?php $this->render_star_rating($testimonial['rating'] ?? 5); ?>
                        </div>
                        <blockquote>
                            <p>"<?php echo esc_html($testimonial['text']); ?>"</p>
                        </blockquote>
                        <cite>
                            <?php if (isset($testimonial['avatar'])) : ?>
                                <img src="<?php echo esc_url($testimonial['avatar']); ?>" alt="<?php echo esc_attr($testimonial['author']); ?>">
                            <?php endif; ?>
                            <span>
                                <strong><?php echo esc_html($testimonial['author']); ?></strong>
                                <?php if (isset($testimonial['role'])) : ?>
                                    <em><?php echo esc_html($testimonial['role']); ?></em>
                                <?php endif; ?>
                            </span>
                        </cite>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Renderiza FAQ
     */
    protected function render_faq() {
        if (empty($this->faqs)) {
            return;
        }
        ?>
        <div class="nosfir-more-faq-card">
            <h3><?php echo esc_html($this->card_title ?: __('Frequently Asked Questions', 'nosfir')); ?></h3>
            
            <div class="nosfir-faq-list">
                <?php foreach ($this->faqs as $faq) : ?>
                    <details class="nosfir-faq-item">
                        <summary>
                            <span class="dashicons dashicons-arrow-down"></span>
                            <?php echo esc_html($faq['question']); ?>
                        </summary>
                        <div class="nosfir-faq-answer">
                            <?php echo wp_kses_post($faq['answer']); ?>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>

            <?php $this->render_buttons(); ?>
        </div>
        <?php
    }

    /**
     * Renderiza estatísticas
     */
    protected function render_stats() {
        if (empty($this->stats)) {
            return;
        }
        ?>
        <div class="nosfir-more-stats-card">
            <h3><?php echo esc_html($this->card_title ?: __('Theme Statistics', 'nosfir')); ?></h3>
            
            <div class="nosfir-stats-grid">
                <?php foreach ($this->stats as $stat) : ?>
                    <div class="nosfir-stat-item">
                        <?php if (isset($stat['icon'])) : ?>
                            <span class="dashicons dashicons-<?php echo esc_attr($stat['icon']); ?>"></span>
                        <?php endif; ?>
                        <div class="nosfir-stat-value"><?php echo esc_html($stat['value']); ?></div>
                        <div class="nosfir-stat-label"><?php echo esc_html($stat['label']); ?></div>
                        <?php if (isset($stat['trend'])) : ?>
                            <div class="nosfir-stat-trend nosfir-trend-<?php echo $stat['trend'] > 0 ? 'up' : 'down'; ?>">
                                <span class="dashicons dashicons-arrow-<?php echo $stat['trend'] > 0 ? 'up' : 'down'; ?>-alt"></span>
                                <?php echo esc_html(abs($stat['trend'])) . '%'; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Renderiza botões
     */
    protected function render_buttons() {
        if (empty($this->buttons)) {
            return;
        }
        ?>
        <div class="nosfir-more-buttons">
            <?php foreach ($this->buttons as $button) : ?>
                <a href="<?php echo esc_url($button['url']); ?>" 
                   class="button <?php echo isset($button['primary']) && $button['primary'] ? 'button-primary' : ''; ?> <?php echo esc_attr($button['class'] ?? ''); ?>"
                   target="<?php echo esc_attr($button['target'] ?? '_blank'); ?>">
                    <?php if (isset($button['icon'])) : ?>
                        <span class="dashicons dashicons-<?php echo esc_attr($button['icon']); ?>"></span>
                    <?php endif; ?>
                    <?php echo esc_html($button['text']); ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Renderiza links
     */
    protected function render_links() {
        if (empty($this->links)) {
            return;
        }
        ?>
        <div class="nosfir-more-links">
            <?php foreach ($this->links as $link) : ?>
                <?php if (is_array($link)) : ?>
                    <a href="<?php echo esc_url($link['url']); ?>" target="_blank">
                        <?php if (isset($link['icon'])) : ?>
                            <span class="dashicons dashicons-<?php echo esc_attr($link['icon']); ?>"></span>
                        <?php endif; ?>
                        <?php echo esc_html($link['text']); ?>
                    </a>
                <?php else : ?>
                    <?php echo wp_kses_post($link); ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Renderiza avaliação com estrelas
     */
    protected function render_star_rating($rating) {
        $full_stars = floor($rating);
        $half_star = ($rating - $full_stars) >= 0.5;
        $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);

        for ($i = 0; $i < $full_stars; $i++) {
            echo '<span class="dashicons dashicons-star-filled"></span>';
        }
        
        if ($half_star) {
            echo '<span class="dashicons dashicons-star-half"></span>';
        }
        
        for ($i = 0; $i < $empty_stars; $i++) {
            echo '<span class="dashicons dashicons-star-empty"></span>';
        }
    }

    /**
     * Renderiza status do plugin
     */
    protected function render_plugin_status($plugin) {
        // Implementar verificação de status do plugin
        echo '<span class="nosfir-plugin-checking">' . esc_html__('Checking...', 'nosfir') . '</span>';
    }

    /**
     * Obtém embed de vídeo
     */
    protected function get_video_embed($url) {
        return wp_oembed_get($url);
    }

    /**
     * Obtém ícone da rede social
     */
    protected function get_social_icon($network) {
        $icons = array(
            'facebook' => 'facebook',
            'twitter' => 'twitter',
            'instagram' => 'instagram',
            'youtube' => 'youtube',
            'linkedin' => 'linkedin',
            'github' => 'github',
            'email' => 'email',
            'website' => 'admin-links'
        );
        
        return isset($icons[$network]) ? $icons[$network] : 'share';
    }

    /**
     * URLs helpers
     */
    protected function get_review_url() {
        return 'https://wordpress.org/support/theme/nosfir/reviews/#new-post';
    }

    protected function get_documentation_url() {
        return 'https://docs.nosfir.com';
    }

    protected function get_support_url() {
        return 'https://wordpress.org/support/theme/nosfir/';
    }

    protected function get_contact_url() {
        return 'https://nosfir.com/contact';
    }

    /**
     * Refresh do controle via JS
     */
    public function to_json() {
        parent::to_json();
        
        $this->json['subtype'] = $this->subtype;
        $this->json['card_title'] = $this->card_title;
        $this->json['card_description'] = $this->card_description;
        $this->json['icon'] = $this->icon;
        $this->json['featured_image'] = $this->featured_image;
        $this->json['features'] = $this->features;
        $this->json['buttons'] = $this->buttons;
        $this->json['links'] = $this->links;
        $this->json['stats'] = $this->stats;
        $this->json['testimonials'] = $this->testimonials;
        $this->json['faqs'] = $this->faqs;
        $this->json['video_url'] = $this->video_url;
        $this->json['badge'] = $this->badge;
        $this->json['color_scheme'] = $this->color_scheme;
        $this->json['show_rating'] = $this->show_rating;
        $this->json['rating'] = $this->rating;
        $this->json['reviews_count'] = $this->reviews_count;
    }
}

// Registra o controle no Customizer
if (class_exists('WP_Customize_Control')) {
    add_action('customize_register', function($wp_customize) {
        // Registra o tipo de controle
        $wp_customize->register_control_type('Nosfir_Customizer_Control_More');
    });
}
