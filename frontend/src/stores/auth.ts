import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User } from '@/types/user'
import { api } from '@/utils/api'

interface RegisterData {
  name: string
  email: string
  password: string
  role: string
  store_id: string | number
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('token'))
  const twoFactorVerified = ref(false)

  const isAuthenticated = computed(() => !!token.value)
  const isTwoFactorVerified = computed(() => twoFactorVerified.value)

  async function login(email: string, password: string) {
    const response = await api.post('/auth/login', { email, password })
    const { token: authToken, requires2fa } = response.data

    if (requires2fa) {
      token.value = authToken
      return 'requires2fa'
    }

    token.value = authToken
    await fetchUser()
    return 'success'
  }

  async function verify2FA(code: string) {
    const response = await api.post('/auth/2fa/verify', { code })
    if (response.data.success) {
      twoFactorVerified.value = true
      await fetchUser()
      return true
    }
    return false
  }

  async function fetchUser() {
    const response = await api.get('/auth/me')
    user.value = response.data
  }

  async function logout() {
    await api.post('/auth/logout')
    user.value = null
    token.value = null
    twoFactorVerified.value = false
    localStorage.removeItem('token')
  }

  async function forgotPassword(email: string) {
    await api.post('/auth/forgot-password', { email })
  }

  async function resetPassword(token: string, password: string) {
    await api.post('/auth/reset-password', { token, password })
  }

  async function register(data: RegisterData) {
    await api.post('/auth/register', data)
  }

  function hasRole(roles: string | string[]): boolean {
    if (!user.value) return false
    
    const userRole = user.value.role
    if (Array.isArray(roles)) {
      return roles.includes(userRole)
    }
    return roles === userRole
  }

  // Initialize by fetching user if token exists
  if (token.value) {
    fetchUser().catch(() => {
      token.value = null
      localStorage.removeItem('token')
    })
  }

  return {
    user,
    token,
    isAuthenticated,
    isTwoFactorVerified,
    login,
    verify2FA,
    forgotPassword,
    resetPassword,
    register,
    logout,
    hasRole
  }
})