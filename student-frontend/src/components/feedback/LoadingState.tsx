type LoadingStateProps = {
  label?: string
}

export default function LoadingState({ label = 'Loading...' }: LoadingStateProps) {
  return (
    <div className="flex items-center justify-center gap-3 py-10" role="status" aria-live="polite">
      <span
        className="h-5 w-5 animate-spin rounded-full border-2 border-slate-300 border-t-slate-700"
        aria-hidden="true"
      />
      <span className="text-sm text-slate-600">{label}</span>
    </div>
  )
}
