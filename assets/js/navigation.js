/**
 * Nosfir Navigation JavaScript
 * 
 * @package     Nosfir
 * @version     1.0.0
 * @author      David Creator
 * 
 * Gerencia navegação responsiva, menus dropdown, acessibilidade,
 * mega menu, busca e outras funcionalidades de navegação
 * Baseado no script de referência com funcionalidades expandidas
 */

/* global nosfirScreenReaderText, storefrontScreenReaderText */

(function () {
    'use strict';

    /**
     * ==================================================================
     * NOSFIR NAVIGATION MANAGER
     * ==================================================================
     */
    
    const NosfirNavigation = {
        
        // Configurações
        config: {
            mobileBreakpoint: 768,
            tabletBreakpoint: 1024,
            stickyEnabled: true,
            megaMenuEnabled: true,
            searchEnabled: true,
            animationSpeed: 300,
            submenuDelay: 200,
            touchThreshold: 5,
            keyboardNavEnabled: true,
            smoothScrollEnabled: true,
            mobileMenuStyle: 'slide', // slide, overlay, dropdown
            debug: false
        },

        // Elementos DOM
        elements: {
            container: null,
            button: null,
            menu: null,
            handheld: null,
            header: null,
            searchForm: null,
            overlay: null,
            submenus: [],
            megaMenus: []
        },

        // Estado
        state: {
            isOpen: false,
            isMobile: false,
            isTablet: false,
            isSticky: false,
            scrollPosition: 0,
            lastScrollPosition: 0,
            touchStartX: 0,
            touchStartY: 0,
            focusedElement: null,
            openSubmenus: [],
            searchVisible: false
        },

        // Screen reader text - Compatível com referência
        screenReaderText: {
            expand: 'Expand child menu',
            collapse: 'Collapse child menu',
            menu: 'Menu',
            close: 'Close menu',
            search: 'Search'
        },

        /**
         * ==================================================================
         * INICIALIZAÇÃO - Baseada na referência
         * ==================================================================
         */
        
        init() {
            const self = this;
            
            // DOMContentLoaded - Como na referência
            document.addEventListener('DOMContentLoaded', function () {
                self.log('Navigation initializing...');
                
                // Merge screen reader text - Compatibilidade com referência
                if (typeof storefrontScreenReaderText !== 'undefined') {
                    Object.assign(self.screenReaderText, storefrontScreenReaderText);
                }
                if (typeof nosfirScreenReaderText !== 'undefined') {
                    Object.assign(self.screenReaderText, nosfirScreenReaderText);
                }
                
                // Setup principal - Baseado na referência
                self.setupNavigation();
                
                // Se não houver navegação, retornar
                if (!self.elements.container) {
                    self.log('Navigation container not found');
                    return;
                }
                
                // Setup adicional
                self.setupMobileMenu();
                self.setupDropdowns();
                self.setupHandheldNavigation();    // Baseado na referência
                self.setupFocusHandlers();         // Baseado na referência
                self.setupTouchHandlers();          // Baseado na referência
                self.setupMegaMenu();
                self.setupSearch();
                self.setupSticky();
                self.setupSmoothScroll();
                self.setupKeyboardNav();
                self.setupBreadcrumbs();
                self.setupAccessibility();
                
                // Bind eventos
                self.bindEvents();
                
                // Check viewport
                self.checkViewport();
                
                // Initialize animations
                self.initAnimations();
                
                // Trigger ready event
                self.triggerEvent('navigation:ready');
                
                self.log('Navigation initialized successfully');
            });
        },

        /**
         * Logger condicional
         */
        log(message, type = 'log') {
            if (this.config.debug) {
                console[type]('[Nosfir Navigation]:', message);
            }
        },

        /**
         * ==================================================================
         * SETUP NAVIGATION - Baseado na referência
         * ==================================================================
         */
        
        setupNavigation() {
            // Get container - Como na referência
            const container = document.getElementById('site-navigation') || 
                            document.querySelector('.site-navigation');
            
            if (!container) {
                return;
            }
            
            this.elements.container = container;
            
            // Get button - Como na referência
            const button = container.querySelector('button');
            
            if (!button) {
                this.log('Navigation button not found');
                return;
            }
            
            this.elements.button = button;
            
            // Get menu - Como na referência
            const menu = container.querySelector('ul');
            
            // Hide button if no menu - Como na referência
            if (!menu) {
                button.style.display = 'none';
                return;
            }
            
            this.elements.menu = menu;
            
            // Set initial attributes - Como na referência
            button.setAttribute('aria-expanded', 'false');
            menu.setAttribute('aria-expanded', 'false');
            menu.classList.add('nav-menu');
            
            // Cache additional elements
            this.elements.header = document.querySelector('.site-header');
            this.elements.overlay = document.querySelector('.menu-overlay');
            this.elements.searchForm = document.querySelector('.navigation-search');
            
            // Setup toggle button
            this.setupToggleButton();
        },

        /**
         * Setup toggle button - Baseado na referência
         */
        setupToggleButton() {
            const self = this;
            
            // Click handler - Como na referência
            this.elements.button.addEventListener('click', function () {
                self.elements.container.classList.toggle('toggled');
                
                const expanded = self.elements.container.classList.contains('toggled') 
                    ? 'true' 
                    : 'false';
                
                self.elements.button.setAttribute('aria-expanded', expanded);
                self.elements.menu.setAttribute('aria-expanded', expanded);
                
                // Funcionalidade adicional
                self.state.isOpen = expanded === 'true';
                
                if (self.state.isOpen) {
                    self.openMenu();
                } else {
                    self.closeMenu();
                }
            });
        },

        /**
         * ==================================================================
         * MOBILE MENU
         * ==================================================================
         */
        
        setupMobileMenu() {
            // Create mobile menu structure if needed
            if (!this.elements.container) return;
            
            // Add mobile menu classes
            this.elements.container.classList.add('navigation-mobile-ready');
            
            // Create overlay if doesn't exist
            if (!this.elements.overlay) {
                this.createOverlay();
            }
            
            // Setup hamburger animation
            this.setupHamburgerAnimation();
            
            // Setup mobile menu style
            this.setupMobileMenuStyle();
        },

        /**
         * Create overlay
         */
        createOverlay() {
            const overlay = document.createElement('div');
            overlay.className = 'menu-overlay';
            document.body.appendChild(overlay);
            this.elements.overlay = overlay;
            
            // Click to close
            overlay.addEventListener('click', () => {
                this.closeMenu();
            });
        },

        /**
         * Setup hamburger animation
         */
        setupHamburgerAnimation() {
            if (!this.elements.button) return;
            
            // Add hamburger lines if not exist
            if (!this.elements.button.querySelector('.hamburger')) {
                const hamburger = document.createElement('span');
                hamburger.className = 'hamburger';
                hamburger.innerHTML = `
                    <span class="line"></span>
                    <span class="line"></span>
                    <span class="line"></span>
                `;
                this.elements.button.appendChild(hamburger);
            }
        },

        /**
         * Setup mobile menu style
         */
        setupMobileMenuStyle() {
            if (!this.elements.menu) return;
            
            // Add style class
            this.elements.menu.classList.add(`menu-${this.config.mobileMenuStyle}`);
        },

        /**
         * Open menu
         */
        openMenu() {
            this.state.isOpen = true;
            document.body.classList.add('menu-open');
            this.elements.container?.classList.add('is-open');
            this.elements.overlay?.classList.add('is-visible');
            
            // Animate menu
            this.animateMenuOpen();
            
            // Trap focus
            this.trapFocus();
            
            // Trigger event
            this.triggerEvent('navigation:open');
        },

        /**
         * Close menu
         */
        closeMenu() {
            this.state.isOpen = false;
            document.body.classList.remove('menu-open');
            this.elements.container?.classList.remove('is-open');
            this.elements.overlay?.classList.remove('is-visible');
            
            // Animate menu
            this.animateMenuClose();
            
            // Release focus
            this.releaseFocus();
            
            // Close all submenus
            this.closeAllSubmenus();
            
            // Trigger event
            this.triggerEvent('navigation:close');
        },

        /**
         * ==================================================================
         * DROPDOWNS
         * ==================================================================
         */
        
        setupDropdowns() {
            const dropdowns = document.querySelectorAll('.menu-item-has-children, .page_item_has_children');
            
            dropdowns.forEach(dropdown => {
                this.setupDropdown(dropdown);
            });
        },

        /**
         * Setup individual dropdown
         */
        setupDropdown(dropdown) {
            const link = dropdown.querySelector('a');
            const submenu = dropdown.querySelector('ul');
            
            if (!submenu) return;
            
            // Store submenu
            this.elements.submenus.push(submenu);
            
            // Desktop hover
            if (!this.state.isMobile) {
                let hoverTimeout;
                
                dropdown.addEventListener('mouseenter', () => {
                    clearTimeout(hoverTimeout);
                    this.openSubmenu(submenu, dropdown);
                });
                
                dropdown.addEventListener('mouseleave', () => {
                    hoverTimeout = setTimeout(() => {
                        this.closeSubmenu(submenu, dropdown);
                    }, this.config.submenuDelay);
                });
            }
            
            // Click handler for parent link
            link?.addEventListener('click', (e) => {
                if (this.state.isMobile && dropdown.querySelector('ul')) {
                    e.preventDefault();
                    this.toggleSubmenu(submenu, dropdown);
                }
            });
        },

        /**
         * Open submenu
         */
        openSubmenu(submenu, parent) {
            submenu.classList.add('is-open');
            parent.classList.add('has-open-submenu');
            
            // Position submenu if needed
            this.positionSubmenu(submenu);
            
            // Add to open submenus
            if (!this.state.openSubmenus.includes(submenu)) {
                this.state.openSubmenus.push(submenu);
            }
        },

        /**
         * Close submenu
         */
        closeSubmenu(submenu, parent) {
            submenu.classList.remove('is-open');
            parent.classList.remove('has-open-submenu');
            
            // Remove from open submenus
            const index = this.state.openSubmenus.indexOf(submenu);
            if (index > -1) {
                this.state.openSubmenus.splice(index, 1);
            }
        },

        /**
         * Toggle submenu
         */
        toggleSubmenu(submenu, parent) {
            if (submenu.classList.contains('is-open')) {
                this.closeSubmenu(submenu, parent);
            } else {
                this.openSubmenu(submenu, parent);
            }
        },

        /**
         * Close all submenus
         */
        closeAllSubmenus() {
            this.state.openSubmenus.forEach(submenu => {
                submenu.classList.remove('is-open');
                submenu.parentElement?.classList.remove('has-open-submenu');
            });
            this.state.openSubmenus = [];
        },

        /**
         * Position submenu
         */
        positionSubmenu(submenu) {
            if (this.state.isMobile) return;
            
            const rect = submenu.getBoundingClientRect();
            const viewportWidth = window.innerWidth;
            
            // Check if submenu goes off screen
            if (rect.right > viewportWidth) {
                submenu.classList.add('submenu-left');
            } else {
                submenu.classList.remove('submenu-left');
            }
        },

        /**
         * ==================================================================
         * HANDHELD NAVIGATION - Baseado na referência
         * ==================================================================
         */
        
        setupHandheldNavigation() {
            // Get handheld navigation - Como na referência
            const handheld = document.getElementsByClassName('handheld-navigation');
            
            if (handheld.length === 0) {
                return;
            }
            
            this.elements.handheld = handheld[0];
            
            // Add dropdown toggles - Como na referência
            [].forEach.call(
                handheld[0].querySelectorAll(
                    '.menu-item-has-children > a, .page_item_has_children > a'
                ),
                (anchor) => {
                    this.addDropdownToggle(anchor);
                }
            );
        },

        /**
         * Add dropdown toggle - Baseado na referência
         */
        addDropdownToggle(anchor) {
            // Create button - Como na referência
            const btn = document.createElement('button');
            btn.setAttribute('aria-expanded', 'false');
            btn.classList.add('dropdown-toggle');
            
            // Create screen reader text - Como na referência
            const btnSpan = document.createElement('span');
            btnSpan.classList.add('screen-reader-text');
            btnSpan.appendChild(
                document.createTextNode(this.screenReaderText.expand)
            );
            
            btn.appendChild(btnSpan);
            
            // Insert button - Como na referência
            anchor.parentNode.insertBefore(btn, anchor.nextSibling);
            
            // Set initial state - Como na referência
            if (anchor.parentNode.classList.contains('current-menu-ancestor')) {
                btn.setAttribute('aria-expanded', 'true');
                btn.classList.add('toggled-on');
                btn.nextElementSibling?.classList.add('toggled-on');
            }
            
            // Add event listener - Como na referência
            btn.addEventListener('click', () => {
                this.toggleDropdown(btn, btnSpan);
            });
        },

        /**
         * Toggle dropdown - Baseado na referência
         */
        toggleDropdown(btn, btnSpan) {
            btn.classList.toggle('toggled-on');
            
            // Update screen reader text - Como na referência
            while (btnSpan.firstChild) {
                btnSpan.removeChild(btnSpan.firstChild);
            }
            
            const expanded = btn.classList.contains('toggled-on');
            
            btn.setAttribute('aria-expanded', expanded);
            btnSpan.appendChild(
                document.createTextNode(
                    expanded 
                        ? this.screenReaderText.collapse
                        : this.screenReaderText.expand
                )
            );
            
            btn.nextElementSibling?.classList.toggle('toggled-on');
        },

        /**
         * ==================================================================
         * FOCUS HANDLERS - Baseado na referência
         * ==================================================================
         */
        
        setupFocusHandlers() {
            // Add focus class to parents - Como na referência
            [].forEach.call(
                document.querySelectorAll(
                    '.site-header .menu-item > a, .site-header .page_item > a, .site-header-cart a'
                ),
                (anchor) => {
                    anchor.addEventListener('focus', () => {
                        this.handleFocus(anchor);
                    });
                    
                    // Blur handler
                    anchor.addEventListener('blur', () => {
                        this.handleBlur(anchor);
                    });
                }
            );
            
            // Close dropdowns on outside click - Como na referência
            this.setupOutsideClick();
        },

        /**
         * Handle focus - Baseado na referência
         */
        handleFocus(anchor) {
            // Remove focus from others - Como na referência
            const elems = document.querySelectorAll('.focus');
            
            [].forEach.call(elems, (el) => {
                if (!el.contains(anchor)) {
                    el.classList.remove('focus');
                    
                    // Remove blocked class - Como na referência
                    if (el.firstChild && el.firstChild.classList) {
                        el.firstChild.classList.remove('blocked');
                    }
                }
            });
            
            // Add focus class - Como na referência
            const li = anchor.parentNode;
            li.classList.add('focus');
            
            // Store focused element
            this.state.focusedElement = anchor;
        },

        /**
         * Handle blur
         */
        handleBlur(anchor) {
            // Delay to check if focus moved to submenu
            setTimeout(() => {
                const li = anchor.parentNode;
                if (!li.contains(document.activeElement)) {
                    li.classList.remove('focus');
                }
            }, 100);
        },

        /**
         * Setup outside click - Baseado na referência
         */
        setupOutsideClick() {
            // Close dropdowns on outside click - Como na referência
            [].forEach.call(
                document.querySelectorAll('body #page > :not(.site-header)'),
                (element) => {
                    element.addEventListener('click', () => {
                        [].forEach.call(
                            document.querySelectorAll('.focus, .blocked'),
                            (el) => {
                                el.classList.remove('focus');
                                el.classList.remove('blocked');
                            }
                        );
                    });
                }
            );
        },

        /**
         * ==================================================================
         * TOUCH HANDLERS - Baseado na referência
         * ==================================================================
         */
        
        setupTouchHandlers() {
            // Check for touch device - Como na referência
            if (!this.isTouchDevice()) {
                return;
            }
            
            // Add touch class to submenus - Como na referência
            if (window.innerWidth > 767) {
                [].forEach.call(
                    document.querySelectorAll(
                        '.site-header ul ul, .site-header-cart .widget_shopping_cart'
                    ),
                    (element) => {
                        element.classList.add('sub-menu--is-touch-device');
                    }
                );
            }
            
            // Setup touch navigation - Como na referência
            this.setupTouchNavigation();
        },

        /**
         * Setup touch navigation - Baseado na referência
         */
        setupTouchNavigation() {
            let acceptClick = false;
            
            // Handle touch on menu items - Como na referência
            [].forEach.call(
                document.querySelectorAll(
                    '.site-header .menu-item > a, .site-header .page_item > a, .site-header-cart a'
                ),
                (anchor) => {
                    // Click handler - Como na referência
                    anchor.addEventListener('click', (event) => {
                        if (anchor.classList.contains('blocked') && acceptClick === false) {
                            event.preventDefault();
                        }
                        acceptClick = false;
                    });
                    
                    // Pointer up handler - Como na referência
                    anchor.addEventListener('pointerup', (event) => {
                        if (anchor.classList.contains('blocked') || event.pointerType === 'mouse') {
                            acceptClick = true;
                        } else if (
                            (anchor.className === 'cart-contents' &&
                                anchor.parentNode.nextElementSibling &&
                                anchor.parentNode.nextElementSibling.textContent.trim() !== '') ||
                            anchor.nextElementSibling
                        ) {
                            anchor.classList.add('blocked');
                        } else {
                            acceptClick = true;
                        }
                    });
                }
            );
            
            // Additional touch gestures
            this.setupTouchGestures();
        },

        /**
         * Setup touch gestures
         */
        setupTouchGestures() {
            let touchStartX = 0;
            let touchStartY = 0;
            let touchEndX = 0;
            let touchEndY = 0;
            
            // Touch start
            document.addEventListener('touchstart', (e) => {
                touchStartX = e.touches[0].clientX;
                touchStartY = e.touches[0].clientY;
            }, { passive: true });
            
            // Touch end
            document.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].clientX;
                touchEndY = e.changedTouches[0].clientY;
                
                this.handleSwipe(touchStartX, touchStartY, touchEndX, touchEndY);
            });
        },

        /**
         * Handle swipe
         */
        handleSwipe(startX, startY, endX, endY) {
            const diffX = startX - endX;
            const diffY = startY - endY;
            const threshold = 50;
            
            // Check if horizontal swipe
            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > threshold) {
                if (diffX > 0) {
                    // Swipe left - close menu
                    if (this.state.isOpen) {
                        this.closeMenu();
                    }
                } else {
                    // Swipe right - open menu
                    if (!this.state.isOpen && this.state.isMobile) {
                        this.openMenu();
                    }
                }
            }
        },

        /**
         * ==================================================================
         * MEGA MENU
         * ==================================================================
         */
        
        setupMegaMenu() {
            if (!this.config.megaMenuEnabled) return;
            
            const megaMenus = document.querySelectorAll('.mega-menu');
            
            megaMenus.forEach(megaMenu => {
                this.initMegaMenu(megaMenu);
            });
        },

        /**
         * Initialize mega menu
         */
        initMegaMenu(megaMenu) {
            const parent = megaMenu.parentElement;
            
            // Store mega menu
            this.elements.megaMenus.push(megaMenu);
            
            // Position mega menu
            this.positionMegaMenu(megaMenu);
            
            // Add hover effects
            if (!this.state.isMobile) {
                parent.addEventListener('mouseenter', () => {
                    this.openMegaMenu(megaMenu);
                });
                
                parent.addEventListener('mouseleave', () => {
                    this.closeMegaMenu(megaMenu);
                });
            }
        },

        /**
         * Position mega menu
         */
        positionMegaMenu(megaMenu) {
            if (this.state.isMobile) return;
            
            const header = this.elements.header;
            if (header) {
                const headerRect = header.getBoundingClientRect();
                megaMenu.style.left = -headerRect.left + 'px';
                megaMenu.style.width = window.innerWidth + 'px';
            }
        },

        /**
         * Open mega menu
         */
        openMegaMenu(megaMenu) {
            megaMenu.classList.add('is-open');
            
            // Animate columns
            const columns = megaMenu.querySelectorAll('.mega-menu-column');
            columns.forEach((column, index) => {
                setTimeout(() => {
                    column.classList.add('animated');
                }, index * 50);
            });
        },

        /**
         * Close mega menu
         */
        closeMegaMenu(megaMenu) {
            megaMenu.classList.remove('is-open');
            
            // Reset animation
            const columns = megaMenu.querySelectorAll('.mega-menu-column');
            columns.forEach(column => {
                column.classList.remove('animated');
            });
        },

        /**
         * ==================================================================
         * SEARCH
         * ==================================================================
         */
        
        setupSearch() {
            if (!this.config.searchEnabled) return;
            
            const searchToggle = document.querySelector('.nav-search-toggle');
            const searchForm = document.querySelector('.navigation-search');
            
            if (!searchToggle || !searchForm) return;
            
            this.elements.searchForm = searchForm;
            
            // Toggle search
            searchToggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleSearch();
            });
            
            // Setup instant search
            this.setupInstantSearch();
        },

        /**
         * Toggle search
         */
        toggleSearch() {
            this.state.searchVisible = !this.state.searchVisible;
            
            if (this.state.searchVisible) {
                this.openSearch();
            } else {
                this.closeSearch();
            }
        },

        /**
         * Open search
         */
        openSearch() {
            this.elements.searchForm?.classList.add('is-visible');
            
            // Focus input
            const input = this.elements.searchForm?.querySelector('input[type="search"]');
            input?.focus();
            
            // Add overlay
            this.showSearchOverlay();
            
            this.triggerEvent('navigation:search:open');
        },

        /**
         * Close search
         */
        closeSearch() {
            this.elements.searchForm?.classList.remove('is-visible');
            
            // Hide overlay
            this.hideSearchOverlay();
            
            this.triggerEvent('navigation:search:close');
        },

        /**
         * Show search overlay
         */
        showSearchOverlay() {
            if (!document.querySelector('.search-overlay')) {
                const overlay = document.createElement('div');
                overlay.className = 'search-overlay';
                document.body.appendChild(overlay);
                
                overlay.addEventListener('click', () => {
                    this.closeSearch();
                });
            }
            
            document.querySelector('.search-overlay')?.classList.add('visible');
        },

        /**
         * Hide search overlay
         */
        hideSearchOverlay() {
            document.querySelector('.search-overlay')?.classList.remove('visible');
        },

        /**
         * Setup instant search
         */
        setupInstantSearch() {
            const searchInput = this.elements.searchForm?.querySelector('input[type="search"]');
            
            if (!searchInput) return;
            
            let searchTimeout;
            
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                
                searchTimeout = setTimeout(() => {
                    this.performInstantSearch(e.target.value);
                }, 300);
            });
        },

        /**
         * Perform instant search
         */
        performInstantSearch(query) {
            if (query.length < 3) {
                this.hideSearchResults();
                return;
            }
            
            // AJAX search
            fetch(`/wp-json/wp/v2/search?search=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(results => {
                    this.displaySearchResults(results);
                })
                .catch(error => {
                    this.log('Search error:', error);
                });
        },

        /**
         * Display search results
         */
        displaySearchResults(results) {
            let resultsContainer = document.querySelector('.instant-search-results');
            
            if (!resultsContainer) {
                resultsContainer = document.createElement('div');
                resultsContainer.className = 'instant-search-results';
                this.elements.searchForm?.appendChild(resultsContainer);
            }
            
            if (results.length === 0) {
                resultsContainer.innerHTML = '<p class="no-results">No results found</p>';
            } else {
                const html = results.map(result => `
                    <a href="${result.url}" class="search-result">
                        <h4>${result.title}</h4>
                        <p>${result.excerpt}</p>
                    </a>
                `).join('');
                
                resultsContainer.innerHTML = html;
            }
            
            resultsContainer.classList.add('visible');
        },

        /**
         * Hide search results
         */
        hideSearchResults() {
            const resultsContainer = document.querySelector('.instant-search-results');
            resultsContainer?.classList.remove('visible');
        },

        /**
         * ==================================================================
         * STICKY NAVIGATION
         * ==================================================================
         */
        
        setupSticky() {
            if (!this.config.stickyEnabled || !this.elements.header) return;
            
            // Get header height
            const headerHeight = this.elements.header.offsetHeight;
            
            // Add placeholder
            const placeholder = document.createElement('div');
            placeholder.className = 'header-placeholder';
            placeholder.style.display = 'none';
            placeholder.style.height = headerHeight + 'px';
            this.elements.header.parentNode?.insertBefore(placeholder, this.elements.header.nextSibling);
            
            // Store reference
            this.elements.headerPlaceholder = placeholder;
        },

        /**
         * Handle sticky on scroll
         */
        handleSticky() {
            if (!this.config.stickyEnabled || !this.elements.header) return;
            
            const scrollPosition = window.pageYOffset;
            const triggerPoint = 100;
            
            if (scrollPosition > triggerPoint) {
                if (!this.state.isSticky) {
                    this.state.isSticky = true;
                    this.elements.header.classList.add('is-sticky');
                    this.elements.headerPlaceholder.style.display = 'block';
                    
                    // Animate in
                    this.animateStickyIn();
                }
            } else {
                if (this.state.isSticky) {
                    this.state.isSticky = false;
                    this.elements.header.classList.remove('is-sticky');
                    this.elements.headerPlaceholder.style.display = 'none';
                    
                    // Animate out
                    this.animateStickyOut();
                }
            }
            
            // Hide on scroll down, show on scroll up
            this.handleStickyVisibility(scrollPosition);
        },

        /**
         * Handle sticky visibility
         */
        handleStickyVisibility(scrollPosition) {
            if (!this.state.isSticky) return;
            
            const scrollDiff = scrollPosition - this.state.lastScrollPosition;
            
            if (scrollDiff > 10 && scrollPosition > 300) {
                // Scrolling down - hide
                this.elements.header?.classList.add('sticky-hidden');
            } else if (scrollDiff < -10) {
                // Scrolling up - show
                this.elements.header?.classList.remove('sticky-hidden');
            }
            
            this.state.lastScrollPosition = scrollPosition;
        },

        /**
         * ==================================================================
         * SMOOTH SCROLL
         * ==================================================================
         */
        
        setupSmoothScroll() {
            if (!this.config.smoothScrollEnabled) return;
            
            // Find all anchor links
            const links = document.querySelectorAll('a[href^="#"]:not([href="#"])');
            
            links.forEach(link => {
                link.addEventListener('click', (e) => {
                    const targetId = link.getAttribute('href');
                    const target = document.querySelector(targetId);
                    
                    if (target) {
                        e.preventDefault();
                        this.smoothScrollTo(target);
                        
                        // Close mobile menu if open
                        if (this.state.isOpen) {
                            this.closeMenu();
                        }
                    }
                });
            });
        },

        /**
         * Smooth scroll to element
         */
        smoothScrollTo(target) {
            const headerHeight = this.elements.header?.offsetHeight || 0;
            const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
            
            // Update URL hash
            history.pushState(null, null, '#' + target.id);
        },

        /**
         * ==================================================================
         * KEYBOARD NAVIGATION
         * ==================================================================
         */
        
        setupKeyboardNav() {
            if (!this.config.keyboardNavEnabled) return;
            
            document.addEventListener('keydown', (e) => {
                this.handleKeyboardNav(e);
            });
        },

        /**
         * Handle keyboard navigation
         */
        handleKeyboardNav(e) {
            // ESC to close menu
            if (e.key === 'Escape') {
                if (this.state.isOpen) {
                    this.closeMenu();
                }
                if (this.state.searchVisible) {
                    this.closeSearch();
                }
                this.closeAllSubmenus();
            }
            
            // Tab navigation
            if (e.key === 'Tab') {
                this.handleTabNavigation(e);
            }
            
            // Arrow key navigation
            if (this.state.focusedElement && ['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
                this.handleArrowNavigation(e);
            }
        },

        /**
         * Handle tab navigation
         */
        handleTabNavigation(e) {
            if (!this.state.isOpen) return;
            
            const focusableElements = this.elements.menu?.querySelectorAll(
                'a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])'
            );
            
            if (!focusableElements || focusableElements.length === 0) return;
            
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];
            
            // Trap focus in menu
            if (e.shiftKey && document.activeElement === firstElement) {
                e.preventDefault();
                lastElement.focus();
            } else if (!e.shiftKey && document.activeElement === lastElement) {
                e.preventDefault();
                firstElement.focus();
            }
        },

        /**
         * Handle arrow navigation
         */
        handleArrowNavigation(e) {
            e.preventDefault();
            
            const currentItem = this.state.focusedElement?.parentElement;
            if (!currentItem) return;
            
            let nextItem;
            
            switch (e.key) {
                case 'ArrowUp':
                    nextItem = currentItem.previousElementSibling;
                    break;
                case 'ArrowDown':
                    nextItem = currentItem.nextElementSibling;
                    break;
                case 'ArrowRight':
                    // Open submenu
                    const submenu = currentItem.querySelector('ul');
                    if (submenu) {
                        this.openSubmenu(submenu, currentItem);
                        submenu.querySelector('a')?.focus();
                    }
                    break;
                case 'ArrowLeft':
                    // Go to parent menu
                    const parentMenu = currentItem.parentElement.parentElement;
                    if (parentMenu && parentMenu.classList.contains('menu-item')) {
                        parentMenu.querySelector('a')?.focus();
                        this.closeSubmenu(currentItem.parentElement, parentMenu);
                    }
                    break;
            }
            
            if (nextItem) {
                nextItem.querySelector('a')?.focus();
            }
        },

        /**
         * ==================================================================
         * BREADCRUMBS
         * ==================================================================
         */
        
        setupBreadcrumbs() {
            const breadcrumbs = document.querySelector('.breadcrumbs');
            
            if (!breadcrumbs) return;
            
            // Make breadcrumbs sticky on mobile
            if (this.state.isMobile) {
                breadcrumbs.classList.add('sticky-breadcrumbs');
            }
            
            // Truncate long breadcrumbs
            this.truncateBreadcrumbs(breadcrumbs);
        },

        /**
         * Truncate breadcrumbs
         */
        truncateBreadcrumbs(breadcrumbs) {
            const items = breadcrumbs.querySelectorAll('.breadcrumb-item');
            
            if (items.length > 3) {
                // Show first, ellipsis, and last two
                items.forEach((item, index) => {
                    if (index > 0 && index < items.length - 2) {
                        item.style.display = 'none';
                    }
                });
                
                // Add ellipsis
                const ellipsis = document.createElement('span');
                ellipsis.className = 'breadcrumb-ellipsis';
                ellipsis.textContent = '...';
                breadcrumbs.insertBefore(ellipsis, items[items.length - 2]);
            }
        },

        /**
         * ==================================================================
         * ACCESSIBILITY
         * ==================================================================
         */
        
        setupAccessibility() {
            // Skip to content link
            this.addSkipLink();
            
            // ARIA live regions
            this.setupAriaLive();
            
            // Focus management
            this.setupFocusManagement();
        },

        /**
         * Add skip link
         */
        addSkipLink() {
            if (document.querySelector('.skip-link')) return;
            
            const skipLink = document.createElement('a');
            skipLink.href = '#main';
            skipLink.className = 'skip-link screen-reader-text';
            skipLink.textContent = 'Skip to content';
            
            document.body.insertBefore(skipLink, document.body.firstChild);
        },

        /**
         * Setup ARIA live regions
         */
        setupAriaLive() {
            const liveRegion = document.createElement('div');
            liveRegion.className = 'screen-reader-text';
            liveRegion.setAttribute('aria-live', 'polite');
            liveRegion.setAttribute('aria-atomic', 'true');
            document.body.appendChild(liveRegion);
            
            this.elements.liveRegion = liveRegion;
        },

        /**
         * Setup focus management
         */
        setupFocusManagement() {
            // Store last focused element before opening menu
            this.elements.button?.addEventListener('click', () => {
                if (!this.state.isOpen) {
                    this.lastFocusedElement = document.activeElement;
                }
            });
        },

        /**
         * Trap focus
         */
        trapFocus() {
            // Focus first menu item when menu opens
            setTimeout(() => {
                this.elements.menu?.querySelector('a')?.focus();
            }, 100);
        },

        /**
         * Release focus
         */
        releaseFocus() {
            // Return focus to last focused element
            if (this.lastFocusedElement) {
                this.lastFocusedElement.focus();
                this.lastFocusedElement = null;
            }
        },

        /**
         * ==================================================================
         * ANIMATIONS
         * ==================================================================
         */
        
        initAnimations() {
            // Add animation classes
            this.elements.menu?.classList.add('animated-menu');
        },

        /**
         * Animate menu open
         */
        animateMenuOpen() {
            if (this.config.mobileMenuStyle === 'slide') {
                this.elements.menu?.style.setProperty('transform', 'translateX(0)');
            } else if (this.config.mobileMenuStyle === 'overlay') {
                this.elements.menu?.style.setProperty('opacity', '1');
            }
        },

        /**
         * Animate menu close
         */
        animateMenuClose() {
            if (this.config.mobileMenuStyle === 'slide') {
                this.elements.menu?.style.setProperty('transform', 'translateX(-100%)');
            } else if (this.config.mobileMenuStyle === 'overlay') {
                this.elements.menu?.style.setProperty('opacity', '0');
            }
        },

        /**
         * Animate sticky in
         */
        animateStickyIn() {
            this.elements.header?.style.setProperty('animation', 'slideDown 0.3s ease');
        },

        /**
         * Animate sticky out
         */
        animateStickyOut() {
            this.elements.header?.style.setProperty('animation', 'slideUp 0.3s ease');
        },

        /**
         * ==================================================================
         * UTILITIES
         * ==================================================================
         */
        
        /**
         * Check if touch device - Baseado na referência
         */
        isTouchDevice() {
            return ('ontouchstart' in window || window.navigator.maxTouchPoints > 0);
        },

        /**
         * Check viewport
         */
        checkViewport() {
            this.state.isMobile = window.innerWidth < this.config.mobileBreakpoint;
            this.state.isTablet = window.innerWidth < this.config.tabletBreakpoint;
            
            // Update body classes
            document.body.classList.toggle('nav-mobile', this.state.isMobile);
            document.body.classList.toggle('nav-tablet', this.state.isTablet);
        },

        /**
         * Announce to screen readers
         */
        announce(message) {
            if (this.elements.liveRegion) {
                this.elements.liveRegion.textContent = message;
            }
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
            // Resize event
            let resizeTimeout;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    this.checkViewport();
                    this.positionSubmenus();
                }, 250);
            });
            
            // Scroll event
            let scrollTimeout;
            window.addEventListener('scroll', () => {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    this.handleSticky();
                }, 10);
            }, { passive: true });
            
            // Orientation change
            window.addEventListener('orientationchange', () => {
                setTimeout(() => {
                    this.checkViewport();
                }, 100);
            });
        },

        /**
         * Position all submenus
         */
        positionSubmenus() {
            this.elements.submenus.forEach(submenu => {
                this.positionSubmenu(submenu);
            });
            
            this.elements.megaMenus.forEach(megaMenu => {
                this.positionMegaMenu(megaMenu);
            });
        }
    };

    /**
     * ==================================================================
     * INICIALIZAÇÃO
     * ==================================================================
     */
    
    // Inicializar
    NosfirNavigation.init();
    
    // Expor globalmente para debugging e extensibilidade
    window.NosfirNavigation = NosfirNavigation;

})();