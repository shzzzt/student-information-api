import axios from 'axios'

export const AUTH_TOKEN_KEY = 'auth_token'

const client = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

client.interceptors.request.use((config) => {
  const token = localStorage.getItem(AUTH_TOKEN_KEY)

  if (token) {
    config.headers.set('Authorization', `Bearer ${token}`)
  }

  return config
})

client.interceptors.response.use(
  (response) => response,
  (error: unknown) => {
    if (axios.isAxiosError(error) && error.response?.status === 401) {
      localStorage.removeItem(AUTH_TOKEN_KEY)

      const requestUrl = error.config?.url?.split('?')[0]
      const isInitialUserRequest = requestUrl?.endsWith('/auth/me') === true

      if (!isInitialUserRequest && window.location.pathname !== '/login') {
        window.location.assign('/login')
      }
    }

    if (axios.isAxiosError(error) && error.response?.status === 403 && window.location.pathname !== '/forbidden') {
      window.location.assign('/forbidden')
    }

    return Promise.reject(error)
  },
)

export default client
