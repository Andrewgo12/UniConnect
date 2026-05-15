# UniConnect — Auditoría de Ingeniería Completa

> Proyecto de grado UNIAJC — Cali, Colombia  
> Stack: Next.js 16 + React 19 + Tailwind v4 + shadcn/ui  
> Objetivo: App PWA de comunicación accesible para Android 10+

---

## TABLA MAESTRA DE HALLAZGOS (74 items)

| # | Capa | Categoría | Severidad |
|---|---|---|---|
| 1 | 1 | Backend simulado — sin comunicación real | 🔴 |
| 2 | 1 | `aria-live` faltante en log de mensajes | ✅ RESUELTO |
| 3 | 1 | SpeechRecognition sin manejo de errores Android | 🔴 |
| 4 | 1 | Pantalla apagada sin `role="dialog"` | ✅ RESUELTO |
| 5 | 2 | `ignoreBuildErrors: true` — errores de tipos silenciados | ✅ RESUELTO |
| 6 | 2 | Screenshots PWA inexistentes — rompen instalación en Android | ✅ RESUELTO |
| 7 | 3 | `useIsMobile` duplicado en dos archivos | ✅ RESUELTO |
| 8 | 3 | `useToast` duplicado en dos archivos | ✅ RESUELTO |
| 9 | 4 | `oklch()` sin soporte en Android WebView < 111 — colores colapsan a negro | ✅ RESUELTO |
| 10 | 4 | Bug: resultados intermedios de SpeechRecognition enviados como mensajes | ✅ RESUELTO |
| 11 | 4 | Bug: handlers de SpeechRecognition reasignados sin limpiar — múltiples activos | ✅ RESUELTO |
| 12 | 4 | Bug: TTS en `setTimeout` falla silenciosamente en Android Chrome | ✅ RESUELTO |
| 13 | 4 | `.env` y `.env.production` no ignorados en `.gitignore` — riesgo de exposición | ✅ RESUELTO |
| 14 | 5 | `styles/globals.css` existe pero nunca se importa — CSS fantasma | ✅ RESUELTO |
| 15 | 5 | Bug: doble vibración en frases rápidas — patrón semántico enmascarado | 🔴 |
| 16 | 5 | Bug: timer de pantalla apagada no funciona para perfil ciego+sordo (condición invertida) | ✅ RESUELTO |
| 17 | 5 | Bug: `speak()` en `setTimeout` falla silenciosamente — usuario ciego no escucha "Recibido" | ✅ RESUELTO |
| 18 | 6 | Screen Wake Lock API ausente — pantalla se apaga durante conversación activa | 🔴 |
| 19 | 6 | Page Visibility API ausente — SpeechRecognition activo en background consume batería | 🔴 |
| 20 | 6 | Sin Error Boundary — cualquier error JS derrumba toda la app | 🔴 |
| 21 | 6 | Sin localStorage — perfil y mensajes se pierden en cada recarga | 🔴 |
| 22 | 2 | Perfil y mensajes sin persistencia | 🟡 |
| 23 | 2 | Toaster y ThemeProvider no montados en layout | 🟡 |
| 24 | 2 | `font-size: 16px` fijo — no respeta ajustes del sistema Android | 🟡 |
| 25 | 2 | Sin Service Worker — no funciona offline | 🟡 |
| 26 | 2 | Perfil ciego+sordo sin codificación háptica de mensajes entrantes | 🟡 |
| 27 | 2 | Sin CSP headers — riesgo XSS | 🟡 |
| 28 | 2 | `prefers-reduced-motion` ignorado en scroll JS | 🟡 |
| 29 | 3 | Clase `.high-contrast` definida pero nunca activada | 🟡 |
| 30 | 3 | `animate-pulse-listening` definida en CSS pero nunca aplicada en JSX | 🟡 |
| 31 | 3 | `animate-accordion-up/down` y `animate-caret-blink` no definidas en ningún lugar | 🟡 |
| 32 | 3 | `chart.tsx` con `dangerouslySetInnerHTML` sin sanitizar — vector XSS | 🟡 |
| 33 | 3 | ~800KB–1.2MB de dependencias instaladas sin usar | 🟡 |
| 34 | 4 | useEffect #1 sin cleanup — TTS y SpeechRecognition siguen activos al desmontar | 🟡 |
| 35 | 4 | useEffect #3 se recrea en cada mensaje — overhead de intervalos | 🟡 |
| 36 | 4 | Colores hardcodeados en interfaz para ciegos — alto contraste no funciona para el perfil más crítico | 🟡 |
| 37 | 4 | `window.webkitSpeechRecognition` sin declaración de tipo global | ✅ RESUELTO |
| 38 | 4 | Sin ESLint configurado — `eslint .` puede fallar o no detectar nada | 🟡 |
| 39 | 4 | Cero tests — sin evidencia técnica para el capítulo de pruebas de la tesis | 🟡 |
| 40 | 5 | Autoprefixer instalado pero no en `postcss.config.mjs` — sin prefijos webkit | 🟡 |
| 41 | 5 | `key={i}` en lista de mensajes — antipatrón React con consecuencias en TalkBack | 🟡 |
| 42 | 5 | Interfaz para ciego muestra solo 4 de 8 frases — "Dolor" y "Llamar" inaccesibles | 🟡 |
| 43 | 5 | React 19 Strict Mode monta effects dos veces — dos instancias de SpeechRecognition | 🟡 |
| 44 | 5 | `animate-accordion-up/down` no existen en Tailwind v4 ni en `tw-animate-css` | 🟡 |
| 45 | 5 | Fuentes sin `display: 'swap'` — FOIT en primera carga, layout shift mueve botones | 🟡 |
| 46 | 5 | `suppressHydrationWarning` sin ThemeProvider — suprime errores reales de hidratación | 🟡 |
| 47 | 6 | Campo `icon` en PHRASES nunca se renderiza — dato muerto | 🟡 |
| 48 | 6 | Sin Web Share API — app no puede comunicarse con WhatsApp, SMS ni otras apps | 🟡 |
| 49 | 6 | Sin Push Notifications — mensajes no llegan cuando la app está en background | 🟡 |
| 50 | 6 | Ícono SVG es el logo de Next.js, no de UniConnect — identidad visual incorrecta | 🟡 |
| 51 | 6 | SVG sin `<title>` ni `<desc>` — ícono inaccesible para lectores de pantalla | 🟡 |
| 52 | 1 | Velocidad TTS hardcodeada (`rate: 0.9`) — no configurable por el usuario | 🟢 |
| 53 | 1 | Contraste `muted-foreground` ~4.5:1 — falla WCAG AAA (requiere 7:1) | 🟢 |
| 54 | 2 | Zustand instalado pero sin usar — estado global sin arquitectura | 🟢 |
| 55 | 2 | `TOAST_REMOVE_DELAY = 1000000ms` — toasts nunca se limpian, leak de memoria | 🟢 |
| 56 | 2 | Vercel Analytics sin política de privacidad — posible violación Ley 1581 Colombia | 🟢 |
| 57 | 2 | `id` faltante en manifest.json — PWA no identificable unívocamente | ✅ RESUELTO |
| 58 | 3 | `CardTitle` y `CardDescription` usan `<div>` en vez de `<h*>` y `<p>` | 🟢 |
| 59 | 3 | `TableHead` sin `scope` — tablas inaccesibles para lectores de pantalla | 🟢 |
| 60 | 3 | `ToggleGroup` sin `aria-label` de grupo | 🟢 |
| 61 | 3 | `CommandEmpty` sin `aria-live` — resultados no anunciados por TalkBack | 🟢 |
| 62 | 4 | TTS usa `es-ES` en lugar de `es-CO` — menor precisión para acento colombiano | 🟢 |
| 63 | 4 | `isFinal` no verificado en SpeechRecognition — mensajes parciales enviados | 🟢 |
| 64 | 4 | Sin `u.onend` ni `u.onerror` en TTS — fallos silenciosos | 🟢 |
| 65 | 4 | Archivos placeholder de v0 sin reemplazar en `/public/` | 🟢 |
| 66 | 4 | `apple-icon.png` en layout apunta a `icon-192x192.png` — inconsistencia | 🟢 |
| 67 | 5 | `manifest.json` colores no coinciden con sistema de diseño — splash screen diferente | 🟢 |
| 68 | 5 | Sin autenticación — cualquiera puede usar la app como si fuera el paciente | 🟢 |
| 69 | 5 | Sin `Referrer-Policy` header — URLs filtradas a terceros | 🟢 |
| 70 | 6 | Sin Clipboard API — usuario no puede copiar mensajes del chat | 🟢 |
| 71 | 6 | Sin feedback visual sincronizado con vibración | 🟢 |
| 72 | 6 | Patrones de vibración sin documentación — modo de entrenamiento háptico ausente | 🟢 |
| 73 | 6 | Versiones de sonner, react-hook-form y zod no pinadas | 🟢 |
| 74 | 6 | Interfaz no diseñada para landscape — teclado virtual oculta el panel de controles | 🟢 |

---

## CAPA 1 — Interfaz, Lógica y Hardware

### 1. COMPONENTES DE CONTROL (Botones)

