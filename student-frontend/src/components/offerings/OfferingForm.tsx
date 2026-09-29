import { zodResolver } from '@hookform/resolvers/zod'
import { useForm } from 'react-hook-form'
import { z } from 'zod'
import { useAcademicTerms } from '../../hooks/useAcademicTerms'
import { useCourses } from '../../hooks/useCourses'
import { useFacultyUsers } from '../../hooks/useUsers'
import ErrorState from '../feedback/ErrorState'
import LoadingState from '../feedback/LoadingState'
import Button from '../ui/Button'
import Input from '../ui/Input'
import Select from '../ui/Select'

const schema = z.object({
  course_id: z.coerce.number().int().positive(),
  academic_term_id: z.coerce.number().int().positive(),
  instructor_id: z.coerce.number().int().positive(),
  section: z.string().min(1).max(10),
  room: z.string().optional(),
  day_of_week: z.enum(['MWF', 'TTh', 'Sat', 'Sun']),
  start_time: z.string().regex(/^([01]\d|2[0-3]):[0-5]\d$/, 'Enter a valid start time'),
  end_time: z.string().regex(/^([01]\d|2[0-3]):[0-5]\d$/, 'Enter a valid end time'),
  delivery_mode: z.enum(['in_person', 'online', 'hybrid']),
  capacity: z.coerce.number().int().min(1),
  status: z.enum(['scheduled', 'open', 'closed', 'cancelled', 'completed']),
}).refine((data) => data.end_time > data.start_time, {
  message: 'End time must be after start time',
  path: ['end_time'],
})

export type OfferingFormValues = z.output<typeof schema>

type Props = {
  defaultValues?: Partial<OfferingFormValues>
  onSubmit: (data: OfferingFormValues) => Promise<void>
  submitLabel: string
  isSubmitting: boolean
}

export default function OfferingForm({ defaultValues, onSubmit, submitLabel, isSubmitting }: Props) {
  const courses = useCourses({ per_page: 100 })
  const terms = useAcademicTerms({ per_page: 100 })
  const faculty = useFacultyUsers()
  const { register, handleSubmit, formState: { errors } } = useForm<z.input<typeof schema>, unknown, OfferingFormValues>({
    resolver: zodResolver(schema),
    defaultValues: { day_of_week: 'MWF', start_time: '09:00', end_time: '10:30', delivery_mode: 'in_person', capacity: 30, status: 'scheduled', ...defaultValues },
  })

  if (courses.isLoading || terms.isLoading || faculty.isLoading) return <LoadingState label="Loading offering options..." />
  if (courses.isError || terms.isError || faculty.isError) return <ErrorState message="Unable to load offering options." />

  return (
    <form className="space-y-5" onSubmit={handleSubmit(onSubmit)}>
      <div className="grid gap-5 md:grid-cols-2">
        <Select label="Course" required error={errors.course_id?.message} {...register('course_id')}>
          <option value="">Select a course</option>
          {courses.data?.data.map((course) => <option key={course.id} value={course.id}>{course.course_code} - {course.title}</option>)}
        </Select>
        <Select label="Academic term" required error={errors.academic_term_id?.message} {...register('academic_term_id')}>
          <option value="">Select a term</option>
          {terms.data?.data.map((term) => <option key={term.id} value={term.id}>{term.code} - {term.name}</option>)}
        </Select>
        <Select label="Instructor" required error={errors.instructor_id?.message} {...register('instructor_id')}>
          <option value="">Select faculty</option>
          {faculty.data?.map((user) => <option key={user.id} value={user.id}>{user.first_name} {user.last_name}</option>)}
        </Select>
        <Input label="Section" required error={errors.section?.message} {...register('section')} />
        <Input label="Room" error={errors.room?.message} {...register('room')} />
        <Select label="Meeting days" required error={errors.day_of_week?.message} {...register('day_of_week')}>
          <option value="MWF">Monday / Wednesday / Friday</option>
          <option value="TTh">Tuesday / Thursday</option>
          <option value="Sat">Saturday</option>
          <option value="Sun">Sunday</option>
        </Select>
        <Input label="Start time" type="time" required error={errors.start_time?.message} {...register('start_time')} />
        <Input label="End time" type="time" required error={errors.end_time?.message} {...register('end_time')} />
        <Select label="Delivery mode" required error={errors.delivery_mode?.message} {...register('delivery_mode')}>
          <option value="in_person">In person</option><option value="online">Online</option><option value="hybrid">Hybrid</option>
        </Select>
        <Input label="Capacity" type="number" min={1} required error={errors.capacity?.message} {...register('capacity')} />
        <Select label="Status" required error={errors.status?.message} {...register('status')}>
          <option value="scheduled">Scheduled</option><option value="open">Open</option><option value="closed">Closed</option><option value="cancelled">Cancelled</option><option value="completed">Completed</option>
        </Select>
      </div>
      <div className="flex justify-end"><Button type="submit" loading={isSubmitting}>{submitLabel}</Button></div>
    </form>
  )
}
