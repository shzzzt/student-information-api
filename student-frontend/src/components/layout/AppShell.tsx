import { useState } from 'react'
import { NavLink, Outlet, useNavigate } from 'react-router-dom'
import Button from '../ui/Button'
import { useAuth } from '../../contexts/AuthContext'
import type { Role } from '../../types'

type NavigationItem = { label: string; path: string; roles?: Role[] }

const navigationItems: NavigationItem[] = [
  { label: 'Dashboard', path: '/dashboard' },
  { label: 'Students', path: '/students', roles: ['admin', 'registrar'] },
  { label: 'Programs', path: '/programs', roles: ['admin', 'registrar'] },
  { label: 'Courses', path: '/courses', roles: ['admin', 'registrar'] },
  { label: 'Academic Terms', path: '/terms', roles: ['admin', 'registrar'] },
  { label: 'Course Offerings', path: '/offerings', roles: ['admin', 'registrar', 'faculty', 'instructor', 'student'] },
  { label: 'Enrollments', path: '/enrollments', roles: ['admin', 'registrar', 'faculty', 'instructor', 'student'] },
  { label: 'Grades', path: '/grades', roles: ['admin', 'registrar', 'faculty', 'instructor', 'student'] },
  { label: 'Profile', path: '/profile' },
]

export default function AppShell() {
  const { user, logout } = useAuth()
  const navigate = useNavigate()
  const [isSigningOut, setIsSigningOut] = useState(false)
  const [mobileNavOpen, setMobileNavOpen] = useState(false)
  const visibleItems = navigationItems.filter(({ roles }) => roles === undefined || (user !== null && roles.includes(user.role)))

  const handleSignOut = async (): Promise<void> => {
    setIsSigningOut(true)
    try { await logout() } finally { navigate('/login', { replace: true }); setIsSigningOut(false) }
  }

  const navigation = <nav className="flex-1 space-y-1 overflow-y-auto px-3 py-5" aria-label="Main navigation">
    {visibleItems.map((item) => <NavLink key={item.path} to={item.path} onClick={() => setMobileNavOpen(false)} className={({ isActive }) => `block rounded-md px-3 py-2 text-sm font-medium transition ${isActive ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'}`}>
      {item.label}
    </NavLink>)}
  </nav>

  return <div className="min-h-screen bg-slate-50">
    <aside className="fixed inset-y-0 left-0 z-30 hidden w-64 flex-col border-r border-slate-200 bg-white md:flex">
      <div className="border-b border-slate-200 px-6 py-5"><h1 className="text-lg font-semibold text-slate-900">Student Information</h1><p className="mt-1 text-xs text-slate-500">Management system</p></div>
      {navigation}
    </aside>

    {mobileNavOpen && <div className="fixed inset-0 z-40 md:hidden">
      <button type="button" aria-label="Close navigation" className="absolute inset-0 bg-slate-950/40" onClick={() => setMobileNavOpen(false)} />
      <aside className="relative flex h-full w-[min(18rem,85vw)] flex-col border-r border-slate-200 bg-white shadow-xl">
        <div className="flex items-start justify-between border-b border-slate-200 px-5 py-5"><div><h1 className="text-lg font-semibold text-slate-900">Student Information</h1><p className="mt-1 text-xs text-slate-500">Management system</p></div><button type="button" className="rounded-md px-2 py-1 text-slate-600 hover:bg-slate-100" aria-label="Close menu" onClick={() => setMobileNavOpen(false)}>×</button></div>
        {navigation}
      </aside>
    </div>}

    <div className="min-h-screen md:ml-64">
      <header className="sticky top-0 z-20 flex min-h-16 items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 sm:px-6 lg:px-8">
        <div className="flex min-w-0 items-center gap-3">
          <button type="button" className="inline-flex h-10 w-10 items-center justify-center rounded-md border border-slate-200 text-slate-700 hover:bg-slate-100 md:hidden" aria-label="Open navigation" aria-expanded={mobileNavOpen} onClick={() => setMobileNavOpen(true)}><span aria-hidden="true">☰</span></button>
          <p className="truncate text-sm text-slate-600">Signed in as <strong className="font-semibold text-slate-900">{user?.name}</strong><span className="ml-2 hidden rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium capitalize text-slate-700 sm:inline-flex">{user?.role}</span></p>
        </div>
        <Button variant="secondary" loading={isSigningOut} onClick={() => void handleSignOut()}>Sign out</Button>
      </header>
      <main className="p-4 sm:p-6 lg:p-8"><Outlet /></main>
    </div>
  </div>
}
