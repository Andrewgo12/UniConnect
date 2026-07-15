"use client"

import { useState, useEffect, useRef, useCallback } from "react"

interface UseSpeechRecognitionProps {
  onResult: (text: string) => void
  lang?: string
}

export function useSpeechRecognition({ onResult, lang = "es-CO" }: UseSpeechRecognitionProps) {
  const [isListening, setIsListening] = useState(false)
  const [micError, setMicError] = useState<string | null>(null)
  const [audioLevel, setAudioLevel] = useState(0)

  const recognitionRef = useRef<any>(null)
  const isStartingRef = useRef(false)
  const audioIntervalRef = useRef<ReturnType<typeof setInterval> | null>(null)

  useEffect(() => {
    if (typeof window === "undefined") return

    const SpeechRecognitionClass = (window as any).SpeechRecognition || (window as any).webkitSpeechRecognition
    if (SpeechRecognitionClass) {
      const rec = new SpeechRecognitionClass()
      rec.continuous = false // Cambiado a false para que se detenga automáticamente al terminar de hablar, o según prefieras
      rec.interimResults = false
      rec.lang = lang
      recognitionRef.current = rec
    }

    return () => {
      if (recognitionRef.current) {
        try {
          recognitionRef.current.abort()
        } catch { /* ignore */ }
      }
      if (audioIntervalRef.current) {
        clearInterval(audioIntervalRef.current)
      }
    }
  }, [lang])

  const stopListening = useCallback(() => {
    if (!recognitionRef.current) return
    try {
      recognitionRef.current.stop()
    } catch { /* ignore */ }
    setIsListening(false)
    isStartingRef.current = false
    if (audioIntervalRef.current) {
      clearInterval(audioIntervalRef.current)
      audioIntervalRef.current = null
    }
    setAudioLevel(0)
  }, [])

  const startListening = useCallback(() => {
    if (!recognitionRef.current || isListening || isStartingRef.current) return

    isStartingRef.current = true
    setMicError(null)

    recognitionRef.current.onresult = (event: any) => {
      const result = event.results[event.results.length - 1]
      if (result && result.isFinal) {
        const text = result[0].transcript.trim()
        if (text) {
          onResult(text)
        }
      }
    }

    recognitionRef.current.onerror = (event: any) => {
      console.error("Speech recognition error:", event.error)
      stopListening()

      if (event.error === "not-allowed") {
        setMicError("Permiso de micrófono denegado. Ve a Ajustes del navegador.")
      } else if (event.error === "network") {
        setMicError("Sin conexión para reconocimiento de voz.")
      } else if (event.error === "no-speech") {
        setMicError("No se detectó voz. Intenta de nuevo.")
      } else {
        setMicError(`Error de reconocimiento de voz: ${event.error}`)
      }
    }

    recognitionRef.current.onend = () => {
      setIsListening(false)
      isStartingRef.current = false
      if (audioIntervalRef.current) {
        clearInterval(audioIntervalRef.current)
        audioIntervalRef.current = null
      }
      setAudioLevel(0)
    }

    try {
      recognitionRef.current.start()
      setIsListening(true)
      isStartingRef.current = false

      // Simular niveles de audio para feedback visual
      audioIntervalRef.current = setInterval(() => {
        setAudioLevel(Math.floor(Math.random() * 80) + 10)
      }, 150)
    } catch (err) {
      console.error("Fallo al iniciar el reconocimiento de voz:", err)
      isStartingRef.current = false
      setIsListening(false)
    }
  }, [isListening, onResult, stopListening])

  const toggleListening = useCallback(() => {
    if (isListening) {
      stopListening()
    } else {
      startListening()
    }
  }, [isListening, startListening, stopListening])

  return {
    isListening,
    micError,
    setMicError,
    audioLevel,
    startListening,
    stopListening,
    toggleListening,
  }
}
