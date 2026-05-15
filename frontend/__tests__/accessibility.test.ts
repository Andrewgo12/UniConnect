import { describe, it, expect } from 'vitest'
import { axe } from '@axe-core/react'

describe('Accessibility Tests', () => {
  it('should have axe-core/react installed', () => {
    expect(axe).toBeDefined()
  })

  // Nota: Los tests de componentes específicos se agregarán cuando se tenga
  // una configuración de renderizado más completa para componentes de Next.js
})
