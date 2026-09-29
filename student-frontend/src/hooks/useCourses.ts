import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import coursesApi, { type CourseInput, type CourseListParams } from '../api/courses'
export const courseKeys = { all: ['courses'] as const, list: (p: CourseListParams) => [...courseKeys.all, p] as const }
export function useCourses(params: CourseListParams = {}, enabled = true) { return useQuery({ queryKey: courseKeys.list(params), queryFn: () => coursesApi.list(params), enabled }) }
export function useCourse(id: number | null) { return useQuery({ queryKey: [...courseKeys.all, id], queryFn: () => coursesApi.get(id as number), enabled: !!id }) }
export function useCreateCourse() { const q = useQueryClient(); return useMutation({ mutationFn: (d: CourseInput) => coursesApi.create(d), onSuccess: () => q.invalidateQueries({ queryKey: courseKeys.all }) }) }
export function useUpdateCourse(id: number) { const q = useQueryClient(); return useMutation({ mutationFn: (d: Partial<CourseInput>) => coursesApi.update(id, d), onSuccess: () => q.invalidateQueries({ queryKey: courseKeys.all }) }) }
export function useDeleteCourse() { const q = useQueryClient(); return useMutation({ mutationFn: (id: number) => coursesApi.delete(id), onSuccess: () => q.invalidateQueries({ queryKey: courseKeys.all }) }) }
