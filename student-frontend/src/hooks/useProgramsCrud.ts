import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import programsApi, { type ProgramInput, type ProgramListParams } from '../api/programs'
export const programCrudKeys = { all: ['programs-crud'] as const, list: (p: ProgramListParams) => [...programCrudKeys.all, p] as const, detail: (id: number) => [...programCrudKeys.all, id] as const }
export function useProgramsCrud(params: ProgramListParams = {}) { return useQuery({ queryKey: programCrudKeys.list(params), queryFn: () => programsApi.list(params) }) }
export function useProgram(id: number | null) { return useQuery({ queryKey: programCrudKeys.detail(id ?? 0), queryFn: () => programsApi.get(id as number), enabled: !!id }) }
export function useCreateProgram() { const q = useQueryClient(); return useMutation({ mutationFn: (d: ProgramInput) => programsApi.create(d), onSuccess: () => q.invalidateQueries({ queryKey: programCrudKeys.all }) }) }
export function useUpdateProgram(id: number) { const q = useQueryClient(); return useMutation({ mutationFn: (d: Partial<ProgramInput>) => programsApi.update(id, d), onSuccess: () => q.invalidateQueries({ queryKey: programCrudKeys.all }) }) }
export function useDeleteProgram() { const q = useQueryClient(); return useMutation({ mutationFn: (id: number) => programsApi.delete(id), onSuccess: () => q.invalidateQueries({ queryKey: programCrudKeys.all }) }) }
