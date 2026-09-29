import { useState } from 'react'
import { Link } from 'react-router-dom'
import EmptyState from '../components/feedback/EmptyState'
import ErrorState from '../components/feedback/ErrorState'
import LoadingState from '../components/feedback/LoadingState'
import Pagination from '../components/ui/Pagination'
import Select from '../components/ui/Select'
import { useAcademicTerms } from '../hooks/useAcademicTerms'
import { useCourseOfferings } from '../hooks/useCourseOfferings'
import { useCourses } from '../hooks/useCourses'
import { useFacultyUsers } from '../hooks/useUsers'
import { useAuth } from '../contexts/AuthContext'

function formatTime(value: string): string {
  const [hourText, minute] = value.slice(0, 5).split(':')
  const hour = Number(hourText)

  return `${hour % 12 || 12}:${minute} ${hour >= 12 ? 'PM' : 'AM'}`
}

export default function OfferingsListPage() {
  const { user } = useAuth()
  const canManage = user?.role === 'admin' || user?.role === 'registrar'
  const [course, setCourse] = useState('')
  const [term, setTerm] = useState('')
  const [instructor, setInstructor] = useState('')
  const [status, setStatus] = useState('')
  const [page, setPage] = useState(1)
  const query = useCourseOfferings({ course_id: course ? Number(course) : undefined, academic_term_id: term ? Number(term) : undefined, instructor_id: instructor ? Number(instructor) : undefined, status: status || undefined, page, per_page: 20 })
  const courses = useCourses({ per_page: 100 }, canManage)
  const terms = useAcademicTerms({ per_page: 100 }, canManage)
  const faculty = useFacultyUsers(canManage)
  const rows = query.data?.data ?? []

  return (
    <section className="space-y-6">
      <header className="flex flex-wrap items-center justify-between gap-3"><div><h1 className="text-2xl font-semibold">Course Offerings</h1><p className="mt-1 text-sm text-slate-600">{canManage ? 'Manage scheduled course sections.' : 'View course sections available to your account.'}</p></div>{canManage && <Link className="btn-primary" to="/course-offerings/new">+ New Offering</Link>}</header>
      <div className={`grid gap-4 rounded-lg border bg-white p-4 ${canManage ? 'md:grid-cols-2 lg:grid-cols-4' : 'md:grid-cols-1'}`}>
        {canManage && <>
        <Select label="Course" value={course} onChange={(event) => { setCourse(event.target.value); setPage(1) }}><option value="">All courses</option>{courses.data?.data.map((item) => <option key={item.id} value={item.id}>{item.course_code}</option>)}</Select>
        <Select label="Academic term" value={term} onChange={(event) => { setTerm(event.target.value); setPage(1) }}><option value="">All terms</option>{terms.data?.data.map((item) => <option key={item.id} value={item.id}>{item.code}</option>)}</Select>
        <Select label="Instructor" value={instructor} onChange={(event) => { setInstructor(event.target.value); setPage(1) }}><option value="">All instructors</option>{faculty.data?.map((user) => <option key={user.id} value={user.id}>{user.last_name}, {user.first_name}</option>)}</Select>
        </>}
        <Select label="Status" value={status} onChange={(event) => { setStatus(event.target.value); setPage(1) }}><option value="">All statuses</option><option value="scheduled">Scheduled</option><option value="open">Open</option><option value="closed">Closed</option><option value="cancelled">Cancelled</option><option value="completed">Completed</option></Select>
      </div>
      {query.isLoading ? <LoadingState /> : query.isError ? <ErrorState message="Unable to load course offerings." onRetry={() => void query.refetch()} /> : rows.length === 0 ? <EmptyState title="No course offerings found" description="Try adjusting your filters." /> : <>
        <div className="overflow-x-auto rounded-lg border bg-white"><table className="min-w-full divide-y text-left text-sm"><thead className="bg-slate-50 text-xs uppercase text-slate-500"><tr>{['Course', 'Title', 'Section', 'Schedule', 'Term', 'Instructor', 'Capacity', 'Enrolled', 'Status', ...(canManage ? ['Actions'] : [])].map((heading) => <th className="px-4 py-3" key={heading}>{heading}</th>)}</tr></thead><tbody className="divide-y">{rows.map((offering) => { const full = offering.capacity > 0 && offering.enrolled_count >= offering.capacity; return <tr key={offering.id}><td className="px-4 py-3 font-medium">{offering.course?.course_code ?? '—'}</td><td className="px-4 py-3">{offering.course?.title ?? '—'}</td><td className="px-4 py-3">{offering.section}</td><td className="whitespace-nowrap px-4 py-3">{offering.day_of_week} {formatTime(offering.start_time)}–{formatTime(offering.end_time)}</td><td className="px-4 py-3">{offering.academic_term?.name ?? offering.academic_term?.code ?? '—'}</td><td className="px-4 py-3">{offering.instructor ? `${offering.instructor.first_name} ${offering.instructor.last_name}` : '—'}</td><td className={`px-4 py-3 ${full ? 'font-semibold text-red-600' : ''}`}>{offering.capacity}</td><td className={`px-4 py-3 ${full ? 'font-semibold text-red-600' : ''}`}>{offering.enrolled_count}</td><td className="px-4 py-3 capitalize">{offering.status}</td>{canManage && <td className="px-4 py-3"><Link className="text-slate-700 hover:underline" to={`/course-offerings/${offering.id}/edit`}>Edit</Link></td>}</tr> })}</tbody></table></div>
        <Pagination currentPage={query.data?.meta.current_page ?? 1} lastPage={query.data?.meta.last_page ?? 1} onPageChange={setPage} />
      </>}
    </section>
  )
}
