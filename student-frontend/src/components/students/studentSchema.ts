import { z } from 'zod'

const optionalDate = z.string().optional()

export const studentSchema = z.object({
  student_number: z.string().min(1).max(30),
  first_name: z.string().min(1),
  middle_name: z.string().optional(),
  last_name: z.string().min(1),
  program_id: z.coerce.number(),
  year_level: z.coerce.number().int().min(1).max(6),
  date_of_birth: optionalDate,
  gender: z.enum(['male', 'female', 'other', 'prefer_not_to_say']).optional(),
  phone: z.string().optional(),
  address: z.string().optional(),
  admission_date: z.string().date(),
  expected_graduation_date: optionalDate,
  status: z.enum(['active', 'on_leave', 'graduated', 'withdrawn', 'dismissed']),
  cumulative_gpa: z.coerce.number().min(0).max(4).optional(),
})

export type StudentFormInput = z.input<typeof studentSchema>
export type StudentFormValues = z.output<typeof studentSchema>
export type StudentFormErrors = Record<string, string[]>
