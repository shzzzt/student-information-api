import { useNavigate } from 'react-router-dom'
import StudentForm, { type StudentFormValues } from '../components/students/StudentForm'
import { useCreateStudent } from '../hooks/useStudents'
import type { StudentWriteData } from '../api/students'

export default function StudentCreatePage() {
  const navigate = useNavigate()
  const createStudent = useCreateStudent()

  const handleSubmit = async (data: StudentFormValues): Promise<void> => {
    const student = await createStudent.mutateAsync(data as StudentWriteData)

    navigate(`/students/${student.id}`, {
      replace: true,
      state: { toast: 'Student created successfully.' },
    })
  }

  return (
    <section className="mx-auto max-w-4xl space-y-6">
      <header>
        <h1 className="text-2xl font-semibold text-slate-900">New student</h1>
        <p className="mt-1 text-sm text-slate-600">Create a student information record.</p>
      </header>
      <div className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <StudentForm
          onSubmit={handleSubmit}
          submitLabel="Create student"
          isSubmitting={createStudent.isPending}
        />
      </div>
    </section>
  )
}
