"use client"

import { useState, useEffect, useCallback, useRef } from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import {
  AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent,
  AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle,
} from "@/components/ui/alert-dialog"
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert"
import { Progress } from "@/components/ui/progress"
import { Switch } from "@/components/ui/switch"
import { Slider } from "@/components/ui/slider"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import {
  Drawer, DrawerClose, DrawerContent, DrawerDescription,
  DrawerFooter, DrawerHeader, DrawerTitle,
} from "@/components/ui/drawer"
import { textToMorse } from "@/lib/morse"
import { toast } from "sonner"
import { useIsMobile } from "@/hooks/use-mobile"

type Profile = { blind: boolean; deaf: boolean; mute: boolean }

const PHRASES = [
  { id: 1, text: "Sí", icon: "✓", vibration: [100] },
  { id: 2, text: "No", icon: "✗", vibration: [100, 80, 100] },
  { id: 3, text: "Ayuda", icon: "!", vibration: [200, 100, 200, 100, 200] },
  { id: 4, text: "Gracias", icon: "♥", vibration: [50, 50, 50] },
  { id: 5, text: "Agua", icon: "💧", vibration: [150, 80, 150] },
  { id: 6, text: "Baño", icon: "🚽", vibration: [100, 100, 100, 100] },
  { id: 7, text: "Dolor", icon: "⚠", vibration: [300, 100, 300] },
  { id: 8, text: "Llamar", icon: "📞", vibration: [400] },
]

/**
 * HapticPhraseNavigator — Navegación por frases sin necesidad de ver la pantalla.
 *
 * Interacciones táctiles:
 *   Swipe derecha  → siguiente frase  (vibra 1 pulso corto [80])
 *   Swipe izquierda → frase anterior  (vibra 2 pulsos [80,60,80])
 *   Toque largo (≥500ms) → enviar frase seleccionada (vibra patrón de la frase)
 *   Toque corto    → anuncia la frase actual por TTS (si hay audio)
 *
 * La frase activa se muestra en pantalla para usuarios con baja visión.
 * Para usuarios completamente ciegos, TalkBack + TTS anuncian la frase.
 */
type Phrase = { id: number; text: string; vibration: number[]; icon?: string }

type HapticPhraseNavigatorProps = {
  phrases: Phrase[]
  onSend: (p: Phrase) => void
  vibrate: (pattern: number[]) => void
  speak: (text: string, cancel?: boolean) => void
  canHear: boolean
}

function HapticPhraseNavigator({ phrases, onSend, vibrate, speak, canHear }: HapticPhraseNavigatorProps) {
  const [activeIndex, setActiveIndex] = useState(0)
  const touchStartX = useRef<number | null>(null)
  const touchStartTime = useRef<number>(0)
  const longPressTimer = useRef<ReturnType<typeof setTimeout> | null>(null)
  const sentRef = useRef(false) // evita doble envío si onTouchEnd llega tras longpress

  const activePhrase = phrases[activeIndex] ?? phrases[0]

  const goNext = useCallback(() => {
    setActiveIndex(i => {
      const next = (i + 1) % phrases.length
      vibrate([80])
      if (canHear) speak(`${phrases[next].text}. Frase ${next + 1} de ${phrases.length}`)
      return next
    })
  }, [phrases, vibrate, speak, canHear])

  const goPrev = useCallback(() => {
    setActiveIndex(i => {
      const prev = (i - 1 + phrases.length) % phrases.length
      vibrate([80, 60, 80])
      if (canHear) speak(`${phrases[prev].text}. Frase ${prev + 1} de ${phrases.length}`)
      return prev
    })
  }, [phrases, vibrate, speak, canHear])

  const sendActive = useCallback(() => {
    if (!activePhrase) return
    sentRef.current = true
    onSend(activePhrase)
    vibrate([...activePhrase.vibration, 150, 300]) // patrón de la frase + confirmación
    if (canHear) speak(`Enviando: ${activePhrase.text}`)
  }, [activePhrase, onSend, vibrate, speak, canHear])

  const handleTouchStart = useCallback((e: React.TouchEvent) => {
    touchStartX.current = e.touches[0].clientX
    touchStartTime.current = Date.now()
    sentRef.current = false

    // Iniciar timer de toque largo
    longPressTimer.current = setTimeout(() => {
      sendActive()
    }, 500)
  }, [sendActive])

  const handleTouchMove = useCallback((e: React.TouchEvent) => {
    // Si hay movimiento horizontal significativo, cancelar el long press
    if (touchStartX.current === null) return
    const dx = Math.abs(e.touches[0].clientX - touchStartX.current)
    if (dx > 10 && longPressTimer.current) {
      clearTimeout(longPressTimer.current)
      longPressTimer.current = null
    }
  }, [])

  const handleTouchEnd = useCallback((e: React.TouchEvent) => {
    if (longPressTimer.current) {
      clearTimeout(longPressTimer.current)
      longPressTimer.current = null
    }
    if (sentRef.current) return // ya se envió por long press

    if (touchStartX.current === null) return
    const dx = e.changedTouches[0].clientX - touchStartX.current
    const dt = Date.now() - touchStartTime.current
    touchStartX.current = null

    const SWIPE_THRESHOLD = 50

    if (Math.abs(dx) >= SWIPE_THRESHOLD) {
      // Swipe
      if (dx > 0) goNext()
      else goPrev()
    } else if (dt < 300) {
      // Toque corto — anunciar frase actual
      if (canHear) speak(`${activePhrase.text}. Frase ${activeIndex + 1} de ${phrases.length}`)
      else vibrate([50, 30, 50]) // pulso de confirmación si no hay audio
    }
  }, [activeIndex, activePhrase, phrases, goNext, goPrev, vibrate, speak, canHear])

  // Anunciar frase inicial al montar
  useEffect(() => {
    if (phrases.length === 0) return
    const timer = setTimeout(() => {
      if (canHear) {
        // Ejecutar speak directamente después del delay (no dentro del setTimeout callback)
        requestAnimationFrame(() => {
          speak(`Navegación háptica activa. ${phrases[0].text}. Frase 1 de ${phrases.length}. Desliza para cambiar. Mantén presionado para enviar.`, false)
        })
      } else {
        vibrate([100, 80, 100]) // señal de inicio
      }
    }, 800)
    return () => clearTimeout(timer)
  }, []) // eslint-disable-line react-hooks/exhaustive-deps

  if (phrases.length === 0) return null

  return (
    <div
      className="flex-1 flex flex-col items-center justify-center select-none touch-none"
      onTouchStart={handleTouchStart}
      onTouchMove={handleTouchMove}
      onTouchEnd={handleTouchEnd}
      aria-label={`Navegación háptica. Frase activa: ${activePhrase.text}. Frase ${activeIndex + 1} de ${phrases.length}. Desliza derecha para siguiente, izquierda para anterior. Mantén presionado para enviar.`}
      role="region"
    >
      {/* Indicadores de navegación */}
      <div className="flex items-center gap-4 mb-6 text-muted-foreground text-sm" aria-hidden="true">
        <span>← anterior</span>
        <span className="text-xs">{activeIndex + 1}/{phrases.length}</span>
        <span>siguiente →</span>
      </div>

      {/* Frase activa — zona de toque principal */}
      <div
        className="w-full flex-1 flex items-center justify-center bg-primary/10 rounded-2xl border-2 border-primary mx-2 p-6"
        aria-hidden="true"
      >
        <span className="text-3xl sm:text-4xl lg:text-5xl font-bold text-foreground text-center">
          {activePhrase.text}
        </span>
      </div>

      {/* Instrucciones visuales */}
      <p className="mt-4 text-muted-foreground text-xs text-center px-4" aria-hidden="true">
        Toca para anunciar · Desliza para cambiar · Mantén para enviar
      </p>

      {/* Puntos de posición */}
      <div className="flex gap-1.5 mt-3" aria-hidden="true">
        {phrases.map((_, i) => (
          <div
            key={i}
            className={`w-2 h-2 rounded-full transition-all ${i === activeIndex ? "bg-primary w-4" : "bg-muted"}`}
          />
        ))}
      </div>
    </div>
  )
}

