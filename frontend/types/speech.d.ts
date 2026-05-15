/**
 * Declaraciones de tipos globales para APIs de Web Speech.
 * window.webkitSpeechRecognition no está en lib.dom.d.ts estándar —
 * sin esta declaración, TypeScript lanza error con ignoreBuildErrors: false.
 *
 * SpeechRecognition y SpeechRecognitionErrorEvent se declaran como globales
 * para que useRef<SpeechRecognition> funcione sin importación explícita.
 */

declare class SpeechRecognition extends EventTarget {
  continuous: boolean
  interimResults: boolean
  lang: string
  maxAlternatives: number
  onresult: ((event: SpeechRecognitionEvent) => void) | null
  onerror: ((event: SpeechRecognitionErrorEvent) => void) | null
  onend: (() => void) | null
  onstart: (() => void) | null
  start(): void
  stop(): void
  abort(): void
}

declare class SpeechRecognitionEvent extends Event {
  readonly resultIndex: number
  readonly results: SpeechRecognitionResultList
}

declare class SpeechRecognitionErrorEvent extends Event {
  readonly error: string
  readonly message: string
}

interface Window {
  webkitSpeechRecognition: typeof SpeechRecognition
  SpeechRecognition: typeof SpeechRecognition
}

/**
 * Screen Wake Lock API — evita que la pantalla se apague durante uso activo.
 * No está en lib.dom.d.ts en todas las versiones de TypeScript.
 */
interface WakeLockSentinel extends EventTarget {
  readonly released: boolean
  readonly type: 'screen'
  release(): Promise<void>
  addEventListener(type: 'release', listener: EventListenerOrEventListenerObject): void
}

interface Navigator {
  wakeLock: {
    request(type: 'screen'): Promise<WakeLockSentinel>
  }
}
