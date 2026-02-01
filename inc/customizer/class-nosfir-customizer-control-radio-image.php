<?php
/**
 * Nosfir Radio Image Customizer Control
 *
 * Cria um controle de seleção com imagens/ícones para o Customizer,
 * com suporte a layouts, patterns, ícones e muito mais.
 *
 * Incorpora conceitos do Kirki Framework e tutoriais de Otto Wood,
 * expandidos com funcionalidades modernas.
 *
 * @package  Nosfir
 * @since    1.0.0
 * @author   David Creator
 * @link     https://github.com/davidcreator/nosfir
 */

// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe de controle Radio Image para o Customizer
 */
class Nosfir_Customizer_Control_Radio_Image extends WP_Customize_Control {

    /**
     * Tipo do controle
     *
     * @var string
     */
    public $type = 'nosfir-radio-image';

    /**
     * Subtipo do controle (layout, pattern, icon, color-scheme, etc)
     *
     * @var string
     */
    public $subtype = 'default';

    /**
     * Layout das opções (grid, list, masonry, carousel)
     *
     * @var string
     */
    public $layout = 'grid';

    /**
     * Número de colunas no grid
     *
     * @var int
     */
    public $columns = 3;

    /**
     * Tamanho das imagens (small, medium, large, auto)
     *
     * @var string
     */
    public $image_size = 'medium';

    /**
     * Se deve mostrar labels
     *
     * @var bool
     */
    public $show_labels = true;

    /**
     * Se deve mostrar tooltips
     *
     * @var bool
     */
    public $show_tooltips = true;

    /**
     * Se deve mostrar preview ao hover
     *
     * @var bool
     */
    public $show_preview = false;

    /**
     * URL de preview
     *
     * @var string
     */
    public $preview_url = '';

    /**
     * Se permite múltipla seleção
     *
     * @var bool
     */
    public $multiselect = false;

    /**
     * Opções expandidas com mais informações
     *
     * @var array
     */
    public $choices_data = array();

    /**
     * Grupos de opções
     *
     * @var array
     */
    public $groups = array();

    /**
     * Se deve agrupar opções
     *
     * @var bool
     */
    public $grouped = false;

    /**
     * Filtros para as opções
     *
     * @var array
     */
    public $filters = array();

    /**
     * Se deve mostrar filtros
     *
     * @var bool
     */
    public $show_filters = false;

    /**
     * Se deve mostrar busca
     *
     * @var bool
     */
    public $show_search = false;

    /**
     * Placeholder da busca
     *
     * @var string
     */
    public $search_placeholder = '';

    /**
     * Se as imagens são lazy loaded
     *
     * @var bool
     */
    public $lazy_load = true;

    /**
     * Aspect ratio das imagens
     *
     * @var string
     */
    public $aspect_ratio = '16:9';

    /**
     * Se deve mostrar badges nas opções
     *
     * @var bool
     */
    public $show_badges = false;

    /**
     * Ícone padrão quando imagem não existe
     *
     * @var string
     */
    public $default_icon = 'dashicons-format-image';

    /**
     * Se deve permitir custom input
     *
     * @var bool
     */
    public $allow_custom = false;

    /**
     * Texto do botão custom
     *
     * @var string
     */
    public $custom_button_text = '';

    /**
     * Classes CSS adicionais
     *
     * @var string
     */
    public $class = '';

    /**
     * Se deve animar as transições
     *
     * @var bool
     */
    public $animate = true;

    /**
     * Construtor
     */
    public function __construct($manager, $id, $args = array()) {
        parent::__construct($manager, $id, $args);
        
        // Processa choices_data se fornecido
        $this->process_choices_data();
        
        // Define placeholder de busca padrão
        if (empty($this->search_placeholder)) {
            $this->search_placeholder = __('Search options...', 'nosfir');
        }
        
        // Define texto do botão custom padrão
        if (empty($this->custom_button_text)) {
            $this->custom_button_text = __('Custom Value', 'nosfir');
        }
    }

