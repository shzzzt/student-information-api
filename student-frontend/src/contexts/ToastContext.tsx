import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from 'react'
import Toast from '../components/ui/Toast'

export type ToastType = 'success' | 'error' | 'info'

type ToastItem = {
  id: number
  message: string
  type: ToastType
}

type ToastContextValue = {
  showToast: (message: string, type: ToastType) => void
}

const ToastContext = createContext<ToastContextValue | undefined>(undefined)

type ToastProviderProps = {
  children: ReactNode
}

let nextToastId = 0

export function ToastProvider({ children }: ToastProviderProps) {
  const [toasts, setToasts] = useState<ToastItem[]>([])

  const dismissToast = useCallback((id: number): void => {
    setToasts((current) => current.filter((toast) => toast.id !== id))
  }, [])

  const showToast = useCallback((message: string, type: ToastType): void => {
    const id = nextToastId++
    setToasts((current) => [...current, { id, message, type }])
  }, [])

  useEffect(() => {
    const timers = toasts.map((toast) => window.setTimeout(() => dismissToast(toast.id), 4000))

    return () => timers.forEach((timer) => window.clearTimeout(timer))
  }, [dismissToast, toasts])

  const value = useMemo<ToastContextValue>(() => ({ showToast }), [showToast])

  return (
    <ToastContext.Provider value={value}>
      {children}
      <div className="fixed right-4 top-4 z-[60] flex w-auto max-w-[calc(100vw-2rem)] flex-col gap-3">
        {toasts.map((toast) => (
          <Toast
            key={toast.id}
            message={toast.message}
            type={toast.type}
            onDismiss={() => dismissToast(toast.id)}
          />
        ))}
      </div>
    </ToastContext.Provider>
  )
}

// This hook intentionally shares its context module with ToastProvider.
// eslint-disable-next-line react-refresh/only-export-components
export function useToast(): ToastContextValue {
  const context = useContext(ToastContext)

  if (context === undefined) {
    throw new Error('useToast must be used within a ToastProvider')
  }

  return context
}
