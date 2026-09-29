import { zodResolver } from '@hookform/resolvers/zod'
import axios from 'axios'
import { useEffect } from 'react'
import { useForm, type SubmitHandler } from 'react-hook-form'
import type { StudentStatus } from '../../types'
import { studentSchema, type StudentFormErrors, type StudentFormInput, type StudentFormValues } from './studentSchema'
export type { StudentFormErrors, StudentFormInput, StudentFormValues } from './studentSchema'
import Button from '../ui/Button'
import Input from '../ui/Input'
import Select from '../ui/Select'
import ErrorState from '../feedback/ErrorState'
import LoadingState from '../feedback/LoadingState'
import { usePrograms } from '../../hooks/usePrograms'

type StudentFormProps = {
  defaultValues?: Partial<StudentFormInput>
  onSubmit: (data: StudentFormValues) => Promise<void>
  submitLabel: string
  isSubmitting: boolean
  serverErrors?: StudentFormErrors
}

const statuses: Array<{ value: StudentStatus; label: string }> = [
  { value: 'active', label: 'Active' },
  { value: 'on_leave', label: 'On leave' },
  { value: 'graduated', label: 'Graduated' },
  { value: 'withdrawn', label: 'Withdrawn' },
]

export default function StudentForm({
  defaultValues,
  onSubmit,
  submitLabel,
  isSubmitting,
  serverErrors,
}: StudentFormProps) {
  const programsQuery = usePrograms()
  const {
    register,
    handleSubmit,
    setError,
    formState: { errors },
  } = useForm<StudentFormInput, unknown, StudentFormValues>({
    resolver: zodResolver(studentSchema),
    defaultValues,
  })

  useEffect(() => {
    if (!serverErrors) {
      return
    }

    Object.entries(serverErrors).forEach(([field, messages]) => {
      setError(field as keyof StudentFormValues, {
        type: 'server',
        message: messages[0],
      })
    })
  }, [serverErrors, setError])

  const submit: SubmitHandler<StudentFormValues> = async (data) => {
    try {
      await onSubmit(data)
    } catch (error: unknown) {
      if (axios.isAxiosError<{ errors?: StudentFormErrors; message?: string }>(error)) {
        const responseData = error.response?.data
        const fieldErrors = responseData?.errors

        if (fieldErrors) {
          Object.entries(fieldErrors).forEach(([field, messages]) => {
            setError(field as keyof StudentFormValues, {
              type: 'server',
              message: messages[0],
            })
          })
        }

        if (!fieldErrors && responseData?.message) {
          setError('root.server', { type: 'server', message: responseData.message })
        }
      }
    }
  }

  if (programsQuery.isLoading) {
    return <LoadingState label="Loading programs..." />
  }

  if (programsQuery.isError) {
    return <ErrorState message="Unable to load programs." onRetry={() => void programsQuery.refetch()} />
  }

  return (
    <form className="space-y-6" onSubmit={handleSubmit(submit)}>
      {errors.root?.server?.message && (
        <div className="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
          {errors.root.server.message}
        </div>
      )}

      <div className="grid gap-5 md:grid-cols-2">
        <Input label="Student number" required error={errors.student_number?.message} {...register('student_number')} />
        <Input label="First name" required error={errors.first_name?.message} {...register('first_name')} />
        <Input label="Middle name" error={errors.middle_name?.message} {...register('middle_name')} />
        <Input label="Last name" required error={errors.last_name?.message} {...register('last_name')} />
        <Select label="Program" required error={errors.program_id?.message} {...register('program_id')}>
          <option value="">Select a program</option>
          {programsQuery.data?.map((program) => (
            <option key={program.id} value={program.id}>{program.code} - {program.name}</option>
          ))}
        </Select>
        <Input label="Year level" type="number" min={1} max={6} required error={errors.year_level?.message} {...register('year_level')} />
        <Input label="Date of birth" type="date" error={errors.date_of_birth?.message} {...register('date_of_birth')} />
        <Select label="Gender" error={errors.gender?.message} {...register('gender')}>
          <option value="">Prefer not to say</option>
          <option value="male">Male</option>
          <option value="female">Female</option>
          <option value="other">Other</option>
          <option value="prefer_not_to_say">Prefer not to say</option>
        </Select>
        <Input label="Phone" error={errors.phone?.message} {...register('phone')} />
        <Input label="Admission date" type="date" required error={errors.admission_date?.message} {...register('admission_date')} />
        <Input label="Expected graduation date" type="date" error={errors.expected_graduation_date?.message} {...register('expected_graduation_date')} />
        <Select label="Status" required error={errors.status?.message} {...register('status')}>
          {statuses.map((status) => <option key={status.value} value={status.value}>{status.label}</option>)}
        </Select>
        <Input label="Cumulative GPA" type="number" min={0} max={4} step="0.01" error={errors.cumulative_gpa?.message} {...register('cumulative_gpa')} />
      </div>

      <Input label="Address" error={errors.address?.message} {...register('address')} />

      <div className="flex justify-end">
        <Button type="submit" loading={isSubmitting}>{submitLabel}</Button>
      </div>
    </form>
  )
}
