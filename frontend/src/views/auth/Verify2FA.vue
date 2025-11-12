<template>
  <AuthLayout>
    <template #title>Two-Factor Authentication</template>
    <template #subtitle>
      Please enter the verification code from your authenticator app
    </template>

    <form class="space-y-6" @submit.prevent="handleVerify">
      <div>
        <label for="code" class="block text-sm font-medium text-gray-700">
          Verification Code
        </label>
        <div class="mt-1">
          <input
            id="code"
            v-model="code"
            name="code"
            type="text"
            required
            maxlength="6"
            pattern="[0-9]*"
            inputmode="numeric"
            autocomplete="one-time-code"
            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
      </div>

      <div>
        <button
          type="submit"
          :disabled="isLoading || code.length !== 6"
          class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
        >
          <span v-if="isLoading">Verifying...</span>
          <span v-else>Verify</span>
        </button>
      </div>

      <div class="mt-4 text-center">
        <button
          type="button"
          @click="handleLogout"
          class="text-sm text-gray-600 hover:text-indigo-500"
        >
          Cancel and return to login
        </button>
      </div>
    </form>
  </AuthLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { useAuthStore } from '@/stores/auth'
import AuthLayout from '@/layouts/AuthLayout.vue'

const router = useRouter()
const toast = useToast()
const auth = useAuthStore()

const code = ref('')
const isLoading = ref(false)

async function handleVerify() {
  try {
    isLoading.value = true
    const success = await auth.verify2FA(code.value)
    
    if (success) {
      toast.success('Successfully verified!')
      router.push('/dashboard')
    } else {
      toast.error('Invalid verification code')
    }
  } catch (error: any) {
    toast.error(error.response?.data?.message || 'Verification failed')
  } finally {
    isLoading.value = false
  }
}

function handleLogout() {
  auth.logout()
  router.push('/login')
}
</script>