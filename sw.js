/**
 * Service Worker for Next Update Mobile App
 * Provides offline support and caching
 */

const CACHE_NAME = 'next-update-v1.0.0';
const STATIC_CACHE = 'next-update-static-v1.0.0';
const DYNAMIC_CACHE = 'next-update-dynamic-v1.0.0';
const IMAGE_CACHE = 'next-update-images-v1.0.0';

// Static assets to cache
const STATIC_ASSETS = [
    '/',
    '/public/assets/css/mobile-app.css',
    '/public/assets/css/style.css',
    '/public/assets/js/mobile-app.js',
    '/public/manifest.json',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
    'https://code.jquery.com/jquery-3.7.1.min.js'
];

// API endpoints to cache
const API_CACHE_PATTERNS = [
    /\/api\/news/,
    /\/api\/categories/,
    /\/api\/cities/,
    /\/api\/notifications/
];

// Image patterns to cache
const IMAGE_PATTERNS = [
    /\/public\/uploads\/news\//,
    /\/public\/uploads\/ads\//,
    /\/public\/uploads\/kyc\//,
    /\/public\/assets\/images\//
];

// Install event - cache static assets
self.addEventListener('install', event => {
    console.log('Service Worker: Installing...');
    
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => {
                console.log('Service Worker: Caching static assets');
                return cache.addAll(STATIC_ASSETS);
            })
            .then(() => {
                console.log('Service Worker: Static assets cached');
                return self.skipWaiting();
            })
            .catch(error => {
                console.error('Service Worker: Failed to cache static assets', error);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    console.log('Service Worker: Activating...');
    
    event.waitUntil(
        caches.keys()
            .then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cacheName => {
                        if (cacheName !== STATIC_CACHE && 
                            cacheName !== DYNAMIC_CACHE && 
                            cacheName !== IMAGE_CACHE) {
                            console.log('Service Worker: Deleting old cache', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                console.log('Service Worker: Activated');
                return self.clients.claim();
            })
    );
});

// Fetch event - serve from cache or network
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }
    
    // Skip chrome-extension and other non-http requests
    if (!request.url.startsWith('http')) {
        return;
    }
    
    // Handle different types of requests
    if (isStaticAsset(request.url)) {
        event.respondWith(handleStaticAsset(request));
    } else if (isAPIRequest(request.url)) {
        event.respondWith(handleAPIRequest(request));
    } else if (isImageRequest(request.url)) {
        event.respondWith(handleImageRequest(request));
    } else if (isHTMLRequest(request)) {
        event.respondWith(handleHTMLRequest(request));
    } else {
        event.respondWith(handleOtherRequest(request));
    }
});

// Handle static assets (CSS, JS, fonts)
function handleStaticAsset(request) {
    return caches.match(request)
        .then(response => {
            if (response) {
                return response;
            }
            
            return fetch(request)
                .then(fetchResponse => {
                    if (fetchResponse.status === 200) {
                        const responseClone = fetchResponse.clone();
                        caches.open(STATIC_CACHE)
                            .then(cache => cache.put(request, responseClone));
                    }
                    return fetchResponse;
                })
                .catch(() => {
                    // Return offline fallback for critical assets
                    if (request.url.includes('mobile-app.css')) {
                        return new Response(`
                            body { 
                                font-family: -apple-system, BlinkMacSystemFont, sans-serif; 
                                margin: 0; 
                                padding: 20px; 
                                background: #f8f9fa; 
                            }
                            .offline-message { 
                                text-align: center; 
                                padding: 40px 20px; 
                                color: #6c757d; 
                            }
                        `, {
                            headers: { 'Content-Type': 'text/css' }
                        });
                    }
                    throw new Error('Network error');
                });
        });
}

// Handle API requests
function handleAPIRequest(request) {
    return caches.open(DYNAMIC_CACHE)
        .then(cache => {
            return cache.match(request)
                .then(response => {
                    if (response) {
                        // Return cached response and update in background
                        fetchAndCache(request, cache);
                        return response;
                    }
                    
                    // Fetch from network
                    return fetch(request)
                        .then(fetchResponse => {
                            if (fetchResponse.status === 200) {
                                cache.put(request, fetchResponse.clone());
                            }
                            return fetchResponse;
                        })
                        .catch(() => {
                            // Return offline data if available
                            return getOfflineData(request);
                        });
                });
        });
}

// Handle image requests
function handleImageRequest(request) {
    return caches.open(IMAGE_CACHE)
        .then(cache => {
            return cache.match(request)
                .then(response => {
                    if (response) {
                        return response;
                    }
                    
                    return fetch(request)
                        .then(fetchResponse => {
                            if (fetchResponse.status === 200) {
                                cache.put(request, fetchResponse.clone());
                            }
                            return fetchResponse;
                        })
                        .catch(() => {
                            // Return placeholder image
                            return new Response(
                                '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg"><rect width="200" height="200" fill="#f8f9fa"/><text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="#6c757d" font-family="Arial, sans-serif" font-size="14">Image not available offline</text></svg>',
                                { headers: { 'Content-Type': 'image/svg+xml' } }
                            );
                        });
                });
        });
}

