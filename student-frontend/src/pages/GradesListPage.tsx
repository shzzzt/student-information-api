import { zodResolver } from '@hookform/resolvers/zod'
import axios from 'axios'
import { useEffect, useState } from 'react'
import { useForm } from 'react-hook-form'
import Modal from '../components/ui/Modal'
import Button from '../components/ui/Button'
import Input from '../components/ui/Input'
import Select from '../components/ui/Select'
import Pagination from '../components/ui/Pagination'
import LoadingState from '../components/feedback/LoadingState'
import EmptyState from '../components/feedback/EmptyState'
import ErrorState from '../components/feedback/ErrorState'
import { useAuth } from '../contexts/AuthContext'
import { useGrades, useUpdateGrade } from '../hooks/useGrades'
import type { Grade } from '../types'
import { z } from 'zod'
import GradeCreateModal from '../components/grades/GradeCreateModal'

const editGradeSchema = z.object({
  letter_grade: z.string().nullable(),
  numeric_grade: z.coerce.number().min(0).max(100),
  grade_points: z.coerce.number().min(0).max(4),
  remarks: z.string().optional(),
  status: z.enum(['pending', 'submitted', 'approved', 'disputed', 'revised']),
})
type EditGradeValues = z.output<typeof editGradeSchema>

const statusOptions = ['pending', 'submitted', 'approved', 'disputed', 'revised'] as const
const letterOptions = ['', 'A', 'B+', 'B', 'C+', 'C', 'D', 'F']

function GradeEditModal({ grade, open, canEdit, onClose }: { grade: Grade | null; open: boolean; canEdit: boolean; onClose: () => void }) {
  const updateGrade = useUpdateGrade()
  const [serverMessage, setServerMessage] = useState<string | null>(null)
  const { register, handleSubmit, reset, formState: { errors } } = useForm<z.input<typeof editGradeSchema>, unknown, EditGradeValues>({ resolver: zodResolver(editGradeSchema) })

  useEffect(() => {
    if (grade) {
      reset({ letter_grade: grade.letter_grade ?? '', numeric_grade: Number(grade.numeric_grade ?? 0), grade_points: Number(grade.grade_points ?? 0), remarks: grade.remarks ?? '', status: grade.status })
    }
  }, [grade, reset])

  if (!grade || !canEdit) return null

  const submit = (data: EditGradeValues): void => {
    setServerMessage(null)
    updateGrade.mutate({ id: grade.id, data }, { onSuccess: onClose, onError: (error: unknown) => {
      if (axios.isAxiosError<{ message?: string }>(error) && error.response?.status === 403) setServerMessage('You cannot modify this grade.')
      else setServerMessage('Unable to update this grade.')
    } })
  }

  return <Modal open={open} title="Edit grade" onClose={onClose} footer={<><Button variant="secondary" onClick={onClose} disabled={updateGrade.isPending}>Cancel</Button><Button type="submit" form="grade-edit-form" loading={updateGrade.isPending}>Save changes</Button></>}>
    <form id="grade-edit-form" className="space-y-4" onSubmit={handleSubmit(submit)}>
      {serverMessage && <div className="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">{serverMessage}</div>}
      <Select label="Letter grade" error={errors.letter_grade?.message} {...register('letter_grade')}><option value="">No letter grade</option>{letterOptions.slice(1).map((letter) => <option key={letter} value={letter}>{letter}</option>)}</Select>
      <Input label="Numeric grade" type="number" min={0} max={100} step="0.01" required error={errors.numeric_grade?.message} {...register('numeric_grade')} />
      <Input label="Grade points" type="number" min={0} max={4} step="0.01" required error={errors.grade_points?.message} {...register('grade_points')} />
      <div className="space-y-1">
        <label className="label" htmlFor="grade-remarks">Remarks</label>
        <textarea id="grade-remarks" className="input min-h-24" {...register('remarks')} />
        {errors.remarks?.message && <p className="error-text">{errors.remarks.message}</p>}
      </div>
      <Select label="Status" required error={errors.status?.message} {...register('status')}>{statusOptions.map((status) => <option key={status} value={status}>{status}</option>)}</Select>
    </form>
  </Modal>
}

