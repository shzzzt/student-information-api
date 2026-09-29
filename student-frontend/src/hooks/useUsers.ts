import { useQuery } from '@tanstack/react-query'
import usersApi from '../api/users'
export function useFacultyUsers(enabled = true) { return useQuery({ queryKey: ['users', 'faculty'], queryFn: () => usersApi.list('faculty'), enabled }) }
