/**
 * PERFORMANCE & UX OPTIMIZER - ELIXIR DU TEMPS
 * Optimisations avancées pour une expérience utilisateur exceptionnelle
 */

class PerformanceOptimizer {
    constructor() {
        this.init();
        this.setupLazyLoading();
        this.setupImageOptimization();
        this.setupCriticalResourceHints();
        this.setupServiceWorker();
        this.monitorPerformance();
    }

    init() {
        // Configuration du prefetching intelligent
        this.prefetchQueue = new Set();
        this.isOnline = navigator.onLine;
        this.connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        
        // Surveillance de la connectivité
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.processPrefetchQueue();
        });
        
        window.addEventListener('offline', () => {
            this.isOnline = false;
        });
    }

    setupLazyLoading() {
        // Lazy loading avancé avec priorités
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadImage(entry.target);
                    imageObserver.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: '100px 0px', // Charger 100px avant d'être visible
            threshold: 0.01
        });

        // Observer toutes les images lazy
        document.querySelectorAll('img[data-src], img[loading="lazy"]').forEach(img => {
            imageObserver.observe(img);
        });

        // Lazy loading pour les iframes (vidéos, cartes, etc.)
        const iframeObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const iframe = entry.target;
                    if (iframe.dataset.src) {
                        iframe.src = iframe.dataset.src;
                        iframe.removeAttribute('data-src');
                    }
                    iframeObserver.unobserve(iframe);
                }
            });
        }, { rootMargin: '50px' });

        document.querySelectorAll('iframe[data-src]').forEach(iframe => {
            iframeObserver.observe(iframe);
        });
    }

    loadImage(img) {
        return new Promise((resolve, reject) => {
            const imageLoader = new Image();
            
            imageLoader.onload = () => {
                // Transition fluide
                img.style.opacity = '0';
                img.style.transition = 'opacity 0.3s ease';
                
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                }
                
                if (img.dataset.srcset) {
                    img.srcset = img.dataset.srcset;
                    img.removeAttribute('data-srcset');
                }
                
                // Fade in
                requestAnimationFrame(() => {
                    img.style.opacity = '1';
                });
                
                img.classList.remove('loading');
                img.classList.add('loaded');
                resolve();
            };
            
            imageLoader.onerror = reject;
            imageLoader.src = img.dataset.src || img.src;
        });
    }

    setupImageOptimization() {
        // Détection de support WebP/AVIF
        this.supportedFormats = {
            webp: false,
            avif: false
        };
        
        this.detectImageFormats().then(() => {
            this.optimizeImageSources();
        });
    }

    async detectImageFormats() {
        // Test WebP
        const webpTest = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
        try {
            const webpImg = new Image();
            await new Promise((resolve, reject) => {
                webpImg.onload = () => {
                    this.supportedFormats.webp = webpImg.width === 2;
                    resolve();
                };
                webpImg.onerror = reject;
                webpImg.src = webpTest;
            });
        } catch (e) {
            this.supportedFormats.webp = false;
        }

        // Test AVIF
        const avifTest = 'data:image/avif;base64,AAAAIGZ0eXBhdmlmAAAAAGF2aWZtaWYxbWlhZk1BMUIAAADybWV0YQAAAAAAAAAoaGRscgAAAAAAAAAAcGljdAAAAAAAAAAAAAAAAGxpYmF2aWYAAAAADnBpdG0AAAAAAAEAAAAeaWxvYwAAAABEAAABAAEAAAABAAABGgAAAB0AAAAoaWluZgAAAAAAAQAAABppbmZlAgAAAAABAABhdjAxQ29sb3IAAAAAamlwcnAAAABLaXBjbwAAABRpc3BlAAAAAAAAAAIAAAACAAAAEHBpeGkAAAAAAwgICAAAAAxhdjFDgQ0MAAAAABNjb2xybmNseAACAAIAAYAAAAAXaXBtYQAAAAAAAAABAAEEAQKDBAAAACVtZGF0EgAKCBgABogQEAwgMg8f8D///8WfhwB8+ErK42A=';
        try {
            const avifImg = new Image();
            await new Promise((resolve, reject) => {
                avifImg.onload = () => {
                    this.supportedFormats.avif = avifImg.width === 2;
                    resolve();
                };
                avifImg.onerror = reject;
                avifImg.src = avifTest;
            });
        } catch (e) {
            this.supportedFormats.avif = false;
        }
    }

    optimizeImageSources() {
        document.querySelectorAll('img[data-src], picture source').forEach(element => {
            if (element.tagName === 'IMG') {
                this.optimizeImageSource(element);
            } else if (element.tagName === 'SOURCE') {
                this.optimizeSourceElement(element);
            }
        });
    }

    optimizeImageSource(img) {
        const originalSrc = img.dataset.src || img.src;
        if (!originalSrc) return;

        // Générer les variantes optimisées
        const optimizedSrc = this.generateOptimizedUrl(originalSrc);
        if (optimizedSrc !== originalSrc) {
            img.dataset.src = optimizedSrc;
        }
    }

    generateOptimizedUrl(url) {
        if (!url.includes('/uploads/') && !url.includes('/assets/img/')) return url;

        const urlParts = url.split('.');
        const extension = urlParts.pop();
        const baseName = urlParts.join('.');

        // Choisir le meilleur format selon le support
        if (this.supportedFormats.avif) {
            return `${baseName}.avif`;
        } else if (this.supportedFormats.webp) {
            return `${baseName}.webp`;
        }

        return url;
    }

    setupCriticalResourceHints() {
        // Preload des ressources critiques
        this.preloadCriticalResources();
        
        // Prefetch intelligent basé sur les interactions utilisateur
        this.setupIntelligentPrefetch();
        
        // DNS prefetch pour les domaines externes
        this.setupDNSPrefetch();
    }

    preloadCriticalResources() {
        const criticalResources = [
            { href: '/public/assets/css/design-system.css', as: 'style' },
            { href: '/public/assets/css/navigation.css', as: 'style' },
            { href: '/public/assets/js/navigation.js', as: 'script' },
            { href: '/public/assets/fonts/playfair-display-v30-latin-regular.woff2', as: 'font', type: 'font/woff2', crossorigin: 'anonymous' }
        ];

        criticalResources.forEach(resource => {
            if (!document.querySelector(`link[href="${resource.href}"]`)) {
                const link = document.createElement('link');
                link.rel = 'preload';
                Object.assign(link, resource);
                document.head.appendChild(link);
            }
        });
    }

    setupIntelligentPrefetch() {
        // Prefetch au survol (avec délai pour éviter les survols accidentels)
        let hoverTimeout;
        document.addEventListener('mouseover', (e) => {
            const link = e.target.closest('a[href]');
            if (link && this.shouldPrefetch(link.href)) {
                hoverTimeout = setTimeout(() => {
                    this.prefetchResource(link.href);
                }, 100);
            }
        });

        document.addEventListener('mouseout', () => {
            clearTimeout(hoverTimeout);
        });

        // Prefetch basé sur la visibilité des liens
        const linkObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const link = entry.target;
                    if (this.shouldPrefetch(link.href)) {
                        // Délai pour éviter le prefetch immédiat
                        setTimeout(() => {
                            this.prefetchResource(link.href);
                        }, 1000);
                    }
                    linkObserver.unobserve(link);
                }
            });
        }, { threshold: 0.5 });

        document.querySelectorAll('a[href^="/"], a[href^="./"]').forEach(link => {
            linkObserver.observe(link);
        });
    }

    shouldPrefetch(url) {
        // Conditions pour décider si prefetch
        if (!this.isOnline || !url) return false;
        if (this.prefetchQueue.has(url)) return false;
        if (url.includes('#') || url.includes('javascript:')) return false;
        
        // Éviter le prefetch sur connexions lentes
        if (this.connection) {
            if (this.connection.saveData || 
                this.connection.effectiveType === 'slow-2g' || 
                this.connection.effectiveType === '2g') {
                return false;
            }
        }

        return true;
    }

    prefetchResource(url) {
        if (this.prefetchQueue.has(url)) return;
        
        this.prefetchQueue.add(url);
        
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = url;
        link.onload = () => console.log(`Prefetched: ${url}`);
        link.onerror = () => {
            console.warn(`Prefetch failed: ${url}`);
            this.prefetchQueue.delete(url);
        };
        
        document.head.appendChild(link);
    }

    processPrefetchQueue() {
        // Traiter la queue quand la connexion revient
        this.prefetchQueue.forEach(url => {
            this.prefetchResource(url);
        });
    }

    setupDNSPrefetch() {
        const externalDomains = [
            'fonts.googleapis.com',
            'fonts.gstatic.com',
            'cdnjs.cloudflare.com',
            'cdn.jsdelivr.net'
        ];

        externalDomains.forEach(domain => {
            const link = document.createElement('link');
            link.rel = 'dns-prefetch';
            link.href = `//${domain}`;
            document.head.appendChild(link);
        });
    }

    setupServiceWorker() {
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('SW registered: ', registration);
                        
                        // Vérifier les mises à jour
                        this.checkForUpdates(registration);
                    })
                    .catch(registrationError => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    }

    checkForUpdates(registration) {
        registration.addEventListener('updatefound', () => {
            const newWorker = registration.installing;
            newWorker.addEventListener('statechange', () => {
                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                    // Nouvelle version disponible
                    this.showUpdateNotification();
                }
            });
        });
    }

    showUpdateNotification() {
        if (window.toast) {
            const toast = window.toast.show(
                'Une nouvelle version est disponible. Actualiser la page ?',
                'info',
                0 // Pas de fermeture automatique
            );
            
            // Ajouter un bouton d'action
            const button = document.createElement('button');
            button.textContent = 'Actualiser';
            button.style.cssText = `
                background: #d4af37;
                color: white;
                border: none;
                padding: 6px 12px;
                border-radius: 4px;
                margin-left: 12px;
                cursor: pointer;
            `;
            button.onclick = () => window.location.reload();
            
            toast.querySelector('div').appendChild(button);
        }
    }

    monitorPerformance() {
        // Core Web Vitals monitoring
        this.observeWebVitals();
        
        // Resource timing
        this.analyzeResourceTiming();
        
        // Long tasks monitoring
        this.observeLongTasks();
    }

    observeWebVitals() {
        // Largest Contentful Paint
        new PerformanceObserver((entryList) => {
            const entries = entryList.getEntries();
            const lastEntry = entries[entries.length - 1];
            console.log('LCP:', lastEntry.startTime);
            
            if (lastEntry.startTime > 2500) {
                console.warn('LCP is slow:', lastEntry.startTime);
            }
        }).observe({ entryTypes: ['largest-contentful-paint'] });

        // First Input Delay
        new PerformanceObserver((entryList) => {
            entryList.getEntries().forEach(entry => {
                console.log('FID:', entry.processingStart - entry.startTime);
                
                if (entry.processingStart - entry.startTime > 100) {
                    console.warn('FID is slow:', entry.processingStart - entry.startTime);
                }
            });
        }).observe({ entryTypes: ['first-input'] });

        // Cumulative Layout Shift
        let clsValue = 0;
        new PerformanceObserver((entryList) => {
            entryList.getEntries().forEach(entry => {
                if (!entry.hadRecentInput) {
                    clsValue += entry.value;
                }
            });
            console.log('CLS:', clsValue);
            
            if (clsValue > 0.1) {
                console.warn('CLS is high:', clsValue);
            }
        }).observe({ entryTypes: ['layout-shift'] });
    }

    analyzeResourceTiming() {
        window.addEventListener('load', () => {
            const resources = performance.getEntriesByType('resource');
            
            // Analyser les ressources lentes
            const slowResources = resources.filter(resource => 
                resource.duration > 1000
            );
            
            if (slowResources.length > 0) {
                console.warn('Slow resources detected:', slowResources);
            }
            
            // Analyser la taille des ressources
            const largeResources = resources.filter(resource => 
                resource.transferSize > 1000000 // > 1MB
            );
            
            if (largeResources.length > 0) {
                console.warn('Large resources detected:', largeResources);
            }
        });
    }

    observeLongTasks() {
        if ('PerformanceObserver' in window) {
            const observer = new PerformanceObserver((entryList) => {
                entryList.getEntries().forEach(entry => {
                    console.warn('Long task detected:', {
                        duration: entry.duration,
                        startTime: entry.startTime
                    });
                });
            });
            
            try {
                observer.observe({ entryTypes: ['longtask'] });
            } catch (e) {
                // Browser doesn't support longtask
            }
        }
    }
}

