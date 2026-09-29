import { useQuery } from '@tanstack/react-query'
import programsApi from '../api/programs'

export const programKeys = {
  all: ['programs'] as const,
  lists: () => [...programKeys.all, 'list'] as const,
}

export function usePrograms() {
  return useQuery({
    queryKey: programKeys.lists(),
    queryFn: async () => {
      const response = await programsApi.list({ per_page: 100 })

      return response.data
    },
  })
}