    /**
     * Processa dados expandidos das choices
     */
    protected function process_choices_data() {
        if (!empty($this->choices_data)) {
            foreach ($this->choices_data as $value => $data) {
                if (!isset($this->choices[$value]) && isset($data['image'])) {
                    $this->choices[$value] = $data['image'];
                }
            }
        }
    }

    /**
     * Enfileira scripts e estilos
     */
    public function enqueue() {
        // jQuery UI para interações avançadas
        wp_enqueue_script('jquery-ui-button');
        wp_enqueue_script('jquery-ui-sortable');
        
        // CSS do controle
        wp_enqueue_style(
            'nosfir-radio-image-control',
            get_template_directory_uri() . '/assets/css/customizer/radio-image-control.css',
            array(),
            wp_get_theme()->get('Version')
        );

        // JavaScript do controle
        wp_enqueue_script(
            'nosfir-radio-image-control',
            get_template_directory_uri() . '/assets/js/customizer/radio-image-control.js',
            array('jquery', 'jquery-ui-button', 'customize-base'),
            wp_get_theme()->get('Version'),
            true
        );

        // Adiciona CSS inline para personalização
        $custom_css = $this->generate_custom_css();
        if ($custom_css) {
            wp_add_inline_style('nosfir-radio-image-control', $custom_css);
        }

        // Localização
        wp_localize_script('nosfir-radio-image-control', 'nosfir_radio_image', array(
            'lazy_load' => $this->lazy_load,
            'animate' => $this->animate,
            'preview_url' => $this->preview_url,
            'strings' => array(
                'select' => __('Select', 'nosfir'),
                'selected' => __('Selected', 'nosfir'),
                'preview' => __('Preview', 'nosfir'),
                'loading' => __('Loading...', 'nosfir'),
                'no_results' => __('No results found', 'nosfir'),
                'clear_selection' => __('Clear Selection', 'nosfir')
            )
        ));
    }

