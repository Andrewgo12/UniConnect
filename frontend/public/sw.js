/**
 * UniConnect Service Worker
 * Estrategia: Cache-first para assets estáticos, network-first para navegación.
 * Permite que la app funcione offline en Android después de la primera visita.
 */

const CACHE_NAME = "uniconnect-v1"

// Assets que se precargan en la instalación del SW
const PRECACHE_ASSETS = [
  "/",
  "/manifest.json",
  "/icon-192x192.png",
  "/icon-512x512.png",
]

// Instalar: precachear assets críticos
self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_ASSETS))
  )
  // Activar inmediatamente sin esperar a que cierren las pestañas anteriores
  self.skipWaiting()
})

// Activar: limpiar caches de versiones anteriores
self.addEventListener("activate", (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((key) => key !== CACHE_NAME)
          .map((key) => caches.delete(key))
      )
    )
  )
  // Tomar control de todas las pestañas abiertas sin recargar
  self.clients.claim()
})

// Fetch: cache-first para assets, network-first para navegación
self.addEventListener("fetch", (event) => {
  const { request } = event

  // Solo interceptar peticiones GET del mismo origen
  if (request.method !== "GET") return
  if (!request.url.startsWith(self.location.origin)) return

  // Navegación (HTML): network-first con fallback a cache
  if (request.mode === "navigate") {
    event.respondWith(
      fetch(request)
        .then((response) => {
          // Guardar copia fresca en cache
          const clone = response.clone()
          caches.open(CACHE_NAME).then((cache) => cache.put(request, clone))
          return response
        })
        .catch(() => caches.match(request).then((cached) => cached || caches.match("/")))
    )
    return
  }

  // Assets estáticos (_next/static, imágenes, manifest): cache-first
  event.respondWith(
    caches.match(request).then((cached) => {
      if (cached) return cached
      return fetch(request).then((response) => {
        // Solo cachear respuestas válidas
        if (!response || response.status !== 200 || response.type === "opaque") {
          return response
        }
        const clone = response.clone()
        caches.open(CACHE_NAME).then((cache) => cache.put(request, clone))
        return response
      })
    })
  )
})
