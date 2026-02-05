/**
 * Header Search Toggle
 *
 * @package Nosfir
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        initHeaderSearch();
    });

    function initHeaderSearch() {
        const searchContainer = document.querySelector('.header-search');
        const searchToggle = document.querySelector('.search-toggle');
        const searchDropdown = document.querySelector('.search-dropdown');
        const searchInput = document.querySelector('.search-dropdown .search-field');

        if (!searchToggle || !searchDropdown) {
            return;
        }

        // Toggle search on button click
        searchToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSearch();
        });

        // Close on click outside
        document.addEventListener('click', function(e) {
            if (searchContainer && !searchContainer.contains(e.target)) {
                closeSearch();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSearch();
            }
        });

        // Focus input when opened
        function toggleSearch() {
            const isOpen = searchContainer.classList.contains('is-open');
            
            if (isOpen) {
                closeSearch();
            } else {
                openSearch();
            }
        }

        function openSearch() {
            searchContainer.classList.add('is-open');
            searchToggle.classList.add('is-active');
            searchToggle.setAttribute('aria-expanded', 'true');
            searchDropdown.setAttribute('aria-hidden', 'false');
            document.body.classList.add('search-open');
            
            // Focus input after animation
            setTimeout(function() {
                if (searchInput) {
                    searchInput.focus();
                }
            }, 300);
        }

        function closeSearch() {
            searchContainer.classList.remove('is-open');
            searchToggle.classList.remove('is-active');
            searchToggle.setAttribute('aria-expanded', 'false');
            searchDropdown.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('search-open');
            
            // Clear input
            if (searchInput) {
                searchInput.blur();
            }
        }

        // Prevent form submit if empty
        const searchForm = searchDropdown.querySelector('.search-form');
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                if (searchInput && searchInput.value.trim() === '') {
                    e.preventDefault();
                    searchInput.focus();
                }
            });
        }
    }

})();