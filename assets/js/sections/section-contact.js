/**
 * Section: Contact JavaScript
 * 
 * @package Nosfir
 */

(function($) {
    'use strict';

    const ContactSection = {
        
        /**
         * Inicialização
         */
        init: function() {
            this.cacheElements();
            
            if (this.$form.length) {
                this.bindEvents();
                this.initValidation();
            }
        },

        /**
         * Cache de elementos
         */
        cacheElements: function() {
            this.$section = $('.nosfir-contact');
            this.$form = $('#nosfir-contact-form');
            this.$submitBtn = this.$form.find('button[type="submit"]');
            this.$message = this.$form.find('.nosfir-form__message');
        },

        /**
         * Binding de eventos
         */
        bindEvents: function() {
            this.$form.on('submit', this.handleSubmit.bind(this));
            
            // Limpar erros ao digitar
            this.$form.find('input, textarea').on('input', function() {
                $(this).removeClass('has-error').next('.error-message').remove();
            });
        },

        /**
         * Inicializar validação
         */
        initValidation: function() {
            // HTML5 validation
            this.$form.attr('novalidate', true);
        },

        /**
         * Validar formulário
         */
        validateForm: function() {
            let isValid = true;
            const $required = this.$form.find('[required]');
            
            $required.each(function() {
                const $field = $(this);
                const value = $field.val().trim();
                const type = $field.attr('type');
                
                // Remover erro anterior
                $field.removeClass('has-error').next('.error-message').remove();
                
                // Verificar se vazio
                if (!value) {
                    isValid = false;
                    ContactSection.showFieldError($field, 'Este campo é obrigatório');
                    return;
                }
                
                // Validar email
                if (type === 'email' && !ContactSection.isValidEmail(value)) {
                    isValid = false;
                    ContactSection.showFieldError($field, 'Email inválido');
                }
            });
            
            return isValid;
        },

        /**
         * Validar email
         */
        isValidEmail: function(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        },

        /**
         * Mostrar erro de campo
         */
        showFieldError: function($field, message) {
            $field.addClass('has-error');
            $('<span class="error-message">' + message + '</span>').insertAfter($field);
        },

        /**
         * Handle submit
         */
        handleSubmit: function(e) {
            e.preventDefault();
            
            // Validar
            if (!this.validateForm()) {
                return;
            }
            
            // Desabilitar botão
            this.setLoading(true);
            
            // Dados do formulário
            const formData = new FormData(this.$form[0]);
            formData.append('action', 'nosfir_contact_submit');
            formData.append('nonce', nosfirData.nonce);
            
            // Enviar via AJAX
            $.ajax({
                url: nosfirData.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: this.handleSuccess.bind(this),
                error: this.handleError.bind(this),
                complete: () => {
                    this.setLoading(false);
                }
            });
        },

        /**
         * Handle success
         */
        handleSuccess: function(response) {
            if (response.success) {
                this.showMessage('success', response.data.message || 'Mensagem enviada com sucesso!');
                this.$form[0].reset();
            } else {
                this.showMessage('error', response.data.message || 'Erro ao enviar mensagem.');
            }
        },

        /**
         * Handle error
         */
        handleError: function(xhr, status, error) {
            console.error('Erro AJAX:', error);
            this.showMessage('error', 'Erro de conexão. Tente novamente.');
        },

        /**
         * Mostrar mensagem
         */
        showMessage: function(type, message) {
            this.$message
                .removeClass('success error')
                .addClass(type)
                .html(message)
                .fadeIn(300);
            
            // Esconder após 5 segundos
            setTimeout(() => {
                this.$message.fadeOut(300);
            }, 5000);
        },

        /**
         * Set loading state
         */
        setLoading: function(loading) {
            if (loading) {
                this.$submitBtn.prop('disabled', true);
                this.$submitBtn.find('.btn-text').hide();
                this.$submitBtn.find('.btn-loader').show();
            } else {
                this.$submitBtn.prop('disabled', false);
                this.$submitBtn.find('.btn-text').show();
                this.$submitBtn.find('.btn-loader').hide();
            }
        }
    };

    // Inicializar quando DOM estiver pronto
    $(document).ready(function() {
        ContactSection.init();
    });

})(jQuery);