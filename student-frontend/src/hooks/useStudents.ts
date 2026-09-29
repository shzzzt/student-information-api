import {
  useMutation,
  useQuery,
  useQueryClient,
} from '@tanstack/react-query'
import studentsApi, {
  type StudentListParams,
  type StudentUpdateData,
  type StudentWriteData,
} from '../api/students'

export const studentKeys = {
  all: ['students'] as const,
  lists: () => [...studentKeys.all, 'list'] as const,
  list: (params: StudentListParams = {}) =>
    [...studentKeys.lists(), params] as const,
  details: () => [...studentKeys.all, 'detail'] as const,
  detail: (id: number) => [...studentKeys.details(), id] as const,
}

export function useStudentsList(params: StudentListParams = {}, enabled = true) {
  return useQuery({
    queryKey: studentKeys.list(params),
    queryFn: () => studentsApi.list(params),
    placeholderData: (previousData) => previousData,
    enabled,
  })
}

export function useStudent(id: number | null | undefined) {
  return useQuery({
    queryKey: studentKeys.detail(id ?? 0),
    queryFn: () => {
      if (!id) {
        throw new Error('A student ID is required')
      }

      return studentsApi.get(id)
    },
    enabled: !!id,
  })
}

export function useCreateStudent() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: (data: StudentWriteData) => studentsApi.create(data),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: studentKeys.lists() })
    },
  })
}

export function useUpdateStudent(id: number) {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: (data: StudentUpdateData) => studentsApi.update(id, data),
    onSuccess: async () => {
      await Promise.all([
        queryClient.invalidateQueries({ queryKey: studentKeys.lists() }),
        queryClient.invalidateQueries({ queryKey: studentKeys.detail(id) }),
      ])
    },
  })
}

export function useDeleteStudent() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: (id: number) => studentsApi.delete(id),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: studentKeys.lists() })
    },
  })
}
