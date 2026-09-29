import axios from 'axios'

export function getApiErrorMessage(error: unknown, fallback = 'Something went wrong. Please try again.'): string {
  if (!axios.isAxiosError<{ message?: string }>(error)) return fallback
  if (!error.response) return 'The API could not be reached. Check your connection and try again.'

  const { status, data } = error.response
  if (status === 400) return data.message ?? 'The request could not be processed.'
  if (status === 403) return data.message ?? 'You do not have permission to perform this action.'
  if (status === 404) return data.message ?? 'The requested record was not found.'
  if (status === 409) return data.message ?? 'This action conflicts with an existing record.'
  if (status === 422) return data.message ?? 'Check the highlighted fields and try again.'
  if (status >= 500) return 'The server encountered an error. Please try again later.'
  return data.message ?? fallback
}

export function getValidationErrors(error: unknown): Record<string, string[]> {
  if (!axios.isAxiosError<{ errors?: Record<string, string[]> }>(error)) return {}
  return error.response?.data.errors ?? {}
}
