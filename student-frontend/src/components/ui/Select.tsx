import { forwardRef, useId, type SelectHTMLAttributes } from 'react'
import clsx from 'clsx'

type SelectProps = SelectHTMLAttributes<HTMLSelectElement> & {
  label?: string
  error?: string
  help?: string
}

const Select = forwardRef<HTMLSelectElement, SelectProps>(
  ({ id, label, error, help, required, className, children, ...props }, ref) => {
    const generatedId = useId()
    const selectId = id ?? generatedId
    const errorId = `${selectId}-error`
    const helpId = `${selectId}-help`
    const describedBy = [error ? errorId : null, help ? helpId : null]
      .filter(Boolean)
      .join(' ') || undefined

    return (
      <div className="space-y-1">
        {label && (
          <label className="label" htmlFor={selectId}>
            {label}
            {required && <span className="ml-1 text-red-500" aria-hidden="true">*</span>}
          </label>
        )}
        <select
          ref={ref}
          id={selectId}
          required={required}
          className={clsx('input', error && 'input-error', className)}
          aria-invalid={error ? true : undefined}
          aria-describedby={describedBy}
          {...props}
        >
          {children}
        </select>
        {help && !error && <p id={helpId} className="text-sm text-slate-500">{help}</p>}
        {error && <p id={errorId} className="error-text">{error}</p>}
      </div>
    )
  },
)

Select.displayName = 'Select'

export default Select
