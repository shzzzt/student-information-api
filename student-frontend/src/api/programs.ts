import client from './client'
import type { ApiResponse, Program } from '../types'
import { normalizePage, type Page } from './pagination'

export type ProgramListParams = {
  search?: string
  degree_level?: string
  status?: string
  per_page?: number
  page?: number
}
export type ProgramInput = Omit<Program, 'id' | 'created_at' | 'updated_at'>
export type ProgramPage = Page<Program>
type ProgramPayload = Program[] | (ProgramPage & { current_page?: number; per_page?: number; last_page?: number; total?: number })

const list = async (params: ProgramListParams = {}): Promise<ProgramPage> => normalizePage((await client.get<ApiResponse<ProgramPayload>>('/programs', { params })).data.data)

export const programsApi = {
  list,
  async get(id: number): Promise<Program> { return (await client.get<ApiResponse<Program>>(`/programs/${id}`)).data.data },
  async create(data: ProgramInput): Promise<Program> { return (await client.post<ApiResponse<Program>>('/programs', data)).data.data },
  async update(id: number, data: Partial<ProgramInput>): Promise<Program> { return (await client.patch<ApiResponse<Program>>(`/programs/${id}`, data)).data.data },
  async delete(id: number): Promise<void> { await client.delete(`/programs/${id}`) },
}

export default programsApi
