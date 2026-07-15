# Plan de Ejecución Autónoma UniConnect

## Objetivo general

Convertir este proyecto en un MVP funcional de comunicación accesible, robusto y listo para demostrar. El agente debe trabajar como si estuviera liderando una implementación completa de principio a fin, sin dejar piezas incompletas ni asumir que algo “está bien” solo porque se ve en pantalla.

Este plan está pensado para ser mucho más detallado, prescriptivo y operativo que el anterior. No solo define qué hacer, sino también cómo hacerlo, en qué orden, con qué archivos, qué validar y qué entregar en cada bloque.

---

## Reglas de oro del proyecto

1. No se puede pasar a una fase nueva si la anterior no está validada.
2. Todo cambio debe dejar evidencia verificable: test, build, lint, respuesta HTTP o comportamiento funcional.
3. No se aceptan soluciones parciales, placeholders o “mockeos visibles” como si fueran funcionalidad real.
4. El agente debe trabajar en bloques de ejecución con las siguientes reglas:
   - implementar,
   - probar,
   - corregir,
   - documentar,
   - avanzar.
5. Si algo falla, no se debe improvisar con cambios dispersos. Se debe identificar la causa raíz y corregirla de forma limpia.
6. El agente debe priorizar estabilidad, claridad de arquitectura y accesibilidad real antes que pulido visual.

---

## Fase 0 — Preparación integral y diagnóstico de base

### Objetivo
Establecer una base sólida antes de modificar el sistema.

### Tareas obligatorias
- Crear una rama de trabajo limpia.
- Revisar el estado actual del backend y del frontend.
- Identificar los archivos principales que se van a modificar.
- Confirmar si existen tests y si están pasando.
- Recolectar los puntos de entrada actuales:
  - autenticación,
  - mensajes,
  - frases,
  - emergencias,
  - cámara,
  - voz,
  - vibración,
  - lógica de accesibilidad.
- Definir qué parte del sistema es “visual” y qué parte es “realmente funcional”.

### Entregables
- Lista actualizada de archivos críticos.
- Resumen de estado de proyecto.
- Lista de riesgos iniciales.

### Criterios de éxito
- El agente tiene claridad completa del contexto.
- No hay decisiones tomadas a ciegas.
- Existe una visión completa del alcance del MVP.

---

## Fase 1 — Arquitectura base del sistema de mensajería

### Objetivo
Diseñar y construir una base real de mensajería que permita enviar, almacenar, consultar y actualizar mensajes con estructura clara.

### Tareas obligatorias
- Revisar el modelo actual de mensajes y su estructura en base de datos.
- Verificar si los campos existentes cubren lo necesario para:
  - contenido,
  - tipo,
  - estado,
  - prioridad,
  - metadata,
  - accesibilidad,
  - conversación,
  - usuario.
- Implementar o corregir el flujo de creación de mensajes.
- Implementar estados claros de mensaje:
  - sent,
  - delivered,
  - read,
  - failed.
- Asegurar que los mensajes puedan asociarse a conversaciones reales.
- Asegurar que los mensajes guarden correctamente la información de accesibilidad.
- Preparar endpoints claros para:
  - crear mensaje,
  - listar mensajes,
  - obtener mensajes por conversación,
  - actualizar estado,
  - enviar frases predefinidas.

### Archivos objetivo
- backend/app/Services/MessageService.php
- backend/app/Http/Controllers/Api/V1/MessageController.php
- backend/routes/api.php
- backend/app/Models/Message.php

### Entregables
- Servicio de mensajes funcional.
- Controlador con respuestas consistentes.
- Endpoints listos para ser consumidos por el frontend.

### Criterios de éxito
- Se pueden crear mensajes desde el backend sin errores.
- Se pueden listar correctamente.
- Los mensajes conservan metadata y estado.
- El sistema no depende de datos temporales o artificiales.

---

## Fase 2 — Lógica de backend para mensajes accesibles y de emergencia

### Objetivo
Asegurar que los mensajes no solo se almacenen, sino que también puedan transportar información accesible y soporte para alertas.

