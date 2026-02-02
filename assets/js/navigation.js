/**
 * Nosfir Navigation JavaScript
 * 
 * @package     Nosfir
 * @version     1.0.0
 * @author      David Creator
 * 
 * Gerencia navegação responsiva, menus dropdown, acessibilidade,
 * mega menu, busca e outras funcionalidades de navegação
 */

;(function() {
    'use strict';

    /**
     * ==================================================================
     * NOSFIR NAVIGATION MANAGER
     * ==================================================================
     */
    
    const NosfirNavigation = {
        
        /**
         * Configurações padrão
         */
        config: {
            mobileBreakpoint: 768,
            tabletBreakpoint: 1024,
            stickyEnabled: true,
            megaMenuEnabled: true,
            searchEnabled: true,
            animationSpeed: 300,
            submenuDelay: 150,
            touchThreshold: 50,
            keyboardNavEnabled: true,
            smoothScrollEnabled: true,
            mobileMenuStyle: 'slide', // slide, overlay, dropdown
            debug: false,
            // Seletores configuráveis
            selectors: {
                container: '#site-navigation, .site-navigation, .main-navigation',
                menu: '.nav-menu, .primary-menu, #primary-menu, ul',
                button: '.menu-toggle, .navbar-toggle, button[aria-controls]',
                header: '.site-header, #masthead, header',
                submenuParent: '.menu-item-has-children, .page_item_has_children',
                searchToggle: '.search-toggle, .nav-search-toggle',
                searchForm: '.header-search, .navigation-search, .search-form',
                overlay: '.menu-overlay, .mobile-menu-overlay',
                handheld: '.handheld-navigation, .mobile-navigation'
            }
        },

        /**
         * Elementos DOM
         */
        elements: {
            container: null,
            button: null,
            menu: null,
            handheld: null,
            header: null,
            searchForm: null,
            searchToggle: null,
            overlay: null,
            headerPlaceholder: null,
            liveRegion: null,
            submenus: [],
            megaMenus: [],
            dropdownToggles: []
        },

        /**
         * Estado da aplicação
         */
        state: {
            isInitialized: false,
            isOpen: false,
            isMobile: false,
            isTablet: false,
            isSticky: false,
            scrollPosition: 0,
            lastScrollPosition: 0,
            touchStartX: 0,
            touchStartY: 0,
            focusedElement: null,
            lastFocusedElement: null,
            openSubmenus: [],
            searchVisible: false,
            isTouch: false
        },

        /**
         * Textos para leitores de tela
         */
        i18n: {
            expand: 'Expand child menu',
            collapse: 'Collapse child menu',
            menu: 'Menu',
            close: 'Close menu',
            open: 'Open menu',
            search: 'Search',
            loading: 'Loading...',
            noResults: 'No results found',
            skipToContent: 'Skip to content'
        },

        /**
         * ==================================================================
         * INICIALIZAÇÃO
         * ==================================================================
         */
        
        /**
         * Inicializa a navegação
         */
        init() {
            // Prevenir dupla inicialização
            if (this.state.isInitialized) {
                this.log('Navigation already initialized');
                return;
            }

            // Aguardar DOM estar pronto
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.onReady());
            } else {
                this.onReady();
            }
        },

        /**
         * Quando o DOM estiver pronto
         */
        onReady() {
            this.log('Initializing navigation...');
            
            try {
                // Carregar configurações do WordPress
                this.loadWordPressConfig();
                
                // Detectar dispositivo touch
                this.detectTouch();
                
                // Configurar elementos principais
                if (!this.setupElements()) {
                    this.log('Required elements not found, aborting initialization', 'warn');
                    return;
                }
                
                // Setup de cada componente
                this.setupToggleButton();
                this.setupDropdowns();
                this.setupHandheldNavigation();
                this.setupMobileMenu();
                this.setupMegaMenu();
                this.setupSearch();
                this.setupSticky();
                this.setupSmoothScroll();
                this.setupKeyboardNav();
                this.setupAccessibility();
                
                // Bind eventos globais
                this.bindEvents();
                
                // Verificar viewport inicial
                this.checkViewport();
                
                // Marcar como inicializado
                this.state.isInitialized = true;
                
                // Disparar evento de ready
                this.triggerEvent('nosfir:navigation:ready');
                
                this.log('Navigation initialized successfully');
                
            } catch (error) {
                this.log('Error initializing navigation: ' + error.message, 'error');
                console.error(error);
            }
        },

        /**
         * Carrega configurações do WordPress (nosfirData)
         */
        loadWordPressConfig() {
            // Verificar se nosfirData existe (do wp_localize_script)
            if (typeof window.nosfirData !== 'undefined') {
                const wpData = window.nosfirData;
                
                // Merge i18n
                if (wpData.i18n) {
                    Object.assign(this.i18n, wpData.i18n);
                }
                
                // Merge breakpoints
                if (wpData.breakpoint) {
                    this.config.mobileBreakpoint = wpData.breakpoint.mobile || this.config.mobileBreakpoint;
                    this.config.tabletBreakpoint = wpData.breakpoint.tablet || this.config.tabletBreakpoint;
                }
                
                // Outras configurações
                if (wpData.is_mobile) {
                    this.state.isMobile = wpData.is_mobile;
                }
                
                this.log('WordPress config loaded');
            }
            
            // Compatibilidade com Storefront
            if (typeof window.storefrontScreenReaderText !== 'undefined') {
                Object.assign(this.i18n, window.storefrontScreenReaderText);
            }
            
            // Compatibilidade com nosfirScreenReaderText legado
            if (typeof window.nosfirScreenReaderText !== 'undefined') {
                Object.assign(this.i18n, window.nosfirScreenReaderText);
            }
        },

        /**
         * Configura elementos DOM principais
         * @returns {boolean} Se os elementos foram encontrados
         */
        setupElements() {
            const sel = this.config.selectors;
            
            // Container de navegação
            this.elements.container = this.querySelector(sel.container);
            
            if (!this.elements.container) {
                this.log('Navigation container not found');
                return false;
            }
            
            // Menu principal
            this.elements.menu = this.elements.container.querySelector(sel.menu);
            
            // Botão de toggle
            this.elements.button = this.elements.container.querySelector(sel.button);
            
            // Se não há menu, esconder botão
            if (!this.elements.menu && this.elements.button) {
                this.elements.button.style.display = 'none';
                this.log('Menu not found, hiding toggle button');
                return false;
            }
            
            // Elementos adicionais
            this.elements.header = this.querySelector(sel.header);
            this.elements.overlay = this.querySelector(sel.overlay);
            this.elements.searchToggle = this.querySelector(sel.searchToggle);
            this.elements.searchForm = this.querySelector(sel.searchForm);
            this.elements.handheld = this.querySelector(sel.handheld);
            
            // Configurar atributos ARIA iniciais
            this.setupInitialAria();
            
            this.log('Elements setup complete');
            return true;
        },

        /**
         * Configura atributos ARIA iniciais
         */
        setupInitialAria() {
            if (this.elements.button) {
                this.elements.button.setAttribute('aria-expanded', 'false');
                
                if (!this.elements.button.hasAttribute('aria-controls') && this.elements.menu) {
                    const menuId = this.elements.menu.id || 'primary-menu';
                    this.elements.menu.id = menuId;
                    this.elements.button.setAttribute('aria-controls', menuId);
                }
                
                if (!this.elements.button.hasAttribute('aria-label')) {
                    this.elements.button.setAttribute('aria-label', this.i18n.menu);
                }
            }
            
            if (this.elements.menu) {
                this.elements.menu.setAttribute('aria-expanded', 'false');
            }
        },

        /**
         * ==================================================================
         * TOGGLE BUTTON
         * ==================================================================
         */
        
        /**
         * Configura o botão de toggle do menu
         */
        setupToggleButton() {
            if (!this.elements.button) {
                this.log('Toggle button not found');
                return;
            }
            
            // Click handler
            this.elements.button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleMenu();
            });
            
            // Adicionar estrutura hamburger se não existir
            this.ensureHamburgerStructure();
            
            this.log('Toggle button setup complete');
        },

        /**
         * Garante que o botão tem estrutura de hamburger
         */
        ensureHamburgerStructure() {
            if (!this.elements.button) return;
            
            // Verificar se já tem estrutura
            if (this.elements.button.querySelector('.hamburger, .menu-icon')) {
                return;
            }
            
            // Verificar se tem texto/conteúdo
            if (this.elements.button.children.length > 0) {
                return;
            }
            
            // Criar estrutura hamburger
            const hamburger = document.createElement('span');
            hamburger.className = 'hamburger';
            hamburger.setAttribute('aria-hidden', 'true');
            hamburger.innerHTML = `
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            `;
            
            // Adicionar texto para screen readers
            const srText = document.createElement('span');
            srText.className = 'screen-reader-text';
            srText.textContent = this.i18n.menu;
            
            this.elements.button.appendChild(hamburger);
            this.elements.button.appendChild(srText);
        },

        /**
         * Toggle do menu
         */
        toggleMenu() {
            if (this.state.isOpen) {
                this.closeMenu();
            } else {
                this.openMenu();
            }
        },

        /**
         * Abre o menu
         */
        openMenu() {
            if (this.state.isOpen) return;
            
            this.state.isOpen = true;
            this.state.lastFocusedElement = document.activeElement;
            
            // Atualizar classes
            this.elements.container?.classList.add('toggled', 'is-open');
            this.elements.button?.classList.add('is-active');
            document.body.classList.add('menu-open', 'nav-open');
            
            // Atualizar ARIA
            this.elements.button?.setAttribute('aria-expanded', 'true');
            this.elements.menu?.setAttribute('aria-expanded', 'true');
            
            // Atualizar texto do botão
            this.updateButtonText(true);
            
            // Mostrar overlay
            this.showOverlay();
            
            // Focar primeiro item
            this.focusFirstMenuItem();
            
            // Anunciar para screen readers
            this.announce(this.i18n.menu + ' ' + this.i18n.open);
            
            // Trigger evento
            this.triggerEvent('nosfir:menu:open');
            
            this.log('Menu opened');
        },

        /**
         * Fecha o menu
         */
        closeMenu() {
            if (!this.state.isOpen) return;
            
            this.state.isOpen = false;
            
            // Atualizar classes
            this.elements.container?.classList.remove('toggled', 'is-open');
            this.elements.button?.classList.remove('is-active');
            document.body.classList.remove('menu-open', 'nav-open');
            
            // Atualizar ARIA
            this.elements.button?.setAttribute('aria-expanded', 'false');
            this.elements.menu?.setAttribute('aria-expanded', 'false');
            
            // Atualizar texto do botão
            this.updateButtonText(false);
            
            // Esconder overlay
            this.hideOverlay();
            
            // Fechar submenus
            this.closeAllSubmenus();
            
            // Retornar foco
            if (this.state.lastFocusedElement) {
                this.state.lastFocusedElement.focus();
            }
            
            // Anunciar para screen readers
            this.announce(this.i18n.menu + ' ' + this.i18n.close);
            
            // Trigger evento
            this.triggerEvent('nosfir:menu:close');
            
            this.log('Menu closed');
        },

        /**
         * Atualiza texto do botão
         */
        updateButtonText(isOpen) {
            if (!this.elements.button) return;
            
            const srText = this.elements.button.querySelector('.screen-reader-text');
            if (srText) {
                srText.textContent = isOpen ? this.i18n.close : this.i18n.menu;
            }
            
            this.elements.button.setAttribute('aria-label', isOpen ? this.i18n.close : this.i18n.menu);
        },

        /**
         * ==================================================================
         * DROPDOWNS / SUBMENUS
         * ==================================================================
         */
        
        /**
         * Configura dropdowns
         */
        setupDropdowns() {
            const parents = this.elements.container?.querySelectorAll(
                this.config.selectors.submenuParent
            );
            
            if (!parents || parents.length === 0) {
                this.log('No dropdown parents found');
                return;
            }
            
            parents.forEach((parent) => {
                this.setupDropdownItem(parent);
            });
            
            this.log(`Setup ${parents.length} dropdowns`);
        },

        /**
         * Configura item dropdown individual
         */
        setupDropdownItem(parent) {
            const link = parent.querySelector(':scope > a');
            const submenu = parent.querySelector(':scope > ul, :scope > .sub-menu');
            
            if (!submenu) return;
            
            // Armazenar referência
            this.elements.submenus.push({ parent, submenu, link });
            
            // Adicionar botão de toggle para mobile/acessibilidade
            const toggleBtn = this.createDropdownToggle(parent, submenu);
            
            // Configurar eventos com base no tipo de dispositivo
            this.setupDropdownEvents(parent, submenu, link, toggleBtn);
        },

        /**
         * Cria botão de toggle para dropdown
         */
        createDropdownToggle(parent, submenu) {
            // Verificar se já existe
            if (parent.querySelector('.dropdown-toggle')) {
                return parent.querySelector('.dropdown-toggle');
            }
            
            const button = document.createElement('button');
            button.className = 'dropdown-toggle';
            button.setAttribute('aria-expanded', 'false');
            button.setAttribute('aria-label', this.i18n.expand);
            button.innerHTML = `
                <span class="dropdown-icon" aria-hidden="true">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="2,4 6,8 10,4"></polyline>
                    </svg>
                </span>
                <span class="screen-reader-text">${this.i18n.expand}</span>
            `;
            
            // Inserir após o link
            const link = parent.querySelector(':scope > a');
            if (link) {
                link.parentNode.insertBefore(button, link.nextSibling);
            } else {
                parent.insertBefore(button, submenu);
            }
            
            // Armazenar referência
            this.elements.dropdownToggles.push(button);
            
            // Verificar se deve estar aberto (current ancestor)
            if (parent.classList.contains('current-menu-ancestor') || 
                parent.classList.contains('current-menu-parent')) {
                button.setAttribute('aria-expanded', 'true');
                button.classList.add('toggled-on');
                submenu.classList.add('toggled-on', 'is-open');
            }
            
            return button;
        },

        /**
         * Configura eventos do dropdown
         */
        setupDropdownEvents(parent, submenu, link, toggleBtn) {
            // Eventos do botão toggle
            toggleBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleDropdown(toggleBtn, submenu, parent);
            });
            
            // Eventos para desktop (hover)
            if (!this.state.isTouch) {
                let hoverTimeout;
                
                parent.addEventListener('mouseenter', () => {
                    if (!this.state.isMobile) {
                        clearTimeout(hoverTimeout);
                        this.openSubmenu(submenu, parent);
                    }
                });
                
                parent.addEventListener('mouseleave', () => {
                    if (!this.state.isMobile) {
                        hoverTimeout = setTimeout(() => {
                            this.closeSubmenu(submenu, parent);
                        }, this.config.submenuDelay);
                    }
                });
            }
            
            // Eventos de foco para acessibilidade
            link?.addEventListener('focus', () => {
                this.handleLinkFocus(parent);
            });
            
            link?.addEventListener('blur', () => {
                this.handleLinkBlur(parent, submenu);
            });
        },

        /**
         * Toggle do dropdown
         */
        toggleDropdown(button, submenu, parent) {
            const isOpen = button.getAttribute('aria-expanded') === 'true';
            
            if (isOpen) {
                this.closeSubmenu(submenu, parent);
                button.setAttribute('aria-expanded', 'false');
                button.classList.remove('toggled-on');
                this.updateDropdownText(button, false);
            } else {
                this.openSubmenu(submenu, parent);
                button.setAttribute('aria-expanded', 'true');
                button.classList.add('toggled-on');
                this.updateDropdownText(button, true);
            }
        },

        /**
         * Atualiza texto do dropdown
         */
        updateDropdownText(button, isOpen) {
            const srText = button.querySelector('.screen-reader-text');
            if (srText) {
                srText.textContent = isOpen ? this.i18n.collapse : this.i18n.expand;
            }
            button.setAttribute('aria-label', isOpen ? this.i18n.collapse : this.i18n.expand);
        },

        /**
         * Abre submenu
         */
        openSubmenu(submenu, parent) {
            if (!submenu) return;
            
            submenu.classList.add('is-open', 'toggled-on');
            parent?.classList.add('has-open-submenu', 'focus');
            
            // Posicionar submenu se necessário
            this.positionSubmenu(submenu);
            
            // Track submenu aberto
            if (!this.state.openSubmenus.includes(submenu)) {
                this.state.openSubmenus.push(submenu);
            }
            
            this.triggerEvent('nosfir:submenu:open', { submenu, parent });
        },

        /**
         * Fecha submenu
         */
        closeSubmenu(submenu, parent) {
            if (!submenu) return;
            
            submenu.classList.remove('is-open', 'toggled-on');
            parent?.classList.remove('has-open-submenu', 'focus');
            
            // Fechar submenus aninhados
            const nestedSubmenus = submenu.querySelectorAll('.is-open');
            nestedSubmenus.forEach(nested => {
                nested.classList.remove('is-open', 'toggled-on');
            });
            
            // Remover do tracking
            const index = this.state.openSubmenus.indexOf(submenu);
            if (index > -1) {
                this.state.openSubmenus.splice(index, 1);
            }
            
            this.triggerEvent('nosfir:submenu:close', { submenu, parent });
        },

        /**
         * Fecha todos os submenus
         */
        closeAllSubmenus() {
            this.state.openSubmenus.forEach(submenu => {
                submenu.classList.remove('is-open', 'toggled-on');
                submenu.parentElement?.classList.remove('has-open-submenu', 'focus');
            });
            
            // Reset toggles
            this.elements.dropdownToggles.forEach(toggle => {
                toggle.setAttribute('aria-expanded', 'false');
                toggle.classList.remove('toggled-on');
            });
            
            this.state.openSubmenus = [];
        },

        /**
         * Posiciona submenu para evitar overflow
         */
        positionSubmenu(submenu) {
            if (!submenu || this.state.isMobile) return;
            
            // Reset position
            submenu.style.left = '';
            submenu.style.right = '';
            submenu.classList.remove('submenu-left', 'submenu-right');
            
            // Forçar reflow
            submenu.offsetHeight;
            
            const rect = submenu.getBoundingClientRect();
            const viewportWidth = window.innerWidth;
            const padding = 20;
            
            // Verificar se sai pela direita
            if (rect.right > viewportWidth - padding) {
                submenu.classList.add('submenu-left');
            }
            
            // Verificar se sai pela esquerda
            if (rect.left < padding) {
                submenu.classList.add('submenu-right');
            }
        },

        /**
         * Handle foco no link
         */
        handleLinkFocus(parent) {
            // Remover foco de outros itens
            const focusedItems = document.querySelectorAll('.focus');
            focusedItems.forEach(item => {
                if (!item.contains(parent) && !parent.contains(item)) {
                    item.classList.remove('focus');
                }
            });
            
            parent.classList.add('focus');
        },

        /**
         * Handle blur no link
         */
        handleLinkBlur(parent, submenu) {
            // Delay para verificar se o foco foi para dentro do submenu
            setTimeout(() => {
                if (!parent.contains(document.activeElement)) {
                    parent.classList.remove('focus');
                    if (!this.state.isMobile) {
                        this.closeSubmenu(submenu, parent);
                    }
                }
            }, 100);
        },

        /**
         * ==================================================================
         * HANDHELD / MOBILE NAVIGATION
         * ==================================================================
         */
        
        /**
         * Configura navegação handheld
         */
        setupHandheldNavigation() {
            if (!this.elements.handheld) {
                this.log('Handheld navigation not found');
                return;
            }
            
            // Configurar toggles para handheld
            const links = this.elements.handheld.querySelectorAll(
                '.menu-item-has-children > a, .page_item_has_children > a'
            );
            
            links.forEach(link => {
                this.addHandheldToggle(link);
            });
            
            this.log('Handheld navigation setup complete');
        },

        /**
         * Adiciona toggle para navegação handheld
         */
        addHandheldToggle(anchor) {
            const parent = anchor.parentNode;
            
            // Verificar se já tem toggle
            if (parent.querySelector('.dropdown-toggle')) {
                return;
            }
            
            const submenu = parent.querySelector(':scope > ul');
            if (!submenu) return;
            
            // Criar toggle
            const toggle = this.createDropdownToggle(parent, submenu);
            
            // Evento de click
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleDropdown(toggle, submenu, parent);
            });
        },

        /**
         * ==================================================================
         * MOBILE MENU
         * ==================================================================
         */
        
        /**
         * Configura menu mobile
         */
        setupMobileMenu() {
            // Criar overlay se não existir
            this.ensureOverlay();
            
            // Adicionar classe de ready
            this.elements.container?.classList.add('mobile-ready');
            
            // Setup gestos de touch
            if (this.state.isTouch) {
                this.setupTouchGestures();
            }
            
            this.log('Mobile menu setup complete');
        },

        /**
         * Garante que overlay existe
         */
        ensureOverlay() {
            if (this.elements.overlay) return;
            
            const overlay = document.createElement('div');
            overlay.className = 'menu-overlay mobile-menu-overlay';
            overlay.setAttribute('aria-hidden', 'true');
            document.body.appendChild(overlay);
            
            this.elements.overlay = overlay;
            
            // Fechar menu ao clicar no overlay
            overlay.addEventListener('click', () => {
                this.closeMenu();
            });
        },

        /**
         * Mostra overlay
         */
        showOverlay() {
            if (!this.elements.overlay) return;
            
            this.elements.overlay.classList.add('is-visible', 'visible');
            this.elements.overlay.setAttribute('aria-hidden', 'false');
        },

        /**
         * Esconde overlay
         */
        hideOverlay() {
            if (!this.elements.overlay) return;
            
            this.elements.overlay.classList.remove('is-visible', 'visible');
            this.elements.overlay.setAttribute('aria-hidden', 'true');
        },

        /**
         * Setup gestos de touch
         */
        setupTouchGestures() {
            let startX = 0;
            let startY = 0;
            let startTime = 0;
            
            document.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
                startTime = Date.now();
            }, { passive: true });
            
            document.addEventListener('touchend', (e) => {
                const endX = e.changedTouches[0].clientX;
                const endY = e.changedTouches[0].clientY;
                const endTime = Date.now();
                
                const diffX = endX - startX;
                const diffY = endY - startY;
                const diffTime = endTime - startTime;
                
                // Verificar se é um swipe válido
                if (diffTime < 500 && Math.abs(diffX) > this.config.touchThreshold && Math.abs(diffX) > Math.abs(diffY)) {
                    if (diffX > 0 && startX < 50) {
                        // Swipe right from edge - abrir menu
                        if (!this.state.isOpen) {
                            this.openMenu();
                        }
                    } else if (diffX < 0 && this.state.isOpen) {
                        // Swipe left - fechar menu
                        this.closeMenu();
                    }
                }
            });
        },

        /**
         * ==================================================================
         * MEGA MENU
         * ==================================================================
         */
        
        /**
         * Configura mega menus
         */
        setupMegaMenu() {
            if (!this.config.megaMenuEnabled) return;
            
            const megaMenus = document.querySelectorAll('.mega-menu, .is-mega-menu');
            
            if (megaMenus.length === 0) {
                this.log('No mega menus found');
                return;
            }
            
            megaMenus.forEach(megaMenu => {
                this.initMegaMenu(megaMenu);
            });
            
            this.log(`Setup ${megaMenus.length} mega menus`);
        },

        /**
         * Inicializa mega menu individual
         */
        initMegaMenu(megaMenu) {
            const parent = megaMenu.parentElement;
            
            // Armazenar referência
            this.elements.megaMenus.push({ megaMenu, parent });
            
            // Posicionar
            this.positionMegaMenu(megaMenu);
            
            // Eventos
            if (!this.state.isTouch) {
                parent?.addEventListener('mouseenter', () => {
                    if (!this.state.isMobile) {
                        megaMenu.classList.add('is-open');
                        this.animateMegaMenuIn(megaMenu);
                    }
                });
                
                parent?.addEventListener('mouseleave', () => {
                    if (!this.state.isMobile) {
                        megaMenu.classList.remove('is-open');
                    }
                });
            }
        },

        /**
         * Posiciona mega menu
         */
        positionMegaMenu(megaMenu) {
            if (this.state.isMobile) return;
            
            // Centralizar no viewport
            const header = this.elements.header;
            if (header) {
                const headerRect = header.getBoundingClientRect();
                megaMenu.style.left = -megaMenu.parentElement.getBoundingClientRect().left + 'px';
                megaMenu.style.width = '100vw';
            }
        },

        /**
         * Anima mega menu entrando
         */
        animateMegaMenuIn(megaMenu) {
            const columns = megaMenu.querySelectorAll('.mega-menu-column, .mega-menu-section');
            
            columns.forEach((column, index) => {
                column.style.animationDelay = (index * 50) + 'ms';
                column.classList.add('animate-in');
            });
        },

        /**
         * ==================================================================
         * SEARCH
         * ==================================================================
         */
        
        /**
         * Configura busca
         */
        setupSearch() {
            if (!this.config.searchEnabled) return;
            
            if (!this.elements.searchToggle || !this.elements.searchForm) {
                this.log('Search elements not found');
                return;
            }
            
            // Toggle search
            this.elements.searchToggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleSearch();
            });
            
            // Fechar com ESC
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.state.searchVisible) {
                    this.closeSearch();
                }
            });
            
            // Fechar ao clicar fora
            document.addEventListener('click', (e) => {
                if (this.state.searchVisible && 
                    !this.elements.searchForm.contains(e.target) && 
                    !this.elements.searchToggle.contains(e.target)) {
                    this.closeSearch();
                }
            });
            
            this.log('Search setup complete');
        },

        /**
         * Toggle search
         */
        toggleSearch() {
            if (this.state.searchVisible) {
                this.closeSearch();
            } else {
                this.openSearch();
            }
        },

        /**
         * Abre search
         */
        openSearch() {
            this.state.searchVisible = true;
            
            this.elements.searchForm?.classList.add('is-visible', 'visible');
            this.elements.searchToggle?.setAttribute('aria-expanded', 'true');
            
            // Focar input
            const input = this.elements.searchForm?.querySelector('input[type="search"], input[type="text"]');
            setTimeout(() => input?.focus(), 100);
            
            this.triggerEvent('nosfir:search:open');
        },

        /**
         * Fecha search
         */
        closeSearch() {
            this.state.searchVisible = false;
            
            this.elements.searchForm?.classList.remove('is-visible', 'visible');
            this.elements.searchToggle?.setAttribute('aria-expanded', 'false');
            
            this.triggerEvent('nosfir:search:close');
        },

        /**
         * ==================================================================
         * STICKY HEADER
         * ==================================================================
         */
        
        /**
         * Configura sticky header
         */
        setupSticky() {
            if (!this.config.stickyEnabled || !this.elements.header) {
                return;
            }
            
            // Criar placeholder
            const placeholder = document.createElement('div');
            placeholder.className = 'header-placeholder';
            placeholder.style.display = 'none';
            this.elements.header.parentNode?.insertBefore(placeholder, this.elements.header.nextSibling);
            this.elements.headerPlaceholder = placeholder;
            
            this.log('Sticky header setup complete');
        },

        /**
         * Handle sticky no scroll
         */
        handleSticky() {
            if (!this.config.stickyEnabled || !this.elements.header) return;
            
            const scrollY = window.pageYOffset || document.documentElement.scrollTop;
            const triggerPoint = 100;
            
            if (scrollY > triggerPoint && !this.state.isSticky) {
                // Ativar sticky
                this.state.isSticky = true;
                
                const headerHeight = this.elements.header.offsetHeight;
                this.elements.headerPlaceholder.style.height = headerHeight + 'px';
                this.elements.headerPlaceholder.style.display = 'block';
                
                this.elements.header.classList.add('is-sticky', 'sticky');
                document.body.classList.add('has-sticky-header');
                
                this.triggerEvent('nosfir:sticky:activate');
                
            } else if (scrollY <= triggerPoint && this.state.isSticky) {
                // Desativar sticky
                this.state.isSticky = false;
                
                this.elements.headerPlaceholder.style.display = 'none';
                
                this.elements.header.classList.remove('is-sticky', 'sticky', 'sticky-hidden');
                document.body.classList.remove('has-sticky-header');
                
                this.triggerEvent('nosfir:sticky:deactivate');
            }
            
            // Auto-hide ao scrollar para baixo
            if (this.state.isSticky) {
                const scrollDiff = scrollY - this.state.lastScrollPosition;
                
                if (scrollDiff > 10 && scrollY > 300) {
                    this.elements.header.classList.add('sticky-hidden');
                } else if (scrollDiff < -10) {
                    this.elements.header.classList.remove('sticky-hidden');
                }
            }
            
            this.state.lastScrollPosition = scrollY;
        },

        /**
         * ==================================================================
         * SMOOTH SCROLL
         * ==================================================================
         */
        
        /**
         * Configura smooth scroll
         */
        setupSmoothScroll() {
            if (!this.config.smoothScrollEnabled) return;
            
            const anchors = document.querySelectorAll('a[href^="#"]:not([href="#"]):not([href="#0"])');
            
            anchors.forEach(anchor => {
                anchor.addEventListener('click', (e) => {
                    const targetId = anchor.getAttribute('href');
                    const target = document.querySelector(targetId);
                    
                    if (target) {
                        e.preventDefault();
                        this.scrollToElement(target);
                        
                        // Fechar menu mobile se aberto
                        if (this.state.isOpen) {
                            this.closeMenu();
                        }
                    }
                });
            });
            
            this.log('Smooth scroll setup complete');
        },

        /**
         * Scroll para elemento
         */
        scrollToElement(target) {
            const headerHeight = this.elements.header?.offsetHeight || 0;
            const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight - 20;
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
            
            // Atualizar URL sem recarregar
            if (target.id) {
                history.pushState(null, '', '#' + target.id);
            }
            
            // Focar no elemento alvo
            target.setAttribute('tabindex', '-1');
            target.focus({ preventScroll: true });
        },

        /**
         * ==================================================================
         * KEYBOARD NAVIGATION
         * ==================================================================
         */
        
        /**
         * Configura navegação por teclado
         */
        setupKeyboardNav() {
            if (!this.config.keyboardNavEnabled) return;
            
            document.addEventListener('keydown', (e) => {
                this.handleKeydown(e);
            });
            
            this.log('Keyboard navigation setup complete');
        },

        /**
         * Handle keydown
         */
        handleKeydown(e) {
            switch (e.key) {
                case 'Escape':
                    if (this.state.isOpen) {
                        e.preventDefault();
                        this.closeMenu();
                    }
                    if (this.state.searchVisible) {
                        e.preventDefault();
                        this.closeSearch();
                    }
                    this.closeAllSubmenus();
                    break;
                    
                case 'Tab':
                    if (this.state.isOpen) {
                        this.handleTabTrap(e);
                    }
                    break;
                    
                case 'ArrowDown':
                case 'ArrowUp':
                case 'ArrowLeft':
                case 'ArrowRight':
                    if (document.activeElement?.closest('.nav-menu, .primary-menu')) {
                        this.handleArrowNav(e);
                    }
                    break;
            }
        },

        /**
         * Trap focus dentro do menu
         */
        handleTabTrap(e) {
            const focusable = this.elements.menu?.querySelectorAll(
                'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
            );
            
            if (!focusable || focusable.length === 0) return;
            
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            
            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault();
                last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        },

        /**
         * Handle navegação por setas
         */
        handleArrowNav(e) {
            const current = document.activeElement;
            if (!current) return;
            
            const menuItem = current.closest('.menu-item, .page_item');
            if (!menuItem) return;
            
            let nextItem = null;
            
            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    nextItem = menuItem.nextElementSibling;
                    
                    // Se for submenu, ir para primeiro item
                    const submenu = menuItem.querySelector(':scope > ul');
                    if (submenu && submenu.classList.contains('is-open')) {
                        nextItem = submenu.querySelector('.menu-item, .page_item');
                    }
                    break;
                    
                case 'ArrowUp':
                    e.preventDefault();
                    nextItem = menuItem.previousElementSibling;
                    break;
                    
                case 'ArrowRight':
                    // Abrir submenu
                    const subMenu = menuItem.querySelector(':scope > ul');
                    if (subMenu) {
                        e.preventDefault();
                        this.openSubmenu(subMenu, menuItem);
                        const firstChild = subMenu.querySelector('.menu-item a, .page_item a');
                        firstChild?.focus();
                    }
                    break;
                    
                case 'ArrowLeft':
                    // Ir para menu pai
                    const parentMenu = menuItem.closest('ul').closest('.menu-item, .page_item');
                    if (parentMenu) {
                        e.preventDefault();
                        const parentSubmenu = menuItem.closest('ul');
                        this.closeSubmenu(parentSubmenu, parentMenu);
                        parentMenu.querySelector(':scope > a')?.focus();
                    }
                    break;
            }
            
            if (nextItem) {
                const nextLink = nextItem.querySelector(':scope > a');
                nextLink?.focus();
            }
        },

        /**
         * ==================================================================
         * ACCESSIBILITY
         * ==================================================================
         */
        
        /**
         * Configura acessibilidade
         */
        setupAccessibility() {
            // Criar live region para anúncios
            this.createLiveRegion();
            
            // Skip link
            this.ensureSkipLink();
            
            this.log('Accessibility setup complete');
        },

        /**
         * Cria região live para anúncios
         */
        createLiveRegion() {
            if (document.querySelector('.nosfir-live-region')) return;
            
            const liveRegion = document.createElement('div');
            liveRegion.className = 'nosfir-live-region screen-reader-text';
            liveRegion.setAttribute('role', 'status');
            liveRegion.setAttribute('aria-live', 'polite');
            liveRegion.setAttribute('aria-atomic', 'true');
            document.body.appendChild(liveRegion);
            
            this.elements.liveRegion = liveRegion;
        },

        /**
         * Garante skip link
         */
        ensureSkipLink() {
            if (document.querySelector('.skip-link')) return;
            
            const main = document.querySelector('#main, #content, main, [role="main"]');
            if (!main) return;
            
            const mainId = main.id || 'main-content';
            main.id = mainId;
            
            const skipLink = document.createElement('a');
            skipLink.className = 'skip-link screen-reader-text';
            skipLink.href = '#' + mainId;
            skipLink.textContent = this.i18n.skipToContent;
            
            document.body.insertBefore(skipLink, document.body.firstChild);
        },

        /**
         * Anuncia para screen readers
         */
        announce(message) {
            if (!this.elements.liveRegion) return;
            
            this.elements.liveRegion.textContent = '';
            
            // Pequeno delay para garantir que o screen reader detecte a mudança
            setTimeout(() => {
                this.elements.liveRegion.textContent = message;
            }, 100);
        },

        /**
         * Foca primeiro item do menu
         */
        focusFirstMenuItem() {
            setTimeout(() => {
                const firstLink = this.elements.menu?.querySelector('a');
                firstLink?.focus();
            }, 100);
        },

        /**
         * ==================================================================
         * EVENTS
         * ==================================================================
         */
        
        /**
         * Bind eventos globais
         */
        bindEvents() {
            // Resize
            let resizeTimeout;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    this.handleResize();
                }, 150);
            });
            
            // Scroll (throttled)
            let ticking = false;
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        this.handleSticky();
                        ticking = false;
                    });
                    ticking = true;
                }
            }, { passive: true });
            
            // Orientation change
            window.addEventListener('orientationchange', () => {
                setTimeout(() => this.handleResize(), 100);
            });
            
            // Fechar menu ao clicar fora
            document.addEventListener('click', (e) => {
                if (this.state.isOpen && 
                    !this.elements.container?.contains(e.target) &&
                    !this.elements.button?.contains(e.target)) {
                    this.closeMenu();
                }
            });
            
            this.log('Events bound');
        },

        /**
         * Handle resize
         */
        handleResize() {
            const wassMobile = this.state.isMobile;
            
            this.checkViewport();
            
            // Se mudou de mobile para desktop, fechar menu
            if (wassMobile && !this.state.isMobile && this.state.isOpen) {
                this.closeMenu();
            }
            
            // Reposicionar submenus
            this.repositionSubmenus();
            
            // Reposicionar mega menus
            this.elements.megaMenus.forEach(({ megaMenu }) => {
                this.positionMegaMenu(megaMenu);
            });
            
            this.triggerEvent('nosfir:resize');
        },

        /**
         * Verifica viewport
         */
        checkViewport() {
            const width = window.innerWidth;
            
            this.state.isMobile = width < this.config.mobileBreakpoint;
            this.state.isTablet = width >= this.config.mobileBreakpoint && width < this.config.tabletBreakpoint;
            
            // Atualizar classes do body
            document.body.classList.toggle('is-mobile', this.state.isMobile);
            document.body.classList.toggle('is-tablet', this.state.isTablet);
            document.body.classList.toggle('is-desktop', !this.state.isMobile && !this.state.isTablet);
        },

        /**
         * Reposiciona todos os submenus
         */
        repositionSubmenus() {
            this.elements.submenus.forEach(({ submenu }) => {
                this.positionSubmenu(submenu);
            });
        },

        /**
         * Detecta dispositivo touch
         */
        detectTouch() {
            this.state.isTouch = ('ontouchstart' in window) || 
                                 (navigator.maxTouchPoints > 0) || 
                                 (navigator.msMaxTouchPoints > 0);
            
            if (this.state.isTouch) {
                document.body.classList.add('is-touch-device');
            }
        },

        /**
         * ==================================================================
         * UTILITIES
         * ==================================================================
         */
        
        /**
         * Query selector com fallback
         */
        querySelector(selectors) {
            if (!selectors) return null;
            
            const selectorList = selectors.split(',').map(s => s.trim());
            
            for (const selector of selectorList) {
                try {
                    const element = document.querySelector(selector);
                    if (element) return element;
                } catch (e) {
                    // Selector inválido, continuar
                }
            }
            
            return null;
        },

        /**
         * Trigger evento customizado
         */
        triggerEvent(eventName, detail = {}) {
            const event = new CustomEvent(eventName, { 
                bubbles: true,
                cancelable: true,
                detail 
            });
            document.dispatchEvent(event);
            
            this.log(`Event: ${eventName}`);
        },

        /**
         * Logger condicional
         */
        log(message, level = 'log') {
            if (!this.config.debug) return;
            
            const prefix = '[Nosfir Nav]';
            
            switch (level) {
                case 'error':
                    console.error(prefix, message);
                    break;
                case 'warn':
                    console.warn(prefix, message);
                    break;
                default:
                    console.log(prefix, message);
            }
        },

        /**
         * Debounce function
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
         * Throttle function
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
     * ==================================================================
     * INICIALIZAÇÃO
     * ==================================================================
     */
    
    // Inicializar
    NosfirNavigation.init();
    
    // Expor globalmente
    window.NosfirNavigation = NosfirNavigation;

})();