    /**
     * Renderiza o conteúdo do controle
     */
    public function render_content() {
        if (empty($this->choices)) {
            $this->render_empty_state();
            return;
        }

        $input_id = '_customize-input-' . $this->id;
        $name = '_customize-radio-' . $this->id;
        
        // Classes do wrapper
        $wrapper_classes = array(
            'nosfir-radio-image-control',
            'nosfir-layout-' . esc_attr($this->layout),
            'nosfir-cols-' . esc_attr($this->columns),
            'nosfir-size-' . esc_attr($this->image_size),
            'nosfir-subtype-' . esc_attr($this->subtype)
        );
        
        if ($this->multiselect) {
            $wrapper_classes[] = 'nosfir-multiselect';
        }
        
        if ($this->animate) {
            $wrapper_classes[] = 'nosfir-animated';
        }
        
        if ($this->class) {
            $wrapper_classes[] = esc_attr($this->class);
        }
        ?>
        
        <div class="<?php echo esc_attr(implode(' ', $wrapper_classes)); ?>" 
             data-control-id="<?php echo esc_attr($this->id); ?>">
            
            <?php $this->render_header(); ?>
            
            <?php if ($this->show_search || $this->show_filters) : ?>
                <div class="nosfir-radio-image-toolbar">
                    <?php $this->render_search(); ?>
                    <?php $this->render_filters(); ?>
                </div>
            <?php endif; ?>
            
            <div class="nosfir-radio-image-container" id="input_<?php echo esc_attr($this->id); ?>">
                <?php if ($this->layout === 'carousel') : ?>
                    <button type="button" class="nosfir-carousel-prev" aria-label="<?php esc_attr_e('Previous', 'nosfir'); ?>">
                        <span class="dashicons dashicons-arrow-left-alt2"></span>
                    </button>
                <?php endif; ?>
                
                <div class="nosfir-radio-image-options <?php echo $this->layout === 'masonry' ? 'nosfir-masonry' : ''; ?>">
                    <?php
                    if ($this->grouped && !empty($this->groups)) {
                        $this->render_grouped_options($name);
                    } else {
                        $this->render_options($name);
                    }
                    ?>
                </div>
                
                <?php if ($this->layout === 'carousel') : ?>
                    <button type="button" class="nosfir-carousel-next" aria-label="<?php esc_attr_e('Next', 'nosfir'); ?>">
                        <span class="dashicons dashicons-arrow-right-alt2"></span>
                    </button>
                <?php endif; ?>
            </div>
            
            <?php if ($this->show_preview) : ?>
                <div class="nosfir-radio-image-preview" style="display: none;">
                    <div class="nosfir-preview-container">
                        <iframe src="" frameborder="0"></iframe>
                        <button type="button" class="nosfir-preview-close">
                            <span class="dashicons dashicons-no"></span>
                        </button>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($this->allow_custom) : ?>
                <div class="nosfir-radio-image-custom">
                    <button type="button" class="button nosfir-custom-value-btn">
                        <span class="dashicons dashicons-edit"></span>
                        <?php echo esc_html($this->custom_button_text); ?>
                    </button>
                    <div class="nosfir-custom-value-input" style="display: none;">
                        <input type="text" 
                               class="nosfir-custom-input" 
                               placeholder="<?php esc_attr_e('Enter custom value...', 'nosfir'); ?>">
                        <button type="button" class="button button-primary nosfir-custom-save">
                            <?php esc_html_e('Save', 'nosfir'); ?>
                        </button>
                        <button type="button" class="button nosfir-custom-cancel">
                            <?php esc_html_e('Cancel', 'nosfir'); ?>
                        </button>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($this->multiselect) : ?>
                <input type="hidden" 
                       id="<?php echo esc_attr($input_id); ?>" 
                       value="<?php echo esc_attr($this->value()); ?>" 
                       <?php $this->link(); ?> />
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Renderiza o cabeçalho do controle
     */
    protected function render_header() {
        ?>
        <div class="nosfir-radio-image-header">
            <?php if (!empty($this->label)) : ?>
                <span class="customize-control-title">
                    <?php echo esc_html($this->label); ?>
                    
                    <?php if ($this->multiselect) : ?>
                        <span class="nosfir-multiselect-count">
                            <span class="count">0</span> <?php esc_html_e('selected', 'nosfir'); ?>
                        </span>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
            
            <?php if (!empty($this->description)) : ?>
                <span class="description customize-control-description">
                    <?php echo wp_kses_post($this->description); ?>
                </span>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Renderiza campo de busca
     */
    protected function render_search() {
        if (!$this->show_search) {
            return;
        }
        ?>
        <div class="nosfir-radio-image-search">
            <input type="search" 
                   class="nosfir-search-input" 
                   placeholder="<?php echo esc_attr($this->search_placeholder); ?>">
            <span class="dashicons dashicons-search"></span>
        </div>
        <?php
    }

    /**
     * Renderiza filtros
     */
    protected function render_filters() {
        if (!$this->show_filters || empty($this->filters)) {
            return;
        }
        ?>
        <div class="nosfir-radio-image-filters">
            <button type="button" class="nosfir-filter-btn active" data-filter="all">
                <?php esc_html_e('All', 'nosfir'); ?>
            </button>
            <?php foreach ($this->filters as $filter_value => $filter_label) : ?>
                <button type="button" 
                        class="nosfir-filter-btn" 
                        data-filter="<?php echo esc_attr($filter_value); ?>">
                    <?php echo esc_html($filter_label); ?>
                </button>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Renderiza opções agrupadas
     */
    protected function render_grouped_options($name) {
        foreach ($this->groups as $group_id => $group_label) {
            ?>
            <div class="nosfir-radio-image-group" data-group="<?php echo esc_attr($group_id); ?>">
                <h4 class="nosfir-group-title"><?php echo esc_html($group_label); ?></h4>
                <div class="nosfir-group-options">
                    <?php
                    foreach ($this->choices as $value => $image) {
                        if ($this->get_option_group($value) === $group_id) {
                            $this->render_option($value, $image, $name);
                        }
                    }
                    ?>
                </div>
            </div>
            <?php
        }
        
        // Renderiza opções sem grupo
        $ungrouped = array();
        foreach ($this->choices as $value => $image) {
            if (!$this->get_option_group($value)) {
                $ungrouped[$value] = $image;
            }
        }
        
        if (!empty($ungrouped)) {
            ?>
            <div class="nosfir-radio-image-group" data-group="ungrouped">
                <?php if (!empty($this->groups)) : ?>
                    <h4 class="nosfir-group-title"><?php esc_html_e('Other', 'nosfir'); ?></h4>
                <?php endif; ?>
                <div class="nosfir-group-options">
                    <?php
                    foreach ($ungrouped as $value => $image) {
                        $this->render_option($value, $image, $name);
                    }
                    ?>
                </div>
            </div>
            <?php
        }
    }

    /**
     * Renderiza opções normais
     */
    protected function render_options($name) {
        foreach ($this->choices as $value => $image) {
            $this->render_option($value, $image, $name);
        }
    }

    /**
     * Renderiza uma opção individual
     */
    protected function render_option($value, $image, $name) {
        $option_data = isset($this->choices_data[$value]) ? $this->choices_data[$value] : array();
        $option_id = $this->id . '-' . sanitize_key($value);
        
        // Classes da opção
        $option_classes = array('nosfir-radio-image-option');
        
        if (isset($option_data['class'])) {
            $option_classes[] = esc_attr($option_data['class']);
        }
        
        if (isset($option_data['filter'])) {
            $option_classes[] = 'filter-' . esc_attr($option_data['filter']);
        }
        
        // Atributos de dados
        $data_attrs = array(
            'value' => esc_attr($value)
        );
        
        if (isset($option_data['filter'])) {
            $data_attrs['filter'] = esc_attr($option_data['filter']);
        }
        
        if (isset($option_data['group'])) {
            $data_attrs['group'] = esc_attr($option_data['group']);
        }
        
        if (isset($option_data['preview'])) {
            $data_attrs['preview'] = esc_url($option_data['preview']);
        }
        
        // Determina se está selecionado
        $is_checked = false;
        if ($this->multiselect) {
            $selected_values = explode(',', $this->value());
            $is_checked = in_array($value, $selected_values);
        } else {
            $is_checked = checked($this->value(), $value, false);
        }
        
        if ($is_checked) {
            $option_classes[] = 'selected';
        }
        ?>
        
        <div class="<?php echo esc_attr(implode(' ', $option_classes)); ?>" 
             <?php foreach ($data_attrs as $key => $val) : ?>
                 data-<?php echo esc_attr($key); ?>="<?php echo esc_attr($val); ?>"
             <?php endforeach; ?>>
            
            <?php if ($this->show_badges && isset($option_data['badge'])) : ?>
                <span class="nosfir-option-badge <?php echo isset($option_data['badge_type']) ? 'badge-' . esc_attr($option_data['badge_type']) : ''; ?>">
                    <?php echo esc_html($option_data['badge']); ?>
                </span>
            <?php endif; ?>
            
            <input type="<?php echo $this->multiselect ? 'checkbox' : 'radio'; ?>" 
                   class="nosfir-radio-image-input" 
                   id="<?php echo esc_attr($option_id); ?>" 
                   name="<?php echo esc_attr($name); ?>" 
                   value="<?php echo esc_attr($value); ?>"
                   <?php if (!$this->multiselect) : ?>
                       <?php $this->link(); ?>
                   <?php endif; ?>
                   <?php echo $is_checked; ?> />
            
            <label for="<?php echo esc_attr($option_id); ?>" 
                   class="nosfir-radio-image-label"
                   <?php if ($this->show_tooltips) : ?>
                       title="<?php echo esc_attr($this->get_option_tooltip($value, $option_data)); ?>"
                   <?php endif; ?>>
                
                <div class="nosfir-option-image-wrapper" 
                     style="<?php echo $this->get_aspect_ratio_style(); ?>">
                    <?php $this->render_option_image($value, $image, $option_data); ?>
                </div>
                
                <?php if ($this->show_labels) : ?>
                    <span class="nosfir-option-label">
                        <?php echo esc_html($this->get_option_label($value, $option_data)); ?>
                    </span>
                <?php endif; ?>
                
                <?php if (isset($option_data['description'])) : ?>
                    <span class="nosfir-option-description">
                        <?php echo wp_kses_post($option_data['description']); ?>
                    </span>
                <?php endif; ?>
                
                <?php if (isset($option_data['pro']) && $option_data['pro']) : ?>
                    <span class="nosfir-pro-badge"><?php esc_html_e('PRO', 'nosfir'); ?></span>
                <?php endif; ?>
                
                <span class="nosfir-option-check">
                    <span class="dashicons dashicons-yes"></span>
                </span>
            </label>
            
            <?php if ($this->show_preview && isset($option_data['preview'])) : ?>
                <button type="button" 
                        class="nosfir-option-preview-btn" 
                        data-preview="<?php echo esc_url($option_data['preview']); ?>"
                        aria-label="<?php esc_attr_e('Preview', 'nosfir'); ?>">
                    <span class="dashicons dashicons-visibility"></span>
                </button>
            <?php endif; ?>
            
            <?php if (isset($option_data['actions'])) : ?>
                <div class="nosfir-option-actions">
                    <?php foreach ($option_data['actions'] as $action) : ?>
                        <button type="button" 
                                class="nosfir-option-action" 
                                data-action="<?php echo esc_attr($action['action']); ?>"
                                title="<?php echo esc_attr($action['label']); ?>">
                            <span class="dashicons dashicons-<?php echo esc_attr($action['icon']); ?>"></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Renderiza imagem da opção
     */
    protected function render_option_image($value, $image, $option_data) {
        // Verifica se é um ícone ao invés de imagem
        if (strpos($image, 'dashicons-') === 0) {
            ?>
            <span class="nosfir-option-icon dashicons <?php echo esc_attr($image); ?>"></span>
            <?php
        } elseif (strpos($image, 'data:') === 0 || strpos($image, 'http') === 0) {
            // É uma URL de imagem
            if ($this->lazy_load) {
                ?>
                <img class="nosfir-option-img lazy" 
                     data-src="<?php echo esc_url($image); ?>" 
                     alt="<?php echo esc_attr($value); ?>"
                     src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 3 2'%3E%3C/svg%3E">
                <?php
            } else {
                ?>
                <img class="nosfir-option-img" 
                     src="<?php echo esc_url($image); ?>" 
                     alt="<?php echo esc_attr($value); ?>">
                <?php
            }
        } elseif (isset($option_data['html'])) {
            // HTML customizado
            echo wp_kses_post($option_data['html']);
        } elseif (isset($option_data['svg'])) {
            // SVG inline
            echo wp_kses($option_data['svg'], array(
                'svg' => array(
                    'class' => true,
                    'xmlns' => true,
                    'viewBox' => true,
                    'fill' => true,
                    'stroke' => true,
                    'stroke-width' => true,
                ),
                'path' => array(
                    'd' => true,
                    'fill' => true,
                    'stroke' => true,
                ),
                'rect' => array(
                    'x' => true,
                    'y' => true,
                    'width' => true,
                    'height' => true,
                    'fill' => true,
                ),
                'circle' => array(
                    'cx' => true,
                    'cy' => true,
                    'r' => true,
                    'fill' => true,
                ),
                'g' => array(
                    'transform' => true,
                ),
            ));
        } else {
            // Fallback para ícone padrão
            ?>
            <span class="nosfir-option-icon dashicons <?php echo esc_attr($this->default_icon); ?>"></span>
            <?php
        }
    }

    /**
     * Obtém label da opção
     */
    protected function get_option_label($value, $option_data) {
        if (isset($option_data['label'])) {
            return $option_data['label'];
        }
        
        // Converte value em label legível
        return ucwords(str_replace(array('-', '_'), ' ', $value));
    }

    /**
     * Obtém tooltip da opção
     */
    protected function get_option_tooltip($value, $option_data) {
        if (isset($option_data['tooltip'])) {
            return $option_data['tooltip'];
        }
        
        return $this->get_option_label($value, $option_data);
    }

    /**
     * Obtém grupo da opção
     */
    protected function get_option_group($value) {
        if (isset($this->choices_data[$value]['group'])) {
            return $this->choices_data[$value]['group'];
        }
        return null;
    }

    /**
     * Obtém estilo do aspect ratio
     */
    protected function get_aspect_ratio_style() {
        if ($this->aspect_ratio === 'auto') {
            return '';
        }
        
        $parts = explode(':', $this->aspect_ratio);
        if (count($parts) === 2) {
            $ratio = (intval($parts[1]) / intval($parts[0])) * 100;
            return 'padding-bottom: ' . $ratio . '%;';
        }
        
        return '';
    }

    /**
     * Renderiza estado vazio
     */
    protected function render_empty_state() {
        ?>
        <div class="nosfir-radio-image-empty">
            <span class="dashicons dashicons-images-alt2"></span>
            <p><?php esc_html_e('No options available', 'nosfir'); ?></p>
        </div>
        <?php
    }

    /**
     * Gera CSS customizado
     */
    protected function generate_custom_css() {
        $css = '';
        
        // CSS para número de colunas customizado
        if ($this->layout === 'grid' && $this->columns != 3) {
            $css .= '
                #customize-control-' . $this->id . ' .nosfir-radio-image-options {
                    grid-template-columns: repeat(' . $this->columns . ', 1fr);
                }
            ';
        }
        
        // CSS para tamanho das imagens
        $sizes = array(
            'small' => '80px',
            'medium' => '120px',
            'large' => '180px',
            'xlarge' => '240px'
        );
        
        if (isset($sizes[$this->image_size])) {
            $css .= '
                #customize-control-' . $this->id . ' .nosfir-option-image-wrapper {
                    max-width: ' . $sizes[$this->image_size] . ';
                }
            ';
        }
        
        return $css;
    }

    /**
     * Refresh do controle via JS
     */
    public function to_json() {
        parent::to_json();
        
        $this->json['subtype'] = $this->subtype;
        $this->json['layout'] = $this->layout;
        $this->json['columns'] = $this->columns;
        $this->json['imageSize'] = $this->image_size;
        $this->json['showLabels'] = $this->show_labels;
        $this->json['showTooltips'] = $this->show_tooltips;
        $this->json['showPreview'] = $this->show_preview;
        $this->json['previewUrl'] = $this->preview_url;
        $this->json['multiselect'] = $this->multiselect;
        $this->json['choicesData'] = $this->choices_data;
        $this->json['groups'] = $this->groups;
        $this->json['grouped'] = $this->grouped;
        $this->json['filters'] = $this->filters;
        $this->json['showFilters'] = $this->show_filters;
        $this->json['showSearch'] = $this->show_search;
        $this->json['searchPlaceholder'] = $this->search_placeholder;
        $this->json['lazyLoad'] = $this->lazy_load;
        $this->json['aspectRatio'] = $this->aspect_ratio;
        $this->json['showBadges'] = $this->show_badges;
        $this->json['defaultIcon'] = $this->default_icon;
        $this->json['allowCustom'] = $this->allow_custom;
        $this->json['customButtonText'] = $this->custom_button_text;
        $this->json['animate'] = $this->animate;
    }

    /**
     * Template JS underscore
     */
    protected function content_template() {
        ?>
        <#
        var inputId = 'input_' + data.id;
        var name = '_customize-radio-' + data.id;
        
        var wrapperClasses = [
            'nosfir-radio-image-control',
            'nosfir-layout-' + data.layout,
            'nosfir-cols-' + data.columns,
            'nosfir-size-' + data.imageSize,
            'nosfir-subtype-' + data.subtype
        ];
        
        if (data.multiselect) {
            wrapperClasses.push('nosfir-multiselect');
        }
        
        if (data.animate) {
            wrapperClasses.push('nosfir-animated');
        }
        #>
        
        <div class="{{ wrapperClasses.join(' ') }}" data-control-id="{{ data.id }}">
            <# if (data.label) { #>
                <span class="customize-control-title">
                    {{{ data.label }}}
                    <# if (data.multiselect) { #>
                        <span class="nosfir-multiselect-count">
                            <span class="count">0</span> selected
                        </span>
                    <# } #>
                </span>
            <# } #>
            
            <# if (data.description) { #>
                <span class="description customize-control-description">{{{ data.description }}}</span>
            <# } #>
            
            <# if (data.showSearch || data.showFilters) { #>
                <div class="nosfir-radio-image-toolbar">
                    <# if (data.showSearch) { #>
                        <div class="nosfir-radio-image-search">
                            <input type="search" class="nosfir-search-input" placeholder="{{ data.searchPlaceholder }}">
                            <span class="dashicons dashicons-search"></span>
                        </div>
                    <# } #>
                    
                    <# if (data.showFilters && data.filters) { #>
                        <div class="nosfir-radio-image-filters">
                            <button type="button" class="nosfir-filter-btn active" data-filter="all">All</button>
                            <# _.each(data.filters, function(label, value) { #>
                                <button type="button" class="nosfir-filter-btn" data-filter="{{ value }}">{{ label }}</button>
                            <# }); #>
                        </div>
                    <# } #>
                </div>
            <# } #>
            
            <div class="nosfir-radio-image-container" id="{{ inputId }}">
                <div class="nosfir-radio-image-options">
                    <# _.each(data.choices, function(image, value) { 
                        var optionData = data.choicesData[value] || {};
                        var optionId = data.id + '-' + value;
                        var isChecked = data.multiselect ? 
                            (data.value.split(',').indexOf(value) !== -1) : 
                            (data.value === value);
                    #>
                        <div class="nosfir-radio-image-option {{ isChecked ? 'selected' : '' }}" data-value="{{ value }}">
                            <input type="{{ data.multiselect ? 'checkbox' : 'radio' }}"
                                   class="nosfir-radio-image-input"
                                   id="{{ optionId }}"
                                   name="{{ name }}"
                                   value="{{ value }}"
                                   <# if (isChecked) { #>checked<# } #> />
                            
                            <label for="{{ optionId }}" class="nosfir-radio-image-label">
                                <div class="nosfir-option-image-wrapper">
                                    <# if (image.indexOf('dashicons-') === 0) { #>
                                        <span class="nosfir-option-icon dashicons {{ image }}"></span>
                                    <# } else { #>
                                        <img class="nosfir-option-img" src="{{ image }}" alt="{{ value }}">
                                    <# } #>
                                </div>
                                
                                <# if (data.showLabels) { #>
                                    <span class="nosfir-option-label">{{ optionData.label || value }}</span>
                                <# } #>
                                
                                <span class="nosfir-option-check">
                                    <span class="dashicons dashicons-yes"></span>
                                </span>
                            </label>
                        </div>
                    <# }); #>
                </div>
            </div>
        </div>
        <?php
    }
}

// Registra o controle no Customizer
if (class_exists('WP_Customize_Control')) {
    add_action('customize_register', function($wp_customize) {
        // Registra o tipo de controle
        $wp_customize->register_control_type('Nosfir_Customizer_Control_Radio_Image');
    });
}