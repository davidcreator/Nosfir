/**
 * Nosfir Theme - Main JavaScript
 *
 * @package Nosfir
 */

(function() {
    'use strict';

    /**
     * DOM Ready
     */
    document.addEventListener('DOMContentLoaded', function() {
        initMobileMenu();
        initStickyHeader();
        initSmoothScroll();
        initAnimations();
    });

    /**
     * Mobile Menu Toggle
     */
    function initMobileMenu() {
        const menuToggle = document.querySelector('.menu-toggle');
        const navigation = document.querySelector('.main-navigation');
        
        if (!menuToggle || !navigation) return;
        
        menuToggle.addEventListener('click', function() {
            const isOpen = navigation.classList.contains('is-open');
            
            navigation.classList.toggle('is-open');
            menuToggle.classList.toggle('is-active');
            menuToggle.setAttribute('aria-expanded', !isOpen);
            
            // Prevent body scroll when menu is open
            document.body.style.overflow = isOpen ? '' : 'hidden';
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!navigation.contains(e.target) && !menuToggle.contains(e.target)) {
                if (navigation.classList.contains('is-open')) {
                    navigation.classList.remove('is-open');
                    menuToggle.classList.remove('is-active');
                    menuToggle.setAttribute('aria-expanded', 'false');
                    document.body.style.overflow = '';
                }
            }
        });
        
        // Close menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && navigation.classList.contains('is-open')) {
                navigation.classList.remove('is-open');
                menuToggle.classList.remove('is-active');
                menuToggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }
        });
        
        // Close menu when window is resized to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768 && navigation.classList.contains('is-open')) {
                navigation.classList.remove('is-open');
                menuToggle.classList.remove('is-active');
                menuToggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }
        });
    }

    /**
 * Header Search Toggle
 */
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        const searchContainer = document.getElementById('nosfir-header-search');
        const searchToggle = document.getElementById('nosfir-search-toggle');
        const searchDropdown = document.getElementById('nosfir-search-dropdown');
        
        if (!searchToggle || !searchContainer) return;
        
        const searchField = searchDropdown ? searchDropdown.querySelector('.search-field') : null;
        
        // Toggle ao clicar no botão
        searchToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const isOpen = searchContainer.classList.contains('is-open');
            
            if (isOpen) {
                closeSearch();
            } else {
                openSearch();
            }
        });
        
        // Fechar ao clicar fora
        document.addEventListener('click', function(e) {
            if (!searchContainer.contains(e.target)) {
                closeSearch();
            }
        });
        
        // Fechar com ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSearch();
            }
        });
        
        function openSearch() {
            searchContainer.classList.add('is-open');
            searchToggle.setAttribute('aria-expanded', 'true');
            if (searchDropdown) {
                searchDropdown.setAttribute('aria-hidden', 'false');
            }
            // Focar no campo de busca
            setTimeout(function() {
                if (searchField) searchField.focus();
            }, 100);
        }
        
        function closeSearch() {
            searchContainer.classList.remove('is-open');
            searchToggle.setAttribute('aria-expanded', 'false');
            if (searchDropdown) {
                searchDropdown.setAttribute('aria-hidden', 'true');
            }
        }
    });
})();

    /**
     * Sticky Header
     */
    function initStickyHeader() {
        const header = document.querySelector('.site-header');
        
        if (!header) return;
        
        const stickyEnabled = document.body.classList.contains('has-sticky-header') || true;
        
        if (!stickyEnabled) return;
        
        let lastScroll = 0;
        const headerHeight = header.offsetHeight;
        
        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll > headerHeight) {
                header.classList.add('is-sticky');
                document.body.style.paddingTop = headerHeight + 'px';
            } else {
                header.classList.remove('is-sticky');
                document.body.style.paddingTop = '';
            }
            
            lastScroll = currentScroll;
        }, { passive: true });
    }

    /**
     * Smooth Scroll for Anchor Links
     */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                
                if (targetId === '#') return;
                
                const target = document.querySelector(targetId);
                
                if (target) {
                    e.preventDefault();
                    
                    const header = document.querySelector('.site-header');
                    const headerHeight = header ? header.offsetHeight : 0;
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight - 20;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Update URL without reload
                    if (history.pushState) {
                        history.pushState(null, null, targetId);
                    }
                }
            });
        });
    }

    /**
     * Scroll Animations
     */
    function initAnimations() {
        const animatedElements = document.querySelectorAll('.animate-on-scroll');
        
        if (!animatedElements.length) return;
        
        // Use Intersection Observer for better performance
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const el = entry.target;
                        const delay = el.dataset.delay || 0;
                        
                        setTimeout(function() {
                            el.classList.add('is-animated');
                        }, parseInt(delay));
                        
                        observer.unobserve(el);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            animatedElements.forEach(function(el) {
                observer.observe(el);
            });
        } else {
            // Fallback for older browsers
            animatedElements.forEach(function(el) {
                el.classList.add('is-animated');
            });
        }
    }

})();

/**
 * Header Search
 */
(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', initHeaderSearch);
    
    function initHeaderSearch() {
        const container = document.getElementById('header-search');
        const toggle = document.getElementById('search-toggle-btn');
        const dropdown = document.getElementById('search-dropdown');
        
        if (!container || !toggle) return;
        
        const searchField = dropdown ? dropdown.querySelector('.search-field') : null;
        
        // Toggle
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (container.classList.contains('is-open')) {
                close();
            } else {
                open();
            }
        });
        
        // Click outside
        document.addEventListener('click', function(e) {
            if (!container.contains(e.target)) {
                close();
            }
        });
        
        // ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                close();
            }
        });
        
        function open() {
            container.classList.add('is-open');
            toggle.setAttribute('aria-expanded', 'true');
            dropdown.setAttribute('aria-hidden', 'false');
            
            setTimeout(function() {
                if (searchField) searchField.focus();
            }, 150);
        }
        
        function close() {
            container.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
            dropdown.setAttribute('aria-hidden', 'true');
        }
    }
})();