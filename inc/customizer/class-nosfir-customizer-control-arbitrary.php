<?php
/**
 * Nosfir Arbitrary Customizer Control
 *
 * Cria controles customizados arbitrários para o Customizer do WordPress,
 * incluindo divisores, títulos, alertas, cards informativos e muito mais.
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
 * Classe de controle arbitrário para o Customizer
 */
class Nosfir_Customizer_Control_Arbitrary extends WP_Customize_Control {

    /**
     * Tipo do controle
     *
     * @var string
     */
    public $type = 'nosfir_arbitrary';

    /**
     * Subtipo do controle (heading, divider, alert, etc)
     *
     * @var string
     */
    public $subtype = 'text';

    /**
     * Descrição do controle
     *
     * @var string
     */
    public $description = '';

    /**
     * Ícone para o controle
     *
     * @var string
     */
    public $icon = '';

    /**
     * Cor/estilo do controle (primary, success, warning, danger, info)
     *
     * @var string
     */
    public $style = 'default';

    /**
     * URL para botão ou link
     *
     * @var string
     */
    public $url = '';

    /**
     * Texto do botão
     *
     * @var string
     */
    public $button_text = '';

    /**
     * Target do link (_blank, _self, etc)
     *
     * @var string
     */
    public $target = '_blank';

    /**
     * Badge/etiqueta
     *
     * @var string
     */
    public $badge = '';

    /**
     * Classe CSS adicional
     *
     * @var string
     */
    public $class = '';

    /**
     * HTML customizado
     *
     * @var string
     */
    public $html = '';

    /**
     * Array de itens (para listas)
     *
     * @var array
     */
    public $items = array();

    /**
     * Imagem/thumbnail
     *
     * @var string
     */
    public $image = '';

    /**
     * Vídeo URL
     *
     * @var string
     */
    public $video = '';

    /**
     * Código a ser exibido
     *
     * @var string
     */
    public $code = '';

    /**
     * Linguagem do código (para syntax highlighting)
     *
     * @var string
     */
    public $code_language = 'css';

    /**
     * Se o controle é colapsável
     *
     * @var bool
     */
    public $collapsible = false;

    /**
     * Estado inicial (expanded/collapsed)
     *
     * @var string
     */
    public $collapsed = false;

    /**
     * Dados adicionais
     *
     * @var array
     */
    public $data = array();

    /**
     * Construtor
     *
     * @param WP_Customize_Manager $manager Instância do Customizer Manager.
     * @param string              $id      ID do controle.
     * @param array               $args    Argumentos do controle.
     */
    public function __construct($manager, $id, $args = array()) {
        parent::__construct($manager, $id, $args);
        
        // Define o tipo principal
        $this->type = 'nosfir_arbitrary';
    }

    /**
     * Enfileira scripts e estilos
     */
    public function enqueue() {
        // CSS do controle
        wp_enqueue_style(
            'nosfir-customizer-arbitrary-control',
            get_template_directory_uri() . '/assets/css/customizer/arbitrary-control.css',
            array(),
            wp_get_theme()->get('Version')
        );

        // JavaScript do controle
        wp_enqueue_script(
            'nosfir-customizer-arbitrary-control',
            get_template_directory_uri() . '/assets/js/customizer/arbitrary-control.js',
            array('jquery', 'customize-base'),
            wp_get_theme()->get('Version'),
            true
        );

        // Adiciona ícones se necessário
        if ($this->icon || $this->subtype === 'icon-list') {
            wp_enqueue_style('dashicons');
        }

        // Adiciona Prism.js para syntax highlighting se necessário
        if ($this->subtype === 'code') {
            wp_enqueue_style(
                'prism',
                'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css',
                array(),
                '1.29.0'
            );
            wp_enqueue_script(
                'prism',
                'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js',
                array(),
                '1.29.0',
                true
            );
        }
    }

