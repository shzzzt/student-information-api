import type { ToastType } from '../../contexts/ToastContext'

type ToastProps = {
  message: string
  type: ToastType
  onDismiss: () => void
}

const styles: Record<ToastType, string> = {
  success: 'border-green-200 bg-green-50 text-green-800',
  error: 'border-red-200 bg-red-50 text-red-800',
  info: 'border-blue-200 bg-blue-50 text-blue-800',
}

export default function Toast({ message, type, onDismiss }: ToastProps) {
  return (
    <div
      className={`flex min-w-72 max-w-sm items-start justify-between gap-4 rounded-md border px-4 py-3 text-sm shadow-lg ${styles[type]}`}
      role={type === 'error' ? 'alert' : 'status'}
    >
      <span>{message}</span>
      <button
        type="button"
        className="shrink-0 rounded p-0.5 text-current/70 hover:bg-black/5 hover:text-current focus:outline-none focus:ring-2 focus:ring-current"
        aria-label="Dismiss notification"
        onClick={onDismiss}
      >
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  )
}
