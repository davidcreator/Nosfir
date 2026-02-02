/**
 * Section: Portfolio JavaScript
 * 
 * @package Nosfir
 */

(function($) {
    'use strict';

    const PortfolioSection = {
        
        /**
         * Inicialização
         */
        init: function() {
            this.cacheElements();
            
            if (this.$grid.length) {
                this.bindEvents();
                this.initIsotope();
                this.initLightbox();
            }
        },

        /**
         * Cache de elementos
         */
        cacheElements: function() {
            this.$section = $('.nosfir-portfolio');
            this.$grid = $('#portfolio-grid');
            this.$filterBtns = $('.nosfir-filter-btn');
        },

        /**
         * Binding de eventos
         */
        bindEvents: function() {
            // Filter click
            this.$filterBtns.on('click', this.handleFilter.bind(this));
        },

        /**
         * Inicializar Isotope (se disponível)
         */
        initIsotope: function() {
            // Verificar se Isotope está carregado
            if (typeof $.fn.isotope === 'undefined') {
                console.log('Isotope não carregado, usando filtro CSS');
                this.useCSSFilter = true;
                return;
            }

            // Aguardar imagens carregarem
            this.$grid.imagesLoaded().progress(() => {
                this.isotope = this.$grid.isotope({
                    itemSelector: '.nosfir-portfolio__item',
                    layoutMode: 'fitRows',
                    transitionDuration: '0.6s',
                    stagger: 30
                });
            });
        },

        /**
         * Handle filter click
         */
        handleFilter: function(e) {
            e.preventDefault();
            
            const $btn = $(e.currentTarget);
            const filter = $btn.data('filter');
            
            // Atualizar botão ativo
            this.$filterBtns.removeClass('is-active');
            $btn.addClass('is-active');
            
            // Aplicar filtro
            if (this.useCSSFilter) {
                this.applyCSSFilter(filter);
            } else if (this.isotope) {
                this.$grid.isotope({ filter: filter });
            }
        },

        /**
         * Filtro CSS (fallback sem Isotope)
         */
        applyCSSFilter: function(filter) {
            const $items = this.$grid.find('.nosfir-portfolio__item');
            
            if (filter === '*') {
                $items.fadeIn(400);
            } else {
                $items.each(function() {
                    const $item = $(this);
                    if ($item.hasClass(filter.replace('.', ''))) {
                        $item.fadeIn(400);
                    } else {
                        $item.fadeOut(400);
                    }
                });
            }
        },

        /**
         * Inicializar Lightbox
         */
        initLightbox: function() {
            // Verificar se tem biblioteca de lightbox
            if (typeof $.fn.magnificPopup !== 'undefined') {
                this.$grid.magnificPopup({
                    delegate: '.nosfir-portfolio__lightbox',
                    type: 'image',
                    gallery: {
                        enabled: true,
                        navigateByImgClick: true
                    },
                    image: {
                        titleSrc: function(item) {
                            return item.el.closest('.nosfir-portfolio__item').find('.nosfir-portfolio__title').text();
                        }
                    }
                });
            } else {
                // Lightbox nativo simples
                this.initNativeLightbox();
            }
        },

        /**
         * Lightbox nativo simples
         */
        initNativeLightbox: function() {
            const $overlay = $('<div class="nosfir-lightbox-overlay"></div>');
            const $content = $('<div class="nosfir-lightbox-content"><img src="" alt=""><button class="nosfir-lightbox-close">&times;</button></div>');
            
            $overlay.append($content).appendTo('body');
            
            this.$grid.on('click', '.nosfir-portfolio__lightbox', function(e) {
                e.preventDefault();
                const imgSrc = $(this).attr('href');
                $content.find('img').attr('src', imgSrc);
                $overlay.fadeIn(300);
                $('body').addClass('lightbox-open');
            });
            
            $overlay.on('click', function(e) {
                if ($(e.target).is($overlay) || $(e.target).is('.nosfir-lightbox-close')) {
                    $overlay.fadeOut(300);
                    $('body').removeClass('lightbox-open');
                }
            });
            
            // ESC para fechar
            $(document).on('keyup', function(e) {
                if (e.key === 'Escape' && $overlay.is(':visible')) {
                    $overlay.fadeOut(300);
                    $('body').removeClass('lightbox-open');
                }
            });
        }
    };

    // Inicializar quando DOM estiver pronto
    $(document).ready(function() {
        PortfolioSection.init();
    });

})(jQuery);