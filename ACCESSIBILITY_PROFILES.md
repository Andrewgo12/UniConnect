si# Perfiles de Accesibilidad de UniConnect

Este documento describe los perfiles de usuario accesibles que se manejan en UniConnect, sus canales de entrada y salida, y los detalles de audio, vibración, temas y cámara.

## Resumen general

UniConnect soporta perfiles que combinan capacidades de:

- `blind` (ciego)
- `deaf` (sordo)
- `mute` (mudo)

También existe un modo invitado temporal para hablar sin iniciar sesión.

La interfaz actual prioriza:

- audio / TTS
- vibración / háptica
- entrada de voz
- frases rápidas
- alta visibilidad / alto contraste
- estado claro para el usuario ciego

## Canales de salida

### Audio / TTS

- El perfil con `blind` usa síntesis de voz para anunciar:
  - cambios de pantalla
  - estado del reconocimiento de voz
  - mensajes recibidos y confirmaciones
  - la interfaz activa
- El usuario puede configurar:
  - activar/desactivar TTS
  - velocidad de voz
  - idioma de voz
- El perfil `deaf` desactiva el TTS y prioriza la vibración.

### Vibración / Háptica

- El perfil `deaf` y las combinaciones con sordera usan vibración para:
  - confirmar envíos
  - indicar mensajes entrantes
  - dar feedback de acciones
- El perfil `blind + deaf` usa un sistema háptico más completo:
  - navegación por frases con swipes
  - toque largo para enviar
  - patrones de vibración específicos por mensaje
- El perfil `mute` y `sordo + mudo` continúa usando vibración para feedback.
- Para todos los perfiles con sordera, la vibración se asocia a patrones claros:
  - un pulso corto = notificación de mensaje nuevo
  - dos pulsos = acción completada / enviado
  - pulso largo = alerta de emergencia o error

### Visual / Temas

- El modo de alto contraste está disponible en la configuración.
- La interfaz tiene etiquetas ARIA y regiones claramente definidas.
- Los botones, formularios y grupos usan `aria-label`, `role`, `aria-live` y `aria-describedby`.
- Hay un modo de aplicación ciega con descripciones habladas y flujo de navegación accesible.

## Canales de entrada

### Voz

- Los perfiles sin `mute` pueden activar reconocimiento de voz.
- El botón de voz anuncia si está `ESCUCHANDO...` o listo para hablar.
- Para usuarios ciegos, la voz es el canal primario cuando no son mudos.
- Si el micrófono no está disponible, se informa con audio y vibración.

### Frases rápidas

- El perfil `mute` o `sordo` usa frases rápidas como canal principal.
- Hay un grid de frases con etiquetas accesibles.
- Los usuarios pueden agregar frases personalizadas.

### Entrada de texto

- Siempre hay un formulario de mensaje con `aria-label` claro.
- Si no hay texto, el botón `Enviar` se desactiva con mensaje de ayuda accesible.

## Perfiles completos

### Perfil Normal

- `blind: false`, `deaf: false`, `mute: false`
- Soporta voz, texto, audio y visual completo.
- Tiene acceso a chat, frases, configuración y emergencia.
- Usa TTS como ayuda opcional.

### Perfil Ciego

- `blind: true`, `deaf: false`, `mute: false`
- Interfaz hablada con TTS y anuncios automáticos.
- Reconocimiento de voz disponible para enviar mensajes.
- Toda la UI está estructurada para navegación por lector de pantalla.
- El botón principal cambia entre `Hablar` y `Parar`.
- Vibración leve opcional para confirmar acciones cuando el dispositivo lo soporta.

### Perfil Sordo

- `blind: false`, `deaf: true`, `mute: false`
- No usa TTS.
- Usa vibración y alertas visuales para feedback.
- Puede usar botones y texto, pero el audio no es necesario.
- Vibration pattern: corto para mensaje nuevo, doble para confirmación, largo para emergencias.

### Perfil Mudo

- `blind: false`, `deaf: false`, `mute: true`
- Desactiva el reconocimiento de voz.
- Prioriza frases rápidas y texto.
- El usuario recibe confirmaciones visuales/vibración.
- Vibración estándar para confirmaciones y errores; no hay entrada por voz.

### Perfil Ciego + Sordo

- `blind: true`, `deaf: true`, `mute: false`
- Sin audio ni voz.
- El canal principal es la vibración.
- La aplicación ofrece navegación háptica por frases.
- Las acciones se describen mediante patrones de vibración y texto.
- Patrón háptico específico:
  - pulso corto = cambio de elemento
  - doble pulso = confirmación de frase
  - pulso largo = alerta de emergencia

### Perfil Ciego + Mudo

- `blind: true`, `deaf: false`, `mute: true`
- TTS para navegación, pero sin entrada de voz.
- Usa frases rápidas y la app puede anunciar las opciones.
- La comunicación depende del lector de pantalla y frases rápidas.
- Vibración breve para indicar que un mensaje ha sido enviado correctamente.

### Perfil Sordo + Mudo

- `blind: false`, `deaf: true`, `mute: true`
- Sin audio ni voz.
- Usa vibración y texto para comunicar.
- Se accede a frases rápidas y alertas visuales.
- Vibration patterns consistentes para:
  - entrada de mensaje nueva
  - confirmación de envío
  - error / aviso crítico

### Perfil Todos (Ciego + Sordo + Mudo)

- `blind: true`, `deaf: true`, `mute: true`
- Canal único: vibración / háptica.
- Se usa navegación táctil y patrones de vibración.
- La pantalla reporta estado mínimo con ayuda háptica.

## Modo invitado

- Permite hablar sin iniciar sesión por tiempo limitado.
- Está pensado para acceso rápido cuando no hay token.
- Proporciona feedback de estado y cuenta regresiva.
- Ideal para usuarios que necesitan entrar rápido y probar la voz.

## Audio, temas y cámara

### Audio

- TTS configurado en `es-CO` por defecto.
- Se puede ajustar la velocidad de voz.
- El sistema informa errores de micrófono: `not-allowed`, `network`, `no-speech`.
- Hay un medidor de nivel de audio visible cuando se escucha.

### Temas

- La app ofrece modo de alto contraste para mejorar la legibilidad.
- Existe un panel de configuración rápido y un drawer completo.
- El usuario puede activar o desactivar:
  - voz (TTS)
  - vibración
  - alto contraste

### Cámara

- En la versión actual de frontend no hay soporte directo de cámara.
- El diseño del proyecto prevé detectar capacidades y ofrecer fallback.
- La documentación futura debe incluir:
  - verificación de cámara disponible
  - capturas de imagen para accesibilidad visual
  - uso de la cámara como canal alternativo si es necesario
  - soporte para usuarios de lengua de señas mediante:
    - detección de presencia de cámara
    - orientación de posición y encuadre
    - integración con un flujo de video seguro para interpretación
    - mensajes visuales y pruebas de cámara antes de iniciar sesión

## Notas de implementación

- Las interfaces usan roles ARIA claros: `main`, `application`, `region`, `status`, `alert`, `log`.
- Los elementos interactivos tienen `aria-label` descriptivos.
- Los controles deshabilitados usan `aria-disabled`.
- El historial de mensajes usa `role="log"` para notificar nuevas entradas.

## Recomendación

Para un documento completo de perfil, conviene mantener esta guía actualizada con los cambios en `frontend/app/page.tsx` y los componentes UI.

> Si quieres, puedo generar también una tabla comparativa entre perfiles y capacidades de entrada/salida en otro archivo Markdown.