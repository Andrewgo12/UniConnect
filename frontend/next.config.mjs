/** @type {import('next').NextConfig} */
const nextConfig = {
  typescript: {
    // ignoreBuildErrors eliminado — los errores de TypeScript deben fallar el build.
    // Un error de tipos silenciado puede romper lógica de accesibilidad en producción.
  },
  images: {
    // unoptimized eliminado — Next.js optimiza imágenes por defecto.
    // En Android con conexión lenta, las imágenes optimizadas reducen el tiempo de carga.
  },
}

export default nextConfig
