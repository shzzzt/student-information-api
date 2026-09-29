import type { AxiosResponse } from 'axios'
import client from './client'
import type { ApiResponse, PaginatedResponse, Student } from '../types'

export type StudentListParams = {
  search?: string
  program_id?: number
  year_level?: number
  status?: string
  sort?: string
  page?: number
  per_page?: number
}

export type StudentWriteData = Omit<
  Student,
  'id' | 'program' | 'user' | 'created_at' | 'updated_at'
>

export type StudentUpdateData = Partial<StudentWriteData>

const list = (
  params: StudentListParams = {},
): Promise<AxiosResponse<PaginatedResponse<Student>>> =>
  client.get<PaginatedResponse<Student>>('/students', { params })

const get = async (id: number): Promise<Student> => {
  const response = await client.get<ApiResponse<Student>>(`/students/${id}`)

  return response.data.data
}

const create = async (data: StudentWriteData): Promise<Student> => {
  const response = await client.post<ApiResponse<Student>>('/students', data)

  return response.data.data
}

const update = async (id: number, data: StudentUpdateData): Promise<Student> => {
  const response = await client.put<ApiResponse<Student>>(`/students/${id}`, data)

  return response.data.data
}

const remove = async (id: number): Promise<void> => {
  await client.delete(`/students/${id}`)
}

export const studentsApi = {
  list,
  get,
  create,
  update,
  delete: remove,
}

export default studentsApi
