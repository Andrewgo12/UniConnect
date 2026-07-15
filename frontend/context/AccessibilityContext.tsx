"use client"

import React, { createContext, useContext, useState, useEffect } from "react"

export type AccessibilityProfile = "blind" | "deaf" | "mute" | "deafmute" | "standard"
export type InputChannel = "voz" | "teclado" | "cámara"
export type OutputChannel = "audio" | "vibración" | "texto"

export interface HardwarePermissions {
  camera: boolean
  microphone: boolean
}

interface AccessibilityContextType {
  profile: AccessibilityProfile | null
  setProfile: (profile: AccessibilityProfile | null) => void
  inputChannel: InputChannel
  setInputChannel: (channel: InputChannel) => void
  outputChannel: OutputChannel
  setOutputChannel: (channel: OutputChannel) => void
  hardwarePermissions: HardwarePermissions
  requestHardwarePermissions: () => Promise<void>
}

const AccessibilityContext = createContext<AccessibilityContextType | undefined>(undefined)

export function AccessibilityProvider({ children }: { children: React.ReactNode }) {
  const [profile, setProfileState] = useState<AccessibilityProfile | null>(() => {
    if (typeof window !== "undefined") {
      const saved = localStorage.getItem("uniconnect-profile-v2") as AccessibilityProfile
      if (saved) return saved
      // Fallback a migrar viejo perfil si existe
      try {
        const oldSaved = localStorage.getItem("uniconnect-profile")
        if (oldSaved) {
          const parsed = JSON.parse(oldSaved)
          if (parsed.blind && parsed.deaf) return "deafmute"
          if (parsed.blind) return "blind"
          if (parsed.deaf) return "deaf"
          if (parsed.mute) return "mute"
        }
      } catch { /* ignore */ }
    }
    return null
  })

  const [inputChannel, setInputChannel] = useState<InputChannel>("teclado")
  const [outputChannel, setOutputChannel] = useState<OutputChannel>("texto")
  const [hardwarePermissions, setHardwarePermissions] = useState<HardwarePermissions>({
    camera: false,
    microphone: false,
  })

  // Sincronizar canales automáticos basados en el perfil
  useEffect(() => {
    if (typeof window !== "undefined") {
      if (profile) {
        localStorage.setItem("uniconnect-profile-v2", profile)
      } else {
        localStorage.removeItem("uniconnect-profile-v2")
      }
    }

    if (!profile) return

    switch (profile) {
      case "blind":
        setInputChannel("voz")
        setOutputChannel("audio")
        break
      case "deaf":
        setInputChannel("cámara")
        setOutputChannel("vibración")
        break
      case "mute":
        setInputChannel("teclado")
        setOutputChannel("texto")
        break
      case "deafmute":
        setInputChannel("cámara")
        setOutputChannel("vibración")
        break
      default:
        setInputChannel("teclado")
        setOutputChannel("texto")
        break
    }
  }, [profile])

  const requestHardwarePermissions = async () => {
    let cameraGranted = false
    let micGranted = false

    try {
      if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        // Intentar micrófono
        try {
          const micStream = await navigator.mediaDevices.getUserMedia({ audio: true })
          micStream.getTracks().forEach(track => track.stop())
          micGranted = true
        } catch {
          console.warn("Permiso de micrófono denegado.")
        }

        // Intentar cámara
        try {
          const camStream = await navigator.mediaDevices.getUserMedia({ video: true })
          camStream.getTracks().forEach(track => track.stop())
          cameraGranted = true
        } catch {
          console.warn("Permiso de cámara denegado.")
        }
      }
    } catch (err) {
      console.error("Error al solicitar permisos de hardware:", err)
    }

    setHardwarePermissions({
      camera: cameraGranted,
      microphone: micGranted,
    })
  }

  // Verificar permisos iniciales al montar si la API de permisos existe
  useEffect(() => {
    if (typeof navigator !== "undefined" && navigator.permissions && navigator.permissions.query) {
      Promise.all([
        navigator.permissions.query({ name: "camera" as PermissionName }).catch(() => null),
        navigator.permissions.query({ name: "microphone" as PermissionName }).catch(() => null)
      ]).then(([cameraPerm, micPerm]) => {
        setHardwarePermissions({
          camera: cameraPerm?.state === "granted",
          microphone: micPerm?.state === "granted",
        })
      })
    }
  }, [])

  return (
    <AccessibilityContext.Provider
      value={{
        profile,
        setProfile: setProfileState,
        inputChannel,
        setInputChannel,
        outputChannel,
        setOutputChannel,
        hardwarePermissions,
        requestHardwarePermissions,
      }}
    >
      {children}
    </AccessibilityContext.Provider>
  )
}

export function useAccessibility() {
  const context = useContext(AccessibilityContext)
  if (!context) {
    throw new Error("useAccessibility debe usarse dentro de un AccessibilityProvider")
  }
  return context
}
