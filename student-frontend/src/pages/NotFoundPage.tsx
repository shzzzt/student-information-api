import { Link } from 'react-router-dom'
import Button from '../components/ui/Button'

export default function NotFoundPage() {
  return (
    <main className="flex min-h-screen items-center justify-center bg-slate-50 px-4">
      <section className="max-w-md text-center">
        <p className="text-sm font-semibold uppercase tracking-wide text-slate-500">404</p>
        <h1 className="mt-2 text-3xl font-semibold text-slate-900">Page not found.</h1>
        <Link className="mt-6 inline-block" to="/dashboard">
          <Button>Back to dashboard</Button>
        </Link>
      </section>
    </main>
  )
}