export default function GradesListPage() {
  const { user } = useAuth()
  const [studentId, setStudentId] = useState('')
  const [offeringId, setOfferingId] = useState('')
  const [status, setStatus] = useState('')
  const [page, setPage] = useState(1)
  const [selectedGrade, setSelectedGrade] = useState<Grade | null>(null)
  const [creating, setCreating] = useState(false)
  const query = useGrades({ student_id: studentId ? Number(studentId) : undefined, enrollment_id: offeringId ? Number(offeringId) : undefined, status: status || undefined, page, per_page: 20 })
  const grades = query.data?.data ?? []
  const canCreate = user?.role === 'admin' || user?.role === 'faculty' || user?.role === 'instructor'
  const canEdit = (grade: Grade): boolean => user?.role === 'admin' || ((user?.role === 'faculty' || user?.role === 'instructor') && grade.enrollment?.course_offering?.instructor_id === user.id)

  return <section className="space-y-6"><header className="flex flex-wrap items-start justify-between gap-3"><div><h1 className="text-2xl font-semibold text-slate-900">Grades</h1><p className="mt-1 text-sm text-slate-600">Review and manage student grades.</p></div>{canCreate && <Button onClick={() => setCreating(true)}>+ Record grade</Button>}</header><div className="grid gap-4 rounded-lg border border-slate-200 bg-white p-4 md:grid-cols-3"><Input label="Student ID" type="number" min={1} value={studentId} onChange={(e) => { setStudentId(e.target.value); setPage(1) }} /><Input label="Enrollment ID" type="number" min={1} value={offeringId} onChange={(e) => { setOfferingId(e.target.value); setPage(1) }} /><Select label="Status" value={status} onChange={(e) => { setStatus(e.target.value); setPage(1) }}><option value="">All statuses</option>{statusOptions.map((item) => <option key={item} value={item}>{item}</option>)}</Select></div>{query.isLoading ? <LoadingState /> : query.isError ? <ErrorState message="Unable to load grades." onRetry={() => void query.refetch()} /> : grades.length === 0 ? <EmptyState title="No grades found" description="Try adjusting your filters." /> : <><div className="overflow-x-auto rounded-lg border border-slate-200 bg-white"><table className="min-w-full divide-y divide-slate-200 text-left text-sm"><thead className="bg-slate-50 text-xs uppercase text-slate-500"><tr>{['Student #', 'Student name', 'Course', 'Section', 'Term', 'Letter', 'Numeric', 'Status', 'Actions'].map((heading) => <th className="px-4 py-3" key={heading}>{heading}</th>)}</tr></thead><tbody className="divide-y divide-slate-100">{grades.map((grade) => { const enrollment = grade.enrollment; const offering = enrollment?.course_offering; const course = offering?.course; const term = offering?.academic_term; return <tr key={grade.id}><td className="px-4 py-3">{enrollment?.student?.student_number ?? '—'}</td><td className="px-4 py-3">{enrollment?.student ? `${enrollment.student.first_name} ${enrollment.student.last_name}` : '—'}</td><td className="px-4 py-3 font-medium">{course?.course_code ?? '—'}</td><td className="px-4 py-3">{offering?.section ?? '—'}</td><td className="px-4 py-3">{term?.name ?? term?.code ?? '—'}</td><td className="px-4 py-3">{grade.letter_grade ?? '—'}</td><td className="px-4 py-3">{grade.numeric_grade ?? '—'}</td><td className="px-4 py-3 capitalize">{grade.status}</td><td className="px-4 py-3">{canEdit(grade) ? <Button variant="secondary" className="px-3 py-1.5" onClick={() => setSelectedGrade(grade)}>Edit</Button> : <span className="text-slate-400">—</span>}</td></tr> })}</tbody></table></div><Pagination currentPage={query.data?.meta.current_page ?? 1} lastPage={query.data?.meta.last_page ?? 1} onPageChange={setPage} /></>}<GradeEditModal key={selectedGrade?.id ?? 'none'} grade={selectedGrade} open={selectedGrade !== null} canEdit={selectedGrade !== null && canEdit(selectedGrade)} onClose={() => setSelectedGrade(null)} /><GradeCreateModal open={creating} onClose={() => setCreating(false)} /></section>
}
