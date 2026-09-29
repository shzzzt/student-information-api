import { Navigate, Outlet, useLocation } from 'react-router-dom'
import { useAuth } from '../contexts/AuthContext'
import type { Role } from '../types'

export type { Role } from '../types'

type ProtectedRouteProps = {
  roles?: Role[]
}

export default function ProtectedRoute({ roles }: ProtectedRouteProps) {
  const { user, isLoading } = useAuth()
  const location = useLocation()

  if (isLoading) {
    return (
      <div className="flex min-h-screen items-center justify-center">
        <p>Loading...</p>
      </div>
    )
  }

  if (user === null) {
    return <Navigate to="/login" state={{ from: location }} replace />
  }

  if (roles !== undefined && !roles.includes(user.role)) {
    return <Navigate to="/forbidden" replace />
  }

  return <Outlet />
}
