import type { ReactNode } from 'react'

type EmptyStateProps = {
  title: string
  description: string
  action?: ReactNode
}

export default function EmptyState({ title, description, action }: EmptyStateProps) {
  return (
    <div className="flex flex-col items-center justify-center gap-2 py-12 text-center">
      <h2 className="text-lg font-semibold text-slate-900">{title}</h2>
      <p className="max-w-md text-sm text-slate-600">{description}</p>
      {action && <div className="mt-3">{action}</div>}
    </div>
  )
}
