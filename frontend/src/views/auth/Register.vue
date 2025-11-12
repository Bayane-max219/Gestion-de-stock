<template>
  <AuthLayout>
    <template #title>Register new user</template>
    <template #subtitle>Admin access only</template>

    <form class="space-y-6" @submit.prevent="handleSubmit">
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700">
          Full name
        </label>
        <div class="mt-1">
          <input
            id="name"
            v-model="form.name"
            name="name"
            type="text"
            required
            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
      </div>

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">
          Email address
        </label>
        <div class="mt-1">
          <input
            id="email"
            v-model="form.email"
            name="email"
            type="email"
            autocomplete="email"
            required
            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">
          Password
        </label>
        <div class="mt-1">
          <input
            id="password"
            v-model="form.password"
            name="password"
            type="password"
            required
            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
      </div>

      <div>
        <label for="role" class="block text-sm font-medium text-gray-700">
          Role
        </label>
        <div class="mt-1">
          <select
            id="role"
            v-model="form.role"
            name="role"
            required
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
            <option value="">Select a role</option>
            <option value="manager">Store Manager</option>
            <option value="cashier">Cashier</option>
          </select>
        </div>
      </div>

      <div>
        <label for="store" class="block text-sm font-medium text-gray-700">
          Assigned Store
        </label>
        <div class="mt-1">
          <select
            id="store"
            v-model="form.store_id"
            name="store"
            required
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
            <option value="">Select a store</option>
            <option v-for="store in stores" :key="store.id" :value="store.id">
              {{ store.name }}
            </option>
          </select>
        </div>
      </div>

      <div>
        <button
          type="submit"
          :disabled="isLoading"
          class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
        >
          <span v-if="isLoading">Creating user...</span>
          <span v-else>Create user</span>
        </button>
      </div>
    </form>
  </AuthLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { useAuthStore } from '@/stores/auth'
import AuthLayout from '@/layouts/AuthLayout.vue'
import { api } from '@/utils/api'

interface Store {
  id: number
  name: string
}

const router = useRouter()
const toast = useToast()
const auth = useAuthStore()

const stores = ref<Store[]>([])
const isLoading = ref(false)
const form = ref({
  name: '',
  email: '',
  password: '',
  role: '',
  store_id: ''
})

onMounted(async () => {
  try {
    const response = await api.get('/stores')
    stores.value = response.data
  } catch (error: any) {
    toast.error('Failed to load stores')
  }
})

async function handleSubmit() {
  try {
    isLoading.value = true
    await auth.register(form.value)
    toast.success('User created successfully!')
    router.push('/users')
  } catch (error: any) {
    toast.error(error.response?.data?.message || 'Failed to create user')
  } finally {
    isLoading.value = false
  }
}
</script>