/**
 * Nosfir Theme - Plugin Install JavaScript
 * 
 * @package     Nosfir
 * @subpackage  Admin/Plugins
 * @version     1.0.0
 * @author      David Creator
 * 
 * Este arquivo gerencia a instalação e ativação de plugins recomendados/requeridos
 * Baseado no sistema de plugins do WordPress com melhorias
 */

(function (wp, $) {
    'use strict';

    // Verificar se WordPress está disponível
    if (!wp) {
        return;
    }

    /**
     * ==================================================================
     * OBJETO PRINCIPAL - NOSFIR PLUGIN MANAGER
     * ==================================================================
     */
    
    const NosfirPluginManager = {
        
        // Configurações
        config: {
            ajaxUrl: ajaxurl || '/wp-admin/admin-ajax.php',
            nonce: nosfirPlugins?.nonce || '',
            plugins: nosfirPlugins?.recommended || [],
            requiredPlugins: nosfirPlugins?.required || [],
            strings: nosfirPlugins?.strings || {},
            debug: nosfirPlugins?.debug || false
        },

        // Estado
        state: {
            installing: [],
            activating: [],
            updating: [],
            errors: [],
            completed: [],
            queue: [],
            batchProcessing: false,
            currentPlugin: null
        },

        // Cache
        cache: {
            pluginData: {},
            installStatus: {}
        },

        /**
         * Inicialização
         */
        init() {
            this.log('Plugin Manager initialized');
            this.bindEvents();
            this.checkPluginStatuses();
            this.setupBulkActions();
            this.initializeProgressTracking();
        },

        /**
         * Logger condicional
         */
        log(message, type = 'log') {
            if (this.config.debug) {
                console[type]('[Nosfir Plugins]:', message);
            }
        },

        /**
         * ==================================================================
         * EVENT BINDING - Baseado na referência e expandido
         * ==================================================================
         */
        
        bindEvents() {
            const self = this;

            $(function () {
                // Install plugin - Baseado na referência original
                $(document).on('click', '.nf-install-now', function (event) {
                    const $button = $(event.target);

                    // Se já está ativado, permitir click normal - Referência
                    if ($button.hasClass('activate-now')) {
                        return true;
                    }

                    event.preventDefault();

                    // Verificar se já está processando - Referência
                    if ($button.hasClass('updating-message') || 
                        $button.hasClass('button-disabled')) {
                        return;
                    }

                    // Credenciais do sistema de arquivos - Referência
                    if (wp.updates.shouldRequestFilesystemCredentials && 
                        !wp.updates.ajaxLocked) {
                        
                        wp.updates.requestFilesystemCredentials(event);

                        $(document).on('credential-modal-cancel', function () {
                            const $message = $('.nf-install-now.updating-message');
                            
                            $message
                                .removeClass('updating-message')
                                .text(wp.updates.l10n.installNow);
                            
                            wp.a11y.speak(wp.updates.l10n.updateCancel, 'polite');
                        });
                    }

                    // Instalar plugin - Baseado na referência
                    self.installPlugin($button);
                });

                // Ativar plugin
                $(document).on('click', '.nf-activate-now', function (event) {
                    event.preventDefault();
                    const $button = $(event.target);
                    
                    if (!$button.hasClass('button-disabled')) {
                        self.activatePlugin($button);
                    }
                });

                // Atualizar plugin
                $(document).on('click', '.nf-update-now', function (event) {
                    event.preventDefault();
                    const $button = $(event.target);
                    
                    if (!$button.hasClass('button-disabled')) {
                        self.updatePlugin($button);
                    }
                });

                // Desativar plugin
                $(document).on('click', '.nf-deactivate-now', function (event) {
                    event.preventDefault();
                    const $button = $(event.target);
                    
                    if (!$button.hasClass('button-disabled')) {
                        self.deactivatePlugin($button);
                    }
                });

                // Instalar todos os plugins recomendados
                $(document).on('click', '#nf-install-all-plugins', function (event) {
                    event.preventDefault();
                    self.installAllPlugins();
                });

                // Ativar todos os plugins instalados
                $(document).on('click', '#nf-activate-all-plugins', function (event) {
                    event.preventDefault();
                    self.activateAllPlugins();
                });

                // Importar plugins via arquivo
                $(document).on('change', '#nf-import-plugins', function (event) {
                    self.importPluginsList(event.target.files[0]);
                });

                // Retry failed installations
                $(document).on('click', '.nf-retry-install', function (event) {
                    event.preventDefault();
                    const $button = $(event.target);
                    self.retryInstallation($button);
                });

                // Ver detalhes do plugin
                $(document).on('click', '.nf-plugin-details', function (event) {
                    event.preventDefault();
                    const $link = $(event.target);
                    self.showPluginDetails($link.data('slug'));
                });

                // Buscar plugins
                $(document).on('input', '#nf-plugin-search', function () {
                    const query = $(this).val();
                    self.searchPlugins(query);
                });

                // Filtrar plugins por categoria
                $(document).on('change', '#nf-plugin-filter', function () {
                    const category = $(this).val();
                    self.filterPlugins(category);
                });

                // Checkbox para seleção em massa
                $(document).on('change', '.nf-plugin-checkbox', function () {
                    self.updateBulkActions();
                });

                // Select all/none
                $(document).on('change', '#nf-select-all-plugins', function () {
                    const checked = $(this).prop('checked');
                    $('.nf-plugin-checkbox').prop('checked', checked);
                    self.updateBulkActions();
                });
            });

            // WordPress updates events
            this.bindWordPressEvents();
        },

        /**
         * ==================================================================
         * INSTALAÇÃO DE PLUGINS - Baseado na referência e melhorado
         * ==================================================================
         */

        /**
         * Instalar plugin individual
         */
        installPlugin($button) {
            const slug = $button.data('slug');
            const name = $button.data('name') || slug;

            // Atualizar UI
            this.setButtonState($button, 'installing');
            this.log(`Installing plugin: ${slug}`);

            // Adicionar ao estado
            this.state.installing.push(slug);
            this.state.currentPlugin = slug;

            // Mostrar progresso
            this.showProgress('install', slug, 0);

            // Usar API do WordPress quando disponível - Baseado na referência
            if (wp.updates && wp.updates.installPlugin) {
                // Método WordPress nativo
                wp.updates.installPlugin({
                    slug: slug,
                    success: (response) => {
                        this.onInstallSuccess($button, response);
                    },
                    error: (response) => {
                        this.onInstallError($button, response);
                    }
                });
            } else {
                // Fallback para AJAX customizado
                this.installPluginAjax($button, slug);
            }
        },

        /**
         * Instalação via AJAX customizado
         */
        installPluginAjax($button, slug) {
            const self = this;

            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'nosfir_install_plugin',
                    slug: slug,
                    nonce: this.config.nonce
                },
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    
                    // Upload progress
                    xhr.addEventListener('progress', function(evt) {
                        if (evt.lengthComputable) {
                            const percentComplete = evt.loaded / evt.total * 100;
                            self.showProgress('install', slug, percentComplete);
                        }
                    }, false);
                    
                    return xhr;
                },
                success: function(response) {
                    if (response.success) {
                        self.onInstallSuccess($button, response.data);
                    } else {
                        self.onInstallError($button, response.data);
                    }
                },
                error: function(xhr, status, error) {
                    self.onInstallError($button, {
                        errorMessage: error,
                        errorCode: status
                    });
                }
            });
        },

        /**
         * Sucesso na instalação
         */
        onInstallSuccess($button, response) {
            this.log(`Plugin installed successfully: ${response.slug}`);
            
            // Remover do array de instalação
            this.state.installing = this.state.installing.filter(s => s !== response.slug);
            this.state.completed.push(response.slug);
            
            // Atualizar cache
            this.cache.installStatus[response.slug] = 'installed';
            
            // Atualizar UI
            this.setButtonState($button, 'installed');
            this.showProgress('install', response.slug, 100);
            
            // Mostrar botão de ativação
            this.showActivateButton($button);
            
            // Notificação
            this.showNotification(`${response.pluginName || response.slug} installed successfully!`, 'success');
            
            // Disparar evento customizado
            $(document).trigger('nosfir:plugin:installed', [response]);
            
            // Processar próximo da fila se houver
            this.processQueue();
        },

        /**
         * Erro na instalação
         */
        onInstallError($button, error) {
            const slug = $button.data('slug');
            
            this.log(`Plugin installation failed: ${slug}`, 'error');
            this.log(error, 'error');
            
            // Remover do array de instalação
            this.state.installing = this.state.installing.filter(s => s !== slug);
            this.state.errors.push({
                slug: slug,
                error: error,
                timestamp: Date.now()
            });
            
            // Atualizar UI
            this.setButtonState($button, 'error');
            this.hideProgress('install', slug);
            
            // Mostrar botão de retry
            this.showRetryButton($button);
            
            // Notificação de erro
            const errorMessage = error.errorMessage || 'Installation failed';
            this.showNotification(`Failed to install ${slug}: ${errorMessage}`, 'error');
            
            // Disparar evento customizado
            $(document).trigger('nosfir:plugin:install:error', [slug, error]);
            
            // Continuar com a fila
            this.processQueue();
        },

        /**
         * ==================================================================
         * ATIVAÇÃO DE PLUGINS
         * ==================================================================
         */

        /**
         * Ativar plugin
         */
        activatePlugin($button) {
            const slug = $button.data('slug');
            const plugin = $button.data('plugin'); // arquivo principal do plugin
            
            this.setButtonState($button, 'activating');
            this.state.activating.push(slug);
            
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'nosfir_activate_plugin',
                    plugin: plugin,
                    slug: slug,
                    nonce: this.config.nonce
                },
                success: (response) => {
                    if (response.success) {
                        this.onActivateSuccess($button, response.data);
                    } else {
                        this.onActivateError($button, response.data);
                    }
                },
                error: (xhr, status, error) => {
                    this.onActivateError($button, {
                        errorMessage: error
                    });
                }
            });
        },

        /**
         * Sucesso na ativação
         */
        onActivateSuccess($button, response) {
            this.log(`Plugin activated: ${response.slug}`);
            
            // Atualizar estado
            this.state.activating = this.state.activating.filter(s => s !== response.slug);
            this.cache.installStatus[response.slug] = 'active';
            
            // Atualizar UI
            this.setButtonState($button, 'activated');
            
            // Mostrar botão de desativação
            this.showDeactivateButton($button);
            
            // Notificação
            this.showNotification(`${response.slug} activated successfully!`, 'success');
            
            // Evento customizado
            $(document).trigger('nosfir:plugin:activated', [response]);
            
            // Processar fila
            this.processQueue();
        },

        /**
         * Erro na ativação
         */
        onActivateError($button, error) {
            const slug = $button.data('slug');
            
            this.log(`Plugin activation failed: ${slug}`, 'error');
            
            // Atualizar estado
            this.state.activating = this.state.activating.filter(s => s !== slug);
            
            // Atualizar UI
            this.setButtonState($button, 'error');
            
            // Notificação
            this.showNotification(`Failed to activate ${slug}: ${error.errorMessage}`, 'error');
            
            // Evento customizado
            $(document).trigger('nosfir:plugin:activate:error', [slug, error]);
        },

        /**
         * ==================================================================
         * ATUALIZAÇÃO DE PLUGINS
         * ==================================================================
         */

        /**
         * Atualizar plugin
         */
        updatePlugin($button) {
            const slug = $button.data('slug');
            
            this.setButtonState($button, 'updating');
            this.state.updating.push(slug);
            
            // Usar WordPress Updates API
            if (wp.updates && wp.updates.updatePlugin) {
                wp.updates.updatePlugin({
                    plugin: $button.data('plugin'),
                    slug: slug,
                    success: (response) => {
                        this.onUpdateSuccess($button, response);
                    },
                    error: (response) => {
                        this.onUpdateError($button, response);
                    }
                });
            } else {
                // Fallback AJAX
                this.updatePluginAjax($button, slug);
            }
        },

        /**
         * Update via AJAX
         */
        updatePluginAjax($button, slug) {
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'nosfir_update_plugin',
                    slug: slug,
                    plugin: $button.data('plugin'),
                    nonce: this.config.nonce
                },
                success: (response) => {
                    if (response.success) {
                        this.onUpdateSuccess($button, response.data);
                    } else {
                        this.onUpdateError($button, response.data);
                    }
                }
            });
        },

        /**
         * Sucesso na atualização
         */
        onUpdateSuccess($button, response) {
            this.log(`Plugin updated: ${response.slug}`);
            
            // Atualizar estado
            this.state.updating = this.state.updating.filter(s => s !== response.slug);
            
            // Atualizar UI
            this.setButtonState($button, 'updated');
            
            // Notificação
            this.showNotification(`${response.slug} updated successfully!`, 'success');
            
            // Evento customizado
            $(document).trigger('nosfir:plugin:updated', [response]);
        },

        /**
         * Erro na atualização
         */
        onUpdateError($button, error) {
            const slug = $button.data('slug');
            
            this.log(`Plugin update failed: ${slug}`, 'error');
            
            // Atualizar estado
            this.state.updating = this.state.updating.filter(s => s !== slug);
            
            // Atualizar UI
            this.setButtonState($button, 'error');
            
            // Notificação
            this.showNotification(`Failed to update ${slug}`, 'error');
        },

        /**
         * ==================================================================
         * DESATIVAÇÃO DE PLUGINS
         * ==================================================================
         */

        /**
         * Desativar plugin
         */
        deactivatePlugin($button) {
            const slug = $button.data('slug');
            const plugin = $button.data('plugin');
            
            this.setButtonState($button, 'deactivating');
            
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'nosfir_deactivate_plugin',
                    plugin: plugin,
                    slug: slug,
                    nonce: this.config.nonce
                },
                success: (response) => {
                    if (response.success) {
                        this.log(`Plugin deactivated: ${slug}`);
                        
                        // Atualizar UI
                        this.setButtonState($button, 'deactivated');
                        this.showActivateButton($button);
                        
                        // Notificação
                        this.showNotification(`${slug} deactivated`, 'info');
                    }
                }
            });
        },

        /**
         * ==================================================================
         * INSTALAÇÃO EM LOTE
         * ==================================================================
         */

        /**
         * Instalar todos os plugins
         */
        installAllPlugins() {
            const $buttons = $('.nf-install-now:not(.button-disabled)');
            
            if ($buttons.length === 0) {
                this.showNotification('No plugins to install', 'info');
                return;
            }
            
            // Criar fila
            this.state.queue = [];
            $buttons.each((index, button) => {
                this.state.queue.push($(button));
            });
            
            // Mostrar progresso geral
            this.showBatchProgress(0, this.state.queue.length);
            
            // Iniciar processamento
            this.state.batchProcessing = true;
            this.processQueue();
        },

        /**
         * Processar fila de instalação
         */
        processQueue() {
            if (!this.state.batchProcessing || this.state.queue.length === 0) {
                this.onBatchComplete();
                return;
            }
            
            // Processar próximo da fila
            const $button = this.state.queue.shift();
            this.installPlugin($button);
            
            // Atualizar progresso
            const completed = this.state.completed.length;
            const total = completed + this.state.queue.length + 1;
            this.showBatchProgress(completed, total);
        },

        /**
         * Batch complete
         */
        onBatchComplete() {
            this.state.batchProcessing = false;
            this.hideBatchProgress();
            
            const successCount = this.state.completed.length;
            const errorCount = this.state.errors.length;
            
            let message = `Batch installation complete. `;
            if (successCount > 0) {
                message += `${successCount} plugins installed successfully. `;
            }
            if (errorCount > 0) {
                message += `${errorCount} plugins failed.`;
            }
            
            this.showNotification(message, errorCount > 0 ? 'warning' : 'success');
            
            // Reset estado
            this.state.completed = [];
            this.state.errors = [];
        },

        /**
         * Ativar todos os plugins
         */
        activateAllPlugins() {
            const $buttons = $('.nf-activate-now:not(.button-disabled)');
            
            if ($buttons.length === 0) {
                this.showNotification('No plugins to activate', 'info');
                return;
            }
            
            // Criar fila de ativação
            const activateQueue = [];
            $buttons.each((index, button) => {
                activateQueue.push($(button));
            });
            
            // Processar sequencialmente
            this.processActivationQueue(activateQueue);
        },

        /**
         * Processar fila de ativação
         */
        processActivationQueue(queue) {
            if (queue.length === 0) {
                this.showNotification('All plugins activated!', 'success');
                return;
            }
            
            const $button = queue.shift();
            
            // Ativar e continuar com o próximo
            this.activatePlugin($button);
            
            // Aguardar e processar próximo
            setTimeout(() => {
                this.processActivationQueue(queue);
            }, 1000);
        },

        /**
         * ==================================================================
         * VERIFICAÇÃO DE STATUS
         * ==================================================================
         */

        /**
         * Verificar status de todos os plugins
         */
        checkPluginStatuses() {
            const pluginCards = $('.nf-plugin-card');
            
            if (pluginCards.length === 0) return;
            
            const slugs = [];
            pluginCards.each(function() {
                slugs.push($(this).data('slug'));
            });
            
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'nosfir_check_plugin_statuses',
                    slugs: slugs,
                    nonce: this.config.nonce
                },
                success: (response) => {
                    if (response.success) {
                        this.updatePluginStatuses(response.data);
                    }
                }
            });
        },

        /**
         * Atualizar status dos plugins na UI
         */
        updatePluginStatuses(statuses) {
            Object.keys(statuses).forEach(slug => {
                const status = statuses[slug];
                const $card = $(`.nf-plugin-card[data-slug="${slug}"]`);
                
                // Atualizar badge de status
                $card.find('.plugin-status')
                     .removeClass('status-active status-inactive status-not-installed')
                     .addClass(`status-${status}`)
                     .text(status);
                
                // Atualizar botões
                this.updatePluginButtons($card, status);
                
                // Atualizar cache
                this.cache.installStatus[slug] = status;
            });
        },

        /**
         * Atualizar botões baseado no status
         */
        updatePluginButtons($card, status) {
            const $buttonContainer = $card.find('.plugin-actions');
            
            $buttonContainer.empty();
            
            switch(status) {
                case 'not-installed':
                    $buttonContainer.html(`
                        <button class="button nf-install-now" 
                                data-slug="${$card.data('slug')}"
                                data-name="${$card.data('name')}">
                            Install Now
                        </button>
                    `);
                    break;
                    
                case 'inactive':
                    $buttonContainer.html(`
                        <button class="button nf-activate-now" 
                                data-slug="${$card.data('slug')}"
                                data-plugin="${$card.data('plugin')}">
                            Activate
                        </button>
                    `);
                    break;
                    
                case 'active':
                    $buttonContainer.html(`
                        <button class="button button-disabled" disabled>
                            Active
                        </button>
                        <button class="button nf-deactivate-now" 
                                data-slug="${$card.data('slug')}"
                                data-plugin="${$card.data('plugin')}">
                            Deactivate
                        </button>
                    `);
                    break;
                    
                case 'update-available':
                    $buttonContainer.html(`
                        <button class="button nf-update-now" 
                                data-slug="${$card.data('slug')}"
                                data-plugin="${$card.data('plugin')}">
                            Update Now
                        </button>
                    `);
                    break;
            }
        },

        /**
         * ==================================================================
         * UI HELPERS
         * ==================================================================
         */

        /**
         * Definir estado do botão
         */
        setButtonState($button, state) {
            const states = {
                'installing': {
                    text: 'Installing...',
                    class: 'updating-message',
                    disabled: true
                },
                'installed': {
                    text: 'Installed',
                    class: 'updated-message',
                    disabled: true
                },
                'activating': {
                    text: 'Activating...',
                    class: 'updating-message',
                    disabled: true
                },
                'activated': {
                    text: 'Active',
                    class: 'activated',
                    disabled: true
                },
                'updating': {
                    text: 'Updating...',
                    class: 'updating-message',
                    disabled: true
                },
                'updated': {
                    text: 'Updated',
                    class: 'updated-message',
                    disabled: true
                },
                'deactivating': {
                    text: 'Deactivating...',
                    class: 'updating-message',
                    disabled: true
                },
                'deactivated': {
                    text: 'Deactivated',
                    class: 'deactivated',
                    disabled: false
                },
                'error': {
                    text: 'Failed',
                    class: 'error-message',
                    disabled: false
                }
            };
            
            const stateConfig = states[state];
            
            if (stateConfig) {
                $button
                    .removeClass('updating-message updated-message activated deactivated error-message')
                    .addClass(stateConfig.class)
                    .text(stateConfig.text)
                    .prop('disabled', stateConfig.disabled);
                
                if (stateConfig.disabled) {
                    $button.addClass('button-disabled');
                } else {
                    $button.removeClass('button-disabled');
                }
            }
        },

        /**
         * Mostrar botão de ativação
         */
        showActivateButton($button) {
            const $newButton = $(`
                <button class="button button-primary nf-activate-now" 
                        data-slug="${$button.data('slug')}"
                        data-plugin="${$button.data('plugin') || $button.data('slug') + '/' + $button.data('slug') + '.php'}">
                    Activate
                </button>
            `);
            
            $button.replaceWith($newButton);
        },

        /**
         * Mostrar botão de desativação
         */
        showDeactivateButton($button) {
            const $container = $button.parent();
            
            $button
                .removeClass('button-primary')
                .addClass('button-disabled')
                .text('Active')
                .prop('disabled', true);
            
            const $deactivateButton = $(`
                <button class="button nf-deactivate-now" 
                        data-slug="${$button.data('slug')}"
                        data-plugin="${$button.data('plugin')}">
                    Deactivate
                </button>
            `);
            
            $container.append($deactivateButton);
        },

        /**
         * Mostrar botão de retry
         */
        showRetryButton($button) {
            const $retryButton = $(`
                <button class="button nf-retry-install" 
                        data-slug="${$button.data('slug')}">
                    Retry
                </button>
            `);
            
            $button.replaceWith($retryButton);
        },

        /**
         * Retry installation
         */
        retryInstallation($button) {
            const slug = $button.data('slug');
            const $installButton = $(`
                <button class="button nf-install-now" 
                        data-slug="${slug}">
                    Install Now
                </button>
            `);
            
            $button.replaceWith($installButton);
            $installButton.trigger('click');
        },

        /**
         * ==================================================================
         * PROGRESSO
         * ==================================================================
         */

        /**
         * Mostrar progresso
         */
        showProgress(type, identifier, percentage) {
            let $progress = $(`#nf-progress-${type}-${identifier}`);
            
            if (!$progress.length) {
                $progress = $(`
                    <div id="nf-progress-${type}-${identifier}" class="nf-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 0%"></div>
                        </div>
                        <span class="progress-text">0%</span>
                    </div>
                `);
                
                // Adicionar ao elemento apropriado
                const $card = $(`.nf-plugin-card[data-slug="${identifier}"]`);
                if ($card.length) {
                    $card.append($progress);
                }
            }
            
            $progress.find('.progress-fill').css('width', percentage + '%');
            $progress.find('.progress-text').text(Math.round(percentage) + '%');
            
            if (percentage >= 100) {
                setTimeout(() => {
                    this.hideProgress(type, identifier);
                }, 1000);
            }
        },

        /**
         * Esconder progresso
         */
        hideProgress(type, identifier) {
            $(`#nf-progress-${type}-${identifier}`).fadeOut(300, function() {
                $(this).remove();
            });
        },

        /**
         * Mostrar progresso em lote
         */
        showBatchProgress(completed, total) {
            let $progress = $('#nf-batch-progress');
            
            if (!$progress.length) {
                $progress = $(`
                    <div id="nf-batch-progress" class="nf-batch-progress">
                        <h3>Batch Installation Progress</h3>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 0%"></div>
                        </div>
                        <span class="progress-text">0 of ${total} completed</span>
                    </div>
                `);
                
                $('.nf-plugins-header').after($progress);
            }
            
            const percentage = (completed / total) * 100;
            $progress.find('.progress-fill').css('width', percentage + '%');
            $progress.find('.progress-text').text(`${completed} of ${total} completed`);
        },

        /**
         * Esconder progresso em lote
         */
        hideBatchProgress() {
            $('#nf-batch-progress').fadeOut(300, function() {
                $(this).remove();
            });
        },

        /**
         * ==================================================================
         * NOTIFICAÇÕES
         * ==================================================================
         */

        /**
         * Mostrar notificação
         */
        showNotification(message, type = 'info', duration = 5000) {
            const $notification = $(`
                <div class="notice notice-${type} is-dismissible nf-plugin-notice">
                    <p>${message}</p>
                </div>
            `);
            
            $('.wrap h1').first().after($notification);
            
            // Auto dismiss
            if (duration) {
                setTimeout(() => {
                    $notification.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, duration);
            }
            
            // Manual dismiss
            $notification.on('click', '.notice-dismiss', function() {
                $notification.fadeOut(300, function() {
                    $(this).remove();
                });
            });
        },

        /**
         * ==================================================================
         * DETALHES DO PLUGIN
         * ==================================================================
         */

        /**
         * Mostrar detalhes do plugin
         */
        showPluginDetails(slug) {
            // Buscar informações do plugin
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'nosfir_get_plugin_info',
                    slug: slug,
                    nonce: this.config.nonce
                },
                success: (response) => {
                    if (response.success) {
                        this.displayPluginModal(response.data);
                    }
                }
            });
        },

        /**
         * Exibir modal com detalhes
         */
        displayPluginModal(pluginInfo) {
            const modalHtml = `
                <div class="nf-plugin-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2>${pluginInfo.name}</h2>
                            <button class="modal-close">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="plugin-info">
                                <p>${pluginInfo.description}</p>
                                <ul>
                                    <li><strong>Version:</strong> ${pluginInfo.version}</li>
                                    <li><strong>Author:</strong> ${pluginInfo.author}</li>
                                    <li><strong>Downloads:</strong> ${pluginInfo.downloaded}</li>
                                    <li><strong>Rating:</strong> ${pluginInfo.rating}/5</li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="button button-primary nf-install-modal" 
                                    data-slug="${pluginInfo.slug}">
                                Install Now
                            </button>
                            <a href="${pluginInfo.homepage}" target="_blank" class="button">
                                More Details
                            </a>
                        </div>
                    </div>
                </div>
            `;
            
            $('body').append(modalHtml);
            
            const $modal = $('.nf-plugin-modal');
            $modal.fadeIn(300);
            
            // Bind close events
            $modal.on('click', '.modal-close', () => {
                $modal.fadeOut(300, function() {
                    $(this).remove();
                });
            });
            
            // Bind install from modal
            $modal.on('click', '.nf-install-modal', (e) => {
                const $button = $(e.target);
                this.installPlugin($button);
                $modal.find('.modal-close').trigger('click');
            });
        },

        /**
         * ==================================================================
         * BUSCA E FILTROS
         * ==================================================================
         */

        /**
         * Buscar plugins
         */
        searchPlugins(query) {
            clearTimeout(this.searchTimeout);
            
            this.searchTimeout = setTimeout(() => {
                if (!query) {
                    $('.nf-plugin-card').show();
                    return;
                }
                
                query = query.toLowerCase();
                
                $('.nf-plugin-card').each(function() {
                    const $card = $(this);
                    const name = $card.find('.plugin-name').text().toLowerCase();
                    const description = $card.find('.plugin-description').text().toLowerCase();
                    
                    if (name.includes(query) || description.includes(query)) {
                        $card.show();
                    } else {
                        $card.hide();
                    }
                });
                
                // Mostrar mensagem se nenhum resultado
                if ($('.nf-plugin-card:visible').length === 0) {
                    this.showNoResultsMessage();
                } else {
                    this.hideNoResultsMessage();
                }
            }, 300);
        },

        /**
         * Filtrar plugins por categoria
         */
        filterPlugins(category) {
            if (!category || category === 'all') {
                $('.nf-plugin-card').show();
                return;
            }
            
            $('.nf-plugin-card').each(function() {
                const $card = $(this);
                const categories = $card.data('categories') || '';
                
                if (categories.includes(category)) {
                    $card.show();
                } else {
                    $card.hide();
                }
            });
        },

        /**
         * Mensagem de sem resultados
         */
        showNoResultsMessage() {
            if (!$('.nf-no-results').length) {
                const message = `
                    <div class="nf-no-results">
                        <p>No plugins found matching your search.</p>
                    </div>
                `;
                $('.nf-plugins-grid').append(message);
            }
        },

        hideNoResultsMessage() {
            $('.nf-no-results').remove();
        },

        /**
         * ==================================================================
         * AÇÕES EM MASSA
         * ==================================================================
         */

        /**
         * Setup bulk actions
         */
        setupBulkActions() {
            const $bulkActions = $(`
                <div class="nf-bulk-actions" style="display: none;">
                    <select id="nf-bulk-action">
                        <option value="">Bulk Actions</option>
                        <option value="install">Install Selected</option>
                        <option value="activate">Activate Selected</option>
                        <option value="deactivate">Deactivate Selected</option>
                        <option value="update">Update Selected</option>
                    </select>
                    <button class="button" id="nf-apply-bulk">Apply</button>
                    <span class="selected-count">0 selected</span>
                </div>
            `);
            
            $('.nf-plugins-header').append($bulkActions);
            
            // Apply bulk action
            $('#nf-apply-bulk').on('click', () => {
                this.applyBulkAction();
            });
        },

        /**
         * Update bulk actions visibility
         */
        updateBulkActions() {
            const checkedCount = $('.nf-plugin-checkbox:checked').length;
            
            if (checkedCount > 0) {
                $('.nf-bulk-actions').show();
                $('.nf-bulk-actions .selected-count').text(`${checkedCount} selected`);
            } else {
                $('.nf-bulk-actions').hide();
            }
        },

        /**
         * Apply bulk action
         */
        applyBulkAction() {
            const action = $('#nf-bulk-action').val();
            
            if (!action) {
                this.showNotification('Please select an action', 'warning');
                return;
            }
            
            const $checked = $('.nf-plugin-checkbox:checked');
            
            if ($checked.length === 0) {
                this.showNotification('No plugins selected', 'warning');
                return;
            }
            
            const pluginButtons = [];
            $checked.each(function() {
                const $card = $(this).closest('.nf-plugin-card');
                const $button = $card.find(`.nf-${action}-now`);
                
                if ($button.length && !$button.hasClass('button-disabled')) {
                    pluginButtons.push($button);
                }
            });
            
            if (pluginButtons.length === 0) {
                this.showNotification(`No plugins available for ${action}`, 'info');
                return;
            }
            
            // Execute bulk action
            this.executeBulkAction(action, pluginButtons);
        },

        /**
         * Execute bulk action
         */
        executeBulkAction(action, buttons) {
            this.state.queue = buttons;
            this.state.batchProcessing = true;
            
            this.showBatchProgress(0, buttons.length);
            
            // Process based on action
            switch(action) {
                case 'install':
                    this.processQueue();
                    break;
                case 'activate':
                    this.processActivationQueue(buttons);
                    break;
                case 'deactivate':
                    this.processBulkDeactivation(buttons);
                    break;
                case 'update':
                    this.processBulkUpdate(buttons);
                    break;
            }
        },

        /**
         * Bulk deactivation
         */
        processBulkDeactivation(buttons) {
            buttons.forEach($button => {
                this.deactivatePlugin($button);
            });
        },

        /**
         * Bulk update
         */
        processBulkUpdate(buttons) {
            buttons.forEach($button => {
                this.updatePlugin($button);
            });
        },

        /**
         * ==================================================================
         * IMPORT/EXPORT
         * ==================================================================
         */

        /**
         * Import plugins list
         */
        importPluginsList(file) {
            if (!file) return;
            
            const reader = new FileReader();
            reader.onload = (e) => {
                try {
                    const plugins = JSON.parse(e.target.result);
                    this.processImportedPlugins(plugins);
                } catch (error) {
                    this.showNotification('Invalid plugins file', 'error');
                }
            };
            reader.readAsText(file);
        },

        /**
         * Process imported plugins
         */
        processImportedPlugins(plugins) {
            // Validate and queue plugins for installation
            const validPlugins = plugins.filter(plugin => plugin.slug);
            
            if (validPlugins.length === 0) {
                this.showNotification('No valid plugins in file', 'warning');
                return;
            }
            
            // Create install queue
            validPlugins.forEach(plugin => {
                // Create temporary button for installation
                const $button = $(`<button data-slug="${plugin.slug}" data-name="${plugin.name || plugin.slug}"></button>`);
                this.state.queue.push($button);
            });
            
            this.state.batchProcessing = true;
            this.processQueue();
        },

        /**
         * ==================================================================
         * WORDPRESS EVENTS
         * ==================================================================
         */

        /**
         * Bind WordPress update events
         */
        bindWordPressEvents() {
            if (!wp.updates) return;
            
            // Install success
            $(document).on('wp-plugin-install-success', (event, response) => {
                this.log('WordPress install success event', response);
            });
            
            // Install error
            $(document).on('wp-plugin-install-error', (event, response) => {
                this.log('WordPress install error event', response);
            });
            
            // Update success
            $(document).on('wp-plugin-update-success', (event, response) => {
                this.log('WordPress update success event', response);
            });
        },

        /**
         * ==================================================================
         * PROGRESS TRACKING
         * ==================================================================
         */

        /**
         * Initialize progress tracking
         */
        initializeProgressTracking() {
            // Create progress container if needed
            if (!$('#nf-plugin-progress').length) {
                const $container = $(`
                    <div id="nf-plugin-progress" class="nf-plugin-progress-container">
                        <div class="progress-header">
                            <h3>Plugin Operations</h3>
                            <button class="close-progress">&times;</button>
                        </div>
                        <div class="progress-content"></div>
                    </div>
                `);
                
                $('body').append($container);
                
                // Bind close event
                $container.on('click', '.close-progress', function() {
                    $container.fadeOut();
                });
            }
        }
    };

    /**
     * ==================================================================
     * COMPATIBILIDADE COM REFERÊNCIA ORIGINAL
     * ==================================================================
     */
    
    // Manter compatibilidade com código original da referência
    $(function () {
        // Código original da referência preservado
        $(document).on('click', '.sf-install-now', function (event) {
            const $button = $(event.target);

            if ($button.hasClass('activate-now')) {
                return true;
            }

            event.preventDefault();

            if ($button.hasClass('updating-message') || 
                $button.hasClass('button-disabled')) {
                return;
            }

            if (wp.updates.shouldRequestFilesystemCredentials && 
                !wp.updates.ajaxLocked) {
                
                wp.updates.requestFilesystemCredentials(event);

                $(document).on('credential-modal-cancel', function () {
                    const $message = $('.sf-install-now.updating-message');
                    
                    $message
                        .removeClass('updating-message')
                        .text(wp.updates.l10n.installNow);
                    
                    wp.a11y.speak(wp.updates.l10n.updateCancel, 'polite');
                });
            }

            wp.updates.installPlugin({
                slug: $button.data('slug'),
            });
        });
    });

    /**
     * ==================================================================
     * INICIALIZAÇÃO
     * ==================================================================
     */

    // Inicializar quando DOM estiver pronto
    $(document).ready(function() {
        // Inicializar Nosfir Plugin Manager
        NosfirPluginManager.init();
        
        // Expor globalmente para debugging
        window.NosfirPluginManager = NosfirPluginManager;
    });

})(window.wp, jQuery);