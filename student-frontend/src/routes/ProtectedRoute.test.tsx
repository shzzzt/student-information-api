import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { render, screen } from '@testing-library/react'
import { MemoryRouter, Route, Routes } from 'react-router-dom'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import authApi from '../api/auth'
import { AuthProvider } from '../contexts/AuthContext'
import type { Role, User } from '../types'
import ProtectedRoute from './ProtectedRoute'

vi.mock('../api/auth', () => ({
  default: {
    login: vi.fn(),
    logout: vi.fn(),
    me: vi.fn(),
  },
}))

const user: User = {
  id: 7,
  first_name: 'Rina',
  last_name: 'Registrar',
  name: 'Rina Registrar',
  email: 'rina@example.com',
  role: 'registrar',
  status: 'active',
}

const renderRoute = (roles?: Role[]) => {
  const queryClient = new QueryClient({
    defaultOptions: { queries: { retry: false }, mutations: { retry: false } },
  })

  return render(
    <QueryClientProvider client={queryClient}>
      <AuthProvider>
        <MemoryRouter initialEntries={['/protected']}>
          <Routes>
            <Route path="/login" element={<p>Login destination</p>} />
            <Route path="/forbidden" element={<p>Forbidden destination</p>} />
            <Route element={<ProtectedRoute roles={roles} />}>
              <Route path="/protected" element={<p>Protected content</p>} />
            </Route>
          </Routes>
        </MemoryRouter>
      </AuthProvider>
    </QueryClientProvider>,
  )
}

describe('ProtectedRoute', () => {
  beforeEach(() => {
    localStorage.clear()
    vi.mocked(authApi.me).mockReset()
  })

  it('redirects to login when the user is null', async () => {
    vi.mocked(authApi.me).mockRejectedValue(new Error('Unauthenticated'))
    renderRoute()

    expect(await screen.findByText('Login destination')).toBeInTheDocument()
  })

  it('renders the nested route when a user is present', async () => {
    localStorage.setItem('auth_token', 'valid-token')
    vi.mocked(authApi.me).mockResolvedValue(user)
    renderRoute()

    expect(await screen.findByText('Protected content')).toBeInTheDocument()
  })

  it('redirects to forbidden when the user role does not match', async () => {
    localStorage.setItem('auth_token', 'valid-token')
    vi.mocked(authApi.me).mockResolvedValue(user)
    renderRoute(['admin'])

    expect(await screen.findByText('Forbidden destination')).toBeInTheDocument()
  })
})