### Tareas obligatorias
- Añadir soporte para metadata accesible en los mensajes.
- Permitir que los mensajes de emergencia incluyan:
  - tipo,
  - prioridad,
  - descripción,
  - contexto de accesibilidad,
  - canal de salida preferido.
- Implementar un flujo consistente para frases de emergencia y mensajes urgentes.
- Asegurar que la respuesta HTTP sea útil tanto para frontend como para pruebas.

### Archivos objetivo
- backend/app/Services/MessageService.php
- backend/app/Http/Controllers/Api/V1/MessageController.php
- backend/app/Models/Message.php

### Entregables
- Mensajes con soporte real para accesibilidad.
- Flujo de emergencia estructurado.

### Criterios de éxito
- Un mensaje de emergencia queda correctamente representado y persistido.
- La información de accesibilidad se conserva en el record.
- El backend no pierde contexto al procesar mensajes urgentes.

---

## Fase 3 — Estado global de accesibilidad en frontend

### Objetivo
Dejar de depender de una pantalla monolítica con lógica dispersa y crear una arquitectura de estado reutilizable para accesibilidad.

### Tareas obligatorias
- Crear un contexto o hook global de accesibilidad.
- Centralizar lo siguiente:
  - perfil activo,
  - modo de entrada,
  - modo de salida,
  - estado de voz,
  - estado de vibración,
  - estado de cámara,
  - mensajes actuales,
  - estado de autenticación,
  - estado de error y fallback.
- Separar claramente:
  - presentación,
  - estado,
  - adaptación de canal,
  - lógica de negocio.
- Asegurar que el estado global pueda usarse desde múltiples componentes.

### Archivos objetivo
- frontend/app/page.tsx
- frontend/lib/api.ts
- nuevos archivos de contexto o hook si son necesarios

### Entregables
- Hook o contexto de accesibilidad funcional.
- Menor dependencia de lógica duplicada.
- La interfaz puede reaccionar de forma coherente ante cambios de estado.

### Criterios de éxito
- El flujo de accesibilidad ya no depende de variables dispersas.
- El comportamiento es coherente aunque cambie el perfil o el canal.
- El sistema admite fallbacks claros.

---

## Fase 4 — Integración real frontend ↔ backend

### Objetivo
Hacer que el frontend deje de depender de flujos simulados y pase a comunicarse con el backend de forma real y verificable.

### Tareas obligatorias
- Conectar la UI con los endpoints reales del backend.
- Asegurar que los mensajes enviados desde la app se guarden en backend.
- Implementar recuperación real de mensajes desde backend.
- Manejar estados de carga, error y retry.
- Evitar que la app se quede en un estado inconsistente si la conexión falla.
- Implementar, al menos, un mecanismo simple de sincronización en tiempo real o polling.

### Archivos objetivo
- frontend/lib/api.ts
- frontend/app/page.tsx

### Entregables
- Envío real de mensajes.
- Lectura real de mensajes.
- Manejo de errores y resynchronización.

### Criterios de éxito
- Un mensaje enviado desde la interfaz aparece en backend y se puede recuperar.
- Si falla la conexión, la app responde correctamente y no se rompe.
- La experiencia es estable tanto con conexión como sin conexión.

---

## Fase 5 — Integración de voz, vibración y cámara en flujo real

### Objetivo
Que los componentes de accesibilidad trabajen como parte de un flujo funcional, no como funciones aisladas.

### Tareas obligatorias
- Asegurar que la entrada por voz dispare una acción real en el sistema.
- Asegurar que la salida por voz se active en respuesta a eventos de negocio relevantes.
- Asegurar que la vibración se use como canal real de alerta o confirmación.
- Asegurar que la cámara pueda participar en un flujo de interacción coherente.
- Definir qué pasa cuando no hay permiso de micrófono, cámara o vibración.

### Archivos objetivo
- frontend/app/page.tsx
- frontend/lib/api.ts

