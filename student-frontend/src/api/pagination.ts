export type PageMeta = { current_page: number; per_page: number; total: number; last_page: number }
export type Page<T> = { data: T[]; meta: PageMeta }
type LaravelPaginator<T> = {
  data: T[]
  current_page?: number
  per_page?: number
  total?: number
  last_page?: number
  meta?: Partial<PageMeta>
}

export function normalizePage<T>(payload: T[] | LaravelPaginator<T>): Page<T> {
  if (Array.isArray(payload)) {
    return { data: payload, meta: { current_page: 1, per_page: payload.length, total: payload.length, last_page: 1 } }
  }
  const meta = payload.meta ?? payload
  return {
    data: payload.data,
    meta: {
      current_page: meta.current_page ?? 1,
      per_page: meta.per_page ?? payload.data.length,
      total: meta.total ?? payload.data.length,
      last_page: meta.last_page ?? 1,
    },
  }
}
