/**
 * SERVICE WORKER - ELIXIR DU TEMPS
 * Gestion du cache et optimisation des performances
 */

const CACHE_NAME = 'elixir-du-temps-v1.0.0';
const STATIC_CACHE = 'elixir-static-v1.0.0';
const DYNAMIC_CACHE = 'elixir-dynamic-v1.0.0';
const IMAGE_CACHE = 'elixir-images-v1.0.0';

// Ressources à mettre en cache immédiatement
const STATIC_ASSETS = [
    '/public/',
    '/public/pages/Accueil.php',
    '/public/assets/css/design-system.css',
    '/public/assets/css/navigation.css',
    '/public/assets/css/products.css',
    '/public/assets/js/navigation.js',
    '/public/assets/js/performance.js',
    '/public/assets/img/layout/logo.png',
    '/public/assets/img/layout/icon2.png',
    'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Source+Sans+Pro:wght@300;400;500;600;700&display=swap',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'
];

// Stratégies de cache
const CACHE_STRATEGIES = {
    // Cache First - pour les assets statiques
    CACHE_FIRST: 'cache-first',
    // Network First - pour les données dynamiques
    NETWORK_FIRST: 'network-first',
    // Stale While Revalidate - pour les images et assets
    STALE_WHILE_REVALIDATE: 'stale-while-revalidate'
};

/**
 * Installation du Service Worker
 */
self.addEventListener('install', (event) => {
    console.log('🔧 Service Worker: Installation');
    
    event.waitUntil(
        Promise.all([
            // Cache des ressources statiques
            caches.open(STATIC_CACHE).then((cache) => {
                console.log('📦 Mise en cache des ressources statiques');
                return cache.addAll(STATIC_ASSETS.map(url => new Request(url, {
                    cache: 'reload' // Force le rechargement depuis le réseau
                })));
            }),
            
            // Initialisation des autres caches
            caches.open(DYNAMIC_CACHE),
            caches.open(IMAGE_CACHE)
        ]).then(() => {
            console.log('✅ Service Worker installé avec succès');
            return self.skipWaiting(); // Active immédiatement
        }).catch((error) => {
            console.error('❌ Erreur installation Service Worker:', error);
        })
    );
});

/**
 * Activation du Service Worker
 */
self.addEventListener('activate', (event) => {
    console.log('🚀 Service Worker: Activation');
    
    event.waitUntil(
        Promise.all([
            // Nettoyage des anciens caches
            cleanupOldCaches(),
            
            // Prise de contrôle immédiate
            self.clients.claim()
        ]).then(() => {
            console.log('✅ Service Worker activé');
        })
    );
});

/**
 * Interception des requêtes réseau
 */
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);
    
    // Ignorer les requêtes non-HTTP
    if (!request.url.startsWith('http')) {
        return;
    }
    
    // Stratégie basée sur le type de ressource
    if (isStaticAsset(url)) {
        event.respondWith(handleStaticAsset(request));
    } else if (isImage(url)) {
        event.respondWith(handleImage(request));
    } else if (isAPIRequest(url)) {
        event.respondWith(handleAPIRequest(request));
    } else if (isPageRequest(request)) {
        event.respondWith(handlePageRequest(request));
    } else {
        event.respondWith(handleGenericRequest(request));
    }
});

/**
 * Gestion des messages depuis l'application
 */
self.addEventListener('message', (event) => {
    const { type, payload } = event.data;
    
    switch (type) {
        case 'SKIP_WAITING':
            self.skipWaiting();
            break;
            
        case 'CACHE_PRODUCT':
            cacheProduct(payload);
            break;
            
        case 'CLEAR_CACHE':
            clearSpecificCache(payload.cacheName);
            break;
            
        case 'GET_CACHE_STATUS':
            getCacheStatus().then(status => {
                event.ports[0].postMessage(status);
            });
            break;
    }
});

/**
 * Gestion en arrière-plan des tâches de synchronisation
 */