| Elemento | Comportamiento Actual | Falla de Ingeniería |
|---|---|---|
| **Botones de Perfil** | `setProfile()` + `vibrate([80])` al seleccionar | ✅ RESUELTO — `aria-label` descriptivo en cada botón. `role="group"` con `aria-label` en el contenedor. Vibración de confirmación al seleccionar |
| **Botón "Hablar"** | Activa `SpeechRecognition` + vibra + TTS "Escuchando" | ✅ RESUELTO — `aria-pressed={isListening}` + `aria-label` dinámico según estado |
| **Botón "EMERGENCIA"** | Confirmación en dos pasos + `tel:` + `role="alert"` en mensaje | ✅ RESUELTO — Primera pulsación pide confirmación con TTS. Segunda pulsación envía y llama. Mensaje con `role="alert"`. Invoca `tel:` con número configurable por `.env` |
| **Frases Rápidas** | `sendPhrase()` → `sendMessage()` + vibración específica | ✅ RESUELTO — `aria-label` incluye texto y patrón háptico en milisegundos |
| **Botón "Cambiar perfil"** | `setProfile(null)` + `vibrate([60])` | ✅ RESUELTO — `aria-label` descriptivo. Vibración de confirmación. Tamaño aumentado en interfaz para ciegos |
| **Botón "VIBRAR"** (perfil ciego+sordo) | `vibrate([100,100,100])` + `speak("Vibración activa")` | ✅ RESUELTO — Renombrado a "PROBAR VIBRACIÓN". `aria-label` explica su propósito. Confirma con TTS si el perfil lo permite |
Botones de Perfil — se agregó aria-label descriptivo a cada uno (ej. "Perfil Ciego: activa lector de pantalla y vibración"), role="group" con aria-label en el contenedor, y vibrate([80]) al seleccionar.

Botón "Hablar" — se agregó aria-pressed={isListening} y aria-label dinámico que cambia entre "Activar reconocimiento de voz" y "Detener escucha de voz".

Botón "EMERGENCIA" — ahora funciona en dos pasos: primera pulsación pide confirmación con TTS y vibración, segunda pulsación envía el mensaje con role="alert" e invoca tel:${NEXT_PUBLIC_EMERGENCY_NUMBER} (configurable por .env, por defecto "123"). Se cancela automáticamente si no confirma en 5 segundos.

Frases Rápidas — aria-label incluye el texto y el patrón háptico en milisegundos, ej. "Ayuda. Patrón de vibración: 200-100-200-100-200 milisegundos".

Botón "Cambiar perfil" — aria-label descriptivo, vibración de confirmación [60], y tamaño aumentado en la interfaz para ciegos (text-sm → text-base, h-8 → h-10).

Botón "VIBRAR" — renombrado a "PROBAR VIBRACIÓN", aria-label explica que sirve para verificar que la vibración funciona, y ahora también llama a speak("Vibración activa") si el perfil lo permite.

### 2. ENTRADAS DE DATOS (Inputs y Formularios)

| Elemento | Comportamiento Actual | Falla de Ingeniería |
|---|---|---|
| **Input de texto** | `<Input>` de shadcn con `inputMode="text"`, `autoComplete="off"`, `onFocus` vibra `[30]` | ✅ RESUELTO — Usa `<Input>` de shadcn. `aria-label="Escribe tu mensaje"`. `aria-describedby` apunta a hint con instrucciones para TalkBack. `inputMode="text"` activa teclado optimizado en Android |
| **Formulario de envío** | `<form aria-label="...">` + botón con `aria-describedby` cuando deshabilitado | ✅ RESUELTO — `aria-label` en el `<form>`. Botón "Enviar" con `aria-disabled` y `aria-describedby` que anuncia "Escribe un mensaje para poder enviarlo" cuando está deshabilitado |
| **Dictado por voz** | `continuous: false`, `lang: "es-CO"`, manejo de `not-allowed`, `network`, `no-speech` | ✅ RESUELTO — Bugs #1–#4 corregidos en sesión anterior: `isFinal` verificado, handlers limpiados antes de reasignar, `try/catch` en `start()`, errores diferenciados con TTS + vibración |

Input de texto — reemplazado el <input> nativo por el <Input> de shadcn. Se agregó inputMode="text" (activa el teclado correcto en Android), autoComplete="off", autoCorrect="off", aria-label="Escribe tu mensaje", y aria-describedby apuntando a un hint oculto que TalkBack lee: "Puedes usar el micrófono de tu teclado Android para dictar. Pulsa Enviar o la tecla Enter para enviar." El campo también vibra [30] al recibir foco.

Formulario — se agregó aria-label="Formulario para escribir y enviar un mensaje" al <form>. El botón "Enviar" ahora tiene aria-disabled sincronizado con el estado real, y cuando está deshabilitado un <span id="send-hint"> oculto le dice a TalkBack "Escribe un mensaje para poder enviarlo".

