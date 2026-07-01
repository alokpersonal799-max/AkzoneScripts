// AkzoneScripts service worker — enables PWA install.
// Intentionally minimal: we do NOT cache HTML pages, so content is always
// fresh (no stale-page issues). The fetch listener just makes the app
// installable; requests pass through to the network as normal.
self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', (event) => {
    // Pass-through (network). No caching to avoid serving stale content.
});
