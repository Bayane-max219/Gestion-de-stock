<template>
  <AuthLayout>
    <template #title>Reset your password</template>
    <template #subtitle>
      <RouterLink
        to="/login"
        class="font-medium text-indigo-600 hover:text-indigo-500"
      >
        Return to login
      </RouterLink>
    </template>

    <div v-if="!emailSent">
      <form class="space-y-6" @submit.prevent="handleSubmit">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">
            Email address
          </label>
          <div class="mt-1">
            <input
              id="email"
              v-model="email"
              name="email"
              type="email"
              autocomplete="email"
              required
              class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            />
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="isLoading"
            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          >
            <span v-if="isLoading">Sending...</span>
            <span v-else>Send reset instructions</span>
          </button>
        </div>
      </form>
    </div>

    <div v-else class="text-center">
      <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
        <CheckCircleIcon class="h-6 w-6 text-green-600" aria-hidden="true" />
      </div>
      <h3 class="text-sm font-medium text-gray-900">Reset instructions sent</h3>
      <p class="mt-2 text-sm text-gray-500">
        We've sent password reset instructions to {{ email }}. Please check your email.
      </p>
      <div class="mt-6">
        <RouterLink
          to="/login"
          class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
        >
          Return to login
        </RouterLink>
      </div>
    </div>
  </AuthLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useToast } from 'vue-toastification'
import { CheckCircleIcon } from '@heroicons/vue/24/outline'
import { useAuthStore } from '@/stores/auth'
import AuthLayout from '@/layouts/AuthLayout.vue'

const toast = useToast()
const auth = useAuthStore()

const email = ref('')
const isLoading = ref(false)
const emailSent = ref(false)

async function handleSubmit() {
  try {
    isLoading.value = true
    await auth.forgotPassword(email.value)
    emailSent.value = true
  } catch (error: any) {
    toast.error(error.response?.data?.message || 'Failed to send reset instructions')
  } finally {
    isLoading.value = false
  }
}
</script>