self.addEventListener('sync', (event) => {
    console.log('🔄 Synchronisation en arrière-plan:', event.tag);
    
    switch (event.tag) {
        case 'background-sync-cart':
            event.waitUntil(syncCartData());
            break;
            
        case 'background-sync-wishlist':
            event.waitUntil(syncWishlistData());
            break;
    }
});

/**
 * Notifications Push (pour les futures fonctionnalités)
 */
self.addEventListener('push', (event) => {
    if (event.data) {
        const data = event.data.json();
        const options = {
            body: data.body,
            icon: '/public/assets/img/layout/icon2.png',
            badge: '/public/assets/img/layout/icon2.png',
            vibrate: [100, 50, 100],
            data: data.data || {},
            actions: data.actions || []
        };
        
        event.waitUntil(
            self.registration.showNotification(data.title, options)
        );
    }
});

/**
 * FONCTIONS UTILITAIRES
 */

function isStaticAsset(url) {
    return url.pathname.includes('/assets/') || 
           url.hostname === 'fonts.googleapis.com' ||
           url.hostname === 'fonts.gstatic.com' ||
           url.hostname === 'cdnjs.cloudflare.com';
}

function isImage(url) {
    return url.pathname.match(/\.(jpg|jpeg|png|gif|webp|svg|avif)$/i) ||
           url.pathname.includes('/uploads/');
}

function isAPIRequest(url) {
    return url.pathname.includes('/api/');
}

function isPageRequest(request) {
    return request.method === 'GET' && 
           request.headers.get('accept').includes('text/html');
}

/**
 * STRATÉGIES DE CACHE
 */

async function handleStaticAsset(request) {
    try {
        // Cache First pour les assets statiques
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        console.warn('Erreur chargement asset statique:', error);
        return new Response('Asset non disponible', { status: 404 });
    }
}

async function handleImage(request) {
    try {
        // Stale While Revalidate pour les images
        const cachedResponse = await caches.match(request);
        
        const fetchPromise = fetch(request).then(response => {
            if (response.ok) {
                const cache = caches.open(IMAGE_CACHE);
                cache.then(c => c.put(request, response.clone()));
            }
            return response;
        }).catch(() => null);
        
        return cachedResponse || await fetchPromise || await getFallbackImage();
        
    } catch (error) {
        console.warn('Erreur chargement image:', error);
        return getFallbackImage();
    }
}

async function handleAPIRequest(request) {
    try {
        // Network First pour les APIs avec cache de secours
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            // Cache seulement les requêtes GET
            if (request.method === 'GET') {
                const cache = await caches.open(DYNAMIC_CACHE);
                cache.put(request, networkResponse.clone());
            }
        }
        
        return networkResponse;
        
    } catch (error) {
        console.warn('Erreur API, tentative cache:', error);
        
        if (request.method === 'GET') {
            const cachedResponse = await caches.match(request);
            if (cachedResponse) {
                return cachedResponse;
            }
        }
        
        return new Response(
            JSON.stringify({ 
                success: false, 
                error: 'Service temporairement indisponible',
                cached: false 
            }), 
            { 
                status: 503, 
                headers: { 'Content-Type': 'application/json' } 
            }
        );
    }
}

async function handlePageRequest(request) {
    try {
        // Network First avec cache de secours
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
        
    } catch (error) {
        console.warn('Erreur chargement page, tentative cache:', error);
        
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Page offline de secours
        return getOfflinePage();
    }
}

async function handleGenericRequest(request) {
    try {
        return await fetch(request);
    } catch (error) {
        const cachedResponse = await caches.match(request);
        return cachedResponse || new Response('Non disponible hors ligne', { status: 404 });
    }
}

/**
 * GESTION DU CACHE
 */

async function cleanupOldCaches() {
    const cacheNames = await caches.keys();
    const validCaches = [CACHE_NAME, STATIC_CACHE, DYNAMIC_CACHE, IMAGE_CACHE];
    
    return Promise.all(
        cacheNames
            .filter(cacheName => !validCaches.includes(cacheName))
            .map(cacheName => {
                console.log('🗑️ Suppression ancien cache:', cacheName);
                return caches.delete(cacheName);
            })
    );
}

