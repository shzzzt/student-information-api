import { useEffect, useMemo, useState } from 'react'
import { Link } from 'react-router-dom'
import { useAuth } from '../contexts/AuthContext'
import {
  useDeleteStudent,
  useStudentsList,
} from '../hooks/useStudents'
import type { StudentListParams } from '../api/students'
import Button from '../components/ui/Button'
import ConfirmDialog from '../components/ui/ConfirmDialog'
import Input from '../components/ui/Input'
import Select from '../components/ui/Select'
import EmptyState from '../components/feedback/EmptyState'
import ErrorState from '../components/feedback/ErrorState'
import LoadingState from '../components/feedback/LoadingState'
import Pagination from '../components/ui/Pagination'

const studentStatuses = [
  { value: '', label: 'All statuses' },
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' },
  { value: 'on_leave', label: 'On leave' },
  { value: 'graduated', label: 'Graduated' },
  { value: 'withdrawn', label: 'Withdrawn' },
  { value: 'dismissed', label: 'Dismissed' },
]

const sortOptions = [
  { value: 'last_name', label: 'Last name (A-Z)' },
  { value: '-last_name', label: 'Last name (Z-A)' },
  { value: 'student_number', label: 'Student number (A-Z)' },
  { value: '-student_number', label: 'Student number (Z-A)' },
]

export default function StudentsListPage() {
  const { user } = useAuth()
  const canManage = user?.role === 'admin' || user?.role === 'registrar'
  const [search, setSearch] = useState('')
  const [debouncedSearch, setDebouncedSearch] = useState('')
  const [programId, setProgramId] = useState('')
  const [yearLevel, setYearLevel] = useState('')
  const [status, setStatus] = useState('')
  const [sort, setSort] = useState('last_name')
  const [page, setPage] = useState(1)
  const [studentToDelete, setStudentToDelete] = useState<number | null>(null)

  useEffect(() => {
    const timeoutId = window.setTimeout(() => {
      setDebouncedSearch(search.trim())
    }, 350)

    return () => window.clearTimeout(timeoutId)
  }, [search])

  const params = useMemo<StudentListParams>(() => ({
    search: debouncedSearch || undefined,
    program_id: programId ? Number(programId) : undefined,
    year_level: yearLevel ? Number(yearLevel) : undefined,
    status: status || undefined,
    sort,
    page,
    per_page: 20,
  }), [debouncedSearch, page, programId, sort, status, yearLevel])

  const studentsQuery = useStudentsList(params)
  const deleteStudent = useDeleteStudent()
  const students = studentsQuery.data?.data.data ?? []
  const meta = studentsQuery.data?.data.meta

  const handleConfirmDelete = (): void => {
    if (studentToDelete === null) {
      return
    }

    deleteStudent.mutate(studentToDelete, {
      onSuccess: () => setStudentToDelete(null),
    })
  }

  return (
    <section className="space-y-6">
      <header className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
          <h1 className="text-2xl font-semibold text-slate-900">Students</h1>
          <p className="mt-1 text-sm text-slate-600">Manage student records and enrollment profiles.</p>
        </div>
        {canManage && (
          <Link className="btn-primary" to="/students/new">
            + New Student
          </Link>
        )}
      </header>

      <div className="grid gap-4 rounded-lg border border-slate-200 bg-white p-4 md:grid-cols-2 lg:grid-cols-5">
        <Input
          label="Search"
          placeholder="Name or student number"
          value={search}
          onChange={(event) => { setSearch(event.target.value); setPage(1) }}
        />
        <Input
          label="Program ID"
          type="number"
          min="1"
          value={programId}
          onChange={(event) => { setProgramId(event.target.value); setPage(1) }}
        />
        <Input
          label="Year level"
          type="number"
          min="1"
          max="5"
          value={yearLevel}
          onChange={(event) => { setYearLevel(event.target.value); setPage(1) }}
        />
        <Select label="Status" value={status} onChange={(event) => { setStatus(event.target.value); setPage(1) }}>
          {studentStatuses.map((option) => (
            <option key={option.value} value={option.value}>{option.label}</option>
          ))}
        </Select>
        <Select label="Sort" value={sort} onChange={(event) => { setSort(event.target.value); setPage(1) }}>
          {sortOptions.map((option) => (
            <option key={option.value} value={option.value}>{option.label}</option>
          ))}
        </Select>
      </div>

      {studentsQuery.isLoading ? (
        <LoadingState />
      ) : studentsQuery.isError ? (
        <ErrorState message="Unable to load students." onRetry={() => void studentsQuery.refetch()} />
      ) : students.length === 0 ? (
        <EmptyState title="No students found" description="Try adjusting your search or filters." />
      ) : (
        <>
          <div className="overflow-x-auto rounded-lg border border-slate-200 bg-white">
            <table className="min-w-full divide-y divide-slate-200 text-left text-sm">
              <thead className="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                  <th className="px-4 py-3">Student #</th>
                  <th className="px-4 py-3">Name</th>
                  <th className="px-4 py-3">Program code</th>
                  <th className="px-4 py-3">Year</th>
                  <th className="px-4 py-3">Status</th>
                  <th className="px-4 py-3">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100 text-slate-700">
                {students.map((student) => (
                  <tr key={student.id}>
                    <td className="whitespace-nowrap px-4 py-3 font-medium">{student.student_number}</td>
                    <td className="whitespace-nowrap px-4 py-3">
                      <Link className="font-medium text-slate-900 hover:underline" to={`/students/${student.id}`}>
                        {student.first_name} {student.last_name}
                      </Link>
                    </td>
                    <td className="px-4 py-3">{student.program?.code ?? '—'}</td>
                    <td className="px-4 py-3">{student.year_level}</td>
                    <td className="px-4 py-3 capitalize">{student.status.replace('_', ' ')}</td>
                    <td className="px-4 py-3">
                      {canManage ? (
                        <div className="flex items-center gap-2">
                          <Link className="text-sm font-medium text-slate-700 hover:underline" to={`/students/${student.id}/edit`}>
                            Edit
                          </Link>
                          <Button variant="danger" className="px-3 py-1.5" onClick={() => setStudentToDelete(student.id)}>
                            Deactivate
                          </Button>
                        </div>
                      ) : (
                        <span className="text-slate-400">—</span>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {meta && (
            <Pagination
              currentPage={meta.current_page}
              lastPage={meta.last_page}
              onPageChange={setPage}
            />
          )}
        </>
      )}

      <ConfirmDialog
        open={studentToDelete !== null}
        title="Deactivate student?"
        message="This will mark the student as inactive."
        confirmLabel="Deactivate"
        onConfirm={handleConfirmDelete}
        onCancel={() => setStudentToDelete(null)}
        loading={deleteStudent.isPending}
      />
    </section>
  )
}