### Entregables
- Flujo de voz funcional.
- Flujo de vibración funcional.
- Flujo de cámara funcional con fallback.

### Criterios de éxito
- La voz produce un resultado real y visible.
- La vibración confirma una acción relevante.
- La cámara no rompe la experiencia ni deja recursos activos sin limpiar.

---

## Fase 6 — Pipeline básico de señas

### Objetivo
Crear una base real para el procesamiento de señas, aunque sea en versión MVP y con enfoque funcional.

### Tareas obligatorias
- Revisar el servicio existente de señas.
- Preparar una capa que pueda recibir información de imagen, video o landmarks.
- Definir un formato de entrada y salida claro para procesamiento.
- Crear una ruta de backend para recibir, validar y devolver una respuesta estructurada.
- Conectar el flujo de cámara con esta ruta de forma no invasiva.
- Garantizar que si la integración no está disponible, el sistema responda con un fallback correcto.

### Archivos objetivo
- backend/app/Services/SignLanguageService.php
- backend/app/Http/Controllers/Api/V1/SignLanguageController.php
- backend/routes/api.php

### Entregables
- Endpoint o servicio de señas funcional al menos en forma de pipeline base.
- Integración mínima con la experiencia de cámara.

### Criterios de éxito
- Hay un camino claro desde la cámara hasta una salida estructurada.
- El sistema no se rompe si la traducción no está disponible.
- El flujo queda preparado para expandirse más adelante.

---

## Fase 7 — Pruebas, validación y hardening

### Objetivo
Garantizar que el sistema sea estable, seguro y usable.

### Tareas obligatorias
- Ejecutar tests relevantes del backend.
- Ejecutar tests relevantes del frontend.
- Revisar errores de compilación.
- Validar que la app no se rompa en los flujos principales.
- Probar:
  - inicio de sesión,
  - envío de mensaje,
  - recepción de mensaje,
  - emergencia,
  - voz,
  - vibración,
  - cámara,
  - manejo de error.
- Revisar accesibilidad básica: labels, roles, lectura por pantalla, contraste, navegación por teclado.

### Entregables
- Informe de validación.
- Lista de errores corregidos.
- Evidencia de ejecución.

### Criterios de éxito
- No hay fallos críticos en el flujo principal.
- Los errores se manejan de forma controlada.
- La experiencia es estable al menos en el MVP.

---

## Fase 8 — Documentación final y handoff

### Objetivo
Dejar el proyecto listo para continuar sin depender de la memoria o del contexto previo.

### Tareas obligatorias
- Documentar los endpoints clave del backend.
- Documentar el flujo de accesibilidad del frontend.
- Documentar variables de entorno importantes.
- Documentar qué partes están listas y qué partes siguen siendo extensión futura.
- Preparar un resumen ejecutivo para seguir trabajando.

### Entregables
- Documentación técnica actualizada.
- Resumen de estado del MVP.
- Lista de próximos pasos recomendados.

### Criterios de éxito
- Cualquier otro desarrollador puede entender el estado del proyecto en una lectura corta.
- El handoff es claro y útil.

---

## Plan operativo para el agente

### Forma de trabajo
El agente debe ejecutar este proyecto en bloques, no como una tarea vaga. Cada bloque debe terminar con:
- implementación,
- verificación,
- corrección si aplica,
- registro del progreso.

### Tiempo sugerido por bloque
- Preparación: 30–45 min
- Mensajería backend: 1.5–2 h
- Accesibilidad frontend: 1.5–2 h
- Integración frontend/backend: 1.5–2 h
- Voz/vibración/cámara: 1–1.5 h
- Señas: 1–1.5 h
- Pruebas y hardening: 1–1.5 h
- Documentación: 30 min

### Qué debe hacer siempre
- leer el contexto real antes de tocar código,
- cambiar una cosa a la vez,
- verificar resultados,
- no avanzar si algo está incompleto,
- priorizar estabilidad sobre velocidad.

### Qué no debe hacer
- dejar TODOs sin resolver,
- asumir que algo funciona sin validarlo,
- mezclar demasiadas capas en una sola modificación,
- ignorar errores de linter o tests.

