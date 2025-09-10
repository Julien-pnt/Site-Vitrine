/**
 * ELIXIR DU TEMPS - NAVIGATION & UX MODERNE
 * Système JavaScript pour une expérience utilisateur exceptionnelle
 */

class ElixirNavigation {
    constructor() {
        this.init();
        this.bindEvents();
        this.initIntersectionObserver();
        this.initMobileMenu();
        this.initSearch();
        this.initScrollEffects();
        this.initAccessibility();
    }

    init() {
        // Configuration des éléments principaux
        this.header = document.querySelector('.main-header');
        this.mobileToggle = document.querySelector('.mobile-menu-toggle');
        this.mobileMenu = document.querySelector('.mobile-menu');
        this.mobileOverlay = document.querySelector('.mobile-overlay');
        this.searchInput = document.querySelector('.search-input');
        this.searchResults = document.querySelector('.search-results');
        this.navLinks = document.querySelectorAll('.nav-link');
        
        // Variables d'état
        this.isMenuOpen = false;
        this.isSearching = false;
        this.lastScrollY = window.scrollY;
        this.scrollThreshold = 100;
        
        // Cache pour les performances
        this.debounceTimeout = null;
        this.scrollTimeout = null;
    }

    bindEvents() {
        // Événements de scroll optimisés
        window.addEventListener('scroll', this.throttle(this.handleScroll.bind(this), 16));
        
        // Événements de redimensionnement
        window.addEventListener('resize', this.debounce(this.handleResize.bind(this), 250));
        
        // Événements de navigation
        this.navLinks.forEach(link => {
            link.addEventListener('click', this.handleNavClick.bind(this));
        });
        
        // Gestion des touches clavier
        document.addEventListener('keydown', this.handleKeydown.bind(this));
        
        // Fermeture des menus en cliquant à l'extérieur
        document.addEventListener('click', this.handleOutsideClick.bind(this));
    }

    initMobileMenu() {
        if (!this.mobileToggle || !this.mobileMenu) return;

        this.mobileToggle.addEventListener('click', (e) => {
            e.preventDefault();
            this.toggleMobileMenu();
        });

        if (this.mobileOverlay) {
            this.mobileOverlay.addEventListener('click', () => {
                this.closeMobileMenu();
            });
        }

        // Animation d'apparition progressive des éléments du menu
        const mobileNavItems = this.mobileMenu?.querySelectorAll('.nav-item');
        mobileNavItems?.forEach((item, index) => {
            item.style.animationDelay = `${0.1 + (index * 0.05)}s`;
        });
    }

    toggleMobileMenu() {
        this.isMenuOpen = !this.isMenuOpen;
        
        this.mobileToggle?.classList.toggle('active', this.isMenuOpen);
        this.mobileMenu?.classList.toggle('active', this.isMenuOpen);
        this.mobileOverlay?.classList.toggle('active', this.isMenuOpen);
        
        // Gestion du scroll du body
        document.body.style.overflow = this.isMenuOpen ? 'hidden' : '';
        
        // Accessibility
        this.mobileToggle?.setAttribute('aria-expanded', this.isMenuOpen.toString());
        this.mobileMenu?.setAttribute('aria-hidden', (!this.isMenuOpen).toString());
        
        // Focus management
        if (this.isMenuOpen) {
            const firstFocusable = this.mobileMenu?.querySelector('a, button');
            firstFocusable?.focus();
        }
    }

    closeMobileMenu() {
        if (!this.isMenuOpen) return;
        
        this.isMenuOpen = false;
        this.mobileToggle?.classList.remove('active');
        this.mobileMenu?.classList.remove('active');
        this.mobileOverlay?.classList.remove('active');
        document.body.style.overflow = '';
        
        this.mobileToggle?.setAttribute('aria-expanded', 'false');
        this.mobileMenu?.setAttribute('aria-hidden', 'true');
    }

    initSearch() {
        if (!this.searchInput) return;

        this.searchInput.addEventListener('input', this.debounce((e) => {
            const query = e.target.value.trim();
            if (query.length >= 2) {
                this.performSearch(query);
            } else {
                this.hideSearchResults();
            }
        }, 300));

        this.searchInput.addEventListener('focus', () => {
            this.searchInput.parentElement?.classList.add('focused');
        });

        this.searchInput.addEventListener('blur', () => {
            // Délai pour permettre les clics sur les résultats
            setTimeout(() => {
                this.searchInput.parentElement?.classList.remove('focused');
                this.hideSearchResults();
            }, 200);
        });
    }

