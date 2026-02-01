/**
 * Nosfir Sticky Add to Cart
 * 
 * Gerencia o comportamento do botão sticky de adicionar ao carrinho
 * para produtos WooCommerce com animações suaves e responsividade
 * 
 * @package Nosfir
 * @since 1.0.0
 */

/* global nosfir_sticky_cart_params */
(function() {
    'use strict';

    /**
     * Configurações principais do módulo
     */
    const StickyAddToCart = {
        
        // Elementos DOM
        elements: {
            stickyBar: null,
            trigger: null,
            productForm: null,
            quantityInput: null,
            variationForm: null,
            addToCartButton: null
        },

        // Estados
        state: {
            isVisible: false,
            isScrolling: false,
            scrollTimeout: null,
            lastScrollTop: 0,
            productId: null,
            productType: null
        },

        // Configurações
        config: {
            triggerOffset: 300,
            animationDuration: 400,
            throttleDelay: 10,
            mobileBreakpoint: 768,
            classes: {
                stickyBar: 'nosfir-sticky-add-to-cart',
                visible: 'nosfir-sticky-add-to-cart--visible',
                hidden: 'nosfir-sticky-add-to-cart--hidden',
                slideIn: 'nosfir-sticky-add-to-cart--slideInDown',
                slideOut: 'nosfir-sticky-add-to-cart--slideOutUp',
                loading: 'nosfir-sticky-add-to-cart--loading',
                success: 'nosfir-sticky-add-to-cart--success'
            }
        },

        /**
         * Inicializa o módulo
         */
        init() {
            // Verifica se estamos em uma página de produto
            if (!this.isProductPage()) {
                return;
            }

            // Carrega parâmetros do WordPress se disponíveis
            this.loadParams();

            // Obtém elementos DOM
            if (!this.cacheElements()) {
                return;
            }

            // Obtém informações do produto
            this.getProductInfo();

            // Configura event listeners
            this.bindEvents();

            // Sincroniza quantidade inicial
            this.syncQuantity();

            // Verifica posição inicial
            this.checkScrollPosition();

            // Adiciona suporte para teclado
            this.setupAccessibility();
        },

        /**
         * Verifica se estamos em uma página de produto
         */
        isProductPage() {
            return document.body.classList.contains('single-product') || 
                   document.querySelector('.product-type-simple, .product-type-variable, .product-type-grouped');
        },

        /**
         * Carrega parâmetros do WordPress
         */
        loadParams() {
            if (typeof nosfir_sticky_cart_params !== 'undefined') {
                // Sobrescreve configurações com parâmetros do WordPress
                if (nosfir_sticky_cart_params.trigger_offset) {
                    this.config.triggerOffset = parseInt(nosfir_sticky_cart_params.trigger_offset);
                }
                if (nosfir_sticky_cart_params.trigger_class) {
                    this.config.triggerClass = nosfir_sticky_cart_params.trigger_class;
                }
                if (nosfir_sticky_cart_params.mobile_enabled !== undefined) {
                    this.config.mobileEnabled = nosfir_sticky_cart_params.mobile_enabled;
                }
            }
        },

        /**
         * Armazena referências aos elementos DOM
         */
        cacheElements() {
            // Sticky bar principal
            this.elements.stickyBar = document.querySelector('.' + this.config.classes.stickyBar);
            
            if (!this.elements.stickyBar) {
                // Cria o sticky bar se não existir
                this.createStickyBar();
            }

            // Elemento trigger (formulário do produto ou custom)
            this.elements.trigger = document.querySelector(
                this.config.triggerClass || 'form.cart, .summary .single_add_to_cart_button'
            );

            // Formulário do produto
            this.elements.productForm = document.querySelector('form.cart');

            // Input de quantidade
            this.elements.quantityInput = document.querySelector('form.cart input.qty');

            // Formulário de variações
            this.elements.variationForm = document.querySelector('form.variations_form');

            // Botão adicionar ao carrinho no sticky
            this.elements.addToCartButton = this.elements.stickyBar?.querySelector('.nosfir-sticky-add-button');

            return this.elements.stickyBar && this.elements.trigger;
        },

        /**
         * Cria o sticky bar dinamicamente
         */
        createStickyBar() {
            const product = document.querySelector('.product');
            if (!product) return;

            const productTitle = document.querySelector('.product_title')?.textContent || '';
            const productPrice = document.querySelector('.price .amount')?.outerHTML || '';
            const productImage = document.querySelector('.woocommerce-product-gallery__image img')?.src || '';

            const stickyBarHTML = `
                <div class="${this.config.classes.stickyBar}">
                    <div class="nosfir-sticky-add-to-cart__content">
                        <div class="nosfir-sticky-add-to-cart__product-info">
                            ${productImage ? `
                                <div class="nosfir-sticky-add-to-cart__product-image">
                                    <img src="${productImage}" alt="${productTitle}">
                                </div>
                            ` : ''}
                            <div class="nosfir-sticky-add-to-cart__product-details">
                                <h4 class="nosfir-sticky-add-to-cart__product-title">${productTitle}</h4>
                                <div class="nosfir-sticky-add-to-cart__product-price">${productPrice}</div>
                            </div>
                        </div>
                        <div class="nosfir-sticky-add-to-cart__product-actions">
                            <div class="nosfir-sticky-add-to-cart__quantity">
                                <button type="button" class="nosfir-qty-minus" aria-label="Diminuir quantidade">-</button>
                                <input type="number" class="nosfir-sticky-qty" value="1" min="1" aria-label="Quantidade">
                                <button type="button" class="nosfir-qty-plus" aria-label="Aumentar quantidade">+</button>
                            </div>
                            <button type="button" class="nosfir-sticky-add-button button alt">
                                <span class="nosfir-sticky-add-button__text">Adicionar ao carrinho</span>
                                <span class="nosfir-sticky-add-button__loading" style="display:none;">
                                    <svg class="nosfir-spinner" viewBox="0 0 50 50">
                                        <circle cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                    <button type="button" class="nosfir-sticky-add-to-cart__close" aria-label="Fechar barra">
                        <span>&times;</span>
                    </button>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', stickyBarHTML);
            this.elements.stickyBar = document.querySelector('.' + this.config.classes.stickyBar);
        },

        /**
         * Obtém informações do produto
         */
        getProductInfo() {
            // Obtém ID do produto das classes do body
            document.body.classList.forEach((className) => {
                if (className.startsWith('postid-')) {
                    this.state.productId = className.replace('postid-', '');
                }
            });

            // Obtém tipo do produto
            const productElement = document.querySelector(`#product-${this.state.productId}, .product`);
            if (productElement) {
                const classList = productElement.classList;
                if (classList.contains('product-type-simple')) {
                    this.state.productType = 'simple';
                } else if (classList.contains('product-type-variable')) {
                    this.state.productType = 'variable';
                } else if (classList.contains('product-type-grouped')) {
                    this.state.productType = 'grouped';
                } else if (classList.contains('product-type-external')) {
                    this.state.productType = 'external';
                }
            }
        },

        /**
         * Configura event listeners
         */
        bindEvents() {
            // Scroll events com throttle
            let ticking = false;
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        this.handleScroll();
                        ticking = false;
                    });
                    ticking = true;
                }
            });

            // Resize events
            window.addEventListener('resize', this.debounce(() => {
                this.checkScrollPosition();
            }, 250));

            // Clique no botão adicionar ao carrinho
            if (this.elements.addToCartButton) {
                this.elements.addToCartButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.handleAddToCart();
                });
            }

            // Sincronização de quantidade
            if (this.elements.quantityInput) {
                this.elements.quantityInput.addEventListener('change', () => {
                    this.syncQuantity();
                });
            }

            // Botões de quantidade no sticky bar
            const qtyMinus = this.elements.stickyBar?.querySelector('.nosfir-qty-minus');
            const qtyPlus = this.elements.stickyBar?.querySelector('.nosfir-qty-plus');
            const stickyQty = this.elements.stickyBar?.querySelector('.nosfir-sticky-qty');

            if (qtyMinus) {
                qtyMinus.addEventListener('click', () => {
                    this.updateQuantity(-1);
                });
            }

            if (qtyPlus) {
                qtyPlus.addEventListener('click', () => {
                    this.updateQuantity(1);
                });
            }

            if (stickyQty) {
                stickyQty.addEventListener('change', () => {
                    this.syncQuantityFromSticky();
                });
            }

            // Botão fechar
            const closeButton = this.elements.stickyBar?.querySelector('.nosfir-sticky-add-to-cart__close');
            if (closeButton) {
                closeButton.addEventListener('click', () => {
                    this.hideStickyBar();
                    // Salva preferência do usuário
                    sessionStorage.setItem('nosfir_sticky_cart_closed', 'true');
                });
            }

            // Eventos de variação (WooCommerce)
            if (this.state.productType === 'variable') {
                jQuery(document).on('found_variation', (event, variation) => {
                    this.updateStickyBarVariation(variation);
                });

                jQuery(document).on('reset_variation', () => {
                    this.resetStickyBarVariation();
                });
            }

            // Eventos do carrinho AJAX
            jQuery(document.body).on('added_to_cart', () => {
                this.onAddedToCart();
            });
        },

        /**
         * Gerencia o evento de scroll
         */
        handleScroll() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            // Verifica direção do scroll
            const scrollingDown = scrollTop > this.state.lastScrollTop;
            this.state.lastScrollTop = scrollTop;

            // Verifica se deve mostrar/ocultar
            this.checkScrollPosition();

            // Auto-hide ao scrollar para cima (opcional)
            if (this.config.autoHideOnScrollUp && !scrollingDown && this.state.isVisible) {
                // this.hideStickyBar();
            }
        },

        /**
         * Verifica posição do scroll e mostra/oculta sticky bar
         */
        checkScrollPosition() {
            // Verifica se o usuário fechou manualmente
            if (sessionStorage.getItem('nosfir_sticky_cart_closed') === 'true') {
                return;
            }

            // Verifica se estamos em mobile e se está habilitado
            if (window.innerWidth <= this.config.mobileBreakpoint && !this.config.mobileEnabled) {
                this.hideStickyBar();
                return;
            }

            if (!this.elements.trigger) return;

            const triggerRect = this.elements.trigger.getBoundingClientRect();
            const triggerBottom = triggerRect.top + triggerRect.height;

            if (triggerBottom < -this.config.triggerOffset) {
                this.showStickyBar();
            } else {
                this.hideStickyBar();
            }
        },

        /**
         * Mostra o sticky bar
         */
        showStickyBar() {
            if (this.state.isVisible) return;

            this.elements.stickyBar.classList.remove(this.config.classes.hidden);
            this.elements.stickyBar.classList.remove(this.config.classes.slideOut);
            this.elements.stickyBar.classList.add(this.config.classes.visible);
            this.elements.stickyBar.classList.add(this.config.classes.slideIn);
            
            this.state.isVisible = true;

            // Dispara evento customizado
            this.triggerEvent('nosfirStickyCartShow');
        },

        /**
         * Oculta o sticky bar
         */
        hideStickyBar() {
            if (!this.state.isVisible) return;

            this.elements.stickyBar.classList.remove(this.config.classes.slideIn);
            this.elements.stickyBar.classList.add(this.config.classes.slideOut);

            setTimeout(() => {
                this.elements.stickyBar.classList.remove(this.config.classes.visible);
                this.elements.stickyBar.classList.add(this.config.classes.hidden);
            }, this.config.animationDuration);

            this.state.isVisible = false;

            // Dispara evento customizado
            this.triggerEvent('nosfirStickyCartHide');
        },

        /**
         * Gerencia o clique em adicionar ao carrinho
         */
        handleAddToCart() {
            // Para produtos variáveis, verifica se uma variação foi selecionada
            if (this.state.productType === 'variable') {
                const variation_id = this.elements.variationForm?.querySelector('[name="variation_id"]')?.value;
                
                if (!variation_id || variation_id === '0') {
                    // Scrolla até o formulário para selecionar variação
                    this.scrollToProductForm();
                    this.showNotification('Por favor, selecione as opções do produto', 'warning');
                    return;
                }
            }

            // Para produtos agrupados, redireciona para o formulário
            if (this.state.productType === 'grouped') {
                this.scrollToProductForm();
                return;
            }

            // Para produtos externos, redireciona
            if (this.state.productType === 'external') {
                const externalLink = this.elements.productForm?.querySelector('.single_add_to_cart_button')?.href;
                if (externalLink) {
                    window.location.href = externalLink;
                }
                return;
            }

            // Adiciona ao carrinho via AJAX
            this.addToCartAjax();
        },

        /**
         * Adiciona produto ao carrinho via AJAX
         */
        addToCartAjax() {
            // Mostra loading
            this.setLoadingState(true);

            // Prepara dados do formulário
            const formData = new FormData();
            formData.append('action', 'nosfir_add_to_cart');
            formData.append('product_id', this.state.productId);
            formData.append('quantity', this.getStickyQuantity());

            // Para produtos variáveis
            if (this.state.productType === 'variable') {
                const variation_id = this.elements.variationForm?.querySelector('[name="variation_id"]')?.value;
                formData.append('variation_id', variation_id);

                // Adiciona atributos da variação
                const attributes = this.elements.variationForm?.querySelectorAll('select[name^="attribute_"]');
                attributes?.forEach(select => {
                    formData.append(select.name, select.value);
                });
            }

            // Faz a requisição AJAX
            fetch(nosfir_sticky_cart_params?.ajax_url || '/wp-admin/admin-ajax.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                this.setLoadingState(false);

                if (data.success) {
                    this.onAddedToCart();
                    this.showNotification('Produto adicionado ao carrinho!', 'success');
                    
                    // Atualiza fragmentos do carrinho se disponível
                    if (data.fragments) {
                        this.updateCartFragments(data.fragments);
                    }
                } else {
                    this.showNotification(data.message || 'Erro ao adicionar ao carrinho', 'error');
                }
            })
            .catch(error => {
                this.setLoadingState(false);
                this.showNotification('Erro ao processar requisição', 'error');
                console.error('Sticky cart error:', error);
            });
        },

        /**
         * Define estado de loading
         */
        setLoadingState(loading) {
            const button = this.elements.addToCartButton;
            const buttonText = button?.querySelector('.nosfir-sticky-add-button__text');
            const buttonLoading = button?.querySelector('.nosfir-sticky-add-button__loading');

            if (loading) {
                button?.classList.add('loading');
                button?.setAttribute('disabled', 'disabled');
                if (buttonText) buttonText.style.display = 'none';
                if (buttonLoading) buttonLoading.style.display = 'inline-block';
                this.elements.stickyBar?.classList.add(this.config.classes.loading);
            } else {
                button?.classList.remove('loading');
                button?.removeAttribute('disabled');
                if (buttonText) buttonText.style.display = 'inline';
                if (buttonLoading) buttonLoading.style.display = 'none';
                this.elements.stickyBar?.classList.remove(this.config.classes.loading);
            }
        },

        /**
         * Callback quando produto é adicionado ao carrinho
         */
        onAddedToCart() {
            // Adiciona classe de sucesso temporariamente
            this.elements.stickyBar?.classList.add(this.config.classes.success);
            
            setTimeout(() => {
                this.elements.stickyBar?.classList.remove(this.config.classes.success);
            }, 2000);

            // Atualiza botão temporariamente
            const originalText = this.elements.addToCartButton?.querySelector('.nosfir-sticky-add-button__text')?.textContent;
            if (this.elements.addToCartButton) {
                const textElement = this.elements.addToCartButton.querySelector('.nosfir-sticky-add-button__text');
                if (textElement) {
                    textElement.textContent = '✓ Adicionado!';
                    setTimeout(() => {
                        textElement.textContent = originalText;
                    }, 2000);
                }
            }

            // Dispara evento customizado
            this.triggerEvent('nosfirStickyCartAdded');
        },

        /**
         * Atualiza quantidade
         */
        updateQuantity(change) {
            const stickyQty = this.elements.stickyBar?.querySelector('.nosfir-sticky-qty');
            if (!stickyQty) return;

            let currentQty = parseInt(stickyQty.value) || 1;
            let newQty = currentQty + change;
            
            // Respeita limites min/max
            const min = parseInt(stickyQty.min) || 1;
            const max = parseInt(stickyQty.max) || 9999;
            
            newQty = Math.max(min, Math.min(newQty, max));
            
            stickyQty.value = newQty;
            
            // Sincroniza com formulário principal
            if (this.elements.quantityInput) {
                this.elements.quantityInput.value = newQty;
                
                // Dispara evento change
                const event = new Event('change', { bubbles: true });
                this.elements.quantityInput.dispatchEvent(event);
            }
        },

        /**
         * Sincroniza quantidade do formulário principal para sticky
         */
        syncQuantity() {
            const mainQty = this.elements.quantityInput?.value || 1;
            const stickyQty = this.elements.stickyBar?.querySelector('.nosfir-sticky-qty');
            
            if (stickyQty) {
                stickyQty.value = mainQty;
            }
        },

        /**
         * Sincroniza quantidade do sticky para formulário principal
         */
        syncQuantityFromSticky() {
            const stickyQty = this.elements.stickyBar?.querySelector('.nosfir-sticky-qty');
            const qty = stickyQty?.value || 1;
            
            if (this.elements.quantityInput) {
                this.elements.quantityInput.value = qty;
                
                // Dispara evento change
                const event = new Event('change', { bubbles: true });
                this.elements.quantityInput.dispatchEvent(event);
            }
        },

        /**
         * Obtém quantidade atual do sticky bar
         */
        getStickyQuantity() {
            return this.elements.stickyBar?.querySelector('.nosfir-sticky-qty')?.value || 1;
        },

        /**
         * Atualiza sticky bar com informações da variação
         */
        updateStickyBarVariation(variation) {
            // Atualiza preço
            const priceElement = this.elements.stickyBar?.querySelector('.nosfir-sticky-add-to-cart__product-price');
            if (priceElement && variation.price_html) {
                priceElement.innerHTML = variation.price_html;
            }

            // Atualiza imagem se disponível
            const imageElement = this.elements.stickyBar?.querySelector('.nosfir-sticky-add-to-cart__product-image img');
            if (imageElement && variation.image && variation.image.src) {
                imageElement.src = variation.image.src;
                imageElement.srcset = variation.image.srcset || '';
            }

            // Atualiza disponibilidade
            if (!variation.is_in_stock) {
                this.elements.addToCartButton?.setAttribute('disabled', 'disabled');
                const textElement = this.elements.addToCartButton?.querySelector('.nosfir-sticky-add-button__text');
                if (textElement) {
                    textElement.textContent = 'Fora de estoque';
                }
            } else {
                this.elements.addToCartButton?.removeAttribute('disabled');
                const textElement = this.elements.addToCartButton?.querySelector('.nosfir-sticky-add-button__text');
                if (textElement) {
                    textElement.textContent = 'Adicionar ao carrinho';
                }
            }

            // Atualiza limites de quantidade
            const stickyQty = this.elements.stickyBar?.querySelector('.nosfir-sticky-qty');
            if (stickyQty) {
                if (variation.min_qty) stickyQty.min = variation.min_qty;
                if (variation.max_qty) stickyQty.max = variation.max_qty;
            }
        },

        /**
         * Reseta sticky bar quando variação é resetada
         */
        resetStickyBarVariation() {
            // Volta ao preço original
            const originalPrice = document.querySelector('.summary > .price')?.innerHTML;
            const priceElement = this.elements.stickyBar?.querySelector('.nosfir-sticky-add-to-cart__product-price');
            if (priceElement && originalPrice) {
                priceElement.innerHTML = originalPrice;
            }

            // Desabilita botão
            this.elements.addToCartButton?.setAttribute('disabled', 'disabled');
            const textElement = this.elements.addToCartButton?.querySelector('.nosfir-sticky-add-button__text');
            if (textElement) {
                textElement.textContent = 'Selecione uma opção';
            }
        },

        /**
         * Scrolla até o formulário do produto
         */
        scrollToProductForm() {
            if (this.elements.productForm) {
                const yOffset = -100; // Offset para header fixo
                const y = this.elements.productForm.getBoundingClientRect().top + window.pageYOffset + yOffset;
                
                window.scrollTo({
                    top: y,
                    behavior: 'smooth'
                });
            }
        },

        /**
         * Atualiza fragmentos do carrinho
         */
        updateCartFragments(fragments) {
            if (!fragments) return;

            Object.keys(fragments).forEach(selector => {
                const elements = document.querySelectorAll(selector);
                elements.forEach(element => {
                    element.outerHTML = fragments[selector];
                });
            });

            // Dispara evento para outros scripts
            jQuery(document.body).trigger('wc_fragments_refreshed');
        },

        /**
         * Mostra notificação
         */
        showNotification(message, type = 'info') {
            // Remove notificação existente
            const existingNotification = document.querySelector('.nosfir-sticky-notification');
            if (existingNotification) {
                existingNotification.remove();
            }

            // Cria nova notificação
            const notification = document.createElement('div');
            notification.className = `nosfir-sticky-notification nosfir-sticky-notification--${type}`;
            notification.innerHTML = `
                <div class="nosfir-sticky-notification__content">
                    <span class="nosfir-sticky-notification__message">${message}</span>
                    <button type="button" class="nosfir-sticky-notification__close">&times;</button>
                </div>
            `;

            document.body.appendChild(notification);

            // Anima entrada
            setTimeout(() => {
                notification.classList.add('nosfir-sticky-notification--visible');
            }, 10);

            // Remove após 3 segundos
            const autoRemove = setTimeout(() => {
                this.hideNotification(notification);
            }, 3000);

            // Botão fechar
            notification.querySelector('.nosfir-sticky-notification__close')?.addEventListener('click', () => {
                clearTimeout(autoRemove);
                this.hideNotification(notification);
            });
        },

        /**
         * Esconde notificação
         */
        hideNotification(notification) {
            notification.classList.remove('nosfir-sticky-notification--visible');
            setTimeout(() => {
                notification.remove();
            }, 300);
        },

        /**
         * Configura acessibilidade
         */
        setupAccessibility() {
            // Adiciona navegação por teclado
            this.elements.stickyBar?.setAttribute('role', 'complementary');
            this.elements.stickyBar?.setAttribute('aria-label', 'Barra de adicionar ao carrinho rápido');

            // ESC fecha o sticky bar
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.state.isVisible) {
                    this.hideStickyBar();
                    sessionStorage.setItem('nosfir_sticky_cart_closed', 'true');
                }
            });

            // Tab trap quando sticky bar está visível
            if (this.config.trapFocus) {
                this.setupFocusTrap();
            }
        },

        /**
         * Configura armadilha de foco
         */
        setupFocusTrap() {
            const focusableElements = this.elements.stickyBar?.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );

            if (!focusableElements || focusableElements.length === 0) return;

            const firstFocusable = focusableElements[0];
            const lastFocusable = focusableElements[focusableElements.length - 1];

            this.elements.stickyBar?.addEventListener('keydown', (e) => {
                if (e.key !== 'Tab') return;

                if (e.shiftKey) {
                    if (document.activeElement === firstFocusable) {
                        lastFocusable.focus();
                        e.preventDefault();
                    }
                } else {
                    if (document.activeElement === lastFocusable) {
                        firstFocusable.focus();
                        e.preventDefault();
                    }
                }
            });
        },

        /**
         * Dispara evento customizado
         */
        triggerEvent(eventName, detail = {}) {
            const event = new CustomEvent(eventName, {
                detail: {
                    ...detail,
                    productId: this.state.productId,
                    quantity: this.getStickyQuantity()
                },
                bubbles: true,
                cancelable: true
            });
            
            document.dispatchEvent(event);
        },

        /**
         * Função debounce para otimização
         */
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        /**
         * Função throttle para otimização
         */
        throttle(func, limit) {
            let inThrottle;
            return function(...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        }
    };

    /**
     * Inicializa quando DOM estiver pronto
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            StickyAddToCart.init();
        });
    } else {
        // DOM já está carregado
        StickyAddToCart.init();
    }

    // Expõe API pública
    window.NosfirStickyCart = {
        show: () => StickyAddToCart.showStickyBar(),
        hide: () => StickyAddToCart.hideStickyBar(),
        updateQuantity: (qty) => {
            const stickyQty = document.querySelector('.nosfir-sticky-qty');
            if (stickyQty) {
                stickyQty.value = qty;
                StickyAddToCart.syncQuantityFromSticky();
            }
        },
        getState: () => StickyAddToCart.state,
        refresh: () => StickyAddToCart.checkScrollPosition()
    };

})();