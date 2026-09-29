import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { render, screen } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import type { ReactElement } from 'react'
import { MemoryRouter } from 'react-router-dom'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import authApi from '../api/auth'
import { AuthProvider } from '../contexts/AuthContext'
import LoginPage from './LoginPage'

vi.mock('../api/auth', () => ({
  default: {
    login: vi.fn(),
    logout: vi.fn(),
    me: vi.fn(),
  },
}))

const renderPage = (ui: ReactElement) => {
  const queryClient = new QueryClient({
    defaultOptions: { queries: { retry: false }, mutations: { retry: false } },
  })

  return render(
    <QueryClientProvider client={queryClient}>
      <AuthProvider>
        <MemoryRouter>{ui}</MemoryRouter>
      </AuthProvider>
    </QueryClientProvider>,
  )
}

describe('LoginPage', () => {
  beforeEach(() => {
    localStorage.clear()
    vi.mocked(authApi.me).mockReset()
    vi.mocked(authApi.login).mockReset()
  })

  it('renders email and password fields', () => {
    renderPage(<LoginPage />)

    expect(screen.getByRole('textbox', { name: /email/i })).toBeInTheDocument()
    expect(screen.getByLabelText(/password/i)).toHaveAttribute('type', 'password')
  })

  it('shows the email validation message when login fails', async () => {
    const user = userEvent.setup()
    vi.mocked(authApi.login).mockRejectedValue({
      isAxiosError: true,
      response: {
        status: 422,
        data: { errors: { email: ['Invalid'] } },
      },
    })
    renderPage(<LoginPage />)

    await user.type(screen.getByRole('textbox', { name: /email/i }), 'bad@example.com')
    await user.type(screen.getByLabelText(/password/i), 'incorrect')
    await user.click(screen.getByRole('button', { name: /sign in/i }))

    expect(await screen.findByRole('alert')).toHaveTextContent('Invalid')
  })

  it('calls login with the submitted credentials', async () => {
    const user = userEvent.setup()
    vi.mocked(authApi.login).mockResolvedValue({
      access_token: 'valid-token',
      token_type: 'bearer',
      expires_in: 3600,
    })
    vi.mocked(authApi.me).mockResolvedValue({
      id: 1,
      first_name: 'Ada',
      last_name: 'Admin',
      name: 'Ada Admin',
      email: 'ada@example.com',
      role: 'admin',
      status: 'active',
    })
    renderPage(<LoginPage />)

    await user.type(screen.getByRole('textbox', { name: /email/i }), 'ada@example.com')
    await user.type(screen.getByLabelText(/password/i), 'secret123')
    await user.click(screen.getByRole('button', { name: /sign in/i }))

    expect(authApi.login).toHaveBeenCalledWith('ada@example.com', 'secret123')
    expect(localStorage.getItem('auth_token')).toBe('valid-token')
    expect(authApi.me).toHaveBeenCalledOnce()
  })
})
