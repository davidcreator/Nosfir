/**
 * Nosfir Theme - Admin JavaScript
 * 
 * @package     Nosfir
 * @subpackage  Admin
 * @version     1.0.0
 * @author      David Creator
 * 
 * Este arquivo contém todos os scripts administrativos do tema Nosfir
 * Funcionalidades: Notificações, AJAX, Personalizações do Admin, Media Upload, etc.
 */

/* global ajaxurl, nosfirAdmin, wp */
(function (wp, $) {
    'use strict';

    // Verificar se WordPress e jQuery estão disponíveis
    if (!wp || !$) {
        return;
    }

    /**
     * Objeto principal do Admin Nosfir
     */
    const NosfirAdmin = {
        
        // Configurações
        config: {
            ajaxUrl: ajaxurl || '/wp-admin/admin-ajax.php',
            nonce: nosfirAdmin?.nonce || '',
            version: nosfirAdmin?.version || '1.0.0',
            debugMode: nosfirAdmin?.debug || false,
            animations: {
                fadeSpeed: 300,
                slideSpeed: 400,
                noticeTimeout: 5000
            }
        },

        // Estado da aplicação
        state: {
            isProcessing: false,
            dismissedNotices: [],
            mediaUploader: null,
            unsavedChanges: false
        },

        /**
         * Inicialização
         */
        init() {
            this.log('Nosfir Admin initialized');
            
            // Bind events
            this.bindEvents();
            
            // Inicializar componentes
            this.initComponents();
            
            // Setup do tema (primeira vez)
            this.checkFirstTimeSetup();
            
            // Verificar atualizações
            this.checkForUpdates();
            
            // Inicializar tooltips
            this.initTooltips();
        },

        /**
         * Logger condicional para debug
         */
        log(message, type = 'log') {
            if (this.config.debugMode) {
                console[type]('[Nosfir Admin]:', message);
            }
        },

        /**
         * ==================================================================
         * NOTIFICAÇÕES E MENSAGENS - Baseado na referência
         * ==================================================================
         */

        /**
         * Ajax request para dispensar notificações NUX - Baseado na referência
         */
        dismissNux() {
            const self = this;
            
            $.ajax({
                type: 'POST',
                url: this.config.ajaxUrl,
                data: {
                    nonce: this.config.nonce,
                    action: 'nosfir_dismiss_notice'
                },
                dataType: 'json',
                beforeSend: () => {
                    self.state.isProcessing = true;
                },
                success: (response) => {
                    if (response.success) {
                        self.log('Notice dismissed successfully');
                        self.state.dismissedNotices.push(response.data.notice_id);
                    } else {
                        self.showNotification('Error dismissing notice', 'error');
                    }
                },
                error: (xhr, status, error) => {
                    self.log(`AJAX Error: ${error}`, 'error');
                    self.showNotification('Connection error. Please try again.', 'error');
                },
                complete: () => {
                    self.state.isProcessing = false;
                }
            });
        },

        /**
         * Sistema de notificações melhorado
         */
        showNotification(message, type = 'success', duration = null) {
            const noticeClass = `notice notice-${type} is-dismissible nosfir-notice`;
            const noticeHtml = `
                <div class="${noticeClass}">
                    <p><strong>Nosfir Theme:</strong> ${message}</p>
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                </div>
            `;
            
            const $notice = $(noticeHtml);
            
            // Adicionar após o título da página ou no topo
            if ($('.wrap h1').length) {
                $('.wrap h1').first().after($notice);
            } else {
                $('.wrap').prepend($notice);
            }
            
            // Animar entrada
            $notice.hide().slideDown(this.config.animations.slideSpeed);
            
            // Auto-dismiss se duração especificada
            if (duration || (type !== 'error' && this.config.animations.noticeTimeout)) {
                setTimeout(() => {
                    this.dismissNotice($notice);
                }, duration || this.config.animations.noticeTimeout);
            }
            
            // Bind dismiss button
            $notice.find('.notice-dismiss').on('click', () => {
                this.dismissNotice($notice);
            });
            
            return $notice;
        },

        /**
         * Dismiss notice com animação
         */
        dismissNotice($notice) {
            $notice.slideUp(this.config.animations.slideSpeed, function() {
                $(this).remove();
            });
        },

        /**
         * ==================================================================
         * EVENTOS - Baseado na referência e expandido
         * ==================================================================
         */

        bindEvents() {
            const self = this;
            
            $(function() {
                // Dismiss notice - Baseado na referência
                $(document).on('click', '.nf-notice-nux .notice-dismiss', function() {
                    self.dismissNux();
                });
                
                // Dismiss notice inside theme page - Baseado na referência  
                $(document).on('click', '.nf-nux-dismiss-button', function() {
                    self.dismissNux();
                    $('.nosfir-intro-setup').hide();
                    $('.nosfir-intro-message').fadeIn('slow');
                });
                
                // Salvar configurações do tema
                $(document).on('submit', '#nosfir-theme-options', function(e) {
                    e.preventDefault();
                    self.saveThemeOptions($(this));
                });
                
                // Upload de mídia
                $(document).on('click', '.nosfir-upload-button', function(e) {
                    e.preventDefault();
                    self.openMediaUploader($(this));
                });
                
                // Remover mídia
                $(document).on('click', '.nosfir-remove-media', function(e) {
                    e.preventDefault();
                    self.removeMedia($(this));
                });
                
                // Color picker
                $('.nosfir-color-picker').each(function() {
                    self.initColorPicker($(this));
                });
                
                // Tabs do admin
                $(document).on('click', '.nosfir-tabs a', function(e) {
                    e.preventDefault();
                    self.switchTab($(this));
                });
                
                // Detectar mudanças não salvas
                $(document).on('change', '.nosfir-admin-form :input', function() {
                    self.state.unsavedChanges = true;
                });
                
                // Aviso ao sair com mudanças não salvas
                $(window).on('beforeunload', function() {
                    if (self.state.unsavedChanges) {
                        return 'You have unsaved changes. Are you sure you want to leave?';
                    }
                });
                
                // Toggle de seções
                $(document).on('click', '.nosfir-section-toggle', function() {
                    self.toggleSection($(this));
                });
                
                // Copiar para clipboard
                $(document).on('click', '.nosfir-copy-button', function() {
                    self.copyToClipboard($(this));
                });
                
                // Live preview
                $(document).on('input change', '.nosfir-live-preview', function() {
                    self.updateLivePreview($(this));
                });
                
                // Import/Export
                $(document).on('click', '#nosfir-export-settings', function(e) {
                    e.preventDefault();
                    self.exportSettings();
                });
                
                $(document).on('change', '#nosfir-import-file', function(e) {
                    self.importSettings(e.target.files[0]);
                });
                
                // Reset settings
                $(document).on('click', '#nosfir-reset-settings', function(e) {
                    e.preventDefault();
                    self.resetSettings();
                });
            });
        },

        /**
         * ==================================================================
         * COMPONENTES
         * ==================================================================
         */

        initComponents() {
            // Accordion
            this.initAccordion();
            
            // Sortable lists
            this.initSortable();
            
            // Select2
            this.initSelect2();
            
            // Code editor
            this.initCodeEditor();
            
            // Chart.js para estatísticas
            this.initCharts();
            
            // DataTables
            this.initDataTables();
        },

        /**
         * Accordion para seções colapsáveis
         */
        initAccordion() {
            $('.nosfir-accordion').each(function() {
                const $accordion = $(this);
                
                $accordion.find('.accordion-header').on('click', function() {
                    const $header = $(this);
                    const $content = $header.next('.accordion-content');
                    const $icon = $header.find('.accordion-icon');
                    
                    // Toggle content
                    $content.slideToggle(300);
                    
                    // Rotate icon
                    $icon.toggleClass('rotated');
                    
                    // Update aria-expanded
                    const expanded = $header.attr('aria-expanded') === 'true';
                    $header.attr('aria-expanded', !expanded);
                });
            });
        },

        /**
         * Sortable para reorganizar elementos
         */
        initSortable() {
            if ($.fn.sortable) {
                $('.nosfir-sortable').sortable({
                    handle: '.sortable-handle',
                    placeholder: 'sortable-placeholder',
                    update: (event, ui) => {
                        this.updateSortOrder(ui.item.parent());
                    }
                });
            }
        },

        /**
         * Select2 para selects avançados
         */
        initSelect2() {
            if ($.fn.select2) {
                $('.nosfir-select2').select2({
                    theme: 'default',
                    width: '100%',
                    minimumResultsForSearch: 10
                });
            }
        },

        /**
         * Code editor para campos de código
         */
        initCodeEditor() {
            if (wp.codeEditor) {
                $('.nosfir-code-editor').each(function() {
                    const $textarea = $(this);
                    const mode = $textarea.data('mode') || 'css';
                    
                    wp.codeEditor.initialize($textarea, {
                        codemirror: {
                            mode: mode,
                            lineNumbers: true,
                            lineWrapping: true,
                            theme: 'monokai'
                        }
                    });
                });
            }
        },

        /**
         * Charts para dashboard
         */
        initCharts() {
            if (typeof Chart !== 'undefined') {
                $('.nosfir-chart').each(function() {
                    const canvas = this;
                    const ctx = canvas.getContext('2d');
                    const type = $(canvas).data('chart-type') || 'line';
                    const data = $(canvas).data('chart-data') || {};
                    
                    new Chart(ctx, {
                        type: type,
                        data: data,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                });
            }
        },

        /**
         * DataTables para tabelas
         */
        initDataTables() {
            if ($.fn.DataTable) {
                $('.nosfir-datatable').DataTable({
                    responsive: true,
                    pageLength: 25,
                    language: {
                        search: 'Search:',
                        lengthMenu: 'Show _MENU_ entries',
                        info: 'Showing _START_ to _END_ of _TOTAL_ entries'
                    }
                });
            }
        },

        /**
         * ==================================================================
         * SETUP INICIAL DO TEMA
         * ==================================================================
         */

        checkFirstTimeSetup() {
            if (nosfirAdmin?.firstTime) {
                this.showWelcomeModal();
            }
        },

        /**
         * Modal de boas-vindas
         */
        showWelcomeModal() {
            const modalHtml = `
                <div id="nosfir-welcome-modal" class="nosfir-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2>Welcome to Nosfir Theme!</h2>
                            <button class="modal-close">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p>Thank you for choosing Nosfir. Let's get your site set up!</p>
                            <div class="setup-options">
                                <a href="#" class="button button-primary" id="start-setup-wizard">
                                    Start Setup Wizard
                                </a>
                                <a href="#" class="button" id="import-demo-content">
                                    Import Demo Content
                                </a>
                                <a href="#" class="button" id="skip-setup">
                                    Skip Setup
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('body').append(modalHtml);
            
            const $modal = $('#nosfir-welcome-modal');
            $modal.fadeIn(300);
            
            // Bind modal events
            $modal.on('click', '.modal-close, #skip-setup', () => {
                this.closeModal($modal);
                this.dismissNux();
            });
            
            $modal.on('click', '#start-setup-wizard', (e) => {
                e.preventDefault();
                this.startSetupWizard();
            });
            
            $modal.on('click', '#import-demo-content', (e) => {
                e.preventDefault();
                this.importDemoContent();
            });
        },

        /**
         * Setup Wizard
         */
        startSetupWizard() {
            const wizardSteps = [
                'welcome',
                'colors',
                'typography',
                'layout',
                'plugins',
                'content',
                'complete'
            ];
            
            let currentStep = 0;
            
            const showStep = (step) => {
                // Implementar lógica de cada step
                this.log(`Setup Wizard - Step: ${wizardSteps[step]}`);
            };
            
            showStep(currentStep);
        },

        /**
         * ==================================================================
         * THEME OPTIONS
         * ==================================================================
         */

        /**
         * Salvar opções do tema via AJAX
         */
        saveThemeOptions($form) {
            const self = this;
            const formData = new FormData($form[0]);
            
            formData.append('action', 'nosfir_save_theme_options');
            formData.append('nonce', this.config.nonce);
            
            // Adicionar loading state
            const $submitButton = $form.find('[type="submit"]');
            const originalText = $submitButton.text();
            $submitButton.prop('disabled', true).text('Saving...');
            
            $.ajax({
                type: 'POST',
                url: this.config.ajaxUrl,
                data: formData,
                processData: false,
                contentType: false,
                success: (response) => {
                    if (response.success) {
                        self.showNotification('Settings saved successfully!', 'success');
                        self.state.unsavedChanges = false;
                        
                        // Trigger custom event
                        $(document).trigger('nosfir:settings:saved', [response.data]);
                    } else {
                        self.showNotification(response.data.message || 'Error saving settings', 'error');
                    }
                },
                error: () => {
                    self.showNotification('Connection error. Please try again.', 'error');
                },
                complete: () => {
                    $submitButton.prop('disabled', false).text(originalText);
                }
            });
        },

        /**
         * ==================================================================
         * MEDIA UPLOADER
         * ==================================================================
         */

        /**
         * Abrir WordPress Media Uploader
         */
        openMediaUploader($button) {
            const self = this;
            const $input = $($button.data('target'));
            const $preview = $($button.data('preview'));
            const multiple = $button.data('multiple') || false;
            
            // Se já existe um uploader, reusar
            if (this.state.mediaUploader) {
                this.state.mediaUploader.open();
                return;
            }
            
            // Criar media uploader
            this.state.mediaUploader = wp.media({
                title: $button.data('title') || 'Select Media',
                button: {
                    text: $button.data('button-text') || 'Use this media'
                },
                multiple: multiple,
                library: {
                    type: $button.data('type') || 'image'
                }
            });
            
            // When media is selected
            this.state.mediaUploader.on('select', function() {
                const selection = self.state.mediaUploader.state().get('selection');
                
                if (multiple) {
                    const urls = [];
                    selection.each(function(attachment) {
                        urls.push(attachment.toJSON().url);
                    });
                    $input.val(urls.join(','));
                } else {
                    const attachment = selection.first().toJSON();
                    $input.val(attachment.url);
                    
                    // Update preview
                    if ($preview.length) {
                        if ($preview.is('img')) {
                            $preview.attr('src', attachment.url);
                        } else {
                            $preview.css('background-image', `url(${attachment.url})`);
                        }
                    }
                }
                
                // Mark as changed
                self.state.unsavedChanges = true;
            });
            
            // Open uploader
            this.state.mediaUploader.open();
        },

        /**
         * Remover mídia
         */
        removeMedia($button) {
            const $input = $($button.data('target'));
            const $preview = $($button.data('preview'));
            
            $input.val('');
            
            if ($preview.length) {
                if ($preview.is('img')) {
                    $preview.attr('src', '');
                } else {
                    $preview.css('background-image', 'none');
                }
            }
            
            this.state.unsavedChanges = true;
        },

        /**
         * ==================================================================
         * COLOR PICKER
         * ==================================================================
         */

        /**
         * Inicializar WordPress Color Picker
         */
        initColorPicker($input) {
            if ($.fn.wpColorPicker) {
                $input.wpColorPicker({
                    change: (event, ui) => {
                        const color = ui.color.toString();
                        
                        // Update live preview if exists
                        const previewTarget = $input.data('preview');
                        if (previewTarget) {
                            $(previewTarget).css($input.data('property') || 'color', color);
                        }
                        
                        // Mark as changed
                        this.state.unsavedChanges = true;
                        
                        // Trigger custom event
                        $input.trigger('nosfir:color:change', [color]);
                    }
                });
            }
        },

        /**
         * ==================================================================
         * TABS
         * ==================================================================
         */

        /**
         * Alternar entre tabs
         */
        switchTab($tab) {
            const target = $tab.attr('href');
            const $tabContainer = $tab.closest('.nosfir-tabs-container');
            
            // Update active tab
            $tabContainer.find('.nosfir-tabs a').removeClass('active');
            $tab.addClass('active');
            
            // Update active panel
            $tabContainer.find('.nosfir-tab-panel').removeClass('active');
            $(target).addClass('active');
            
            // Save active tab to localStorage
            if (typeof(Storage) !== 'undefined') {
                localStorage.setItem('nosfir_active_tab', target);
            }
        },

        /**
         * ==================================================================
         * UTILITIES
         * ==================================================================
         */

        /**
         * Toggle de seção
         */
        toggleSection($toggle) {
            const $section = $($toggle.data('target'));
            const $icon = $toggle.find('.toggle-icon');
            
            $section.slideToggle(300);
            $icon.toggleClass('rotated');
            
            // Save state
            const sectionId = $section.attr('id');
            const isOpen = $section.is(':visible');
            
            if (typeof(Storage) !== 'undefined') {
                localStorage.setItem(`nosfir_section_${sectionId}`, isOpen);
            }
        },

        /**
         * Copiar para clipboard
         */
        copyToClipboard($button) {
            const text = $button.data('copy-text') || $button.prev('input').val();
            
            // Criar elemento temporário
            const $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(text).select();
            
            try {
                document.execCommand('copy');
                this.showNotification('Copied to clipboard!', 'success', 2000);
                
                // Feedback visual
                const originalText = $button.text();
                $button.text('Copied!');
                setTimeout(() => {
                    $button.text(originalText);
                }, 2000);
            } catch (err) {
                this.showNotification('Failed to copy', 'error');
            }
            
            $temp.remove();
        },

        /**
         * Live preview
         */
        updateLivePreview($input) {
            const previewTarget = $input.data('preview-target');
            const previewProperty = $input.data('preview-property');
            const value = $input.val();
            
            if (previewTarget && previewProperty) {
                $(previewTarget).css(previewProperty, value);
            }
            
            // Trigger custom event
            $input.trigger('nosfir:preview:update', [value]);
        },

        /**
         * ==================================================================
         * IMPORT/EXPORT
         * ==================================================================
         */

        /**
         * Exportar configurações
         */
        exportSettings() {
            const self = this;
            
            $.ajax({
                type: 'POST',
                url: this.config.ajaxUrl,
                data: {
                    action: 'nosfir_export_settings',
                    nonce: this.config.nonce
                },
                success: (response) => {
                    if (response.success) {
                        // Criar download
                        const blob = new Blob([JSON.stringify(response.data, null, 2)], {
                            type: 'application/json'
                        });
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `nosfir-settings-${Date.now()}.json`;
                        a.click();
                        window.URL.revokeObjectURL(url);
                        
                        self.showNotification('Settings exported successfully!', 'success');
                    }
                }
            });
        },

        /**
         * Importar configurações
         */
        importSettings(file) {
            if (!file) return;
            
            const self = this;
            const reader = new FileReader();
            
            reader.onload = function(e) {
                try {
                    const settings = JSON.parse(e.target.result);
                    
                    // Confirmar importação
                    if (!confirm('This will override your current settings. Continue?')) {
                        return;
                    }
                    
                    $.ajax({
                        type: 'POST',
                        url: self.config.ajaxUrl,
                        data: {
                            action: 'nosfir_import_settings',
                            nonce: self.config.nonce,
                            settings: settings
                        },
                        success: (response) => {
                            if (response.success) {
                                self.showNotification('Settings imported successfully! Reloading...', 'success');
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                            }
                        }
                    });
                } catch (error) {
                    self.showNotification('Invalid settings file', 'error');
                }
            };
            
            reader.readAsText(file);
        },

        /**
         * Reset settings
         */
        resetSettings() {
            if (!confirm('This will reset all theme settings to defaults. This action cannot be undone. Continue?')) {
                return;
            }
            
            const self = this;
            
            $.ajax({
                type: 'POST',
                url: this.config.ajaxUrl,
                data: {
                    action: 'nosfir_reset_settings',
                    nonce: this.config.nonce
                },
                success: (response) => {
                    if (response.success) {
                        self.showNotification('Settings reset successfully! Reloading...', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                }
            });
        },

        /**
         * ==================================================================
         * TOOLTIPS
         * ==================================================================
         */

        /**
         * Inicializar tooltips
         */
        initTooltips() {
            if ($.fn.tooltip) {
                $('.nosfir-tooltip').tooltip({
                    placement: 'top',
                    trigger: 'hover'
                });
            }
            
            // Custom tooltips
            $('.has-tooltip').each(function() {
                const $elem = $(this);
                const tooltip = $elem.data('tooltip');
                
                $elem.on('mouseenter', function() {
                    const $tooltip = $(`<div class="nosfir-custom-tooltip">${tooltip}</div>`);
                    $('body').append($tooltip);
                    
                    const offset = $elem.offset();
                    $tooltip.css({
                        top: offset.top - $tooltip.outerHeight() - 10,
                        left: offset.left + ($elem.outerWidth() / 2) - ($tooltip.outerWidth() / 2)
                    }).fadeIn(200);
                });
                
                $elem.on('mouseleave', function() {
                    $('.nosfir-custom-tooltip').fadeOut(200, function() {
                        $(this).remove();
                    });
                });
            });
        },

        /**
         * ==================================================================
         * UPDATE CHECK
         * ==================================================================
         */

        /**
         * Verificar atualizações do tema
         */
        checkForUpdates() {
            const lastCheck = localStorage.getItem('nosfir_last_update_check');
            const now = Date.now();
            const dayInMs = 24 * 60 * 60 * 1000;
            
            // Check only once per day
            if (lastCheck && (now - lastCheck) < dayInMs) {
                return;
            }
            
            $.ajax({
                type: 'GET',
                url: this.config.ajaxUrl,
                data: {
                    action: 'nosfir_check_updates',
                    nonce: this.config.nonce
                },
                success: (response) => {
                    if (response.success && response.data.hasUpdate) {
                        this.showUpdateNotification(response.data);
                    }
                    localStorage.setItem('nosfir_last_update_check', now);
                }
            });
        },

        /**
         * Mostrar notificação de atualização
         */
        showUpdateNotification(updateData) {
            const message = `
                A new version (${updateData.version}) of Nosfir Theme is available! 
                <a href="${updateData.url}" target="_blank">View details</a> or 
                <a href="#" class="nosfir-update-now">update now</a>.
            `;
            
            const $notice = this.showNotification(message, 'info', null);
            
            $notice.on('click', '.nosfir-update-now', (e) => {
                e.preventDefault();
                this.updateTheme();
            });
        },

        /**
         * Atualizar tema
         */
        updateTheme() {
            // Implementar lógica de atualização
            this.log('Theme update initiated');
        },

        /**
         * ==================================================================
         * UTILITIES
         * ==================================================================
         */

        /**
         * Fechar modal
         */
        closeModal($modal) {
            $modal.fadeOut(300, function() {
                $(this).remove();
            });
        },

        /**
         * Import demo content
         */
        importDemoContent() {
            const self = this;
            
            if (!confirm('This will import demo content. Existing content will not be deleted. Continue?')) {
                return;
            }
            
            const $progress = $(`
                <div class="nosfir-import-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 0%"></div>
                    </div>
                    <div class="progress-text">Starting import...</div>
                </div>
            `);
            
            $('body').append($progress);
            
            // Simulate progress (replace with actual import logic)
            let progress = 0;
            const interval = setInterval(() => {
                progress += 10;
                $progress.find('.progress-fill').css('width', progress + '%');
                $progress.find('.progress-text').text(`Importing... ${progress}%`);
                
                if (progress >= 100) {
                    clearInterval(interval);
                    $progress.find('.progress-text').text('Import complete!');
                    
                    setTimeout(() => {
                        $progress.fadeOut(300, function() {
                            $(this).remove();
                        });
                        self.showNotification('Demo content imported successfully!', 'success');
                    }, 1500);
                }
            }, 500);
        },

        /**
         * Update sort order
         */
        updateSortOrder($container) {
            const order = [];
            
            $container.find('.sortable-item').each(function(index) {
                order.push({
                    id: $(this).data('id'),
                    order: index
                });
            });
            
            // Save order via AJAX
            $.ajax({
                type: 'POST',
                url: this.config.ajaxUrl,
                data: {
                    action: 'nosfir_update_sort_order',
                    nonce: this.config.nonce,
                    order: order
                }
            });
        }
    };

    /**
     * ==================================================================
     * INICIALIZAÇÃO
     * ==================================================================
     */

    // Aguardar DOM ready
    $(document).ready(function() {
        // Inicializar Nosfir Admin
        NosfirAdmin.init();
        
        // Expor globalmente para debugging
        window.NosfirAdmin = NosfirAdmin;
    });

    /**
     * ==================================================================
     * COMPATIBILIDADE COM REFERÊNCIA ORIGINAL
     * ==================================================================
     */

    // Manter compatibilidade com código legado baseado na referência
    function dismissNux() {
        NosfirAdmin.dismissNux();
    }

    $(function() {
        // Dismiss notice - Código original da referência
        $(document).on('click', '.sf-notice-nux .notice-dismiss', function() {
            dismissNux();
        });

        // Dismiss notice inside theme page - Código original da referência
        $(document).on('click', '.sf-nux-dismiss-button', function() {
            dismissNux();
            $('.storefront-intro-setup').hide();
            $('.storefront-intro-message').fadeIn('slow');
        });
    });

})(window.wp, jQuery);