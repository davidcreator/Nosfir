/**
 * WooCommerce Brands Extension JavaScript
 * 
 * @package     Nosfir
 * @subpackage  WooCommerce/Extensions
 * @version     1.0.0
 * @author      David Creator
 * 
 * Funcionalidades: Sticky index, filtros, busca de marcas, carousel, etc.
 * Baseado no script de referência com funcionalidades expandidas
 */

(function () {
    'use strict';

    /**
     * ==================================================================
     * BRANDS MANAGER OBJECT
     * ==================================================================
     */
    
    const NosfirBrands = {
        
        // Configurações
        config: {
            stickyEnabled: true,
            filterEnabled: true,
            searchEnabled: true,
            carouselEnabled: true,
            lazyLoadEnabled: true,
            animationSpeed: 300,
            debounceDelay: 250,
            mobileBreakpoint: 768,
            tabletBreakpoint: 1024,
            debug: false
        },

        // Elementos DOM
        elements: {
            brandsIndex: null,
            brandsContainer: null,
            brandsGrid: null,
            searchInput: null,
            filterButtons: null,
            carousel: null,
            adminBar: null
        },

        // Estado
        state: {
            isSticky: false,
            currentFilter: 'all',
            searchQuery: '',
            viewMode: 'grid', // grid, list, carousel
            sortBy: 'name', // name, products, popular
            currentLetter: '',
            brandsData: [],
            filteredBrands: [],
            favorites: [],
            isLoading: false
        },

        // Dimensões
        dimensions: {
            adminBarHeight: 0,
            containerHeight: 0,
            indexHeight: 0,
            scrollPosition: 0,
            windowWidth: 0,
            windowHeight: 0
        },

        /**
         * ==================================================================
         * INICIALIZAÇÃO - Baseada na referência e expandida
         * ==================================================================
         */
        
        init() {
            const self = this;
            
            // Aguardar DOM ready - Como na referência
            document.addEventListener('DOMContentLoaded', function () {
                self.log('Brands extension initialized');
                
                // Cachear elementos
                self.cacheElements();
                
                // Se não houver elementos de marca, retornar - Como na referência
                if (!self.elements.brandsIndex) {
                    self.log('No brands index found');
                    return;
                }
                
                // Calcular dimensões
                self.calculateDimensions();
                
                // Setup funcionalidades
                self.setupStickyIndex();      // Baseado na referência
                self.setupFilters();
                self.setupSearch();
                self.setupSorting();
                self.setupViewModes();
                self.setupCarousel();
                self.setupLazyLoad();
                self.setupFavorites();
                self.setupQuickView();
                self.setupInfiniteScroll();
                
                // Bind eventos
                self.bindEvents();
                
                // Aplicar sticky inicial - Como na referência
                self.applyStickyBehavior();
                
                // Carregar dados das marcas
                self.loadBrandsData();
                
                // Trigger custom event
                self.triggerEvent('brands:ready');
            });
        },

        /**
         * Logger condicional
         */
        log(message, type = 'log') {
            if (this.config.debug) {
                console[type]('[Nosfir Brands]:', message);
            }
        },

        /**
         * ==================================================================
         * CACHEAR ELEMENTOS
         * ==================================================================
         */
        
        cacheElements() {
            // Elementos principais - Baseado na referência
            this.elements.brandsIndex = document.getElementsByClassName('brands_index')[0];
            this.elements.brandsContainer = document.getElementById('brands_a_z');
            
            // Elementos adicionais
            this.elements.brandsGrid = document.querySelector('.brands-grid');
            this.elements.searchInput = document.getElementById('brands-search');
            this.elements.filterButtons = document.querySelectorAll('.brand-filter');
            this.elements.carousel = document.querySelector('.brands-carousel');
            this.elements.adminBar = document.getElementById('wpadminbar');
            
            // Elementos de UI
            this.elements.viewToggle = document.querySelector('.brands-view-toggle');
            this.elements.sortSelect = document.getElementById('brands-sort');
            this.elements.loadMoreButton = document.querySelector('.brands-load-more');
            this.elements.scrollToTop = document.querySelector('.brands-scroll-top');
            
            // Modals
            this.elements.quickViewModal = document.getElementById('brand-quick-view');
            this.elements.compareModal = document.getElementById('brands-compare');
        },

        /**
         * ==================================================================
         * CALCULAR DIMENSÕES - Baseado na referência
         * ==================================================================
         */
        
        calculateDimensions() {
            // Admin bar height - Como na referência
            this.dimensions.adminBarHeight = document.body.classList.contains('admin-bar') 
                ? 32 
                : 0;
            
            // Container e index heights - Como na referência
            if (this.elements.brandsContainer) {
                this.dimensions.containerHeight = this.elements.brandsContainer.scrollHeight;
            }
            
            if (this.elements.brandsIndex) {
                this.dimensions.indexHeight = this.elements.brandsIndex.scrollHeight + 40; // +40 como na referência
            }
            
            // Window dimensions
            this.dimensions.windowWidth = window.innerWidth;
            this.dimensions.windowHeight = window.innerHeight;
            
            this.log('Dimensions calculated:', this.dimensions);
        },

        /**
         * ==================================================================
         * STICKY INDEX - Implementação baseada na referência
         * ==================================================================
         */
        
        setupStickyIndex() {
            if (!this.config.stickyEnabled || !this.elements.brandsIndex) {
                return;
            }
            
            const self = this;
            
            // Função sticky - Baseada na referência
            this.stickyBrandsAZ = function () {
                // Verificar largura da janela - Como na referência
                if (window.innerWidth > 768 && 
                    self.elements.brandsIndex.getBoundingClientRect().top < 0) {
                    
                    // Calcular padding - Exatamente como na referência
                    const paddingTop = Math.min(
                        Math.abs(self.elements.brandsIndex.getBoundingClientRect().top) + 
                        20 + 
                        self.dimensions.adminBarHeight,
                        self.dimensions.containerHeight - self.dimensions.indexHeight
                    );
                    
                    self.elements.brandsIndex.style.paddingTop = paddingTop + 'px';
                    
                    // Adicionar classe para styling adicional
                    if (!self.state.isSticky) {
                        self.elements.brandsIndex.classList.add('is-sticky');
                        self.state.isSticky = true;
                        self.triggerEvent('brands:sticky:on');
                    }
                } else {
                    // Reset padding - Como na referência
                    self.elements.brandsIndex.style.paddingTop = 0;
                    
                    if (self.state.isSticky) {
                        self.elements.brandsIndex.classList.remove('is-sticky');
                        self.state.isSticky = false;
                        self.triggerEvent('brands:sticky:off');
                    }
                }
            };
            
            // Aplicar imediatamente - Como na referência
            this.stickyBrandsAZ();
            
            // Adicionar smooth scrolling para links do índice
            this.setupIndexNavigation();
        },

        /**
         * Aplicar comportamento sticky
         */
        applyStickyBehavior() {
            if (this.stickyBrandsAZ) {
                this.stickyBrandsAZ();
            }
        },

        /**
         * Setup navegação do índice A-Z
         */
        setupIndexNavigation() {
            if (!this.elements.brandsIndex) return;
            
            const indexLinks = this.elements.brandsIndex.querySelectorAll('a');
            
            indexLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    
                    const targetId = link.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);
                    
                    if (targetElement) {
                        const offset = targetElement.offsetTop - this.dimensions.adminBarHeight - 60;
                        
                        window.scrollTo({
                            top: offset,
                            behavior: 'smooth'
                        });
                        
                        // Highlight active letter
                        this.setActiveLetter(targetId.replace('brands-', ''));
                    }
                });
            });
        },

        /**
         * Set active letter in index
         */
        setActiveLetter(letter) {
            const indexLinks = this.elements.brandsIndex.querySelectorAll('a');
            
            indexLinks.forEach(link => {
                link.classList.remove('active');
                if (link.textContent.toLowerCase() === letter.toLowerCase()) {
                    link.classList.add('active');
                }
            });
            
            this.state.currentLetter = letter;
        },

        /**
         * ==================================================================
         * FILTROS
         * ==================================================================
         */
        
        setupFilters() {
            if (!this.config.filterEnabled || !this.elements.filterButtons) {
                return;
            }
            
            this.elements.filterButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.applyFilter(button.dataset.filter);
                });
            });
            
            // Adicionar filtros avançados
            this.setupAdvancedFilters();
        },

        /**
         * Aplicar filtro
         */
        applyFilter(filter) {
            this.state.currentFilter = filter;
            
            // Update UI
            this.elements.filterButtons.forEach(btn => {
                btn.classList.toggle('active', btn.dataset.filter === filter);
            });
            
            // Filter brands
            this.filterBrands();
            
            // Trigger event
            this.triggerEvent('brands:filtered', { filter });
        },

        /**
         * Setup filtros avançados
         */
        setupAdvancedFilters() {
            // Filtro por categoria
            const categoryFilter = document.getElementById('brands-category-filter');
            if (categoryFilter) {
                categoryFilter.addEventListener('change', (e) => {
                    this.filterByCategory(e.target.value);
                });
            }
            
            // Filtro por país
            const countryFilter = document.getElementById('brands-country-filter');
            if (countryFilter) {
                countryFilter.addEventListener('change', (e) => {
                    this.filterByCountry(e.target.value);
                });
            }
            
            // Filtro por rating
            const ratingFilter = document.getElementById('brands-rating-filter');
            if (ratingFilter) {
                ratingFilter.addEventListener('change', (e) => {
                    this.filterByRating(e.target.value);
                });
            }
        },

        /**
         * ==================================================================
         * BUSCA
         * ==================================================================
         */
        
        setupSearch() {
            if (!this.config.searchEnabled || !this.elements.searchInput) {
                return;
            }
            
            let searchTimeout;
            
            this.elements.searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                
                searchTimeout = setTimeout(() => {
                    this.searchBrands(e.target.value);
                }, this.config.debounceDelay);
            });
            
            // Autocomplete
            this.setupAutocomplete();
            
            // Voice search
            this.setupVoiceSearch();
        },

        /**
         * Buscar marcas
         */
        searchBrands(query) {
            this.state.searchQuery = query.toLowerCase();
            
            if (!query) {
                this.resetSearch();
                return;
            }
            
            this.filterBrands();
            
            // Highlight search terms
            this.highlightSearchTerms(query);
            
            // Analytics
            this.trackSearch(query);
            
            // Trigger event
            this.triggerEvent('brands:searched', { query });
        },

        /**
         * Reset search
         */
        resetSearch() {
            this.state.searchQuery = '';
            this.filterBrands();
            this.removeHighlights();
        },

        /**
         * Setup autocomplete
         */
        setupAutocomplete() {
            if (!this.elements.searchInput) return;
            
            const autocompleteContainer = document.createElement('div');
            autocompleteContainer.className = 'brands-autocomplete';
            this.elements.searchInput.parentNode.appendChild(autocompleteContainer);
            
            this.elements.searchInput.addEventListener('focus', () => {
                this.showAutocomplete();
            });
            
            this.elements.searchInput.addEventListener('blur', () => {
                setTimeout(() => this.hideAutocomplete(), 200);
            });
        },

        /**
         * Setup voice search
         */
        setupVoiceSearch() {
            if (!('webkitSpeechRecognition' in window)) return;
            
            const voiceButton = document.createElement('button');
            voiceButton.className = 'voice-search-btn';
            voiceButton.innerHTML = '🎤';
            voiceButton.title = 'Voice Search';
            
            if (this.elements.searchInput) {
                this.elements.searchInput.parentNode.appendChild(voiceButton);
                
                voiceButton.addEventListener('click', () => {
                    this.startVoiceSearch();
                });
            }
        },

        /**
         * ==================================================================
         * SORTING
         * ==================================================================
         */
        
        setupSorting() {
            const sortSelect = this.elements.sortSelect;
            
            if (!sortSelect) return;
            
            sortSelect.addEventListener('change', (e) => {
                this.sortBrands(e.target.value);
            });
        },

        /**
         * Sort brands
         */
        sortBrands(sortBy) {
            this.state.sortBy = sortBy;
            
            const brands = [...this.state.filteredBrands];
            
            switch(sortBy) {
                case 'name':
                    brands.sort((a, b) => a.name.localeCompare(b.name));
                    break;
                case 'name-desc':
                    brands.sort((a, b) => b.name.localeCompare(a.name));
                    break;
                case 'products':
                    brands.sort((a, b) => b.productCount - a.productCount);
                    break;
                case 'popular':
                    brands.sort((a, b) => b.popularity - a.popularity);
                    break;
                case 'newest':
                    brands.sort((a, b) => new Date(b.dateAdded) - new Date(a.dateAdded));
                    break;
                case 'rating':
                    brands.sort((a, b) => b.rating - a.rating);
                    break;
            }
            
            this.state.filteredBrands = brands;
            this.renderBrands();
            
            this.triggerEvent('brands:sorted', { sortBy });
        },

        /**
         * ==================================================================
         * VIEW MODES
         * ==================================================================
         */
        
        setupViewModes() {
            if (!this.elements.viewToggle) return;
            
            const viewButtons = this.elements.viewToggle.querySelectorAll('[data-view]');
            
            viewButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.changeViewMode(button.dataset.view);
                });
            });
        },

        /**
         * Change view mode
         */
        changeViewMode(mode) {
            this.state.viewMode = mode;
            
            // Update UI
            const container = this.elements.brandsGrid || this.elements.brandsContainer;
            if (container) {
                container.className = container.className.replace(/view-\w+/, '');
                container.classList.add(`view-${mode}`);
            }
            
            // Update buttons
            const viewButtons = this.elements.viewToggle?.querySelectorAll('[data-view]');
            viewButtons?.forEach(btn => {
                btn.classList.toggle('active', btn.dataset.view === mode);
            });
            
            // Re-render if needed
            if (mode === 'carousel') {
                this.initCarousel();
            }
            
            // Save preference
            localStorage.setItem('brands_view_mode', mode);
            
            this.triggerEvent('brands:view:changed', { mode });
        },

        /**
         * ==================================================================
         * CAROUSEL
         * ==================================================================
         */
        
        setupCarousel() {
            if (!this.config.carouselEnabled || !this.elements.carousel) {
                return;
            }
            
            this.initCarousel();
            this.setupCarouselControls();
        },

        /**
         * Initialize carousel
         */
        initCarousel() {
            // Usar biblioteca de carousel (ex: Swiper, Slick, etc.)
            // ou implementação customizada
            
            const carousel = this.elements.carousel;
            if (!carousel) return;
            
            // Exemplo com implementação customizada
            this.carousel = {
                currentSlide: 0,
                slidesPerView: this.getSlidesPerView(),
                totalSlides: carousel.querySelectorAll('.brand-item').length,
                autoplay: null
            };
            
            this.updateCarousel();
            this.startCarouselAutoplay();
        },

        /**
         * Get slides per view based on viewport
         */
        getSlidesPerView() {
            if (window.innerWidth < this.config.mobileBreakpoint) {
                return 2;
            } else if (window.innerWidth < this.config.tabletBreakpoint) {
                return 4;
            }
            return 6;
        },

        /**
         * Setup carousel controls
         */
        setupCarouselControls() {
            const prevBtn = document.querySelector('.brands-carousel-prev');
            const nextBtn = document.querySelector('.brands-carousel-next');
            
            prevBtn?.addEventListener('click', () => this.carouselPrev());
            nextBtn?.addEventListener('click', () => this.carouselNext());
            
            // Touch/swipe support
            this.setupCarouselSwipe();
        },

        /**
         * Carousel previous
         */
        carouselPrev() {
            if (!this.carousel) return;
            
            this.carousel.currentSlide = Math.max(0, this.carousel.currentSlide - 1);
            this.updateCarousel();
            this.restartCarouselAutoplay();
        },

        /**
         * Carousel next
         */
        carouselNext() {
            if (!this.carousel) return;
            
            const maxSlide = this.carousel.totalSlides - this.carousel.slidesPerView;
            this.carousel.currentSlide = Math.min(maxSlide, this.carousel.currentSlide + 1);
            this.updateCarousel();
            this.restartCarouselAutoplay();
        },

        /**
         * Update carousel position
         */
        updateCarousel() {
            if (!this.carousel || !this.elements.carousel) return;
            
            const slideWidth = 100 / this.carousel.slidesPerView;
            const offset = -this.carousel.currentSlide * slideWidth;
            
            const track = this.elements.carousel.querySelector('.carousel-track');
            if (track) {
                track.style.transform = `translateX(${offset}%)`;
            }
        },

        /**
         * Setup carousel swipe
         */
        setupCarouselSwipe() {
            if (!this.elements.carousel) return;
            
            let startX = 0;
            let currentX = 0;
            let isDragging = false;
            
            this.elements.carousel.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                isDragging = true;
            });
            
            this.elements.carousel.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                currentX = e.touches[0].clientX;
            });
            
            this.elements.carousel.addEventListener('touchend', () => {
                if (!isDragging) return;
                
                const diff = startX - currentX;
                if (Math.abs(diff) > 50) {
                    if (diff > 0) {
                        this.carouselNext();
                    } else {
                        this.carouselPrev();
                    }
                }
                
                isDragging = false;
            });
        },

        /**
         * Start carousel autoplay
         */
        startCarouselAutoplay() {
            if (!this.carousel) return;
            
            this.carousel.autoplay = setInterval(() => {
                this.carouselNext();
                
                // Loop back to start
                if (this.carousel.currentSlide >= this.carousel.totalSlides - this.carousel.slidesPerView) {
                    this.carousel.currentSlide = 0;
                    this.updateCarousel();
                }
            }, 5000);
        },

        /**
         * Restart carousel autoplay
         */
        restartCarouselAutoplay() {
            if (this.carousel?.autoplay) {
                clearInterval(this.carousel.autoplay);
                this.startCarouselAutoplay();
            }
        },

        /**
         * ==================================================================
         * LAZY LOAD
         * ==================================================================
         */
        
        setupLazyLoad() {
            if (!this.config.lazyLoadEnabled) return;
            
            const lazyImages = document.querySelectorAll('.brand-logo[data-src]');
            
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            this.loadImage(img);
                            observer.unobserve(img);
                        }
                    });
                }, {
                    rootMargin: '50px 0px',
                    threshold: 0.01
                });
                
                lazyImages.forEach(img => imageObserver.observe(img));
            } else {
                // Fallback for browsers without IntersectionObserver
                lazyImages.forEach(img => this.loadImage(img));
            }
        },

        /**
         * Load image
         */
        loadImage(img) {
            const src = img.dataset.src;
            if (!src) return;
            
            const tempImg = new Image();
            tempImg.onload = () => {
                img.src = src;
                img.classList.add('loaded');
                delete img.dataset.src;
            };
            tempImg.src = src;
        },

        /**
         * ==================================================================
         * FAVORITES
         * ==================================================================
         */
        
        setupFavorites() {
            // Load favorites from localStorage
            this.loadFavorites();
            
            // Setup favorite buttons
            const favoriteButtons = document.querySelectorAll('.brand-favorite');
            
            favoriteButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.toggleFavorite(button.dataset.brandId);
                });
            });
        },

        /**
         * Load favorites
         */
        loadFavorites() {
            const saved = localStorage.getItem('brand_favorites');
            this.state.favorites = saved ? JSON.parse(saved) : [];
            this.updateFavoriteButtons();
        },

        /**
         * Toggle favorite
         */
        toggleFavorite(brandId) {
            const index = this.state.favorites.indexOf(brandId);
            
            if (index > -1) {
                this.state.favorites.splice(index, 1);
                this.showNotification('Removed from favorites', 'info');
            } else {
                this.state.favorites.push(brandId);
                this.showNotification('Added to favorites', 'success');
            }
            
            // Save to localStorage
            localStorage.setItem('brand_favorites', JSON.stringify(this.state.favorites));
            
            // Update UI
            this.updateFavoriteButtons();
            
            // Trigger event
            this.triggerEvent('brands:favorite:toggle', { brandId, isFavorite: index === -1 });
        },

        /**
         * Update favorite buttons
         */
        updateFavoriteButtons() {
            const buttons = document.querySelectorAll('.brand-favorite');
            
            buttons.forEach(button => {
                const brandId = button.dataset.brandId;
                const isFavorite = this.state.favorites.includes(brandId);
                
                button.classList.toggle('is-favorite', isFavorite);
                button.innerHTML = isFavorite ? '❤️' : '🤍';
            });
        },

        /**
         * ==================================================================
         * QUICK VIEW
         * ==================================================================
         */
        
        setupQuickView() {
            const quickViewButtons = document.querySelectorAll('.brand-quick-view');
            
            quickViewButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.showQuickView(button.dataset.brandId);
                });
            });
        },

        /**
         * Show quick view
         */
        showQuickView(brandId) {
            // Load brand data
            this.loadBrandData(brandId).then(brand => {
                this.displayQuickView(brand);
            });
        },

        /**
         * Display quick view modal
         */
        displayQuickView(brand) {
            if (!this.elements.quickViewModal) {
                this.createQuickViewModal();
            }
            
            // Populate modal with brand data
            const modal = this.elements.quickViewModal;
            
            modal.querySelector('.brand-name').textContent = brand.name;
            modal.querySelector('.brand-logo').src = brand.logo;
            modal.querySelector('.brand-description').textContent = brand.description;
            modal.querySelector('.brand-products-count').textContent = brand.productCount;
            modal.querySelector('.brand-website').href = brand.website;
            
            // Show modal
            modal.classList.add('active');
            document.body.classList.add('modal-open');
            
            this.triggerEvent('brands:quickview:open', { brand });
        },

        /**
         * Create quick view modal
         */
        createQuickViewModal() {
            const modal = document.createElement('div');
            modal.id = 'brand-quick-view';
            modal.className = 'brand-modal';
            modal.innerHTML = `
                <div class="modal-content">
                    <button class="modal-close">&times;</button>
                    <div class="brand-modal-header">
                        <img class="brand-logo" alt="">
                        <h2 class="brand-name"></h2>
                    </div>
                    <div class="brand-modal-body">
                        <p class="brand-description"></p>
                        <div class="brand-stats">
                            <span>Products: <strong class="brand-products-count"></strong></span>
                        </div>
                        <a href="#" class="brand-website" target="_blank">Visit Website</a>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            this.elements.quickViewModal = modal;
            
            // Close button
            modal.querySelector('.modal-close').addEventListener('click', () => {
                this.closeQuickView();
            });
            
            // Close on backdrop click
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeQuickView();
                }
            });
        },

        /**
         * Close quick view
         */
        closeQuickView() {
            this.elements.quickViewModal?.classList.remove('active');
            document.body.classList.remove('modal-open');
            this.triggerEvent('brands:quickview:close');
        },

        /**
         * ==================================================================
         * INFINITE SCROLL
         * ==================================================================
         */
        
        setupInfiniteScroll() {
            if (!this.elements.brandsGrid) return;
            
            let page = 1;
            let loading = false;
            let hasMore = true;
            
            const loadMore = () => {
                if (loading || !hasMore) return;
                
                const scrollPosition = window.scrollY + window.innerHeight;
                const threshold = document.body.offsetHeight - 200;
                
                if (scrollPosition > threshold) {
                    loading = true;
                    page++;
                    
                    this.loadMoreBrands(page).then(brands => {
                        if (brands.length > 0) {
                            this.appendBrands(brands);
                        } else {
                            hasMore = false;
                        }
                        loading = false;
                    });
                }
            };
            
            // Throttled scroll handler
            let scrollTimeout;
            window.addEventListener('scroll', () => {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(loadMore, 100);
            });
        },

        /**
         * ==================================================================
         * DATA MANAGEMENT
         * ==================================================================
         */
        
        /**
         * Load brands data
         */
        loadBrandsData() {
            this.state.isLoading = true;
            this.showLoader();
            
            // Simular carregamento de dados (substituir com AJAX real)
            setTimeout(() => {
                this.state.brandsData = this.getMockBrandsData();
                this.state.filteredBrands = [...this.state.brandsData];
                this.renderBrands();
                this.state.isLoading = false;
                this.hideLoader();
            }, 500);
        },

        /**
         * Load brand data
         */
        async loadBrandData(brandId) {
            // Implementar carregamento real via AJAX
            return this.state.brandsData.find(b => b.id === brandId);
        },

        /**
         * Load more brands
         */
        async loadMoreBrands(page) {
            // Implementar carregamento real via AJAX
            return [];
        },

        /**
         * Get mock brands data
         */
        getMockBrandsData() {
            // Dados mock para desenvolvimento
            return [];
        },

        /**
         * ==================================================================
         * FILTERING
         * ==================================================================
         */
        
        /**
         * Filter brands
         */
        filterBrands() {
            let filtered = [...this.state.brandsData];
            
            // Apply search filter
            if (this.state.searchQuery) {
                filtered = filtered.filter(brand => 
                    brand.name.toLowerCase().includes(this.state.searchQuery) ||
                    brand.description?.toLowerCase().includes(this.state.searchQuery)
                );
            }
            
            // Apply category filter
            if (this.state.currentFilter && this.state.currentFilter !== 'all') {
                filtered = filtered.filter(brand => 
                    brand.category === this.state.currentFilter
                );
            }
            
            this.state.filteredBrands = filtered;
            this.renderBrands();
        },

        /**
         * Filter by category
         */
        filterByCategory(category) {
            this.state.currentFilter = category;
            this.filterBrands();
        },

        /**
         * Filter by country
         */
        filterByCountry(country) {
            // Implementar filtro por país
        },

        /**
         * Filter by rating
         */
        filterByRating(minRating) {
            // Implementar filtro por rating
        },

        /**
         * ==================================================================
         * RENDERING
         * ==================================================================
         */
        
        /**
         * Render brands
         */
        renderBrands() {
            if (!this.elements.brandsGrid) return;
            
            const html = this.state.filteredBrands.map(brand => 
                this.getBrandHTML(brand)
            ).join('');
            
            this.elements.brandsGrid.innerHTML = html;
            
            // Re-initialize components
            this.setupLazyLoad();
            this.updateFavoriteButtons();
            
            // Update count
            this.updateBrandCount();
        },

        /**
         * Get brand HTML
         */
        getBrandHTML(brand) {
            return `
                <div class="brand-item" data-brand-id="${brand.id}">
                    <img class="brand-logo" data-src="${brand.logo}" alt="${brand.name}">
                    <h3 class="brand-name">${brand.name}</h3>
                    <p class="brand-products">${brand.productCount} products</p>
                    <button class="brand-favorite" data-brand-id="${brand.id}">🤍</button>
                    <button class="brand-quick-view" data-brand-id="${brand.id}">Quick View</button>
                </div>
            `;
        },

        /**
         * Append brands
         */
        appendBrands(brands) {
            if (!this.elements.brandsGrid) return;
            
            const html = brands.map(brand => this.getBrandHTML(brand)).join('');
            this.elements.brandsGrid.insertAdjacentHTML('beforeend', html);
            
            // Re-initialize for new elements
            this.setupLazyLoad();
            this.updateFavoriteButtons();
        },

        /**
         * Update brand count
         */
        updateBrandCount() {
            const countElement = document.querySelector('.brands-count');
            if (countElement) {
                countElement.textContent = `${this.state.filteredBrands.length} brands`;
            }
        },

        /**
         * ==================================================================
         * UI HELPERS
         * ==================================================================
         */
        
        /**
         * Show loader
         */
        showLoader() {
            const loader = document.querySelector('.brands-loader');
            if (loader) {
                loader.classList.add('active');
            }
        },

        /**
         * Hide loader
         */
        hideLoader() {
            const loader = document.querySelector('.brands-loader');
            if (loader) {
                loader.classList.remove('active');
            }
        },

        /**
         * Show notification
         */
        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `brand-notification ${type}`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);
            
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        },

        /**
         * Highlight search terms
         */
        highlightSearchTerms(query) {
            // Implementar destaque de termos de busca
        },

        /**
         * Remove highlights
         */
        removeHighlights() {
            // Remover destaques
        },

        /**
         * Show autocomplete
         */
        showAutocomplete() {
            // Mostrar autocomplete
        },

        /**
         * Hide autocomplete
         */
        hideAutocomplete() {
            // Esconder autocomplete
        },

        /**
         * Start voice search
         */
        startVoiceSearch() {
            // Implementar busca por voz
        },

        /**
         * ==================================================================
         * EVENTOS
         * ==================================================================
         */
        
        /**
         * Bind eventos - Baseado na referência e expandido
         */
        bindEvents() {
            const self = this;
            
            // Scroll event - Como na referência
            window.addEventListener('scroll', function () {
                self.applyStickyBehavior(); // Como na referência
                self.checkScrollPosition();
                self.updateActiveLetterOnScroll();
            });
            
            // Resize event
            window.addEventListener('resize', this.debounce(() => {
                self.calculateDimensions();
                self.applyStickyBehavior();
                
                if (self.carousel) {
                    self.carousel.slidesPerView = self.getSlidesPerView();
                    self.updateCarousel();
                }
            }, 250));
            
            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                this.handleKeyboardNavigation(e);
            });
            
            // Click outside modals
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('brand-modal')) {
                    this.closeQuickView();
                }
            });
            
            // Back button for filters
            window.addEventListener('popstate', () => {
                this.restoreStateFromURL();
            });
        },

        /**
         * Check scroll position
         */
        checkScrollPosition() {
            this.dimensions.scrollPosition = window.scrollY;
            
            // Show/hide scroll to top button
            const scrollButton = this.elements.scrollToTop;
            if (scrollButton) {
                scrollButton.classList.toggle('visible', this.dimensions.scrollPosition > 500);
            }
        },

        /**
         * Update active letter on scroll
         */
        updateActiveLetterOnScroll() {
            if (!this.elements.brandsContainer) return;
            
            const sections = this.elements.brandsContainer.querySelectorAll('[id^="brands-"]');
            let currentSection = '';
            
            sections.forEach(section => {
                const rect = section.getBoundingClientRect();
                if (rect.top <= 100 && rect.bottom >= 100) {
                    currentSection = section.id.replace('brands-', '');
                }
            });
            
            if (currentSection) {
                this.setActiveLetter(currentSection);
            }
        },

        /**
         * Handle keyboard navigation
         */
        handleKeyboardNavigation(e) {
            // ESC to close modals
            if (e.key === 'Escape') {
                this.closeQuickView();
            }
            
            // Arrow keys for carousel
            if (this.state.viewMode === 'carousel') {
                if (e.key === 'ArrowLeft') {
                    this.carouselPrev();
                } else if (e.key === 'ArrowRight') {
                    this.carouselNext();
                }
            }
        },

        /**
         * Restore state from URL
         */
        restoreStateFromURL() {
            const params = new URLSearchParams(window.location.search);
            const filter = params.get('filter');
            const search = params.get('search');
            const sort = params.get('sort');
            
            if (filter) this.applyFilter(filter);
            if (search) this.searchBrands(search);
            if (sort) this.sortBrands(sort);
        },

        /**
         * ==================================================================
         * ANALYTICS
         * ==================================================================
         */
        
        /**
         * Track search
         */
        trackSearch(query) {
            // Google Analytics or other tracking
            if (typeof ga !== 'undefined') {
                ga('send', 'event', 'Brands', 'search', query);
            }
        },

        /**
         * Track filter
         */
        trackFilter(filter) {
            if (typeof ga !== 'undefined') {
                ga('send', 'event', 'Brands', 'filter', filter);
            }
        },

        /**
         * ==================================================================
         * UTILITIES
         * ==================================================================
         */
        
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
         * Trigger custom event
         */
        triggerEvent(eventName, detail = {}) {
            const event = new CustomEvent(eventName, { detail });
            document.dispatchEvent(event);
            this.log(`Event triggered: ${eventName}`, detail);
        }
    };

    /**
     * ==================================================================
     * INICIALIZAÇÃO
     * ==================================================================
     */
    
    // Inicializar quando o script carregar
    NosfirBrands.init();
    
    // Expor globalmente para debugging e extensibilidade
    window.NosfirBrands = NosfirBrands;

})();