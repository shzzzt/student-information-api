import { useQuery } from '@tanstack/react-query'
import studentAcademicApi from '../api/studentAcademic'

export const studentAcademicKeys = {
  all: ['student-academic'] as const,
  enrollments: (studentId: number) => [...studentAcademicKeys.all, 'enrollments', studentId] as const,
  record: (studentId: number) => [...studentAcademicKeys.all, 'record', studentId] as const,
}

export function useStudentEnrollments(studentId: number | null | undefined) {
  return useQuery({
    queryKey: studentAcademicKeys.enrollments(studentId ?? 0),
    queryFn: () => {
      if (!studentId) {
        throw new Error('A student ID is required')
      }

      return studentAcademicApi.listEnrollments(studentId)
    },
    enabled: !!studentId,
  })
}

export function useStudentAcademicRecord(studentId: number | null | undefined) {
  return useQuery({
    queryKey: studentAcademicKeys.record(studentId ?? 0),
    queryFn: () => {
      if (!studentId) {
        throw new Error('A student ID is required')
      }

      return studentAcademicApi.getAcademicRecord(studentId)
    },
    enabled: !!studentId,
  })
}
