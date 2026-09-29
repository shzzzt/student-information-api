import client from './client'
import type { ApiResponse, Enrollment, EnrollmentStatus, GradeMode } from '../types'
import { normalizePage, type Page } from './pagination'

export type EnrollmentListParams = {
  student_id?: number
  course_offering_id?: number
  status?: EnrollmentStatus
  sort?: 'enrolled_on' | 'status'
  direction?: 'asc' | 'desc'
  page?: number
  per_page?: number
}

export type EnrollmentInput = {
  student_id: number
  course_offering_id: number
  enrolled_on: string
  dropped_on?: string | null
  status: EnrollmentStatus
  grade_mode: GradeMode
}

export type EnrollmentPage = Page<Enrollment>
type EnrollmentPayload = Enrollment[] | (EnrollmentPage & { current_page?: number; per_page?: number; last_page?: number; total?: number })

export const enrollmentsApi = {
  async list(params: EnrollmentListParams = {}): Promise<EnrollmentPage> {
    const response = await client.get<ApiResponse<EnrollmentPayload>>('/enrollments', { params })
    return normalizePage(response.data.data as EnrollmentPayload)
  },
  async create(data: EnrollmentInput): Promise<Enrollment> {
    return (await client.post<ApiResponse<Enrollment>>('/enrollments', data)).data.data
  },
  async update(id: number, data: Partial<EnrollmentInput>): Promise<Enrollment> {
    return (await client.patch<ApiResponse<Enrollment>>(`/enrollments/${id}`, data)).data.data
  },
  async remove(id: number): Promise<void> {
    await client.delete(`/enrollments/${id}`)
  },
}

export default enrollmentsApi
