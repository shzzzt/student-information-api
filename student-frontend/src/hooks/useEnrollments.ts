import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import api, { type EnrollmentInput, type EnrollmentListParams } from '../api/enrollments'

export const enrollmentKeys = {
  all: ['enrollments'] as const,
  list: (params: EnrollmentListParams) => [...enrollmentKeys.all, params] as const,
}

export function useEnrollments(params: EnrollmentListParams = {}, enabled = true) {
  return useQuery({
    queryKey: enrollmentKeys.list(params),
    queryFn: () => api.list(params),
    enabled,
  })
}

export function useCreateEnrollment() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: (data: EnrollmentInput) => api.create(data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: enrollmentKeys.all }),
  })
}

export function useUpdateEnrollment() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: ({ id, data }: { id: number; data: Partial<EnrollmentInput> }) => api.update(id, data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: enrollmentKeys.all }),
  })
}

export function useDeleteEnrollment() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: (id: number) => api.remove(id),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: enrollmentKeys.all }),
  })
}
