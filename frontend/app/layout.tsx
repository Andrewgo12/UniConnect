import type { Metadata, Viewport } from 'next'
import { Geist, Geist_Mono } from 'next/font/google'
import { Analytics } from '@vercel/analytics/next'
import Script from 'next/script'
import { ThemeProvider } from '@/components/theme-provider'
import { Toaster } from '@/components/ui/sonner'
import { ErrorBoundary } from '@/components/error-boundary'
import { AccessibilityProvider } from '@/context/AccessibilityContext'
import './globals.css'

const geist = Geist({ 
  subsets: ["latin"],
  variable: '--font-geist-sans',
  display: 'swap',
  fallback: ['system-ui', 'sans-serif'],
})
const geistMono = Geist_Mono({ 
  subsets: ["latin"],
  variable: '--font-geist-mono',
  display: 'swap',
  fallback: ['monospace'],
})

export const metadata: Metadata = {
  title: 'UniConnect - Comunicación Accesible',
  description: 'Aplicación de comunicación accesible con soporte para voz, alto contraste y navegación por teclado. Diseñada para personas con diversidad funcional.',
  generator: 'v0.app',
  keywords: ['accesibilidad', 'comunicación', 'WCAG', 'AAA', 'PWA', 'voz', 'discapacidad'],
  authors: [{ name: 'UniConnect Team' }],
  manifest: '/manifest.json',
  appleWebApp: {
    capable: true,
    statusBarStyle: 'default',
    title: 'UniConnect',
  },
  formatDetection: {
    telephone: false,
  },
  icons: {
    icon: '/icon-192x192.png',
    apple: '/apple-icon.png',
  },
}

export const viewport: Viewport = {
  themeColor: [
    { media: '(prefers-color-scheme: light)', color: '#0d47a1' },
    { media: '(prefers-color-scheme: dark)', color: '#1a237e' },
  ],
  width: 'device-width',
  initialScale: 1,
  maximumScale: 5,
  userScalable: true,
  viewportFit: 'cover',
}

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode
}>) {
  return (
    <html 
      lang="es" 
      className={`${geist.variable} ${geistMono.variable} bg-background`}
      suppressHydrationWarning
    >
      <body className="font-sans antialiased">
        <ThemeProvider
          attribute="class"
          defaultTheme="system"
          enableSystem
          disableTransitionOnChange
        >
          <AccessibilityProvider>
            <ErrorBoundary>
              {children}
            </ErrorBoundary>
          </AccessibilityProvider>
          {/* Toaster de Sonner — accesible, usa aria-live internamente */}
          <Toaster
            position="top-center"
            richColors
            closeButton
            duration={4000}
            aria-label="Notificaciones"
          />
        </ThemeProvider>
        {process.env.NODE_ENV === 'production' && <Analytics />}
        {/* Registro del Service Worker — solo en producción y solo en cliente */}
        <Script
          id="sw-register"
          strategy="afterInteractive"
          dangerouslySetInnerHTML={{
            __html: `
              if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                  navigator.serviceWorker.register('/sw.js')
                    .catch(function() { /* SW no disponible — app sigue funcionando online */ });
                });
              }
            `,
          }}
        />
      </body>
    </html>
  )
}