type BlindInterfaceProps = {
  profile: Profile
  canSpeak: boolean
  canHear: boolean
  isListening: boolean
  allPhrases: Phrase[]
  onToggleVoice: () => void
  onSendPhrase: (p: Phrase) => void
  onEmergency: () => void
  onChangeProfile: () => void
  vibrate: (pattern: number[]) => void
  speak: (text: string, cancel?: boolean) => void
  emergencyDialogOpen: boolean
  setEmergencyDialogOpen: (v: boolean) => void
  confirmEmergency: () => void
}

function BlindInterface({
  profile, canSpeak, canHear, isListening, allPhrases,
  onToggleVoice, onSendPhrase, onEmergency, onChangeProfile,
  vibrate, speak, emergencyDialogOpen, setEmergencyDialogOpen, confirmEmergency,
}: BlindInterfaceProps) {
  const announcedRef = useRef(false)

  useEffect(() => {
    // Anuncio automático al entrar al modo ciego — solo una vez al montar
    if (announcedRef.current) return
    announcedRef.current = true
    const label = profile.deaf
      ? "Modo ciego y sordo activado. La vibración es tu canal principal."
      : profile.mute
        ? "Modo ciego y mudo activado. Usa las frases rápidas para comunicarte."
        : "Modo ciego activado. Toca el botón central para hablar."
    // Pequeño delay para que TalkBack termine de anunciar el cambio de pantalla
    const timer = setTimeout(() => {
      requestAnimationFrame(() => speak(label, false))
    }, 600)
    return () => clearTimeout(timer)
  }, [profile]) // eslint-disable-line react-hooks/exhaustive-deps

  return (
    <main className="h-dvh flex flex-col bg-background text-foreground p-3 sm:p-4 gap-2 sm:gap-3">
      <Button
        variant="ghost"
        aria-label="Cambiar perfil de accesibilidad"
        onClick={onChangeProfile}
        className="text-muted-foreground self-start text-sm sm:text-base h-10 sm:h-12 px-4"
      >
        Cambiar perfil
      </Button>

      {canSpeak ? (
        <Button
          aria-label={isListening ? "Detener escucha de voz" : "Activar reconocimiento de voz"}
          aria-pressed={isListening}
          onClick={onToggleVoice}
          className={`flex-1 text-xl sm:text-2xl lg:text-3xl font-bold transition-all ${
            isListening
              ? "bg-destructive hover:bg-destructive/90 animate-pulse-listening"
              : "bg-primary hover:bg-primary/90"
          }`}
        >
          {isListening ? "ESCUCHANDO..." : "TOCAR PARA HABLAR"}
        </Button>
      ) : (
        // Ciego+mudo o Todos: navegación háptica si no hay audio, grid si hay audio
        !canHear ? (
          <HapticPhraseNavigator
            phrases={allPhrases}
            onSend={onSendPhrase}
            vibrate={vibrate}
            speak={speak}
            canHear={canHear}
          />
        ) : (
          <div
            className="flex-1 grid grid-cols-2 gap-2 sm:gap-3 overflow-y-auto"
            role="group"
            aria-label={`Frases rápidas. ${allPhrases.length} disponibles`}
          >
            {allPhrases.map((p, i) => (
              <Button
                key={p.id}
                aria-label={`Frase ${i + 1} de ${allPhrases.length}: ${p.text}. Patrón de vibración: ${p.vibration.join('-')} milisegundos`}
                aria-setsize={allPhrases.length}
                aria-posinset={i + 1}
                onClick={() => onSendPhrase(p)}
                className="text-lg sm:text-xl lg:text-2xl bg-primary hover:bg-primary/90 text-primary-foreground"
              >
                {p.text}
              </Button>
            ))}
          </div>
        )
      )}

      <div className="grid grid-cols-2 gap-2 sm:gap-3">
        <Button
          aria-label="Enviar alerta de emergencia"
          onClick={onEmergency}
          className="h-16 sm:h-20 lg:h-24 text-base sm:text-lg lg:text-xl font-bold bg-destructive hover:bg-destructive/90 text-destructive-foreground"
        >
          EMERGENCIA
        </Button>
        {!canHear ? (
          <Button
            aria-label="Probar vibración del dispositivo. Toca para verificar que la vibración funciona correctamente"
            onClick={() => { vibrate([100, 100, 100]); speak("Vibración activa") }}
            className="h-16 sm:h-20 lg:h-24 text-base sm:text-lg lg:text-xl bg-muted hover:bg-muted/90 text-muted-foreground"
          >
            PROBAR VIBRACIÓN
          </Button>
        ) : (
          <Button
            aria-label="Cambiar perfil de accesibilidad"
            onClick={onChangeProfile}
            className="h-16 sm:h-20 lg:h-24 text-base sm:text-lg lg:text-xl bg-muted hover:bg-muted/90 text-muted-foreground"
          >
            PERFIL
          </Button>
        )}
      </div>

      <AlertDialog open={emergencyDialogOpen} onOpenChange={setEmergencyDialogOpen}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>¿Enviar alerta de emergencia?</AlertDialogTitle>
            <AlertDialogDescription>
              Se enviará el mensaje "🆘 EMERGENCIA" y se llamará al número de emergencias. Esta acción no se puede deshacer.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel onClick={() => vibrate([50])}>Cancelar</AlertDialogCancel>
            <AlertDialogAction
              onClick={confirmEmergency}
              className="bg-destructive text-destructive-foreground hover:bg-destructive/90"
            >
              Confirmar emergencia
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </main>
  )
}

