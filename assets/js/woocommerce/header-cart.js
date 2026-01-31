/**
 * WooCommerce Header Cart JavaScript
 * 
 * @package     Nosfir
 * @subpackage  WooCommerce
 * @version     1.0.0
 * @author      David Creator
 * 
 * Gerencia o carrinho do cabeçalho com scroll, AJAX, animações e funcionalidades avançadas
 * Baseado no script de referência com melhorias significativas
 */

(function () {
    'use strict';

    /**
     * ==================================================================
     * HEADER CART MANAGER
     * ==================================================================
     */
    
    const NosfirHeaderCart = {
        
        // Configurações
        config: {
            scrollable: true,
            maxHeight: '15em',              // Valor da referência
            animationSpeed: 300,
            updateDelay: 500,
            mobileBreakpoint: 768,          // Valor da referência
            autoClose: true,
            autoCloseDelay: 3000,
            miniCartEnabled: true,
            quickAddEnabled: true,
            saveForLaterEnabled: true,
            recentlyViewedEnabled: true,
            crossSellEnabled: true,
            debug: false
        },

        // Elementos DOM
        elements: {
            cart: null,
            cartContent: null,
            cartList: null,
            cartCount: null,
            cartTotal: null,
            cartTrigger: null,
            cartOverlay: null,
            miniCart: null,
            quickShop: null
        },

        // Estado
        state: {
            isOpen: false,
            isLoading: false,
            isEmpty: false,
            itemCount: 0,
            cartTotal: 0,
            lastUpdate: null,
            scrollLocked: false,
            recentlyAdded: [],
            savedItems: []
        },

        // Cache
        cache: {
            cartData: null,
            products: {},
            fragments: {}
        },

        /**
         * ==================================================================
         * INICIALIZAÇÃO - Baseada na referência
         * ==================================================================
         */
        
        init() {
            // Verificações iniciais - Como na referência
            if (
                document.body.classList.contains('woocommerce-cart') ||
                document.body.classList.contains('woocommerce-checkout') ||
                window.innerWidth < this.config.mobileBreakpoint ||
                !document.getElementById('site-header-cart')
            ) {
                this.log('Header cart not initialized - conditions not met');
                return;
            }

            // Aguardar carregamento completo - Como na referência
            window.addEventListener('load', () => {
                this.log('Header cart initializing...');
                
                // Cachear elementos
                this.cacheElements();
                
                // Se não houver carrinho, retornar
                if (!this.elements.cart) {
                    this.log('Header cart element not found');
                    return;
                }
                
                // Setup funcionalidades
                this.setupScrollableCart();    // Baseado na referência
                this.setupCartTrigger();
                this.setupCartEvents();
                this.setupAjaxHandlers();
                this.setupMiniCart();
                this.setupQuickAdd();
                this.setupSaveForLater();
                this.setupCrossSell();
                this.setupRecentlyViewed();
                this.setupCartAnimations();
                this.setupKeyboardNavigation();
                
                // Bind eventos
                this.bindEvents();
                
                // Verificar estado inicial
                this.checkCartState();
                
                // Atualizar fragmentos
                this.refreshCartFragments();
                
                // Trigger custom event
                this.triggerEvent('headercart:ready');
                
                this.log('Header cart initialized successfully');
            });
        },

        /**
         * Logger condicional
         */
        log(message, type = 'log') {
            if (this.config.debug) {
                console[type]('[Nosfir Header Cart]:', message);
            }
        },

        /**
         * ==================================================================
         * CACHEAR ELEMENTOS
         * ==================================================================
         */
        
        cacheElements() {
            // Elemento principal - Como na referência
            this.elements.cart = document.querySelector('.site-header-cart');
            
            if (this.elements.cart) {
                // Elementos internos
                this.elements.cartContent = this.elements.cart.querySelector('.widget_shopping_cart_content');
                this.elements.cartList = this.elements.cart.querySelector('.cart_list');
                this.elements.cartCount = document.querySelector('.cart-contents-count');
                this.elements.cartTotal = document.querySelector('.cart-contents-total');
                this.elements.cartTrigger = document.querySelector('.cart-contents');
                
                // Elementos adicionais
                this.elements.miniCart = document.querySelector('.mini-cart-panel');
                this.elements.cartOverlay = document.querySelector('.cart-overlay');
                this.elements.quickShop = document.querySelector('.quick-shop-panel');
                
                // Botões e controles
                this.elements.closeButton = this.elements.cart.querySelector('.cart-close');
                this.elements.clearButton = this.elements.cart.querySelector('.clear-cart');
                this.elements.checkoutButton = this.elements.cart.querySelector('.checkout');
                this.elements.continueShoppingButton = this.elements.cart.querySelector('.continue-shopping');
            }
            
            this.log('Elements cached', this.elements);
        },

        /**
         * ==================================================================
         * SCROLLABLE CART - Implementação baseada na referência
         * ==================================================================
         */
        
        setupScrollableCart() {
            if (!this.config.scrollable || !this.elements.cart) {
                return;
            }
            
            const self = this;
            
            // Event listener de mouseover - Como na referência
            this.elements.cart.addEventListener('mouseover', function () {
                self.makeCartScrollable();
            });
            
            // Também aplicar quando o carrinho for aberto via click
            this.elements.cart.addEventListener('click', function () {
                self.makeCartScrollable();
            });
            
            // Aplicar em mudanças via AJAX
            document.addEventListener('added_to_cart', () => {
                setTimeout(() => self.makeCartScrollable(), 100);
            });
        },

        /**
         * Tornar carrinho scrollável - Baseado na referência
         */
        makeCartScrollable() {
            if (!this.elements.cartContent || !this.elements.cartList) {
                return;
            }
            
            // Cálculos - Similar à referência
            const windowHeight = window.outerHeight;
            const cartBottomPos = this.elements.cartContent.getBoundingClientRect().bottom + 
                                this.elements.cart.offsetHeight;
            
            // Aplicar scroll se necessário - Como na referência
            if (cartBottomPos > windowHeight) {
                this.elements.cartList.style.maxHeight = this.config.maxHeight; // Valor da referência
                this.elements.cartList.style.overflowY = 'auto';             // Valor da referência
                this.elements.cartList.classList.add('is-scrollable');
                
                this.log('Cart made scrollable');
                
                // Adicionar indicador de scroll
                this.addScrollIndicator();
                
                // Custom scrollbar
                this.styleScrollbar();
            } else {
                // Reset se não precisar de scroll
                this.elements.cartList.style.maxHeight = '';
                this.elements.cartList.style.overflowY = '';
                this.elements.cartList.classList.remove('is-scrollable');
                this.removeScrollIndicator();
            }
        },

        /**
         * Adicionar indicador de scroll
         */
        addScrollIndicator() {
            if (this.elements.cartList.querySelector('.scroll-indicator')) {
                return;
            }
            
            const indicator = document.createElement('div');
            indicator.className = 'scroll-indicator';
            indicator.innerHTML = '<span>↓ Scroll for more items ↓</span>';
            
            this.elements.cartList.appendChild(indicator);
            
            // Remover indicador ao fazer scroll
            this.elements.cartList.addEventListener('scroll', () => {
                const scrollTop = this.elements.cartList.scrollTop;
                const scrollHeight = this.elements.cartList.scrollHeight;
                const clientHeight = this.elements.cartList.clientHeight;
                
                if (scrollTop > 20) {
                    indicator.style.opacity = '0';
                } else {
                    indicator.style.opacity = '1';
                }
                
                // Mostrar indicador de "voltar ao topo" se necessário
                if (scrollTop > scrollHeight - clientHeight - 50) {
                    this.showScrollToTop();
                }
            });
        },

        /**
         * Remover indicador de scroll
         */
        removeScrollIndicator() {
            const indicator = this.elements.cartList?.querySelector('.scroll-indicator');
            if (indicator) {
                indicator.remove();
            }
        },

        /**
         * Estilizar scrollbar
         */
        styleScrollbar() {
            if (!this.elements.cartList) return;
            
            // Adicionar classe para custom scrollbar
            this.elements.cartList.classList.add('custom-scrollbar');
        },

        /**
         * ==================================================================
         * CART TRIGGER
         * ==================================================================
         */
        
        setupCartTrigger() {
            if (!this.elements.cartTrigger) return;
            
            this.elements.cartTrigger.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleCart();
            });
            
            // Hover para desktop
            if (window.innerWidth >= this.config.mobileBreakpoint) {
                this.elements.cartTrigger.addEventListener('mouseenter', () => {
                    this.openCart();
                });
                
                // Fechar ao sair (com delay)
                let closeTimeout;
                this.elements.cart?.addEventListener('mouseleave', () => {
                    closeTimeout = setTimeout(() => {
                        this.closeCart();
                    }, 500);
                });
                
                this.elements.cart?.addEventListener('mouseenter', () => {
                    clearTimeout(closeTimeout);
                });
            }
        },

        /**
         * Toggle cart
         */
        toggleCart() {
            if (this.state.isOpen) {
                this.closeCart();
            } else {
                this.openCart();
            }
        },

        /**
         * Open cart
         */
        openCart() {
            if (this.state.isOpen) return;
            
            this.state.isOpen = true;
            this.elements.cart?.classList.add('is-open');
            this.elements.cartOverlay?.classList.add('is-visible');
            document.body.classList.add('cart-open');
            
            // Recalcular scroll
            this.makeCartScrollable();
            
            // Animar entrada
            this.animateCartOpen();
            
            // Auto close se configurado
            if (this.config.autoClose) {
                this.setupAutoClose();
            }
            
            // Analytics
            this.trackEvent('cart_open');
            
            // Trigger event
            this.triggerEvent('headercart:open');
        },

        /**
         * Close cart
         */
        closeCart() {
            if (!this.state.isOpen) return;
            
            this.state.isOpen = false;
            this.elements.cart?.classList.remove('is-open');
            this.elements.cartOverlay?.classList.remove('is-visible');
            document.body.classList.remove('cart-open');
            
            // Animar saída
            this.animateCartClose();
            
            // Clear auto close
            this.clearAutoClose();
            
            // Trigger event
            this.triggerEvent('headercart:close');
        },

        /**
         * ==================================================================
         * CART EVENTS
         * ==================================================================
         */
        
        setupCartEvents() {
            // Botão fechar
            this.elements.closeButton?.addEventListener('click', (e) => {
                e.preventDefault();
                this.closeCart();
            });
            
            // Overlay click
            this.elements.cartOverlay?.addEventListener('click', () => {
                this.closeCart();
            });
            
            // Limpar carrinho
            this.elements.clearButton?.addEventListener('click', (e) => {
                e.preventDefault();
                this.clearCart();
            });
            
            // Continuar comprando
            this.elements.continueShoppingButton?.addEventListener('click', (e) => {
                e.preventDefault();
                this.closeCart();
            });
            
            // Quantidade de itens
            this.setupQuantityButtons();
            
            // Remover item
            this.setupRemoveButtons();
            
            // Cupons
            this.setupCouponForm();
        },

        /**
         * Setup quantity buttons
         */
        setupQuantityButtons() {
            document.addEventListener('click', (e) => {
                if (e.target.matches('.cart-item-quantity-plus')) {
                    e.preventDefault();
                    this.updateQuantity(e.target, 1);
                }
                
                if (e.target.matches('.cart-item-quantity-minus')) {
                    e.preventDefault();
                    this.updateQuantity(e.target, -1);
                }
            });
            
            // Input direto
            document.addEventListener('change', (e) => {
                if (e.target.matches('.cart-item-quantity-input')) {
                    this.updateQuantityDirect(e.target);
                }
            });
        },

        /**
         * Update quantity
         */
        updateQuantity(button, change) {
            const input = button.parentElement.querySelector('.cart-item-quantity-input');
            const currentValue = parseInt(input.value) || 1;
            const newValue = Math.max(1, currentValue + change);
            
            input.value = newValue;
            
            // Trigger update
            this.updateCartItem(button.closest('.cart-item'), newValue);
        },

        /**
         * Update quantity direct
         */
        updateQuantityDirect(input) {
            const newValue = Math.max(1, parseInt(input.value) || 1);
            input.value = newValue;
            
            this.updateCartItem(input.closest('.cart-item'), newValue);
        },

        /**
         * Setup remove buttons
         */
        setupRemoveButtons() {
            document.addEventListener('click', (e) => {
                if (e.target.matches('.remove-from-cart')) {
                    e.preventDefault();
                    this.removeCartItem(e.target);
                }
            });
        },

        /**
         * Setup coupon form
         */
        setupCouponForm() {
            const couponForm = this.elements.cart?.querySelector('.coupon-form');
            
            couponForm?.addEventListener('submit', (e) => {
                e.preventDefault();
                this.applyCoupon(e.target);
            });
        },

        /**
         * ==================================================================
         * AJAX HANDLERS
         * ==================================================================
         */
        
        setupAjaxHandlers() {
            // WooCommerce events
            jQuery(document.body).on('added_to_cart', (e, fragments, cart_hash, button) => {
                this.onItemAdded(fragments, cart_hash, button);
            });
            
            jQuery(document.body).on('removed_from_cart', (e, fragments, cart_hash, button) => {
                this.onItemRemoved(fragments, cart_hash, button);
            });
            
            jQuery(document.body).on('wc_fragments_refreshed', () => {
                this.onFragmentsRefreshed();
            });
            
            // Custom AJAX handlers
            this.setupCustomAjax();
        },

        /**
         * Setup custom AJAX
         */
        setupCustomAjax() {
            // Interceptar requisições AJAX do WooCommerce
            jQuery(document).ajaxComplete((event, xhr, settings) => {
                if (settings.url.includes('wc-ajax')) {
                    this.handleAjaxComplete(xhr, settings);
                }
            });
        },

        /**
         * On item added
         */
        onItemAdded(fragments, cart_hash, button) {
            this.log('Item added to cart', fragments);
            
            // Atualizar estado
            this.updateCartState(fragments);
            
            // Mostrar notificação
            this.showAddedNotification(button);
            
            // Abrir carrinho (opcional)
            if (this.shouldOpenCartOnAdd()) {
                this.openCart();
            }
            
            // Adicionar à lista de recentemente adicionados
            this.addToRecentlyAdded(button);
            
            // Animar ícone do carrinho
            this.animateCartIcon();
            
            // Analytics
            this.trackAddToCart(button);
            
            // Trigger event
            this.triggerEvent('headercart:item:added', { fragments, button });
        },

        /**
         * On item removed
         */
        onItemRemoved(fragments, cart_hash, button) {
            this.log('Item removed from cart', fragments);
            
            // Atualizar estado
            this.updateCartState(fragments);
            
            // Mostrar notificação
            this.showRemovedNotification(button);
            
            // Verificar se carrinho está vazio
            this.checkIfEmpty();
            
            // Trigger event
            this.triggerEvent('headercart:item:removed', { fragments, button });
        },

        /**
         * On fragments refreshed
         */
        onFragmentsRefreshed() {
            this.log('Cart fragments refreshed');
            
            // Recachear elementos
            this.cacheElements();
            
            // Reconfigurar scroll
            this.makeCartScrollable();
            
            // Atualizar contadores
            this.updateCartCounters();
            
            // Trigger event
            this.triggerEvent('headercart:refreshed');
        },

        /**
         * Update cart item via AJAX
         */
        updateCartItem(item, quantity) {
            const itemKey = item.dataset.cartItemKey;
            
            if (!itemKey) return;
            
            this.setLoadingState(true);
            
            jQuery.ajax({
                url: wc_add_to_cart_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'update_cart_item_quantity',
                    cart_item_key: itemKey,
                    quantity: quantity,
                    security: wc_add_to_cart_params.update_cart_nonce
                },
                success: (response) => {
                    if (response.success) {
                        // Atualizar fragmentos
                        this.updateFragments(response.data.fragments);
                        
                        // Atualizar totais
                        this.updateTotals(response.data);
                    }
                },
                complete: () => {
                    this.setLoadingState(false);
                }
            });
        },

        /**
         * Remove cart item
         */
        removeCartItem(button) {
            const item = button.closest('.cart-item');
            const itemKey = item?.dataset.cartItemKey;
            
            if (!itemKey) return;
            
            // Confirmar remoção
            if (!this.confirmRemoval(item)) {
                return;
            }
            
            // Animar saída
            item.classList.add('removing');
            
            setTimeout(() => {
                jQuery.ajax({
                    url: wc_add_to_cart_params.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'remove_from_cart',
                        cart_item_key: itemKey,
                        security: wc_add_to_cart_params.remove_from_cart_nonce
                    },
                    success: (response) => {
                        if (response.success) {
                            // Remover item do DOM
                            item.remove();
                            
                            // Atualizar fragmentos
                            this.updateFragments(response.data.fragments);
                            
                            // Verificar se vazio
                            this.checkIfEmpty();
                        }
                    }
                });
            }, 300);
        },

        /**
         * Clear cart
         */
        clearCart() {
            if (!confirm('Are you sure you want to clear your cart?')) {
                return;
            }
            
            this.setLoadingState(true);
            
            jQuery.ajax({
                url: wc_add_to_cart_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'clear_cart',
                    security: wc_add_to_cart_params.clear_cart_nonce
                },
                success: (response) => {
                    if (response.success) {
                        // Limpar UI
                        this.elements.cartList.innerHTML = '';
                        
                        // Atualizar estado
                        this.state.isEmpty = true;
                        this.state.itemCount = 0;
                        
                        // Mostrar mensagem de carrinho vazio
                        this.showEmptyMessage();
                        
                        // Fechar carrinho após delay
                        setTimeout(() => this.closeCart(), 2000);
                    }
                },
                complete: () => {
                    this.setLoadingState(false);
                }
            });
        },

        /**
         * Apply coupon
         */
        applyCoupon(form) {
            const couponCode = form.querySelector('input[name="coupon_code"]').value;
            
            if (!couponCode) return;
            
            this.setLoadingState(true);
            
            jQuery.ajax({
                url: wc_add_to_cart_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'apply_coupon',
                    coupon_code: couponCode,
                    security: wc_add_to_cart_params.apply_coupon_nonce
                },
                success: (response) => {
                    if (response.success) {
                        // Mostrar sucesso
                        this.showNotification('Coupon applied successfully!', 'success');
                        
                        // Atualizar totais
                        this.refreshCartFragments();
                        
                        // Limpar campo
                        form.reset();
                    } else {
                        // Mostrar erro
                        this.showNotification(response.data.message, 'error');
                    }
                },
                complete: () => {
                    this.setLoadingState(false);
                }
            });
        },

        /**
         * Refresh cart fragments
         */
        refreshCartFragments() {
            jQuery(document.body).trigger('wc_fragment_refresh');
        },

        /**
         * ==================================================================
         * MINI CART
         * ==================================================================
         */
        
        setupMiniCart() {
            if (!this.config.miniCartEnabled) return;
            
            // Criar mini cart se não existir
            if (!this.elements.miniCart) {
                this.createMiniCart();
            }
            
            // Setup eventos do mini cart
            this.setupMiniCartEvents();
        },

        /**
         * Create mini cart
         */
        createMiniCart() {
            const miniCart = document.createElement('div');
            miniCart.className = 'mini-cart-panel';
            miniCart.innerHTML = `
                <div class="mini-cart-header">
                    <h3>Recently Added</h3>
                    <button class="mini-cart-close">&times;</button>
                </div>
                <div class="mini-cart-items"></div>
                <div class="mini-cart-footer">
                    <a href="${wc_add_to_cart_params.cart_url}" class="button">View Cart</a>
                </div>
            `;
            
            document.body.appendChild(miniCart);
            this.elements.miniCart = miniCart;
        },

        /**
         * Setup mini cart events
         */
        setupMiniCartEvents() {
            // Close button
            this.elements.miniCart?.querySelector('.mini-cart-close')?.addEventListener('click', () => {
                this.hideMiniCart();
            });
        },

        /**
         * Show mini cart notification
         */
        showMiniCart(product) {
            if (!this.elements.miniCart) return;
            
            // Adicionar produto ao mini cart
            const itemsContainer = this.elements.miniCart.querySelector('.mini-cart-items');
            
            const itemHtml = `
                <div class="mini-cart-item" data-product-id="${product.id}">
                    <img src="${product.image}" alt="${product.name}">
                    <div class="mini-cart-item-details">
                        <h4>${product.name}</h4>
                        <span class="price">${product.price}</span>
                    </div>
                </div>
            `;
            
            itemsContainer.insertAdjacentHTML('afterbegin', itemHtml);
            
            // Limitar a 3 itens
            const items = itemsContainer.querySelectorAll('.mini-cart-item');
            if (items.length > 3) {
                items[items.length - 1].remove();
            }
            
            // Mostrar mini cart
            this.elements.miniCart.classList.add('is-visible');
            
            // Auto hide
            setTimeout(() => this.hideMiniCart(), 5000);
        },

        /**
         * Hide mini cart
         */
        hideMiniCart() {
            this.elements.miniCart?.classList.remove('is-visible');
        },

        /**
         * ==================================================================
         * QUICK ADD
         * ==================================================================
         */
        
        setupQuickAdd() {
            if (!this.config.quickAddEnabled) return;
            
            // Quick add buttons
            document.addEventListener('click', (e) => {
                if (e.target.matches('.quick-add-to-cart')) {
                    e.preventDefault();
                    this.quickAddToCart(e.target);
                }
            });
        },

        /**
         * Quick add to cart
         */
        quickAddToCart(button) {
            const productId = button.dataset.productId;
            const quantity = button.dataset.quantity || 1;
            
            // Adicionar loading
            button.classList.add('loading');
            
            jQuery.ajax({
                url: wc_add_to_cart_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'woocommerce_ajax_add_to_cart',
                    product_id: productId,
                    quantity: quantity,
                    product_sku: ''
                },
                success: (response) => {
                    if (response.error) {
                        this.showNotification(response.error_message, 'error');
                    } else {
                        // Trigger WooCommerce event
                        jQuery(document.body).trigger('added_to_cart', [
                            response.fragments,
                            response.cart_hash,
                            button
                        ]);
                    }
                },
                complete: () => {
                    button.classList.remove('loading');
                }
            });
        },

        /**
         * ==================================================================
         * SAVE FOR LATER
         * ==================================================================
         */
        
        setupSaveForLater() {
            if (!this.config.saveForLaterEnabled) return;
            
            // Load saved items
            this.loadSavedItems();
            
            // Save for later buttons
            document.addEventListener('click', (e) => {
                if (e.target.matches('.save-for-later')) {
                    e.preventDefault();
                    this.saveItemForLater(e.target);
                }
                
                if (e.target.matches('.move-to-cart')) {
                    e.preventDefault();
                    this.moveToCart(e.target);
                }
            });
        },

        /**
         * Save item for later
         */
        saveItemForLater(button) {
            const item = button.closest('.cart-item');
            const itemData = this.getItemData(item);
            
            // Adicionar aos salvos
            this.state.savedItems.push(itemData);
            this.saveSavedItems();
            
            // Remover do carrinho
            this.removeCartItem(button);
            
            // Mostrar notificação
            this.showNotification('Item saved for later', 'success');
            
            // Atualizar UI
            this.updateSavedItemsUI();
        },

        /**
         * Move to cart
         */
        moveToCart(button) {
            const savedItem = button.closest('.saved-item');
            const itemData = JSON.parse(savedItem.dataset.itemData);
            
            // Adicionar ao carrinho
            this.quickAddToCart({
                dataset: {
                    productId: itemData.product_id,
                    quantity: itemData.quantity
                }
            });
            
            // Remover dos salvos
            this.removeSavedItem(itemData.id);
            
            // Atualizar UI
            savedItem.remove();
        },

        /**
         * Load saved items
         */
        loadSavedItems() {
            const saved = localStorage.getItem('cart_saved_items');
            this.state.savedItems = saved ? JSON.parse(saved) : [];
        },

        /**
         * Save saved items
         */
        saveSavedItems() {
            localStorage.setItem('cart_saved_items', JSON.stringify(this.state.savedItems));
        },

        /**
         * ==================================================================
         * CROSS-SELL
         * ==================================================================
         */
        
        setupCrossSell() {
            if (!this.config.crossSellEnabled) return;
            
            // Carregar produtos cross-sell quando o carrinho abrir
            document.addEventListener('headercart:open', () => {
                this.loadCrossSellProducts();
            });
        },

        /**
         * Load cross-sell products
         */
        loadCrossSellProducts() {
            // Implementar carregamento de produtos relacionados
            jQuery.ajax({
                url: wc_add_to_cart_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_cross_sell_products',
                    cart_items: this.getCartItemIds()
                },
                success: (response) => {
                    if (response.success && response.data.products) {
                        this.displayCrossSellProducts(response.data.products);
                    }
                }
            });
        },

        /**
         * Display cross-sell products
         */
        displayCrossSellProducts(products) {
            // Implementar display de produtos relacionados
        },

        /**
         * ==================================================================
         * RECENTLY VIEWED
         * ==================================================================
         */
        
        setupRecentlyViewed() {
            if (!this.config.recentlyViewedEnabled) return;
            
            // Carregar produtos visualizados recentemente
            this.loadRecentlyViewed();
        },

        /**
         * Load recently viewed
         */
        loadRecentlyViewed() {
            const viewed = this.getCookie('woocommerce_recently_viewed');
            if (viewed) {
                const productIds = viewed.split('|').filter(id => id);
                this.displayRecentlyViewed(productIds);
            }
        },

        /**
         * Display recently viewed
         */
        displayRecentlyViewed(productIds) {
            // Implementar display de produtos visualizados
        },

        /**
         * ==================================================================
         * ANIMATIONS
         * ==================================================================
         */
        
        setupCartAnimations() {
            // Adicionar classes para animações CSS
            this.elements.cart?.classList.add('animated');
        },

        /**
         * Animate cart open
         */
        animateCartOpen() {
            if (!this.elements.cart) return;
            
            this.elements.cart.style.animation = 'slideInRight 0.3s ease';
            
            // Animar itens individualmente
            const items = this.elements.cartList?.querySelectorAll('.cart-item');
            items?.forEach((item, index) => {
                item.style.animation = `fadeInUp 0.3s ease ${index * 0.05}s`;
                item.style.animationFillMode = 'both';
            });
        },

        /**
         * Animate cart close
         */
        animateCartClose() {
            if (!this.elements.cart) return;
            
            this.elements.cart.style.animation = 'slideOutRight 0.3s ease';
        },

        /**
         * Animate cart icon
         */
        animateCartIcon() {
            this.elements.cartTrigger?.classList.add('bounce');
            
            setTimeout(() => {
                this.elements.cartTrigger?.classList.remove('bounce');
            }, 1000);
        },

        /**
         * ==================================================================
         * KEYBOARD NAVIGATION
         * ==================================================================
         */
        
        setupKeyboardNavigation() {
            document.addEventListener('keydown', (e) => {
                // ESC para fechar
                if (e.key === 'Escape' && this.state.isOpen) {
                    this.closeCart();
                }
                
                // Ctrl+Shift+C para abrir carrinho
                if (e.ctrlKey && e.shiftKey && e.key === 'C') {
                    e.preventDefault();
                    this.toggleCart();
                }
            });
        },

        /**
         * ==================================================================
         * STATE MANAGEMENT
         * ==================================================================
         */
        
        /**
         * Check cart state
         */
        checkCartState() {
            const items = this.elements.cartList?.querySelectorAll('.cart-item');
            this.state.itemCount = items?.length || 0;
            this.state.isEmpty = this.state.itemCount === 0;
            
            this.updateCartCounters();
            
            if (this.state.isEmpty) {
                this.showEmptyMessage();
            }
        },

        /**
         * Update cart state
         */
        updateCartState(fragments) {
            // Atualizar cache
            this.cache.fragments = fragments;
            
            // Atualizar contadores
            this.updateCartCounters();
            
            // Verificar se vazio
            this.checkIfEmpty();
        },

        /**
         * Update cart counters
         */
        updateCartCounters() {
            // Atualizar contador de itens
            if (this.elements.cartCount) {
                this.elements.cartCount.textContent = this.state.itemCount;
                
                // Mostrar/ocultar badge
                this.elements.cartCount.style.display = this.state.itemCount > 0 ? 'block' : 'none';
            }
            
            // Atualizar total
            this.updateCartTotal();
        },

        /**
         * Update cart total
         */
        updateCartTotal() {
            // Implementar atualização do total
        },

        /**
         * Check if empty
         */
        checkIfEmpty() {
            const items = this.elements.cartList?.querySelectorAll('.cart-item');
            
            if (!items || items.length === 0) {
                this.state.isEmpty = true;
                this.showEmptyMessage();
                
                // Fechar carrinho após delay
                if (this.state.isOpen) {
                    setTimeout(() => this.closeCart(), 3000);
                }
            }
        },

        /**
         * ==================================================================
         * UI HELPERS
         * ==================================================================
         */
        
        /**
         * Show empty message
         */
        showEmptyMessage() {
            if (!this.elements.cartList) return;
            
            this.elements.cartList.innerHTML = `
                <li class="empty-cart-message">
                    <p>Your cart is empty</p>
                    <a href="${wc_add_to_cart_params.shop_url}" class="button">Continue Shopping</a>
                </li>
            `;
        },

        /**
         * Show notification
         */
        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `cart-notification ${type}`;
            notification.textContent = message;
            
            this.elements.cart?.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);
            
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        },

        /**
         * Show added notification
         */
        showAddedNotification(button) {
            const productName = button?.dataset.productName || 'Item';
            this.showNotification(`${productName} added to cart`, 'success');
            
            // Mostrar mini cart
            if (this.config.miniCartEnabled) {
                this.showMiniCart({
                    id: button?.dataset.productId,
                    name: productName,
                    image: button?.dataset.productImage,
                    price: button?.dataset.productPrice
                });
            }
        },

        /**
         * Show removed notification
         */
        showRemovedNotification(button) {
            const productName = button?.dataset.productName || 'Item';
            this.showNotification(`${productName} removed from cart`, 'info');
        },

        /**
         * Set loading state
         */
        setLoadingState(loading) {
            this.state.isLoading = loading;
            
            if (loading) {
                this.elements.cart?.classList.add('is-loading');
            } else {
                this.elements.cart?.classList.remove('is-loading');
            }
        },

        /**
         * Confirm removal
         */
        confirmRemoval(item) {
            // Opção de desfazer ao invés de confirmar
            return true;
        },

        /**
         * Show scroll to top
         */
        showScrollToTop() {
            // Implementar botão de voltar ao topo
        },

        /**
         * ==================================================================
         * AUTO CLOSE
         * ==================================================================
         */
        
        /**
         * Setup auto close
         */
        setupAutoClose() {
            this.autoCloseTimeout = setTimeout(() => {
                if (this.state.isOpen && !this.isHovering()) {
                    this.closeCart();
                }
            }, this.config.autoCloseDelay);
        },

        /**
         * Clear auto close
         */
        clearAutoClose() {
            if (this.autoCloseTimeout) {
                clearTimeout(this.autoCloseTimeout);
                this.autoCloseTimeout = null;
            }
        },

        /**
         * Check if hovering
         */
        isHovering() {
            return this.elements.cart?.matches(':hover');
        },

        /**
         * ==================================================================
         * UTILITIES
         * ==================================================================
         */
        
        /**
         * Should open cart on add
         */
        shouldOpenCartOnAdd() {
            // Verificar configuração e contexto
            return !document.body.classList.contains('woocommerce-shop');
        },

        /**
         * Add to recently added
         */
        addToRecentlyAdded(button) {
            if (!button) return;
            
            this.state.recentlyAdded.unshift({
                id: button.dataset.productId,
                name: button.dataset.productName,
                time: Date.now()
            });
            
            // Limitar a 5 itens
            this.state.recentlyAdded = this.state.recentlyAdded.slice(0, 5);
        },

        /**
         * Get cart item IDs
         */
        getCartItemIds() {
            const items = this.elements.cartList?.querySelectorAll('.cart-item');
            return Array.from(items || []).map(item => item.dataset.productId);
        },

        /**
         * Get item data
         */
        getItemData(item) {
            return {
                id: item.dataset.cartItemKey,
                product_id: item.dataset.productId,
                name: item.querySelector('.product-name')?.textContent,
                quantity: item.querySelector('.quantity-input')?.value || 1,
                price: item.querySelector('.product-price')?.textContent
            };
        },

        /**
         * Update fragments
         */
        updateFragments(fragments) {
            // Implementar atualização de fragmentos
            jQuery.each(fragments, (key, value) => {
                jQuery(key).replaceWith(value);
            });
            
            // Recachear elementos
            this.cacheElements();
            
            // Reconfigurar
            this.makeCartScrollable();
        },

        /**
         * Update totals
         */
        updateTotals(data) {
            // Implementar atualização de totais
        },

        /**
         * Update saved items UI
         */
        updateSavedItemsUI() {
            // Implementar atualização da UI dos itens salvos
        },

        /**
         * Remove saved item
         */
        removeSavedItem(itemId) {
            this.state.savedItems = this.state.savedItems.filter(item => item.id !== itemId);
            this.saveSavedItems();
        },

        /**
         * Get cookie
         */
        getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) {
                return parts.pop().split(';').shift();
            }
            return null;
        },

        /**
         * Track event
         */
        trackEvent(eventName, data = {}) {
            // Google Analytics
            if (typeof ga !== 'undefined') {
                ga('send', 'event', 'Cart', eventName, JSON.stringify(data));
            }
            
            // Facebook Pixel
            if (typeof fbq !== 'undefined') {
                fbq('track', eventName, data);
            }
        },

        /**
         * Track add to cart
         */
        trackAddToCart(button) {
            const productData = {
                product_id: button?.dataset.productId,
                product_name: button?.dataset.productName,
                product_price: button?.dataset.productPrice
            };
            
            this.trackEvent('AddToCart', productData);
        },

        /**
         * Trigger custom event
         */
        triggerEvent(eventName, detail = {}) {
            const event = new CustomEvent(eventName, { detail });
            document.dispatchEvent(event);
            this.log(`Event triggered: ${eventName}`, detail);
        },

        /**
         * ==================================================================
         * EVENT BINDING
         * ==================================================================
         */
        
        bindEvents() {
            // Resize event para recalcular scroll
            window.addEventListener('resize', () => {
                this.makeCartScrollable();
            });
            
            // Visibility change
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden && this.state.isOpen) {
                    this.refreshCartFragments();
                }
            });
        }
    };

    /**
     * ==================================================================
     * INICIALIZAÇÃO
     * ==================================================================
     */
    
    // Inicializar
    NosfirHeaderCart.init();
    
    // Expor globalmente para debugging e extensibilidade
    window.NosfirHeaderCart = NosfirHeaderCart;

})();