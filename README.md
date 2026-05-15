# UniConnect

![UniConnect](https://raw.githubusercontent.com/Andrewgo12/UniConnect/main/.github/logo.png)

[![License](https://img.shields.io/badge/license-MIT-green)](https://opensource.org/licenses/MIT)
[![Status](https://img.shields.io/badge/status-in%20progress-yellow)](https://github.com/Andrewgo12/UniConnect)
[![PHP](https://img.shields.io/badge/php-8.3-blue)](https://www.php.net/)
[![Next.js](https://img.shields.io/badge/Next.js-15-black)](https://nextjs.org/)

## Resumen / Summary

**UniConnect** es un proyecto de tesis que integra un backend Laravel y un frontend Next.js para crear una plataforma inclusiva de comunicación, mensajería y gestión de emergencias.

Este repositorio contiene el proyecto completo con:

- Backend API REST con Laravel
- Frontend web accesible con Next.js y React
- Funcionalidades de emergencia, accesibilidad, audio, imágenes y mensajería

## Contenido / Table of Contents

- [Resumen / Summary](#resumen--summary)
- [Estado del proyecto / Project status](#estado-del-proyecto--project-status)
- [Arquitectura / Architecture](#arquitectura--architecture)
- [Tecnologías / Technologies](#tecnologías--technologies)
- [Funcionalidades principales / Main features](#funcionalidades-principales--main-features)
- [Implementación real / Actual implementation](#implementación-real--actual-implementation)
- [API principal / API overview](#api-principal--api-overview)
- [Instalación / Installation](#instalación--installation)
- [Ejecución local / Local run](#ejecución-local--local-run)
- [Pruebas / Testing](#pruebas--testing)
- [Despliegue / Deployment](#despliegue--deployment)
- [Tesis / Thesis project](#tesis--thesis-project)
- [Contribuciones / Contributions](#contribuciones--contributions)
- [Licencia / License](#licencia--license)
- [Contacto / Contact](#contacto--contact)

---

## Estado del proyecto / Project status

**Este proyecto es un trabajo de tesis en progreso.**

### Avance actual / Current progress

- Backend: 80% desarrollado
  - Autenticación completa con Laravel Sanctum
  - Gestión de usuarios, perfiles y accesibilidad
  - Módulos de emergencias, mensajes, conversaciones y registro médico
  - Multimedia: audio, imágenes y texto a voz / voz a texto
  - Analytics y reportes de uso

- Frontend: 70% desarrollado
  - Interfaz móvil accesible y táctil
  - Navegación háptica y soporte para usuarios con baja visión
  - Componentes UI con Radix, Tailwind CSS y Sonner
  - Next.js + TypeScript + React

---

## Arquitectura / Architecture

| Carpeta | Descripción |
|---|---|
| `backend/` | API REST con Laravel, lógica de negocio, migraciones y pruebas backend |
| `frontend/` | App web con Next.js, páginas React y UI accesible |
| `.github/` | Configuración de GitHub, acciones o plantillas (si existen) |
| `MISSING_FEATURES.md` | Lista de funcionalidades pendientes |
| `problemas copy.md` | Notas internas y registros de problemas |

---

## Tecnologías / Technologies

### Backend

- PHP 8.3
- Laravel 13.7
- Laravel Sanctum
- Composer
- SQLite por defecto (configurable a MySQL/PostgreSQL)
- Vite
- PHPUnit

### Frontend

- Next.js 15
- React 19
- TypeScript
- Tailwind CSS 4
- Radix UI
- Sonner
- React Hook Form
- Vitest
- ESLint

### Dev tools

- `pnpm` (recomendado)
- `npm` compatible
- `composer`

---

## Funcionalidades principales / Main features

- Registro, login, logout y recuperación de contraseña
- Autenticación API con Laravel Sanctum
- Perfil de usuario con opciones de accesibilidad personalizadas
- Fichas de frases rápidas y envío de frases como mensaje
- Emergencias con activación, historial, estado activo y alertas instantáneas
- Mensajes, conversaciones y envío de mensajes por conversación
- Gestión de registro médico, medicamentos y citas
- Subida y gestión de imágenes de perfil y recursos multimedia
- Audio: texto a voz (TTS), voz a texto (STT) y grabación de mensajes
- API de lenguaje de señas con categorías, signos básicos y signos de emergencia
- Reportes de analíticas de mensajes, emergencias y accesibilidad
- Interfaz accesible móvil con alto contraste, voz, vibración y navegación háptica
- Persistencia de perfil, mensajes, frases personalizadas y configuración en localStorage
- Web Share Target para recibir texto compartido desde otras aplicaciones
- Wake lock y gestión de visibilidad para mejorar la experiencia móvil

---

## Implementación real / Actual implementation

### Backend real / Real backend

El backend está construido con Laravel y agrupa la lógica en controladores y servicios reales:

- `AuthController` para registro, login, logout, usuario actual y reinicio de contraseña
- `PhraseController` para listar frases y frases por defecto
- `EmergencyController` para crear, listar, actualizar, ver, eliminar y activar emergencias
- `MessageController` para listar mensajes, crear mensajes, enviar frases, ver y actualizar mensajes y obtener mensajes de una conversación
- `UserController` para perfil de usuario, actualización de perfil y ajustes de accesibilidad
- `ConversationController` para conversaciones y participantes
- `SignLanguageController` para categorías y listas de lenguaje de señas
- `AudioController` para TTS, STT, almacenamiento y gestión de audio
- `ImageController` para imágenes de perfil, carga y búsqueda por tipo
- `MedicalController` para registros médicos, medicamentos y citas
- `AnalyticsController` para métricas, mensajes, emergencias y accesibilidad
- `AccessibilityController` para ajustes, recomendaciones y pruebas de accesibilidad

### Frontend real / Real frontend

El frontend incluye una interfaz real comprobada que utiliza:

- `useIsMobile` para distinguir entre móvil y escritorio
- `SpeechRecognition` y `SpeechSynthesis` de la Web Speech API
- TTS configurado en `es-CO` con velocidad ajustable
- Navegación háptica con swipes, tiras de frase y toque largo para enviar
- Función `PROBAR VIBRACIÓN` para verificar salida háptica
- Modo ciego/mudo/sordo con frases por defecto y feedback auditivo/vibratorio
- Guardado en `localStorage` de perfil, mensajes, frases personalizadas y configuración
- Web Share Target para importar texto desde otras apps Android/Chrome
- Wake Lock para evitar que la pantalla se apague durante uso activo
- Clase `high-contrast` para temas de alto contraste
- Componentes UI construidos con Radix, Tailwind y Sonner para accesibilidad

---

## Qué falta / What is missing

Esta sección describe las brechas reales que aún existen en el proyecto.

### Integración frontend-backend
- El frontend actual tiene un flujo de mensajería simulado en `frontend/app/page.tsx` con un `setTimeout` que genera una respuesta "Recibido".
- No hay integración real de API REST en la UI principal, por lo que las rutas del backend no se están llamando desde el cliente.
- No existe un flujo visible de autenticación en la interfaz: no hay login/register ni almacenamiento de token `Authorization`.
- El proyecto depende fuertemente de `localStorage` para persistir perfil, mensajes, frases y configuración, lo que limita el uso multi-dispositivo.

### Backend y seguridad
- Aunque las rutas y controladores existen, no hay comprobación documentada de migraciones ejecutadas con éxito ni seeders poblados.
- Falta validación de producción completa: configuración de CORS, CSRF, CSP, headers HTTPS y rate limiting consistente.
- Las políticas de acceso (`app/Policies/`) y permisos de recursos aún deben verificarse completamente.

### Pruebas y estabilidad
- Los tests frontend son básicos y existen mocks para APIs nativas en `frontend/vitest.setup.ts`.
- No hay pruebas end-to-end que cubran el flujo completo de registro, login, envío de mensaje y emergencia.
- La estabilidad de la lógica de backend sólo se puede confirmar con ejecución real de `composer test` y migraciones.

### Soporte real móvil / Mobile readiness
- La UI táctil es avanzada, pero el backend real no está conectado, así que la experiencia móvil está incompleta.
- El comportamiento de emergencia y de mensajes real debe ser reemplazado por WebSocket o fetch al backend.

---

## Rutas de mejora / Next steps

Estas son las tareas prioritarias para pasar de prototipo a una versión funcional:

1. Implementar el login/register en el frontend y conectar con `/api/v1/auth`.
2. Cambiar el mock de `sendMessage()` en `frontend/app/page.tsx` por llamadas reales al backend o WebSocket.
3. Validar todas las migraciones en `backend` con `php artisan migrate` y revisar cualquier error de base de datos.
4. Configurar CORS/CSRF y seguridad de producción en `backend/config`.
5. Añadir tests E2E o integración para cubrir el flujo de autenticación y mensajería.
6. Revisar `backend/BACKEND_STATUS.md` contra el código real y actualizar el progreso de tesis.

---

## API principal / API overview

La API está disponible en `/api/v1` y ofrece las siguientes rutas reales:

### Autenticación
- `POST /api/v1/auth/register`
- `POST /api/v1/auth/login`
- `POST /api/v1/auth/logout`
- `GET /api/v1/auth/me`
- `POST /api/v1/auth/reset-password`

### Frases
- `GET /api/v1/phrases`
- `GET /api/v1/phrases/defaults`

### Emergencias
- `GET /api/v1/emergencies`
- `POST /api/v1/emergencies`
- `GET /api/v1/emergencies/active`
- `POST /api/v1/emergencies/trigger`
- `GET /api/v1/emergencies/{emergency}`
- `PUT /api/v1/emergencies/{emergency}`
- `DELETE /api/v1/emergencies/{emergency}`

### Mensajería
- `GET /api/v1/messages`
- `POST /api/v1/messages`
- `POST /api/v1/messages/send-phrase`
- `GET /api/v1/messages/{message}`
- `PUT /api/v1/messages/{message}`
- `DELETE /api/v1/messages/{message}`
- `GET /api/v1/conversations/{conversation}/messages`

### Conversaciones
- `GET /api/v1/conversations`
- `POST /api/v1/conversations`
- `GET /api/v1/conversations/{conversation}`
- `PUT /api/v1/conversations/{conversation}`
- `DELETE /api/v1/conversations/{conversation}`
- `POST /api/v1/conversations/{conversation}/participants`
- `DELETE /api/v1/conversations/{conversation}/participants/{user}`
- `PUT /api/v1/conversations/{conversation}/read`

### Lenguaje de señas
- `GET /api/v1/sign-languages/categories`
- `GET /api/v1/sign-languages/basic`
- `GET /api/v1/sign-languages/emergency`
- `GET /api/v1/sign-languages`
- `POST /api/v1/sign-languages`
- `GET /api/v1/sign-languages/{signLanguage}`
- `PUT /api/v1/sign-languages/{signLanguage}`
- `DELETE /api/v1/sign-languages/{signLanguage}`

### Audio
- `POST /api/v1/audio/speech-to-text`
- `POST /api/v1/audio/text-to-speech`
- `GET /api/v1/audio`
- `POST /api/v1/audio`
- `GET /api/v1/audio/{audio}`
- `PUT /api/v1/audio/{audio}`
- `DELETE /api/v1/audio/{audio}`

### Imágenes
- `GET /api/v1/images/profile`
- `POST /api/v1/images/profile`
- `GET /api/v1/images/type/{type}`
- `GET /api/v1/images`
- `POST /api/v1/images`
- `GET /api/v1/images/{image}`
- `PUT /api/v1/images/{image}`
- `DELETE /api/v1/images/{image}`

### Registros médicos
- `GET /api/v1/medical-records`
- `POST /api/v1/medical-records`
- `GET /api/v1/medical-records/{medicalRecord}`
- `PUT /api/v1/medical-records/{medicalRecord}`
- `DELETE /api/v1/medical-records/{medicalRecord}`
- `POST /api/v1/medical-records/{medicalRecord}/medications`
- `GET /api/v1/medical-records/{medicalRecord}/medications`
- `POST /api/v1/medical-records/{medicalRecord}/appointments`
- `GET /api/v1/medical-records/{medicalRecord}/appointments`

### Analytics
- `GET /api/v1/analytics`
- `GET /api/v1/analytics/messages`
- `GET /api/v1/analytics/emergencies`
- `GET /api/v1/analytics/accessibility`
- `POST /api/v1/analytics/generate-report`

### Accesibilidad
- `GET /api/v1/accessibility/settings`
- `PUT /api/v1/accessibility/settings`
- `GET /api/v1/accessibility/recommendations`
- `POST /api/v1/accessibility/test`
- `GET /api/v1/accessibility`
- `POST /api/v1/accessibility`
- `GET /api/v1/accessibility/{accessibilityLog}`
- `PUT /api/v1/accessibility/{accessibilityLog}`
- `DELETE /api/v1/accessibility/{accessibilityLog}`

---

## Variables de entorno importantes / Key environment variables

Las principales variables se encuentran en `backend/.env.example`, incluyendo:

- `APP_URL`
- `APP_ENV`
- `APP_DEBUG`
- `DB_CONNECTION`
- `SESSION_DRIVER`
- `QUEUE_CONNECTION`
- `CACHE_STORE`
- `MAIL_MAILER`
- `FILESYSTEM_DISK`
- `REDIS_*`

---

## Instalación / Installation

### Backend

```bash
cd backend
composer install
copy .env.example .env
php artisan key:generate
mkdir database
if (-not (Test-Path database/database.sqlite)) { New-Item database/database.sqlite -ItemType File }
php artisan migrate --force
npm install --ignore-scripts
npm run build
```

> Si usas Windows PowerShell, reemplaza `cp` por `copy` y crea el archivo SQLite con `New-Item`.

### Frontend

```bash
cd frontend
pnpm install
pnpm dev
```

Si no tienes `pnpm`, también puedes usar:

```bash
npm install
npm run dev
```

---

## Ejecución local / Local run

### Backend

```bash
cd backend
php artisan serve --host=127.0.0.1 --port=8000
```

### Frontend

```bash
cd frontend
pnpm dev
```

### Modo de desarrollo integrado / Integrated dev mode

El backend incluye un script `dev` en `backend/composer.json` que ejecuta simultáneamente:

- Servidor Laravel
- Cola de trabajos
- Logs en tiempo real
- Vite

```bash
cd backend
composer dev
```

---

## Variables de entorno importantes / Key environment variables

Edita `backend/.env.example` y configura:

- `APP_URL`
- `APP_ENV`
- `APP_DEBUG`
- `DB_CONNECTION`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `SESSION_DRIVER`
- `QUEUE_CONNECTION`
- `CACHE_STORE`
- `MAIL_MAILER`
- `FILESYSTEM_DISK`
- `REDIS_HOST`
- `REDIS_PORT`

---

## API principal / API overview

La API está disponible en `/api/v1`.

### Rutas clave / Key endpoints

- `POST /api/v1/auth/register`
- `POST /api/v1/auth/login`
- `POST /api/v1/auth/logout`
- `GET /api/v1/auth/me`
- `POST /api/v1/auth/reset-password`
- `GET /api/v1/phrases`
- `GET /api/v1/phrases/defaults`
- `GET /api/v1/emergencies`
- `POST /api/v1/emergencies/trigger`
- `GET /api/v1/messages`
- `GET /api/v1/conversations`
- `GET /api/v1/sign-languages`
- `POST /api/v1/audio/text-to-speech`
- `POST /api/v1/audio/speech-to-text`

Más rutas de administración de perfiles, multimedia, accesibilidad y registros médicos están definidas en el backend.

---

## Pruebas / Testing

### Backend

```bash
cd backend
composer test
```

### Frontend

```bash
cd frontend
pnpm test
pnpm test:ui
pnpm test:coverage
```

---

## Despliegue / Deployment

### Backend

- Genera la clave: `php artisan key:generate`
- Ejecuta migraciones: `php artisan migrate --force`
- Compila assets: `npm run build`

### Frontend

- Construye el sitio: `pnpm build`
- Despliega `.next` y archivos estáticos

---

## Tesis / Thesis project

UniConnect se desarrolla como proyecto de tesis escolar/profesional. El enfoque actual está en:

- completar la lógica del backend para autenticación, emergencias, mensajería y accesibilidad
- robustecer el frontend para interacción táctil, navegación háptica y visualización accesible
- integrar funcionalidades multimedia con audio y señalización

Este README refleja el estado real del desarrollo al momento de la última actualización.

---

## Contribuciones / Contributions

1. Haz un fork del repositorio.
2. Crea una rama descriptiva: `git checkout -b feature/nombre`
3. Realiza los cambios.
4. Envía un pull request con una descripción clara.

---

## Licencia / License

Este proyecto se distribuye bajo licencia MIT.

---

## Contacto / Contact

Para preguntas o mejoras, abre un issue o agrega comentarios en el repositorio.
