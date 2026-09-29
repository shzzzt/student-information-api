import client from './client'
import type { ApiResponse, User } from '../types'
export const usersApi = { async list(role = 'faculty'): Promise<User[]> { return (await client.get<ApiResponse<User[]>>('/users', { params: { role } })).data.data } }
export default usersApi
