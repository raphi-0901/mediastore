var staticCacheName = "pwa-v" + new Date().getTime();
var filesToCache = [
    './css/app.min.css',
    './css/app-creative-dark.min.css',
    './css/app-creative.min.css',
    './js/app.min.js',
    './js/vendor.min.js',
    './js/qr-code.js',
    './images/Circle-Outline-Info.svg',
    './images/Circle-Outline-Success.svg',
    './images/Circle-Outline-Lightgrey.svg',
    './images/Circle-Outline-Error.svg',
    './images/Circle-Lightgrey.svg',
    './images/Circle-Lightblue.svg',
];

// Cache on install
self.addEventListener("install", event => {
    this.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => {
                return cache.addAll(filesToCache);
            })
    )
});

// Clear cache on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

// Serve from Cache
self.addEventListener("fetch", event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
            .catch(() => {
                return caches.match('offline');
            })
    )
});
