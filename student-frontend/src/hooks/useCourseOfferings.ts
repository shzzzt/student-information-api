import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import api, { type OfferingInput, type OfferingListParams } from '../api/courseOfferings'
export const offeringKeys = { all: ['course-offerings'] as const, list: (p: OfferingListParams) => [...offeringKeys.all, p] as const, detail: (id: number) => [...offeringKeys.all, id] as const }
export function useCourseOfferings(params: OfferingListParams = {}) { return useQuery({ queryKey: offeringKeys.list(params), queryFn: () => api.list(params) }) }
export function useCourseOffering(id: number | null) { return useQuery({ queryKey: offeringKeys.detail(id ?? 0), queryFn: () => api.get(id as number), enabled: !!id }) }
export function useCreateCourseOffering() { const q = useQueryClient(); return useMutation({ mutationFn: (d: OfferingInput) => api.create(d), onSuccess: () => q.invalidateQueries({ queryKey: offeringKeys.all }) }) }
export function useUpdateCourseOffering(id: number) { const q = useQueryClient(); return useMutation({ mutationFn: (d: Partial<OfferingInput>) => api.update(id, d), onSuccess: () => q.invalidateQueries({ queryKey: offeringKeys.all }) }) }
