import { Link } from 'react-router-dom'
import Button from '../components/ui/Button'

export default function ForbiddenPage() {
  return (
    <main className="flex min-h-screen items-center justify-center bg-slate-50 px-4">
      <section className="max-w-md text-center">
        <p className="text-sm font-semibold uppercase tracking-wide text-red-600">403</p>
        <h1 className="mt-2 text-3xl font-semibold text-slate-900">Forbidden</h1>
        <p className="mt-3 text-slate-600">You don't have permission to view this page.</p>
        <Link className="mt-6 inline-block" to="/dashboard">
          <Button>Back to dashboard</Button>
        </Link>
      </section>
    </main>
  )
}
