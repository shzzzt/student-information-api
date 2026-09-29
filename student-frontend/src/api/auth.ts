import client from './client'
import type { User } from '../types'

export type { User } from '../types'

export type LoginResponse = {
  access_token: string
  token_type: string
  expires_in: number
}

type ApiUser = Omit<User, 'name'> & { name?: string }

const normalizeUser = (user: ApiUser): User => {
  return {
    ...user,
    name: user.name ?? `${user.first_name} ${user.last_name}`.trim(),
  }
}

const login = async (email: string, password: string): Promise<LoginResponse> => {
  const response = await client.post<LoginResponse>('/auth/login', { email, password })

  return response.data
}

const logout = async (): Promise<void> => {
  await client.post('/auth/logout')
}

const me = async (): Promise<User> => {
  const response = await client.get<ApiUser>('/auth/me')

  return normalizeUser(response.data)
}

const authApi = {
  login,
  logout,
  me,
}

export default authApi
