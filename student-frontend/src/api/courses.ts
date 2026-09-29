import client from './client'
import type { ApiResponse, Course } from '../types'
import { normalizePage, type Page } from './pagination'

export type CourseListParams = { search?: string; program_id?: number; status?: string; page?: number; per_page?: number }
export type CourseInput = Omit<Course, 'id' | 'program' | 'created_at' | 'updated_at'>
export type CoursePage = Page<Course>
type CoursePayload = Course[] | (CoursePage & { current_page?: number; per_page?: number; last_page?: number; total?: number })

export const coursesApi = {
  async list(params: CourseListParams = {}): Promise<CoursePage> { return normalizePage((await client.get<ApiResponse<CoursePayload>>('/courses', { params })).data.data) },
  async get(id: number): Promise<Course> { return (await client.get<ApiResponse<Course>>(`/courses/${id}`)).data.data },
  async create(data: CourseInput): Promise<Course> { return (await client.post<ApiResponse<Course>>('/courses', data)).data.data },
  async update(id: number, data: Partial<CourseInput>): Promise<Course> { return (await client.patch<ApiResponse<Course>>(`/courses/${id}`, data)).data.data },
  async delete(id: number): Promise<void> { await client.delete(`/courses/${id}`) },
}
export default coursesApi
