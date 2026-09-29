import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { fireEvent, render, screen } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import type { ReactElement } from 'react'
import { http, HttpResponse } from 'msw'
import { setupServer } from 'msw/node'
import { MemoryRouter } from 'react-router-dom'
import { afterAll, afterEach, beforeAll, describe, expect, it, vi } from 'vitest'
import { AuthProvider } from '../../contexts/AuthContext'
import StudentForm from './StudentForm'

vi.mock('../../api/auth', () => ({
  default: {
    login: vi.fn(),
    logout: vi.fn(),
    me: vi.fn().mockRejectedValue(new Error('Unauthenticated')),
  },
}))

const programsResponse = {
  success: true,
  message: 'Programs retrieved successfully.',
  data: [
    {
      id: 3,
      code: 'BSIT',
      name: 'Information Technology',
      degree_level: 'bachelor',
      status: 'active',
    },
  ],
}

const server = setupServer(
  http.get('*/programs', () => HttpResponse.json(programsResponse)),
)

const renderForm = (ui: ReactElement) => {
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

beforeAll(() => server.listen({ onUnhandledRequest: 'error' }))
afterEach(() => server.resetHandlers())
afterAll(() => server.close())

describe('StudentForm', () => {
  it('shows validation errors after an empty submit', async () => {
    const onSubmit = vi.fn()
    renderForm(
      <StudentForm onSubmit={onSubmit} submitLabel="Save student" isSubmitting={false} />,
    )
    const submitButton = await screen.findByRole('button', { name: 'Save student' })

    fireEvent.submit(submitButton.closest('form')!)

    expect(await screen.findByLabelText(/student number/i)).toHaveAttribute('aria-invalid', 'true')
    expect(screen.getByLabelText(/first name/i)).toHaveAttribute('aria-invalid', 'true')
    expect(screen.getByLabelText(/last name/i)).toHaveAttribute('aria-invalid', 'true')
    expect(onSubmit).not.toHaveBeenCalled()
  })

  it('maps server 422 validation errors to form fields', async () => {
    const user = userEvent.setup()
    const onSubmit = vi.fn().mockRejectedValue({
      isAxiosError: true,
      response: {
        status: 422,
        data: { errors: { student_number: ['Student number is already in use.'] } },
      },
    })
    renderForm(
      <StudentForm onSubmit={onSubmit} submitLabel="Save student" isSubmitting={false} />,
    )

    await screen.findByRole('option', { name: /BSIT - Information Technology/ })
    await user.type(screen.getByLabelText(/student number/i), '2026-0012')
    await user.type(screen.getByLabelText(/first name/i), 'Jamie')
    await user.type(screen.getByLabelText(/last name/i), 'Reyes')
    await user.selectOptions(screen.getByLabelText(/program/i), '3')
    await user.type(screen.getByLabelText(/year level/i), '2')
    await user.selectOptions(screen.getByLabelText(/gender/i), 'prefer_not_to_say')
    fireEvent.change(screen.getByLabelText(/admission date/i), {
      target: { value: '2026-08-01' },
    })
    await user.click(screen.getByRole('button', { name: 'Save student' }))

    expect(await screen.findByText('Student number is already in use.')).toBeInTheDocument()
    expect(screen.getByLabelText(/student number/i)).toHaveAttribute('aria-invalid', 'true')
  })
})