async function clearSpecificCache(cacheName) {
    if (await caches.has(cacheName)) {
        await caches.delete(cacheName);
        console.log('🗑️ Cache supprimé:', cacheName);
    }
}

async function getCacheStatus() {
    const cacheNames = await caches.keys();
    const status = {};
    
    for (const cacheName of cacheNames) {
        const cache = await caches.open(cacheName);
        const keys = await cache.keys();
        status[cacheName] = {
            count: keys.length,
            size: await getCacheSize(cache)
        };
    }
    
    return status;
}

async function getCacheSize(cache) {
    const keys = await cache.keys();
    let totalSize = 0;
    
    for (const key of keys) {
        try {
            const response = await cache.match(key);
            if (response) {
                const blob = await response.blob();
                totalSize += blob.size;
            }
        } catch (error) {
            console.warn('Erreur calcul taille cache:', error);
        }
    }
    
    return totalSize;
}

/**
 * SYNCHRONISATION DES DONNÉES
 */

async function syncCartData() {
    try {
        // Synchroniser les données du panier en arrière-plan
        const response = await fetch('/php/api/cart/sync.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            console.log('✅ Panier synchronisé');
        }
    } catch (error) {
        console.warn('Erreur sync panier:', error);
    }
}

async function syncWishlistData() {
    try {
        // Synchroniser la liste de souhaits
        const response = await fetch('/php/api/wishlist/sync.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            console.log('✅ Liste de souhaits synchronisée');
        }
    } catch (error) {
        console.warn('Erreur sync wishlist:', error);
    }
}

async function cacheProduct(productData) {
    try {
        const cache = await caches.open(DYNAMIC_CACHE);
        const response = new Response(JSON.stringify(productData), {
            headers: { 'Content-Type': 'application/json' }
        });
        
        await cache.put(`/product/${productData.id}`, response);
        console.log('📦 Produit mis en cache:', productData.id);
    } catch (error) {
        console.warn('Erreur cache produit:', error);
    }
}

/**
 * RESSOURCES DE SECOURS
 */

async function getFallbackImage() {
    try {
        const cache = await caches.open(STATIC_CACHE);
        return await cache.match('/public/assets/img/layout/default-watch.jpg') ||
               new Response('', { status: 404 });
    } catch (error) {
        return new Response('', { status: 404 });
    }
}

async function getOfflinePage() {
    const offlineHTML = `
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Mode hors ligne - Elixir du Temps</title>
            <style>
                body {
                    font-family: 'Source Sans Pro', sans-serif;
                    text-align: center;
                    padding: 2rem;
                    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0;
                }
                .offline-container {
                    background: white;
                    border-radius: 16px;
                    padding: 3rem;
                    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
                    max-width: 500px;
                }
                .offline-icon {
                    font-size: 4rem;
                    color: #d4af37;
                    margin-bottom: 1rem;
                }
                h1 {
                    font-family: 'Playfair Display', serif;
                    color: #2c3e50;
                    margin-bottom: 1rem;
                }
                p {
                    color: #6c757d;
                    line-height: 1.6;
                    margin-bottom: 2rem;
                }
                .retry-btn {
                    background: linear-gradient(135deg, #d4af37, #b8941f);
                    color: white;
                    border: none;
                    padding: 12px 24px;
                    border-radius: 8px;
                    cursor: pointer;
                    font-weight: 500;
                    transition: transform 0.2s ease;
                }
                .retry-btn:hover {
                    transform: translateY(-2px);
                }
            </style>
        </head>
        <body>
            <div class="offline-container">
                <div class="offline-icon">🕰️</div>
                <h1>Mode hors ligne</h1>
                <p>Vous semblez être hors ligne. Vérifiez votre connexion Internet et réessayez.</p>
                <button class="retry-btn" onclick="window.location.reload()">
                    Réessayer
                </button>
            </div>
        </body>
        </html>
    `;
    
    return new Response(offlineHTML, {
        headers: { 'Content-Type': 'text/html' }
    });
}

console.log('🕰️ Elixir du Temps Service Worker chargé');
