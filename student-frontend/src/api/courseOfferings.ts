import client from './client'
import type { ApiResponse, CourseOffering } from '../types'
import { normalizePage, type Page } from './pagination'

export type OfferingListParams = { course_id?: number; academic_term_id?: number; instructor_id?: number; status?: string; page?: number; per_page?: number }
export type OfferingInput = Omit<CourseOffering, 'id' | 'course' | 'academic_term' | 'instructor' | 'enrolled_count' | 'created_at' | 'updated_at'>
export type OfferingPage = Page<CourseOffering>
type Payload = CourseOffering[] | (OfferingPage & { current_page?: number; per_page?: number; last_page?: number; total?: number })

export const courseOfferingsApi = {
  async list(params: OfferingListParams = {}): Promise<OfferingPage> { return normalizePage((await client.get<ApiResponse<Payload>>('/course-offerings', { params })).data.data) },
  async get(id: number): Promise<CourseOffering> { return (await client.get<ApiResponse<CourseOffering>>(`/course-offerings/${id}`)).data.data },
  async create(data: OfferingInput): Promise<CourseOffering> { return (await client.post<ApiResponse<CourseOffering>>('/course-offerings', data)).data.data },
  async update(id: number, data: Partial<OfferingInput>): Promise<CourseOffering> { return (await client.patch<ApiResponse<CourseOffering>>(`/course-offerings/${id}`, data)).data.data },
}
export default courseOfferingsApi
