<?php
/**
 * Nosfir WooCommerce Adjacent Products Class
 *
 * Gerencia navegação entre produtos adjacentes (anterior/próximo) no WooCommerce,
 * com suporte a navegação circular, filtros por categoria, e navegação inteligente.
 *
 * @package  Nosfir
 * @since    1.0.0
 * @author   David Creator
 */

// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Nosfir_WooCommerce_Adjacent_Products')) :

    /**
     * Classe de produtos adjacentes para WooCommerce
     */
    class Nosfir_WooCommerce_Adjacent_Products {

        /**
         * ID do produto atual
         *
         * @var int|null
         */
        private $current_product = null;

        /**
         * Se deve estar na mesma categoria/termo
         *
         * @var bool
         */
        private $in_same_term = false;

        /**
         * Termos excluídos
         *
         * @var array|string
         */
        private $excluded_terms = '';

        /**
         * Taxonomia para navegação
         *
         * @var string
         */
        private $taxonomy = 'product_cat';

        /**
         * Se é navegação para produto anterior
         *
         * @var bool
         */
        private $previous = false;

        /**
         * Configurações de navegação
         *
         * @var array
         */
        private $navigation_config = array();

        /**
         * Cache de produtos adjacentes
         *
         * @var array
         */
        private static $cache = array();

        /**
         * Filtros aplicados
         *
         * @var array
         */
        private $applied_filters = array();

        /**
         * Modo de navegação
         *
         * @var string
         */
        private $navigation_mode = 'default';

        /**
         * Produtos visitados na sessão
         *
         * @var array
         */
        private $visited_products = array();

        /**
         * Construtor
         *
         * @param bool         $in_same_term   Se deve estar na mesma categoria
         * @param array|string $excluded_terms Termos excluídos
         * @param string       $taxonomy       Taxonomia para filtrar
         * @param bool         $previous       Se é navegação anterior
         * @param array        $config         Configurações adicionais
         */
        public function __construct($in_same_term = false, $excluded_terms = '', $taxonomy = 'product_cat', $previous = false, $config = array()) {
            $this->in_same_term = $in_same_term;
            $this->excluded_terms = $excluded_terms;
            $this->taxonomy = $taxonomy;
            $this->previous = $previous;

            // Configurações padrão
            $default_config = array(
                'circular' => true,                    // Navegação circular
                'skip_out_of_stock' => true,          // Pular produtos fora de estoque
                'skip_hidden' => true,                 // Pular produtos ocultos
                'respect_catalog_visibility' => true,  // Respeitar visibilidade do catálogo
                'include_variations' => false,         // Incluir variações
                'orderby' => 'date',                  // Ordenação (date, title, menu_order, rand)
                'order' => 'DESC',                     // Ordem (ASC, DESC)
                'meta_key' => '',                      // Meta key para ordenação
                'meta_value' => '',                    // Meta value para filtrar
                'min_price' => '',                     // Preço mínimo
                'max_price' => '',                     // Preço máximo
                'featured_only' => false,              // Apenas produtos em destaque
                'on_sale_only' => false,              // Apenas produtos em promoção
                'specific_categories' => array(),      // Categorias específicas
                'exclude_categories' => array(),       // Excluir categorias
                'specific_tags' => array(),            // Tags específicas
                'exclude_tags' => array(),            // Excluir tags
                'limit_to_stock' => false,            // Limitar ao estoque disponível
                'custom_query_args' => array(),       // Args customizados para query
                'cache_enabled' => true,              // Habilitar cache
                'cache_expiration' => HOUR_IN_SECONDS, // Tempo de expiração do cache
                'navigation_mode' => 'default',       // Modo: default, smart, related, history
            );

            $this->navigation_config = wp_parse_args($config, $default_config);
            $this->navigation_mode = $this->navigation_config['navigation_mode'];

            // Carrega produtos visitados da sessão
            $this->load_visited_products();
        }

        /**
         * Obtém o produto adjacente
         *
         * @return WC_Product|false Produto ou false se não encontrado
         */
        public function get_product() {
            global $post;

            if (!$post || !is_singular('product')) {
                return false;
            }

            $this->current_product = $post->ID;

            // Verifica cache primeiro
            $cache_key = $this->get_cache_key();
            if ($this->navigation_config['cache_enabled'] && isset(self::$cache[$cache_key])) {
                $cached_product = wc_get_product(self::$cache[$cache_key]);
                if ($cached_product && $this->is_valid_product($cached_product)) {
                    return $cached_product;
                }
            }

            // Escolhe estratégia baseada no modo de navegação
            $product = false;
            
            switch ($this->navigation_mode) {
                case 'smart':
                    $product = $this->get_smart_adjacent_product();
                    break;
                    
                case 'related':
                    $product = $this->get_related_adjacent_product();
                    break;
                    
                case 'history':
                    $product = $this->get_history_based_product();
                    break;
                    
                default:
                    $product = $this->get_standard_adjacent_product();
                    break;
            }

            // Se não encontrou e navegação circular está ativa
            if (!$product && $this->navigation_config['circular']) {
                $product = $this->get_circular_product();
            }

            // Armazena em cache se encontrou
            if ($product && $this->navigation_config['cache_enabled']) {
                self::$cache[$cache_key] = $product->get_id();
                set_transient($cache_key, $product->get_id(), $this->navigation_config['cache_expiration']);
            }

            // Registra produto visitado
            if ($product) {
                $this->track_visited_product($product->get_id());
            }

            return $product;
        }

        /**
         * Obtém produto adjacente padrão
         *
         * @return WC_Product|false
         */
        private function get_standard_adjacent_product() {
            $product = false;
            $original_id = $this->current_product;

            // Tenta obter via get_adjacent_post do WordPress
            while ($adjacent = $this->get_adjacent_post()) {
                $product = wc_get_product($adjacent->ID);

                if ($this->is_valid_product($product)) {
                    break;
                }

                $product = false;
                $this->current_product = $adjacent->ID;
            }

            // Restaura ID original
            $this->current_product = $original_id;

            if (!$product) {
                // Tenta query direta do WooCommerce
                $product = $this->query_adjacent_product();
            }

            return $product;
        }

        /**
         * Obtém produto adjacente inteligente
         *
         * @return WC_Product|false
         */
        private function get_smart_adjacent_product() {
            global $post;

            // Analisa comportamento do usuário
            $user_preferences = $this->analyze_user_preferences();
            
            // Obtém produtos candidatos
            $candidates = $this->get_smart_candidates($user_preferences);
            
            if (!empty($candidates)) {
                // Pontua candidatos
                $scored_candidates = $this->score_candidates($candidates, $user_preferences);
                
                // Ordena por pontuação
                arsort($scored_candidates);
                
                // Retorna o melhor candidato
                $best_id = key($scored_candidates);
                return wc_get_product($best_id);
            }

            // Fallback para navegação padrão
            return $this->get_standard_adjacent_product();
        }

        /**
         * Obtém produto relacionado adjacente
         *
         * @return WC_Product|false
         */
        private function get_related_adjacent_product() {
            global $post;

            $current_product = wc_get_product($post->ID);
            if (!$current_product) {
                return false;
            }

            // Obtém produtos relacionados
            $related_ids = wc_get_related_products($post->ID, 20);
            
            if (empty($related_ids)) {
                return $this->get_standard_adjacent_product();
            }

            // Filtra produtos válidos
            $valid_products = array();
            foreach ($related_ids as $product_id) {
                $product = wc_get_product($product_id);
                if ($this->is_valid_product($product)) {
                    $valid_products[] = $product;
                }
            }

            if (empty($valid_products)) {
                return false;
            }

            // Ordena produtos relacionados
            usort($valid_products, array($this, 'sort_products'));

            // Encontra posição do produto atual na lista ordenada
            $current_index = -1;
            foreach ($valid_products as $index => $product) {
                if ($product->get_id() == $post->ID) {
                    $current_index = $index;
                    break;
                }
            }

            // Determina índice do próximo/anterior
            if ($this->previous) {
                $target_index = ($current_index > 0) ? $current_index - 1 : count($valid_products) - 1;
            } else {
                $target_index = ($current_index < count($valid_products) - 1) ? $current_index + 1 : 0;
            }

            return $valid_products[$target_index];
        }

        /**
         * Obtém produto baseado no histórico
         *
         * @return WC_Product|false
         */
        private function get_history_based_product() {
            if (empty($this->visited_products)) {
                return $this->get_standard_adjacent_product();
            }

            $current_index = array_search($this->current_product, $this->visited_products);
            
            if ($current_index === false) {
                return $this->get_standard_adjacent_product();
            }

            // Navega pelo histórico
            if ($this->previous && $current_index > 0) {
                $target_id = $this->visited_products[$current_index - 1];
            } elseif (!$this->previous && $current_index < count($this->visited_products) - 1) {
                $target_id = $this->visited_products[$current_index + 1];
            } else {
                return $this->get_standard_adjacent_product();
            }

            $product = wc_get_product($target_id);
            
            if ($this->is_valid_product($product)) {
                return $product;
            }

            return $this->get_standard_adjacent_product();
        }

        /**
         * Obtém post adjacente
         *
         * @return WP_Post|false
         */
        private function get_adjacent_post() {
            global $post;

            $original_post = $post;
            $post = get_post($this->current_product);

            $direction = $this->previous ? 'previous' : 'next';

            // Adiciona filtros
            add_filter('get_' . $direction . '_post_where', array($this, 'filter_post_where'), 10, 5);
            add_filter('get_' . $direction . '_post_join', array($this, 'filter_post_join'), 10, 5);
            add_filter('get_' . $direction . '_post_sort', array($this, 'filter_post_sort'), 10, 2);

            // Obtém post adjacente
            $adjacent = get_adjacent_post(
                $this->in_same_term,
                $this->excluded_terms,
                $this->previous,
                $this->taxonomy
            );

            // Remove filtros
            remove_filter('get_' . $direction . '_post_where', array($this, 'filter_post_where'), 10);
            remove_filter('get_' . $direction . '_post_join', array($this, 'filter_post_join'), 10);
            remove_filter('get_' . $direction . '_post_sort', array($this, 'filter_post_sort'), 10);

            $post = $original_post;

            return $adjacent;
        }

        /**
         * Filtra WHERE clause
         *
         * @param string $where
         * @param bool   $in_same_term
         * @param array  $excluded_terms
         * @param string $taxonomy
         * @param WP_Post $post
         * @return string
         */
        public function filter_post_where($where, $in_same_term = false, $excluded_terms = '', $taxonomy = 'category', $post = null) {
            global $wpdb;

            // Substitui data do post atual
            if ($post && $this->current_product) {
                $current = get_post($this->current_product);
                if ($current) {
                    $where = str_replace($post->post_date, $current->post_date, $where);
                    
                    // Substitui ID também para evitar duplicatas
                    if (strpos($where, 'p.ID') !== false) {
                        $op = $this->previous ? '<' : '>';
                        $where = str_replace(
                            "p.ID {$op} {$post->ID}",
                            "p.ID {$op} {$current->ID}",
                            $where
                        );
                    }
                }
            }

            // Adiciona filtros customizados
            if ($this->navigation_config['skip_out_of_stock']) {
                $where .= " AND p.ID IN (
                    SELECT post_id FROM {$wpdb->postmeta} 
                    WHERE meta_key = '_stock_status' 
                    AND meta_value = 'instock'
                )";
            }

            if ($this->navigation_config['featured_only']) {
                $where .= " AND p.ID IN (
                    SELECT post_id FROM {$wpdb->postmeta} 
                    WHERE meta_key = '_featured' 
                    AND meta_value = 'yes'
                )";
            }

            if ($this->navigation_config['on_sale_only']) {
                $where .= " AND p.ID IN (
                    SELECT post_id FROM {$wpdb->postmeta} 
                    WHERE meta_key = '_sale_price' 
                    AND meta_value != ''
                    AND meta_value > 0
                )";
            }

            // Filtro de preço
            if ($this->navigation_config['min_price']) {
                $where .= $wpdb->prepare(
                    " AND p.ID IN (
                        SELECT post_id FROM {$wpdb->postmeta} 
                        WHERE meta_key = '_price' 
                        AND CAST(meta_value AS DECIMAL) >= %f
                    )",
                    floatval($this->navigation_config['min_price'])
                );
            }

            if ($this->navigation_config['max_price']) {
                $where .= $wpdb->prepare(
                    " AND p.ID IN (
                        SELECT post_id FROM {$wpdb->postmeta} 
                        WHERE meta_key = '_price' 
                        AND CAST(meta_value AS DECIMAL) <= %f
                    )",
                    floatval($this->navigation_config['max_price'])
                );
            }

            return apply_filters('nosfir_adjacent_products_where', $where, $this);
        }

        /**
         * Filtra JOIN clause
         *
         * @param string $join
         * @param bool   $in_same_term
         * @param array  $excluded_terms
         * @param string $taxonomy
         * @param WP_Post $post
         * @return string
         */
        public function filter_post_join($join, $in_same_term = false, $excluded_terms = '', $taxonomy = 'category', $post = null) {
            global $wpdb;

            // Adiciona JOINs necessários para filtros customizados
            if ($this->navigation_config['meta_key']) {
                $join .= " LEFT JOIN {$wpdb->postmeta} AS mt ON p.ID = mt.post_id";
            }

            return apply_filters('nosfir_adjacent_products_join', $join, $this);
        }

        /**
         * Filtra ORDER BY clause
         *
         * @param string $sort
         * @param WP_Post $post
         * @return string
         */
        public function filter_post_sort($sort, $post = null) {
            // Customiza ordenação baseada na configuração
            switch ($this->navigation_config['orderby']) {
                case 'title':
                    $order = $this->previous ? 'DESC' : 'ASC';
                    $sort = "ORDER BY p.post_title {$order} LIMIT 1";
                    break;
                    
                case 'menu_order':
                    $order = $this->previous ? 'DESC' : 'ASC';
                    $sort = "ORDER BY p.menu_order {$order}, p.post_date DESC LIMIT 1";
                    break;
                    
                case 'price':
                    $order = $this->previous ? 'DESC' : 'ASC';
                    $sort = "ORDER BY CAST(mt.meta_value AS DECIMAL) {$order} LIMIT 1";
                    break;
                    
                case 'popularity':
                    $order = $this->previous ? 'DESC' : 'ASC';
                    $sort = "ORDER BY p.comment_count {$order} LIMIT 1";
                    break;
                    
                case 'rating':
                    // Ordenação por rating requer join adicional
                    break;
                    
                case 'rand':
                    $sort = "ORDER BY RAND() LIMIT 1";
                    break;
            }

            return apply_filters('nosfir_adjacent_products_sort', $sort, $this);
        }

        /**
         * Query direta para produto adjacente
         *
         * @return WC_Product|false
         */
        private function query_adjacent_product() {
            $args = array(
                'limit' => 5, // Busca alguns para garantir que encontra um válido
                'status' => 'publish',
                'type' => array('simple', 'variable', 'grouped', 'external'),
                'exclude' => array($this->current_product),
                'orderby' => $this->navigation_config['orderby'],
                'order' => $this->previous ? 'DESC' : 'ASC',
                'return' => 'objects',
            );

            // Visibilidade do catálogo
            if ($this->navigation_config['respect_catalog_visibility']) {
                $args['visibility'] = 'catalog';
            }

            // Filtro de estoque
            if ($this->navigation_config['skip_out_of_stock']) {
                $args['stock_status'] = 'instock';
            }

            // Filtro de categoria
            if ($this->in_same_term) {
                $terms = get_the_terms($this->current_product, $this->taxonomy);
                if ($terms && !is_wp_error($terms)) {
                    $args['category'] = wp_list_pluck($terms, 'slug');
                }
            }

            // Categorias específicas
            if (!empty($this->navigation_config['specific_categories'])) {
                $args['category'] = $this->navigation_config['specific_categories'];
            }

            // Excluir categorias
            if (!empty($this->navigation_config['exclude_categories'])) {
                $args['category_exclude'] = $this->navigation_config['exclude_categories'];
            }

            // Tags
            if (!empty($this->navigation_config['specific_tags'])) {
                $args['tag'] = $this->navigation_config['specific_tags'];
            }

            // Produtos em destaque
            if ($this->navigation_config['featured_only']) {
                $args['featured'] = true;
            }

            // Produtos em promoção
            if ($this->navigation_config['on_sale_only']) {
                $args['on_sale'] = true;
            }

            // Faixa de preço
            if ($this->navigation_config['min_price'] || $this->navigation_config['max_price']) {
                $args['meta_query'] = array();
                
                if ($this->navigation_config['min_price']) {
                    $args['meta_query'][] = array(
                        'key' => '_price',
                        'value' => $this->navigation_config['min_price'],
                        'compare' => '>=',
                        'type' => 'DECIMAL',
                    );
                }
                
                if ($this->navigation_config['max_price']) {
                    $args['meta_query'][] = array(
                        'key' => '_price',
                        'value' => $this->navigation_config['max_price'],
                        'compare' => '<=',
                        'type' => 'DECIMAL',
                    );
                }
            }

            // Args customizados
            if (!empty($this->navigation_config['custom_query_args'])) {
                $args = array_merge($args, $this->navigation_config['custom_query_args']);
            }

            // Aplica filtro
            $args = apply_filters('nosfir_adjacent_product_query_args', $args, $this);

            // Executa query
            $products = wc_get_products($args);

            // Retorna o primeiro produto válido
            foreach ($products as $product) {
                if ($this->is_valid_product($product)) {
                    return $product;
                }
            }

            return false;
        }

        /**
         * Obtém produto circular (volta ao início/fim)
         *
         * @return WC_Product|false
         */
        private function get_circular_product() {
            $args = array(
                'limit' => 1,
                'status' => 'publish',
                'visibility' => 'catalog',
                'exclude' => array($this->current_product),
                'orderby' => $this->navigation_config['orderby'],
                'order' => $this->previous ? 'DESC' : 'ASC',
            );

            // Se navegando para trás, pega o último produto
            if ($this->previous) {
                $args['order'] = 'DESC';
            } else {
                // Se navegando para frente, pega o primeiro produto
                $args['order'] = 'ASC';
            }

            // Mantém filtros de categoria se configurado
            if ($this->in_same_term) {
                $terms = get_the_terms($this->current_product, $this->taxonomy);
                if ($terms && !is_wp_error($terms)) {
                    $args['category'] = wp_list_pluck($terms, 'slug');
                }
            }

            $products = wc_get_products($args);

            if (!empty($products)) {
                return $products[0];
            }

            return false;
        }

        /**
         * Verifica se produto é válido para navegação
         *
         * @param mixed $product
         * @return bool
         */
        private function is_valid_product($product) {
            if (!$product || !is_a($product, 'WC_Product')) {
                return false;
            }

            // Verifica visibilidade
            if ($this->navigation_config['skip_hidden'] && !$product->is_visible()) {
                return false;
            }

            // Verifica estoque
            if ($this->navigation_config['skip_out_of_stock'] && !$product->is_in_stock()) {
                return false;
            }

            // Verifica visibilidade do catálogo
            if ($this->navigation_config['respect_catalog_visibility']) {
                $visibility = $product->get_catalog_visibility();
                if (in_array($visibility, array('hidden', 'search'))) {
                    return false;
                }
            }

            // Permite que outros plugins filtrem
            return apply_filters('nosfir_is_valid_adjacent_product', true, $product, $this);
        }

        /**
         * Analisa preferências do usuário
         *
         * @return array
         */
        private function analyze_user_preferences() {
            $preferences = array(
                'categories' => array(),
                'tags' => array(),
                'price_range' => array(),
                'attributes' => array(),
            );

            // Analisa produtos visitados
            if (!empty($this->visited_products)) {
                foreach ($this->visited_products as $product_id) {
                    $product = wc_get_product($product_id);
                    if (!$product) continue;

                    // Categorias
                    $cats = $product->get_category_ids();
                    foreach ($cats as $cat_id) {
                        if (!isset($preferences['categories'][$cat_id])) {
                            $preferences['categories'][$cat_id] = 0;
                        }
                        $preferences['categories'][$cat_id]++;
                    }

                    // Tags
                    $tags = $product->get_tag_ids();
                    foreach ($tags as $tag_id) {
                        if (!isset($preferences['tags'][$tag_id])) {
                            $preferences['tags'][$tag_id] = 0;
                        }
                        $preferences['tags'][$tag_id]++;
                    }

                    // Faixa de preço
                    $price = $product->get_price();
                    if ($price) {
                        $preferences['price_range'][] = $price;
                    }
                }
            }

            // Calcula médias e tendências
            if (!empty($preferences['price_range'])) {
                $preferences['avg_price'] = array_sum($preferences['price_range']) / count($preferences['price_range']);
                $preferences['min_price'] = min($preferences['price_range']);
                $preferences['max_price'] = max($preferences['price_range']);
            }

            return apply_filters('nosfir_user_preferences', $preferences, $this);
        }

        /**
         * Obtém candidatos inteligentes
         *
         * @param array $preferences
         * @return array
         */
        private function get_smart_candidates($preferences) {
            $args = array(
                'limit' => 20,
                'status' => 'publish',
                'visibility' => 'catalog',
                'exclude' => array_merge(array($this->current_product), $this->visited_products),
                'return' => 'ids',
            );

            // Filtra por categorias preferidas
            if (!empty($preferences['categories'])) {
                arsort($preferences['categories']);
                $top_categories = array_slice(array_keys($preferences['categories']), 0, 3);
                $args['category'] = $top_categories;
            }

            // Filtra por faixa de preço preferida
            if (isset($preferences['avg_price'])) {
                $margin = $preferences['avg_price'] * 0.3; // 30% de margem
                $args['meta_query'] = array(
                    array(
                        'key' => '_price',
                        'value' => array($preferences['avg_price'] - $margin, $preferences['avg_price'] + $margin),
                        'compare' => 'BETWEEN',
                        'type' => 'DECIMAL',
                    ),
                );
            }

            $products = wc_get_products($args);

            return apply_filters('nosfir_smart_candidates', $products, $preferences, $this);
        }

        /**
         * Pontua candidatos
         *
         * @param array $candidates
         * @param array $preferences
         * @return array
         */
        private function score_candidates($candidates, $preferences) {
            $scores = array();

            foreach ($candidates as $product_id) {
                $score = 0;
                $product = wc_get_product($product_id);
                
                if (!$product) continue;

                // Pontuação por categoria
                $cats = $product->get_category_ids();
                foreach ($cats as $cat_id) {
                    if (isset($preferences['categories'][$cat_id])) {
                        $score += $preferences['categories'][$cat_id] * 10;
                    }
                }

                // Pontuação por tags
                $tags = $product->get_tag_ids();
                foreach ($tags as $tag_id) {
                    if (isset($preferences['tags'][$tag_id])) {
                        $score += $preferences['tags'][$tag_id] * 5;
                    }
                }

                // Pontuação por preço
                if (isset($preferences['avg_price'])) {
                    $price_diff = abs($product->get_price() - $preferences['avg_price']);
                    $price_score = max(0, 100 - ($price_diff / $preferences['avg_price'] * 100));
                    $score += $price_score;
                }

                // Pontuação por popularidade
                $score += $product->get_review_count() * 2;
                $score += $product->get_average_rating() * 20;

                // Pontuação por vendas
                $total_sales = get_post_meta($product_id, 'total_sales', true);
                if ($total_sales) {
                    $score += min($total_sales, 100); // Cap em 100 pontos
                }

                $scores[$product_id] = $score;
            }

            return apply_filters('nosfir_candidate_scores', $scores, $candidates, $preferences, $this);
        }

        /**
         * Ordena produtos
         *
         * @param WC_Product $a
         * @param WC_Product $b
         * @return int
         */
        private function sort_products($a, $b) {
            switch ($this->navigation_config['orderby']) {
                case 'title':
                    return strcmp($a->get_title(), $b->get_title());
                    
                case 'price':
                    return $a->get_price() <=> $b->get_price();
                    
                case 'date':
                    return $a->get_date_created() <=> $b->get_date_created();
                    
                case 'popularity':
                    return get_post_meta($b->get_id(), 'total_sales', true) <=> get_post_meta($a->get_id(), 'total_sales', true);
                    
                case 'rating':
                    return $b->get_average_rating() <=> $a->get_average_rating();
                    
                default:
                    return 0;
            }
        }

        /**
         * Carrega produtos visitados
         */
        private function load_visited_products() {
            if (isset($_SESSION['nosfir_visited_products'])) {
                $this->visited_products = $_SESSION['nosfir_visited_products'];
            } elseif (isset($_COOKIE['nosfir_visited_products'])) {
                $this->visited_products = json_decode(stripslashes($_COOKIE['nosfir_visited_products']), true);
            }

            if (!is_array($this->visited_products)) {
                $this->visited_products = array();
            }

            // Limita a 50 produtos
            $this->visited_products = array_slice($this->visited_products, 0, 50);
        }

        /**
         * Rastreia produto visitado
         *
         * @param int $product_id
         */
        private function track_visited_product($product_id) {
            if (!in_array($product_id, $this->visited_products)) {
                array_unshift($this->visited_products, $product_id);
                $this->visited_products = array_slice($this->visited_products, 0, 50);

                // Salva na sessão
                if (session_status() === PHP_SESSION_ACTIVE) {
                    $_SESSION['nosfir_visited_products'] = $this->visited_products;
                }

                // Salva em cookie
                setcookie(
                    'nosfir_visited_products',
                    json_encode($this->visited_products),
                    time() + (30 * DAY_IN_SECONDS),
                    COOKIEPATH,
                    COOKIE_DOMAIN,
                    is_ssl(),
                    true
                );
            }
        }

        /**
         * Gera chave de cache
         *
         * @return string
         */
        private function get_cache_key() {
            $key_parts = array(
                'nosfir_adjacent',
                $this->current_product,
                $this->previous ? 'prev' : 'next',
                $this->in_same_term ? 'same' : 'all',
                $this->taxonomy,
                md5(serialize($this->navigation_config))
            );

            return implode('_', $key_parts);
        }

        /**
         * Limpa cache
         */
        public static function clear_cache() {
            self::$cache = array();
            
            // Limpa transients
            global $wpdb;
            $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_nosfir_adjacent_%'");
            $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_nosfir_adjacent_%'");
        }

        /**
         * Obtém link de navegação formatado
         *
         * @return string HTML do link
         */
        public function get_navigation_link() {
            $product = $this->get_product();
            
            if (!$product) {
                return '';
            }

            $classes = array(
                'nosfir-product-nav-link',
                $this->previous ? 'nav-previous' : 'nav-next'
            );

            $arrow = $this->previous ? '&larr;' : '&rarr;';
            $label = $this->previous ? __('Previous Product', 'nosfir') : __('Next Product', 'nosfir');

            $html = sprintf(
                '<a href="%s" class="%s" rel="%s">
                    <span class="nav-arrow">%s</span>
                    <span class="nav-label">%s</span>
                    <span class="nav-title">%s</span>
                    %s
                </a>',
                esc_url($product->get_permalink()),
                esc_attr(implode(' ', $classes)),
                $this->previous ? 'prev' : 'next',
                $arrow,
                esc_html($label),
                esc_html($product->get_title()),
                $product->get_image('thumbnail')
            );

            return apply_filters('nosfir_product_navigation_link', $html, $product, $this);
        }
    }

endif;