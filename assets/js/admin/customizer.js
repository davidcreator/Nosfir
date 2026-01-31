/**
 * Nosfir Theme - Customizer JavaScript
 * 
 * @package     Nosfir
 * @subpackage  Admin/Customizer
 * @version     1.0.0
 * @author      David Creator
 * 
 * Este arquivo contém todos os scripts do Customizer do tema Nosfir
 * Funcionalidades: Live Preview, Guided Tour, Controls Dinâmicos, etc.
 */

/* global _wpCustomizeNFGuidedTourSteps, _wpCustomizeNosfirSettings */
(function (wp, $) {
    'use strict';

    // Verificar se WordPress Customizer está disponível
    if (!wp || !wp.customize) {
        return;
    }

    // Set up namespace
    const api = wp.customize;

    /**
     * ==================================================================
     * GUIDED TOUR - Baseado na referência original
     * ==================================================================
     */

    api.NFGuidedTourSteps = [];

    // Estender com steps customizados - Compatível com referência
    if (typeof _wpCustomizeNFGuidedTourSteps !== 'undefined') {
        $.extend(api.NFGuidedTourSteps, _wpCustomizeNFGuidedTourSteps);
    }

    /**
     * wp.customize.NFGuidedTour - Baseado na referência original
     * Tour guiado pelo Customizer
     */
    api.NFGuidedTour = {
        $container: null,
        currentStep: -1,
        tourActive: false,
        tourCompleted: false,

        /**
         * Inicialização - Baseado na referência
         */
        init() {
            this._setupUI();
            this._checkTourStatus();
        },

        /**
         * Setup da UI - Baseado na referência
         */
        _setupUI() {
            const self = this;
            const $wpCustomize = $('body.wp-customizer .wp-full-overlay');

            // Criar container do tour
            this.$container = $('<div/>').addClass('nf-guided-tour');

            // Add guided tour div - Baseado na referência
            $wpCustomize.prepend(this.$container);

            // Add listeners - Baseado na referência
            this._addListeners();

            // Initial position - Baseado na referência
            this.$container
                .css(
                    !$('body').hasClass('rtl') ? 'left' : 'right',
                    $('#customize-controls').width() + 10 + 'px'
                )
                .on('transitionend', function () {
                    self.$container.addClass('nf-loaded');
                });

            // Show first step - Baseado na referência
            this._showNextStep();

            // Button handlers - Baseado na referência
            $(document).on('click', '.nf-guided-tour-step .nf-nux-button', function () {
                self._showNextStep();
                return false;
            });

            $(document).on('click', '.nf-guided-tour-step .nf-guided-tour-skip', function () {
                if (self.currentStep === 0) {
                    self._hideTour(true);
                } else {
                    self._showNextStep();
                }
                return false;
            });

            // Adicionar botão de restart tour
            this._addRestartButton();
        },

        /**
         * Add listeners - Baseado na referência
         */
        _addListeners() {
            const self = this;

            // Baseado na referência
            api.state('expandedSection').bind(function () {
                self._adjustPosition();
            });

            api.state('expandedPanel').bind(function () {
                self._adjustPosition();
            });

            // Listeners adicionais
            api.previewer.bind('ready', function () {
                self._onPreviewerReady();
            });

            // Resize listener
            $(window).on('resize.nosfir-tour', function () {
                self._adjustPosition();
            });
        },

        /**
         * Adjust position - Baseado na referência
         */
        _adjustPosition() {
            const step = this._getCurrentStep();

            if (!step) {
                return;
            }

            this.$container.removeClass('nf-inside-section');

            const expandedSection = api.state('expandedSection').get();
            const expandedPanel = api.state('expandedPanel').get();

            // Baseado na referência
            if (expandedSection && step.section === expandedSection.id) {
                this._moveContainer(
                    $(expandedSection.container[1]).find('.customize-section-title')
                );
                this.$container.addClass('nf-inside-section');
            } else if (expandedSection === false && expandedPanel === false) {
                if (this._isTourHidden()) {
                    this._revealTour();
                } else {
                    const selector = this._getSelector(step.section);
                    this._moveContainer(selector);
                }
            } else {
                this._hideTour();
            }
        },

        /**
         * Hide tour - Baseado na referência
         */
        _hideTour(remove) {
            const self = this;

            // Already hidden? - Baseado na referência
            if (this._isTourHidden()) {
                return;
            }

            const containerOffset = this.$container.offset();

            this.$container.css({
                transform: '',
                top: containerOffset.top,
            });

            // Baseado na referência
            $('body')
                .addClass('nf-exiting')
                .on('animationend.nosfir webkitAnimationEnd.nosfir', function () {
                    $(this)
                        .removeClass('nf-exiting')
                        .off('animationend.nosfir webkitAnimationEnd.nosfir')
                        .addClass('nf-hidden');
                    self.$container.hide();

                    if (typeof remove !== 'undefined' && remove === true) {
                        self._removeTour();
                    }
                });

            // Salvar status
            this._saveTourStatus('hidden');
        },

        /**
         * Reveal tour - Baseado na referência
         */
        _revealTour() {
            const self = this;

            $('body').removeClass('nf-hidden');
            self.$container.show();

            const containerOffset = this.$container.offset();
            const offsetTop = parseInt(containerOffset.top, 10);

            // Baseado na referência
            $('body')
                .addClass('nf-entering')
                .on('animationend.nosfir webkitAnimationEnd.nosfir', function () {
                    $(this)
                        .removeClass('nf-entering')
                        .off('animationend.nosfir webkitAnimationEnd.nosfir');

                    self.$container.css({
                        top: 'auto',
                        transform: 'translateY(' + offsetTop + 'px)',
                    });
                });
        },

        /**
         * Remove tour - Baseado na referência
         */
        _removeTour() {
            this.$container.remove();
            this.tourActive = false;
            this._saveTourStatus('completed');
        },

        /**
         * Close all sections - Baseado na referência
         */
        _closeAllSections() {
            api.section.each(function (section) {
                section.collapse({ duration: 0 });
            });

            api.panel.each(function (panel) {
                panel.collapse({ duration: 0 });
            });
        },

        /**
         * Show next step - Baseado na referência
         */
        _showNextStep() {
            if (this._isLastStep()) {
                this._hideTour(true);
                this._onTourComplete();
                return;
            }

            this._closeAllSections();

            // Get next step - Baseado na referência
            const step = this._getNextStep();

            // Convert line breaks to paragraphs - Baseado na referência
            step.message = this._lineBreaksToParagraphs(step.message);

            // Load template - Baseado na referência
            const template = wp.template('nf-guided-tour-step');

            this.$container.removeClass('nf-first-step');

            // Baseado na referência
            if (this.currentStep === 0) {
                step.first_step = true;
                this.$container.addClass('nf-first-step');
                this.tourActive = true;
            }

            if (this._isLastStep()) {
                step.last_step = true;
                this.$container.addClass('nf-last-step');
            }

            // Adicionar informações extras ao step
            step.current = this.currentStep + 1;
            step.total = api.NFGuidedTourSteps.length;
            step.progress = ((step.current / step.total) * 100).toFixed(0);

            this._moveContainer(this._getSelector(step.section));
            this.$container.html(template(step));

            // Trigger evento customizado
            $(document).trigger('nosfir:tour:step', [step]);
        },

        /**
         * Move container - Baseado na referência
         */
        _moveContainer($selector) {
            const self = this;

            if (!$selector) {
                return;
            }

            const position =
                parseInt($selector.offset().top, 10) +
                $selector.height() / 2 -
                44;

            // Baseado na referência
            this.$container
                .addClass('nf-moving')
                .css({
                    transform: 'translateY(' + position + 'px)',
                })
                .on('transitionend.nosfir', function () {
                    self.$container.removeClass('nf-moving');
                    self.$container.off('transitionend.nosfir');
                });
        },

        /**
         * Get selector - Baseado na referência
         */
        _getSelector(pointTo) {
            const sectionOrPanel = api.section(pointTo)
                ? api.section(pointTo)
                : api.panel(pointTo);

            // Check whether this is a section, panel, or a regular selector - Baseado na referência
            if (typeof sectionOrPanel !== 'undefined') {
                return $(sectionOrPanel.container[0]);
            }

            return $(pointTo);
        },

        /**
         * Get current step - Baseado na referência
         */
        _getCurrentStep() {
            return api.NFGuidedTourSteps[this.currentStep];
        },

        /**
         * Get next step - Baseado na referência
         */
        _getNextStep() {
            this.currentStep = this.currentStep + 1;
            return api.NFGuidedTourSteps[this.currentStep];
        },

        /**
         * Check if tour is hidden - Baseado na referência
         */
        _isTourHidden() {
            return $('body').hasClass('nf-hidden') ? true : false;
        },

        /**
         * Check if last step - Baseado na referência
         */
        _isLastStep() {
            return this.currentStep + 1 < api.NFGuidedTourSteps.length ? false : true;
        },

        /**
         * Convert line breaks to paragraphs - Baseado na referência
         */
        _lineBreaksToParagraphs(message) {
            return '<p>' + message.replace('\n\n', '</p><p>') + '</p>';
        },

        /**
         * ==================================================================
         * MÉTODOS ADICIONAIS (além da referência)
         * ==================================================================
         */

        /**
         * Check tour status
         */
        _checkTourStatus() {
            const status = localStorage.getItem('nosfir_tour_status');
            if (status === 'completed') {
                this.tourCompleted = true;
                this.$container.hide();
                $('body').addClass('nf-tour-completed');
            }
        },

        /**
         * Save tour status
         */
        _saveTourStatus(status) {
            localStorage.setItem('nosfir_tour_status', status);
            
            // Salvar via AJAX também
            $.post(api.settings.url.ajax, {
                action: 'nosfir_save_tour_status',
                status: status,
                nonce: api.settings.nonce.save
            });
        },

        /**
         * On tour complete
         */
        _onTourComplete() {
            this.tourCompleted = true;
            
            // Show completion message
            this._showCompletionMessage();
            
            // Trigger event
            $(document).trigger('nosfir:tour:complete');
        },

        /**
         * Show completion message
         */
        _showCompletionMessage() {
            const message = `
                <div class="nf-tour-complete">
                    <h3>🎉 Tour Complete!</h3>
                    <p>You've completed the Nosfir theme tour.</p>
                    <p>You can restart the tour anytime from the help menu.</p>
                </div>
            `;
            
            const $message = $(message);
            this.$container.html($message);
            
            setTimeout(() => {
                this._hideTour(true);
            }, 5000);
        },

        /**
         * Add restart tour button
         */
        _addRestartButton() {
            const $button = $(`
                <button class="nf-restart-tour" title="Restart Tour">
                    <span class="dashicons dashicons-controls-repeat"></span>
                </button>
            `);
            
            $('#customize-header-actions').append($button);
            
            $button.on('click', () => {
                this.restartTour();
            });
        },

        /**
         * Restart tour
         */
        restartTour() {
            this.currentStep = -1;
            this.tourCompleted = false;
            this._saveTourStatus('active');
            $('body').removeClass('nf-hidden nf-tour-completed');
            
            if (!this.$container.parent().length) {
                this._setupUI();
            } else {
                this.$container.show();
                this._showNextStep();
            }
        },

        /**
         * On previewer ready
         */
        _onPreviewerReady() {
            // Highlight elementos no preview quando necessário
            const step = this._getCurrentStep();
            if (step && step.previewSelector) {
                this._highlightPreviewElement(step.previewSelector);
            }
        },

        /**
         * Highlight preview element
         */
        _highlightPreviewElement(selector) {
            api.previewer.send('highlight-element', {
                selector: selector,
                duration: 3000
            });
        }
    };

    /**
     * ==================================================================
     * LIVE PREVIEW ENHANCEMENTS
     * ==================================================================
     */

    api.NosfirLivePreview = {
        /**
         * Inicialização
         */
        init() {
            this._bindSettings();
            this._bindControls();
            this._setupPartialRefresh();
            this._setupDevicePreview();
        },

        /**
         * Bind settings para live preview
         */
        _bindSettings() {
            // Cores
            this._bindColorSetting('primary_color', '--primary-color');
            this._bindColorSetting('secondary_color', '--secondary-color');
            this._bindColorSetting('accent_color', '--accent-color');
            this._bindColorSetting('text_color', '--text-color');
            this._bindColorSetting('background_color', 'background-color', 'body');
            
            // Tipografia
            this._bindFontSetting('body_font', '--body-font');
            this._bindFontSetting('heading_font', '--heading-font');
            this._bindNumberSetting('font_size_base', '--font-size-base', 'px');
            
            // Layout
            this._bindSelectSetting('layout_style', this._updateLayoutStyle);
            this._bindNumberSetting('container_width', '--container-width', 'px');
            this._bindToggleSetting('boxed_layout', 'boxed-layout', 'body');
            
            // Header
            this._bindSelectSetting('header_style', this._updateHeaderStyle);
            this._bindToggleSetting('sticky_header', 'sticky-header', 'header');
            this._bindImageSetting('logo', '.site-logo img', 'src');
            
            // Footer
            this._bindSelectSetting('footer_columns', this._updateFooterColumns);
            this._bindTextSetting('footer_text', '.footer-copyright');
        },

        /**
         * Bind color setting
         */
        _bindColorSetting(settingId, cssVar, selector = ':root') {
            api(`nosfir_${settingId}`, function (setting) {
                setting.bind(function (value) {
                    $(selector).css(cssVar, value);
                });
            });
        },

        /**
         * Bind font setting
         */
        _bindFontSetting(settingId, cssVar) {
            api(`nosfir_${settingId}`, function (setting) {
                setting.bind(function (value) {
                    // Load Google Font if needed
                    if (value && value.includes(' ')) {
                        const fontUrl = `https://fonts.googleapis.com/css2?family=${value.replace(' ', '+')}:wght@300;400;500;600;700&display=swap`;
                        
                        if (!$(`link[href*="${value.replace(' ', '+')}"]`).length) {
                            $('head').append(`<link rel="stylesheet" href="${fontUrl}">`);
                        }
                    }
                    
                    $(':root').css(cssVar, `"${value}", sans-serif`);
                });
            });
        },

        /**
         * Bind number setting
         */
        _bindNumberSetting(settingId, cssVar, unit = '', selector = ':root') {
            api(`nosfir_${settingId}`, function (setting) {
                setting.bind(function (value) {
                    $(selector).css(cssVar, value + unit);
                });
            });
        },

        /**
         * Bind select setting
         */
        _bindSelectSetting(settingId, callback) {
            api(`nosfir_${settingId}`, function (setting) {
                setting.bind(callback);
            });
        },

        /**
         * Bind toggle setting
         */
        _bindToggleSetting(settingId, className, selector) {
            api(`nosfir_${settingId}`, function (setting) {
                setting.bind(function (value) {
                    if (value) {
                        $(selector).addClass(className);
                    } else {
                        $(selector).removeClass(className);
                    }
                });
            });
        },

        /**
         * Bind image setting
         */
        _bindImageSetting(settingId, selector, attribute) {
            api(`nosfir_${settingId}`, function (setting) {
                setting.bind(function (value) {
                    if (attribute === 'src') {
                        $(selector).attr(attribute, value);
                    } else if (attribute === 'background-image') {
                        $(selector).css('background-image', `url(${value})`);
                    }
                });
            });
        },

        /**
         * Bind text setting
         */
        _bindTextSetting(settingId, selector) {
            api(`nosfir_${settingId}`, function (setting) {
                setting.bind(function (value) {
                    $(selector).html(value);
                });
            });
        },

        /**
         * Update layout style
         */
        _updateLayoutStyle(value) {
            $('body').removeClass('layout-wide layout-boxed layout-framed')
                     .addClass(`layout-${value}`);
        },

        /**
         * Update header style
         */
        _updateHeaderStyle(value) {
            $('header').removeClass(function(index, className) {
                return (className.match(/(^|\s)header-style-\S+/g) || []).join(' ');
            }).addClass(`header-style-${value}`);
        },

        /**
         * Update footer columns
         */
        _updateFooterColumns(value) {
            $('.footer-widgets').removeClass(function(index, className) {
                return (className.match(/(^|\s)columns-\S+/g) || []).join(' ');
            }).addClass(`columns-${value}`);
        },

        /**
         * Bind controls
         */
        _bindControls() {
            // Adicionar controles customizados
            this._addColorPalettes();
            this._addFontPairs();
            this._addLayoutPresets();
            this._addExportImport();
        },

        /**
         * Add color palettes
         */
        _addColorPalettes() {
            const palettes = [
                {
                    name: 'Default',
                    colors: {
                        primary: '#2c3e50',
                        secondary: '#3498db',
                        accent: '#e74c3c'
                    }
                },
                {
                    name: 'Ocean',
                    colors: {
                        primary: '#006994',
                        secondary: '#00a8cc',
                        accent: '#ff6b6b'
                    }
                },
                {
                    name: 'Forest',
                    colors: {
                        primary: '#2d5016',
                        secondary: '#5a9216',
                        accent: '#8bc34a'
                    }
                },
                {
                    name: 'Sunset',
                    colors: {
                        primary: '#ff6b35',
                        secondary: '#f77b71',
                        accent: '#ffd3b6'
                    }
                }
            ];
            
            // Adicionar UI para selecionar paletas
            const $paletteSelector = $(`
                <div class="nf-palette-selector">
                    <h4>Quick Color Palettes</h4>
                    <div class="palette-options"></div>
                </div>
            `);
            
            palettes.forEach(palette => {
                const $option = $(`
                    <div class="palette-option" data-palette='${JSON.stringify(palette.colors)}'>
                        <span class="palette-name">${palette.name}</span>
                        <div class="palette-colors">
                            <span style="background: ${palette.colors.primary}"></span>
                            <span style="background: ${palette.colors.secondary}"></span>
                            <span style="background: ${palette.colors.accent}"></span>
                        </div>
                    </div>
                `);
                
                $paletteSelector.find('.palette-options').append($option);
            });
            
            // Adicionar ao customizer
            $('#customize-theme-controls').prepend($paletteSelector);
            
            // Bind click events
            $paletteSelector.on('click', '.palette-option', function() {
                const colors = $(this).data('palette');
                
                api('nosfir_primary_color').set(colors.primary);
                api('nosfir_secondary_color').set(colors.secondary);
                api('nosfir_accent_color').set(colors.accent);
                
                $('.palette-option').removeClass('active');
                $(this).addClass('active');
            });
        },

        /**
         * Add font pairs
         */
        _addFontPairs() {
            const fontPairs = [
                {
                    name: 'Classic',
                    body: 'Georgia',
                    heading: 'Helvetica'
                },
                {
                    name: 'Modern',
                    body: 'Open Sans',
                    heading: 'Montserrat'
                },
                {
                    name: 'Elegant',
                    body: 'Lora',
                    heading: 'Playfair Display'
                },
                {
                    name: 'Clean',
                    body: 'Roboto',
                    heading: 'Roboto Slab'
                }
            ];
            
            // Implementar seletor de pares de fontes similar às paletas
        },

        /**
         * Add layout presets
         */
        _addLayoutPresets() {
            const presets = [
                {
                    name: 'Blog',
                    settings: {
                        layout_style: 'wide',
                        sidebar_position: 'right',
                        container_width: 1200
                    }
                },
                {
                    name: 'Portfolio',
                    settings: {
                        layout_style: 'wide',
                        sidebar_position: 'none',
                        container_width: 1400
                    }
                },
                {
                    name: 'Business',
                    settings: {
                        layout_style: 'boxed',
                        sidebar_position: 'left',
                        container_width: 1170
                    }
                }
            ];
            
            // Implementar presets de layout
        },

        /**
         * Add export/import functionality
         */
        _addExportImport() {
            const $controls = $(`
                <div class="nf-export-import">
                    <button class="button" id="nf-export-settings">Export Settings</button>
                    <button class="button" id="nf-import-settings">Import Settings</button>
                    <input type="file" id="nf-import-file" accept=".json" style="display: none;">
                </div>
            `);
            
            $('#customize-header-actions').append($controls);
            
            // Export handler
            $('#nf-export-settings').on('click', () => {
                this._exportSettings();
            });
            
            // Import handler
            $('#nf-import-settings').on('click', () => {
                $('#nf-import-file').click();
            });
            
            $('#nf-import-file').on('change', (e) => {
                this._importSettings(e.target.files[0]);
            });
        },

        /**
         * Export settings
         */
        _exportSettings() {
            const settings = {};
            
            // Coletar todas as configurações do tema
            api.each(function(setting) {
                if (setting.id.startsWith('nosfir_')) {
                    settings[setting.id] = setting.get();
                }
            });
            
            // Criar arquivo JSON
            const blob = new Blob([JSON.stringify(settings, null, 2)], {
                type: 'application/json'
            });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `nosfir-customizer-${Date.now()}.json`;
            a.click();
            URL.revokeObjectURL(url);
        },

        /**
         * Import settings
         */
        _importSettings(file) {
            if (!file) return;
            
            const reader = new FileReader();
            reader.onload = (e) => {
                try {
                    const settings = JSON.parse(e.target.result);
                    
                    // Aplicar configurações importadas
                    Object.keys(settings).forEach(key => {
                        if (api.has(key)) {
                            api(key).set(settings[key]);
                        }
                    });
                    
                    // Mostrar mensagem de sucesso
                    this._showNotification('Settings imported successfully!', 'success');
                } catch (error) {
                    this._showNotification('Invalid settings file', 'error');
                }
            };
            reader.readAsText(file);
        },

        /**
         * Setup partial refresh
         */
        _setupPartialRefresh() {
            if (!api.selectiveRefresh) return;
            
            // Site title
            api.selectiveRefresh.partialConstructor.blogname = api.selectiveRefresh.Partial.extend({
                refresh: function() {
                    $('.site-title').text(api('blogname').get());
                }
            });
            
            // Site description
            api.selectiveRefresh.partialConstructor.blogdescription = api.selectiveRefresh.Partial.extend({
                refresh: function() {
                    $('.site-description').text(api('blogdescription').get());
                }
            });
            
            // Adicionar mais partial refreshes conforme necessário
        },

        /**
         * Setup device preview
         */
        _setupDevicePreview() {
            const devices = ['desktop', 'tablet', 'mobile'];
            
            devices.forEach(device => {
                api.previewedDevice.bind(function(newDevice) {
                    if (newDevice === device) {
                        $('body').removeClass('preview-desktop preview-tablet preview-mobile')
                                 .addClass(`preview-${device}`);
                        
                        // Trigger evento customizado
                        $(document).trigger('nosfir:device:change', [device]);
                    }
                });
            });
        },

        /**
         * Show notification
         */
        _showNotification(message, type = 'success') {
            const $notification = $(`
                <div class="nf-customizer-notification ${type}">
                    ${message}
                </div>
            `);
            
            $('#customize-preview').append($notification);
            
            setTimeout(() => {
                $notification.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }
    };

    /**
     * ==================================================================
     * CUSTOM CONTROLS
     * ==================================================================
     */

    /**
     * Range Slider Control
     */
    api.controlConstructor['nosfir-range'] = api.Control.extend({
        ready: function() {
            const control = this;
            const $slider = control.container.find('.nf-range-slider');
            const $input = control.container.find('.nf-range-input');
            const $value = control.container.find('.nf-range-value');
            
            $slider.on('input', function() {
                const value = $(this).val();
                $input.val(value);
                $value.text(value);
                control.setting.set(value);
            });
            
            $input.on('change', function() {
                const value = $(this).val();
                $slider.val(value);
                $value.text(value);
                control.setting.set(value);
            });
        }
    });

    /**
     * Gradient Picker Control
     */
    api.controlConstructor['nosfir-gradient'] = api.Control.extend({
        ready: function() {
            const control = this;
            
            // Implementar gradient picker
            control.container.find('.nf-gradient-picker').each(function() {
                // Lógica do gradient picker
            });
        }
    });

    /**
     * Icon Picker Control
     */
    api.controlConstructor['nosfir-icon-picker'] = api.Control.extend({
        ready: function() {
            const control = this;
            const $button = control.container.find('.nf-icon-picker-button');
            const $preview = control.container.find('.nf-icon-preview');
            
            $button.on('click', function() {
                // Abrir modal de seleção de ícones
                control.openIconPicker();
            });
        },
        
        openIconPicker: function() {
            // Implementar modal de ícones
        }
    });

    /**
     * ==================================================================
     * SHORTCUTS E HELPERS
     * ==================================================================
     */

    /**
     * Keyboard shortcuts
     */
    api.NosfirShortcuts = {
        init() {
            $(document).on('keydown', (e) => {
                // Ctrl/Cmd + S para salvar
                if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                    e.preventDefault();
                    api.previewer.save();
                }
                
                // Ctrl/Cmd + Z para desfazer
                if ((e.ctrlKey || e.metaKey) && e.key === 'z') {
                    e.preventDefault();
                    this.undo();
                }
                
                // Ctrl/Cmd + Shift + Z para refazer
                if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'z') {
                    e.preventDefault();
                    this.redo();
                }
                
                // ESC para fechar painéis
                if (e.key === 'Escape') {
                    this.closeActivePanels();
                }
            });
        },
        
        undo() {
            // Implementar sistema de undo
        },
        
        redo() {
            // Implementar sistema de redo
        },
        
        closeActivePanels() {
            api.section.each(function(section) {
                if (section.expanded()) {
                    section.collapse();
                }
            });
            
            api.panel.each(function(panel) {
                if (panel.expanded()) {
                    panel.collapse();
                }
            });
        }
    };

    /**
     * ==================================================================
     * SEARCH FUNCTIONALITY
     * ==================================================================
     */

    api.NosfirSearch = {
        init() {
            this._addSearchBox();
        },
        
        _addSearchBox() {
            const $searchBox = $(`
                <div class="nf-customizer-search">
                    <input type="search" placeholder="Search settings..." />
                    <span class="dashicons dashicons-search"></span>
                </div>
            `);
            
            $('#customize-header-actions').prepend($searchBox);
            
            const $input = $searchBox.find('input');
            let searchTimeout;
            
            $input.on('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this._performSearch($input.val());
                }, 300);
            });
        },
        
        _performSearch(query) {
            if (!query) {
                $('.control-section').show();
                $('.customize-control').show();
                return;
            }
            
            query = query.toLowerCase();
            
            $('.customize-control').each(function() {
                const $control = $(this);
                const label = $control.find('label').text().toLowerCase();
                const description = $control.find('.description').text().toLowerCase();
                
                if (label.includes(query) || description.includes(query)) {
                    $control.show();
                    $control.closest('.control-section').show();
                } else {
                    $control.hide();
                }
            });
        }
    };

    /**
     * ==================================================================
     * INICIALIZAÇÃO - Compatível com referência
     * ==================================================================
     */

    $(document).ready(function () {
        // Inicializar Guided Tour - Baseado na referência
        api.NFGuidedTour.init();
        
        // Inicializar Live Preview
        api.NosfirLivePreview.init();
        
        // Inicializar Shortcuts
        api.NosfirShortcuts.init();
        
        // Inicializar Search
        api.NosfirSearch.init();
        
        // Trigger ready event
        $(document).trigger('nosfir:customizer:ready');
    });

    /**
     * ==================================================================
     * COMPATIBILIDADE COM REFERÊNCIA ORIGINAL
     * ==================================================================
     */

    // Manter compatibilidade com código legado se necessário
    if (typeof _wpCustomizeSFGuidedTourSteps !== 'undefined') {
        api.SFGuidedTour = api.NFGuidedTour;
        api.SFGuidedTourSteps = api.NFGuidedTourSteps;
    }

})(window.wp, jQuery);