    /**
     * Renderiza o conteúdo do controle
     */
    public function render_content() {
        $wrapper_classes = array(
            'nosfir-arbitrary-control',
            'nosfir-arbitrary-' . esc_attr($this->subtype),
            'nosfir-style-' . esc_attr($this->style)
        );

        if ($this->class) {
            $wrapper_classes[] = esc_attr($this->class);
        }

        if ($this->collapsible) {
            $wrapper_classes[] = 'nosfir-collapsible';
            if ($this->collapsed) {
                $wrapper_classes[] = 'nosfir-collapsed';
            }
        }

        echo '<div class="' . implode(' ', $wrapper_classes) . '">';

        switch ($this->subtype) {
            case 'heading':
                $this->render_heading();
                break;

            case 'divider':
                $this->render_divider();
                break;

            case 'alert':
                $this->render_alert();
                break;

            case 'info-card':
                $this->render_info_card();
                break;

            case 'button':
                $this->render_button();
                break;

            case 'list':
                $this->render_list();
                break;

            case 'icon-list':
                $this->render_icon_list();
                break;

            case 'progress':
                $this->render_progress();
                break;

            case 'stats':
                $this->render_stats();
                break;

            case 'image':
                $this->render_image();
                break;

            case 'video':
                $this->render_video();
                break;

            case 'code':
                $this->render_code();
                break;

            case 'tabs':
                $this->render_tabs();
                break;

            case 'accordion':
                $this->render_accordion();
                break;

            case 'timeline':
                $this->render_timeline();
                break;

            case 'pricing':
                $this->render_pricing();
                break;

            case 'testimonial':
                $this->render_testimonial();
                break;

            case 'social':
                $this->render_social();
                break;

            case 'html':
                $this->render_html();
                break;

            case 'spacer':
                $this->render_spacer();
                break;

            case 'text':
            default:
                $this->render_text();
                break;
        }

        echo '</div>';
    }

