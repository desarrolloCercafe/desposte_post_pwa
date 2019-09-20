(
  () => {
    const cacheName = 'news-v1';
    const staticAssets = [
      '/',
      'index.html',
      '/css/solicitud.css',
      '/css/consulta.css',
      '/node_modules/bootstrap/dist/css/bootstrap.min.css',
      '/node_modules/jquery/dist/jquery.min.js',
      '/node_modules/popper.js/dist/umd/popper.min.js',
      '/node_modules/bootstrap/dist/js/bootstrap.min.js',
      '/js/jquery-3.3.1.js',
      '/js/jquery.dataTables.min.js',
      '/js/dataTables.bootstrap4.min.js',
      '/js/fallback.json',
      '/js/app_pwa.js'
    ];

    self.addEventListener('install', async function () {
      const cache = await caches.open(cacheName);
      cache.addAll(staticAssets);
    });

    self.addEventListener('activate', event => {
      event.waitUntil(self.clients.claim());
    });

    self.addEventListener('fetch', event => {
      const request = event.request;
      const url = new URL(request.url);
      if (url.origin === location.origin) {
        event.respondWith(cacheFirst(request));
      } else {
        event.respondWith(networkFirst(request));
      }
    });

    async function cacheFirst(request) {
      const cachedResponse = await caches.match(request);
      return cachedResponse || fetch(request);
    }

    async function networkFirst(request) {
      const dynamicCache = await caches.open('news-dynamic');
      try {
        const networkResponse = await fetch(request);
        dynamicCache.put(request, networkResponse.clone());
        return networkResponse;
      } catch (err) {
        const cachedResponse = await dynamicCache.match(request);
        return cachedResponse || await caches.match('./fallback.json');
      }
    }
  }
)();
