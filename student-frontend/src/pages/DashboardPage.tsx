import { Link } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import { useAuth } from '../contexts/AuthContext'
import { getApiErrorMessage } from '../api/errors'
import studentsApi from '../api/students'
import programsApi from '../api/programs'
import coursesApi from '../api/courses'
import termsApi from '../api/academicTerms'
import offeringsApi from '../api/courseOfferings'
import enrollmentsApi from '../api/enrollments'
import gradesApi from '../api/grades'
import Button from '../components/ui/Button'

type CountCardProps = { label: string; count: number | undefined; isLoading: boolean; isError: boolean; href: string }

function CountCard({ label, count, isLoading, isError, href }: CountCardProps) {
  return <Link to={href} className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-slate-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-slate-400">
    <p className="text-sm font-medium text-slate-600">{label}</p>
    <p className="mt-3 text-3xl font-semibold text-slate-900">{isLoading ? '…' : isError ? '—' : (count ?? 0).toLocaleString()}</p>
    <span className="mt-3 inline-block text-sm text-slate-600">View records →</span>
  </Link>
}

export default function DashboardPage() {
  const { user } = useAuth()
  const role = user?.role
  const isAdmin = role === 'admin' || role === 'registrar'
  const canReadStudentList = isAdmin
  const canReadCatalog = isAdmin
  const canReadAcademicActivity = ['admin', 'registrar', 'faculty', 'instructor', 'student'].includes(role ?? '')
  const canReadGrades = ['admin', 'registrar', 'faculty', 'instructor', 'student'].includes(role ?? '')

  const students = useQuery({ queryKey: ['dashboard', 'students'], queryFn: async () => (await studentsApi.list({ per_page: 1 })).data, enabled: canReadStudentList })
  const programs = useQuery({ queryKey: ['dashboard', 'programs'], queryFn: () => programsApi.list({ per_page: 1 }), enabled: canReadCatalog })
  const courses = useQuery({ queryKey: ['dashboard', 'courses'], queryFn: () => coursesApi.list({ per_page: 1 }), enabled: canReadCatalog })
  const terms = useQuery({ queryKey: ['dashboard', 'terms'], queryFn: () => termsApi.list({ per_page: 1 }), enabled: canReadCatalog })
  const offerings = useQuery({ queryKey: ['dashboard', 'offerings'], queryFn: () => offeringsApi.list({ per_page: 1 }), enabled: canReadAcademicActivity })
  const enrollments = useQuery({ queryKey: ['dashboard', 'enrollments'], queryFn: () => enrollmentsApi.list({ per_page: 1 }), enabled: canReadAcademicActivity })
  const grades = useQuery({ queryKey: ['dashboard', 'grades'], queryFn: () => gradesApi.list({ per_page: 1 }), enabled: canReadGrades })

  const cards = isAdmin
    ? [
        { label: 'Students', count: students.data?.meta?.total, query: students, href: '/students' },
        { label: 'Programs', count: programs.data?.meta?.total, query: programs, href: '/programs' },
        { label: 'Courses', count: courses.data?.meta?.total, query: courses, href: '/courses' },
        { label: 'Academic terms', count: terms.data?.meta?.total, query: terms, href: '/terms' },
        { label: 'Course offerings', count: offerings.data?.meta?.total, query: offerings, href: '/offerings' },
        { label: 'Enrollments', count: enrollments.data?.meta?.total, query: enrollments, href: '/enrollments' },
      ]
    : canReadAcademicActivity ? [
        { label: 'My course offerings', count: offerings.data?.meta?.total, query: offerings, href: '/offerings' },
        { label: 'My enrollments', count: enrollments.data?.meta?.total, query: enrollments, href: '/enrollments' },
        { label: 'Grades', count: grades.data?.meta?.total, query: grades, href: '/grades' },
      ] : []

  const failed = cards.filter((card) => card.query.isError)
  return <section className="space-y-8">
    <header className="flex flex-wrap items-end justify-between gap-4">
      <div><p className="text-sm font-medium text-slate-500">Student Information Management System</p><h1 className="mt-1 text-3xl font-semibold tracking-tight text-slate-900">Welcome, {user?.first_name ?? user?.name}</h1><p className="mt-2 text-slate-600">Here is a live overview based on your account access.</p></div>
      <span className="rounded-full bg-slate-100 px-3 py-1.5 text-sm font-medium capitalize text-slate-700">{role?.replace('_', ' ')}</span>
    </header>
    {failed.length > 0 && <div className="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900" role="status">
      <span>Some dashboard counts could not be loaded. {getApiErrorMessage(failed[0].query.error, 'Try again in a moment.')}</span>
      <Button variant="secondary" onClick={() => { void Promise.all(failed.map((card) => card.query.refetch())) }}>Retry</Button>
    </div>}
    {cards.length > 0 ? <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
      {cards.map((card) => <CountCard key={card.label} label={card.label} count={card.count} isLoading={card.query.isLoading} isError={card.query.isError} href={card.href} />)}
    </div> : <p className="rounded-lg border border-slate-200 bg-white p-5 text-sm text-slate-600">Your account does not currently have dashboard data modules enabled.</p>}
    <div className="rounded-xl border border-slate-200 bg-white p-5">
      <h2 className="text-lg font-semibold text-slate-900">Quick actions</h2>
      <div className="mt-4 flex flex-wrap gap-3">
        {isAdmin && <><Link className="btn-primary" to="/students/new">Add student</Link><Link className="btn-secondary" to="/offerings/new">Create course offering</Link><Link className="btn-secondary" to="/enrollments">Manage enrollments</Link></>}
        {!isAdmin && canReadAcademicActivity && <><Link className="btn-primary" to="/offerings">View course offerings</Link><Link className="btn-secondary" to="/enrollments">View enrollments</Link><Link className="btn-secondary" to="/grades">View grades</Link></>}
        {role === 'student' && enrollments.data?.data[0] && <Link className="btn-secondary" to={`/students/${enrollments.data.data[0].student_id}`}>Open my academic record</Link>}
      </div>
    </div>
  </section>
}
