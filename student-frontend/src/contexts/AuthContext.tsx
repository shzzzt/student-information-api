import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from 'react'
import authApi, { type User } from '../api/auth'
import { AUTH_TOKEN_KEY } from '../api/client'

type AuthContextValue = {
  user: User | null
  isLoading: boolean
  isAuthenticated: boolean
  login: (email: string, password: string) => Promise<void>
  logout: () => Promise<void>
  refreshUser: () => Promise<void>
}

const AuthContext = createContext<AuthContextValue | undefined>(undefined)

type AuthProviderProps = {
  children: ReactNode
}

export function AuthProvider({ children }: AuthProviderProps) {
  const [user, setUser] = useState<User | null>(null)
  const [isLoading, setIsLoading] = useState(true)

  const refreshUser = useCallback(async (): Promise<void> => {
    if (!localStorage.getItem(AUTH_TOKEN_KEY)) {
      setUser(null)
      return
    }

    try {
      const currentUser = await authApi.me()
      setUser(currentUser)
    } catch {
      localStorage.removeItem(AUTH_TOKEN_KEY)
      setUser(null)
    }
  }, [])

  const login = useCallback(async (email: string, password: string): Promise<void> => {
    const { access_token: accessToken } = await authApi.login(email, password)
    localStorage.setItem(AUTH_TOKEN_KEY, accessToken)

    try {
      const authenticatedUser = await authApi.me()
      setUser(authenticatedUser)
    } catch (error: unknown) {
      localStorage.removeItem(AUTH_TOKEN_KEY)
      setUser(null)
      throw error
    }
  }, [])

  const logout = useCallback(async (): Promise<void> => {
    try {
      if (localStorage.getItem(AUTH_TOKEN_KEY)) {
        await authApi.logout()
      }
    } finally {
      localStorage.removeItem(AUTH_TOKEN_KEY)
      setUser(null)
    }
  }, [])

  useEffect(() => {
    const loadUser = async (): Promise<void> => {
      await refreshUser()
      setIsLoading(false)
    }

    void loadUser()
  }, [refreshUser])

  const value = useMemo<AuthContextValue>(
    () => ({
      user,
      isLoading,
      isAuthenticated: user !== null,
      login,
      logout,
      refreshUser,
    }),
    [isLoading, login, logout, refreshUser, user],
  )

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}

// This hook intentionally shares its context module with AuthProvider.
// eslint-disable-next-line react-refresh/only-export-components
export function useAuth(): AuthContextValue {
  const context = useContext(AuthContext)

  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider')
  }

  return context
}
