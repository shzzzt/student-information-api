import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import api, { type TermInput, type TermListParams } from '../api/academicTerms'
export const termKeys = { all: ['academic-terms'] as const, list: (p: TermListParams) => [...termKeys.all, p] as const }
export function useAcademicTerms(params: TermListParams = {}, enabled = true) { return useQuery({ queryKey: termKeys.list(params), queryFn: () => api.list(params), enabled }) }
export function useAcademicTerm(id: number | null) { return useQuery({ queryKey: [...termKeys.all, id], queryFn: () => api.get(id as number), enabled: !!id }) }
export function useCreateAcademicTerm() { const q = useQueryClient(); return useMutation({ mutationFn: (d: TermInput) => api.create(d), onSuccess: () => q.invalidateQueries({ queryKey: termKeys.all }) }) }
export function useUpdateAcademicTerm(id: number) { const q = useQueryClient(); return useMutation({ mutationFn: (d: Partial<TermInput>) => api.update(id, d), onSuccess: () => q.invalidateQueries({ queryKey: termKeys.all }) }) }
export function useDeleteAcademicTerm() { const q = useQueryClient(); return useMutation({ mutationFn: (id: number) => api.delete(id), onSuccess: () => q.invalidateQueries({ queryKey: termKeys.all }) }) }
