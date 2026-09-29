import { zodResolver } from '@hookform/resolvers/zod'
import { useForm } from 'react-hook-form'
import { z } from 'zod'
import Button from '../ui/Button'
import Input from '../ui/Input'
import Select from '../ui/Select'

const schema = z.object({ code: z.string().min(1).max(20), name: z.string().min(1), department: z.string().optional(), degree_level: z.enum(['certificate', 'associate', 'bachelor', 'master', 'doctorate']), total_credits: z.coerce.number().int().min(0).optional(), status: z.enum(['active', 'inactive', 'archived']) })
export type ProgramFormValues = z.output<typeof schema>
type Props = { defaultValues?: Partial<ProgramFormValues>; onSubmit: (data: ProgramFormValues) => Promise<void>; submitLabel: string; isSubmitting: boolean }
export default function ProgramForm({ defaultValues, onSubmit, submitLabel, isSubmitting }: Props) {
  const { register, handleSubmit, formState: { errors } } = useForm<z.input<typeof schema>, unknown, ProgramFormValues>({ resolver: zodResolver(schema), defaultValues })
  return <form className="space-y-5" onSubmit={handleSubmit(onSubmit)}><div className="grid gap-5 md:grid-cols-2"><Input label="Code" required error={errors.code?.message} {...register('code')} /><Input label="Name" required error={errors.name?.message} {...register('name')} /><Input label="Department" error={errors.department?.message} {...register('department')} /><Select label="Degree level" required error={errors.degree_level?.message} {...register('degree_level')}><option value="bachelor">Bachelor</option><option value="certificate">Certificate</option><option value="associate">Associate</option><option value="master">Master</option><option value="doctorate">Doctorate</option></Select><Input label="Total credits" type="number" error={errors.total_credits?.message} {...register('total_credits')} /><Select label="Status" required error={errors.status?.message} {...register('status')}><option value="active">Active</option><option value="inactive">Inactive</option><option value="archived">Archived</option></Select></div><div className="flex justify-end"><Button type="submit" loading={isSubmitting}>{submitLabel}</Button></div></form>
}