    async performSearch(query) {
        if (this.isSearching) return;
        
        this.isSearching = true;
        this.showSearchLoading();
        
        try {
            const response = await fetch('/php/api/search.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ query, limit: 8 })
            });
            
            if (!response.ok) throw new Error('Erreur de recherche');
            
            const data = await response.json();
            this.displaySearchResults(data.results || []);
        } catch (error) {
            console.error('Erreur de recherche:', error);
            this.showSearchError();
        } finally {
            this.isSearching = false;
        }
    }

    showSearchLoading() {
        if (!this.searchResults) return;
        
        this.searchResults.innerHTML = `
            <div class="search-loading">
                <div class="shimmer" style="height: 60px; margin: 16px;"></div>
                <div class="shimmer" style="height: 60px; margin: 16px;"></div>
                <div class="shimmer" style="height: 60px; margin: 16px;"></div>
            </div>
        `;
        this.searchResults.classList.add('show');
    }

    displaySearchResults(results) {
        if (!this.searchResults) return;
        
        if (results.length === 0) {
            this.searchResults.innerHTML = `
                <div class="search-no-results">
                    <p>Aucun résultat trouvé</p>
                </div>
            `;
        } else {
            this.searchResults.innerHTML = results.map(item => `
                <a href="${item.url}" class="search-result-item">
                    <img src="${item.image}" alt="${item.title}" class="search-result-image" loading="lazy">
                    <div class="search-result-content">
                        <h6>${this.highlightQuery(item.title)}</h6>
                        <p>${item.category} - ${item.price}</p>
                    </div>
                </a>
            `).join('');
        }
        
        this.searchResults.classList.add('show');
    }

    showSearchError() {
        if (!this.searchResults) return;
        
        this.searchResults.innerHTML = `
            <div class="search-error">
                <p>Erreur lors de la recherche. Veuillez réessayer.</p>
            </div>
        `;
        this.searchResults.classList.add('show');
    }

    hideSearchResults() {
        this.searchResults?.classList.remove('show');
    }

    highlightQuery(text) {
        const query = this.searchInput?.value.trim();
        if (!query) return text;
        
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }

    initScrollEffects() {
        if (!this.header) return;

        // Parallax léger pour le header
        this.parallaxIntensity = 0.5;
        
        // Observation du scroll pour les animations
        this.observeScrollElements();
    }

    handleScroll() {
        const currentScrollY = window.scrollY;
        const scrollDifference = currentScrollY - this.lastScrollY;
        
        // Header scroll effects
        if (this.header) {
            if (currentScrollY > this.scrollThreshold) {
                this.header.classList.add('scrolled');
                
                // Auto-hide navigation on scroll down (mobile)
                if (window.innerWidth <= 768) {
                    if (scrollDifference > 5 && currentScrollY > 200) {
                        this.header.style.transform = 'translateY(-100%)';
                    } else if (scrollDifference < -5) {
                        this.header.style.transform = 'translateY(0)';
                    }
                }
            } else {
                this.header.classList.remove('scrolled');
                this.header.style.transform = 'translateY(0)';
            }
            
            // Parallax effect
            const parallaxOffset = currentScrollY * this.parallaxIntensity;
            this.header.style.setProperty('--scroll-offset', `${parallaxOffset}px`);
        }
        
        this.lastScrollY = currentScrollY;
    }

    observeScrollElements() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    // Ne plus observer cet élément après l'animation
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observer les éléments avec animation
        const animatedElements = document.querySelectorAll('.product-card, .card, .fade-in');
        animatedElements.forEach(el => observer.observe(el));
    }

    initIntersectionObserver() {
        // Lazy loading des images
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('loading');
                        imageObserver.unobserve(img);
                    }
                }
            });
        });

        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => imageObserver.observe(img));
    }

    initAccessibility() {
        // Skip links
        const skipLink = document.querySelector('.skip-link');
        if (skipLink) {
            skipLink.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(skipLink.getAttribute('href'));
                target?.focus();
                target?.scrollIntoView({ behavior: 'smooth' });
            });
        }

        // Amélioration du focus visible
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                document.body.classList.add('keyboard-navigation');
            }
        });

        document.addEventListener('mousedown', () => {
            document.body.classList.remove('keyboard-navigation');
        });
    }

    handleNavClick(e) {
        const link = e.currentTarget;
        const href = link.getAttribute('href');
        
        // Fermer le menu mobile si ouvert
        if (this.isMenuOpen) {
            this.closeMobileMenu();
        }
        
        // Smooth scroll pour les liens d'ancre
        if (href?.startsWith('#')) {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Mettre à jour l'URL
                history.pushState(null, null, href);
            }
        }
    }

    handleKeydown(e) {
        // Escape pour fermer les menus
        if (e.key === 'Escape') {
            if (this.isMenuOpen) {
                this.closeMobileMenu();
            }
            if (this.searchResults?.classList.contains('show')) {
                this.hideSearchResults();
                this.searchInput?.blur();
            }
        }
        
        // Navigation au clavier dans les résultats de recherche
        if (this.searchResults?.classList.contains('show')) {
            const results = this.searchResults.querySelectorAll('.search-result-item');
            const currentFocus = document.activeElement;
            const currentIndex = Array.from(results).indexOf(currentFocus);
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const nextIndex = currentIndex < results.length - 1 ? currentIndex + 1 : 0;
                results[nextIndex]?.focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                const prevIndex = currentIndex > 0 ? currentIndex - 1 : results.length - 1;
                results[prevIndex]?.focus();
            }
        }
    }

    handleOutsideClick(e) {
        // Fermer la recherche si clic à l'extérieur
        if (this.searchResults?.classList.contains('show')) {
            const searchContainer = this.searchInput?.closest('.search-bar');
            if (searchContainer && !searchContainer.contains(e.target)) {
                this.hideSearchResults();
            }
        }
        
        // Fermer les dropdowns
        const openDropdowns = document.querySelectorAll('.dropdown:hover');
        openDropdowns.forEach(dropdown => {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('hover');
            }
        });
    }

    handleResize() {
        // Fermer le menu mobile si on passe en desktop
        if (window.innerWidth > 1023 && this.isMenuOpen) {
            this.closeMobileMenu();
        }
        
        // Reset des styles de scroll mobile
        if (window.innerWidth > 768 && this.header) {
            this.header.style.transform = '';
        }
    }

    // Utilitaires de performance
    debounce(func, wait) {
        return (...args) => {
            clearTimeout(this.debounceTimeout);
            this.debounceTimeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    throttle(func, limit) {
        let inThrottle;
        return (...args) => {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
}

/**
 * SYSTÈME DE NOTIFICATIONS TOAST
 */
class ToastManager {
    constructor() {
        this.container = this.createContainer();
        this.toasts = [];
    }

    createContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 350px;
        `;
        document.body.appendChild(container);
        return container;
    }

    show(message, type = 'info', duration = 5000) {
        const toast = this.createToast(message, type);
        this.container.appendChild(toast);
        this.toasts.push(toast);

        // Animation d'entrée
        requestAnimationFrame(() => {
            toast.style.transform = 'translateX(0)';
            toast.style.opacity = '1';
        });

        // Fermeture automatique
        if (duration > 0) {
            setTimeout(() => this.remove(toast), duration);
        }

        return toast;
    }

    createToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.style.cssText = `
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            padding: 16px 20px;
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.3s ease;
            border-left: 4px solid ${this.getTypeColor(type)};
            position: relative;
            cursor: pointer;
        `;

        const icon = this.getTypeIcon(type);
        toast.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="color: ${this.getTypeColor(type)}; font-size: 18px;">${icon}</span>
                <span style="flex: 1; font-size: 14px; color: #2c3e50;">${message}</span>
                <button style="background: none; border: none; color: #6c757d; cursor: pointer; font-size: 18px;">&times;</button>
            </div>
        `;

        // Événements
        toast.addEventListener('click', () => this.remove(toast));
        toast.querySelector('button').addEventListener('click', (e) => {
            e.stopPropagation();
            this.remove(toast);
        });

        return toast;
    }

    remove(toast) {
        toast.style.transform = 'translateX(100%)';
        toast.style.opacity = '0';
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
            this.toasts = this.toasts.filter(t => t !== toast);
        }, 300);
    }

    getTypeColor(type) {
        const colors = {
            success: '#28a745',
            error: '#dc3545',
            warning: '#ffc107',
            info: '#17a2b8'
        };
        return colors[type] || colors.info;
    }

    getTypeIcon(type) {
        const icons = {
            success: '✓',
            error: '✗',
            warning: '⚠',
            info: 'ℹ'
        };
        return icons[type] || icons.info;
    }
}

/**
 * INITIALISATION GLOBALE
 */
document.addEventListener('DOMContentLoaded', () => {
    // Initialiser la navigation
    window.elixirNav = new ElixirNavigation();
    window.toast = new ToastManager();
    
    // Initialiser les modules métier si présents
    if (typeof ProductManager !== 'undefined') {
        window.productManager = new ProductManager();
    }
    
    if (typeof CartManager !== 'undefined') {
        window.cartManager = new CartManager();
    }
    
    // Gestion globale des erreurs
    window.addEventListener('error', (e) => {
        console.error('Erreur JavaScript:', e.error);
        // En production, envoyer à un service de monitoring
    });
    
    // Performance monitoring
    if ('performance' in window) {
        window.addEventListener('load', () => {
            const perfData = performance.getEntriesByType('navigation')[0];
            console.log('Page load time:', perfData.loadEventEnd - perfData.loadEventStart, 'ms');
        });
    }
    
    console.log('🕰️ Elixir du Temps - Navigation initialisée avec succès');
});

/**
 * EXPORT POUR MODULES
 */
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { ElixirNavigation, ToastManager };
}
