import { forwardRef, useId, type InputHTMLAttributes } from 'react'
import clsx from 'clsx'

type InputProps = InputHTMLAttributes<HTMLInputElement> & {
  label?: string
  error?: string
  help?: string
}

const Input = forwardRef<HTMLInputElement, InputProps>(
  ({ id, label, error, help, required, className, ...props }, ref) => {
    const generatedId = useId()
    const inputId = id ?? generatedId
    const errorId = `${inputId}-error`
    const helpId = `${inputId}-help`
    const describedBy = [error ? errorId : null, help ? helpId : null]
      .filter(Boolean)
      .join(' ') || undefined

    return (
      <div className="space-y-1">
        {label && (
          <label className="label" htmlFor={inputId}>
            {label}
            {required && <span className="ml-1 text-red-500" aria-hidden="true">*</span>}
          </label>
        )}
        <input
          ref={ref}
          id={inputId}
          required={required}
          className={clsx('input', error && 'input-error', className)}
          aria-invalid={error ? true : undefined}
          aria-describedby={describedBy}
          {...props}
        />
        {help && !error && <p id={helpId} className="text-sm text-slate-500">{help}</p>}
        {error && <p id={errorId} className="error-text">{error}</p>}
      </div>
    )
  },
)

Input.displayName = 'Input'

export default Input
