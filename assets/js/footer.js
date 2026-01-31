/**
 * Nosfir Footer JavaScript
 * 
 * @package     Nosfir
 * @version     1.0.0
 * @author      David Creator
 * 
 * Gerencia funcionalidades do footer incluindo handheld footer bar, 
 * busca móvel, newsletter, back to top, e outras interações
 * Baseado no script de referência com funcionalidades expandidas
 */

(function () {
    'use strict';

    /**
     * ==================================================================
     * NOSFIR FOOTER MANAGER
     * ==================================================================
     */
    
    const NosfirFooter = {
        
        // Configurações
        config: {
            mobileBreakpoint: 768,
            tabletBreakpoint: 1024,
            scrollThreshold: 300,
            animationSpeed: 300,
            debounceDelay: 250,
            stickyFooterBar: true,
            autoHideOnScroll: true,
            newsletterValidation: true,
            backToTopEnabled: true,
            socialShareEnabled: true,
            cookieConsentEnabled: true,
            debug: false
        },

        // Elementos DOM
        elements: {
            footerBar: null,
            searchToggle: null,
            searchForm: null,
            backToTop: null,
            newsletter: null,
            socialLinks: null,
            cookieBanner: null,
            forms: null,
            inputs: null
        },

        // Estado
        state: {
            isSearchActive: false,
            isFooterVisible: true,
            isInputFocused: false,
            scrollPosition: 0,
            lastScrollPosition: 0,
            isMobile: false,
            isTablet: false,
            newsletterSubmitted: false,
            cookieAccepted: false
        },

        /**
         * ==================================================================
         * INICIALIZAÇÃO - Baseada na referência
         * ==================================================================
         */
        
        init() {
            const self = this;
            
            // Wait for DOM to be ready - Como na referência
            document.addEventListener('DOMContentLoaded', function () {
                self.log('Footer initializing...');
                
                // Verificar se existe handheld footer bar - Como na referência
                if (document.getElementsByClassName('nosfir-handheld-footer-bar').length === 0 && 
                    document.getElementsByClassName('storefront-handheld-footer-bar').length === 0) {
                    self.log('No handheld footer bar found - initializing standard footer');
                    self.initStandardFooter();
                    return;
                }
                
                // Cachear elementos
                self.cacheElements();
                
                // Setup funcionalidades
                self.setupHandheldFooter();      // Baseado na referência
                self.setupSearchToggle();         // Baseado na referência
                self.setupInputFocusHandler();    // Baseado na referência
                self.setupBackToTop();
                self.setupNewsletter();
                self.setupSocialShare();
                self.setupCookieConsent();
                self.setupAccordion();
                self.setupStickyFooter();
                self.setupAnimations();
                self.setupLiveChat();
                self.setupAccessibility();
                
                // Bind eventos
                self.bindEvents();
                
                // Verificar estado inicial
                self.checkDeviceType();
                self.checkScrollPosition();
                
                // Trigger custom event
                self.triggerEvent('footer:ready');
                
                self.log('Footer initialized successfully');
            });
        },

        /**
         * Logger condicional
         */
        log(message, type = 'log') {
            if (this.config.debug) {
                console[type]('[Nosfir Footer]:', message);
            }
        },

        /**
         * ==================================================================
         * CACHEAR ELEMENTOS
         * ==================================================================
         */
        
        cacheElements() {
            // Elementos principais - Compatível com referência
            this.elements.footerBar = document.querySelector('.nosfir-handheld-footer-bar') || 
                                      document.querySelector('.storefront-handheld-footer-bar');
            
            // Formulários - Como na referência
            this.elements.forms = document.forms;
            
            // Elementos de busca
            this.elements.searchToggle = this.elements.footerBar?.querySelector('.search > a');
            this.elements.searchForm = this.elements.footerBar?.querySelector('.search-form');
            
            // Elementos adicionais
            this.elements.backToTop = document.querySelector('.back-to-top');
            this.elements.newsletter = document.querySelector('.footer-newsletter');
            this.elements.socialLinks = document.querySelectorAll('.footer-social-links a');
            this.elements.cookieBanner = document.querySelector('.cookie-consent-banner');
            this.elements.footerMain = document.querySelector('.site-footer');
            this.elements.footerWidgets = document.querySelector('.footer-widgets');
            this.elements.footerAccordion = document.querySelectorAll('.footer-widget-toggle');
            this.elements.liveChat = document.querySelector('.live-chat-widget');
            
            // Inputs para focus handler
            this.elements.inputs = document.querySelectorAll('input, textarea, select');
            
            this.log('Elements cached', this.elements);
        },

        /**
         * ==================================================================
         * HANDHELD FOOTER - Baseado na referência
         * ==================================================================
         */
        
        setupHandheldFooter() {
            if (!this.elements.footerBar) {
                return;
            }
            
            // Adicionar classes para styling
            this.elements.footerBar.classList.add('is-ready');
            
            // Setup dos ícones/botões do footer móvel
            this.setupFooterBarButtons();
            
            // Setup sticky behavior
            if (this.config.stickyFooterBar) {
                this.setupStickyBehavior();
            }
            
            // Setup auto-hide on scroll
            if (this.config.autoHideOnScroll) {
                this.setupAutoHide();
            }
        },

        /**
         * Setup footer bar buttons
         */
        setupFooterBarButtons() {
            const buttons = this.elements.footerBar?.querySelectorAll('.footer-bar-item');
            
            buttons?.forEach(button => {
                button.addEventListener('click', (e) => {
                    // Se não for link, prevenir default
                    if (!button.querySelector('a[href]') || button.classList.contains('search')) {
                        e.preventDefault();
                    }
                    
                    this.handleFooterBarAction(button);
                });
            });
        },

        /**
         * Handle footer bar action
         */
        handleFooterBarAction(button) {
            const action = button.dataset.action || button.className.split(' ')[0];
            
            switch(action) {
                case 'search':
                    this.toggleSearch();
                    break;
                case 'account':
                    this.openAccountPanel();
                    break;
                case 'cart':
                    this.openCartPanel();
                    break;
                case 'menu':
                    this.openMobileMenu();
                    break;
                case 'wishlist':
                    this.openWishlist();
                    break;
                default:
                    this.log(`Unknown action: ${action}`);
            }
            
            // Analytics
            this.trackEvent('footer_bar_action', action);
        },

        /**
         * ==================================================================
         * SEARCH TOGGLE - Baseado na referência
         * ==================================================================
         */
        
        setupSearchToggle() {
            // Implementação baseada na referência
            const searchAnchors = document.querySelectorAll(
                '.nosfir-handheld-footer-bar .search > a, .storefront-handheld-footer-bar .search > a'
            );
            
            // forEach como na referência
            [].forEach.call(searchAnchors, (anchor) => {
                anchor.addEventListener('click', (event) => {
                    // Toggle class como na referência
                    anchor.parentElement.classList.toggle('active');
                    event.preventDefault();
                    
                    // Funcionalidades adicionais
                    this.handleSearchToggle(anchor.parentElement);
                });
            });
        },

        /**
         * Handle search toggle
         */
        handleSearchToggle(searchElement) {
            const isActive = searchElement.classList.contains('active');
            this.state.isSearchActive = isActive;
            
            if (isActive) {
                // Focus no input de busca
                const searchInput = searchElement.querySelector('input[type="search"]');
                searchInput?.focus();
                
                // Adicionar overlay
                this.showSearchOverlay();
                
                // Animar entrada
                this.animateSearchIn(searchElement);
                
                // Trigger event
                this.triggerEvent('footer:search:open');
            } else {
                // Remover overlay
                this.hideSearchOverlay();
                
                // Animar saída
                this.animateSearchOut(searchElement);
                
                // Trigger event
                this.triggerEvent('footer:search:close');
            }
        },

        /**
         * Toggle search
         */
        toggleSearch() {
            const searchElement = this.elements.footerBar?.querySelector('.search');
            searchElement?.classList.toggle('active');
            this.handleSearchToggle(searchElement);
        },

        /**
         * Show search overlay
         */
        showSearchOverlay() {
            let overlay = document.querySelector('.search-overlay');
            
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.className = 'search-overlay';
                document.body.appendChild(overlay);
                
                overlay.addEventListener('click', () => {
                    this.toggleSearch();
                });
            }
            
            overlay.classList.add('active');
        },

        /**
         * Hide search overlay
         */
        hideSearchOverlay() {
            const overlay = document.querySelector('.search-overlay');
            overlay?.classList.remove('active');
        },

        /**
         * ==================================================================
         * INPUT FOCUS HANDLER - Baseado na referência
         * ==================================================================
         */
        
        setupInputFocusHandler() {
            // Implementação baseada na referência
            const footerBar = this.elements.footerBar;
            const forms = this.elements.forms;
            
            // Função isFocused como na referência
            const isFocused = (focused) => {
                return (event) => {
                    if (!!focused && event.target.tabIndex !== -1) {
                        document.body.classList.add('nf-input-focused');
                        // Compatibilidade com referência
                        document.body.classList.add('sf-input-focused');
                        this.state.isInputFocused = true;
                        this.handleInputFocus(true, event.target);
                    } else {
                        document.body.classList.remove('nf-input-focused');
                        document.body.classList.remove('sf-input-focused');
                        this.state.isInputFocused = false;
                        this.handleInputFocus(false, event.target);
                    }
                };
            };
            
            // Loop através dos forms como na referência
            if (footerBar && forms.length) {
                for (let i = 0; i < forms.length; i++) {
                    // Skip forms dentro do footer como na referência
                    if (footerBar.contains(forms[i])) {
                        continue;
                    }
                    
                    // Add listeners como na referência
                    forms[i].addEventListener('focus', isFocused(true), true);
                    forms[i].addEventListener('blur', isFocused(false), true);
                }
            }
            
            // Setup adicional para todos os inputs
            this.setupAdvancedInputHandlers();
        },

        /**
         * Handle input focus
         */
        handleInputFocus(focused, input) {
            if (focused) {
                // Esconder footer bar quando input está focado
                this.hideFooterBar();
                
                // Ajustar viewport se necessário
                this.adjustViewportForKeyboard(input);
                
                // Analytics
                this.trackEvent('input_focused', input.name || input.type);
            } else {
                // Mostrar footer bar quando input perde foco
                setTimeout(() => {
                    if (!this.state.isInputFocused) {
                        this.showFooterBar();
                    }
                }, 100);
            }
        },

        /**
         * Setup advanced input handlers
         */
        setupAdvancedInputHandlers() {
            // Detectar teclado virtual em mobile
            if (this.isMobileDevice()) {
                window.visualViewport?.addEventListener('resize', () => {
                    this.handleVirtualKeyboard();
                });
            }
            
            // Auto-resize para textareas
            const textareas = document.querySelectorAll('textarea[data-autoresize]');
            textareas.forEach(textarea => {
                textarea.addEventListener('input', () => {
                    this.autoResizeTextarea(textarea);
                });
            });
        },

        /**
         * ==================================================================
         * BACK TO TOP
         * ==================================================================
         */
        
        setupBackToTop() {
            if (!this.config.backToTopEnabled) return;
            
            // Criar botão se não existir
            if (!this.elements.backToTop) {
                this.createBackToTopButton();
            }
            
            // Click handler
            this.elements.backToTop?.addEventListener('click', (e) => {
                e.preventDefault();
                this.scrollToTop();
            });
        },

        /**
         * Create back to top button
         */
        createBackToTopButton() {
            const button = document.createElement('button');
            button.className = 'back-to-top';
            button.innerHTML = '<span class="screen-reader-text">Back to top</span>↑';
            button.setAttribute('aria-label', 'Back to top');
            
            document.body.appendChild(button);
            this.elements.backToTop = button;
        },

        /**
         * Scroll to top
         */
        scrollToTop() {
            const startPosition = window.pageYOffset;
            const startTime = performance.now();
            const duration = 500;
            
            const easeOutCubic = (t) => 1 - Math.pow(1 - t, 3);
            
            const animateScroll = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                window.scrollTo(0, startPosition * (1 - easeOutCubic(progress)));
                
                if (progress < 1) {
                    requestAnimationFrame(animateScroll);
                } else {
                    this.triggerEvent('footer:scrolltop:complete');
                }
            };
            
            requestAnimationFrame(animateScroll);
            
            // Analytics
            this.trackEvent('back_to_top_clicked');
        },

        /**
         * Update back to top visibility
         */
        updateBackToTopVisibility() {
            if (!this.elements.backToTop) return;
            
            if (this.state.scrollPosition > this.config.scrollThreshold) {
                this.elements.backToTop.classList.add('visible');
            } else {
                this.elements.backToTop.classList.remove('visible');
            }
        },

        /**
         * ==================================================================
         * NEWSLETTER
         * ==================================================================
         */
        
        setupNewsletter() {
            const form = this.elements.newsletter?.querySelector('form');
            
            if (!form) return;
            
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleNewsletterSubmit(form);
            });
            
            // Real-time validation
            const emailInput = form.querySelector('input[type="email"]');
            emailInput?.addEventListener('input', (e) => {
                this.validateEmail(e.target);
            });
        },

        /**
         * Handle newsletter submit
         */
        handleNewsletterSubmit(form) {
            if (this.state.newsletterSubmitted) {
                this.showNotification('You are already subscribed!', 'info');
                return;
            }
            
            const formData = new FormData(form);
            const email = formData.get('email');
            
            // Validate
            if (!this.isValidEmail(email)) {
                this.showNotification('Please enter a valid email address', 'error');
                return;
            }
            
            // Show loading
            this.setLoadingState(form, true);
            
            // Submit via AJAX
            fetch(form.action || '/wp-admin/admin-ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.state.newsletterSubmitted = true;
                    this.showNotification('Successfully subscribed!', 'success');
                    form.reset();
                    
                    // Show thank you message
                    this.showNewsletterThankYou(form);
                    
                    // Save to localStorage
                    localStorage.setItem('newsletter_subscribed', 'true');
                    
                    // Analytics
                    this.trackEvent('newsletter_subscribed', { email });
                } else {
                    this.showNotification(data.message || 'Subscription failed', 'error');
                }
            })
            .catch(error => {
                this.showNotification('Network error. Please try again.', 'error');
                this.log('Newsletter error:', error);
            })
            .finally(() => {
                this.setLoadingState(form, false);
            });
        },

        /**
         * Validate email
         */
        validateEmail(input) {
            const isValid = this.isValidEmail(input.value);
            
            if (input.value.length > 0) {
                input.classList.toggle('valid', isValid);
                input.classList.toggle('invalid', !isValid);
            } else {
                input.classList.remove('valid', 'invalid');
            }
            
            return isValid;
        },

        /**
         * Check if valid email
         */
        isValidEmail(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        },

        /**
         * Show newsletter thank you
         */
        showNewsletterThankYou(form) {
            const thankYou = document.createElement('div');
            thankYou.className = 'newsletter-thank-you';
            thankYou.innerHTML = `
                <h4>Thank You!</h4>
                <p>Check your email for confirmation.</p>
            `;
            
            form.style.display = 'none';
            form.parentNode.appendChild(thankYou);
            
            // Remove after 5 seconds
            setTimeout(() => {
                thankYou.remove();
                form.style.display = '';
            }, 5000);
        },

        /**
         * ==================================================================
         * SOCIAL SHARE
         * ==================================================================
         */
        
        setupSocialShare() {
            if (!this.config.socialShareEnabled) return;
            
            this.elements.socialLinks?.forEach(link => {
                link.addEventListener('click', (e) => {
                    const network = link.dataset.network || link.className;
                    
                    if (this.shouldOpenInPopup(network)) {
                        e.preventDefault();
                        this.openSocialPopup(link.href, network);
                    }
                    
                    // Analytics
                    this.trackEvent('social_share', network);
                });
            });
        },

        /**
         * Should open in popup
         */
        shouldOpenInPopup(network) {
            return ['facebook', 'twitter', 'linkedin', 'pinterest'].includes(network);
        },

        /**
         * Open social popup
         */
        openSocialPopup(url, network) {
            const width = 600;
            const height = 400;
            const left = (screen.width - width) / 2;
            const top = (screen.height - height) / 2;
            
            window.open(
                url,
                `${network}_share`,
                `width=${width},height=${height},left=${left},top=${top},toolbar=0,status=0`
            );
        },

        /**
         * ==================================================================
         * COOKIE CONSENT
         * ==================================================================
         */
        
        setupCookieConsent() {
            if (!this.config.cookieConsentEnabled) return;
            
            // Check if already accepted
            this.state.cookieAccepted = localStorage.getItem('cookie_consent') === 'accepted';
            
            if (!this.state.cookieAccepted && !this.elements.cookieBanner) {
                this.createCookieBanner();
            }
            
            // Setup handlers
            this.setupCookieHandlers();
        },

        /**
         * Create cookie banner
         */
        createCookieBanner() {
            const banner = document.createElement('div');
            banner.className = 'cookie-consent-banner';
            banner.innerHTML = `
                <div class="cookie-content">
                    <p>We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies.</p>
                    <div class="cookie-actions">
                        <button class="cookie-accept">Accept</button>
                        <button class="cookie-settings">Settings</button>
                        <a href="/privacy-policy" class="cookie-learn-more">Learn More</a>
                    </div>
                </div>
            `;
            
            document.body.appendChild(banner);
            this.elements.cookieBanner = banner;
            
            // Animate in
            setTimeout(() => {
                banner.classList.add('visible');
            }, 1000);
        },

        /**
         * Setup cookie handlers
         */
        setupCookieHandlers() {
            // Accept button
            document.querySelector('.cookie-accept')?.addEventListener('click', () => {
                this.acceptCookies();
            });
            
            // Settings button
            document.querySelector('.cookie-settings')?.addEventListener('click', () => {
                this.openCookieSettings();
            });
        },

        /**
         * Accept cookies
         */
        acceptCookies() {
            this.state.cookieAccepted = true;
            localStorage.setItem('cookie_consent', 'accepted');
            localStorage.setItem('cookie_consent_date', new Date().toISOString());
            
            // Hide banner
            this.elements.cookieBanner?.classList.remove('visible');
            
            setTimeout(() => {
                this.elements.cookieBanner?.remove();
            }, 300);
            
            // Initialize analytics/tracking
            this.initializeTracking();
            
            // Trigger event
            this.triggerEvent('footer:cookies:accepted');
        },

        /**
         * Open cookie settings
         */
        openCookieSettings() {
            // Implement cookie settings modal
            this.log('Cookie settings opened');
        },

        /**
         * ==================================================================
         * ACCORDION (Mobile Footer Widgets)
         * ==================================================================
         */
        
        setupAccordion() {
            if (!this.isMobileDevice()) return;
            
            this.elements.footerAccordion?.forEach(toggle => {
                toggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.toggleAccordion(toggle);
                });
            });
        },

        /**
         * Toggle accordion
         */
        toggleAccordion(toggle) {
            const content = toggle.nextElementSibling;
            const isOpen = toggle.classList.contains('active');
            
            if (isOpen) {
                toggle.classList.remove('active');
                content.style.maxHeight = '0';
            } else {
                // Close others
                this.elements.footerAccordion?.forEach(t => {
                    t.classList.remove('active');
                    t.nextElementSibling.style.maxHeight = '0';
                });
                
                // Open this one
                toggle.classList.add('active');
                content.style.maxHeight = content.scrollHeight + 'px';
            }
        },

        /**
         * ==================================================================
         * STICKY FOOTER
         * ==================================================================
         */
        
        setupStickyBehavior() {
            if (!this.config.stickyFooterBar || !this.elements.footerBar) return;
            
            this.elements.footerBar.style.position = 'fixed';
            this.elements.footerBar.style.bottom = '0';
            this.elements.footerBar.style.left = '0';
            this.elements.footerBar.style.right = '0';
            this.elements.footerBar.style.zIndex = '999';
            
            // Add padding to body
            document.body.style.paddingBottom = this.elements.footerBar.offsetHeight + 'px';
        },

        /**
         * Setup auto hide
         */
        setupAutoHide() {
            let scrollTimeout;
            
            window.addEventListener('scroll', () => {
                clearTimeout(scrollTimeout);
                
                scrollTimeout = setTimeout(() => {
                    this.handleAutoHide();
                }, 100);
            }, { passive: true });
        },

        /**
         * Handle auto hide
         */
        handleAutoHide() {
            const currentScroll = window.pageYOffset;
            const scrollDiff = currentScroll - this.state.lastScrollPosition;
            
            if (scrollDiff > 10 && currentScroll > 100) {
                // Scrolling down - hide
                this.hideFooterBar();
            } else if (scrollDiff < -10) {
                // Scrolling up - show
                this.showFooterBar();
            }
            
            this.state.lastScrollPosition = currentScroll;
        },

        /**
         * Hide footer bar
         */
        hideFooterBar() {
            if (!this.elements.footerBar || !this.state.isFooterVisible) return;
            
            this.state.isFooterVisible = false;
            this.elements.footerBar.classList.add('hidden');
            this.elements.footerBar.style.transform = 'translateY(100%)';
            
            this.triggerEvent('footer:bar:hidden');
        },

        /**
         * Show footer bar
         */
        showFooterBar() {
            if (!this.elements.footerBar || this.state.isFooterVisible) return;
            
            this.state.isFooterVisible = true;
            this.elements.footerBar.classList.remove('hidden');
            this.elements.footerBar.style.transform = 'translateY(0)';
            
            this.triggerEvent('footer:bar:visible');
        },

        /**
         * ==================================================================
         * ANIMATIONS
         * ==================================================================
         */
        
        setupAnimations() {
            // Intersection Observer para animações on scroll
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('animated');
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1
                });
                
                // Observar elementos do footer
                const animatedElements = document.querySelectorAll('.footer-animate');
                animatedElements.forEach(el => observer.observe(el));
            }
        },

        /**
         * Animate search in
         */
        animateSearchIn(searchElement) {
            searchElement.style.animation = 'slideInUp 0.3s ease';
        },

        /**
         * Animate search out
         */
        animateSearchOut(searchElement) {
            searchElement.style.animation = 'slideOutDown 0.3s ease';
        },

        /**
         * ==================================================================
         * LIVE CHAT
         * ==================================================================
         */
        
        setupLiveChat() {
            if (!this.elements.liveChat) return;
            
            // Setup chat widget
            this.elements.liveChat.addEventListener('click', () => {
                this.openLiveChat();
            });
            
            // Check for unread messages
            this.checkUnreadMessages();
        },

        /**
         * Open live chat
         */
        openLiveChat() {
            // Integrate with live chat service
            if (typeof tawk_API !== 'undefined') {
                tawk_API.toggle();
            } else if (typeof Intercom !== 'undefined') {
                Intercom('show');
            } else if (typeof drift !== 'undefined') {
                drift.api.openChat();
            } else {
                // Fallback
                this.log('No live chat service found');
            }
            
            this.trackEvent('live_chat_opened');
        },

        /**
         * Check unread messages
         */
        checkUnreadMessages() {
            // Check for unread messages and show badge
            // Implementation depends on chat service
        },

        /**
         * ==================================================================
         * ACCESSIBILITY
         * ==================================================================
         */
        
        setupAccessibility() {
            // Skip to content link
            this.setupSkipLinks();
            
            // Keyboard navigation
            this.setupKeyboardNavigation();
            
            // ARIA labels
            this.updateAriaLabels();
            
            // Focus trap for modals
            this.setupFocusTrap();
        },

        /**
         * Setup skip links
         */
        setupSkipLinks() {
            const skipLink = document.createElement('a');
            skipLink.href = '#main';
            skipLink.className = 'skip-to-content';
            skipLink.textContent = 'Skip to content';
            
            document.body.insertBefore(skipLink, document.body.firstChild);
        },

        /**
         * Setup keyboard navigation
         */
        setupKeyboardNavigation() {
            document.addEventListener('keydown', (e) => {
                // ESC to close search
                if (e.key === 'Escape' && this.state.isSearchActive) {
                    this.toggleSearch();
                }
                
                // Tab trap in footer bar when search is active
                if (e.key === 'Tab' && this.state.isSearchActive) {
                    this.handleTabTrap(e);
                }
            });
        },

        /**
         * Update ARIA labels
         */
        updateAriaLabels() {
            // Add ARIA labels to footer elements
            this.elements.footerBar?.setAttribute('role', 'navigation');
            this.elements.footerBar?.setAttribute('aria-label', 'Mobile footer navigation');
            
            // Update search toggle
            this.elements.searchToggle?.setAttribute('aria-expanded', this.state.isSearchActive);
            this.elements.searchToggle?.setAttribute('aria-label', 'Toggle search');
        },

        /**
         * Setup focus trap
         */
        setupFocusTrap() {
            // Implement focus trap for modal-like elements
        },

        /**
         * Handle tab trap
         */
        handleTabTrap(e) {
            const focusableElements = this.elements.footerBar?.querySelectorAll(
                'a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])'
            );
            
            if (!focusableElements) return;
            
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];
            
            if (e.shiftKey && document.activeElement === firstElement) {
                e.preventDefault();
                lastElement.focus();
            } else if (!e.shiftKey && document.activeElement === lastElement) {
                e.preventDefault();
                firstElement.focus();
            }
        },

        /**
         * ==================================================================
         * STANDARD FOOTER
         * ==================================================================
         */
        
        initStandardFooter() {
            // Inicialização para footer padrão (não móvel)
            this.setupBackToTop();
            this.setupNewsletter();
            this.setupSocialShare();
            this.setupCookieConsent();
            this.setupAnimations();
            this.setupLiveChat();
            this.setupAccessibility();
            
            // Bind eventos para footer padrão
            this.bindStandardEvents();
            
            this.log('Standard footer initialized');
        },

        /**
         * ==================================================================
         * EVENT BINDING
         * ==================================================================
         */
        
        bindEvents() {
            const self = this;
            
            // Scroll event
            window.addEventListener('scroll', this.debounce(() => {
                self.checkScrollPosition();
                self.updateBackToTopVisibility();
            }, 100), { passive: true });
            
            // Resize event
            window.addEventListener('resize', this.debounce(() => {
                self.checkDeviceType();
                self.adjustFooterLayout();
            }, 250));
            
            // Orientation change
            window.addEventListener('orientationchange', () => {
                setTimeout(() => {
                    self.adjustFooterLayout();
                }, 100);
            });
            
            // Page visibility
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    self.checkUnreadMessages();
                }
            });
            
            // Custom events
            this.bindCustomEvents();
        },

        /**
         * Bind standard events
         */
        bindStandardEvents() {
            // Similar to bindEvents but for standard footer
            this.bindEvents();
        },

        /**
         * Bind custom events
         */
        bindCustomEvents() {
            // Listen for cart updates
            document.addEventListener('cart:updated', () => {
                this.updateCartBadge();
            });
            
            // Listen for wishlist updates
            document.addEventListener('wishlist:updated', () => {
                this.updateWishlistBadge();
            });
        },

        /**
         * ==================================================================
         * PANEL HANDLERS
         * ==================================================================
         */
        
        /**
         * Open account panel
         */
        openAccountPanel() {
            // Implement account panel
            this.log('Account panel opened');
            this.triggerEvent('footer:account:open');
        },

        /**
         * Open cart panel
         */
        openCartPanel() {
            // Trigger cart open event
            document.dispatchEvent(new CustomEvent('cart:toggle'));
            this.triggerEvent('footer:cart:open');
        },

        /**
         * Open mobile menu
         */
        openMobileMenu() {
            // Trigger menu open event
            document.dispatchEvent(new CustomEvent('menu:toggle'));
            this.triggerEvent('footer:menu:open');
        },

        /**
         * Open wishlist
         */
        openWishlist() {
            // Navigate to wishlist or open panel
            window.location.href = '/wishlist';
            this.triggerEvent('footer:wishlist:open');
        },

        /**
         * ==================================================================
         * UTILITIES
         * ==================================================================
         */
        
        /**
         * Check device type
         */
        checkDeviceType() {
            this.state.isMobile = window.innerWidth < this.config.mobileBreakpoint;
            this.state.isTablet = window.innerWidth < this.config.tabletBreakpoint;
            
            // Update body classes
            document.body.classList.toggle('is-mobile', this.state.isMobile);
            document.body.classList.toggle('is-tablet', this.state.isTablet);
        },

        /**
         * Check scroll position
         */
        checkScrollPosition() {
            this.state.scrollPosition = window.pageYOffset;
        },

        /**
         * Is mobile device
         */
        isMobileDevice() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        },

        /**
         * Adjust viewport for keyboard
         */
        adjustViewportForKeyboard(input) {
            if (!this.isMobileDevice()) return;
            
            // Scroll input into view
            setTimeout(() => {
                input.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 300);
        },

        /**
         * Handle virtual keyboard
         */
        handleVirtualKeyboard() {
            const viewportHeight = window.visualViewport?.height;
            const windowHeight = window.innerHeight;
            
            if (viewportHeight < windowHeight * 0.75) {
                // Keyboard is open
                this.hideFooterBar();
            } else {
                // Keyboard is closed
                this.showFooterBar();
            }
        },

        /**
         * Auto resize textarea
         */
        autoResizeTextarea(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        },

        /**
         * Adjust footer layout
         */
        adjustFooterLayout() {
            // Responsive adjustments
            if (this.state.isMobile) {
                this.enableMobileLayout();
            } else {
                this.disableMobileLayout();
            }
        },

        /**
         * Enable mobile layout
         */
        enableMobileLayout() {
            this.elements.footerMain?.classList.add('mobile-layout');
            this.setupAccordion();
        },

        /**
         * Disable mobile layout
         */
        disableMobileLayout() {
            this.elements.footerMain?.classList.remove('mobile-layout');
            
            // Reset accordions
            this.elements.footerAccordion?.forEach(toggle => {
                toggle.classList.remove('active');
                toggle.nextElementSibling.style.maxHeight = '';
            });
        },

        /**
         * Update cart badge
         */
        updateCartBadge() {
            const badge = this.elements.footerBar?.querySelector('.cart-badge');
            if (badge) {
                // Update badge count
                const count = parseInt(badge.textContent) || 0;
                badge.textContent = count + 1;
                badge.classList.add('updated');
                
                setTimeout(() => {
                    badge.classList.remove('updated');
                }, 1000);
            }
        },

        /**
         * Update wishlist badge
         */
        updateWishlistBadge() {
            const badge = this.elements.footerBar?.querySelector('.wishlist-badge');
            if (badge) {
                // Update badge count
                const count = parseInt(badge.textContent) || 0;
                badge.textContent = count + 1;
                badge.classList.add('updated');
                
                setTimeout(() => {
                    badge.classList.remove('updated');
                }, 1000);
            }
        },

        /**
         * Show notification
         */
        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `footer-notification ${type}`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('visible');
            }, 10);
            
            setTimeout(() => {
                notification.classList.remove('visible');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        },

        /**
         * Set loading state
         */
        setLoadingState(element, loading) {
            if (loading) {
                element.classList.add('loading');
                element.setAttribute('disabled', 'disabled');
            } else {
                element.classList.remove('loading');
                element.removeAttribute('disabled');
            }
        },

        /**
         * Initialize tracking
         */
        initializeTracking() {
            // Initialize analytics/tracking scripts after cookie consent
            if (typeof gtag !== 'undefined') {
                gtag('consent', 'update', {
                    'analytics_storage': 'granted',
                    'ads_storage': 'granted'
                });
            }
        },

        /**
         * Track event
         */
        trackEvent(action, label = '', value = '') {
            // Google Analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', action, {
                    event_category: 'Footer',
                    event_label: label,
                    value: value
                });
            }
            
            // Custom tracking
            this.triggerEvent('footer:track', { action, label, value });
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
        }
    };

    /**
     * ==================================================================
     * INICIALIZAÇÃO
     * ==================================================================
     */
    
    // Inicializar
    NosfirFooter.init();
    
    // Expor globalmente para debugging e extensibilidade
    window.NosfirFooter = NosfirFooter;

})();