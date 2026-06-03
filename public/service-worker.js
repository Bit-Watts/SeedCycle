/**
 * Service Worker for SeedCycle PWA
 * Provides offline functionality and caching
 */

const CACHE_NAME = 'seedcycle-v1.4.0';
const OFFLINE_URL = '/public/offline.html';

// Assets to cache on install
const STATIC_ASSETS = [
  '/public/',
  '/public/index.php',
  '/public/marketplace.php',
  '/public/assets/css/mobile-optimizations.css',
  '/public/assets/js/websocket-client.js',
  '/public/offline.html'
];

// Install event - cache static assets
self.addEventListener('install', (event) => {
  console.log('[Service Worker] Installing...');
  
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('[Service Worker] Caching static assets');
        return cache.addAll(STATIC_ASSETS);
      })
      .then(() => self.skipWaiting())
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  console.log('[Service Worker] Activating...');
  
  event.waitUntil(
    caches.keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => {
            if (cacheName !== CACHE_NAME) {
              console.log('[Service Worker] Deleting old cache:', cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      })
      .then(() => self.clients.claim())
  );
});

// Fetch event - network-first for PHP pages, cache-first for static assets
self.addEventListener('fetch', (event) => {
  // Skip non-GET requests
  if (event.request.method !== 'GET') {
    return;
  }

  // Skip WebSocket requests
  if (event.request.url.startsWith('ws://') || event.request.url.startsWith('wss://')) {
    return;
  }

  const url = event.request.url;
  const isStaticAsset = url.match(/\.(css|js|png|jpg|jpeg|gif|svg|webp|ico|woff|woff2)$/);
  const isPhpPage     = url.match(/\.php/) || url.endsWith('/');

  if (isPhpPage) {
    // ── NETWORK-ONLY for PHP pages ────────────────────────────────────────
    // Never cache dynamic pages — always get fresh data from server
    event.respondWith(
      fetch(event.request)
        .catch(() => {
          // Offline fallback only
          return caches.match(OFFLINE_URL);
        })
    );
  } else if (isStaticAsset) {
    // ── CACHE-FIRST for static assets ────────────────────────────────────
    // CSS, JS, images rarely change — serve from cache instantly
    event.respondWith(
      caches.match(event.request)
        .then((cached) => {
          if (cached) return cached;
          return fetch(event.request).then((response) => {
            if (response && response.status === 200) {
              const clone = response.clone();
              caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
            }
            return response;
          }).catch(() => caches.match(OFFLINE_URL));
        })
    );
  }
  // All other requests: let browser handle normally (no service worker intercept)
});

// Background sync for offline actions
self.addEventListener('sync', (event) => {
  console.log('[Service Worker] Background sync:', event.tag);
  
  if (event.tag === 'sync-messages') {
    event.waitUntil(syncMessages());
  }
  
  if (event.tag === 'sync-orders') {
    event.waitUntil(syncOrders());
  }
});

// Push notifications
self.addEventListener('push', (event) => {
  console.log('[Service Worker] Push notification received');
  
  const data = event.data ? event.data.json() : {};
  const title = data.title || 'SeedCycle';
  const options = {
    body: data.body || 'You have a new notification',
    icon: '/public/assets/images/icon-192x192.png',
    badge: '/public/assets/images/badge-72x72.png',
    vibrate: [200, 100, 200],
    data: data,
    actions: [
      {
        action: 'view',
        title: 'View'
      },
      {
        action: 'close',
        title: 'Close'
      }
    ]
  };
  
  event.waitUntil(
    self.registration.showNotification(title, options)
  );
});

// Notification click handler
self.addEventListener('notificationclick', (event) => {
  console.log('[Service Worker] Notification clicked');
  
  event.notification.close();
  
  if (event.action === 'view') {
    const urlToOpen = event.notification.data.url || '/public/';
    
    event.waitUntil(
      clients.matchAll({ type: 'window', includeUncontrolled: true })
        .then((clientList) => {
          // Check if app is already open
          for (let client of clientList) {
            if (client.url === urlToOpen && 'focus' in client) {
              return client.focus();
            }
          }
          // Open new window if not
          if (clients.openWindow) {
            return clients.openWindow(urlToOpen);
          }
        })
    );
  }
});

// Helper functions
async function syncMessages() {
  // Sync pending messages when back online
  console.log('[Service Worker] Syncing messages...');
  // Implementation depends on your offline storage strategy
}

async function syncOrders() {
  // Sync pending orders when back online
  console.log('[Service Worker] Syncing orders...');
  // Implementation depends on your offline storage strategy
}
