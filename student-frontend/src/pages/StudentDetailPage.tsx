import { useMemo } from 'react'
import { Link, useNavigate, useParams } from 'react-router-dom'
import { useAuth } from '../contexts/AuthContext'
import { useDeleteStudent, useStudent } from '../hooks/useStudents'
import {
  useStudentAcademicRecord,
  useStudentEnrollments,
} from '../hooks/useStudentAcademic'
import Button from '../components/ui/Button'
import EmptyState from '../components/feedback/EmptyState'
import ErrorState from '../components/feedback/ErrorState'
import LoadingState from '../components/feedback/LoadingState'
import type { Enrollment, Student } from '../types'

const formatDate = (value?: string | null): string => {
  if (!value) {
    return '—'
  }

  return new Intl.DateTimeFormat('en', { dateStyle: 'medium' }).format(new Date(value))
}

const displayValue = (value?: string | number | null): string =>
  value === null || value === undefined || value === '' ? '—' : String(value)

const enrollmentCourse = (enrollment: Enrollment) => enrollment.course_offering?.course
const enrollmentTerm = (enrollment: Enrollment) => enrollment.course_offering?.academic_term

function StatusBadge({ status }: { status: Student['status'] }) {
  return (
    <span className="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold capitalize text-slate-700">
      {status.replace('_', ' ')}
    </span>
  )
}

function EnrollmentTable({ enrollments }: { enrollments: Enrollment[] }) {
  return (
    <div className="overflow-x-auto rounded-lg border border-slate-200 bg-white">
      <table className="min-w-full divide-y divide-slate-200 text-left text-sm">
        <thead className="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
          <tr>
            <th className="px-4 py-3">Term</th>
            <th className="px-4 py-3">Course code</th>
            <th className="px-4 py-3">Title</th>
            <th className="px-4 py-3">Section</th>
            <th className="px-4 py-3">Status</th>
          </tr>
        </thead>
        <tbody className="divide-y divide-slate-100 text-slate-700">
          {enrollments.map((enrollment) => {
            const course = enrollmentCourse(enrollment)
            const term = enrollmentTerm(enrollment)

            return (
              <tr key={enrollment.id}>
                <td className="px-4 py-3">{term?.name ?? term?.code ?? '—'}</td>
                <td className="px-4 py-3 font-medium">{course?.course_code ?? '—'}</td>
                <td className="px-4 py-3">{course?.title ?? '—'}</td>
                <td className="px-4 py-3">{enrollment.course_offering?.section ?? '—'}</td>
                <td className="px-4 py-3 capitalize">{enrollment.status.replace('_', ' ')}</td>
              </tr>
            )
          })}
        </tbody>
      </table>
    </div>
  )
}

