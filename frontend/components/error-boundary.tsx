"use client"

import { Component, type ReactNode } from "react"

interface Props {
  children: ReactNode
}

interface State {
  hasError: boolean
  error: Error | null
}

export class ErrorBoundary extends Component<Props, State> {
  constructor(props: Props) {
    super(props)
    this.state = { hasError: false, error: null }
  }

  static getDerivedStateFromError(error: Error): State {
    return { hasError: true, error }
  }

  componentDidCatch(error: Error) {
    console.error("[UniConnect] Error no capturado:", error)
  }

  render() {
    if (this.state.hasError) {
      return (
        <main
          className="h-dvh flex flex-col items-center justify-center bg-background text-foreground p-6 gap-4"
          role="alert"
          aria-live="assertive"
        >
          <h1 className="text-xl font-bold">Algo salió mal</h1>
          <p className="text-muted-foreground text-sm text-center">
            La aplicación encontró un error inesperado.
          </p>
          <button
            className="px-4 py-2 bg-primary text-primary-foreground rounded-lg text-sm"
            onClick={() => {
              this.setState({ hasError: false, error: null })
              window.location.reload()
            }}
          >
            Reintentar
          </button>
        </main>
      )
    }

    return this.props.children
  }
}
