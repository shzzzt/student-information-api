import client from './client'
import type { AcademicTerm, ApiResponse } from '../types'
import { normalizePage, type Page } from './pagination'

export type TermListParams = { academic_year?: number; term_type?: string; status?: string; page?: number; per_page?: number }
export type TermInput = Omit<AcademicTerm, 'id' | 'created_at' | 'updated_at'>
export type TermPage = Page<AcademicTerm>
type TermPayload = AcademicTerm[] | (TermPage & { current_page?: number; per_page?: number; last_page?: number; total?: number })

export const academicTermsApi = {
  async list(params: TermListParams = {}): Promise<TermPage> { return normalizePage((await client.get<ApiResponse<TermPayload>>('/academic-terms', { params })).data.data) },
  async get(id: number): Promise<AcademicTerm> { return (await client.get<ApiResponse<AcademicTerm>>(`/academic-terms/${id}`)).data.data },
  async create(data: TermInput): Promise<AcademicTerm> { return (await client.post<ApiResponse<AcademicTerm>>('/academic-terms', data)).data.data },
  async update(id: number, data: Partial<TermInput>): Promise<AcademicTerm> { return (await client.patch<ApiResponse<AcademicTerm>>(`/academic-terms/${id}`, data)).data.data },
  async delete(id: number): Promise<void> { await client.delete(`/academic-terms/${id}`) },
}
export default academicTermsApi
