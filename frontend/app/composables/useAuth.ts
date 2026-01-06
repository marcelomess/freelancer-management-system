import type { User, LoginCredentials, RegisterData } from '@/types'

export const useAuth = () => {
  const { apiFetch } = useApi()
  const token = useCookie('auth_token')
  const user = useState<User | null>('user', () => null)

  const login = async (credentials: LoginCredentials) => {
    const data = await apiFetch<{ user: User; token: string }>('/login', {
      method: 'POST',
      body: credentials,
    })

    token.value = data.token
    user.value = data.user
    
    return data
  }

  const register = async (data: RegisterData) => {
    const response = await apiFetch<{ user: User; token: string }>('/register', {
      method: 'POST',
      body: data,
    })

    token.value = response.token
    user.value = response.user

    return response
  }

  const logout = async () => {
    try {
      await apiFetch('/logout', { method: 'POST' })
    } finally {
      token.value = null
      user.value = null
      navigateTo('/login')
    }
  }

  const fetchUser = async () => {
    if (!token.value) return null

    try {
      user.value = await apiFetch<User>('/user')
      return user.value
    } catch (error) {
      token.value = null
      user.value = null
      return null
    }
  }

  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isFreelancer = computed(() => user.value?.user_type === 'freelancer')
  const isClient = computed(() => user.value?.user_type === 'client')

  return {
    user,
    token,
    login,
    register,
    logout,
    fetchUser,
    isAuthenticated,
    isFreelancer,
    isClient,
  }
}
