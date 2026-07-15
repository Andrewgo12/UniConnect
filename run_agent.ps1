$instrucciones = @'
Actúa como un Ingeniero de Software Principal y especialista en Accesibilidad Web. Tu misión es avanzar de forma autónoma el proyecto UniConnect desde el estado actual hacia un MVP funcional de comunicación inclusiva.

## Objetivo
Transformar la app de una interfaz visual con lógica parcial en un sistema real de comunicación accesible y verificable.

## Reglas de ejecución
1. Trabaja de forma continua y ordenada.
2. No dejes placeholders ni simulaciones como si fueran funcionalidad real.
3. No intentes introducir MongoDB ni cambiar la arquitectura real del proyecto; el backend usa Laravel + Eloquent + SQL.
4. Prioriza estabilidad, seguridad, rendimiento y accesibilidad.
5. Después de cada cambio, valida sintaxis o comportamiento con los comandos disponibles.
6. Si un cambio falla, registra el problema en ERRORES_PENDIENTES.md, revierte lo roto con Git y sigue con la siguiente tarea.

## Prioridades de desarrollo
### Fase 1: Estado global de accesibilidad en React
- Crear o completar un contexto/hook global de accesibilidad.
- Centralizar perfil activo, canales de entrada/salida, voz, vibración, cámara y fallbacks.
- Reducir la dependencia del archivo monolítico frontend/app/page.tsx.

### Fase 2: Modularización de APIs de accesibilidad
- Extraer la lógica de SpeechRecognition, SpeechSynthesis y vibración a hooks reutilizables.
- Mantener el rendimiento de forma estable y evitar fugas de memoria.

### Fase 3: Mensajería real en backend
- Implementar o completar el flujo de mensajes con estado real: sent, delivered, read, failed.
- Asegurar que mensajes y emergencias se almacenen y consulten correctamente.
- Mejorar el servicio y controlador de mensajes.

### Fase 4: Integración frontend-backend real
- Conectar la UI con los endpoints reales del backend.
- Eliminar lógica de respuesta simulada donde exista.
- Manejar errores, carga y reintentos.

### Fase 5: Pipeline básico de señas
- Preparar la ruta de backend para recibir landmarks o payload de cámara.
- Conectar con una integración básica de Gemini si la configuración lo permite.
- Mantener fallback si no hay servicio externo disponible.

### Fase 6: Pruebas y hardening
- Revisar compilación, sintaxis y flujo principal.
- Probar mensajes, voz, vibración, cámara, errores y accesibilidad básica.
- Dejar el proyecto en un estado estable para seguir desarrollando.

## Archivos clave a trabajar
- frontend/app/page.tsx
- frontend/lib/api.ts
- backend/app/Services/MessageService.php
- backend/app/Http/Controllers/Api/V1/MessageController.php
- backend/app/Services/SignLanguageService.php
- backend/app/Http/Controllers/Api/V1/SignLanguageController.php
- backend/routes/api.php
- backend/app/Models/Message.php

## Protocolo operativo
- Empieza por entender el estado actual del proyecto.
- Implementa una mejora concreta a la vez.
- Si algo falla, corrige hasta 3 veces; si sigue fallando, registra y continúa.
- Deja el proyecto en un estado mejor que el encontrado.
'@
Set-Content -Path "instrucciones_aider.md" -Value $instrucciones -Encoding utf8

# Reemplaza las comillas de abajo con la clave que acabas de generar
$apiKey = "AQ.Ab8RN6LEQ7CEpynG9AxDmPzpbOtyZvNSFpoNqZW2neWD26o6MA"
$env:GEMINI_API_KEY = $apiKey
$env:GOOGLE_API_KEY = $apiKey
$primaryModel = "gemini/gemini-2.0-flash"
$fallbackModel = "gemini/gemini-1.5-pro"
$modelo = if ($env:AIDER_MODEL) { $env:AIDER_MODEL } else { $primaryModel }

$totalCores = [int]$env:NUMBER_OF_PROCESSORS
$coresToUse = [Math]::Max(1, [Math]::Floor($totalCores * 0.60))
$affinityMask = [Math]::Pow(2, $coresToUse) - 1

function Start-AiderProcess($model, $cores, $mask, $weakModel) {
  $Argumentos = @(
    "--model", $model,
    "--weak-model", $weakModel,
    "--auto-commit",
    "--yes",
    "--no-git",
    "--no-gitignore",
    "--set-env", "AIDER_TIMEOUT=120",
    "--set-env", "AIDER_MAX_CHAT_HISTORY_TOKENS=1024",
    "--set-env", "AIDER_REASONING_EFFORT=low",
    "--set-env", "AIDER_THINKING_TOKENS=256",
    "--message-file", "instrucciones_aider.md",
    "frontend/app/page.tsx",
    "frontend/lib/api.ts",
    "backend/app/Services/MessageService.php",
    "backend/app/Http/Controllers/Api/V1/MessageController.php",
    "backend/app/Services/SignLanguageService.php",
    "backend/routes/api.php",
    "backend/app/Models/Message.php",
    "backend/app/Http/Controllers/Api/V1/SignLanguageController.php"
  )

  Write-Host "[INFO] Iniciando Aider con modelo: $model" -ForegroundColor Green
  Write-Host "[INFO] Limitando el agente a $cores de $env:NUMBER_OF_PROCESSORS núcleos (aprox. 60% de CPU)." -ForegroundColor Green
  
  $Proceso = Start-Process -FilePath "aider" -ArgumentList $Argumentos -PassThru -NoNewWindow
  if ($Proceso) {
    $Proceso.ProcessorAffinity = $mask
    $Proceso.PriorityClass = "BelowNormal"
    Write-Host "[OK] Agente iniciado. Trabajando en modo autónomo con prioridad baja." -ForegroundColor Cyan
    $Proceso.WaitForExit()
    return $Proceso.ExitCode
  }
  return 1
}

$exitCode = Start-AiderProcess -model $modelo -cores $coresToUse -mask $affinityMask -weakModel $primaryModel
if ($exitCode -ne 0 -and $modelo -eq $primaryModel) {
  Write-Host "[WARN] Modelo preferido falló o no está disponible, reintentando con el modelo de fallback: $fallbackModel" -ForegroundColor Yellow
  Start-AiderProcess -model $fallbackModel -cores $coresToUse -mask $affinityMask -weakModel $primaryModel | Out-Null
}