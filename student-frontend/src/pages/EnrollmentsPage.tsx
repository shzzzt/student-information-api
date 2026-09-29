import { useState } from 'react'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import Button from '../components/ui/Button'
import ConfirmDialog from '../components/ui/ConfirmDialog'
import EmptyState from '../components/feedback/EmptyState'
import ErrorState from '../components/feedback/ErrorState'
import LoadingState from '../components/feedback/LoadingState'
import Input from '../components/ui/Input'
import Modal from '../components/ui/Modal'
import Pagination from '../components/ui/Pagination'
import Select from '../components/ui/Select'
import { useToast } from '../contexts/ToastContext'
import { getApiErrorMessage, getValidationErrors } from '../api/errors'
import { useAuth } from '../contexts/AuthContext'
import { useCourseOfferings } from '../hooks/useCourseOfferings'
import { useStudentsList } from '../hooks/useStudents'
import { useCreateEnrollment, useDeleteEnrollment, useEnrollments, useUpdateEnrollment } from '../hooks/useEnrollments'
import type { Enrollment, EnrollmentStatus, GradeMode } from '../types'

const statuses: EnrollmentStatus[] = ['enrolled', 'waitlisted', 'dropped', 'withdrawn', 'completed']
const gradeModes: GradeMode[] = ['graded', 'pass_fail', 'audit']

const enrollmentSchema = z.object({
  student_id: z.coerce.number().int().positive('Choose a student.'),
  course_offering_id: z.coerce.number().int().positive('Choose a course offering.'),
  enrolled_on: z.string().min(1, 'Enrollment date is required.'),
  status: z.enum(statuses),
  grade_mode: z.enum(gradeModes),
})
type EnrollmentValues = z.infer<typeof enrollmentSchema>

function EnrollmentForm({ open, onClose }: { open: boolean; onClose: () => void }) {
  const create = useCreateEnrollment()
  const { showToast } = useToast()
  const students = useStudentsList({ per_page: 100, sort: 'last_name' })
  const offerings = useCourseOfferings({ per_page: 100 })
  const [formError, setFormError] = useState<string | null>(null)
  const { register, handleSubmit, reset, setError, formState: { errors } } = useForm<z.input<typeof enrollmentSchema>, unknown, EnrollmentValues>({
    resolver: zodResolver(enrollmentSchema),
    defaultValues: { enrolled_on: new Date().toLocaleDateString('en-CA'), status: 'enrolled', grade_mode: 'graded' },
  })

  const close = (): void => {
    reset({ enrolled_on: new Date().toLocaleDateString('en-CA'), status: 'enrolled', grade_mode: 'graded' })
    setFormError(null)
    onClose()
  }

  const submit = (values: EnrollmentValues): void => {
    setFormError(null)
    create.mutate(values, {
      onSuccess: () => { showToast('Enrollment created.', 'success'); close() },
      onError: (error: unknown) => {
        const validation = getValidationErrors(error)
        Object.entries(validation).forEach(([field, messages]) => {
          if (field in values) setError(field as keyof EnrollmentValues, { message: messages[0] })
        })
        setFormError(getApiErrorMessage(error, 'Could not create the enrollment.'))
      },
    })
  }

  return <Modal open={open} title="Create enrollment" onClose={close} footer={<><Button variant="secondary" onClick={close} disabled={create.isPending}>Cancel</Button><Button type="submit" form="enrollment-create-form" loading={create.isPending}>Create enrollment</Button></>}>
    {formError && <p className="mb-4 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700" role="alert">{formError}</p>}
    {students.isError || offerings.isError ? <ErrorState message="Could not load student or offering choices." onRetry={() => { void students.refetch(); void offerings.refetch() }} /> : <form id="enrollment-create-form" className="space-y-4" onSubmit={handleSubmit(submit)}>
      <Select label="Student" required error={errors.student_id?.message} {...register('student_id')}>
        <option value="">Select a student</option>
        {students.data?.data.data.map((student) => <option key={student.id} value={student.id}>{student.student_number} · {student.first_name} {student.last_name}</option>)}
      </Select>
      <Select label="Course offering" required error={errors.course_offering_id?.message} {...register('course_offering_id')}>
        <option value="">Select an offering</option>
        {offerings.data?.data.map((offering) => <option key={offering.id} value={offering.id}>{offering.course?.course_code ?? `Offering #${offering.id}`} · {offering.section} · {offering.academic_term?.name ?? offering.academic_term?.code ?? 'Term'}</option>)}
      </Select>
      <Input label="Enrollment date" type="date" required error={errors.enrolled_on?.message} {...register('enrolled_on')} />
      <Select label="Status" required error={errors.status?.message} {...register('status')}>{statuses.map((status) => <option key={status} value={status}>{status}</option>)}</Select>
      <Select label="Grade mode" required error={errors.grade_mode?.message} {...register('grade_mode')}>{gradeModes.map((mode) => <option key={mode} value={mode}>{mode.replace('_', ' ')}</option>)}</Select>
      {students.data?.data.data.length === 0 && <p className="text-sm text-amber-700">Create a student record before enrolling.</p>}
      {offerings.data?.data.length === 0 && <p className="text-sm text-amber-700">Create a course offering before enrolling.</p>}
    </form>}
  </Modal>
}