    /**
     * Renderiza um heading/título
     */
    protected function render_heading() {
        ?>
        <div class="nosfir-heading-control">
            <?php if ($this->icon) : ?>
                <span class="nosfir-heading-icon dashicons dashicons-<?php echo esc_attr($this->icon); ?>"></span>
            <?php endif; ?>
            
            <span class="customize-control-title nosfir-heading-title">
                <?php echo esc_html($this->label); ?>
                
                <?php if ($this->badge) : ?>
                    <span class="nosfir-badge nosfir-badge-<?php echo esc_attr($this->style); ?>">
                        <?php echo esc_html($this->badge); ?>
                    </span>
                <?php endif; ?>
            </span>
            
            <?php if ($this->description) : ?>
                <span class="description customize-control-description">
                    <?php echo wp_kses_post($this->description); ?>
                </span>
            <?php endif; ?>
            
            <?php if ($this->collapsible) : ?>
                <button type="button" class="nosfir-toggle-collapse">
                    <span class="dashicons dashicons-arrow-down"></span>
                </button>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Renderiza um divisor
     */
    protected function render_divider() {
        $styles = array();
        
        if (!empty($this->data['height'])) {
            $styles[] = 'height: ' . esc_attr($this->data['height']);
        }
        
        if (!empty($this->data['margin'])) {
            $styles[] = 'margin: ' . esc_attr($this->data['margin']);
        }
        
        if (!empty($this->data['color'])) {
            $styles[] = 'border-color: ' . esc_attr($this->data['color']);
        }
        
        $style_attr = !empty($styles) ? 'style="' . implode('; ', $styles) . '"' : '';
        
        if (!empty($this->data['type']) && $this->data['type'] === 'dots') {
            echo '<div class="nosfir-divider-dots" ' . $style_attr . '><span></span><span></span><span></span></div>';
        } elseif (!empty($this->data['type']) && $this->data['type'] === 'gradient') {
            echo '<div class="nosfir-divider-gradient" ' . $style_attr . '></div>';
        } else {
            echo '<hr class="nosfir-divider" ' . $style_attr . ' />';
        }
        
        if ($this->label) {
            echo '<div class="nosfir-divider-text"><span>' . esc_html($this->label) . '</span></div>';
        }
    }

    /**
     * Renderiza um alerta
     */
    protected function render_alert() {
        $alert_class = 'nosfir-alert nosfir-alert-' . esc_attr($this->style);
        
        if (!empty($this->data['dismissible'])) {
            $alert_class .= ' nosfir-alert-dismissible';
        }
        ?>
        <div class="<?php echo esc_attr($alert_class); ?>" role="alert">
            <?php if ($this->icon) : ?>
                <span class="nosfir-alert-icon dashicons dashicons-<?php echo esc_attr($this->icon); ?>"></span>
            <?php endif; ?>
            
            <div class="nosfir-alert-content">
                <?php if ($this->label) : ?>
                    <strong class="nosfir-alert-title"><?php echo esc_html($this->label); ?></strong>
                <?php endif; ?>
                
                <?php if ($this->description) : ?>
                    <p class="nosfir-alert-message"><?php echo wp_kses_post($this->description); ?></p>
                <?php endif; ?>
                
                <?php if ($this->url && $this->button_text) : ?>
                    <a href="<?php echo esc_url($this->url); ?>" 
                       class="nosfir-alert-link" 
                       target="<?php echo esc_attr($this->target); ?>">
                        <?php echo esc_html($this->button_text); ?>
                    </a>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($this->data['dismissible'])) : ?>
                <button type="button" class="nosfir-alert-close" aria-label="<?php esc_attr_e('Close', 'nosfir'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Renderiza um card informativo
     */
    protected function render_info_card() {
        ?>
        <div class="nosfir-info-card nosfir-card-<?php echo esc_attr($this->style); ?>">
            <?php if ($this->image) : ?>
                <div class="nosfir-card-image">
                    <img src="<?php echo esc_url($this->image); ?>" alt="<?php echo esc_attr($this->label); ?>">
                </div>
            <?php endif; ?>
            
            <div class="nosfir-card-body">
                <?php if ($this->icon && !$this->image) : ?>
                    <div class="nosfir-card-icon">
                        <span class="dashicons dashicons-<?php echo esc_attr($this->icon); ?>"></span>
                    </div>
                <?php endif; ?>
                
                <?php if ($this->label) : ?>
                    <h4 class="nosfir-card-title"><?php echo esc_html($this->label); ?></h4>
                <?php endif; ?>
                
                <?php if ($this->description) : ?>
                    <div class="nosfir-card-text"><?php echo wp_kses_post($this->description); ?></div>
                <?php endif; ?>
                
                <?php if ($this->items) : ?>
                    <ul class="nosfir-card-list">
                        <?php foreach ($this->items as $item) : ?>
                            <li><?php echo esc_html($item); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                
                <?php if ($this->url && $this->button_text) : ?>
                    <div class="nosfir-card-footer">
                        <a href="<?php echo esc_url($this->url); ?>" 
                           class="button button-<?php echo $this->style === 'primary' ? 'primary' : 'secondary'; ?>" 
                           target="<?php echo esc_attr($this->target); ?>">
                            <?php echo esc_html($this->button_text); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Renderiza um botão
     */
    protected function render_button() {
        if (!$this->url || !$this->button_text) {
            return;
        }
        
        $button_class = 'button';
        
        if ($this->style === 'primary') {
            $button_class .= ' button-primary';
        } elseif ($this->style === 'link') {
            $button_class = 'button-link';
        } else {
            $button_class .= ' button-secondary';
        }
        
        if (!empty($this->data['size'])) {
            $button_class .= ' button-' . esc_attr($this->data['size']);
        }
        
        if (!empty($this->data['fullwidth'])) {
            $button_class .= ' button-fullwidth';
        }
        ?>
        <div class="nosfir-button-control">
            <?php if ($this->description) : ?>
                <p class="description"><?php echo wp_kses_post($this->description); ?></p>
            <?php endif; ?>
            
            <a href="<?php echo esc_url($this->url); ?>" 
               class="<?php echo esc_attr($button_class); ?>" 
               target="<?php echo esc_attr($this->target); ?>">
                <?php if ($this->icon) : ?>
                    <span class="dashicons dashicons-<?php echo esc_attr($this->icon); ?>"></span>
                <?php endif; ?>
                <?php echo esc_html($this->button_text); ?>
            </a>
        </div>
        <?php
    }

    /**
     * Renderiza uma lista
     */
    protected function render_list() {
        if (empty($this->items)) {
            return;
        }
        
        $list_type = !empty($this->data['ordered']) ? 'ol' : 'ul';
        ?>
        <div class="nosfir-list-control">
            <?php if ($this->label) : ?>
                <h4 class="nosfir-list-title"><?php echo esc_html($this->label); ?></h4>
            <?php endif; ?>
            
            <?php if ($this->description) : ?>
                <p class="description"><?php echo wp_kses_post($this->description); ?></p>
            <?php endif; ?>
            
            <<?php echo $list_type; ?> class="nosfir-list">
                <?php foreach ($this->items as $item) : ?>
                    <?php if (is_array($item)) : ?>
                        <li>
                            <?php if (isset($item['title'])) : ?>
                                <strong><?php echo esc_html($item['title']); ?></strong>
                            <?php endif; ?>
                            <?php if (isset($item['description'])) : ?>
                                <span><?php echo wp_kses_post($item['description']); ?></span>
                            <?php endif; ?>
                        </li>
                    <?php else : ?>
                        <li><?php echo wp_kses_post($item); ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </<?php echo $list_type; ?>>
        </div>
        <?php
    }

    /**
     * Renderiza uma lista com ícones
     */
    protected function render_icon_list() {
        if (empty($this->items)) {
            return;
        }
        ?>
        <div class="nosfir-icon-list-control">
            <?php if ($this->label) : ?>
                <h4 class="nosfir-icon-list-title"><?php echo esc_html($this->label); ?></h4>
            <?php endif; ?>
            
            <?php if ($this->description) : ?>
                <p class="description"><?php echo wp_kses_post($this->description); ?></p>
            <?php endif; ?>
            
            <ul class="nosfir-icon-list">
                <?php foreach ($this->items as $item) : ?>
                    <li class="nosfir-icon-list-item">
                        <?php if (isset($item['icon'])) : ?>
                            <span class="nosfir-icon-list-icon dashicons dashicons-<?php echo esc_attr($item['icon']); ?>"></span>
                        <?php elseif ($this->icon) : ?>
                            <span class="nosfir-icon-list-icon dashicons dashicons-<?php echo esc_attr($this->icon); ?>"></span>
                        <?php endif; ?>
                        
                        <span class="nosfir-icon-list-content">
                            <?php if (is_array($item) && isset($item['text'])) : ?>
                                <?php echo wp_kses_post($item['text']); ?>
                            <?php else : ?>
                                <?php echo wp_kses_post($item); ?>
                            <?php endif; ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
    }

    /**
     * Renderiza uma barra de progresso
     */
    protected function render_progress() {
        $progress = isset($this->data['progress']) ? intval($this->data['progress']) : 0;
        $progress = max(0, min(100, $progress));
        ?>
        <div class="nosfir-progress-control">
            <?php if ($this->label) : ?>
                <div class="nosfir-progress-header">
                    <span class="nosfir-progress-label"><?php echo esc_html($this->label); ?></span>
                    <span class="nosfir-progress-value"><?php echo esc_html($progress); ?>%</span>
                </div>
            <?php endif; ?>
            
            <div class="nosfir-progress-bar">
                <div class="nosfir-progress-fill nosfir-progress-<?php echo esc_attr($this->style); ?>" 
                     style="width: <?php echo esc_attr($progress); ?>%"></div>
            </div>
            
            <?php if ($this->description) : ?>
                <p class="description"><?php echo wp_kses_post($this->description); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Renderiza estatísticas
     */
    protected function render_stats() {
        if (empty($this->data['stats'])) {
            return;
        }
        ?>
        <div class="nosfir-stats-control">
            <?php if ($this->label) : ?>
                <h4 class="nosfir-stats-title"><?php echo esc_html($this->label); ?></h4>
            <?php endif; ?>
            
            <div class="nosfir-stats-grid">
                <?php foreach ($this->data['stats'] as $stat) : ?>
                    <div class="nosfir-stat-item">
                        <?php if (isset($stat['icon'])) : ?>
                            <span class="nosfir-stat-icon dashicons dashicons-<?php echo esc_attr($stat['icon']); ?>"></span>
                        <?php endif; ?>
                        
                        <?php if (isset($stat['value'])) : ?>
                            <div class="nosfir-stat-value"><?php echo esc_html($stat['value']); ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($stat['label'])) : ?>
                            <div class="nosfir-stat-label"><?php echo esc_html($stat['label']); ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($stat['change'])) : ?>
                            <div class="nosfir-stat-change nosfir-change-<?php echo $stat['change'] > 0 ? 'positive' : 'negative'; ?>">
                                <?php echo $stat['change'] > 0 ? '+' : ''; ?><?php echo esc_html($stat['change']); ?>%
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Renderiza uma imagem
     */
    protected function render_image() {
        if (!$this->image) {
            return;
        }
        ?>
        <div class="nosfir-image-control">
            <?php if ($this->label) : ?>
                <h4 class="nosfir-image-title"><?php echo esc_html($this->label); ?></h4>
            <?php endif; ?>
            
            <div class="nosfir-image-wrapper">
                <?php if ($this->url) : ?>
                    <a href="<?php echo esc_url($this->url); ?>" target="<?php echo esc_attr($this->target); ?>">
                <?php endif; ?>
                
                <img src="<?php echo esc_url($this->image); ?>" 
                     alt="<?php echo esc_attr($this->label); ?>"
                     class="nosfir-image">
                
                <?php if ($this->url) : ?>
                    </a>
                <?php endif; ?>
            </div>
            
            <?php if ($this->description) : ?>
                <p class="description"><?php echo wp_kses_post($this->description); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Renderiza um vídeo
     */
    protected function render_video() {
        if (!$this->video) {
            return;
        }
        ?>
        <div class="nosfir-video-control">
            <?php if ($this->label) : ?>
                <h4 class="nosfir-video-title"><?php echo esc_html($this->label); ?></h4>
            <?php endif; ?>
            
            <div class="nosfir-video-wrapper">
                <?php
                // Verifica se é um vídeo do YouTube ou Vimeo
                if (strpos($this->video, 'youtube.com') !== false || strpos($this->video, 'youtu.be') !== false) {
                    // YouTube
                    $video_id = '';
                    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $this->video, $match)) {
                        $video_id = $match[1];
                    }
                    if ($video_id) {
                        echo '<iframe src="https://www.youtube.com/embed/' . esc_attr($video_id) . '" frameborder="0" allowfullscreen></iframe>';
                    }
                } elseif (strpos($this->video, 'vimeo.com') !== false) {
                    // Vimeo
                    $video_id = '';
                    if (preg_match('/vimeo\.com\/(\d+)/i', $this->video, $match)) {
                        $video_id = $match[1];
                    }
                    if ($video_id) {
                        echo '<iframe src="https://player.vimeo.com/video/' . esc_attr($video_id) . '" frameborder="0" allowfullscreen></iframe>';
                    }
                } else {
                    // Vídeo local ou outro
                    echo '<video controls><source src="' . esc_url($this->video) . '"></video>';
                }
                ?>
            </div>
            
            <?php if ($this->description) : ?>
                <p class="description"><?php echo wp_kses_post($this->description); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Renderiza código
     */
    protected function render_code() {
        if (!$this->code) {
            return;
        }
        ?>
        <div class="nosfir-code-control">
            <?php if ($this->label) : ?>
                <h4 class="nosfir-code-title"><?php echo esc_html($this->label); ?></h4>
            <?php endif; ?>
            
            <?php if ($this->description) : ?>
                <p class="description"><?php echo wp_kses_post($this->description); ?></p>
            <?php endif; ?>
            
            <pre class="nosfir-code language-<?php echo esc_attr($this->code_language); ?>"><code><?php echo esc_html($this->code); ?></code></pre>
            
            <?php if (!empty($this->data['copy_button'])) : ?>
                <button type="button" class="button nosfir-copy-code" data-code="<?php echo esc_attr($this->code); ?>">
                    <?php esc_html_e('Copy Code', 'nosfir'); ?>
                </button>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Renderiza tabs
     */
    protected function render_tabs() {
        if (empty($this->data['tabs'])) {
            return;
        }
        
        $tab_id = 'nosfir-tabs-' . uniqid();
        ?>
        <div class="nosfir-tabs-control" id="<?php echo esc_attr($tab_id); ?>">
            <?php if ($this->label) : ?>
                <h4 class="nosfir-tabs-title"><?php echo esc_html($this->label); ?></h4>
            <?php endif; ?>
            
            <div class="nosfir-tabs-nav">
                <?php foreach ($this->data['tabs'] as $index => $tab) : ?>
                    <button type="button" 
                            class="nosfir-tab-button <?php echo $index === 0 ? 'active' : ''; ?>"
                            data-tab="<?php echo esc_attr($tab_id . '-' . $index); ?>">
                        <?php if (isset($tab['icon'])) : ?>
                            <span class="dashicons dashicons-<?php echo esc_attr($tab['icon']); ?>"></span>
                        <?php endif; ?>
                        <?php echo esc_html($tab['title']); ?>
                    </button>
                <?php endforeach; ?>
            </div>
            
            <div class="nosfir-tabs-content">
                <?php foreach ($this->data['tabs'] as $index => $tab) : ?>
                    <div class="nosfir-tab-pane <?php echo $index === 0 ? 'active' : ''; ?>" 
                         id="<?php echo esc_attr($tab_id . '-' . $index); ?>">
                        <?php echo wp_kses_post($tab['content']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Renderiza accordion
     */
    protected function render_accordion() {
        if (empty($this->data['panels'])) {
            return;
        }
        
        $accordion_id = 'nosfir-accordion-' . uniqid();
        ?>
        <div class="nosfir-accordion-control" id="<?php echo esc_attr($accordion_id); ?>">
            <?php if ($this->label) : ?>
                <h4 class="nosfir-accordion-title"><?php echo esc_html($this->label); ?></h4>
            <?php endif; ?>
            
            <?php foreach ($this->data['panels'] as $index => $panel) : ?>
                <div class="nosfir-accordion-item">
                    <button type="button" 
                            class="nosfir-accordion-header <?php echo $index === 0 && !$this->collapsed ? 'active' : ''; ?>"
                            data-target="<?php echo esc_attr($accordion_id . '-panel-' . $index); ?>">
                        <?php if (isset($panel['icon'])) : ?>
                            <span class="dashicons dashicons-<?php echo esc_attr($panel['icon']); ?>"></span>
                        <?php endif; ?>
                        <span><?php echo esc_html($panel['title']); ?></span>
                        <span class="nosfir-accordion-arrow dashicons dashicons-arrow-down"></span>
                    </button>
                    <div class="nosfir-accordion-panel <?php echo $index === 0 && !$this->collapsed ? 'active' : ''; ?>" 
                         id="<?php echo esc_attr($accordion_id . '-panel-' . $index); ?>">
                        <div class="nosfir-accordion-content">
                            <?php echo wp_kses_post($panel['content']); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Renderiza timeline
     */
    protected function render_timeline() {
        if (empty($this->data['events'])) {
            return;
        }
        ?>
        <div class="nosfir-timeline-control">
            <?php if ($this->label) : ?>
                <h4 class="nosfir-timeline-title"><?php echo esc_html($this->label); ?></h4>
            <?php endif; ?>
            
            <div class="nosfir-timeline">
                <?php foreach ($this->data['events'] as $event) : ?>
                    <div class="nosfir-timeline-item">
                        <div class="nosfir-timeline-marker">
                            <?php if (isset($event['icon'])) : ?>
                                <span class="dashicons dashicons-<?php echo esc_attr($event['icon']); ?>"></span>
                            <?php endif; ?>
                        </div>
                        <div class="nosfir-timeline-content">
                            <?php if (isset($event['date'])) : ?>
                                <div class="nosfir-timeline-date"><?php echo esc_html($event['date']); ?></div>
                            <?php endif; ?>
                            <?php if (isset($event['title'])) : ?>
                                <h5 class="nosfir-timeline-event-title"><?php echo esc_html($event['title']); ?></h5>
                            <?php endif; ?>
                            <?php if (isset($event['description'])) : ?>
                                <p class="nosfir-timeline-description"><?php echo wp_kses_post($event['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Renderiza pricing table
     */
    protected function render_pricing() {
        if (empty($this->data['plans'])) {
            return;
        }
        ?>
        <div class="nosfir-pricing-control">
            <?php if ($this->label) : ?>
                <h4 class="nosfir-pricing-title"><?php echo esc_html($this->label); ?></h4>
            <?php endif; ?>
            
            <div class="nosfir-pricing-table">
                <?php foreach ($this->data['plans'] as $plan) : ?>
                    <div class="nosfir-pricing-plan <?php echo isset($plan['featured']) && $plan['featured'] ? 'featured' : ''; ?>">
                        <?php if (isset($plan['badge'])) : ?>
                            <span class="nosfir-pricing-badge"><?php echo esc_html($plan['badge']); ?></span>
                        <?php endif; ?>
                        
                        <h5 class="nosfir-plan-name"><?php echo esc_html($plan['name']); ?></h5>
                        
                        <div class="nosfir-plan-price">
                            <?php if (isset($plan['currency'])) : ?>
                                <span class="nosfir-currency"><?php echo esc_html($plan['currency']); ?></span>
                            <?php endif; ?>
                            <span class="nosfir-amount"><?php echo esc_html($plan['price']); ?></span>
                            <?php if (isset($plan['period'])) : ?>
                                <span class="nosfir-period"><?php echo esc_html($plan['period']); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (isset($plan['features']) && is_array($plan['features'])) : ?>
                            <ul class="nosfir-plan-features">
                                <?php foreach ($plan['features'] as $feature) : ?>
                                    <li><?php echo esc_html($feature); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        
                        <?php if (isset($plan['button_text']) && isset($plan['button_url'])) : ?>
                            <a href="<?php echo esc_url($plan['button_url']); ?>" 
                               class="button <?php echo isset($plan['featured']) && $plan['featured'] ? 'button-primary' : ''; ?>"
                               target="_blank">
                                <?php echo esc_html($plan['button_text']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Renderiza testimonial
     */
    protected function render_testimonial() {
        ?>
        <div class="nosfir-testimonial-control">
            <blockquote class="nosfir-testimonial">
                <?php if ($this->description) : ?>
                    <p class="nosfir-testimonial-text"><?php echo wp_kses_post($this->description); ?></p>
                <?php endif; ?>
                
                <?php if ($this->label || $this->image || isset($this->data['author_title'])) : ?>
                    <footer class="nosfir-testimonial-footer">
                        <?php if ($this->image) : ?>
                            <img src="<?php echo esc_url($this->image); ?>" 
                                 alt="<?php echo esc_attr($this->label); ?>" 
                                 class="nosfir-testimonial-avatar">
                        <?php endif; ?>
                        
                        <div class="nosfir-testimonial-author">
                            <?php if ($this->label) : ?>
                                <cite class="nosfir-author-name"><?php echo esc_html($this->label); ?></cite>
                            <?php endif; ?>
                            <?php if (isset($this->data['author_title'])) : ?>
                                <span class="nosfir-author-title"><?php echo esc_html($this->data['author_title']); ?></span>
                            <?php endif; ?>
                        </div>
                    </footer>
                <?php endif; ?>
            </blockquote>
        </div>
        <?php
    }

    /**
     * Renderiza links sociais
     */
    protected function render_social() {
        if (empty($this->data['social_links'])) {
            return;
        }
        ?>
        <div class="nosfir-social-control">
            <?php if ($this->label) : ?>
                <h4 class="nosfir-social-title"><?php echo esc_html($this->label); ?></h4>
            <?php endif; ?>
            
            <?php if ($this->description) : ?>
                <p class="description"><?php echo wp_kses_post($this->description); ?></p>
            <?php endif; ?>
            
            <div class="nosfir-social-links">
                <?php foreach ($this->data['social_links'] as $network => $url) : ?>
                    <a href="<?php echo esc_url($url); ?>" 
                       class="nosfir-social-link nosfir-social-<?php echo esc_attr($network); ?>" 
                       target="_blank"
                       aria-label="<?php echo esc_attr(ucfirst($network)); ?>">
                        <span class="dashicons dashicons-<?php echo esc_attr($this->get_social_icon($network)); ?>"></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Renderiza HTML customizado
     */
    protected function render_html() {
        if ($this->html) {
            echo wp_kses_post($this->html);
        }
    }

    /**
     * Renderiza um espaçador
     */
    protected function render_spacer() {
        $height = isset($this->data['height']) ? $this->data['height'] : '20px';
        echo '<div class="nosfir-spacer" style="height: ' . esc_attr($height) . ';"></div>';
    }

    /**
     * Renderiza texto simples
     */
    protected function render_text() {
        if ($this->label) {
            echo '<h4 class="nosfir-text-title">' . esc_html($this->label) . '</h4>';
        }
        
        if ($this->description) {
            echo '<p class="description">' . wp_kses_post($this->description) . '</p>';
        }
    }

    /**
     * Obtém ícone da rede social
     */
    protected function get_social_icon($network) {
        $icons = array(
            'facebook' => 'facebook',
            'twitter' => 'twitter',
            'instagram' => 'instagram',
            'linkedin' => 'linkedin',
            'youtube' => 'youtube',
            'pinterest' => 'pinterest',
            'github' => 'github',
            'email' => 'email',
            'rss' => 'rss',
            'website' => 'admin-links'
        );
        
        return isset($icons[$network]) ? $icons[$network] : 'share';
    }

    /**
     * Refresh do controle via JS
     */
    public function to_json() {
        parent::to_json();
        
        $this->json['subtype'] = $this->subtype;
        $this->json['icon'] = $this->icon;
        $this->json['style'] = $this->style;
        $this->json['url'] = $this->url;
        $this->json['button_text'] = $this->button_text;
        $this->json['target'] = $this->target;
        $this->json['badge'] = $this->badge;
        $this->json['class'] = $this->class;
        $this->json['html'] = $this->html;
        $this->json['items'] = $this->items;
        $this->json['image'] = $this->image;
        $this->json['video'] = $this->video;
        $this->json['code'] = $this->code;
        $this->json['code_language'] = $this->code_language;
        $this->json['collapsible'] = $this->collapsible;
        $this->json['collapsed'] = $this->collapsed;
        $this->json['data'] = $this->data;
    }

    /**
     * Template JS
     */
    protected function content_template() {
        ?>
        <# if ( data.label ) { #>
            <span class="customize-control-title">{{ data.label }}</span>
        <# } #>
        <# if ( data.description ) { #>
            <span class="description customize-control-description">{{{ data.description }}}</span>
        <# } #>
        <?php
    }
}

// Registra o controle no Customizer
if (class_exists('WP_Customize_Control')) {
    add_action('customize_register', function($wp_customize) {
        // Registra o tipo de controle
        $wp_customize->register_control_type('Nosfir_Customizer_Control_Arbitrary');
    });
}