---

## Criterios de éxito final

El proyecto se considera listo para la siguiente etapa cuando:
- el backend gestiona mensajes reales con estado,
- el frontend consume ese backend de forma coherente,
- la accesibilidad tiene un estado centralizado y reutilizable,
- voz, vibración y cámara participan en un flujo útil,
- las señas tienen una base funcional de procesamiento,
- los flujos principales están verificados,
- y la solución no depende de placeholders o lógica simulada para ser demostrable.

## Sección 9 — Gaps manuales y tareas que requieren intervención humana

Esta sección recoge todos los pasos, ajustes y decisiones que el agente no puede completar de forma autónoma y que necesitan intervención humana, configuración externa o validación real.

### 9.1 Propósito

El objetivo de esta sección es evitar que queden tareas abiertas, incompletas o asumidas como resueltas sin verificación real. Todo gap manual debe registrarse aquí con claridad y orden.

### 9.2 Qué debe documentarse en cada gap

Cada punto debe quedar registrado con:
1. Qué falta completar.
2. Por qué no se puede automatizar completamente.
3. Qué debe hacer la persona manualmente.
4. Pasos concretos a seguir.
5. Qué validar al final.

---

### 9.3 Gaps manuales esperables

#### 9.3.1 Configuración de credenciales externas

- Tipo: externo
- Estado: pendiente hasta que existan credenciales reales
- Responsable: humano / equipo de operaciones

**Descripción**
Requiere claves, secretos, cuentas o accesos reales a servicios externos.

**Ejemplos**
- API de traducción o IA.
- almacenamiento en la nube.
- notificaciones push.
- servicios de voz o reconocimiento avanzado.

**Qué debe hacer el humano**
- Crear o validar las credenciales.
- Añadirlas al archivo de entorno correspondiente.
- Verificar que el proyecto las lea correctamente.

**Pasos a seguir**
1. Ir al proveedor externo.
2. Crear o seleccionar el proyecto correcto.
3. Generar la clave o token.
4. Añadirlo al archivo de entorno.
5. Reiniciar el servicio o la app.
6. Validar con una prueba simple.

**Validación final**
- La app acepta la configuración.
- El flujo externo responde correctamente.
- No hay errores de autenticación.

---

#### 9.3.2 Validación real con micrófono y cámara

- Tipo: validación
- Estado: pendiente hasta probar en navegador o dispositivo real
- Responsable: humano

**Descripción**
El agente puede implementar el flujo, pero la validación real depende de permisos del navegador, hardware y entorno del usuario.

**Qué debe hacer el humano**
- Permitir micrófono y cámara en el navegador.
- Probar el flujo completo en un entorno real.
- Comprobar si la app responde correctamente.

**Pasos a seguir**
1. Abrir la app en un navegador real.
2. Permitir acceso a micrófono y cámara.
3. Ejecutar el flujo completo.
4. Confirmar que la entrada y salida funcionan.
5. Registrar si hay errores de permisos o rendimiento.

**Validación final**
- El sistema detecta audio y vídeo correctamente.
- No hay bloqueos inesperados.
- El flujo es usable en condiciones reales.

---

#### 9.3.3 Validación de accesibilidad real con lector de pantalla

- Tipo: accesibilidad / validación
- Estado: pendiente hasta revisar con herramientas reales o usuario final
- Responsable: humano

**Descripción**
La implementación técnica puede estar bien, pero la experiencia real debe comprobarse con lector de pantalla, teclado o usuario real.

**Qué debe hacer el humano**
- Probar la app con un lector de pantalla.
- Revisar el orden de lectura, etiquetas, foco y mensajes auditivos.
- Ajustar el flujo si es confuso o incompleto.

**Pasos a seguir**
1. Activar un lector de pantalla.
2. Navegar por la app completa.
3. Verificar que los elementos se anuncian claramente.
4. Corregir problemas de foco o contexto.
5. Confirmar que los mensajes y acciones tienen sentido auditivo.

