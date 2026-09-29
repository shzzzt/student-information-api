import { forwardRef, type ButtonHTMLAttributes } from 'react'
import clsx from 'clsx'

export type ButtonVariant = 'primary' | 'secondary' | 'danger'

type ButtonProps = ButtonHTMLAttributes<HTMLButtonElement> & {
  variant?: ButtonVariant
  loading?: boolean
}

const variantClasses: Record<ButtonVariant, string> = {
  primary: 'btn-primary',
  secondary: 'btn-secondary',
  danger: 'btn-danger',
}

const Button = forwardRef<HTMLButtonElement, ButtonProps>(
  ({ variant = 'primary', loading = false, className, children, disabled, ...props }, ref) => (
    <button
      ref={ref}
      type="button"
      className={clsx(variantClasses[variant], className)}
      disabled={disabled || loading}
      aria-busy={loading}
      {...props}
    >
      {loading && (
        <span
          className="mr-2 inline-block h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"
          aria-hidden="true"
        />
      )}
      {loading ? 'Loading...' : children}
    </button>
  ),
)

Button.displayName = 'Button'

export default Button
