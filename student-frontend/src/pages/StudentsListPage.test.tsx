import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { render, screen } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { http, HttpResponse } from 'msw'
import { setupServer } from 'msw/node'
import { MemoryRouter } from 'react-router-dom'
import { afterAll, afterEach, beforeAll, beforeEach, describe, expect, it, vi } from 'vitest'
import authApi from '../api/auth'
import { AuthProvider } from '../contexts/AuthContext'
import StudentsListPage from './StudentsListPage'

vi.mock('../api/auth', () => ({
  default: {
    login: vi.fn(),
    logout: vi.fn(),
    me: vi.fn(),
  },
}))

const admin = {
  id: 1,
  first_name: 'Ada',
  last_name: 'Admin',
  name: 'Ada Admin',
  email: 'ada@example.com',
  role: 'admin' as const,
  status: 'active' as const,
}

const student = {
  id: 12,
  user_id: 22,
  student_number: '2026-0012',
  first_name: 'Jamie',
  last_name: 'Reyes',
  program_id: 3,
  year_level: 2,
  admission_date: '2025-08-01',
  status: 'active',
  program: { code: 'BSIT' },
}

const paginated = (data: unknown[]) => ({
  success: true,
  message: 'Students retrieved successfully.',
  data,
  meta: { current_page: 1, per_page: 20, total: data.length, last_page: 1 },
})

const server = setupServer()

const renderPage = () => {
  const queryClient = new QueryClient({
    defaultOptions: { queries: { retry: false }, mutations: { retry: false } },
  })

  return render(
    <QueryClientProvider client={queryClient}>
      <AuthProvider>
        <MemoryRouter>
          <StudentsListPage />
        </MemoryRouter>
      </AuthProvider>
    </QueryClientProvider>,
  )
}

beforeAll(() => server.listen({ onUnhandledRequest: 'error' }))
afterEach(() => server.resetHandlers())
afterAll(() => server.close())

describe('StudentsListPage', () => {
  beforeEach(() => {
    localStorage.setItem('auth_token', 'admin-token')
    vi.mocked(authApi.me).mockResolvedValue(admin)
  })

  it('renders rows from the students API', async () => {
    server.use(http.get('*/students', () => HttpResponse.json(paginated([student]))))
    renderPage()

    expect(await screen.findByText('2026-0012')).toBeInTheDocument()
    expect(screen.getByRole('link', { name: 'Jamie Reyes' })).toBeInTheDocument()
    expect(screen.getByText('BSIT')).toBeInTheDocument()
  })

  it('displays the empty state when the API data array is empty', async () => {
    server.use(http.get('*/students', () => HttpResponse.json(paginated([]))))
    renderPage()

    expect(await screen.findByRole('heading', { name: 'No students found' })).toBeInTheDocument()
  })

  it('opens the confirmation dialog when Deactivate is clicked', async () => {
    const userEvents = userEvent.setup()
    server.use(http.get('*/students', () => HttpResponse.json(paginated([student]))))
    renderPage()

    await userEvents.click(await screen.findByRole('button', { name: 'Deactivate' }))

    expect(screen.getByRole('dialog', { name: 'Deactivate student?' })).toBeInTheDocument()
    expect(screen.getByText('This will mark the student as inactive.')).toBeInTheDocument()
  })
})
