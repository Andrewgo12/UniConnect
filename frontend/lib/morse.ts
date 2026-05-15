/**
 * Codificación Morse por vibración para UniConnect
 *
 * Diseño háptico:
 *   Punto  (·) = pulso corto  100ms
 *   Raya   (−) = pulso largo  300ms
 *   Pausa entre símbolos      80ms
 *   Pausa entre letras        200ms
 *   Pausa entre palabras      400ms
 *
 * Solo se codifican letras A-Z y dígitos 0-9.
 * Caracteres no reconocidos se omiten silenciosamente.
 */

const DOT = 100
const DASH = 300
const SYM_GAP = 80   // pausa entre punto/raya dentro de una letra
const LET_GAP = 200  // pausa entre letras
const WRD_GAP = 400  // pausa entre palabras

const MORSE_MAP: Record<string, string> = {
  A: ".-",   B: "-...", C: "-.-.", D: "-..",  E: ".",
  F: "..-.", G: "--.",  H: "....", I: "..",   J: ".---",
  K: "-.-",  L: ".-..", M: "--",   N: "-.",   O: "---",
  P: ".--.", Q: "--.-", R: ".-.",  S: "...",  T: "-",
  U: "..-",  V: "...-", W: ".--",  X: "-..-", Y: "-.--",
  Z: "--..",
  "0": "-----", "1": ".----", "2": "..---", "3": "...--",
  "4": "....-", "5": ".....", "6": "-....", "7": "--...",
  "8": "---..", "9": "----.",
}

/**
 * Convierte texto a un patrón de vibración Morse.
 * @param text  Texto a codificar
 * @param maxWords  Máximo de palabras a codificar (evita patrones interminables)
 * @returns Array de duraciones para navigator.vibrate()
 */
export function textToMorse(text: string, maxWords = 3): number[] {
  const words = text
    .toUpperCase()
    .replace(/[^A-Z0-9 ]/g, "")
    .trim()
    .split(/\s+/)
    .slice(0, maxWords)

  const pattern: number[] = []

  words.forEach((word, wi) => {
    const letters = word.split("")

    letters.forEach((char, ci) => {
      const code = MORSE_MAP[char]
      if (!code) return

      const symbols = code.split("")
      symbols.forEach((sym, si) => {
        pattern.push(sym === "." ? DOT : DASH)
        // Pausa entre símbolos (no después del último)
        if (si < symbols.length - 1) pattern.push(SYM_GAP)
      })

      // Pausa entre letras (no después de la última)
      if (ci < letters.length - 1) pattern.push(LET_GAP)
    })

    // Pausa entre palabras (no después de la última)
    if (wi < words.length - 1) pattern.push(WRD_GAP)
  })

  return pattern
}

/**
 * Devuelve la representación Morse en texto (puntos y rayas) para mostrar en UI.
 */
export function textToMorseString(text: string, maxWords = 3): string {
  return text
    .toUpperCase()
    .replace(/[^A-Z0-9 ]/g, "")
    .trim()
    .split(/\s+/)
    .slice(0, maxWords)
    .map(word =>
      word.split("").map(c => MORSE_MAP[c] ?? "").filter(Boolean).join(" ")
    )
    .join("   ")
}
