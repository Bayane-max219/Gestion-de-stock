<template>
  <TransitionRoot appear :show="true" as="template">
    <Dialog as="div" @close="$emit('close')" class="relative z-50">
      <TransitionChild
        as="template"
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black bg-opacity-25" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel class="w-full max-w-2xl transform overflow-hidden rounded-xl bg-white dark:bg-gray-800 p-6 shadow-xl transition-all">
              <DialogTitle
                as="h3"
                class="text-lg font-medium leading-6 text-gray-900 dark:text-white"
              >
                Select Customer
              </DialogTitle>

              <!-- Search -->
              <div class="mt-4">
                <input
                  type="text"
                  v-model="searchQuery"
                  placeholder="Search customers..."
                  class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:ring-2 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                  @input="searchCustomers"
                />
              </div>

              <!-- Customer list -->
              <div class="mt-4 max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                      <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300"
                      >
                        Name
                      </th>
                      <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300"
                      >
                        Contact
                      </th>
                      <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr
                      v-for="customer in filteredCustomers"
                      :key="customer.id"
                      class="hover:bg-gray-50 dark:hover:bg-gray-700"
                    >
                      <td class="whitespace-nowrap px-6 py-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                          {{ customer.name }}
                        </div>
                      </td>
                      <td class="whitespace-nowrap px-6 py-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                          {{ customer.email }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                          {{ customer.phone }}
                        </div>
                      </td>
                      <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                        <button
                          @click="$emit('select', customer)"
                          class="font-medium text-green-600 hover:text-green-500 dark:text-green-500 dark:hover:text-green-400"
                        >
                          Select
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div class="mt-6 flex justify-end gap-3">
                <button
                  @click="$emit('close')"
                  class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                >
                  Cancel
                </button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle
} from '@headlessui/vue'
import { api } from '@/utils/api'
import type { Customer } from '@/stores/pos'

// Props & Emits
defineEmits<{
  (e: 'close'): void
  (e: 'select', customer: Customer): void
}>()

// State
const searchQuery = ref('')
const customers = ref<Customer[]>([])

// Computed
const filteredCustomers = computed(() => {
  if (!searchQuery.value) return customers.value
  
  const query = searchQuery.value.toLowerCase()
  return customers.value.filter(
    customer =>
      customer.name.toLowerCase().includes(query) ||
      customer.email.toLowerCase().includes(query) ||
      customer.phone.includes(query)
  )
})

// Methods
async function searchCustomers() {
  if (searchQuery.value.length >= 2) {
    try {
      const response = await api.get('/customers/search', {
        params: { query: searchQuery.value }
      })
      customers.value = response.data
    } catch (error) {
      console.error('Failed to search customers:', error)
    }
  }
}

// Initial load
searchCustomers()
</script>