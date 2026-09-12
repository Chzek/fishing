const CACHE_NAME = 'fishinglog-v3';
const MAP_CACHE_NAME = 'fishinglog-map-tiles-v1';

const STATIC_ASSETS = [
  '/',
  '/record/quick',
  '/map/offline',
  '/record/offline-review',
  '/manifest.json',
  '/favicon.ico',
  '/css/leaflet.css',
  '/js/leaflet.js',
  '/js/offline-sync.js',
  '/css/images/marker-icon.png',
  '/css/images/marker-icon-2x.png',
  '/css/images/marker-shadow.png',
  '/css/images/layers.png',
  '/css/images/layers-2x.png',
  '/api/v1/reference-data'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      // Use individual caching so a single failed asset never aborts the service worker installation
      return Promise.allSettled(
        STATIC_ASSETS.map((asset) =>
          cache.add(asset).catch((err) => console.warn('PWA: Non-fatal error caching initial asset:', asset, err))
        )
      );
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME && key !== MAP_CACHE_NAME) {
            console.log('PWA: Clearing obsolete cache:', key);
            return caches.delete(key);
          }
        })
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;

  const url = event.request.url;

  // 1. Intercept map tile requests for offline spatial tile caching
  if (url.includes('arcgisonline.com') || url.includes('opentopomap.org') || url.includes('tile.openstreetmap.org') || url.includes('/tile/')) {
    event.respondWith(
      caches.open(MAP_CACHE_NAME).then((cache) => {
        return cache.match(event.request).then((cachedResponse) => {
          if (cachedResponse) {
            return cachedResponse;
          }
          return fetch(event.request).then((networkResponse) => {
            if (networkResponse && (networkResponse.status === 200 || networkResponse.type === 'opaque')) {
              cache.put(event.request, networkResponse.clone());
            }
            return networkResponse;
          }).catch(() => {
            return new Response('', { status: 404, statusText: 'Tile Offline' });
          });
        });
      })
    );
    return;
  }

  // 2. Reference Data API Cache-First / Stale-While-Revalidate
  if (url.includes('/api/v1/reference-data')) {
    event.respondWith(
      fetch(event.request)
        .then((networkResponse) => {
          if (networkResponse && networkResponse.status === 200) {
            const clone = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
          }
          return networkResponse;
        })
        .catch(() => {
          return caches.match(event.request).then((cached) => {
            return cached || new Response('{"anglers":[],"lakes":[],"fish_breeds":[],"lures":[],"expeditions":[]}', {
              status: 200,
              headers: { 'Content-Type': 'application/json' }
            });
          });
        })
    );
    return;
  }

  // 3. Standard PWA Network-First with Cache Fallback
  event.respondWith(
    fetch(event.request)
      .then((networkResponse) => {
        if (networkResponse && networkResponse.status === 200) {
          const responseClone = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseClone);
          });
        }
        return networkResponse;
      })
      .catch(() => {
        return caches.match(event.request).then((cachedResponse) => {
          if (cachedResponse) {
            return cachedResponse;
          }
          // If HTML navigation request fails offline, fallback to boat quick catch logger
          if (event.request.headers.get('accept') && event.request.headers.get('accept').includes('text/html')) {
            return caches.match('/record/quick').then((quickFallback) => {
              return quickFallback || new Response('Offline - Fishing Logbook', { status: 503, statusText: 'Offline' });
            });
          }
          return new Response('Network offline', { status: 503, statusText: 'Offline' });
        });
      })
  );
});