/**
 * GESTIONNAIRE DE CACHE INTELLIGENT
 */
class CacheManager {
    constructor() {
        this.dbName = 'ElixirCache';
        this.version = 1;
        this.db = null;
        this.init();
    }

    async init() {
        if ('indexedDB' in window) {
            this.db = await this.openDB();
        }
    }

    openDB() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.version);
            
            request.onerror = () => reject(request.error);
            request.onsuccess = () => resolve(request.result);
            
            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                
                // Store pour les données des produits
                if (!db.objectStoreNames.contains('products')) {
                    const productsStore = db.createObjectStore('products', { keyPath: 'id' });
                    productsStore.createIndex('category', 'category', { unique: false });
                    productsStore.createIndex('lastUpdated', 'lastUpdated', { unique: false });
                }
                
                // Store pour les images
                if (!db.objectStoreNames.contains('images')) {
                    db.createObjectStore('images', { keyPath: 'url' });
                }
            };
        });
    }

    async cacheProduct(product) {
        if (!this.db) return;
        
        const transaction = this.db.transaction(['products'], 'readwrite');
        const store = transaction.objectStore('products');
        
        product.lastUpdated = Date.now();
        await store.put(product);
    }

    async getProduct(id) {
        if (!this.db) return null;
        
        const transaction = this.db.transaction(['products'], 'readonly');
        const store = transaction.objectStore('products');
        
        return new Promise((resolve) => {
            const request = store.get(id);
            request.onsuccess = () => {
                const product = request.result;
                // Vérifier si les données ne sont pas trop anciennes (24h)
                if (product && Date.now() - product.lastUpdated < 24 * 60 * 60 * 1000) {
                    resolve(product);
                } else {
                    resolve(null);
                }
            };
            request.onerror = () => resolve(null);
        });
    }

    async clearOldCache() {
        if (!this.db) return;
        
        const transaction = this.db.transaction(['products'], 'readwrite');
        const store = transaction.objectStore('products');
        const index = store.index('lastUpdated');
        
        const oneWeekAgo = Date.now() - (7 * 24 * 60 * 60 * 1000);
        const range = IDBKeyRange.upperBound(oneWeekAgo);
        
        const request = index.openCursor(range);
        request.onsuccess = (event) => {
            const cursor = event.target.result;
            if (cursor) {
                cursor.delete();
                cursor.continue();
            }
        };
    }
}

/**
 * INITIALISATION
 */
document.addEventListener('DOMContentLoaded', () => {
    // Initialiser l'optimiseur de performance
    window.performanceOptimizer = new PerformanceOptimizer();
    window.cacheManager = new CacheManager();
    
    // Nettoyer le cache ancien périodiquement
    setInterval(() => {
        window.cacheManager.clearOldCache();
    }, 60 * 60 * 1000); // Toutes les heures
    
    console.log('🚀 Performance Optimizer initialized');
});

// Export pour modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { PerformanceOptimizer, CacheManager };
}
