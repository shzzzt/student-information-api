import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import gradesApi, { type GradeCreateData, type GradeListParams, type GradeUpdateData } from '../api/grades'

export const gradeKeys = {
  all: ['grades'] as const,
  list: (params: GradeListParams) => [...gradeKeys.all, params] as const,
}

export function useGrades(params: GradeListParams = {}) {
  return useQuery({ queryKey: gradeKeys.list(params), queryFn: () => gradesApi.list(params) })
}

export function useUpdateGrade() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: ({ id, data }: { id: number; data: GradeUpdateData }) => gradesApi.update(id, data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: gradeKeys.all }),
  })
}

export function useCreateGrade() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: (data: GradeCreateData) => gradesApi.create(data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: gradeKeys.all }),
  })
}
