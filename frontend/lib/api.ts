const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL || "http://localhost:8000"

async function parseJson(response: Response) {
  const text = await response.text()
  if (!text) return null
  try {
    return JSON.parse(text)
  } catch {
    return null
  }
}

async function apiFetch<T>(path: string, options: RequestInit = {}, token?: string): Promise<T> {
  const url = `${API_BASE_URL}${path}`
  const headers: Record<string, string> = {
    "Content-Type": "application/json",
    ...(options.headers as Record<string, string> || {}),
  }

  if (token) {
    headers.Authorization = `Bearer ${token}`
  }

  const response = await fetch(url, {
    ...options,
    headers,
    credentials: token ? "omit" : options.credentials,
  })

  const data = await parseJson(response)
  if (!response.ok) {
    const error = new Error(data?.message || `Request failed with status ${response.status}`) as Error & { status?: number; data?: unknown }
    error.status = response.status
    error.data = data
    throw error
  }

  return data as T
}

export type ProfileSettings = {
  blind: boolean
  deaf: boolean
  mute: boolean
}

export type ApiUser = {
  id: number
  name: string
  email: string
  profile: {
    accessibility_settings: ProfileSettings
    blind: boolean
    deaf: boolean
    mute: boolean
  }
}

export type AuthResponse = {
  access_token: string
  token_type: string
  expires_at?: string | null
  user: ApiUser
}

export function loginUser(email: string, password: string) {
  return apiFetch<AuthResponse>("/api/v1/auth/login", {
    method: "POST",
    body: JSON.stringify({ email, password }),
  })
}

export function registerUser(name: string, email: string, password: string, password_confirmation: string) {
  return apiFetch<AuthResponse>("/api/v1/auth/register", {
    method: "POST",
    body: JSON.stringify({
      name,
      email,
      password,
      password_confirmation,
      terms_accepted: true,
      privacy_accepted: true,
    }),
  })
}

export function fetchMe(token: string) {
  return apiFetch<ApiUser>("/api/v1/auth/me", {
    method: "GET",
  }, token)
}

export function fetchMessages(token: string) {
  return apiFetch<Array<{ id: number | string; content: string; type: string; created_at: string; user_id?: number }>>("/api/v1/messages", {
    method: "GET",
  }, token)
}

export type ApiPhrase = {
  id: number
  text: string
  icon?: string
  vibration_pattern: number[]
}

export function fetchDefaultPhrases() {
  return apiFetch<ApiPhrase[]>("/api/v1/phrases/defaults", {
    method: "GET",
  })
}

export function postMessage(content: string, token: string) {
  return apiFetch<{ id: number | string; content: string; type: string; created_at: string }>("/api/v1/messages", {
    method: "POST",
    body: JSON.stringify({ content }),
  }, token)
}

export function sendPhrase(phrase_id: number, token: string) {
  return apiFetch<{ id: number | string; content: string; type: string; created_at: string }>("/api/v1/messages/send-phrase", {
    method: "POST",
    body: JSON.stringify({ phrase_id }),
  }, token)
}

export function triggerEmergency(token: string, data: Record<string, unknown>) {
  return apiFetch<{ emergency: unknown; message: string }>("/api/v1/emergencies/trigger", {
    method: "POST",
    body: JSON.stringify(data),
  }, token)
}
