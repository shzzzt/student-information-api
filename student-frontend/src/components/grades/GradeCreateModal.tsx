import { useState } from 'react'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import { getApiErrorMessage, getValidationErrors } from '../../api/errors'
import { useEnrollments } from '../../hooks/useEnrollments'
import { useCreateGrade } from '../../hooks/useGrades'
import { useToast } from '../../contexts/ToastContext'
import Button from '../ui/Button'
import Input from '../ui/Input'
import Modal from '../ui/Modal'
import Select from '../ui/Select'

const schema = z.object({
  enrollment_id: z.coerce.number().int().positive('Choose an enrollment.'),
  letter_grade: z.string().max(5).optional(),
  numeric_grade: z.union([z.literal(''), z.coerce.number().min(0).max(100)]).optional(),
  grade_points: z.union([z.literal(''), z.coerce.number().min(0).max(4)]).optional(),
  status: z.enum(['pending', 'submitted', 'approved', 'disputed', 'revised']),
  remarks: z.string().optional(),
})
type Values = z.infer<typeof schema>

export default function GradeCreateModal({ open, onClose }: { open: boolean; onClose: () => void }) {
  const create = useCreateGrade()
  const enrollments = useEnrollments({ per_page: 100 }, open)
  const { showToast } = useToast()
  const [serverError, setServerError] = useState<string | null>(null)
  const { register, handleSubmit, reset, setError, formState: { errors } } = useForm<z.input<typeof schema>, unknown, Values>({
    resolver: zodResolver(schema),
    defaultValues: { status: 'submitted', letter_grade: '', numeric_grade: '', grade_points: '', remarks: '' },
  })

  const close = (): void => { reset(); setServerError(null); onClose() }
  const submit = (values: Values): void => {
    setServerError(null)
    create.mutate({
      enrollment_id: values.enrollment_id,
      letter_grade: values.letter_grade || null,
      numeric_grade: values.numeric_grade === '' ? null : Number(values.numeric_grade),
      grade_points: values.grade_points === '' ? null : Number(values.grade_points),
      status: values.status,
      remarks: values.remarks || null,
    }, {
      onSuccess: () => { showToast('Grade recorded.', 'success'); close() },
      onError: (error: unknown) => {
        Object.entries(getValidationErrors(error)).forEach(([field, messages]) => {
          if (field in values) setError(field as keyof Values, { message: messages[0] })
        })
        setServerError(getApiErrorMessage(error, 'Could not record this grade.'))
      },
    })
  }

  const eligible = enrollments.data?.data.filter((enrollment) => enrollment.grade == null) ?? []
  return <Modal open={open} title="Record grade" onClose={close} footer={<><Button variant="secondary" onClick={close} disabled={create.isPending}>Cancel</Button><Button type="submit" form="grade-create-form" loading={create.isPending}>Save grade</Button></>}>
    {serverError && <p className="mb-4 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700" role="alert">{serverError}</p>}
    {enrollments.isError ? <p className="text-sm text-red-700" role="alert">{getApiErrorMessage(enrollments.error, 'Could not load enrollments for grading.')}</p> : <form id="grade-create-form" className="space-y-4" onSubmit={handleSubmit(submit)}>
      <Select label="Enrollment" required error={errors.enrollment_id?.message} {...register('enrollment_id')}><option value="">Select a student and course</option>{eligible.map((enrollment) => <option key={enrollment.id} value={enrollment.id}>{enrollment.student?.student_number ?? `Student #${enrollment.student_id}`} · {enrollment.student?.first_name} {enrollment.student?.last_name} · {enrollment.course_offering?.course?.course_code ?? 'Course'} / {enrollment.course_offering?.section ?? 'Section'}</option>)}</Select>
      {eligible.length === 0 && enrollments.isSuccess && <p className="text-sm text-slate-600">There are no enrollments without a grade to select.</p>}
      <div className="grid gap-4 sm:grid-cols-2">
        <Input label="Letter grade" placeholder="A, B+, etc." error={errors.letter_grade?.message} {...register('letter_grade')} />
        <Input label="Numeric grade" type="number" min={0} max={100} step="0.01" error={errors.numeric_grade?.message} {...register('numeric_grade')} />
        <Input label="Grade points" type="number" min={0} max={4} step="0.01" error={errors.grade_points?.message} {...register('grade_points')} />
        <Select label="Status" required error={errors.status?.message} {...register('status')}><option value="pending">Pending</option><option value="submitted">Submitted</option><option value="approved">Approved</option><option value="disputed">Disputed</option><option value="revised">Revised</option></Select>
      </div>
      <label className="block space-y-1"><span className="label">Remarks</span><textarea className="input min-h-20" {...register('remarks')} /></label>
    </form>}
  </Modal>
}
