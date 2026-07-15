"use client"

import { useEffect, useRef, useCallback } from "react"

interface UseSpeechSynthesisProps {
  ttsEnabled?: boolean
  lang?: string
  rate?: number
}

export function useSpeechSynthesis({ ttsEnabled = true, lang = "es-CO", rate = 0.9 }: UseSpeechSynthesisProps = {}) {
  const synthRef = useRef<SpeechSynthesis | null>(null)

  useEffect(() => {
    if (typeof window !== "undefined") {
      synthRef.current = window.speechSynthesis
    }

    return () => {
      if (synthRef.current) {
        try {
          synthRef.current.cancel()
        } catch { /* ignore */ }
      }
    }
  }, [])

  const speak = useCallback((text: string, cancel: boolean = true) => {
    if (!ttsEnabled || !synthRef.current) return

    try {
      if (cancel) {
        synthRef.current.cancel()
      }

      const utterance = new SpeechSynthesisUtterance(text)
      utterance.lang = lang
      utterance.rate = rate

      synthRef.current.speak(utterance)
    } catch (err) {
      console.error("Speech synthesis failed:", err)
    }
  }, [ttsEnabled, lang, rate])

  const cancel = useCallback(() => {
    if (synthRef.current) {
      try {
        synthRef.current.cancel()
      } catch { /* ignore */ }
    }
  }, [])

  return {
    speak,
    cancel,
  }
}
export default useSpeechSynthesis
