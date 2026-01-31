/**
 * Nosfir Homepage JavaScript
 * 
 * @package     Nosfir
 * @version     1.0.0
 * @author      David Creator
 * 
 * Gerencia comportamentos da homepage incluindo hero section, 
 * featured image, animações, sliders e conteúdo dinâmico
 * Baseado no script de referência com funcionalidades expandidas
 */

(function () {
    'use strict';

    /**
     * ==================================================================
     * NOSFIR HOMEPAGE MANAGER
     * ==================================================================
     */
    
    const NosfirHomepage = {
        
        // Configurações
        config: {
            heroEnabled: true,
            parallaxEnabled: true,
            animationsEnabled: true,
            slidersEnabled: true,
            lazyLoadEnabled: true,
            videoBackgroundEnabled: true,
            dynamicContentEnabled: true,
            mobileBreakpoint: 768,
            tabletBreakpoint: 1024,
            animationSpeed: 600,
            parallaxSpeed: 0.5,
            autoPlaySliders: true,
            sliderInterval: 5000,
            debug: false
        },

        // Elementos DOM
        elements: {
            homepageContent: null,
            heroSection: null,
            featuredImage: null,
            siteMain: null,
            sliders: null,
            parallaxElements: null,
            animatedElements: null,
            videoBackgrounds: null,
            dynamicSections: null
        },

        // Estado
        state: {
            isLoaded: false,
            heroHeight: 0,
            scrollPosition: 0,
            viewportWidth: 0,
            viewportHeight: 0,
            isMobile: false,
            isTablet: false,
            slidersInitialized: false,
            videosLoaded: false,
            contentLoaded: false
        },

        // Cache
        cache: {
            dimensions: {},
            colors: {},
            images: {},
            content: {}
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
                self.log('Homepage initializing...');
                
                // Cachear elementos principais - Baseado na referência
                self.cacheElements();
                
                // Verificar se é homepage com thumbnail - Como na referência
                if (!self.elements.homepageContent) {
                    self.log('Not a homepage with featured image - skipping');
                    return;
                }
                
                // Setup hero content - Baseado na referência
                self.setupHeroContent();
                
                // Setup dimensões - Baseado na referência
                self.updateDimensions();
                
                // Setup funcionalidades adicionais
                self.setupParallax();
                self.setupAnimations();
                self.setupSliders();
                self.setupVideoBackgrounds();
                self.setupLazyLoading();
                self.setupDynamicContent();
                self.setupTestimonials();
                self.setupCounters();
                self.setupPortfolio();
                self.setupCTA();
                self.setupNewsletter();
                self.setupMaps();
                
                // Bind eventos
                self.bindEvents();
                
                // Adaptive backgrounds
                self.setupAdaptiveBackgrounds();
                
                // Verificar estado inicial
                self.checkViewport();
                
                // Marcar como carregado
                self.state.isLoaded = true;
                document.body.classList.add('homepage-loaded');
                
                // Trigger custom event
                self.triggerEvent('homepage:ready');
                
                self.log('Homepage initialized successfully');
            });
        },

        /**
         * Logger condicional
         */
        log(message, type = 'log') {
            if (this.config.debug) {
                console[type]('[Nosfir Homepage]:', message);
            }
        },

        /**
         * ==================================================================
         * CACHEAR ELEMENTOS
         * ==================================================================
         */
        
        cacheElements() {
            // Elemento principal da homepage - Como na referência
            this.elements.homepageContent = document.querySelector(
                '.page-template-template-homepage .type-page.has-post-thumbnail'
            );
            
            // Site main - Como na referência
            this.elements.siteMain = document.querySelector('.site-main');
            
            // Elementos adicionais
            this.elements.heroSection = document.querySelector('.hero-section');
            this.elements.featuredImage = document.querySelector('.homepage-featured-image');
            this.elements.sliders = document.querySelectorAll('.homepage-slider');
            this.elements.parallaxElements = document.querySelectorAll('[data-parallax]');
            this.elements.animatedElements = document.querySelectorAll('[data-animate]');
            this.elements.videoBackgrounds = document.querySelectorAll('.video-background');
            this.elements.dynamicSections = document.querySelectorAll('[data-dynamic]');
            this.elements.testimonials = document.querySelector('.testimonials-section');
            this.elements.counters = document.querySelectorAll('.counter');
            this.elements.portfolio = document.querySelector('.portfolio-grid');
            this.elements.ctaSection = document.querySelector('.cta-section');
            
            this.log('Elements cached', this.elements);
        },

        /**
         * ==================================================================
         * HERO CONTENT SETUP - Baseado na referência
         * ==================================================================
         */
        
        setupHeroContent() {
            if (!this.elements.homepageContent) return;
            
            // Adicionar classe loaded aos elementos - Como na referência
            const entries = this.elements.homepageContent.querySelectorAll(
                '.entry-title, .entry-content'
            );
            
            for (let i = 0; i < entries.length; i++) {
                entries[i].classList.add('loaded');
            }
            
            // Setup adicional do hero
            this.setupHeroEffects();
            this.setupHeroAnimation();
            this.setupHeroInteraction();
        },

        /**
         * Setup hero effects
         */
        setupHeroEffects() {
            const hero = this.elements.heroSection || this.elements.homepageContent;
            
            if (!hero) return;
            
            // Add gradient overlay
            this.addGradientOverlay(hero);
            
            // Add pattern overlay
            if (hero.dataset.pattern) {
                this.addPatternOverlay(hero);
            }
            
            // Setup text effects
            this.setupTextEffects(hero);
            
            // Setup image effects
            this.setupImageEffects(hero);
        },

        /**
         * Setup hero animation
         */
        setupHeroAnimation() {
            const hero = this.elements.heroSection || this.elements.homepageContent;
            
            if (!hero || !this.config.animationsEnabled) return;
            
            // Entrada animada do título
            const title = hero.querySelector('.entry-title');
            if (title) {
                title.style.opacity = '0';
                title.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    title.style.transition = `all ${this.config.animationSpeed}ms cubic-bezier(0.4, 0, 0.2, 1)`;
                    title.style.opacity = '1';
                    title.style.transform = 'translateY(0)';
                }, 100);
            }
            
            // Entrada animada do conteúdo
            const content = hero.querySelector('.entry-content');
            if (content) {
                content.style.opacity = '0';
                content.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    content.style.transition = `all ${this.config.animationSpeed}ms cubic-bezier(0.4, 0, 0.2, 1)`;
                    content.style.opacity = '1';
                    content.style.transform = 'translateY(0)';
                }, 300);
            }
            
            // Animação de botões
            this.animateButtons(hero);
        },

        /**
         * Setup hero interaction
         */
        setupHeroInteraction() {
            const hero = this.elements.heroSection || this.elements.homepageContent;
            
            if (!hero) return;
            
            // Mouse parallax
            if (!this.state.isMobile) {
                hero.addEventListener('mousemove', (e) => {
                    this.handleMouseParallax(e, hero);
                });
            }
            
            // Scroll indicator
            this.addScrollIndicator(hero);
            
            // Video play button
            const playButton = hero.querySelector('.video-play-button');
            if (playButton) {
                playButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.playHeroVideo();
                });
            }
        },

        /**
         * ==================================================================
         * UPDATE DIMENSIONS - Baseado na referência
         * ==================================================================
         */
        
        updateDimensions() {
            const self = this;
            
            // Implementação baseada na referência
            const updateDimensionsFunc = function () {
                if (updateDimensionsFunc._tick) {
                    window.cancelAnimationFrame(updateDimensionsFunc._tick);
                }
                
                updateDimensionsFunc._tick = window.requestAnimationFrame(function () {
                    updateDimensionsFunc._tick = null;
                    
                    if (!self.elements.homepageContent || !self.elements.siteMain) {
                        return;
                    }
                    
                    // Make homepage content full width - Como na referência
                    self.elements.homepageContent.style.width = window.innerWidth + 'px';
                    
                    // Check RTL - Como na referência
                    const htmlDirValue = document.documentElement.getAttribute('dir');
                    
                    // Set margins - Como na referência
                    if (htmlDirValue !== 'rtl') {
                        self.elements.homepageContent.style.marginLeft = 
                            -self.elements.siteMain.getBoundingClientRect().left + 'px';
                    } else {
                        self.elements.homepageContent.style.marginRight = 
                            -self.elements.siteMain.getBoundingClientRect().left + 'px';
                    }
                    
                    // Funcionalidade adicional
                    self.updateHeroDimensions();
                    self.updateCachedDimensions();
                    
                    // Trigger resize event
                    self.triggerEvent('homepage:resize');
                });
            };
            
            // Store function reference for event binding
            this.updateDimensionsFunc = updateDimensionsFunc;
            
            // Execute immediately
            updateDimensionsFunc();
        },

        /**
         * Update hero dimensions
         */
        updateHeroDimensions() {
            const hero = this.elements.heroSection || this.elements.homepageContent;
            
            if (!hero) return;
            
            // Calculate hero height
            let heroHeight = window.innerHeight;
            
            // Adjust for header
            const header = document.querySelector('.site-header');
            if (header) {
                const headerHeight = header.offsetHeight;
                if (!header.classList.contains('absolute')) {
                    heroHeight -= headerHeight;
                }
            }
            
            // Set minimum height
            hero.style.minHeight = heroHeight + 'px';
            
            // Cache dimensions
            this.state.heroHeight = heroHeight;
        },

        /**
         * Update cached dimensions
         */
        updateCachedDimensions() {
            this.cache.dimensions = {
                windowWidth: window.innerWidth,
                windowHeight: window.innerHeight,
                heroHeight: this.state.heroHeight,
                scrollHeight: document.documentElement.scrollHeight
            };
            
            this.state.viewportWidth = window.innerWidth;
            this.state.viewportHeight = window.innerHeight;
        },

        /**
         * ==================================================================
         * PARALLAX
         * ==================================================================
         */
        
        setupParallax() {
            if (!this.config.parallaxEnabled || this.state.isMobile) return;
            
            this.elements.parallaxElements?.forEach(element => {
                // Set initial position
                this.setParallaxPosition(element, 0);
                
                // Store original position
                element.dataset.originalTransform = element.style.transform || '';
            });
        },

        /**
         * Handle parallax on scroll
         */
        handleParallax() {
            if (!this.config.parallaxEnabled || this.state.isMobile) return;
            
            this.elements.parallaxElements?.forEach(element => {
                const rect = element.getBoundingClientRect();
                const speed = parseFloat(element.dataset.parallaxSpeed) || this.config.parallaxSpeed;
                const offset = (rect.top + rect.height / 2 - this.state.viewportHeight / 2) * speed;
                
                this.setParallaxPosition(element, offset);
            });
        },

        /**
         * Set parallax position
         */
        setParallaxPosition(element, offset) {
            const transform = `translateY(${offset}px)`;
            element.style.transform = transform;
            
            // Hardware acceleration
            element.style.willChange = 'transform';
        },

        /**
         * Handle mouse parallax
         */
        handleMouseParallax(e, container) {
            const rect = container.getBoundingClientRect();
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const mouseX = e.clientX - rect.left;
            const mouseY = e.clientY - rect.top;
            
            const moveX = (mouseX - centerX) / centerX * 20;
            const moveY = (mouseY - centerY) / centerY * 20;
            
            const elements = container.querySelectorAll('[data-mouse-parallax]');
            elements.forEach(element => {
                const speed = parseFloat(element.dataset.mouseParallaxSpeed) || 1;
                element.style.transform = `translate(${moveX * speed}px, ${moveY * speed}px)`;
            });
        },

        /**
         * ==================================================================
         * ANIMATIONS
         * ==================================================================
         */
        
        setupAnimations() {
            if (!this.config.animationsEnabled) return;
            
            // Intersection Observer for scroll animations
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            this.animateElement(entry.target);
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });
                
                this.elements.animatedElements?.forEach(element => {
                    observer.observe(element);
                });
            } else {
                // Fallback for older browsers
                this.elements.animatedElements?.forEach(element => {
                    this.animateElement(element);
                });
            }
        },

        /**
         * Animate element
         */
        animateElement(element) {
            const animation = element.dataset.animate;
            const delay = element.dataset.animateDelay || 0;
            const duration = element.dataset.animateDuration || this.config.animationSpeed;
            
            setTimeout(() => {
                element.style.animationDuration = duration + 'ms';
                element.classList.add('animated', animation);
            }, delay);
        },

        /**
         * Animate buttons
         */
        animateButtons(container) {
            const buttons = container.querySelectorAll('.button, .btn');
            
            buttons.forEach((button, index) => {
                button.style.opacity = '0';
                button.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    button.style.transition = `all ${this.config.animationSpeed}ms cubic-bezier(0.4, 0, 0.2, 1)`;
                    button.style.opacity = '1';
                    button.style.transform = 'translateY(0)';
                }, 500 + (index * 100));
            });
        },

        /**
         * ==================================================================
         * SLIDERS
         * ==================================================================
         */
        
        setupSliders() {
            if (!this.config.slidersEnabled || !this.elements.sliders?.length) return;
            
            this.elements.sliders.forEach(slider => {
                this.initializeSlider(slider);
            });
            
            this.state.slidersInitialized = true;
        },

        /**
         * Initialize slider
         */
        initializeSlider(slider) {
            const slides = slider.querySelectorAll('.slide');
            if (!slides.length) return;
            
            let currentSlide = 0;
            const totalSlides = slides.length;
            
            // Create navigation
            this.createSliderNavigation(slider, totalSlides);
            
            // Create indicators
            this.createSliderIndicators(slider, totalSlides);
            
            // Show first slide
            slides[0].classList.add('active');
            
            // Auto play
            if (this.config.autoPlaySliders) {
                setInterval(() => {
                    this.nextSlide(slider, currentSlide, totalSlides);
                    currentSlide = (currentSlide + 1) % totalSlides;
                }, this.config.sliderInterval);
            }
            
            // Touch/swipe support
            this.addSwipeSupport(slider);
        },

        /**
         * Create slider navigation
         */
        createSliderNavigation(slider, totalSlides) {
            const nav = document.createElement('div');
            nav.className = 'slider-navigation';
            
            const prevButton = document.createElement('button');
            prevButton.className = 'slider-prev';
            prevButton.innerHTML = '‹';
            prevButton.setAttribute('aria-label', 'Previous slide');
            
            const nextButton = document.createElement('button');
            nextButton.className = 'slider-next';
            nextButton.innerHTML = '›';
            nextButton.setAttribute('aria-label', 'Next slide');
            
            nav.appendChild(prevButton);
            nav.appendChild(nextButton);
            slider.appendChild(nav);
            
            // Event listeners
            let currentSlide = 0;
            
            prevButton.addEventListener('click', () => {
                currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                this.goToSlide(slider, currentSlide);
            });
            
            nextButton.addEventListener('click', () => {
                currentSlide = (currentSlide + 1) % totalSlides;
                this.goToSlide(slider, currentSlide);
            });
        },

        /**
         * Create slider indicators
         */
        createSliderIndicators(slider, totalSlides) {
            const indicators = document.createElement('div');
            indicators.className = 'slider-indicators';
            
            for (let i = 0; i < totalSlides; i++) {
                const dot = document.createElement('button');
                dot.className = 'indicator';
                dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
                
                if (i === 0) dot.classList.add('active');
                
                dot.addEventListener('click', () => {
                    this.goToSlide(slider, i);
                });
                
                indicators.appendChild(dot);
            }
            
            slider.appendChild(indicators);
        },

        /**
         * Go to specific slide
         */
        goToSlide(slider, index) {
            const slides = slider.querySelectorAll('.slide');
            const indicators = slider.querySelectorAll('.indicator');
            
            slides.forEach(slide => slide.classList.remove('active'));
            indicators.forEach(indicator => indicator.classList.remove('active'));
            
            slides[index].classList.add('active');
            indicators[index]?.classList.add('active');
            
            this.triggerEvent('homepage:slider:change', { slider, index });
        },

        /**
         * Next slide
         */
        nextSlide(slider, currentSlide, totalSlides) {
            const nextIndex = (currentSlide + 1) % totalSlides;
            this.goToSlide(slider, nextIndex);
        },

        /**
         * Add swipe support
         */
        addSwipeSupport(slider) {
            let touchStartX = 0;
            let touchEndX = 0;
            
            slider.addEventListener('touchstart', (e) => {
                touchStartX = e.touches[0].clientX;
            });
            
            slider.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].clientX;
                this.handleSwipe(slider, touchStartX, touchEndX);
            });
        },

        /**
         * Handle swipe
         */
        handleSwipe(slider, startX, endX) {
            const diff = startX - endX;
            const threshold = 50;
            
            if (Math.abs(diff) > threshold) {
                if (diff > 0) {
                    // Swipe left - next slide
                    slider.querySelector('.slider-next')?.click();
                } else {
                    // Swipe right - previous slide
                    slider.querySelector('.slider-prev')?.click();
                }
            }
        },

        /**
         * ==================================================================
         * VIDEO BACKGROUNDS
         * ==================================================================
         */
        
        setupVideoBackgrounds() {
            if (!this.config.videoBackgroundEnabled || !this.elements.videoBackgrounds?.length) {
                return;
            }
            
            this.elements.videoBackgrounds.forEach(container => {
                this.initializeVideoBackground(container);
            });
        },

        /**
         * Initialize video background
         */
        initializeVideoBackground(container) {
            const video = container.querySelector('video');
            
            if (!video) return;
            
            // Set attributes
            video.setAttribute('playsinline', '');
            video.setAttribute('muted', '');
            video.setAttribute('loop', '');
            
            // Play when in view
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        video.play().catch(err => {
                            this.log('Video autoplay failed:', err);
                        });
                    } else {
                        video.pause();
                    }
                });
            });
            
            observer.observe(video);
            
            // Add play/pause button
            this.addVideoControls(container, video);
        },

        /**
         * Add video controls
         */
        addVideoControls(container, video) {
            const button = document.createElement('button');
            button.className = 'video-control';
            button.innerHTML = '⏸';
            button.setAttribute('aria-label', 'Pause video');
            
            button.addEventListener('click', () => {
                if (video.paused) {
                    video.play();
                    button.innerHTML = '⏸';
                    button.setAttribute('aria-label', 'Pause video');
                } else {
                    video.pause();
                    button.innerHTML = '▶';
                    button.setAttribute('aria-label', 'Play video');
                }
            });
            
            container.appendChild(button);
        },

        /**
         * Play hero video
         */
        playHeroVideo() {
            const videoUrl = this.elements.heroSection?.dataset.videoUrl;
            
            if (!videoUrl) return;
            
            // Create video modal
            const modal = document.createElement('div');
            modal.className = 'video-modal';
            modal.innerHTML = `
                <div class="video-modal-content">
                    <button class="video-modal-close">&times;</button>
                    <iframe src="${videoUrl}?autoplay=1" frameborder="0" allowfullscreen></iframe>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Close button
            modal.querySelector('.video-modal-close').addEventListener('click', () => {
                modal.remove();
            });
            
            // Close on backdrop click
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        },

        /**
         * ==================================================================
         * LAZY LOADING
         * ==================================================================
         */
        
        setupLazyLoading() {
            if (!this.config.lazyLoadEnabled) return;
            
            const images = document.querySelectorAll('img[data-src]');
            
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            this.loadImage(entry.target);
                            imageObserver.unobserve(entry.target);
                        }
                    });
                }, {
                    rootMargin: '50px 0px'
                });
                
                images.forEach(img => imageObserver.observe(img));
            } else {
                // Fallback for older browsers
                images.forEach(img => this.loadImage(img));
            }
        },

        /**
         * Load image
         */
        loadImage(img) {
            const src = img.dataset.src;
            const srcset = img.dataset.srcset;
            
            if (src) {
                img.src = src;
                delete img.dataset.src;
            }
            
            if (srcset) {
                img.srcset = srcset;
                delete img.dataset.srcset;
            }
            
            img.classList.add('loaded');
        },

        /**
         * ==================================================================
         * DYNAMIC CONTENT
         * ==================================================================
         */
        
        setupDynamicContent() {
            if (!this.config.dynamicContentEnabled || !this.elements.dynamicSections?.length) {
                return;
            }
            
            this.elements.dynamicSections.forEach(section => {
                this.loadDynamicContent(section);
            });
        },

        /**
         * Load dynamic content
         */
        loadDynamicContent(section) {
            const endpoint = section.dataset.endpoint;
            const type = section.dataset.type;
            
            if (!endpoint) return;
            
            fetch(endpoint)
                .then(response => response.json())
                .then(data => {
                    this.renderDynamicContent(section, data, type);
                })
                .catch(error => {
                    this.log('Error loading dynamic content:', error);
                });
        },

        /**
         * Render dynamic content
         */
        renderDynamicContent(section, data, type) {
            let html = '';
            
            switch (type) {
                case 'posts':
                    html = this.renderPosts(data);
                    break;
                case 'products':
                    html = this.renderProducts(data);
                    break;
                case 'testimonials':
                    html = this.renderTestimonials(data);
                    break;
                default:
                    html = this.renderDefault(data);
            }
            
            section.innerHTML = html;
            
            // Re-initialize animations for new content
            this.setupAnimations();
        },

        /**
         * ==================================================================
         * TESTIMONIALS
         * ==================================================================
         */
        
        setupTestimonials() {
            if (!this.elements.testimonials) return;
            
            const testimonials = this.elements.testimonials.querySelectorAll('.testimonial');
            
            if (!testimonials.length) return;
            
            let currentTestimonial = 0;
            
            // Auto rotate testimonials
            setInterval(() => {
                testimonials[currentTestimonial].classList.remove('active');
                currentTestimonial = (currentTestimonial + 1) % testimonials.length;
                testimonials[currentTestimonial].classList.add('active');
            }, 5000);
        },

        /**
         * ==================================================================
         * COUNTERS
         * ==================================================================
         */
        
        setupCounters() {
            if (!this.elements.counters?.length) return;
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.animateCounter(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            });
            
            this.elements.counters.forEach(counter => {
                observer.observe(counter);
            });
        },

        /**
         * Animate counter
         */
        animateCounter(counter) {
            const target = parseInt(counter.dataset.target);
            const duration = parseInt(counter.dataset.duration) || 2000;
            const start = 0;
            const increment = target / (duration / 16);
            let current = start;
            
            const updateCounter = () => {
                current += increment;
                
                if (current < target) {
                    counter.textContent = Math.ceil(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                }
            };
            
            updateCounter();
        },

        /**
         * ==================================================================
         * PORTFOLIO
         * ==================================================================
         */
        
        setupPortfolio() {
            if (!this.elements.portfolio) return;
            
            // Filter buttons
            const filterButtons = this.elements.portfolio.querySelectorAll('.filter-button');
            const items = this.elements.portfolio.querySelectorAll('.portfolio-item');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const filter = button.dataset.filter;
                    
                    // Update active button
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    
                    // Filter items
                    items.forEach(item => {
                        if (filter === 'all' || item.dataset.category === filter) {
                            item.style.display = '';
                            item.classList.add('fade-in');
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });
            
            // Lightbox for portfolio items
            this.setupPortfolioLightbox();
        },

        /**
         * Setup portfolio lightbox
         */
        setupPortfolioLightbox() {
            const items = this.elements.portfolio?.querySelectorAll('.portfolio-item');
            
            items?.forEach(item => {
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.openLightbox(item);
                });
            });
        },

        /**
         * Open lightbox
         */
        openLightbox(item) {
            const image = item.dataset.image;
            const title = item.dataset.title;
            const description = item.dataset.description;
            
            const lightbox = document.createElement('div');
            lightbox.className = 'lightbox';
            lightbox.innerHTML = `
                <div class="lightbox-content">
                    <button class="lightbox-close">&times;</button>
                    <img src="${image}" alt="${title}">
                    <h3>${title}</h3>
                    <p>${description}</p>
                </div>
            `;
            
            document.body.appendChild(lightbox);
            
            // Close events
            lightbox.querySelector('.lightbox-close').addEventListener('click', () => {
                lightbox.remove();
            });
            
            lightbox.addEventListener('click', (e) => {
                if (e.target === lightbox) {
                    lightbox.remove();
                }
            });
        },

        /**
         * ==================================================================
         * CTA SECTION
         * ==================================================================
         */
        
        setupCTA() {
            const ctaButtons = this.elements.ctaSection?.querySelectorAll('.cta-button');
            
            ctaButtons?.forEach(button => {
                button.addEventListener('click', (e) => {
                    // Track CTA click
                    this.trackEvent('cta_click', button.dataset.cta);
                    
                    // Smooth scroll if anchor link
                    if (button.getAttribute('href')?.startsWith('#')) {
                        e.preventDefault();
                        const target = document.querySelector(button.getAttribute('href'));
                        if (target) {
                            target.scrollIntoView({ behavior: 'smooth' });
                        }
                    }
                });
            });
        },

        /**
         * ==================================================================
         * NEWSLETTER
         * ==================================================================
         */
        
        setupNewsletter() {
            const form = document.querySelector('.homepage-newsletter form');
            
            if (!form) return;
            
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitNewsletter(form);
            });
        },

        /**
         * Submit newsletter
         */
        submitNewsletter(form) {
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showNotification('Successfully subscribed!', 'success');
                    form.reset();
                } else {
                    this.showNotification('Subscription failed. Please try again.', 'error');
                }
            })
            .catch(error => {
                this.showNotification('Network error. Please try again.', 'error');
            });
        },

        /**
         * ==================================================================
         * MAPS
         * ==================================================================
         */
        
        setupMaps() {
            const mapContainer = document.querySelector('.homepage-map');
            
            if (!mapContainer) return;
            
            // Load map when in view
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.loadMap(mapContainer);
                        observer.unobserve(mapContainer);
                    }
                });
            });
            
            observer.observe(mapContainer);
        },

        /**
         * Load map
         */
        loadMap(container) {
            // Implement map loading (Google Maps, Mapbox, etc.)
            const lat = parseFloat(container.dataset.lat);
            const lng = parseFloat(container.dataset.lng);
            
            // Example with Google Maps
            if (typeof google !== 'undefined') {
                const map = new google.maps.Map(container, {
                    center: { lat, lng },
                    zoom: 15
                });
                
                new google.maps.Marker({
                    position: { lat, lng },
                    map: map
                });
            }
        },

        /**
         * ==================================================================
         * ADAPTIVE BACKGROUNDS
         * ==================================================================
         */
        
        setupAdaptiveBackgrounds() {
            const images = document.querySelectorAll('[data-adaptive-background]');
            
            images.forEach(img => {
                if (img.complete) {
                    this.extractColors(img);
                } else {
                    img.addEventListener('load', () => {
                        this.extractColors(img);
                    });
                }
            });
        },

        /**
         * Extract colors from image
         */
        extractColors(img) {
            // Use canvas to extract dominant color
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            canvas.width = img.width;
            canvas.height = img.height;
            
            ctx.drawImage(img, 0, 0);
            
            try {
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const dominantColor = this.getDominantColor(imageData.data);
                
                // Apply color to parent element
                const parent = img.closest('[data-adaptive-background]');
                if (parent) {
                    parent.style.backgroundColor = `rgb(${dominantColor.r}, ${dominantColor.g}, ${dominantColor.b})`;
                    
                    // Adjust text color for contrast
                    const brightness = (dominantColor.r * 299 + dominantColor.g * 587 + dominantColor.b * 114) / 1000;
                    parent.style.color = brightness > 128 ? '#000' : '#fff';
                }
            } catch (e) {
                this.log('Cannot extract colors (CORS):', e);
            }
        },

        /**
         * Get dominant color
         */
        getDominantColor(data) {
            let r = 0, g = 0, b = 0;
            let count = 0;
            
            for (let i = 0; i < data.length; i += 4) {
                r += data[i];
                g += data[i + 1];
                b += data[i + 2];
                count++;
            }
            
            return {
                r: Math.floor(r / count),
                g: Math.floor(g / count),
                b: Math.floor(b / count)
            };
        },

        /**
         * ==================================================================
         * UTILITY FUNCTIONS
         * ==================================================================
         */
        
        /**
         * Add gradient overlay
         */
        addGradientOverlay(element) {
            const overlay = document.createElement('div');
            overlay.className = 'gradient-overlay';
            element.appendChild(overlay);
        },

        /**
         * Add pattern overlay
         */
        addPatternOverlay(element) {
            const pattern = document.createElement('div');
            pattern.className = 'pattern-overlay';
            pattern.style.backgroundImage = `url(${element.dataset.pattern})`;
            element.appendChild(pattern);
        },

        /**
         * Setup text effects
         */
        setupTextEffects(container) {
            // Split text animation
            const splitTexts = container.querySelectorAll('[data-split-text]');
            
            splitTexts.forEach(text => {
                const words = text.textContent.split(' ');
                text.innerHTML = words.map(word => 
                    `<span class="split-word">${word}</span>`
                ).join(' ');
            });
            
            // Typing effect
            const typingTexts = container.querySelectorAll('[data-typing]');
            
            typingTexts.forEach(text => {
                this.typeText(text);
            });
        },

        /**
         * Type text effect
         */
        typeText(element) {
            const text = element.textContent;
            element.textContent = '';
            element.style.visibility = 'visible';
            
            let index = 0;
            const type = () => {
                if (index < text.length) {
                    element.textContent += text.charAt(index);
                    index++;
                    setTimeout(type, 50);
                }
            };
            
            type();
        },

        /**
         * Setup image effects
         */
        setupImageEffects(container) {
            // Ken Burns effect
            const kenBurnsImages = container.querySelectorAll('[data-ken-burns]');
            
            kenBurnsImages.forEach(img => {
                img.classList.add('ken-burns');
            });
        },

        /**
         * Add scroll indicator
         */
        addScrollIndicator(container) {
            const indicator = document.createElement('div');
            indicator.className = 'scroll-indicator';
            indicator.innerHTML = `
                <span>Scroll</span>
                <div class="scroll-arrow"></div>
            `;
            
            indicator.addEventListener('click', () => {
                const nextSection = container.nextElementSibling;
                if (nextSection) {
                    nextSection.scrollIntoView({ behavior: 'smooth' });
                }
            });
            
            container.appendChild(indicator);
        },

        /**
         * Check viewport
         */
        checkViewport() {
            this.state.isMobile = window.innerWidth < this.config.mobileBreakpoint;
            this.state.isTablet = window.innerWidth < this.config.tabletBreakpoint;
            
            document.body.classList.toggle('is-mobile', this.state.isMobile);
            document.body.classList.toggle('is-tablet', this.state.isTablet);
        },

        /**
         * Render posts
         */
        renderPosts(posts) {
            return posts.map(post => `
                <article class="post-card">
                    <img src="${post.thumbnail}" alt="${post.title}">
                    <h3>${post.title}</h3>
                    <p>${post.excerpt}</p>
                    <a href="${post.url}" class="read-more">Read More</a>
                </article>
            `).join('');
        },

        /**
         * Render products
         */
        renderProducts(products) {
            return products.map(product => `
                <div class="product-card">
                    <img src="${product.image}" alt="${product.name}">
                    <h3>${product.name}</h3>
                    <span class="price">${product.price}</span>
                    <button class="add-to-cart" data-product-id="${product.id}">Add to Cart</button>
                </div>
            `).join('');
        },

        /**
         * Render testimonials
         */
        renderTestimonials(testimonials) {
            return testimonials.map(testimonial => `
                <div class="testimonial">
                    <blockquote>${testimonial.content}</blockquote>
                    <cite>${testimonial.author}</cite>
                </div>
            `).join('');
        },

        /**
         * Render default
         */
        renderDefault(data) {
            return JSON.stringify(data);
        },

        /**
         * Show notification
         */
        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `homepage-notification ${type}`;
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
         * Track event
         */
        trackEvent(action, label = '') {
            // Google Analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', action, {
                    event_category: 'Homepage',
                    event_label: label
                });
            }
            
            // Custom tracking
            this.triggerEvent('homepage:track', { action, label });
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
            // Resize event - Como na referência
            window.addEventListener('resize', this.updateDimensionsFunc);
            
            // Scroll event
            window.addEventListener('scroll', () => {
                this.state.scrollPosition = window.pageYOffset;
                this.handleParallax();
            }, { passive: true });
            
            // Orientation change
            window.addEventListener('orientationchange', () => {
                setTimeout(() => {
                    this.updateDimensions();
                }, 100);
            });
            
            // Page visibility
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    // Resume animations/videos
                    this.resumeAnimations();
                } else {
                    // Pause animations/videos
                    this.pauseAnimations();
                }
            });
        },

        /**
         * Resume animations
         */
        resumeAnimations() {
            // Resume video backgrounds
            this.elements.videoBackgrounds?.forEach(container => {
                container.querySelector('video')?.play();
            });
        },

        /**
         * Pause animations
         */
        pauseAnimations() {
            // Pause video backgrounds
            this.elements.videoBackgrounds?.forEach(container => {
                container.querySelector('video')?.pause();
            });
        }
    };

    /**
     * ==================================================================
     * INICIALIZAÇÃO
     * ==================================================================
     */
    
    // Inicializar
    NosfirHomepage.init();
    
    // Expor globalmente para debugging e extensibilidade
    window.NosfirHomepage = NosfirHomepage;

})();