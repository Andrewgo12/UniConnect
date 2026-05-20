import { describe, it } from 'vitest'

describe('Test Setup', () => {
  it('should have localStorage mock', () => {
    localStorage.setItem('test', 'value')
    expect(localStorage.getItem('test')).toBe('value')
    localStorage.removeItem('test')
    expect(localStorage.getItem('test')).toBeNull()
  })

  it('should have navigator.vibrate mock', () => {
    expect(navigator.vibrate).toBeDefined()
    navigator.vibrate([100])
    expect(navigator.vibrate).toHaveBeenCalled()
  })

  it('should have speechSynthesis mock', () => {
    expect(window.speechSynthesis).toBeDefined()
    expect(window.speechSynthesis.speak).toBeDefined()
    expect(window.speechSynthesis.cancel).toBeDefined()
  })

  it('should have SpeechRecognition mock', () => {
    expect(window.SpeechRecognition).toBeDefined()
    expect(window.webkitSpeechRecognition).toBeDefined()
  })

  it('should have matchMedia mock', () => {
    const mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)')
    expect(mediaQuery).toBeDefined()
    expect(mediaQuery.matches).toBe(false)
  })
})
