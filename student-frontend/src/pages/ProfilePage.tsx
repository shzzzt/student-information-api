import { useAuth } from '../contexts/AuthContext'

export default function ProfilePage() {
  const { user } = useAuth()

  return (
    <section>
      <h1 className="text-2xl font-semibold text-slate-900">Profile</h1>
      <p className="mt-2 text-slate-600">{user?.name}</p>
    </section>
  )
}