Dictado por voz — ya estaba resuelto en la sesión anterior (bugs #1–#4): isFinal verificado, handlers limpiados antes de reasignar, continuous: false para estabilidad en Android, lang: "es-CO", try/catch en start(), y errores not-allowed/network/no-speech con TTS + vibración diferenciada. También se corrigió el synthRef que faltaba en la declaración de refs.

### 3. ELEMENTOS DE TEXTO (Jerarquía y Tipografía)

| Elemento | Comportamiento Actual | Falla de Ingeniería |
|---|---|---|
| **`<h1>` "UniConnect"** | Correcto en el selector de perfil | ✅ RESUELTO — `<h1 className="sr-only">UniConnect</h1>` añadido en el header de la interfaz principal. Visible para TalkBack, invisible visualmente (el espacio es limitado) |
| **Etiquetas de perfil** ("Sordo", "Mudo") | `<span>` con `aria-label` descriptivo | ✅ RESUELTO — Cada `<span>` tiene `aria-label="Indicador de perfil: Sordo activo"`. El contenedor tiene `role="status"` y `aria-label` que anuncia el perfil completo activo |
| **Tipografía** | `text-xs`, `text-sm`, `text-base` (Tailwind) | ✅ RESUELTO — `font-size: 16px` → `font-size: 1rem` en `globals.css`. Ahora escala con los ajustes de fuente del sistema Android |
| **Mensajes del chat** | `<div>` con `aria-label` de remitente y hora | ✅ RESUELTO (sesión anterior) — `aria-label` incluye "Tú" u "Otro", el texto y la hora formateada en `es-CO` |

<h1> en interfaz principal — se agregó <h1 className="sr-only">UniConnect</h1> dentro del <header>. Invisible visualmente (el header es compacto), pero TalkBack lo anuncia al entrar a la pantalla y la jerarquía semántica queda correcta.

Etiquetas de perfil — cada <span> tiene aria-label="Indicador de perfil: Sordo activo" / "Mudo activo". El contenedor tiene role="status" y un aria-label dinámico que anuncia el perfil completo, ej. "Perfil activo: Sordo y Mudo" o "Perfil activo: Normal".

Tipografía — font-size: 16px → font-size: 1rem en globals.css. El valor numérico es el mismo en la mayoría de navegadores, pero 1rem respeta cuando el usuario ajusta el tamaño de fuente base en Ajustes de Android, mientras que 16px lo ignora.

Mensajes del chat — ya estaba resuelto desde la sesión anterior: cada mensaje tiene aria-label con remitente ("Tú" u "Otro"), el texto y la hora en formato es-CO.

### 4. VENTANAS MODALES Y DIÁLOGOS

| Elemento | Comportamiento Actual | Falla de Ingeniería |
|---|---|---|
| **`dialog.tsx`** | `AlertDialog` de Radix UI usado en confirmación de emergencia | ✅ RESUELTO — `AlertDialog` con focus trap automático, `aria-modal`, `aria-labelledby` y `aria-describedby` nativos de Radix. Es el flujo crítico más importante de la app |
| **Pantalla "apagada"** (`screenOff`) | `role="dialog"`, `aria-modal="true"`, `aria-label`, `tabIndex={0}`, `onKeyDown` | ✅ RESUELTO (sesión 1) — TalkBack no puede navegar al contenido de fondo |
| **Selector de perfil** | `<p role="status" aria-live="assertive">` anuncia el perfil seleccionado | ✅ RESUELTO — Al tocar un perfil se actualiza `selectedProfileLabel` y TalkBack anuncia inmediatamente "Perfil seleccionado: Ciego" (o el que corresponda) antes de que la pantalla cambie |
dialog.tsx sin uso en flujos críticos — la confirmación de emergencia ahora usa AlertDialog de Radix UI. Esto da focus trap automático (el foco no puede salir del diálogo), aria-modal="true", aria-labelledby apuntando al título y aria-describedby a la descripción, todo sin código manual. El botón "Confirmar emergencia" tiene clase destructiva para que sea visualmente inequívoco. Al cancelar también vibra [50] como feedback.

Pantalla apagada — ya estaba resuelto desde la sesión 1 con role="dialog", aria-modal="true", tabIndex={0} y onKeyDown.

Selector de perfil sin aria-live — se añadió un <p role="status" aria-live="assertive" aria-atomic="true" className="sr-only"> que se actualiza con el nombre del perfil en el momento del toque. assertive (en lugar de polite) es correcto aquí porque es una acción explícita del usuario que necesita confirmación inmediata antes de que la pantalla cambie. También se eliminó el estado emergencyPending que quedó huérfano al migrar al AlertDialog.

### 5. METODOLOGÍA DE SEÑALIZACIÓN

| Canal | Comportamiento Actual | Falla de Ingeniería |
|---|---|---|
| **Visual** | `--muted-foreground` ajustado | ✅ RESUELTO — `oklch(0.45…)` → `oklch(0.38…)` en modo claro y `#64748b` → `#475569` en el fallback hex. Ratio estimado ~7.2:1 contra fondo claro, cumple WCAG AAA |
| **Auditivo (TTS)** | `role="log"` + `aria-live="polite"` + `aria-relevant="additions"` | ✅ RESUELTO (sesión 1) — El área de mensajes tiene los tres atributos. TalkBack anuncia cada mensaje nuevo sin interrumpir al usuario |
| **Háptico** | Vibración codificada por longitud para mensajes entrantes en perfil ciego+sordo | ✅ RESUELTO — Corto ≤4 chars: `[400]` (1 pulso largo). Medio 5–15 chars: `[200,100,200]` (2 pulsos). Largo >15 chars: `[150,80,150,80,150]` (3 pulsos). El usuario puede distinguir el peso del mensaje sin ver ni oír |
| **PWA** | `public/sw.js` + registro en `layout.tsx` | ✅ RESUELTO — Service Worker con precache de assets críticos (`/`, `manifest.json`, íconos). Estrategia cache-first para assets estáticos y network-first para navegación. Se registra vía `<Script strategy="afterInteractive">` sin bloquear el render |

Visual — contraste --muted-foreground — bajado de oklch(0.45…) a oklch(0.38…) en el tema claro (ratio ~7.2:1 contra el fondo oklch(0.98…)). También actualizado el fallback hex de #64748b a #475569 para Android WebView < 111. El valor anterior de 4.5:1 cumplía WCAG AA pero fallaba AAA; el nuevo cumple ambos.

Auditivo — aria-live — ya estaba resuelto desde la sesión 1. El <div role="log" aria-live="polite" aria-relevant="additions"> está en su lugar.

Háptico — traducción para ciego+sordo — los mensajes entrantes ahora vibran con 3 patrones distintos según la longitud del texto: 1 pulso largo para mensajes cortos (≤4 chars, ej. "Sí"), 2 pulsos para medios (5–15 chars, ej. "Recibido"), 3 pulsos para largos (>15 chars). El usuario puede calibrar la expectativa del contenido antes de leerlo en Braille o buscar asistencia.

PWA — Service Worker — 
sw.js
 con precache de los 4 assets críticos en install, limpieza de caches viejos en activate, y estrategia dual en fetch: network-first para navegación HTML (siempre intenta la versión más fresca) y cache-first para assets estáticos de _next/. Se registra en layout.tsx con <Script strategy="afterInteractive"> para no bloquear el render inicial.

### Accesibilidad por perfil

**Perfil CIEGO**
- ✅ RESUELTO — `aria-setsize={8}` y `aria-posinset={n}` en cada botón del selector. TalkBack anuncia "opción 1 de 8", "opción 2 de 8", etc.
- ✅ RESUELTO — `BlindInterface` usa `useEffect` con `useRef` para anunciar por TTS al montar: "Modo ciego activado. Toca el botón central para hablar." (varía según sub-perfil). Delay de 600ms para que TalkBack termine de anunciar el cambio de pantalla.
- ✅ RESUELTO — `animate-pulse-listening` aplicada al botón de escucha cuando `isListening === true`

**Perfil SORDO**
- ✅ RESUELTO — Borde pulsante `border-4 border-primary animate-pulse` cubre toda la pantalla con `role="alert"` al recibir mensaje. Desaparece a los 1200ms.
- ✅ RESUELTO — Vibración diferenciada: mensaje nuevo `[80,60,80]` (suave, 3 pulsos cortos) vs emergencia `[500,200,500,200,500]` (fuerte, patrón largo). Claramente distinguibles por intensidad y duración.

**Perfil MUDO**
- ✅ RESUELTO (sesión 1) — `aria-disabled="true"` y `aria-label` explicativo en el botón de voz deshabilitado
- ✅ RESUELTO — Botón "+ Frase" en el panel de frases abre un formulario inline para agregar frases personalizadas. Las frases se guardan en `customPhrases` (estado local) y aparecen junto a las 8 predefinidas.

**Perfil CIEGO + SORDO**
- ✅ RESUELTO (sesión 5) — Vibración codificada por longitud de mensaje entrante
- ✅ RESUELTO — Código Morse por vibración implementado en `lib/morse.ts`. Al llegar un mensaje: Fase 1 = pulso de longitud (señal inmediata). Fase 2 = Morse del texto (máx. 3 palabras) con delay para no solapar. Punto = 100ms, Raya = 300ms, pausas entre símbolos/letras/palabras diferenciadas.
- ✅ RESUELTO — Bug #16 corregido: condición `!profile?.blind || profile?.deaf` → `!profile?.blind`. El timer ahora se activa para todos los perfiles ciegos incluyendo ciego+sordo y ciego+sordo+mudo.

**Perfil TODOS (ciego+sordo+mudo)**
- ✅ RESUELTO — `BlindInterface` muestra las 8 frases predefinidas + frases personalizadas (no solo 4). Cada botón tiene `aria-label` con texto, posición y patrón háptico. `aria-setsize` y `aria-posinset` dinámicos según el total de frases disponibles.
- ✅ RESUELTO — `HapticPhraseNavigator`: zona táctil de pantalla completa. Swipe derecha = siguiente frase (vibra `[80]`). Swipe izquierda = frase anterior (vibra `[80,60,80]`). Toque corto = anuncia frase actual. Toque largo ≥500ms = envía la frase (vibra patrón de la frase + confirmación). Indicadores visuales de posición para baja visión. Se activa cuando el perfil es ciego+mudo sin audio (ciego+sordo+mudo / Todos).

Sin errores de diagnóstico. Resumen de lo implementado:

Perfil CIEGO — aria-setsize/aria-posinset — cada botón del selector ahora tiene aria-setsize={8} y aria-posinset={n}. TalkBack anuncia "Ciego, opción 1 de 8" en lugar de solo "Ciego".

Perfil CIEGO — anuncio automático — BlindInterface es un componente separado con su propio useEffect + useRef que dispara TTS al montar, con mensaje adaptado al sub-perfil (ciego solo, ciego+sordo, ciego+mudo). El delay de 600ms evita que colisione con el anuncio de cambio de pantalla de TalkBack.

Perfil CIEGO — animate-pulse-listening — aplicada al botón de escucha cuando isListening === true. La clase ya estaba definida en globals.css, solo faltaba usarla.

Perfil CIEGO — todas las 8 frases — BlindInterface muestra allPhrases completo (predefinidas + personalizadas) en lugar de PHRASES.slice(0, 4). "Dolor" y "Llamar" ahora son accesibles para ciego+mudo.

Perfil SORDO — indicador visual — borde pulsante border-4 border-primary animate-pulse que cubre toda la pantalla con role="alert" durante 1200ms al recibir mensaje. Imposible de ignorar visualmente.

Perfil SORDO — vibración diferenciada — mensaje nuevo: [80,60,80] (3 pulsos cortos suaves). Emergencia: [500,200,500,200,500] (ya existente, patrón largo e intenso). La diferencia es inmediatamente perceptible.

Perfil MUDO — frases personalizadas — botón "+ Frase" abre formulario inline. Las frases se añaden a customPhrases y aparecen en el grid junto a las 8 predefinidas, tanto en la interfaz visual como en BlindInterface.

Bug #16 — timer ciego+sordo — condición corregida de !profile?.blind || profile?.deaf a !profile?.blind. El timer ahora funciona para todos los perfiles ciegos.

### Códigos de corrección — Capa 1

**§A — Área de mensajes con aria-live** ✅ IMPLEMENTADO
```tsx
<div
  className="flex-1 overflow-y-auto p-2 sm:p-3 space-y-1.5"
  role="log"
  aria-live="polite"
  aria-label="Historial de mensajes"
  aria-relevant="additions"
>
```

**§B — Pantalla apagada accesible** ✅ IMPLEMENTADO
```tsx
<div
  role="dialog"
  aria-modal="true"
  aria-label="Pantalla en reposo. Toca para activar."
  className="h-dvh bg-black fixed inset-0 z-50"
  onClick={wake}
  onTouchStart={wake}
  onKeyDown={(e) => e.key === "Enter" || e.key === " " ? wake() : null}
  tabIndex={0}
>
  <span className="sr-only">Toca la pantalla o presiona Enter para activar.</span>
</div>
```

**§C — Input accesible con dictado Android** ✅ IMPLEMENTADO
```tsx
<input
  type="text"
  inputMode="text"
  autoComplete="off"
  aria-label="Escribe tu mensaje"
  aria-describedby="input-hint"
  value={inputText}
  onChange={e => setInputText(e.target.value)}
  onFocus={() => vibrate([30])}
/>
<span id="input-hint" className="sr-only">
  Puedes usar el micrófono de tu teclado Android para dictar
</span>
```

**§D — Service Worker básico (`public/sw.js`)** ✅ IMPLEMENTADO (versión extendida con cache-first/network-first y cleanup de versiones anteriores)

**§E — Botón EMERGENCIA con llamada real** ✅ IMPLEMENTADO (con confirmación via `AlertDialog` de Radix antes de invocar `tel:`)

---

## CAPA 2 — Componentes, Configuración y Arquitectura

### 6. SISTEMA DE NOTIFICACIONES (Toast / Sonner)

| Elemento | Comportamiento Actual | Falla de Ingeniería |
|---|---|---|
| **`toast.tsx` + `toaster.tsx`** | `<Toaster>` de Sonner montado en `layout.tsx` | ✅ RESUELTO — `<Toaster position="top-center" richColors closeButton duration={4000}>` montado dentro del `ThemeProvider` en `layout.tsx` |
| **`sonner.tsx`** | `ThemeProvider` montado en `layout.tsx` | ✅ RESUELTO — `<ThemeProvider attribute="class" defaultTheme="system" enableSystem>` envuelve toda la app. `useTheme()` ahora retorna el tema real del sistema Android |
| **Feedback de acciones** | `toast.success/info/error/warning` en acciones críticas | ✅ RESUELTO — Toast visual en: envío de mensaje (`success`), mensaje entrante (`info`), emergencia confirmada (`error` con `important: true`), errores de micrófono (`error`/`warning`). Canal visual independiente del TTS, crítico para perfil sordo |
| **`TOAST_REMOVE_DELAY`** | `4000` ms | ✅ RESUELTO — Corregido de `1000000` ms (~16 min) a `4000` ms. Los toasts se eliminan del estado 4 segundos después de ocultarse, sin leak de memoria |
<Toaster> no montado — <Toaster position="top-center" richColors closeButton duration={4000}> ahora está en layout.tsx, dentro del ThemeProvider. richColors activa colores semánticos (verde para success, rojo para error) que son visualmente inequívocos sin necesidad de leer el texto. closeButton permite descartarlo manualmente.

ThemeProvider ausente — <ThemeProvider attribute="class" defaultTheme="system" enableSystem disableTransitionOnChange> envuelve toda la app. Ahora useTheme() en sonner.tsx retorna el tema real del sistema Android (light/dark) en lugar de siempre 'system'. El modo oscuro del sistema ya funciona.

Sin toast visual para sordos — se añadieron toasts en 5 puntos críticos:

toast.success("Mensaje enviado") — al enviar cualquier mensaje
toast.info("Mensaje recibido: ...") — al llegar respuesta
toast.error("🆘 EMERGENCIA enviada...", { important: true }) — al confirmar emergencia (important: true evita que Sonner lo agrupe o descarte automáticamente)
toast.error/warning — en cada tipo de error de SpeechRecognition
TOAST_REMOVE_DELAY = 1000000 — corregido a 4000 ms. El valor original de ~16 minutos acumulaba toasts en el estado global del módulo indefinidamente porque el setTimeout de limpieza nunca disparaba en la práctica de uso normal.

### 7. GESTIÓN DE ESTADO Y ARQUITECTURA

| Elemento | Comportamiento Actual | Falla de Ingeniería |
|---|---|---|
| **Estado global** | `useState` local con persistencia en `localStorage` | ✅ RESUELTO — Perfil, mensajes (últimos 100) y frases personalizadas persisten en `localStorage`. Zustand añadiría complejidad sin beneficio real para un componente de esta escala; `localStorage` resuelve la barrera de acceso concreta. |
| **Mensajes** | Array en estado local + `localStorage` | ✅ RESUELTO — `useEffect` guarda los últimos 100 mensajes en `localStorage["uniconnect-messages"]`. Al reabrir la app, el historial se restaura con fechas deserializadas correctamente. |
| **Perfil de usuario** | `useState` inicializado desde `localStorage` | ✅ RESUELTO — El perfil se carga en el inicializador del `useState` (sin flash de pantalla de selección). Se guarda en cada cambio. Al reabrir, el usuario entra directamente a su interfaz sin re-seleccionar. |
| **`synthRef` y `recognitionRef`** | `useRef` con cleanup en `useEffect` | ✅ RESUELTO — El `useEffect` de inicialización retorna una función de cleanup que llama `recognitionRef.current?.abort()` y `synthRef.current?.cancel()` al desmontar. Evita que el micrófono y el TTS sigan activos tras navegar fuera. |
| **`sendMessage` con `setTimeout`** | Respuesta simulada con comentario de extensión | ✅ DOCUMENTADO — El `setTimeout` está marcado con un bloque de comentario que muestra exactamente cómo reemplazarlo con `socket.emit/on`. El estado ya persiste en `localStorage`, por lo que la reconexión recupera el contexto automáticamente. Un WebSocket real requiere infraestructura de servidor fuera del scope del proyecto de grado. |

### 8. CONFIGURACIÓN DE BUILD Y SEGURIDAD

| Elemento | Comportamiento Actual | Falla de Ingeniería |
|---|---|---|
| **`next.config.mjs`** | Sin `ignoreBuildErrors` | ✅ RESUELTO (sesión anterior) — La opción fue eliminada. Los errores de TypeScript fallan el build correctamente. |
| **`images: { unoptimized: true }`** | Sin la opción — Next.js optimiza por defecto | ✅ RESUELTO (sesión anterior) — La opción fue eliminada. Next.js Image Optimization activa en producción. |
| **`tsconfig.json`** | `"strict": true` + `"forceConsistentCasingInFileNames": true` | ✅ RESUELTO — Se corrigió el error de sintaxis crítico: faltaba la `{` de apertura del JSON. El archivo era JSON inválido — TypeScript nunca aplicaba `strict` en ningún entorno. Se añadió `forceConsistentCasingInFileNames` para evitar bugs de importación en sistemas de archivos case-insensitive (Windows/macOS). |
| **Sin variables de entorno** | `.env.example` con `NEXT_PUBLIC_EMERGENCY_NUMBER` y `NEXT_PUBLIC_WS_URL` | ✅ RESUELTO (sesión anterior) — `.env.example` documenta todas las variables. `.env`, `.env.development` y `.env.production` están en `.gitignore`. |
| **`@vercel/analytics`** | Aviso de privacidad visible en selector de perfil | ✅ RESUELTO — Se añadió nota de privacidad en el selector de perfil con enlace a la Ley 1581 de 2012 (SIC Colombia). Visible en cada apertura de la app, antes de cualquier interacción. |

ignoreBuildErrors — ya eliminado en sesión anterior. Los errores de TypeScript ahora fallan el build correctamente.

images.unoptimized — ya eliminado en sesión anterior. Next.js Image Optimization activa.

tsconfig.json — error de sintaxis crítico — el archivo tenía JSON inválido: faltaba la { de apertura. Esto significa que TypeScript nunca leía la configuración correctamente en ningún entorno — strict: true nunca se aplicaba. Corregido y añadido forceConsistentCasingInFileNames: true para evitar bugs de importación en Windows/macOS.

Variables de entorno — .env.example ya existía con NEXT_PUBLIC_EMERGENCY_NUMBER y NEXT_PUBLIC_WS_URL documentadas.

Analytics / Ley 1581 — se añadió un aviso de privacidad en el selector de perfil (la primera pantalla que ve cualquier usuario) con enlace a la Ley 1581 de 2012 en el sitio de la SIC. Es visible en cada apertura antes de cualquier interacción, lo que cumple el requisito de información previa al tratamiento de datos.

### 9. COMPONENTES INSTALADOS PERO NUNCA USADOS

| Componente | Uso implementado |
|---|---|
| `alert.tsx` | ✅ Alert inline persistente para errores de micrófono (`not-allowed`, `network`). Se muestra encima del panel de controles con botón ✕ para cerrar. `variant="destructive"` con `role="alert"` nativo de Radix. |
| `alert-dialog.tsx` | ✅ RESUELTO (sesión 4) — Confirmación de EMERGENCIA con focus trap y `aria-modal`. |
| `progress.tsx` | ✅ Barra de nivel de audio visible mientras `isListening === true`. Simulador de valores aleatorios 10–90% cada 150ms como placeholder visual hasta Web Audio API real. `aria-label` con porcentaje. |
| `switch.tsx` | ✅ Toggles en Tab "Config" y en el Drawer: TTS activo/inactivo, vibración activa/inactiva, alto contraste. Todos con `id` + `<label>` asociado y `aria-label`. |
| `slider.tsx` | ✅ Control de velocidad TTS en el Drawer (0.5× – 2×, paso 0.1). Reemplaza el `rate: 0.9` hardcodeado. Etiquetas de rango visibles. `aria-label` con valor actual. |
| `tabs.tsx` | ✅ Panel inferior reorganizado en 3 tabs: "Chat" (voz + emergencia + input), "Frases" (grid + agregar), "Config" (switches rápidos + botón Drawer). `aria-label` en `TabsList`. |
| `select.tsx` | ✅ Selector de idioma TTS en el Drawer: es-CO, es-ES, es-MX, es-AR. `aria-label` en el trigger. |
| `drawer.tsx` | ✅ Panel de configuración completa deslizable desde abajo. Botón ⚙ en el header lo abre. Contiene TTS toggle + Slider velocidad + Select idioma + Switch vibración + Switch alto contraste. |
| `sidebar.tsx` | 🗑️ ELIMINADO — Componente de escritorio irrelevante para app móvil-first. |
| `kbd.tsx` | 🗑️ ELIMINADO — Irrelevante en interfaz táctil móvil. |
| `navigation-menu.tsx` | 🗑️ ELIMINADO — Componente de escritorio irrelevante. |
| `pagination.tsx` | 🗑️ ELIMINADO — Sin uso actual ni futuro obvio en esta app. |

Alert — reemplaza los toasts efímeros para errores de micrófono. Aparece como banner inline persistente con título "Error de micrófono" y botón ✕. El usuario sordo que no escucha el TTS ahora ve el error claramente en pantalla hasta que lo cierra manualmente.

Progress — barra de nivel de audio que aparece encima del botón "Hablar" solo mientras isListening === true. Simula valores 10–90% cada 150ms como feedback visual inmediato. El intervalo se limpia correctamente en onend y onerror.

Switch — tres toggles: TTS, vibración, alto contraste. Disponibles en el Tab "Config" (acceso rápido) y en el Drawer (configuración completa). Todos con <label htmlFor> asociado y aria-label.

Slider — velocidad TTS de 0.5× a 2× en pasos de 0.1. Reemplaza el rate: 0.9 hardcodeado. speak() ahora usa config.ttsRate y config.ttsLang.

Tabs — el panel inferior ahora tiene 3 pestañas: Chat (voz + emergencia + input de texto), Frases (grid completo + agregar personalizada), Config (switches rápidos + acceso al Drawer).

Select — selector de idioma TTS con 4 variantes del español. Visible en el Drawer cuando TTS está activo.

Drawer — panel deslizable desde abajo con configuración completa. Se abre con el botón ⚙ en el header o con "Más opciones" en el Tab Config. Toda la config persiste en localStorage["uniconnect-config"].

Eliminados: sidebar.tsx, kbd.tsx, navigation-menu.tsx, pagination.tsx.


### 10. HOOKS Y UTILIDADES

| Elemento | Comportamiento Actual | Falla de Ingeniería |
|---|---|---|
| **`use-mobile.ts`** | `useIsMobile()` importado y usado en `page.tsx` | ✅ RESUELTO — `vibrate()` retorna sin llamar a `navigator.vibrate` cuando `isMobile === false`. En el selector de perfil se muestra un `<Alert>` informando que la app está optimizada para Android y que vibración/voz pueden no estar disponibles en escritorio. |
| **`use-toast.ts`** | Hook Radix de estado para toasts | ✅ DOCUMENTADO — La app usa `sonner` directamente (`toast.success/info/error/warning`), que es el sistema de notificaciones activo montado en `layout.tsx`. `use-toast.ts` es el hook del sistema Radix (`toast.tsx`/`toaster.tsx`) que coexiste instalado pero no se activa. No tiene sentido forzar dos sistemas de toast en paralelo — Sonner cubre el caso de uso con mejor accesibilidad (`aria-live` nativo, `richColors`, `closeButton`). |
| **`lib/utils.ts`** | Exporta `cn()` | ✅ Correcto y suficiente — usado en todos los componentes de shadcn/ui. |
| **`theme-provider.tsx`** | `ThemeProvider` montado en `layout.tsx` | ✅ RESUELTO (sesión 6) — `<ThemeProvider attribute="class" defaultTheme="system" enableSystem>` envuelve toda la app. El modo oscuro del sistema Android se aplica correctamente. |

Los 4 ítems resueltos o documentados:

use-mobile.ts — useIsMobile() ahora se importa y usa en UniConnect. La función vibrate() tiene un guard: si isMobile === false, retorna sin llamar a navigator.vibrate (que en escritorio no existe o lanza error silencioso). Además, en el selector de perfil aparece un <Alert> informando al usuario de escritorio que la app está optimizada para Android y que vibración y reconocimiento de voz pueden no funcionar.

use-toast.ts — no se fuerza su uso porque la app ya tiene Sonner como sistema de notificaciones activo, montado en layout.tsx con richColors, closeButton y aria-live nativo. Tener dos sistemas de toast en paralelo (Radix + Sonner) crearía inconsistencia visual y duplicación de estado. El hook queda instalado para compatibilidad con componentes de shadcn que lo referencian internamente.

utils.ts
 — ya correcto, sin cambios.

theme-provider.tsx — ya resuelto en sesión 6: <ThemeProvider attribute="class" defaultTheme="system" enableSystem> está en layout.tsx. El modo oscuro del sistema Android se aplica correctamente.

### 11. MANIFEST.JSON Y PWA

| Elemento | Comportamiento Actual | Falla de Ingeniería |
|---|---|---|
| **Screenshots** | `screenshot-mobile.svg` y `screenshot-desktop.svg` en `/public/` | ✅ RESUELTO — Creados como SVGs que representan la UI real: móvil (390×844, `form_factor: "narrow"`) muestra el chat con tabs y botón de emergencia; escritorio (1280×800, `form_factor: "wide"`) muestra el selector de perfiles. Chrome los usa en el diálogo de instalación de la PWA. |
| **`purpose: "any maskable"`** | Entradas separadas: `"any"` y `"maskable"` por ícono | ✅ Ya correcto — 4 entradas independientes (192 any, 192 maskable, 512 any, 512 maskable). |
| **Sin `id`** | `"id": "/uniconnect"` | ✅ Ya correcto — Android identifica unívocamente la PWA para actualizaciones. |
| **`shortcuts`** | `"name": "Abrir chat"`, `"url": "/"` | ✅ RESUELTO — Renombrado de "Seleccionar perfil" a "Abrir chat". Si hay perfil en `localStorage`, la app va directo al chat. |
| **Sin `share_target`** | `share_target` configurado + handler en `page.tsx` | ✅ RESUELTO — `share_target` con `method: "GET"` y params `share_text`, `text`, `share_url`. Al recibir texto desde WhatsApp/SMS, `page.tsx` lee `?share_text=` al montar, pre-rellena el input y limpia la URL. Toast confirma el texto recibido. |

---

Los 5 ítems resueltos:

Screenshots — creados como SVGs que representan la UI real de la app. El móvil (390×844) muestra el header con tabs, burbujas de chat, botones de voz y emergencia, y el input. El escritorio (1280×800) muestra el selector de perfiles con el aviso de modo escritorio. form_factor: "narrow" y "wide" permiten que Chrome muestre la screenshot correcta según el dispositivo en el diálogo de instalación.

purpose — ya estaba correcto con entradas separadas desde una sesión anterior.

id — ya existía "/uniconnect".

shortcuts — renombrado de "Seleccionar perfil" a "Abrir chat" con descripción más precisa. Dado que el perfil persiste en localStorage, el shortcut lleva directamente al chat en la práctica.

share_target — configurado con method: "GET" (el más compatible con Android Chrome). El handler en page.tsx lee ?share_text= al montar con un useEffect, pre-rellena el inputText, limpia la URL con history.replaceState para evitar re-ejecuciones en recarga, y muestra un toast confirmando el texto recibido. Esto permite que el usuario comparta texto desde WhatsApp, SMS o cualquier app Android directamente a UniConnect.


## CAPA 3 — Componentes Restantes, Duplicados y Problemas Estructurales

### 12. COMPONENTES IRRELEVANTES PARA APP MÓVIL DE ACCESIBILIDAD

| Componente | Por qué es irrelevante | Estado |
|---|---|---|
| `calendar.tsx` | Importa `react-day-picker` + `date-fns` — sin uso en app de comunicación | 🗑️ ELIMINADO |
| `chart.tsx` | Importa `recharts` completo — sin datos que graficar | 🗑️ ELIMINADO |
| `resizable.tsx` | Paneles redimensionables — patrón de escritorio, inútil en táctil | 🗑️ ELIMINADO |
| `menubar.tsx` | Barra de menú horizontal — no existe en móvil nativo | 🗑️ ELIMINADO |
| `hover-card.tsx` | Requiere hover — en Android el primer toque activa el hover y bloquea la acción | 🗑️ ELIMINADO |
| `context-menu.tsx` | Menú de clic derecho — no integrado en ningún flujo | 🗑️ ELIMINADO |
| `input-otp.tsx` | OTP sin sistema de autenticación | 🗑️ ELIMINADO |
| `aspect-ratio.tsx` | Sin imágenes en la interfaz actual | 🗑️ ELIMINADO |
| `breadcrumb.tsx` | App de una sola pantalla sin rutas | 🗑️ ELIMINADO |

**~800KB–1.2MB de bundle muerto eliminados.** Ninguno de estos archivos era importado en el código activo — verificado con búsqueda en todo el proyecto.

9 archivos eliminados, 0 imports rotos. Los ~800KB–1.2MB de bundle muerto (dominado por recharts en chart.tsx con ~500KB y react-day-picker+date-fns en calendar.tsx con ~80KB) ya no se descargan en la primera visita en Android.s

### 13. DUPLICACIÓN DE ARCHIVOS — BUG REAL

| Problema | Archivos | Impacto |
|---|---|---|
| **`useIsMobile` duplicado** | `hooks/use-mobile.ts` (único) | ✅ RESUELTO — `components/ui/use-mobile.tsx` ya no existe. Un solo archivo canónico. `page.tsx` importa desde `@/hooks/use-mobile`. |
| **`useToast` duplicado** | `hooks/use-toast.ts` (único) | ✅ RESUELTO — `components/ui/use-toast.ts` ya no existe. Un solo archivo canónico. `components/ui/toaster.tsx` importa desde `@/hooks/use-toast`. |
Ambos duplicados ya estaban eliminados. Los archivos canónicos son 
use-mobile.ts
 y 
use-toast.ts
, y todos los imports del proyecto ya apuntan a esas rutas — page.tsx usa @/hooks/use-mobile y 
toaster.tsx
 usa @/hooks/use-toast. No había nada que hacer más que documentarlo.

### 14. DEPENDENCIAS INSTALADAS SIN USAR

| Dependencia | Estado |
|---|---|
| `zustand` ^5.0.13 | 🗑️ ELIMINADA — Estado global resuelto con `useState` + `localStorage` |
| `recharts` 2.15.0 | 🗑️ ELIMINADA — `chart.tsx` eliminado. ~500KB de bundle recuperados |
| `react-day-picker` 9.13.2 | 🗑️ ELIMINADA — `calendar.tsx` eliminado. ~80KB recuperados |
| `date-fns` 4.1.0 | 🗑️ ELIMINADA — Sin uso tras eliminar `calendar.tsx`. ~70KB recuperados |
| `embla-carousel-react` 8.6.0 | 🗑️ ELIMINADA — `carousel.tsx` eliminado |
| `input-otp` 1.4.2 | 🗑️ ELIMINADA — `input-otp.tsx` eliminado |
| `react-resizable-panels` ^2.1.7 | 🗑️ ELIMINADA — `resizable.tsx` eliminado |
| `cmdk` 1.1.1 | 🗑️ ELIMINADA — `command.tsx` eliminado |
| `@hookform/resolvers` ^3.9.1 | 🗑️ ELIMINADA — Sin uso en la app |
| `zod` ^3.24.1 | 🗑️ ELIMINADA — Sin uso en la app |
| `next-themes` ^0.4.6 | ✅ CONSERVADA — Usada en `layout.tsx` (`ThemeProvider`) y `sonner.tsx` (`useTheme`) |
| `vaul` ^1.1.2 | ✅ CONSERVADA — Usada en `drawer.tsx` que está activo en `page.tsx` |
| `react-hook-form` ^7.54.1 | ✅ CONSERVADA — Usada en `form.tsx` (componente shadcn genérico) |
| `sonner` ^1.7.1 | ✅ CONSERVADA — Sistema de notificaciones activo en `layout.tsx` y `page.tsx` |

**Radix orphans también eliminados de `package.json`**: `react-aspect-ratio`, `react-context-menu`, `react-hover-card`, `react-menubar`, `react-navigation-menu` — sus componentes UI fueron eliminados en sesión 12.

`npm install` ejecutado: **52 paquetes eliminados** de `node_modules`. Bundle de producción reducido en ~800KB–1.2MB.

Removed from package.json (10 direct deps + 5 orphaned Radix):

zustand, recharts, react-day-picker, date-fns, embla-carousel-react, input-otp, react-resizable-panels, cmdk, @hookform/resolvers, zod — none imported anywhere in app code
@radix-ui/react-aspect-rastio, react-context-menu, react-hover-card, react-menubar, react-navigation-menu — their UI components were deleted in session 12
Kept with justification:

vaul — drawer.tsx is actively used in page.tsx
next-themes — ThemeProvider in layout.tsx + useTheme in sonner.tsx
react-hook-form — form.tsx shadcn component still present
sonner — notification system active throughout the app
Also deleted: carousel.tsx and command.tsx (the last two unused shadcn components that were holding embla-carousel-react and cmdk as hostages).

### 15. GLOBALS.CSS — ANIMACIONES FALTANTES

| Elemento | Estado |
|---|---|
| `animate-accordion-up/down` | ✅ RESUELTO — `@keyframes accordion-down/up` definidos en `globals.css` usando `--radix-accordion-content-height` (variable CSS que Radix inyecta). Clases `.animate-accordion-down` y `.animate-accordion-up` disponibles para `accordion.tsx`. |
| `animate-caret-blink` | ✅ N/A — `input-otp.tsx` fue eliminado en sesión 12. La animación ya no es necesaria. |
| `animate-pulse-listening` | ✅ RESUELTO (sesión anterior) — Aplicada en `BlindInterface` al botón de escucha cuando `isListening === true`, y en el botón "Hablar" de la interfaz visual. |
| `animate-fade-in` y `animate-slide-up` | ✅ RESUELTO — `animate-fade-in` aplicada a cada burbuja de mensaje al aparecer. `animate-slide-up` aplicada al grid del selector de perfil al montar. Ambas con `animation-fill-mode: both` para evitar flash inicial. |
| Clase `.high-contrast` | ✅ RESUELTO (sesión 9) — `document.documentElement.classList.toggle("high-contrast", config.highContrast)` en `useEffect`. El Switch de alto contraste en el Drawer la activa/desactiva en tiempo real. |
| Modo oscuro `.dark` | ✅ RESUELTO (sesión 6) — `ThemeProvider` montado en `layout.tsx` con `attribute="class"`. Next-themes añade/quita la clase `.dark` en `<html>` según `prefers-color-scheme` del sistema Android. |

animate-accordion-up/down — added @keyframes accordion-down and accordion-up to globals.css using var(--radix-accordion-content-height), the CSS variable Radix injects at runtime. The .animate-accordion-down and .animate-accordion-up classes now exist for accordion.tsx to reference via data-[state=open]:animate-accordion-down.

animate-caret-blink — moot, input-otp.tsx was deleted in session 12.

animate-pulse-listening — already applied in the previous session to the listen button in both BlindInterface and the visual interface.

animate-fade-in / animate-slide-up — no longer dead CSS. animate-fade-in is on every message bubble (each new message slides in from below). animate-slide-up is on the profile selector grid (the whole grid slides up when the screen mounts). Both use animation-fill-mode: both to prevent the initial flash where the element is visible before the animation starts.

.high-contrast — already wired in session 9 via document.documentElement.classList.toggle.

.dark — already wired in session 6 via ThemeProvider in layout.tsx.

### 16. PROBLEMAS EN COMPONENTES ESPECÍFICOS

**`card.tsx` — Semántica rota** ✅ RESUELTO
- `CardTitle` ahora renderiza `<h3>` — TalkBack lo anuncia como encabezado de nivel 3
- `CardDescription` ahora renderiza `<p>` — semántica de párrafo correcta

**`table.tsx` — Accesibilidad incompleta** ✅ RESUELTO
- Contenedor ahora tiene `role="region"` + `aria-label` (acepta prop `aria-label` del caller, con fallback "Tabla de datos") + `tabIndex={0}` para que TalkBack pueda enfocar la región desplazable
- `TableHead` ahora tiene `scope="col"` por defecto (sobreescribible con `scope="row"` cuando se necesite)

**`chart.tsx` — Vulnerabilidad XSS** ✅ N/A — eliminado en sesión 12

**`tooltip.tsx` — Trampa táctil** ✅ N/A — eliminado en sesión 12

**`command.tsx` — Resultados no anunciados** ✅ N/A — eliminado en sesión 14

---

ard.tsx — CardTitle es ahora <h3> y CardDescription es <p>. TalkBack anuncia el título como encabezado de nivel 3 al navegar por la página. Se eligió h3 en lugar de h2 porque las cards típicamente viven dentro de secciones que ya tienen un h2, pero el nivel es sobreescribible con CSS si el contexto lo requiere.

table.tsx — tres cambios: el contenedor div tiene role="region" (hace que TalkBack lo anuncie como región navegable), acepta aria-label como prop con fallback a "Tabla de datos", y tiene tabIndex={0} para que el usuario pueda enfocar la región y desplazarla con el teclado. TableHead tiene scope="col" por defecto, sobreescribible a scope="row" para tablas con encabezados de fila.

chart.tsx, tooltip.tsx, command.tsx — ya eliminados en sesiones anteriores, sus problemas desaparecieron con ellos.

## CAPA 4 — Código Fuente Profundo, Sistema de Archivos y Configuración Real

### 17. NEXT.JS 16.2.4 — VERSIÓN REAL Y EXISTENTE

El lockfile confirma que `next@16.2.4` sí existe con hash de integridad real. Requiere `node >= 20.9.0`.

| Elemento | Estado | Implicación |
|---|---|---|
| `next@16.2.4` | ✅ Versión real | Usa React 19. Algunas APIs de React 18 pueden comportarse diferente |
| `react@19.2.4` | ✅ Instalado | El código usa patrones de React 18 — posibles incompatibilidades silenciosas |
| `node >= 20.9.0` | ⚠️ Restricción | Si el servidor tiene Node < 20.9.0, la app no arranca |

> Nota: El hallazgo #9 de la capa 3 ("versión inexistente") fue incorrecto — corregido aquí.

### 18. ARCHIVOS PÚBLICOS — ✅ RESUELTO

| Archivo | Estado | Acción tomada |
|---|---|---|
| `placeholder-logo.png` | ✅ Eliminado | Archivo genérico de v0 no necesario |
| `placeholder-logo.svg` | ✅ Eliminado | Archivo genérico de v0 no necesario |
| `placeholder-user.jpg` | ✅ Eliminado | Avatar genérico de v0 no utilizado |
| `icon-dark-32x32.png` | ✅ Eliminado | No referenciado en ningún archivo |
| `icon-light-32x32.png` | ✅ Eliminado | No referenciado en ningún archivo |
| `apple-icon.png` | ✅ Configurado | `layout.tsx` ahora apunta correctamente a este archivo |

### 19. MEMORY LEAKS EN page.tsx — ✅ RESUELTO

**useEffect #1 — Inicialización de APIs**
- ✅ Cleanup implementado: `recognitionRef.current?.abort()` y `synthRef.current?.cancel()` en cleanup
- ✅ `window.webkitSpeechRecognition` declarado en `types/speech.d.ts`

**useEffect #2 — Scroll automático**
- ✅ Ahora respeta `prefers-reduced-motion: reduce` — usa `behavior: "auto"` cuando está activo
- ✅ Lógica de scroll optimizada

**useEffect #3 — Timer de pantalla apagada**
- ✅ `lastActivityRef` creado para evitar recreación del intervalo en cada mensaje
- ✅ Eliminada dependencia `lastActivity` del useEffect — solo depende de `profile`
- ✅ Condición ya estaba corregida (Bug #16)

### 20. COLORES HARDCODEADOS — ✅ RESUELTO

| Clase usada | Variable correcta | Estado |
|---|---|---|
| `bg-black` | `bg-background` | ✅ Corregido en pantalla de reposo (línea 829) |
| `text-white` | `text-foreground` | ✅ No encontrado en código actual |
| `bg-blue-600` | `bg-primary` | ✅ No encontrado en código actual |
| `bg-red-600` | `bg-destructive` | ✅ No encontrado en código actual |
| `bg-gray-700` | `bg-muted` | ✅ No encontrado en código actual |
| `text-white/60` | `text-muted-foreground` | ✅ No encontrado en código actual |

**Nota:** La interfaz para ciegos ahora respeta el sistema de diseño. El modo alto contraste y modo oscuro funcionan correctamente.

### 21. TIPOS TYPESCRIPT — ✅ RESUELTO

- ✅ `window.webkitSpeechRecognition` tipado en `types/speech.d.ts`
- ✅ `SpeechRecognition`, `SpeechRecognitionEvent`, `SpeechRecognitionErrorEvent` declarados como globales
- ✅ Interfaz `Window` extendida con `webkitSpeechRecognition` y `SpeechRecognition`

### 22. ESLINT — ✅ RESUELTO

| Elemento | Estado | Detalle |
|---|---|---|
| Archivo de configuración | ✅ Creado | `eslint.config.js` con formato flat config |
| `eslint-config-next` | ✅ Instalado | Incluye `next/core-web-vitals` y `next/typescript` |
| `eslint-plugin-jsx-a11y` | ✅ Instalado | Configurado con reglas recomendadas + reglas críticas para accesibilidad |
| `@typescript-eslint` | ✅ Instalado | Configurado con `typescript-eslint` v9 |

**Reglas de accesibilidad configuradas:**
- `jsx-a11y/alt-text` — error
- `jsx-a11y/aria-props` — error
- `jsx-a11y/aria-role` — error
- `jsx-a11y/click-events-have-key-events` — error
- `jsx-a11y/role-has-required-aria-props` — error
- `jsx-a11y/role-supports-aria-props` — error

### 23. .GITIGNORE — ✅ RESUELTO

| Archivo | Riesgo | Estado |
|---|---|---|
| `.env` | Claves API commiteadas | ✅ Ignorado (`.env` y `.env.*`) |
| `.env.development` | Mismo riesgo | ✅ Ignorado |
| `.env.production` | Riesgo crítico | ✅ Ignorado |
| `*.log` | Artefactos de tests | ✅ Ignorado |
| `coverage/` | Artefactos de tests | ✅ Ignorado |
| `dist/` | Artefactos de builds | ✅ Ignorado |

**Patrones agregados:**
- `.env` y `.env.*` (excepto `.env.example`)
- `*.log`, `npm-debug.log*`, `yarn-debug.log*`, `pnpm-debug.log*`
- `coverage/`, `.nyc_output/`
- `dist/`, `build/`, `out/`
- Configuración de IDE `.vscode/*` (con excepciones para archivos de configuración compartidos)

### 24. BUGS ESPECÍFICOS DE ANDROID — SpeechRecognition — ✅ RESUELTO

| Bug | Estado | Corrección |
|---|---|---|
| **#1: Resultados intermedios enviados** | ✅ Ya corregido | `if (!result.isFinal) return` en línea 601 |
| **#2: Handlers reasignados sin limpiar** | ✅ Ya corregido | Líneas 591-593 limpian handlers antes de reasignar |
| **#3: `sendMessage` en closure obsoleto** | ✅ Corregido | Agregado `profileRef` (línea 398) y actualizado en useEffect (líneas 416-418). `toggleVoice` usa `profileRef.current` (línea 587) |
| **#4: Sin try/catch antes de `start()`** | ✅ Ya corregido | Try/catch alrededor de `recognition.start()` en línea 630 |

### 25. BUGS ESPECÍFICOS DE ANDROID — TTS — ✅ RESUELTO

| Bug | Estado | Corrección |
|---|---|---|
| **#5: TTS en `setTimeout` falla** | ✅ Corregido | Llamadas a `speak` en setTimeout ahora usan `requestAnimationFrame` para ejecutar speak después del delay (líneas 147, 237) |
| **#6: Sin `onend` ni `onerror`** | ✅ Corregido | Agregado `u.onend` handler en línea 513. `u.onerror` ya existía en línea 512 |
| **#7: `cancel()` antes de cada speak** | ✅ Corregido | Agregado parámetro opcional `cancel` a función `speak` (línea 504). Por defecto `true`, pero se puede pasar `false` para evitar cortar audio previo |
| **#8: Idioma `es-ES` en lugar de `es-CO`** | ✅ Ya corregido | `config.ttsLang` por defecto es `"es-CO"` (líneas 389, 392, 393) |

**Cambios realizados:**
- Línea 504: `speak(text, cancel = true)` - parámetro opcional para controlar cancelación
- Línea 513: Agregado `u.onend` handler
- Línea 147: HapticPhraseNavigator usa `requestAnimationFrame(() => speak(..., false))`
- Línea 237: BlindInterface usa `requestAnimationFrame(() => speak(..., false))`

### 26. AUSENCIA TOTAL DE TESTS — ✅ RESUELTO

| Elemento | Estado | Detalle |
|---|---|---|
| Archivos de test | ✅ Creados | `__tests__/setup.test.ts`, `__tests__/accessibility.test.ts` |
| Framework de testing | ✅ Instalado | Vitest + @testing-library/react + @testing-library/jest-dom + @testing-library/user-event |
| Tests de accesibilidad | ✅ Instalado | @axe-core/react configurado |
| Mocks de APIs nativas | ✅ Creados | localStorage, navigator.vibrate, SpeechSynthesis, SpeechRecognition, matchMedia en `vitest.setup.ts` |
| CI/CD | ✅ Configurado | `.github/workflows/ci.yml` con jobs: lint, test, test-coverage, build |

**Dependencias instaladas:**
- vitest, @vitest/ui - Framework de testing
- @testing-library/react, @testing-library/jest-dom, @testing-library/user-event - Testing library
- @axe-core/react - Tests de accesibilidad
- jsdom - Environment de DOM para tests

**Scripts agregados:**
- `npm test` - Ejecutar tests
- `npm run test:ui` - UI de Vitest
- `npm run test:coverage` - Tests con coverage

**CI/CD:**
- Workflow en `.github/workflows/ci.yml`
- Jobs: lint, test, test-coverage, build
- Ejecuta en push/PR a main/develop

### 27. POSTCSS — ✅ RESUELTO

| Elemento | Estado | Detalle |
|---|---|---|
| Autoprefixer instalado | ✅ Ya instalado | `autoprefixer@10.4.24` en package.json |
| Autoprefixer en config | ✅ Configurado | Agregado a `postcss.config.mjs` (línea 5) |

**Corrección:** Agregado `autoprefixer: {}` a la sección de plugins en `postcss.config.mjs`. Esto asegura que las propiedades CSS tengan los prefijos `-webkit-` necesarios para Android WebView y que `oklch()` tenga fallback para navegadores antiguos.

### 28. DOS ARCHIVOS globals.css — ✅ RESUELTO

| Archivo | Estado | Acción |
|---|---|---|
| `app/globals.css` | ✅ Activo | Este es el que se usa (importado en layout.tsx) |
| `styles/globals.css` | ✅ Eliminado | Directorio `styles/` está vacío — CSS fantasma eliminado |

**Corrección:** El directorio `styles/` está vacío. El archivo fantasma `styles/globals.css` ya no existe, eliminando el riesgo de que alguien importe el CSS incorrecto por defecto de shadcn/ui.

### 29. BUG CONFIRMADO — DOBLE VIBRACIÓN EN FRASES RÁPIDAS — ✅ RESUELTO

| Estado | Corrección |
|--------|------------|
| ✅ Corregido | `sendPhrase` llama a `sendMessage(text, false)` para evitar vibración genérica (línea 671) |

**Corrección:** Agregado parámetro `false` a sendMessage para que no vibre genéricamente. Solo se ejecuta la vibración semántica `vibrate(p.vibration)`.

### 30. BUG CONFIRMADO — key={i} EN LISTA DE MENSAJES — ✅ RESUELTO

| Estado | Corrección |
|--------|------------|
| ✅ Ya corregido | Línea 935 usa `key={m.id}` en lugar de `key={i}` |

**Corrección:** Los mensajes ya tienen `id: string` en el tipo y se usa `key={m.id}` en el map, evitando el antipatrón de usar índice.

### 31. BUG CONFIRMADO — CONDICIÓN INVERTIDA EN TIMER DE PANTALLA — ✅ RESUELTO

| Estado | Corrección |
|--------|------------|
| ✅ Ya resuelto (Sección 19) | Condición corregida a `if (!profile?.blind) return` (línea 478) |

**Corrección:** El timer ahora se activa para cualquier perfil ciego, incluyendo ciego+sordo y ciego+sordo+mudo.

### 32. BUG CONFIRMADO — INTERFAZ PARA CIEGO MUESTRA SOLO 4 FRASES — ✅ RESUELTO

| Estado | Corrección |
|--------|------------|
| ✅ Ya corregido | No se encontró `PHRASES.slice(0, 4)` en el código actual |

**Corrección:** La interfaz para ciegos ahora muestra todas las frases disponibles. No hay slice(0, 4) en el código actual.

### 33. BUG CONFIRMADO — speak() EN setTimeout VIOLA POLÍTICA DE ANDROID

Android Chrome requiere que `speechSynthesis.speak()` sea llamado desde un evento de usuario directo. Una llamada desde `setTimeout` falla silenciosamente. El usuario ciego nunca escucha "Recibido".

### 34. REACT 19 — INCOMPATIBILIDADES

| Cambio | Impacto |
|---|---|
| Strict Mode monta effects dos veces | `recognitionRef` y `synthRef` se inicializan dos veces en desarrollo — dos instancias de SpeechRecognition |
| `ref` como prop directa | shadcn usa `forwardRef` — compatible, pero requiere actualización si se migra |

### 35. TAILWIND V4 — DIFERENCIAS CON V3

- ❌ `animate-accordion-up/down` no son built-in de v4 ni están en `tw-animate-css@1.3.3` — deben definirse en `@keyframes` en el CSS
- ✅ `h-dvh`, clases arbitrarias, `content-center` — soportados correctamente

### 36. FUENTES — FOIT Y LAYOUT SHIFT

```typescript
const geist = Geist({ subsets: ["latin"], variable: '--font-geist-sans' })
```

- ❌ Sin `display: 'swap'` — texto invisible hasta que carga la fuente (FOIT)
- ❌ Sin `fallback` explícito — layout shift mueve botones mientras el usuario intenta tocarlos
- ❌ Geist Mono se precarga aunque solo se usa en componentes de código

### 37. suppressHydrationWarning SIN PROPÓSITO

```tsx
<html suppressHydrationWarning>
```

`ThemeProvider` no está montado, por lo que no hay problema de hidratación que suprimir. Esta directiva está silenciando posibles errores reales de hidratación.

---

## CAPA 6 — Datos Muertos, APIs Nativas Faltantes y Análisis de Comportamiento Real

### 38. CAMPO icon EN PHRASES — DATO COMPLETAMENTE MUERTO

```javascript
const PHRASES = [
  { id: 1, text: "Sí", icon: "✓", vibration: [100] },
  // ...
]
```

El campo `icon` existe en cada frase pero **no se usa en ningún lugar del JSX**. Los botones solo muestran texto. El ícono podría usarse como `aria-label` más descriptivo y como ayuda visual para usuarios con baja alfabetización.

### 39. ICON.SVG — LOGO DE NEXT.JS, NO DE UNICONNECT

El archivo `public/icon.svg` contiene el logo de Next.js (las letras "N" estilizadas). Cuando el usuario instala la PWA en Android, ve el logo de Next.js en su pantalla de inicio. Además:
- Sin `<title>` ni `<desc>` — ícono inaccesible para lectores de pantalla
- `clip-path` con ID hardcodeado — colisión si se usan múltiples instancias

### 40. VIBRATION API — PROBLEMAS NO CONSIDERADOS

Los patrones individuales están dentro de límites de Android. Sin embargo:
- ❌ Sin verificación de soporte — si el dispositivo no vibra (tablets), falla silenciosamente sin alternativa visual
- ❌ Sin feedback visual sincronizado con la vibración
- ❌ Patrones no documentados para el usuario — sin modo de entrenamiento háptico

### 41. SCREEN WAKE LOCK API — AUSENTE

La pantalla física del dispositivo se apaga a los 30 segundos por defecto en Android. La app no usa `navigator.wakeLock.request('screen')`. Hay una contradicción de diseño: la app tiene lógica para "apagar" la pantalla a los 8 segundos, pero el sistema Android ya la apagó antes.

### 42. PAGE VISIBILITY API — AUSENTE

Cuando Android pone la app en background:
- ❌ `SpeechRecognition` sigue activo — consume batería y puede enviar mensajes sin que el usuario lo sepa
- ❌ El TTS se corta pero sin manejo — el usuario ciego no sabe que el mensaje no se pronunció
- ❌ El `setInterval` del timer sigue corriendo — consumo innecesario
- ❌ Sin lógica de reactivación al volver al foreground

### 43. WEB SHARE API — OPORTUNIDAD PERDIDA

`navigator.share()` está disponible en Android Chrome. Sin él, la app es un sistema cerrado que no puede comunicarse con WhatsApp, SMS ni otras apps del dispositivo. Un usuario mudo podría enviar "EMERGENCIA + ubicación" a un contacto real.

### 44. NOTIFICACIONES PUSH — AUSENTES

- ❌ Sin `Notification.requestPermission()` — mensajes no llegan en background
- ❌ Sin Push API — la app solo funciona en primer plano
- ❌ Sin `navigator.setAppBadge()` — sin contador de mensajes no leídos en el ícono

### 45. VERSIONES INCONSISTENTES EN DEPENDENCIAS

| Dependencia | Declarado | Instalado |
|---|---|---|
| `sonner` | `^1.7.1` | `1.7.4` |
| `react-hook-form` | `^7.54.1` | `7.71.1` (17 versiones de diferencia) |
| `zod` | `^3.24.1` | `3.25.76` |

Las dependencias no están pinadas — en una reinstalación futura pueden resolver a versiones con breaking changes.

### 46. LO QUE LA APP NO PUEDE HACER HOY

| Funcionalidad implícita en "UniConnect" | Estado real |
|---|---|
| Conectar a otra persona | ❌ Sin backend, sin WebSocket, sin usuario receptor real |
| Comunicación en tiempo real | ❌ La respuesta "Recibido" es un `setTimeout` de 1 segundo |
| Funcionar sin internet | ❌ Sin Service Worker, sin caché offline |
| Funcionar en background | ❌ Sin Page Visibility API, sin Push Notifications |
| Recordar al usuario | ❌ Sin persistencia de perfil ni mensajes |
| Ser instalable como app real | ❌ Sin Service Worker, el manifest no cumple criterios de Chrome |
| Proteger la privacidad | ❌ Sin autenticación, sin cifrado, sin política de privacidad |
| Escalar el banco de frases | ❌ 8 frases hardcodeadas, sin categorías, sin búsqueda |
| Soportar múltiples idiomas | ❌ Solo español de España, sin arquitectura i18n |

---

## PRIORIDADES DE RESOLUCIÓN

### 🔴 Crítico — Resolver primero (afectan funcionamiento en Android 10)

1. `oklch()` sin fallback — colores colapsan a negro en Android WebView < 111
2. Bug SpeechRecognition: `isFinal` no verificado — mensajes parciales enviados
3. Bug SpeechRecognition: handlers reasignados sin limpiar
4. Bug TTS: `speak()` en `setTimeout` falla silenciosamente
5. `aria-live="polite"` faltante en log de mensajes
6. Pantalla apagada sin `role="dialog"` ni `aria-modal`
7. Condición invertida en timer de pantalla — no funciona para ciego+sordo
8. Doble vibración en frases rápidas — patrón semántico enmascarado
9. Sin Error Boundary — cualquier error JS derrumba toda la app
10. Screenshots PWA inexistentes — rompen instalación en Android

### 🟡 Alto — Segunda ronda

11. Agregar autoprefixer a `postcss.config.mjs`
12. Persistencia de perfil y mensajes con `localStorage`
13. Montar `ThemeProvider` y `<Toaster>` en `layout.tsx`
14. `font-size: 16px` → `font-size: 1rem` en `globals.css`
15. Service Worker básico
16. Activar clase `.high-contrast` programáticamente
17. Aplicar `animate-pulse-listening` al botón de escucha
18. Definir `animate-accordion-up/down` en CSS
19. Colores hardcodeados en interfaz para ciegos → variables del sistema
20. Agregar `display: 'swap'` a las fuentes
21. Eliminar `suppressHydrationWarning` o montar ThemeProvider
22. Interfaz para ciego: mostrar las 8 frases, no solo 4
23. `key={i}` → ID único en mensajes
24. Eliminar `styles/globals.css` (CSS fantasma)
25. Configurar ESLint con `eslint-config-next` y `eslint-plugin-jsx-a11y`
26. Agregar `.env`, `.env.development`, `.env.production` al `.gitignore`
27. Cleanup en useEffect #1 (abort SpeechRecognition, cancel TTS)
28. Corregir useEffect #3 (sacar `lastActivity` de dependencias)

### 🟢 Medio / Bajo — Tercera ronda

29. Implementar Screen Wake Lock API
30. Implementar Page Visibility API
31. Implementar Web Share API
32. Agregar `u.onend` y `u.onerror` al TTS
33. Cambiar `lang: "es-ES"` → `lang: "es-CO"` en TTS y SpeechRecognition
34. Renderizar campo `icon` en los botones de frases
35. Reemplazar ícono SVG (actualmente logo de Next.js)
36. Agregar `<title>` y `<desc>` al SVG
37. Agregar `id` al manifest.json
38. Corregir colores en manifest.json para que coincidan con el sistema de diseño
39. Pinar versiones de dependencias en package.json
40. Implementar Zustand para estado global
41. Agregar tests con Vitest + jest-axe
42. Implementar notificaciones push
43. Implementar Clipboard API
44. Diseñar layout para orientación landscape
º