"use client"

import { useCallback } from "react"
import { textToMorse } from "../lib/morse"

interface UseVibrationProps {
  vibrationEnabled?: boolean
}

export function useVibration({ vibrationEnabled = true }: UseVibrationProps = {}) {
  const vibrate = useCallback((pattern: number[]) => {
    if (!vibrationEnabled) return
    if (typeof navigator !== "undefined" && navigator.vibrate) {
      try {
        navigator.vibrate(pattern)
      } catch (err) {
        console.warn("navigator.vibrate failed:", err)
      }
    }
  }, [vibrationEnabled])

  const vibrateMorse = useCallback((text: string, maxWords = 3) => {
    if (!vibrationEnabled) return
    const pattern = textToMorse(text, maxWords)
    if (pattern.length > 0) {
      vibrate(pattern)
    }
  }, [vibrationEnabled, vibrate])

  return {
    vibrate,
    vibrateMorse,
  }
}

export default useVibration
