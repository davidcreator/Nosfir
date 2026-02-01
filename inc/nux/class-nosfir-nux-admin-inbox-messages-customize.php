<?php
/**
 * Nosfir NUX Admin Inbox Messages
 *
 * Gerencia mensagens e notificações no Admin Inbox do WooCommerce/WordPress,
 * incluindo onboarding, dicas, atualizações e promoções.
 *
 * @package  Nosfir
 * @since    1.0.0
 * @author   David Creator
 */

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;
use Automattic\WooCommerce\Admin\Notes\Notes;
use Automattic\WooCommerce\Admin\Notes\NotesUnavailableException;

// Evita acesso direto
defined('ABSPATH') || exit;

if (!class_exists('Nosfir_NUX_Admin_Inbox')) :

    /**
     * Classe principal de mensagens do Admin Inbox
     */
    class Nosfir_NUX_Admin_Inbox {

        use NoteTraits;

        /**
         * Instance única da classe
         *
         * @var Nosfir_NUX_Admin_Inbox
         */
        private static $instance = null;

        /**
         * Prefixo para notas
         *
         * @var string
         */
        const NOTE_PREFIX = 'nosfir-';

        /**
         * Versão do tema
         *
         * @var string
         */
        private $theme_version;

        /**
         * Lista de notas registradas
         *
         * @var array
         */
        private $registered_notes = array();

        /**
         * Configurações de timing para notas
         *
         * @var array
         */
        private $note_timings = array();

        /**
         * Retorna a instância única da classe
         *
         * @return Nosfir_NUX_Admin_Inbox
         */
        public static function get_instance() {
            if (null === self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Construtor
         */
        public function __construct() {
            // Verifica se WooCommerce Admin está disponível
            if (!$this->is_wc_admin_available()) {
                return;
            }

            $theme = wp_get_theme('nosfir');
            $this->theme_version = $theme->get('Version');

            $this->init();
            $this->register_hooks();
        }

        /**
         * Inicialização
         */
        private function init() {
            // Define timings das notas
            $this->note_timings = array(
                'welcome' => 0, // Imediato
                'customize' => DAY_IN_SECONDS, // 1 dia
                'plugins' => 3 * DAY_IN_SECONDS, // 3 dias
                'demo_import' => WEEK_IN_SECONDS, // 1 semana
                'review' => 2 * WEEK_IN_SECONDS, // 2 semanas
                'premium' => MONTH_IN_SECONDS, // 1 mês
                'support' => 0, // Quando necessário
                'update' => 0, // Quando houver atualização
            );

            // Registra notas disponíveis
            $this->registered_notes = array(
                'welcome' => 'Nosfir_NUX_Note_Welcome',
                'customize' => 'Nosfir_NUX_Note_Customize',
                'plugins' => 'Nosfir_NUX_Note_Plugins',
                'demo_import' => 'Nosfir_NUX_Note_Demo_Import',
                'review' => 'Nosfir_NUX_Note_Review',
                'premium' => 'Nosfir_NUX_Note_Premium',
                'support' => 'Nosfir_NUX_Note_Support',
                'update' => 'Nosfir_NUX_Note_Update',
                'performance' => 'Nosfir_NUX_Note_Performance',
                'seo' => 'Nosfir_NUX_Note_SEO',
            );

            // Permite filtrar notas registradas
            $this->registered_notes = apply_filters('nosfir_nux_registered_notes', $this->registered_notes);
        }

        /**
         * Registra hooks
         */
        private function register_hooks() {
            // Adiciona notas
            add_action('init', array($this, 'add_notes'));
            add_action('after_switch_theme', array($this, 'add_welcome_note'));
            
            // Atualização de notas
            add_action('admin_init', array($this, 'maybe_add_scheduled_notes'));
            add_action('nosfir_theme_updated', array($this, 'add_update_note'));
            
            // Remove notas antigas
            add_action('admin_init', array($this, 'maybe_delete_old_notes'));
            
            // Tracking de ações
            add_action('woocommerce_note_action_clicked', array($this, 'track_note_action'), 10, 2);
            
            // Filtros
            add_filter('woocommerce_admin_note_query_args', array($this, 'filter_note_query_args'));
            
            // AJAX handlers
            add_action('wp_ajax_nosfir_dismiss_inbox_note', array($this, 'ajax_dismiss_note'));
            add_action('wp_ajax_nosfir_complete_inbox_action', array($this, 'ajax_complete_action'));
        }

        /**
         * Verifica se WooCommerce Admin está disponível
         */
        private function is_wc_admin_available() {
            return class_exists('Automattic\WooCommerce\Admin\Notes\Note') && 
                   class_exists('Automattic\WooCommerce\Admin\Notes\NoteTraits');
        }

        /**
         * Adiciona notas iniciais
         */
        public function add_notes() {
            // Adiciona nota de boas-vindas se for primeira instalação
            if ($this->is_fresh_install()) {
                $this->add_note('welcome');
            }
        }

        /**
         * Adiciona nota de boas-vindas
         */
        public function add_welcome_note() {
            $this->add_note('welcome');
        }

        /**
         * Adiciona notas agendadas
         */
        public function maybe_add_scheduled_notes() {
            $install_timestamp = get_option('nosfir_installed_time', current_time('timestamp'));
            $current_time = current_time('timestamp');
            $time_since_install = $current_time - $install_timestamp;

            foreach ($this->note_timings as $note_type => $timing) {
                if ($timing > 0 && $time_since_install >= $timing) {
                    if (!$this->note_exists($note_type)) {
                        $this->add_note($note_type);
                    }
                }
            }
        }

        /**
         * Adiciona uma nota específica
         */
        public function add_note($type) {
            if (!isset($this->registered_notes[$type])) {
                return false;
            }

            $note_class = $this->registered_notes[$type];
            
            if (!class_exists($note_class)) {
                $this->load_note_class($type);
            }

            if (!class_exists($note_class)) {
                return false;
            }

            try {
                $note = $note_class::get_note();
                if ($note && !$this->note_exists($type)) {
                    $note->save();
                    do_action('nosfir_inbox_note_added', $type, $note);
                    return true;
                }
            } catch (NotesUnavailableException $e) {
                error_log('Nosfir NUX: Failed to add note - ' . $e->getMessage());
            }

            return false;
        }

        /**
         * Carrega classe de nota
         */
        private function load_note_class($type) {
            $file = __DIR__ . '/notes/class-nosfir-nux-note-' . str_replace('_', '-', $type) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        }

        /**
         * Verifica se nota existe
         */
        private function note_exists($type) {
            $note_name = self::NOTE_PREFIX . $type;
            
            try {
                $data_store = Notes::load_data_store();
                $note_ids = $data_store->get_notes_with_name($note_name);
                return !empty($note_ids);
            } catch (Exception $e) {
                return false;
            }
        }

        /**
         * Remove notas antigas
         */
        public function maybe_delete_old_notes() {
            $max_age = apply_filters('nosfir_nux_note_max_age', 90 * DAY_IN_SECONDS);
            $cutoff_time = current_time('timestamp') - $max_age;

            try {
                $data_store = Notes::load_data_store();
                $note_ids = $data_store->get_notes();

                foreach ($note_ids as $note_id) {
                    $note = Notes::get_note($note_id);
                    if ($note && strpos($note->get_name(), self::NOTE_PREFIX) === 0) {
                        $date_created = $note->get_date_created();
                        if ($date_created && $date_created->getTimestamp() < $cutoff_time) {
                            $note->delete();
                        }
                    }
                }
            } catch (Exception $e) {
                error_log('Nosfir NUX: Failed to delete old notes - ' . $e->getMessage());
            }
        }

        /**
         * Track ação de nota
         */
        public function track_note_action($note_name, $action_name) {
            if (strpos($note_name, self::NOTE_PREFIX) !== 0) {
                return;
            }

            $note_type = str_replace(self::NOTE_PREFIX, '', $note_name);
            
            // Registra no log
            update_option('nosfir_nux_action_' . $note_type . '_' . $action_name, current_time('timestamp'));
            
            // Dispara hook para tracking adicional
            do_action('nosfir_nux_note_action', $note_type, $action_name);
        }

        /**
         * Filtra argumentos de query de notas
         */
        public function filter_note_query_args($args) {
            // Pode filtrar notas por source
            if (isset($_GET['source']) && $_GET['source'] === 'nosfir') {
                $args['source'] = 'nosfir';
            }
            
            return $args;
        }

        /**
         * AJAX: Dismiss nota
         */
        public function ajax_dismiss_note() {
            check_ajax_referer('nosfir-nux', 'nonce');

            $note_id = isset($_POST['note_id']) ? absint($_POST['note_id']) : 0;
            
            if (!$note_id) {
                wp_send_json_error('Invalid note ID');
            }

            try {
                $note = Notes::get_note($note_id);
                if ($note && strpos($note->get_name(), self::NOTE_PREFIX) === 0) {
                    $note->set_status(Note::E_WC_ADMIN_NOTE_SNOOZED);
                    $note->save();
                    wp_send_json_success('Note dismissed');
                }
            } catch (Exception $e) {
                wp_send_json_error($e->getMessage());
            }

            wp_send_json_error('Note not found');
        }

        /**
         * AJAX: Complete ação
         */
        public function ajax_complete_action() {
            check_ajax_referer('nosfir-nux', 'nonce');

            $note_id = isset($_POST['note_id']) ? absint($_POST['note_id']) : 0;
            $action = isset($_POST['action_name']) ? sanitize_text_field($_POST['action_name']) : '';

            if (!$note_id || !$action) {
                wp_send_json_error('Invalid parameters');
            }

            try {
                $note = Notes::get_note($note_id);
                if ($note && strpos($note->get_name(), self::NOTE_PREFIX) === 0) {
                    $note->set_status(Note::E_WC_ADMIN_NOTE_ACTIONED);
                    $note->save();
                    
                    // Track action
                    $this->track_note_action($note->get_name(), $action);
                    
                    wp_send_json_success('Action completed');
                }
            } catch (Exception $e) {
                wp_send_json_error($e->getMessage());
            }

            wp_send_json_error('Note not found');
        }

        /**
         * Verifica se é instalação nova
         */
        private function is_fresh_install() {
            $installed = get_option('nosfir_installed_time');
            
            if (!$installed) {
                update_option('nosfir_installed_time', current_time('timestamp'));
                return true;
            }
            
            // Considera "fresh" se instalado há menos de 1 hora
            return (current_time('timestamp') - $installed) < HOUR_IN_SECONDS;
        }

        /**
         * Adiciona nota de atualização
         */
        public function add_update_note($new_version) {
            $note = new Note();
            $note->set_title(sprintf(__('Nosfir updated to %s 🎉', 'nosfir'), $new_version));
            $note->set_content(__('Check out the new features and improvements in this version.', 'nosfir'));
            $note->set_type(Note::E_WC_ADMIN_NOTE_INFORMATIONAL);
            $note->set_name(self::NOTE_PREFIX . 'update-' . str_replace('.', '-', $new_version));
            $note->set_source('nosfir');
            $note->add_action(
                'view-changelog',
                __('View Changelog', 'nosfir'),
                admin_url('admin.php?page=nosfir-dashboard&tab=changelog'),
                Note::E_WC_ADMIN_NOTE_UNACTIONED,
                true
            );
            
            try {
                $note->save();
            } catch (Exception $e) {
                error_log('Nosfir NUX: Failed to add update note - ' . $e->getMessage());
            }
        }

        /**
         * Obtém todas as notas do Nosfir
         */
        public function get_nosfir_notes() {
            try {
                $data_store = Notes::load_data_store();
                $note_ids = $data_store->get_notes(array(
                    'source' => 'nosfir',
                ));

                $notes = array();
                foreach ($note_ids as $note_id) {
                    $notes[] = Notes::get_note($note_id);
                }
                
                return $notes;
            } catch (Exception $e) {
                return array();
            }
        }
    }

    /**
     * Nota de Boas-vindas
     */
    class Nosfir_NUX_Note_Welcome {
        use NoteTraits;

        const NOTE_NAME = 'nosfir-welcome';

        public static function get_note() {
            $note = new Note();
            $note->set_title(__('Welcome to Nosfir Theme! 🎨', 'nosfir'));
            $note->set_content(__('Thank you for choosing Nosfir. Let\'s get started with setting up your beautiful website.', 'nosfir'));
            $note->set_type(Note::E_WC_ADMIN_NOTE_INFORMATIONAL);
            $note->set_name(self::NOTE_NAME);
            $note->set_source('nosfir');
            $note->add_action(
                'get-started',
                __('Get Started', 'nosfir'),
                admin_url('admin.php?page=nosfir-welcome'),
                Note::E_WC_ADMIN_NOTE_ACTIONED,
                true
            );
            $note->add_action(
                'watch-tutorial',
                __('Watch Tutorial', 'nosfir'),
                'https://youtube.com/nosfir-tutorial',
                Note::E_WC_ADMIN_NOTE_UNACTIONED,
                true
            );
            return $note;
        }
    }

    /**
     * Nota de Personalização
     */
    class Nosfir_NUX_Note_Customize {
        use NoteTraits;

        const NOTE_NAME = 'nosfir-customize';

        public static function get_note() {
            $note = new Note();
            $note->set_title(__('Customize Your Store Design 🎯', 'nosfir'));
            $note->set_content(__('Make your store unique! Visit the Customizer to adjust colors, typography, layouts and more.', 'nosfir'));
            $note->set_type(Note::E_WC_ADMIN_NOTE_INFORMATIONAL);
            $note->set_name(self::NOTE_NAME);
            $note->set_source('nosfir');
            $note->add_action(
                'open-customizer',
                __('Open Customizer', 'nosfir'),
                admin_url('customize.php'),
                Note::E_WC_ADMIN_NOTE_ACTIONED,
                true
            );
            $note->add_action(
                'theme-settings',
                __('Theme Settings', 'nosfir'),
                admin_url('admin.php?page=nosfir-settings'),
                Note::E_WC_ADMIN_NOTE_UNACTIONED,
                false
            );
            return $note;
        }
    }

    /**
     * Nota de Plugins
     */
    class Nosfir_NUX_Note_Plugins {
        use NoteTraits;

        const NOTE_NAME = 'nosfir-plugins';

        public static function get_note() {
            $note = new Note();
            $note->set_title(__('Enhance with Recommended Plugins 🔌', 'nosfir'));
            $note->set_content(__('Install our recommended plugins to unlock additional features and functionality for your store.', 'nosfir'));
            $note->set_type(Note::E_WC_ADMIN_NOTE_INFORMATIONAL);
            $note->set_name(self::NOTE_NAME);
            $note->set_source('nosfir');
            $note->add_action(
                'install-plugins',
                __('Install Plugins', 'nosfir'),
                admin_url('admin.php?page=nosfir-plugins'),
                Note::E_WC_ADMIN_NOTE_ACTIONED,
                true
            );
            return $note;
        }
    }

    /**
     * Nota de Demo Import
     */
    class Nosfir_NUX_Note_Demo_Import {
        use NoteTraits;

        const NOTE_NAME = 'nosfir-demo-import';

        public static function get_note() {
            $note = new Note();
            $note->set_title(__('Quick Start with Demo Content 🚀', 'nosfir'));
            $note->set_content(__('Import professionally designed demo content to jumpstart your website. Choose from multiple layouts and styles.', 'nosfir'));
            $note->set_type(Note::E_WC_ADMIN_NOTE_INFORMATIONAL);
            $note->set_name(self::NOTE_NAME);
            $note->set_source('nosfir');
            $note->add_action(
                'import-demo',
                __('Browse Demos', 'nosfir'),
                admin_url('admin.php?page=nosfir-demo-import'),
                Note::E_WC_ADMIN_NOTE_ACTIONED,
                true
            );
            return $note;
        }
    }

    /**
     * Nota de Review
     */
    class Nosfir_NUX_Note_Review {
        use NoteTraits;

        const NOTE_NAME = 'nosfir-review';

        public static function get_note() {
            $note = new Note();
            $note->set_title(__('Enjoying Nosfir? Leave a Review! ⭐', 'nosfir'));
            $note->set_content(__('We\'d love to hear your feedback! If you\'re enjoying Nosfir, please consider leaving us a 5-star review.', 'nosfir'));
            $note->set_type(Note::E_WC_ADMIN_NOTE_INFORMATIONAL);
            $note->set_name(self::NOTE_NAME);
            $note->set_source('nosfir');
            $note->add_action(
                'leave-review',
                __('Leave Review', 'nosfir'),
                'https://wordpress.org/support/theme/nosfir/reviews/#new-post',
                Note::E_WC_ADMIN_NOTE_ACTIONED,
                true
            );
            $note->add_action(
                'maybe-later',
                __('Maybe Later', 'nosfir'),
                '#',
                Note::E_WC_ADMIN_NOTE_SNOOZED,
                false
            );
            return $note;
        }
    }

    /**
     * Nota Premium
     */
    class Nosfir_NUX_Note_Premium {
        use NoteTraits;

        const NOTE_NAME = 'nosfir-premium';

        public static function get_note() {
            $note = new Note();
            $note->set_title(__('Unlock Premium Features 💎', 'nosfir'));
            $note->set_content(__('Take your store to the next level with Nosfir Premium. Get access to exclusive features, priority support, and regular updates.', 'nosfir'));
            $note->set_type(Note::E_WC_ADMIN_NOTE_MARKETING);
            $note->set_name(self::NOTE_NAME);
            $note->set_source('nosfir');
            $note->add_action(
                'view-premium',
                __('View Premium Features', 'nosfir'),
                'https://nosfir.com/premium',
                Note::E_WC_ADMIN_NOTE_ACTIONED,
                true
            );
            $note->add_action(
                'not-interested',
                __('Not Interested', 'nosfir'),
                '#',
                Note::E_WC_ADMIN_NOTE_SNOOZED,
                false
            );
            return $note;
        }
    }

    /**
     * Nota de Suporte
     */
    class Nosfir_NUX_Note_Support {
        use NoteTraits;

        const NOTE_NAME = 'nosfir-support';

        public static function get_note() {
            $note = new Note();
            $note->set_title(__('Need Help? We\'re Here! 🤝', 'nosfir'));
            $note->set_content(__('Having trouble with something? Check out our documentation or contact our support team for assistance.', 'nosfir'));
            $note->set_type(Note::E_WC_ADMIN_NOTE_INFORMATIONAL);
            $note->set_name(self::NOTE_NAME);
            $note->set_source('nosfir');
            $note->add_action(
                'view-docs',
                __('View Documentation', 'nosfir'),
                'https://docs.nosfir.com',
                Note::E_WC_ADMIN_NOTE_UNACTIONED,
                true
            );
            $note->add_action(
                'contact-support',
                __('Contact Support', 'nosfir'),
                admin_url('admin.php?page=nosfir-support'),
                Note::E_WC_ADMIN_NOTE_ACTIONED,
                false
            );
            return $note;
        }
    }

    /**
     * Nota de Performance
     */
    class Nosfir_NUX_Note_Performance {
        use NoteTraits;

        const NOTE_NAME = 'nosfir-performance';

        public static function get_note() {
            $note = new Note();
            $note->set_title(__('Optimize Your Store Performance ⚡', 'nosfir'));
            $note->set_content(__('Enable performance features like lazy loading, caching, and minification to speed up your store.', 'nosfir'));
            $note->set_type(Note::E_WC_ADMIN_NOTE_INFORMATIONAL);
            $note->set_name(self::NOTE_NAME);
            $note->set_source('nosfir');
            $note->add_action(
                'optimize-now',
                __('Optimize Now', 'nosfir'),
                admin_url('admin.php?page=nosfir-settings&tab=performance'),
                Note::E_WC_ADMIN_NOTE_ACTIONED,
                true
            );
            return $note;
        }
    }

    /**
     * Nota de SEO
     */
    class Nosfir_NUX_Note_SEO {
        use NoteTraits;

        const NOTE_NAME = 'nosfir-seo';

        public static function get_note() {
            $note = new Note();
            $note->set_title(__('Improve Your SEO Rankings 📈', 'nosfir'));
            $note->set_content(__('Nosfir is SEO-ready! Configure meta tags, schema markup, and more to improve your search rankings.', 'nosfir'));
            $note->set_type(Note::E_WC_ADMIN_NOTE_INFORMATIONAL);
            $note->set_name(self::NOTE_NAME);
            $note->set_source('nosfir');
            $note->add_action(
                'seo-settings',
                __('Configure SEO', 'nosfir'),
                admin_url('admin.php?page=nosfir-settings&tab=seo'),
                Note::E_WC_ADMIN_NOTE_ACTIONED,
                true
            );
            $note->add_action(
                'install-yoast',
                __('Install Yoast SEO', 'nosfir'),
                admin_url('plugin-install.php?s=yoast+seo&tab=search'),
                Note::E_WC_ADMIN_NOTE_UNACTIONED,
                false
            );
            return $note;
        }
    }

endif;

// Inicializa se WooCommerce Admin estiver disponível
if (class_exists('Automattic\WooCommerce\Admin\Notes\Note')) {
    return Nosfir_NUX_Admin_Inbox::get_instance();
}