export default function StudentDetailPage() {
  const { id } = useParams<{ id: string }>()
  const studentId = Number(id)
  const navigate = useNavigate()
  const { user } = useAuth()
  const canManage = user?.role === 'admin' || user?.role === 'registrar'
  const canDeactivate = user?.role === 'admin'
  const studentQuery = useStudent(studentId)
  const enrollmentsQuery = useStudentEnrollments(studentId)
  const recordQuery = useStudentAcademicRecord(studentId)
  const deleteStudent = useDeleteStudent()

  const totalUnits = useMemo(
    () => (enrollmentsQuery.data ?? []).reduce(
      (total, enrollment) => total + (enrollmentCourse(enrollment)?.credits ?? 0),
      0,
    ),
    [enrollmentsQuery.data],
  )

  if (!Number.isInteger(studentId) || studentId < 1 || studentQuery.isError) {
    return <ErrorState message="Unable to load this student." />
  }

  if (studentQuery.isLoading || !studentQuery.data) {
    return <LoadingState label="Loading student..." />
  }

  const student = studentQuery.data
  const email = student.email ?? student.user?.email

  const handleDeactivate = (): void => {
    deleteStudent.mutate(student.id, {
      onSuccess: () => navigate('/students'),
    })
  }

  return (
    <section className="space-y-8">
      <header className="flex flex-col justify-between gap-4 lg:flex-row lg:items-start">
        <div>
          <div className="flex flex-wrap items-center gap-3">
            <h1 className="text-2xl font-semibold text-slate-900">
              {student.first_name} {student.middle_name ? `${student.middle_name} ` : ''}{student.last_name}
            </h1>
            <StatusBadge status={student.status} />
          </div>
          <p className="mt-2 text-sm text-slate-600">
            {student.student_number} · {student.program?.code ?? 'No program'} · Year {student.year_level}
          </p>
        </div>
        <div className="flex gap-3">
          {canManage && <Link className="btn-secondary" to={`/students/${student.id}/edit`}>Edit</Link>}
          {canDeactivate && (
            <Button variant="danger" loading={deleteStudent.isPending} onClick={handleDeactivate}>
              Deactivate
            </Button>
          )}
        </div>
      </header>

      <div className="grid gap-6 lg:grid-cols-2">
        <section className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-slate-900">Personal information</h2>
          <dl className="mt-5 grid gap-4 sm:grid-cols-2">
            <div><dt className="text-xs uppercase text-slate-500">Date of birth</dt><dd className="mt-1 text-sm">{formatDate(student.date_of_birth)}</dd></div>
            <div><dt className="text-xs uppercase text-slate-500">Gender</dt><dd className="mt-1 text-sm capitalize">{displayValue(student.gender?.replaceAll('_', ' '))}</dd></div>
            <div><dt className="text-xs uppercase text-slate-500">Phone</dt><dd className="mt-1 text-sm">{displayValue(student.phone)}</dd></div>
            <div><dt className="text-xs uppercase text-slate-500">Email</dt><dd className="mt-1 break-words text-sm">{displayValue(email)}</dd></div>
            <div className="sm:col-span-2"><dt className="text-xs uppercase text-slate-500">Address</dt><dd className="mt-1 text-sm">{displayValue(student.address)}</dd></div>
          </dl>
        </section>

        <section className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-slate-900">Academic information</h2>
          <dl className="mt-5 grid gap-4 sm:grid-cols-2">
            <div><dt className="text-xs uppercase text-slate-500">Admission date</dt><dd className="mt-1 text-sm">{formatDate(student.admission_date)}</dd></div>
            <div><dt className="text-xs uppercase text-slate-500">Expected graduation</dt><dd className="mt-1 text-sm">{formatDate(student.expected_graduation_date)}</dd></div>
            <div><dt className="text-xs uppercase text-slate-500">Cumulative GPA</dt><dd className="mt-1 text-sm">{displayValue(student.cumulative_gpa)}</dd></div>
            <div><dt className="text-xs uppercase text-slate-500">Total units</dt><dd className="mt-1 text-sm">{totalUnits}</dd></div>
          </dl>
        </section>
      </div>

      <section className="space-y-4">
        <h2 className="text-xl font-semibold text-slate-900">Enrollments</h2>
        {enrollmentsQuery.isLoading ? <LoadingState label="Loading enrollments..." /> : enrollmentsQuery.isError ? <ErrorState message="Unable to load enrollments." onRetry={() => void enrollmentsQuery.refetch()} /> : enrollmentsQuery.data?.length === 0 ? <EmptyState title="No enrollments" description="This student has no enrollment records." /> : <EnrollmentTable enrollments={enrollmentsQuery.data ?? []} />}
      </section>

      <section className="space-y-4">
        <h2 className="text-xl font-semibold text-slate-900">Academic record</h2>
        {recordQuery.isLoading ? <LoadingState label="Loading academic record..." /> : recordQuery.isError ? <ErrorState message="Unable to load the academic record." onRetry={() => void recordQuery.refetch()} /> : recordQuery.data?.terms.length === 0 ? <EmptyState title="No academic record" description="Grades and course history will appear here." /> : (
          <div className="space-y-5">
            {recordQuery.data?.terms.map((term) => (
              <section key={term.academic_term.id} className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <h3 className="font-semibold text-slate-900">{term.academic_term.name} <span className="font-normal text-slate-500">({term.academic_term.code})</span></h3>
                <div className="mt-4 overflow-x-auto">
                  <table className="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead className="text-xs uppercase tracking-wide text-slate-500"><tr><th className="py-2 pr-4">Course</th><th className="py-2 pr-4">Title</th><th className="py-2 pr-4">Grade</th><th className="py-2">Status</th></tr></thead>
                    <tbody className="divide-y divide-slate-100">
                      {term.enrollments.map((enrollment) => {
                        const course = enrollmentCourse(enrollment)
                        return <tr key={enrollment.id}><td className="py-3 pr-4 font-medium">{course?.course_code ?? '—'}</td><td className="py-3 pr-4">{course?.title ?? '—'}</td><td className="py-3 pr-4">{enrollment.grade?.letter_grade ?? enrollment.grade?.numeric_grade ?? '—'}</td><td className="py-3 capitalize">{enrollment.grade?.status ?? enrollment.status}</td></tr>
                      })}
                    </tbody>
                  </table>
                </div>
              </section>
            ))}
          </div>
        )}
      </section>
    </section>
  )
}