export default function UniConnect() {
  const isMobile = useIsMobile()

  // Persistencia: cargar perfil y mensajes desde localStorage al montar
  const [profile, setProfile] = useState<Profile | null>(() => {
    if (typeof window === "undefined") return null
    try {
      const saved = localStorage.getItem("uniconnect-profile")
      return saved ? (JSON.parse(saved) as Profile) : null
    } catch { return null }
  })
  const [messages, setMessages] = useState<Array<{ id: string; text: string; from: "me" | "other"; time: Date; isAlert?: boolean }>>(() => {
    if (typeof window === "undefined") return []
    try {
      const saved = localStorage.getItem("uniconnect-messages")
      if (!saved) return []
      // Deserializar fechas correctamente
      return (JSON.parse(saved) as Array<{ id: string; text: string; from: "me" | "other"; time: string; isAlert?: boolean }>)
        .map(m => ({ ...m, time: new Date(m.time) }))
        .slice(-100) // máximo 100 mensajes en memoria
    } catch { return [] }
  })
  const [inputText, setInputText] = useState("")
  const [isListening, setIsListening] = useState(false)
  const [emergencyDialogOpen, setEmergencyDialogOpen] = useState(false)
  const [selectedProfileLabel, setSelectedProfileLabel] = useState("")
  const [screenOff, setScreenOff] = useState(false)
  const [newMessageFlash, setNewMessageFlash] = useState(false)
  const [customPhrases, setCustomPhrases] = useState<Phrase[]>(() => {
    if (typeof window === "undefined") return []
    try {
      const saved = localStorage.getItem("uniconnect-custom-phrases")
      return saved ? JSON.parse(saved) : []
    } catch { return [] }
  })
  const [newPhraseText, setNewPhraseText] = useState("")
  const [showAddPhrase, setShowAddPhrase] = useState(false)
  const [configDrawerOpen, setConfigDrawerOpen] = useState(false)
  const [micError, setMicError] = useState<string | null>(null)
  const [audioLevel, setAudioLevel] = useState(0)
  const audioLevelRef = useRef<ReturnType<typeof setInterval> | null>(null)

  // Config persistida: TTS, vibración, alto contraste, velocidad TTS, idioma TTS
  const [config, setConfig] = useState<{
    ttsEnabled: boolean
    vibrationEnabled: boolean
    highContrast: boolean
    ttsRate: number
    ttsLang: string
  }>(() => {
    if (typeof window === "undefined") return { ttsEnabled: true, vibrationEnabled: true, highContrast: false, ttsRate: 0.9, ttsLang: "es-CO" }
    try {
      const saved = localStorage.getItem("uniconnect-config")
      return saved ? JSON.parse(saved) : { ttsEnabled: true, vibrationEnabled: true, highContrast: false, ttsRate: 0.9, ttsLang: "es-CO" }
    } catch { return { ttsEnabled: true, vibrationEnabled: true, highContrast: false, ttsRate: 0.9, ttsLang: "es-CO" } }
  })
  
  const [lastActivity, setLastActivity] = useState(Date.now())
  const [vibrateFlash, setVibrateFlash] = useState(false)
  const [unreadCount, setUnreadCount] = useState(0)
  const lastActivityRef = useRef(Date.now())
  const profileRef = useRef(profile)
  const recognitionRef = useRef<SpeechRecognition | null>(null)
  const synthRef = useRef<SpeechSynthesis | null>(null)
  const messagesEndRef = useRef<HTMLDivElement>(null)
  const wakeLockRef = useRef<WakeLockSentinel | null>(null)
  const analyserRef = useRef<AnalyserNode | null>(null)
  const audioContextRef = useRef<AudioContext | null>(null)
  const animFrameRef = useRef<number | null>(null)

  // Persistencia: guardar perfil en localStorage cuando cambia
  useEffect(() => {
    if (typeof window === "undefined") return
    try {
      if (profile === null) {
        localStorage.removeItem("uniconnect-profile")
      } else {
        localStorage.setItem("uniconnect-profile", JSON.stringify(profile))
      }
    } catch { /* localStorage no disponible (modo privado, cuota llena) */ }
  }, [profile])

  // Actualizar profileRef cuando cambia el profile
  useEffect(() => {
    profileRef.current = profile
  }, [profile])

  // Persistencia: guardar últimos 100 mensajes en localStorage cuando cambian
  useEffect(() => {
    if (typeof window === "undefined") return
    try {
      localStorage.setItem("uniconnect-messages", JSON.stringify(messages.slice(-100)))
    } catch { /* localStorage no disponible */ }
  }, [messages])

  // Persistencia: guardar frases personalizadas en localStorage cuando cambian
  useEffect(() => {
    if (typeof window === "undefined") return
    try {
      localStorage.setItem("uniconnect-custom-phrases", JSON.stringify(customPhrases))
    } catch { /* localStorage no disponible */ }
  }, [customPhrases])

  // Web Share Target: leer texto compartido desde otras apps (WhatsApp, SMS, etc.)
  // Android pasa el texto como ?share_text=... al abrir la PWA desde el share sheet
  useEffect(() => {
    if (typeof window === "undefined") return
    const params = new URLSearchParams(window.location.search)
    const sharedText = params.get("share_text") || params.get("text") || params.get("share_title")
    if (sharedText) {
      setInputText(sharedText.trim())
      // Limpiar la URL para que no se repita en recargas
      window.history.replaceState({}, "", window.location.pathname)
      toast.info(`Texto recibido: "${sharedText.trim().slice(0, 40)}${sharedText.length > 40 ? "…" : ""}"`, { duration: 4000 })
    }
  }, []) // eslint-disable-line react-hooks/exhaustive-deps

  // Persistencia: guardar config en localStorage cuando cambia
  useEffect(() => {
    if (typeof window === "undefined") return
    try { localStorage.setItem("uniconnect-config", JSON.stringify(config)) } catch {}
  }, [config])

  // Alto contraste: aplicar/quitar clase en <html> cuando cambia config.highContrast
  useEffect(() => {
    document.documentElement.classList.toggle("high-contrast", config.highContrast)
  }, [config.highContrast])

  // Screen Wake Lock — evita que la pantalla se apague durante conversación activa
  useEffect(() => {
    if (!profile || typeof navigator === "undefined") return
    let released = false

    const requestWakeLock = async () => {
      try {
        if ('wakeLock' in navigator) {
          wakeLockRef.current = await navigator.wakeLock.request('screen')
          wakeLockRef.current.addEventListener('release', () => {
            if (!released) requestWakeLock() // re-adquirir si el sistema lo liberó
          })
        }
      } catch { /* dispositivo no soporta WakeLock o está en background */ }
    }

    requestWakeLock()

    return () => {
      released = true
      wakeLockRef.current?.release().catch(() => {})
      wakeLockRef.current = null
    }
  }, [profile])

  // Page Visibility API — pausar SpeechRecognition y TTS cuando la app va a background
  useEffect(() => {
    if (typeof document === "undefined") return

    const handleVisibilityChange = () => {
      if (document.hidden) {
        // App en background: detener micrófono y TTS para ahorrar batería
        try { recognitionRef.current?.abort() } catch {}
        try { synthRef.current?.cancel() } catch {}
        setIsListening(false)
        if (audioLevelRef.current) { clearInterval(audioLevelRef.current); audioLevelRef.current = null }
        setAudioLevel(0)
      } else {
        // App vuelve al foreground: re-adquirir WakeLock si se perdió
        if ('wakeLock' in navigator && wakeLockRef.current === null && profileRef.current) {
          navigator.wakeLock.request('screen')
            .then(lock => { wakeLockRef.current = lock })
            .catch(() => {})
        }
      }
    }

    document.addEventListener('visibilitychange', handleVisibilityChange)
    return () => document.removeEventListener('visibilitychange', handleVisibilityChange)
  }, [])

  useEffect(() => {
    if (typeof window !== "undefined") {
      synthRef.current = window.speechSynthesis
      const SR = window.SpeechRecognition || window.webkitSpeechRecognition
      if (SR) {
        recognitionRef.current = new SR()
        recognitionRef.current.continuous = true
        recognitionRef.current.lang = "es-CO"
      }
    }
    // Cleanup al desmontar: detener reconocimiento y síntesis activos
    return () => {
      try { recognitionRef.current?.abort() } catch { /* ya estaba inactivo */ }
      try { synthRef.current?.cancel() } catch { /* ya estaba inactivo */ }
    }
  }, [])

  useEffect(() => {
    const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches
    messagesEndRef.current?.scrollIntoView({ behavior: prefersReducedMotion ? "auto" : "smooth" })
  }, [messages])

  // Bug #16 fix: condición corregida — el timer debe activarse para cualquier perfil ciego,
  // incluyendo ciego+sordo y ciego+sordo+mudo. La condición original (!blind || deaf)
  // excluía incorrectamente a ciego+sordo.
  useEffect(() => {
    if (!profile?.blind) return
    const interval = setInterval(() => {
      if (Date.now() - lastActivityRef.current > 8000) setScreenOff(true)
    }, 1000)
    return () => clearInterval(interval)
  }, [profile])

  const vibrate = useCallback((pattern: number[]) => {
    if (!config.vibrationEnabled) return
    if (!isMobile) return
    navigator.vibrate?.(pattern)
    // Feedback visual sincronizado con vibración — útil en tablets/escritorio sin motor háptico
    setVibrateFlash(true)
    setTimeout(() => setVibrateFlash(false), pattern.reduce((a, b) => a + b, 0) + 100)
  }, [config.vibrationEnabled, isMobile])

  const speak = useCallback((text: string, cancel: boolean = true) => {
    if (!config.ttsEnabled || profile?.deaf) return
    if (!synthRef.current) return
    try {
      if (cancel) synthRef.current.cancel()
      const u = new SpeechSynthesisUtterance(text)
      u.lang = config.ttsLang
      u.rate = config.ttsRate
      u.onerror = () => { /* TTS falló — vibración es el canal de respaldo */ }
      u.onend = () => { /* TTS completado exitosamente */ }
      synthRef.current.speak(u)
    } catch { /* Fallo silencioso */ }
  }, [profile, config.ttsEnabled, config.ttsLang, config.ttsRate])

  const wake = useCallback(() => {
    setScreenOff(false)
    const now = Date.now()
    setLastActivity(now)
    lastActivityRef.current = now
    vibrate([50])
    speak("Pantalla activa")
  }, [vibrate, speak])

  const sendMessage = useCallback((text: string, shouldVibrate: boolean = true) => {
    if (!text.trim()) return
    setMessages(prev => [...prev, { id: `msg-${Date.now()}-${Math.random()}`, text: text.trim(), from: "me", time: new Date() }])
    setInputText("")
    const now = Date.now()
    setLastActivity(now)
    lastActivityRef.current = now
    if (shouldVibrate) vibrate([100])
    speak("Enviado")
    toast.success("Mensaje enviado", { duration: 3000 })

    // ─── PUNTO DE EXTENSIÓN PARA BACKEND REAL ────────────────────────────────
    // Reemplazar este setTimeout con una llamada WebSocket o fetch:
    //
    //   socket.emit("message", { text: text.trim(), roomId, senderId })
    //
    //   socket.on("message", (msg) => {
    //     setMessages(prev => [...prev, { ...msg, time: new Date(msg.time) }])
    //     // ... lógica de vibración/TTS según perfil
    //   })
    //
    // El estado `messages` y `profile` ya persisten en localStorage,
    // por lo que la reconexión recupera el contexto automáticamente.
    // ─────────────────────────────────────────────────────────────────────────
    setTimeout(() => {
      const reply = "Recibido"
      setMessages(prev => [...prev, { id: `msg-${Date.now()}-${Math.random()}`, text: reply, from: "other", time: new Date() }])
      const now = Date.now()
      setLastActivity(now)
      lastActivityRef.current = now
      toast.info(`Mensaje recibido: ${reply}`, { duration: 4000 })

      if (profile?.blind && profile?.deaf) {
        const len = reply.length
        if (len <= 4) {
          vibrate([400])
        } else if (len <= 15) {
          vibrate([200, 100, 200])
        } else {
          vibrate([150, 80, 150, 80, 150])
        }
        const morsePattern = textToMorse(reply, 3)
        if (morsePattern.length > 0) {
          const phase1Duration = (len <= 4 ? 400 : len <= 15 ? 500 : 630) + 500
          setTimeout(() => navigator.vibrate?.(morsePattern), phase1Duration)
        }
      } else if (profile?.blind) {
        vibrate([200, 100, 200])
        speak("Mensaje recibido: " + reply)
      } else if (profile?.deaf) {
        vibrate([80, 60, 80])
        setNewMessageFlash(true)
        setTimeout(() => setNewMessageFlash(false), 1200)
      } else {
        vibrate([100])
        speak(reply)
      }
    }, 1000)
  }, [profile, vibrate, speak])

  const toggleVoice = useCallback(() => {
    if (profileRef.current?.mute || !recognitionRef.current) return

    if (isListening) {
      recognitionRef.current.stop()
      setIsListening(false)
      return
    }

    const recognition = recognitionRef.current

    recognition.onresult = null
    recognition.onerror = null
    recognition.onend = null

    recognition.continuous = false
    recognition.interimResults = false
    recognition.lang = "es-CO"

    recognition.onresult = (e) => {
      const result = e.results[e.results.length - 1]
      if (!result.isFinal) return
      const text = result[0].transcript.trim()
      if (text) sendMessage(text)
    }

    recognition.onerror = (e: SpeechRecognitionErrorEvent) => {
      setIsListening(false)
      if (audioLevelRef.current) { clearInterval(audioLevelRef.current); audioLevelRef.current = null }
      setAudioLevel(0)
      if (e.error === "not-allowed") {
        const msg = "Permiso de micrófono denegado. Ve a Ajustes del navegador."
        speak(msg); vibrate([500, 100, 500]); toast.error(msg); setMicError(msg)
      } else if (e.error === "network") {
        const msg = "Sin conexión para reconocimiento de voz."
        speak(msg); vibrate([300, 100, 300]); toast.warning(msg); setMicError(msg)
      } else if (e.error === "no-speech") {
        const msg = "No se detectó voz. Intenta de nuevo."
        speak(msg); vibrate([200, 100, 200]); toast.warning(msg)
      } else {
        vibrate([300, 100, 300]); toast.error("Error en el reconocimiento de voz.")
      }
    }

    recognition.onend = () => {
      setIsListening(false)
      if (audioLevelRef.current) { clearInterval(audioLevelRef.current); audioLevelRef.current = null }
      setAudioLevel(0)
    }

    try {
      recognition.start()
      setIsListening(true)
      setMicError(null)
      vibrate([100, 50, 100])
      speak("Escuchando")
      audioLevelRef.current = setInterval(() => {
        setAudioLevel(Math.floor(Math.random() * 80) + 10)
      }, 150)
    } catch {
      recognition.stop()
      setTimeout(() => {
        try {
          recognition.start()
          setIsListening(true)
          vibrate([100, 50, 100])
        } catch {
          setIsListening(false)
          vibrate([300, 100, 300])
        }
      }, 300)
    }
  }, [isListening, profile, vibrate, speak, sendMessage])

  const sendPhrase = useCallback((p: Phrase) => {
    sendMessage(p.text, false) // false = no vibrar genérico
    vibrate(p.vibration)
  }, [sendMessage, vibrate])

  const addCustomPhrase = useCallback(() => {
    const text = newPhraseText.trim()
    if (!text) return
    setCustomPhrases(prev => [...prev, {
      id: Date.now(),
      text,
      vibration: [100, 80, 100], // patrón genérico para frases personalizadas
    }])
    setNewPhraseText("")
    setShowAddPhrase(false)
    vibrate([50, 30, 50])
  }, [newPhraseText, vibrate])

  const triggerEmergency = useCallback(() => {
    vibrate([300, 100, 300])
    speak("¿Confirmar emergencia?")
    setEmergencyDialogOpen(true)
  }, [vibrate, speak])

  const confirmEmergency = useCallback(() => {
    setEmergencyDialogOpen(false)
    vibrate([500, 200, 500, 200, 500])
    speak("Enviando emergencia")
    toast.error("🆘 EMERGENCIA enviada — llamando al número de emergencias", {
      duration: 6000,
    })
    setMessages(prev => [...prev, {
      id: `msg-${Date.now()}`,
      text: "🆘 EMERGENCIA",
      from: "me",
      time: new Date(),
      isAlert: true,
    }])
    setInputText("")
    const now = Date.now()
    setLastActivity(now)
    lastActivityRef.current = now
    const emergencyNumber = process.env.NEXT_PUBLIC_EMERGENCY_NUMBER ?? "123"
    window.location.href = `tel:${emergencyNumber}`
  }, [vibrate, speak])

  // SELECTOR DE PERFIL - Grid responsivo
  if (!profile) {
    return (
      <main className="h-dvh bg-background flex flex-col p-3 sm:p-4 lg:p-6">
        <div className="text-center mb-3 sm:mb-4">
          <h1 className="text-lg sm:text-xl lg:text-2xl font-bold text-foreground">UniConnect</h1>
          <p className="text-muted-foreground text-xs sm:text-sm">Selecciona tu perfil</p>
        </div>

        {/* Aviso de privacidad — Ley 1581 Colombia. Se muestra siempre en el selector de perfil. */}
        <p className="text-center text-muted-foreground text-[10px] sm:text-xs mb-2 px-2">
          Esta app puede recopilar datos de uso anónimos para mejorar la experiencia.
          No se recopilan datos personales identificables.{" "}
          <a
            href="https://www.sic.gov.co/proteccion-de-datos-personales"
            target="_blank"
            rel="noopener noreferrer"
            className="underline"
            aria-label="Política de protección de datos personales — Superintendencia de Industria y Comercio de Colombia"
          >
            Ley 1581 de 2012
          </a>
        </p>

        {/* Aviso de escritorio — vibración y algunas APIs no disponibles fuera de Android */}
        {!isMobile && (
          <Alert className="mb-2 mx-0">
            <AlertTitle>Modo escritorio detectado</AlertTitle>
            <AlertDescription>
              UniConnect está optimizada para Android. La vibración háptica y el reconocimiento de voz pueden no estar disponibles en este dispositivo.
            </AlertDescription>
          </Alert>
        )}

        {/* Anuncia el perfil seleccionado a TalkBack en cuanto cambia */}
        {selectedProfileLabel && (
          <p role="status" aria-live="assertive" aria-atomic="true" className="sr-only">
            Perfil seleccionado: {selectedProfileLabel}
          </p>
        )}

        <div
          className="flex-1 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-3 content-center max-w-4xl mx-auto w-full animate-slide-up"
          role="group"
          aria-label="Selecciona tu perfil de accesibilidad. 8 opciones disponibles."
        >
          <Button
            aria-label="Perfil Ciego: activa lector de pantalla y vibración"
            aria-setsize={8}
            aria-posinset={1}
            onClick={() => { vibrate([80]); setSelectedProfileLabel("Ciego"); setProfile({ blind: true, deaf: false, mute: false }) }}
            className="h-14 sm:h-16 lg:h-20 text-sm sm:text-base lg:text-lg bg-primary hover:bg-primary/90"
          >
            Ciego
          </Button>
          <Button
            aria-label="Perfil Sordo: activa vibración y alertas visuales"
            aria-setsize={8}
            aria-posinset={2}
            onClick={() => { vibrate([80]); setSelectedProfileLabel("Sordo"); setProfile({ blind: false, deaf: true, mute: false }) }}
            className="h-14 sm:h-16 lg:h-20 text-sm sm:text-base lg:text-lg bg-primary hover:bg-primary/90"
          >
            Sordo
          </Button>
          <Button
            aria-label="Perfil Mudo: activa frases rápidas y texto"
            aria-setsize={8}
            aria-posinset={3}
            onClick={() => { vibrate([80]); setSelectedProfileLabel("Mudo"); setProfile({ blind: false, deaf: false, mute: true }) }}
            className="h-14 sm:h-16 lg:h-20 text-sm sm:text-base lg:text-lg bg-primary hover:bg-primary/90"
          >
            Mudo
          </Button>
          <Button
            aria-label="Perfil Normal: sin restricciones de accesibilidad"
            aria-setsize={8}
            aria-posinset={4}
            onClick={() => { vibrate([80]); setSelectedProfileLabel("Normal"); setProfile({ blind: false, deaf: false, mute: false }) }}
            variant="outline"
            className="h-14 sm:h-16 lg:h-20 text-sm sm:text-base lg:text-lg"
          >
            Normal
          </Button>
          <Button
            aria-label="Perfil Ciego y Sordo: activa vibración como canal principal"
            aria-setsize={8}
            aria-posinset={5}
            onClick={() => { vibrate([80]); setSelectedProfileLabel("Ciego y Sordo"); setProfile({ blind: true, deaf: true, mute: false }) }}
            className="h-14 sm:h-16 lg:h-20 text-xs sm:text-sm lg:text-base bg-secondary text-secondary-foreground hover:bg-secondary/90"
          >
            Ciego + Sordo
          </Button>
          <Button
            aria-label="Perfil Ciego y Mudo: activa lector de pantalla y frases rápidas"
            aria-setsize={8}
            aria-posinset={6}
            onClick={() => { vibrate([80]); setSelectedProfileLabel("Ciego y Mudo"); setProfile({ blind: true, deaf: false, mute: true }) }}
            className="h-14 sm:h-16 lg:h-20 text-xs sm:text-sm lg:text-base bg-secondary text-secondary-foreground hover:bg-secondary/90"
          >
            Ciego + Mudo
          </Button>
          <Button
            aria-label="Perfil Sordo y Mudo: activa vibración y frases rápidas"
            aria-setsize={8}
            aria-posinset={7}
            onClick={() => { vibrate([80]); setSelectedProfileLabel("Sordo y Mudo"); setProfile({ blind: false, deaf: true, mute: true }) }}
            className="h-14 sm:h-16 lg:h-20 text-xs sm:text-sm lg:text-base bg-secondary text-secondary-foreground hover:bg-secondary/90"
          >
            Sordo + Mudo
          </Button>
          <Button
            aria-label="Perfil Todos: ciego, sordo y mudo. Usa vibración como único canal"
            aria-setsize={8}
            aria-posinset={8}
            onClick={() => { vibrate([80]); setSelectedProfileLabel("Todos: ciego, sordo y mudo"); setProfile({ blind: true, deaf: true, mute: true }) }}
            className="h-14 sm:h-16 lg:h-20 text-xs sm:text-sm lg:text-base bg-muted text-muted-foreground hover:bg-muted/90"
          >
            Todos
          </Button>
        </div>
      </main>
    )
  }

  // PANTALLA APAGADA
  if (screenOff) {
    return (
      <div
        role="dialog"
        aria-modal="true"
        aria-label="Pantalla en reposo. Toca para activar."
        className="h-dvh bg-background fixed inset-0 z-50 flex items-center justify-center"
        onClick={wake}
        onTouchStart={wake}
        onKeyDown={(e) => { if (e.key === "Enter" || e.key === " ") wake() }}
        tabIndex={0}
      >
        <span className="sr-only">
          Pantalla en reposo. Toca la pantalla o presiona Enter para activar.
        </span>
      </div>
    )
  }

  const canSee = !profile.blind
  const canHear = !profile.deaf
  const canSpeak = !profile.mute

  // INTERFAZ PARA CIEGO
  if (!canSee) {
    // Anuncio automático al entrar al modo ciego — se ejecuta una sola vez al montar
    // Usamos un ref para evitar que el efecto se dispare en re-renders
    return (
      <BlindInterface
        profile={profile}
        canSpeak={canSpeak}
        canHear={canHear}
        isListening={isListening}
        allPhrases={[...PHRASES, ...customPhrases]}
        onToggleVoice={toggleVoice}
        onSendPhrase={sendPhrase}
        onEmergency={triggerEmergency}
        onChangeProfile={() => { vibrate([60]); setProfile(null) }}
        vibrate={vibrate}
        speak={speak}
        emergencyDialogOpen={emergencyDialogOpen}
        setEmergencyDialogOpen={setEmergencyDialogOpen}
        confirmEmergency={confirmEmergency}
      />
    )
  }

  // INTERFAZ VISUAL
  return (
    <main className="h-dvh flex flex-col bg-background overflow-hidden">
      {/* Header compacto */}
      <header className="flex items-center justify-between px-2 py-1.5 sm:px-3 sm:py-2 border-b border-border shrink-0">
        <Button variant="ghost" size="sm" aria-label="Cambiar perfil de accesibilidad" onClick={() => { vibrate([60]); setProfile(null) }} className="h-7 sm:h-8 text-xs sm:text-sm px-2">
          Perfil
        </Button>
        <h1 className="sr-only">UniConnect</h1>
        <div className="flex items-center gap-1">
          <div className="flex gap-1" role="status" aria-label={`Perfil activo: ${[profile.deaf && "Sordo", profile.mute && "Mudo"].filter(Boolean).join(" y ") || "Normal"}`}>
            {profile.deaf && (
              <span className="bg-muted px-1.5 sm:px-2 py-0.5 rounded text-[10px] sm:text-xs" aria-label="Indicador de perfil: Sordo activo">Sordo</span>
            )}
            {profile.mute && (
              <span className="bg-muted px-1.5 sm:px-2 py-0.5 rounded text-[10px] sm:text-xs" aria-label="Indicador de perfil: Mudo activo">Mudo</span>
            )}
          </div>
          <Button variant="ghost" size="sm" aria-label="Abrir configuración" onClick={() => setConfigDrawerOpen(true)} className="h-7 sm:h-8 px-2 text-base">
            ⚙
          </Button>
        </div>
      </header>

      {/* Alert inline para errores de micrófono — persistente hasta que el usuario lo cierre */}
      {micError && (
        <Alert variant="destructive" className="mx-2 mt-2 shrink-0">
          <AlertTitle>Error de micrófono</AlertTitle>
          <AlertDescription className="flex items-center justify-between gap-2">
            <span>{micError}</span>
            <Button variant="ghost" size="sm" aria-label="Cerrar alerta de error" onClick={() => setMicError(null)} className="h-6 px-2 text-xs shrink-0">✕</Button>
          </AlertDescription>
        </Alert>
      )}

      {/* Indicador visual de mensaje nuevo para perfil sordo */}
      {profile.deaf && newMessageFlash && (
        <div role="alert" aria-live="assertive" aria-label="Mensaje nuevo recibido" className="fixed inset-0 pointer-events-none z-40 border-4 border-primary animate-pulse" />
      )}

      {/* Mensajes */}
      <div className="flex-1 overflow-y-auto p-2 sm:p-3 space-y-1.5" role="log" aria-live="polite" aria-label="Historial de mensajes" aria-relevant="additions">
        {messages.length === 0 && (
          <p className="text-center text-muted-foreground text-xs sm:text-sm py-4">Sin mensajes</p>
        )}
        {messages.map((m) => (
          <div
            key={m.id}
            role={m.isAlert ? "alert" : undefined}
            aria-label={`${m.from === "me" ? "Tú" : "Otro"}: ${m.text}, ${m.time.toLocaleTimeString("es-CO", { hour: "2-digit", minute: "2-digit" })}`}
            className={`px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-lg max-w-[80%] text-xs sm:text-sm lg:text-base animate-fade-in ${m.from === "me" ? "bg-primary text-primary-foreground ml-auto" : "bg-muted text-foreground"}`}
          >
            {m.text}
          </div>
        ))}
        <div ref={messagesEndRef} />
      </div>

      {/* Panel de controles con Tabs: Chat / Frases / Config */}
      <Tabs defaultValue="chat" className="shrink-0 border-t border-border bg-card">
        <TabsList className="w-full rounded-none border-b h-9 bg-card" aria-label="Secciones del panel de control">
          <TabsTrigger value="chat" className="flex-1 text-xs sm:text-sm">Chat</TabsTrigger>
          <TabsTrigger value="frases" className="flex-1 text-xs sm:text-sm">Frases</TabsTrigger>
          <TabsTrigger value="config" className="flex-1 text-xs sm:text-sm">Config</TabsTrigger>
        </TabsList>

        {/* Tab Chat: voz + emergencia + input */}
        <TabsContent value="chat" className="p-2 sm:p-3 space-y-2 mt-0">
          {/* Progress bar de nivel de audio — visible solo mientras escucha */}
          {isListening && (
            <Progress
              value={audioLevel}
              aria-label={`Nivel de audio del micrófono: ${audioLevel}%`}
              className="h-2"
            />
          )}
          <div className="grid grid-cols-2 gap-2">
            {canSpeak ? (
              <Button
                aria-label={isListening ? "Detener escucha de voz" : "Activar reconocimiento de voz"}
                aria-pressed={isListening}
                onClick={toggleVoice}
                className={`h-11 sm:h-12 lg:h-14 text-sm sm:text-base ${isListening ? "bg-destructive hover:bg-destructive/90 animate-pulse-listening" : "bg-primary hover:bg-primary/90"}`}
              >
                {isListening ? "Parar" : "Hablar"}
              </Button>
            ) : (
              <Button disabled aria-disabled="true" aria-label="Reconocimiento de voz no disponible en este perfil" className="h-11 sm:h-12 lg:h-14 text-sm sm:text-base bg-muted text-muted-foreground cursor-not-allowed">
                (Sin voz)
              </Button>
            )}
            <Button aria-label="Enviar alerta de emergencia" onClick={triggerEmergency} className="h-11 sm:h-12 lg:h-14 text-sm sm:text-base font-bold bg-destructive hover:bg-destructive/90 text-destructive-foreground">
              EMERGENCIA
            </Button>
          </div>
          <form onSubmit={e => { e.preventDefault(); sendMessage(inputText) }} aria-label="Formulario para escribir y enviar un mensaje" className="flex gap-2">
            <Input
              type="text" inputMode="text" autoComplete="off" autoCorrect="off"
              value={inputText} onChange={e => setInputText(e.target.value)}
              onFocus={() => vibrate([30])} placeholder="Escribe mensaje..."
              aria-label="Escribe tu mensaje" aria-describedby="input-hint"
              className="flex-1 h-10 sm:h-11 text-sm sm:text-base"
            />
            <span id="input-hint" className="sr-only">Puedes usar el micrófono de tu teclado Android para dictar. Pulsa Enviar o la tecla Enter para enviar.</span>
            <Button type="submit" disabled={!inputText.trim()} aria-disabled={!inputText.trim()} aria-describedby={!inputText.trim() ? "send-hint" : undefined} className="h-10 sm:h-11 px-4 sm:px-6 text-sm sm:text-base">
              Enviar
            </Button>
            {!inputText.trim() && <span id="send-hint" className="sr-only">Escribe un mensaje para poder enviarlo</span>}
          </form>
        </TabsContent>

        {/* Tab Frases: grid de frases + agregar personalizada */}
        <TabsContent value="frases" className="p-2 sm:p-3 space-y-2 mt-0">
          <div className="grid grid-cols-4 gap-1.5 sm:gap-2">
            {PHRASES.map(p => (
              <Button key={p.id} variant="outline" aria-label={`${p.text}. Patrón de vibración: ${p.vibration.join('-')} milisegundos`} onClick={() => sendPhrase(p)} className="h-9 sm:h-10 lg:h-12 text-[10px] sm:text-xs lg:text-sm px-1 flex flex-col gap-0.5">
                {p.icon && <span aria-hidden="true" className="text-base leading-none">{p.icon}</span>}
                <span>{p.text}</span>
              </Button>
            ))}
            {customPhrases.map(p => (
              <Button key={p.id} variant="outline" aria-label={`Frase personalizada: ${p.text}`} onClick={() => sendPhrase(p)} className="h-9 sm:h-10 lg:h-12 text-[10px] sm:text-xs lg:text-sm px-1 border-primary/50">
                {p.text}
              </Button>
            ))}
            <Button variant="ghost" aria-label="Agregar frase personalizada" onClick={() => setShowAddPhrase(v => !v)} className="h-9 sm:h-10 lg:h-12 text-[10px] sm:text-xs lg:text-sm px-1 border border-dashed border-border">
              + Frase
            </Button>
          </div>
          {showAddPhrase && (
            <form onSubmit={e => { e.preventDefault(); addCustomPhrase() }} aria-label="Agregar frase personalizada al banco de frases" className="flex gap-2">
              <Input type="text" inputMode="text" autoComplete="off" value={newPhraseText} onChange={e => setNewPhraseText(e.target.value)} placeholder="Escribe la frase..." aria-label="Texto de la nueva frase" className="flex-1 h-9 sm:h-10 text-xs sm:text-sm" autoFocus />
              <Button type="submit" disabled={!newPhraseText.trim()} aria-disabled={!newPhraseText.trim()} className="h-9 sm:h-10 text-xs sm:text-sm px-3">Guardar</Button>
              <Button type="button" variant="ghost" aria-label="Cancelar agregar frase" onClick={() => { setShowAddPhrase(false); setNewPhraseText("") }} className="h-9 sm:h-10 text-xs sm:text-sm px-3">✕</Button>
            </form>
          )}
        </TabsContent>

        {/* Tab Config: acceso rápido a switches + botón para abrir Drawer completo */}
        <TabsContent value="config" className="p-2 sm:p-3 space-y-3 mt-0">
          <div className="flex items-center justify-between">
            <label htmlFor="tts-quick" className="text-sm">Voz (TTS)</label>
            <Switch id="tts-quick" checked={config.ttsEnabled} onCheckedChange={v => setConfig(c => ({ ...c, ttsEnabled: v }))} aria-label="Activar o desactivar síntesis de voz" />
          </div>
          <div className="flex items-center justify-between">
            <label htmlFor="vib-quick" className="text-sm">Vibración</label>
            <Switch id="vib-quick" checked={config.vibrationEnabled} onCheckedChange={v => setConfig(c => ({ ...c, vibrationEnabled: v }))} aria-label="Activar o desactivar vibración" />
          </div>
          <div className="flex items-center justify-between">
            <label htmlFor="hc-quick" className="text-sm">Alto contraste</label>
            <Switch id="hc-quick" checked={config.highContrast} onCheckedChange={v => setConfig(c => ({ ...c, highContrast: v }))} aria-label="Activar o desactivar modo de alto contraste" />
          </div>
          <Button variant="outline" className="w-full text-sm" onClick={() => setConfigDrawerOpen(true)} aria-label="Abrir configuración completa con velocidad de voz e idioma">
            Más opciones ⚙
          </Button>
        </TabsContent>
      </Tabs>

      {/* Drawer de configuración completa */}
      <Drawer open={configDrawerOpen} onOpenChange={setConfigDrawerOpen}>
        <DrawerContent>
          <DrawerHeader>
            <DrawerTitle>Configuración</DrawerTitle>
            <DrawerDescription>Personaliza UniConnect según tus necesidades</DrawerDescription>
          </DrawerHeader>
          <div className="p-4 space-y-6 overflow-y-auto">
            {/* TTS toggle */}
            <div className="flex items-center justify-between">
              <label htmlFor="tts-drawer" className="text-sm font-medium">Voz (TTS)</label>
              <Switch id="tts-drawer" checked={config.ttsEnabled} onCheckedChange={v => setConfig(c => ({ ...c, ttsEnabled: v }))} aria-label="Activar o desactivar síntesis de voz" />
            </div>
            {/* Velocidad TTS */}
            {config.ttsEnabled && (
              <div className="space-y-2">
                <label className="text-sm font-medium">Velocidad de voz: {config.ttsRate.toFixed(1)}×</label>
                <Slider
                  min={0.5} max={2} step={0.1}
                  value={[config.ttsRate]}
                  onValueChange={([v]) => setConfig(c => ({ ...c, ttsRate: v }))}
                  aria-label={`Velocidad de síntesis de voz: ${config.ttsRate.toFixed(1)} veces`}
                />
                <div className="flex justify-between text-xs text-muted-foreground" aria-hidden="true">
                  <span>Lento (0.5×)</span><span>Normal (1×)</span><span>Rápido (2×)</span>
                </div>
              </div>
            )}
            {/* Idioma TTS */}
            {config.ttsEnabled && (
              <div className="space-y-2">
                <label className="text-sm font-medium">Idioma de voz</label>
                <Select value={config.ttsLang} onValueChange={v => setConfig(c => ({ ...c, ttsLang: v }))}>
                  <SelectTrigger aria-label="Seleccionar idioma de síntesis de voz" className="w-full">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="es-CO">Español (Colombia)</SelectItem>
                    <SelectItem value="es-ES">Español (España)</SelectItem>
                    <SelectItem value="es-MX">Español (México)</SelectItem>
                    <SelectItem value="es-AR">Español (Argentina)</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            )}
            {/* Vibración toggle */}
            <div className="flex items-center justify-between">
              <label htmlFor="vib-drawer" className="text-sm font-medium">Vibración</label>
              <Switch id="vib-drawer" checked={config.vibrationEnabled} onCheckedChange={v => setConfig(c => ({ ...c, vibrationEnabled: v }))} aria-label="Activar o desactivar vibración" />
            </div>
            {/* Alto contraste toggle */}
            <div className="flex items-center justify-between">
              <label htmlFor="hc-drawer" className="text-sm font-medium">Alto contraste</label>
              <Switch id="hc-drawer" checked={config.highContrast} onCheckedChange={v => setConfig(c => ({ ...c, highContrast: v }))} aria-label="Activar o desactivar modo de alto contraste" />
            </div>
          </div>
          <DrawerFooter>
            <DrawerClose asChild>
              <Button variant="outline" aria-label="Cerrar panel de configuración">Cerrar</Button>
            </DrawerClose>
          </DrawerFooter>
        </DrawerContent>
      </Drawer>

      {/* Diálogo de confirmación de emergencia */}
      <AlertDialog open={emergencyDialogOpen} onOpenChange={setEmergencyDialogOpen}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>¿Enviar alerta de emergencia?</AlertDialogTitle>
            <AlertDialogDescription>
              Se enviará el mensaje "🆘 EMERGENCIA" y se llamará al número de emergencias. Esta acción no se puede deshacer.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel onClick={() => vibrate([50])}>Cancelar</AlertDialogCancel>
            <AlertDialogAction onClick={confirmEmergency} className="bg-destructive text-destructive-foreground hover:bg-destructive/90">
              Confirmar emergencia
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </main>
  )
}
