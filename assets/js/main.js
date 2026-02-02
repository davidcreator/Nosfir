/**
 * Nosfir Theme - Main JavaScript
 * 
 * @package Nosfir
 */

(function($) {
    'use strict';

    // Namespace do tema
    const Nosfir = {
        
        /**
         * Inicialização
         */
        init: function() {
            this.cacheElements();
            this.bindEvents();
            this.initAnimations();
            this.initSmoothScroll();
            this.initStickyHeader();
        },

        /**
         * Cache de elementos DOM
         */
        cacheElements: function() {
            this.$window = $(window);
            this.$document = $(document);
            this.$body = $('body');
            this.$header = $('.site-header');
            this.$sections = $('[data-section]');
        },

        /**
         * Bindng de eventos
         */
        bindEvents: function() {
            // Resize com debounce
            let resizeTimer;
            this.$window.on('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    this.onResize();
                }, 250);
            });

            // Scroll com throttle
            let scrolling = false;
            this.$window.on('scroll', () => {
                if (!scrolling) {
                    window.requestAnimationFrame(() => {
                        this.onScroll();
                        scrolling = false;
                    });
                    scrolling = true;
                }
            });
        },

        /**
         * Callback de resize
         */
        onResize: function() {
            // Atualizar variáveis se necessário
        },

        /**
         * Callback de scroll
         */
        onScroll: function() {
            const scrollTop = this.$window.scrollTop();
            
            // Verificar elementos para animar
            this.checkAnimations(scrollTop);
            
            // Atualizar header sticky
            this.updateStickyHeader(scrollTop);
        },

        /**
         * Inicializar animações on scroll
         */
        initAnimations: function() {
            // Intersection Observer para melhor performance
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const $el = $(entry.target);
                            const animation = $el.data('animation') || 'fadeInUp';
                            const delay = $el.data('delay') || 0;
                            
                            setTimeout(() => {
                                $el.addClass('is-animated ' + animation);
                            }, delay);
                            
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });

                $('.animate-on-scroll').each(function() {
                    observer.observe(this);
                });
            } else {
                // Fallback para navegadores antigos
                $('.animate-on-scroll').addClass('is-animated');
            }
        },

        /**
         * Verificar animações (fallback)
         */
        checkAnimations: function(scrollTop) {
            // Fallback se IntersectionObserver não estiver disponível
        },

        /**
         * Smooth scroll para links âncora
         */
        initSmoothScroll: function() {
            $('a[href^="#"]').on('click', function(e) {
                const targetId = $(this).attr('href');
                
                if (targetId === '#') return;
                
                const $target = $(targetId);
                
                if ($target.length) {
                    e.preventDefault();
                    
                    const headerHeight = $('.site-header').outerHeight() || 0;
                    const targetOffset = $target.offset().top - headerHeight - 20;
                    
                    $('html, body').animate({
                        scrollTop: targetOffset
                    }, 800, 'easeInOutQuad');
                    
                    // Atualizar URL sem recarregar
                    if (history.pushState) {
                        history.pushState(null, null, targetId);
                    }
                }
            });
        },

        /**
         * Header sticky
         */
        initStickyHeader: function() {
            this.headerHeight = this.$header.outerHeight() || 0;
        },

        /**
         * Atualizar header sticky
         */
        updateStickyHeader: function(scrollTop) {
            if (scrollTop > 100) {
                this.$header.addClass('is-sticky');
            } else {
                this.$header.removeClass('is-sticky');
            }
        }
    };

    // jQuery easing
    $.extend($.easing, {
        easeInOutQuad: function(x, t, b, c, d) {
            if ((t /= d / 2) < 1) return c / 2 * t * t + b;
            return -c / 2 * ((--t) * (t - 2) - 1) + b;
        }
    });

    // Inicializar quando DOM estiver pronto
    $(document).ready(function() {
        Nosfir.init();
    });

    // Expor globalmente se necessário
    window.Nosfir = Nosfir;

})(jQuery);