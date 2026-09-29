import { useMemo } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import StudentForm, { type StudentFormInput, type StudentFormValues } from '../components/students/StudentForm'
import ErrorState from '../components/feedback/ErrorState'
import LoadingState from '../components/feedback/LoadingState'
import { useStudent, useUpdateStudent } from '../hooks/useStudents'
import type { StudentUpdateData } from '../api/students'

const toFormDate = (value?: string | null): string | undefined => value?.slice(0, 10)

export default function StudentEditPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const studentId = Number(id)
  const studentQuery = useStudent(studentId)
  const updateStudent = useUpdateStudent(studentId)

  const defaultValues = useMemo(() => {
    const student = studentQuery.data

    if (!student) {
      return undefined
    }

    return {
      student_number: student.student_number,
      first_name: student.first_name,
      middle_name: student.middle_name ?? undefined,
      last_name: student.last_name,
      program_id: student.program_id,
      year_level: student.year_level,
      date_of_birth: toFormDate(student.date_of_birth),
      gender: student.gender ?? undefined,
      phone: student.phone ?? undefined,
      address: student.address ?? undefined,
      admission_date: toFormDate(student.admission_date) ?? '',
      expected_graduation_date: toFormDate(student.expected_graduation_date),
      status: student.status === 'inactive' || student.status === 'dismissed'
        ? 'active'
        : student.status,
      cumulative_gpa: student.cumulative_gpa === null ? undefined : Number(student.cumulative_gpa),
    } satisfies Partial<StudentFormInput>
  }, [studentQuery.data])

  const handleSubmit = async (data: StudentFormValues): Promise<void> => {
    await updateStudent.mutateAsync(data as StudentUpdateData)
    navigate(`/students/${studentId}`)
  }

  if (!Number.isInteger(studentId) || studentId < 1 || studentQuery.isError) {
    return <ErrorState message="Unable to load this student." />
  }

  if (studentQuery.isLoading || !defaultValues) {
    return <LoadingState label="Loading student..." />
  }

  return (
    <section className="mx-auto max-w-4xl space-y-6">
      <header>
        <h1 className="text-2xl font-semibold text-slate-900">Edit student</h1>
        <p className="mt-1 text-sm text-slate-600">Update the student information record.</p>
      </header>
      <div className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <StudentForm
          defaultValues={defaultValues}
          onSubmit={handleSubmit}
          submitLabel="Save changes"
          isSubmitting={updateStudent.isPending}
        />
      </div>
    </section>
  )
}
