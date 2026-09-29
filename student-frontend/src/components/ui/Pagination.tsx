import Button from './Button'

type PaginationProps = {
  currentPage: number
  lastPage: number
  onPageChange: (page: number) => void
}

export default function Pagination({ currentPage, lastPage, onPageChange }: PaginationProps) {
  const canGoPrevious = currentPage > 1
  const canGoNext = currentPage < lastPage

  return (
    <nav className="flex items-center justify-between gap-4" aria-label="Pagination">
      <Button
        variant="secondary"
        disabled={!canGoPrevious}
        onClick={() => onPageChange(currentPage - 1)}
      >
        Prev
      </Button>
      <span className="text-sm text-slate-600">Page {currentPage} of {lastPage}</span>
      <Button
        variant="secondary"
        disabled={!canGoNext}
        onClick={() => onPageChange(currentPage + 1)}
      >
        Next
      </Button>
    </nav>
  )
}