**Validación final**
- La navegación es comprensible por audio.
- Los controles tienen intención clara.
- El flujo de accesibilidad es usable en práctica.

---

#### 9.3.4 Diseño de flujo de negocio y experiencia de usuario

- Tipo: negocio / UX
- Estado: pendiente hasta definir la experiencia final
- Responsable: humano / producto

**Descripción**
El agente puede implementar estructura técnica, pero algunas decisiones finales de experiencia deben tomarlas personas con criterio de negocio o producto.

**Qué debe hacer el humano**
- Definir cómo debe sonar, verse o responder la app en cada caso.
- Acordar mensajes de voz, prioridades y comportamiento de emergencia.
- Ajustar la experiencia para usuarios reales.

**Pasos a seguir**
1. Definir el flujo principal para un usuario ciego o sordo.
2. Definir cómo debe reaccionar la app ante una emergencia.
3. Elegir el mensaje verbal correcto para cada situación.
4. Ajustar la interfaz o el feedback tras revisión humana.

**Validación final**
- El flujo es coherente para usuarios reales.
- Los mensajes y alertas tienen sentido.
- La experiencia es apropiada para el contexto de uso.

---

#### 9.3.5 Ajustes de seguridad y privacidad

- Tipo: seguridad
- Estado: pendiente hasta revisar políticas reales
- Responsable: humano / responsable de producto o legal

**Descripción**
Hay decisiones sensibles que no deben dejarse solo a la automatización.

**Qué debe hacer el humano**
- Revisar qué datos se almacenan y transmiten.
- Confirmar si se compartirá audio, vídeo o contenido personal.
- Definir políticas de retención y control de acceso.

**Pasos a seguir**
1. Revisar los datos que entran al sistema.
2. Confirmar si deben almacenarse o procesarse temporalmente.
3. Definir reglas de seguridad y acceso.
4. Ajustar la lógica si es necesario.

**Validación final**
- Se entiende qué datos se manejan.
- Existen reglas claras de seguridad y privacidad.
- No hay riesgo evidente de exposición innecesaria.

---

#### 9.3.6 Integración con servicios reales de IA o traducción

- Tipo: externo / IA
- Estado: pendiente hasta conectar el servicio real
- Responsable: humano / equipo técnico

**Descripción**
La integración técnica puede prepararse, pero el servicio real necesita configuración externa y validación concreta.

**Qué debe hacer el humano**
- Crear la integración real con el servicio.
- Probar con datos reales.
- Confirmar que los resultados son útiles y consistentes.

**Pasos a seguir**
1. Obtener acceso al servicio real.
2. Configurar la cuenta o credencial.
3. Conectar el backend a la integración.
4. Probar con un ejemplo real.
5. Ajustar prompts, límites o estructura de respuesta si es necesario.

**Validación final**
- La integración responde correctamente.
- Los resultados son coherentes y aprovechables.
- El sistema no depende de una respuesta simulada.

---

### 9.4 Formato obligatorio para registrar cada gap

Cada entrada nueva debe seguir este formato:

#### Título del gap

- Tipo: manual / externo / negocio / validación / seguridad
- Estado: pendiente / en proceso / completado
- Responsable: humano / agente / ambos

**Descripción**
Explicar qué falta y por qué no se puede completar automáticamente.

**Qué debe hacer el humano**
Describir la acción concreta.

**Pasos a seguir**
1. Paso 1
2. Paso 2
3. Paso 3

**Validación final**
Qué debe comprobarse al terminar.

---

### 9.5 Plantilla lista para usar

```md
# Título del gap

- Tipo:
- Estado:
- Responsable:

## Descripción

## Qué debe hacer el humano

## Pasos a seguir
1.
2.
3.

## Validación final
```

---

## Cierre del plan

Este documento debe servir como guía completa de ejecución, implementación y seguimiento. Si el agente no puede completar un punto por sí solo, debe dejarlo registrado aquí con claridad para que la siguiente acción sea ejecutada por la persona correspondiente.
