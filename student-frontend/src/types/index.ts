export type Role = 'admin' | 'registrar' | 'faculty' | 'instructor' | 'student' | 'advisor'

export type UserStatus = 'active' | 'inactive' | 'suspended' | 'pending'

export interface User {
  id: number
  first_name: string
  last_name: string
  name: string
  email: string
  role: Role
  status: UserStatus
  email_verified_at?: string | null
}

export type ProgramDegreeLevel =
  | 'certificate'
  | 'associate'
  | 'bachelor'
  | 'master'
  | 'doctorate'

export type ProgramStatus = 'active' | 'inactive' | 'archived'

export interface Program {
  id: number
  code: string
  name: string
  department?: string | null
  degree_level: ProgramDegreeLevel
  total_credits?: number | null
  status: ProgramStatus
  created_at?: string
  updated_at?: string
}

export type StudentGender = 'male' | 'female' | 'other' | 'prefer_not_to_say'

export type StudentStatus =
  | 'active'
  | 'inactive'
  | 'on_leave'
  | 'graduated'
  | 'withdrawn'
  | 'dismissed'

export interface Student {
  id: number
  user_id: number
  student_number: string
  first_name: string
  middle_name?: string | null
  last_name: string
  email?: string | null
  program_id: number
  year_level: number
  date_of_birth?: string | null
  gender?: StudentGender | null
  phone?: string | null
  address?: string | null
  admission_date: string
  expected_graduation_date?: string | null
  status: StudentStatus
  cumulative_gpa?: number | string | null
  program?: Program
  user?: User
  created_at?: string
  updated_at?: string
}

export type AcademicTermType = 'fall' | 'spring' | 'summer' | 'winter' | 'intersession'

export type AcademicTermStatus = 'planned' | 'open' | 'closed' | 'archived'

export interface AcademicTerm {
  id: number
  code: string
  name: string
  term_type: AcademicTermType
  academic_year: number
  start_date: string
  end_date: string
  registration_start?: string | null
  registration_end?: string | null
  status: AcademicTermStatus
  created_at?: string
  updated_at?: string
}

export type CourseStatus = 'active' | 'inactive' | 'archived'

export interface Course {
  id: number
  course_code: string
  title: string
  description?: string | null
  credits: number
  lecture_hours: number
  lab_hours: number
  program_id?: number | null
  status: CourseStatus
  program?: Program
  created_at?: string
  updated_at?: string
}

export type DeliveryMode = 'in_person' | 'online' | 'hybrid'

export type CourseOfferingStatus =
  | 'scheduled'
  | 'open'
  | 'closed'
  | 'cancelled'
  | 'completed'

export interface CourseOffering {
  id: number
  course_id: number
  academic_term_id: number
  instructor_id?: number | null
  section: string
  room?: string | null
  day_of_week: 'MWF' | 'TTh' | 'Sat' | 'Sun'
  start_time: string
  end_time: string
  delivery_mode: DeliveryMode
  capacity: number
  enrolled_count: number
  status: CourseOfferingStatus
  course?: Course
  academic_term?: AcademicTerm
  instructor?: User | null
  created_at?: string
  updated_at?: string
}

export type EnrollmentStatus =
  | 'enrolled'
  | 'waitlisted'
  | 'dropped'
  | 'withdrawn'
  | 'completed'

export type GradeMode = 'graded' | 'pass_fail' | 'audit'

export interface Enrollment {
  id: number
  student_id: number
  course_offering_id: number
  enrolled_on: string
  dropped_on?: string | null
  status: EnrollmentStatus
  grade_mode: GradeMode
  student?: Student
  course_offering?: CourseOffering
  grade?: Grade | null
  created_at?: string
  updated_at?: string
}

export type GradeStatus = 'pending' | 'submitted' | 'approved' | 'disputed' | 'revised'

export interface Grade {
  id: number
  enrollment_id: number
  letter_grade?: string | null
  numeric_grade?: number | string | null
  grade_points?: number | string | null
  status: GradeStatus
  graded_by?: number | null
  graded_at?: string | null
  remarks?: string | null
  enrollment?: Enrollment
  graded_by_user?: User | null
  created_at?: string
  updated_at?: string
}

export interface ApiResponse<T> {
  success: boolean
  message: string
  data: T
}

export interface PaginatedResponse<T> extends ApiResponse<T[]> {
  meta: {
    current_page: number
    per_page: number
    total: number
    last_page: number
  }
}

export interface ApiError {
  success: false
  message: string
  errors?: Record<string, string[]>
}
