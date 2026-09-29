import client from './client'
import type { ApiResponse, Grade } from '../types'
import { normalizePage, type Page } from './pagination'

export type GradeListParams = {
  student_id?: number
  enrollment_id?: number
  status?: string
  page?: number
  per_page?: number
}

export type GradeUpdateData = {
  letter_grade?: string | null
  numeric_grade?: number | null
  grade_points?: number | null
  remarks?: string | null
  status?: Grade['status']
}
export type GradeCreateData = GradeUpdateData & { enrollment_id: number; status: NonNullable<GradeUpdateData['status']> }

export type GradePage = Page<Grade>
type GradePayload = Grade[] | (GradePage & { current_page?: number; per_page?: number; last_page?: number; total?: number })

export const gradesApi = {
  async list(params: GradeListParams = {}): Promise<GradePage> {
    return normalizePage((await client.get<ApiResponse<GradePayload>>('/grades', { params })).data.data)
  },
  async update(id: number, data: GradeUpdateData): Promise<Grade> {
    return (await client.patch<ApiResponse<Grade>>(`/grades/${id}`, data)).data.data
  },
  async create(data: GradeCreateData): Promise<Grade> {
    return (await client.post<ApiResponse<Grade>>('/grades', data)).data.data
  },
}

export default gradesApi