// Handle HTML requests
function handleHTMLRequest(request) {
    return fetch(request)
        .then(response => {
            if (response.status === 200) {
                const responseClone = response.clone();
                caches.open(DYNAMIC_CACHE)
                    .then(cache => cache.put(request, responseClone));
            }
            return response;
        })
        .catch(() => {
            // Return offline page
            return caches.match('/offline.html')
                .then(response => {
                    if (response) {
                        return response;
                    }
                    
                    // Create offline page
                    return new Response(`
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Offline - Next Update</title>
                            <style>
                                body { 
                                    font-family: -apple-system, BlinkMacSystemFont, sans-serif; 
                                    margin: 0; 
                                    padding: 20px; 
                                    background: #f8f9fa; 
                                    text-align: center;
                                }
                                .offline-container {
                                    max-width: 400px;
                                    margin: 50px auto;
                                    padding: 40px 20px;
                                    background: white;
                                    border-radius: 10px;
                                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                                }
                                .offline-icon {
                                    font-size: 4rem;
                                    color: #6c757d;
                                    margin-bottom: 20px;
                                }
                                .offline-title {
                                    font-size: 1.5rem;
                                    color: #2d3748;
                                    margin-bottom: 10px;
                                }
                                .offline-message {
                                    color: #6c757d;
                                    margin-bottom: 30px;
                                    line-height: 1.5;
                                }
                                .retry-btn {
                                    background: #667eea;
                                    color: white;
                                    border: none;
                                    padding: 12px 24px;
                                    border-radius: 6px;
                                    font-size: 1rem;
                                    cursor: pointer;
                                    transition: background 0.3s ease;
                                }
                                .retry-btn:hover {
                                    background: #5a6fd8;
                                }
                            </style>
                        </head>
                        <body>
                            <div class="offline-container">
                                <div class="offline-icon">📱</div>
                                <h1 class="offline-title">You're Offline</h1>
                                <p class="offline-message">
                                    It looks like you're not connected to the internet. 
                                    Some features may not be available.
                                </p>
                                <button class="retry-btn" onclick="window.location.reload()">
                                    Try Again
                                </button>
                            </div>
                        </body>
                        </html>
                    `, {
                        headers: { 'Content-Type': 'text/html' }
                    });
                });
        });
}

// Handle other requests
function handleOtherRequest(request) {
    return fetch(request)
        .catch(() => {
            // Return basic offline response
            return new Response('Offline', {
                status: 503,
                statusText: 'Service Unavailable'
            });
        });
}

// Helper functions
function isStaticAsset(url) {
    return url.includes('/public/assets/') || 
           url.includes('cdn.jsdelivr.net') || 
           url.includes('cdnjs.cloudflare.com') ||
           url.includes('code.jquery.com');
}

function isAPIRequest(url) {
    return API_CACHE_PATTERNS.some(pattern => pattern.test(url));
}

function isImageRequest(url) {
    return IMAGE_PATTERNS.some(pattern => pattern.test(url)) ||
           url.match(/\.(jpg|jpeg|png|gif|webp|svg)$/i);
}

function isHTMLRequest(request) {
    return request.headers.get('accept')?.includes('text/html');
}

function fetchAndCache(request, cache) {
    fetch(request)
        .then(response => {
            if (response.status === 200) {
                cache.put(request, response);
            }
        })
        .catch(() => {
            // Ignore network errors for background updates
        });
}

function getOfflineData(request) {
    // Return cached data or default response
    const url = new URL(request.url);
    
    if (url.pathname.includes('/api/news')) {
        return new Response(JSON.stringify({
            success: true,
            data: [],
            message: 'Offline mode - limited data available'
        }), {
            headers: { 'Content-Type': 'application/json' }
        });
    }
    
    if (url.pathname.includes('/api/categories')) {
        return new Response(JSON.stringify({
            success: true,
            data: [
                { id: 1, name: 'Local News', slug: 'local-news' },
                { id: 2, name: 'Sports', slug: 'sports' },
                { id: 3, name: 'Business', slug: 'business' }
            ]
        }), {
            headers: { 'Content-Type': 'application/json' }
        });
    }
    
    return new Response(JSON.stringify({
        success: false,
        message: 'No offline data available'
    }), {
        headers: { 'Content-Type': 'application/json' }
    });
}

// Background sync for offline actions
self.addEventListener('sync', event => {
    console.log('Service Worker: Background sync', event.tag);
    
    if (event.tag === 'background-sync') {
        event.waitUntil(doBackgroundSync());
    }
});

function doBackgroundSync() {
    // Sync offline actions when back online
    return new Promise((resolve) => {
        // Implement background sync logic here
        console.log('Service Worker: Performing background sync');
        resolve();
    });
}

// Push notifications
self.addEventListener('push', event => {
    console.log('Service Worker: Push notification received');
    
    const options = {
        body: event.data ? event.data.text() : 'New notification from Next Update',
        icon: '/public/assets/images/icon-192x192.png',
        badge: '/public/assets/images/badge-72x72.png',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'View',
                icon: '/public/assets/images/action-view.png'
            },
            {
                action: 'close',
                title: 'Close',
                icon: '/public/assets/images/action-close.png'
            }
        ]
    };
    
    event.waitUntil(
        self.registration.showNotification('Next Update', options)
    );
});

// Notification click
self.addEventListener('notificationclick', event => {
    console.log('Service Worker: Notification clicked');
    
    event.notification.close();
    
    if (event.action === 'explore') {
        event.waitUntil(
            clients.openWindow('/')
        );
    } else if (event.action === 'close') {
        // Just close the notification
    } else {
        // Default action - open the app
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});

// Message handling
self.addEventListener('message', event => {
    console.log('Service Worker: Message received', event.data);
    
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'GET_VERSION') {
        event.ports[0].postMessage({ version: CACHE_NAME });
    }
});

// Error handling
self.addEventListener('error', event => {
    console.error('Service Worker: Error', event.error);
});

self.addEventListener('unhandledrejection', event => {
    console.error('Service Worker: Unhandled promise rejection', event.reason);
});

console.log('Service Worker: Loaded successfully');
