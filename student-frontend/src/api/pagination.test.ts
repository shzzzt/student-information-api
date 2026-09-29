import { describe, expect, it } from 'vitest'
import { normalizePage } from './pagination'

describe('normalizePage', () => {
  it('normalizes the Laravel paginator nested in the API data property', () => {
    const page = normalizePage({
      current_page: 2,
      data: [{ id: 7 }, { id: 8 }],
      last_page: 4,
      per_page: 2,
      total: 8,
    })

    expect(page.data.map((item) => item.id)).toEqual([7, 8])
    expect(page.meta).toEqual({ current_page: 2, per_page: 2, total: 8, last_page: 4 })
  })

  it('creates consistent metadata when the API returns an unpaginated list', () => {
    expect(normalizePage([{ id: 1 }, { id: 2 }]).meta).toEqual({
      current_page: 1,
      per_page: 2,
      total: 2,
      last_page: 1,
    })
  })
})
