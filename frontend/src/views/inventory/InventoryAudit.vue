<template>
  <div class="space-y-6 p-6">
    <header class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
          Inventory Audit
        </h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          Manage physical inventory counts and reconciliation
        </p>
      </div>
      <button
        @click="startNewAudit"
        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
      >
        Start New Audit
      </button>
    </header>

    <!-- Active Audits -->
    <section
      v-if="activeAudits.length > 0"
      class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
    >
      <div class="border-b border-gray-200 px-4 py-5 dark:border-gray-700 sm:px-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Active Audits</h2>
      </div>
      <ul class="divide-y divide-gray-200 dark:divide-gray-700">
        <li
          v-for="audit in activeAudits"
          :key="audit.id"
          class="px-4 py-5 sm:px-6"
        >
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                {{ audit.store.name }}
              </h3>
              <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Started {{ formatDate(audit.created_at) }} by {{ audit.initiated_by.name }}
              </p>
            </div>
            <div class="flex space-x-3">
              <button
                @click="continueAudit(audit)"
                class="rounded-lg bg-green-100 px-4 py-2 text-sm font-medium text-green-700 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:bg-green-900 dark:text-green-100"
              >
                Continue
              </button>
              <button
                @click="reviewAudit(audit)"
                class="rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-600"
              >
                Review
              </button>
            </div>
          </div>
          <div class="mt-4">
            <div class="flex items-center space-x-2">
              <div class="flex-1">
                <div class="h-2 rounded-full bg-gray-200 dark:bg-gray-700">
                  <div
                    class="h-2 rounded-full bg-green-600"
                    :style="{ width: `${getProgressPercentage(audit)}%` }"
                  ></div>
                </div>
              </div>
              <span class="text-sm text-gray-600 dark:text-gray-400">
                {{ getProgressPercentage(audit) }}% Complete
              </span>
            </div>
          </div>
        </li>
      </ul>
    </section>

    <!-- Completed Audits -->
    <section class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
      <div class="border-b border-gray-200 px-4 py-5 dark:border-gray-700 sm:px-6">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-medium text-gray-900 dark:text-white">
            Completed Audits
          </h2>
          <div class="flex items-center space-x-4">
            <input
              type="date"
              v-model="filters.date"
              class="rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
            />
            <select
              v-model="filters.store"
              class="rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
            >
              <option value="">All Stores</option>
              <option v-for="store in stores" :key="store.id" :value="store.id">
                {{ store.name }}
              </option>
            </select>
          </div>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead>
            <tr class="bg-gray-50 dark:bg-gray-700">
              <th
                v-for="header in tableHeaders"
                :key="header.value"
                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400"
              >
                {{ header.text }}
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
            <tr v-for="audit in completedAudits" :key="audit.id">
              <td class="whitespace-nowrap px-6 py-4">
                {{ formatDate(audit.completed_at) }}
              </td>
              <td class="whitespace-nowrap px-6 py-4">
                {{ audit.store.name }}
              </td>
              <td class="whitespace-nowrap px-6 py-4">
                {{ audit.initiated_by.name }}
              </td>
              <td class="whitespace-nowrap px-6 py-4">
                {{ audit.completed_by.name }}
              </td>
              <td class="whitespace-nowrap px-6 py-4">
                <span
                  :class="[
                    'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                    getDifferenceClass(audit.difference_percentage)
                  ]"
                >
                  {{ formatDifference(audit.difference_percentage) }}
                </span>
              </td>
              <td class="whitespace-nowrap px-6 py-4 text-right">
                <button
                  @click="downloadReport(audit)"
                  class="text-sm font-medium text-green-600 hover:text-green-700 dark:text-green-500"
                >
                  Download Report
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- New Audit Modal -->
    <TransitionRoot appear :show="showNewAuditModal" as="template">
      <Dialog as="div" @close="showNewAuditModal = false" class="relative z-50">
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
              <DialogPanel
                class="w-full max-w-md transform overflow-hidden rounded-xl bg-white p-6 shadow-xl transition-all dark:bg-gray-800"
              >
                <DialogTitle
                  as="h3"
                  class="text-lg font-medium leading-6 text-gray-900 dark:text-white"
                >
                  Start New Audit
                </DialogTitle>

                <form @submit.prevent="createAudit" class="mt-4">
                  <div class="space-y-4">
                    <div>
                      <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                      >
                        Store
                      </label>
                      <select
                        v-model="newAudit.store_id"
                        required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                      >
                        <option value="">Select store</option>
                        <option v-for="store in stores" :key="store.id" :value="store.id">
                          {{ store.name }}
                        </option>
                      </select>
                    </div>
                    <div>
                      <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                      >
                        Notes
                      </label>
                      <textarea
                        v-model="newAudit.notes"
                        rows="3"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                      ></textarea>
                    </div>
                  </div>

                  <div class="mt-6 flex justify-end space-x-3">
                    <button
                      type="button"
                      @click="showNewAuditModal = false"
                      class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                    >
                      Cancel
                    </button>
                    <button
                      type="submit"
                      class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                    >
                      Start Audit
                    </button>
                  </div>
                </form>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle
} from '@headlessui/vue'
import { useInventoryStore } from '@/stores/inventory'
import { useToast } from 'vue-toastification'

const router = useRouter()
const inventory = useInventoryStore()
const toast = useToast()

// State
const showNewAuditModal = ref(false)
const filters = ref({
  date: '',
  store: ''
})
const newAudit = ref({
  store_id: '',
  notes: ''
})

// Computed
const stores = computed(() => inventory.stores)
const activeAudits = computed(() => inventory.activeAudits)
const completedAudits = computed(() => {
  let filtered = inventory.completedAudits
  if (filters.value.date) {
    filtered = filtered.filter(
      audit => audit.completed_at.startsWith(filters.value.date)
    )
  }
  if (filters.value.store) {
    filtered = filtered.filter(
      audit => audit.store.id === parseInt(filters.value.store)
    )
  }
  return filtered
})

const tableHeaders = [
  { text: 'Date', value: 'date' },
  { text: 'Store', value: 'store' },
  { text: 'Started By', value: 'initiated_by' },
  { text: 'Completed By', value: 'completed_by' },
  { text: 'Difference', value: 'difference' },
  { text: 'Actions', value: 'actions' }
]

// Methods
function formatDate(date: string) {
  return new Date(date).toLocaleDateString()
}

function getProgressPercentage(audit: any) {
  return Math.round((audit.counted_items / audit.total_items) * 100)
}

function getDifferenceClass(percentage: number) {
  if (percentage <= 1) return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
  if (percentage <= 3) return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
  return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
}

function formatDifference(percentage: number) {
  return `${percentage.toFixed(2)}% Difference`
}

function startNewAudit() {
  showNewAuditModal.value = true
}

async function createAudit() {
  try {
    const audit = await inventory.createInventoryAudit({
      store_id: parseInt(newAudit.value.store_id),
      notes: newAudit.value.notes
    })
    showNewAuditModal.value = false
    router.push(`/inventory/audit/${audit.id}`)
  } catch (error: any) {
    toast.error(error.response?.data?.message || 'Failed to create audit')
  }
}

function continueAudit(audit: any) {
  router.push(`/inventory/audit/${audit.id}`)
}

function reviewAudit(audit: any) {
  router.push(`/inventory/audit/${audit.id}/review`)
}

async function downloadReport(audit: any) {
  try {
    await inventory.downloadAuditReport(audit.id)
    toast.success('Report downloaded successfully')
  } catch (error: any) {
    toast.error(error.response?.data?.message || 'Failed to download report')
  }
}
</script>