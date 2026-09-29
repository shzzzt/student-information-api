import client from './client'
import type { AcademicTerm, ApiResponse, Enrollment, Student } from '../types'

export type AcademicRecordTerm = {
  academic_term: AcademicTerm
  enrollments: Enrollment[]
}

export type AcademicRecord = {
  student: Pick<
    Student,
    | 'id'
    | 'student_number'
    | 'first_name'
    | 'middle_name'
    | 'last_name'
    | 'program_id'
    | 'year_level'
    | 'status'
  >
  terms: AcademicRecordTerm[]
}

const listEnrollments = async (studentId: number): Promise<Enrollment[]> => {
  const response = await client.get<ApiResponse<Enrollment[]>>(
    `/students/${studentId}/enrollments`,
  )

  return response.data.data
}

const getAcademicRecord = async (studentId: number): Promise<AcademicRecord> => {
  const response = await client.get<ApiResponse<AcademicRecord>>(
    `/students/${studentId}/academic-record`,
  )

  return response.data.data
}

export const studentAcademicApi = {
  listEnrollments,
  getAcademicRecord,
}

export default studentAcademicApi