export default function EnrollmentsPage() {
  const { user } = useAuth()
  const { showToast } = useToast()
  const canManage = user?.role === 'admin' || user?.role === 'registrar'
  const [studentId, setStudentId] = useState('')
  const [offeringId, setOfferingId] = useState('')
  const [status, setStatus] = useState('')
  const [page, setPage] = useState(1)
  const [creating, setCreating] = useState(false)
  const [deleting, setDeleting] = useState<Enrollment | null>(null)
  const [feedback, setFeedback] = useState<string | null>(null)
  const query = useEnrollments({ student_id: studentId ? Number(studentId) : undefined, course_offering_id: offeringId ? Number(offeringId) : undefined, status: (status || undefined) as EnrollmentStatus | undefined, page, per_page: 20 })
  const students = useStudentsList({ per_page: 100 }, canManage)
  const offerings = useCourseOfferings({ per_page: 100 })
  const update = useUpdateEnrollment()
  const remove = useDeleteEnrollment()
  const rows = query.data?.data ?? []

  const setFilter = (setter: (value: string) => void, value: string): void => { setter(value); setPage(1) }

  const updateStatus = (enrollment: Enrollment, nextStatus: EnrollmentStatus): void => {
    setFeedback(null)
    update.mutate({ id: enrollment.id, data: { status: nextStatus } }, {
      onSuccess: () => showToast('Enrollment status updated.', 'success'),
      onError: (error: unknown) => setFeedback(getApiErrorMessage(error, 'Could not update the enrollment.')),
    })
  }

  const deleteEnrollment = (): void => {
    if (!deleting) return
    remove.mutate(deleting.id, {
      onSuccess: () => { showToast('Enrollment deleted.', 'success'); setDeleting(null) },
      onError: (error: unknown) => { setFeedback(getApiErrorMessage(error, 'Could not delete the enrollment.')); setDeleting(null) },
    })
  }

  return <section className="space-y-6">
    <header className="flex flex-wrap items-center justify-between gap-3">
      <div><h1 className="text-2xl font-semibold text-slate-900">Enrollments</h1><p className="mt-1 text-sm text-slate-600">View course registrations and update enrollment status.</p></div>
      {canManage && <Button onClick={() => setCreating(true)}>+ New enrollment</Button>}
    </header>
    {feedback && <div className="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700" role="alert">{feedback}</div>}
    <div className="grid gap-4 rounded-lg border border-slate-200 bg-white p-4 md:grid-cols-3">
      {canManage && <Select label="Student" value={studentId} onChange={(event) => setFilter(setStudentId, event.target.value)}><option value="">All students</option>{students.data?.data.data.map((student) => <option key={student.id} value={student.id}>{student.student_number} · {student.last_name}, {student.first_name}</option>)}</Select>}
      <Select label="Course offering" value={offeringId} onChange={(event) => setFilter(setOfferingId, event.target.value)}><option value="">All offerings</option>{offerings.data?.data.map((offering) => <option key={offering.id} value={offering.id}>{offering.course?.course_code ?? `Offering #${offering.id}`} · {offering.section}</option>)}</Select>
      <Select label="Status" value={status} onChange={(event) => setFilter(setStatus, event.target.value)}><option value="">All statuses</option>{statuses.map((item) => <option key={item} value={item}>{item}</option>)}</Select>
    </div>
    {query.isLoading ? <LoadingState /> : query.isError ? <ErrorState message={getApiErrorMessage(query.error, 'Unable to load enrollments.')} onRetry={() => void query.refetch()} /> : rows.length === 0 ? <EmptyState title="No enrollments found" description="Try adjusting the filters or create an enrollment." /> : <>
      <div className="overflow-x-auto rounded-lg border border-slate-200 bg-white"><table className="min-w-[900px] divide-y divide-slate-200 text-left text-sm">
        <thead className="bg-slate-50 text-xs uppercase text-slate-500"><tr>{['Student', 'Course', 'Section', 'Academic term', 'Enrolled on', 'Grade mode', 'Status', 'Actions'].map((heading) => <th key={heading} className="px-4 py-3">{heading}</th>)}</tr></thead>
        <tbody className="divide-y divide-slate-100">{rows.map((enrollment) => {
          const student = enrollment.student
          const offering = enrollment.course_offering
          return <tr key={enrollment.id}>
            <td className="px-4 py-3"><span className="font-medium">{student ? `${student.first_name} ${student.last_name}` : 'Student'}</span><span className="block text-xs text-slate-500">{student?.student_number ?? `#${enrollment.student_id}`}</span></td>
            <td className="px-4 py-3">{offering?.course?.course_code ?? '—'}<span className="block text-xs text-slate-500">{offering?.course?.title ?? ''}</span></td>
            <td className="px-4 py-3">{offering?.section ?? '—'}</td>
            <td className="px-4 py-3">{offering?.academic_term?.name ?? offering?.academic_term?.code ?? '—'}</td>
            <td className="px-4 py-3">{enrollment.enrolled_on}</td>
            <td className="px-4 py-3 capitalize">{enrollment.grade_mode.replace('_', ' ')}</td>
            <td className="px-4 py-3">{canManage ? <Select aria-label={`Status for enrollment ${enrollment.id}`} className="min-w-36" value={enrollment.status} disabled={update.isPending} onChange={(event) => updateStatus(enrollment, event.target.value as EnrollmentStatus)}>{statuses.map((item) => <option key={item} value={item}>{item}</option>)}</Select> : <span className="capitalize">{enrollment.status}</span>}</td>
            <td className="px-4 py-3">{canManage ? <Button variant="danger" className="px-3 py-1.5" onClick={() => setDeleting(enrollment)}>Delete</Button> : '—'}</td>
          </tr>
        })}</tbody>
      </table></div>
      <Pagination currentPage={query.data?.meta.current_page ?? 1} lastPage={query.data?.meta.last_page ?? 1} onPageChange={setPage} />
    </>}
    <EnrollmentForm open={creating} onClose={() => setCreating(false)} />
    <ConfirmDialog open={deleting !== null} title="Delete enrollment?" message="This removes the enrollment record and updates the course offering count. This action cannot be undone." confirmLabel="Delete enrollment" loading={remove.isPending} onCancel={() => setDeleting(null)} onConfirm={deleteEnrollment} />
  </section>
}
