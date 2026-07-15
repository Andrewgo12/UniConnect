# Manual Gaps UniConnect

Esta carpeta recoge los pasos, ajustes y decisiones que no puede completar automáticamente el agente y que requieren intervención humana o configuración externa.

## Propósito

Usar este espacio como registro claro de:
- tareas manuales,
- configuraciones externas,
- decisiones de negocio,
- ajustes de entorno,
- pasos que requieren aprobación humana,
- y componentes que necesitan validación real en navegador o dispositivo.

---

## Cómo usar esta carpeta

Cada punto debe quedar documentado con:
1. Qué falta completar.
2. Por qué no se puede automatizar completamente.
3. Qué debes hacer tú manualmente.
4. Paso a paso para hacerlo.
5. Qué validar al final.

---

## Lista de gaps manuales esperables

### 1. Configuración de credenciales externas

**Situación**
Requiere llaves, secretos o cuentas reales.

**Ejemplos**
- API de traducción o IA.
- servicio de almacenamiento de archivos.
- notificaciones push.
- servicios de voz o reconocimiento avanzado.

**Qué hacer**
- Crear las credenciales en el servicio externo.
- Añadirlas al archivo de entorno correspondiente.
- Verificar que el proyecto las lea correctamente.

**Pasos**
1. Ir al proveedor externo.
2. Crear cuenta o proyecto.
3. Generar la clave o token.
4. Añadirla al archivo .env o configuración del entorno.
5. Reiniciar el servicio.
6. Validar con una prueba simple.

---

### 2. Validación real con micrófono y cámara

**Situación**
El agente puede implementar el flujo, pero la validación real depende de permisos del navegador o del dispositivo.

**Qué hacer**
- Permitir micrófono y cámara en el navegador.
- Probar el flujo fuera del entorno de desarrollo.
- Revisar si el sistema responde correctamente.

**Pasos**
1. Abrir la app en navegador.
2. Permitir micrófono y cámara.
3. Probar el flujo completo.
4. Confirmar que el sistema detecta entrada y salida correctamente.
5. Registrar si hay errores de permisos.

---

### 3. Validación de accesibilidad real con lector de pantalla

**Situación**
La implementación técnica puede estar bien, pero la experiencia real debe comprobarse con herramientas o un usuario real.

**Qué hacer**
- Probar con un lector de pantalla.
- Revisar órdenes de lectura, etiquetas y voz.
- Ajustar el flujo si es confuso.

**Pasos**
1. Activar un lector de pantalla.
2. Navegar por la app.
3. Verificar que los elementos se anuncian claramente.
4. Corregir cualquier problema de foco o contexto.
5. Confirmar que los mensajes y acciones tienen sentido auditivo.

---

### 4. Diseño de flujo de negocio y experiencia de usuario

**Situación**
El agente puede implementar estructura técnica, pero algunas decisiones de UX deben tomarlas personas con criterio de negocio o producto.

**Qué hacer**
- Definir cómo debe sonar, verse o responder la app en cada caso.
- Acordar mensajes de voz, prioridades y comportamiento de emergencias.
- Ajustar la experiencia para usuarios reales.

**Pasos**
1. Definir el flujo principal para un usuario ciego o sordo.
2. Definir cómo debe reaccionar la app ante una emergencia.
3. Definir cuál es el mensaje verbal correcto para cada situación.
4. Ajustar la UI o el feedback tras revisión humana.

---

### 5. Ajustes de seguridad y privacidad

**Situación**
Hay decisiones sensibles que no deben dejarse solo a la automatización.

**Qué hacer**
- Revisar qué datos se almacenan.
- Confirmar si se compartirá audio, vídeo o contenido personal.
- Definir políticas de retención y control de acceso.

**Pasos**
1. Revisar los datos que entran al sistema.
2. Confirmar si deben almacenarse o procesarse temporalmente.
3. Definir reglas de seguridad y acceso.
4. Ajustar la lógica si es necesario.

---

### 6. Integración con servicios reales de IA o traducción

**Situación**
La integración técnica puede prepararse, pero el servicio real necesita configuración externa y validación.

**Qué hacer**
- Crear la integración real con el servicio.
- Probar con datos reales.
- Confirmar que los resultados son útiles.

**Pasos**
1. Obtener acceso al servicio.
2. Configurar la cuenta o credencial.
3. Conectar el backend.
4. Probar con un ejemplo real.
5. Ajustar prompts, límites o estructura de respuesta.

---

## Formato recomendado para cada gap

Cada entrada nueva debe seguir este formato:

### Título del gap

- Tipo: manual / externo / negocio / validación / seguridad
- Estado: pendiente / en proceso / completado
- Responsable: humano / agente / ambos

#### Descripción
Explicar qué falta y por qué no se puede completar automáticamente.

#### Qué debe hacer el humano
Describir la acción concreta.

#### Pasos a seguir
1. Paso 1
2. Paso 2
3. Paso 3

#### Validación final
Qué debe comprobarse al terminar.

---

## Plantilla lista para usar

Crear un archivo nuevo en esta carpeta con el nombre:
- gap-01-configuracion-externa.md
- gap-02-microfono-camara.md
- gap-03-accesibilidad-real.md
- gap-04-negocio-ux.md
- gap-05-seguridad.md
- gap-06-ia-traduccion.md

Y usar este contenido base:

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
