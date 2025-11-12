<template>
  <div class="space-y-6 p-6">
    <header class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
          Activity Logs
        </h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          Track and monitor system activities
        </p>
      </div>
      <button
        @click="exportLogs"
        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
      >
        <svg
          class="-ml-1 mr-2 h-5 w-5 text-gray-400"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            d="M3 3a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2H3zm0 2h14v10H3V5zm2 3a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm0 3a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm0 3a1 1 0 011-1h4a1 1 0 110 2H6a1 1 0 01-1-1z"
          />
        </svg>
        Export to Excel
      </button>
    </header>

    <!-- Filters -->
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Date Range -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Date Range
          </label>
          <div class="mt-1 flex space-x-2">
            <input
              type="date"
              v-model="filters.start_date"
              class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
            />
            <input
              type="date"
              v-model="filters.end_date"
              class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
            />
          </div>
        </div>

        <!-- Action Type -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Action
          </label>
          <select
            v-model="filters.action"
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
          >
            <option value="">All Actions</option>
            <option v-for="action in actions" :key="action.value" :value="action.value">
              {{ action.label }}
            </option>
          </select>
        </div>

        <!-- Entity Type -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Entity Type
          </label>
          <select
            v-model="filters.entity_type"
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
          >
            <option value="">All Types</option>
            <option
              v-for="type in entityTypes"
              :key="type.value"
              :value="type.value"
            >
              {{ type.label }}
            </option>
          </select>
        </div>

        <!-- Search -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Search
          </label>
          <div class="relative mt-1">
            <input
              type="text"
              v-model="filters.search"
              class="block w-full rounded-lg border-gray-300 pl-10 focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
              placeholder="Search logs..."
            />
            <div
              class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
            >
              <svg
                class="h-5 w-5 text-gray-400"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Activity List -->
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead>
            <tr class="bg-gray-50 dark:bg-gray-700">
              <th
                v-for="header in headers"
                :key="header.value"
                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400"
              >
                {{ header.text }}
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
            <tr v-for="log in logs.data" :key="log.id">
              <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                {{ formatDate(log.created_at) }}
              </td>
              <td class="whitespace-nowrap px-6 py-4">
                <span
                  :class="[
                    'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                    getActionClass(log.action)
                  ]"
                >
                  {{ capitalize(log.action) }}
                </span>
              </td>
              <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                {{ getEntityName(log.entity_type) }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                {{ log.description }}
              </td>
              <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                {{ log.user?.name ?? 'System' }}
              </td>
              <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                <button
                  @click="showDetails(log)"
                  class="text-green-600 hover:text-green-700 dark:text-green-500"
                >
                  View Details
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 dark:border-gray-700 dark:bg-gray-800 sm:px-6"
      >
        <div class="flex items-center">
          <label class="mr-2 text-sm text-gray-700 dark:text-gray-300">
            Show per page
          </label>
          <select
            v-model="filters.per_page"
            class="rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
          >
            <option v-for="n in [10, 25, 50, 100]" :key="n" :value="n">
              {{ n }}
            </option>
          </select>
        </div>
        <div class="flex items-center space-x-2">
          <button
            :disabled="!logs.prev_page_url"
            @click="getPage(logs.current_page - 1)"
            class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
          >
            Previous
          </button>
          <span class="text-sm text-gray-700 dark:text-gray-300">
            Page {{ logs.current_page }} of {{ logs.last_page }}
          </span>
          <button
            :disabled="!logs.next_page_url"
            @click="getPage(logs.current_page + 1)"
            class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Details Modal -->
    <TransitionRoot appear :show="!!selectedLog" as="template">
      <Dialog as="div" @close="selectedLog = null" class="relative z-50">
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
                v-if="selectedLog"
                class="w-full max-w-2xl transform overflow-hidden rounded-xl bg-white p-6 shadow-xl transition-all dark:bg-gray-800"
              >
                <DialogTitle
                  as="h3"
                  class="text-lg font-medium leading-6 text-gray-900 dark:text-white"
                >
                  Activity Details
                </DialogTitle>

                <div class="mt-4">
                  <dl class="grid grid-cols-2 gap-x-4 gap-y-6">
                    <div>
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Timestamp
                      </dt>
                      <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ formatDate(selectedLog.created_at) }}
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Action
                      </dt>
                      <dd class="mt-1">
                        <span
                          :class="[
                            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                            getActionClass(selectedLog.action)
                          ]"
                        >
                          {{ capitalize(selectedLog.action) }}
                        </span>
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Entity Type
                      </dt>
                      <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ getEntityName(selectedLog.entity_type) }}
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        User
                      </dt>
                      <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ selectedLog.user?.name ?? 'System' }}
                      </dd>
                    </div>
                    <div class="col-span-2">
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Description
                      </dt>
                      <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ selectedLog.description }}
                      </dd>
                    </div>
                    <div class="col-span-2">
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Changes
                      </dt>
                      <dd class="mt-2 space-y-2">
                        <div
                          v-if="selectedLog.old_values"
                          class="rounded-lg bg-red-50 p-3 dark:bg-red-900/20"
                        >
                          <h4 class="text-sm font-medium text-red-800 dark:text-red-300">
                            Old Values
                          </h4>
                          <pre
                            class="mt-1 whitespace-pre-wrap text-sm text-red-700 dark:text-red-200"
                          >{{ JSON.stringify(selectedLog.old_values, null, 2) }}</pre>
                        </div>
                        <div
                          v-if="selectedLog.new_values"
                          class="rounded-lg bg-green-50 p-3 dark:bg-green-900/20"
                        >
                          <h4
                            class="text-sm font-medium text-green-800 dark:text-green-300"
                          >
                            New Values
                          </h4>
                          <pre
                            class="mt-1 whitespace-pre-wrap text-sm text-green-700 dark:text-green-200"
                          >{{ JSON.stringify(selectedLog.new_values, null, 2) }}</pre>
                        </div>
                      </dd>
                    </div>
                  </dl>
                </div>

                <div class="mt-6 flex justify-end">
                  <button
                    type="button"
                    @click="selectedLog = null"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                  >
                    Close
                  </button>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, watch } from 'vue'
import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle
} from '@headlessui/vue'
import axios from 'axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

// State
const logs = ref({
  data: [],
  current_page: 1,
  last_page: 1,
  prev_page_url: null,
  next_page_url: null
})
const filters = reactive({
  search: '',
  action: '',
  entity_type: '',
  start_date: '',
  end_date: '',
  per_page: 25
})
const actions = ref([])
const entityTypes = ref([])
const selectedLog = ref(null)

// Table headers
const headers = [
  { text: 'Timestamp', value: 'created_at' },
  { text: 'Action', value: 'action' },
  { text: 'Entity Type', value: 'entity_type' },
  { text: 'Description', value: 'description' },
  { text: 'User', value: 'user' },
  { text: 'Actions', value: 'actions' }
]

// Methods
function formatDate(date: string) {
  return new Date(date).toLocaleString()
}

function capitalize(str: string) {
  return str.charAt(0).toUpperCase() + str.slice(1)
}

function getEntityName(type: string) {
  return type.split('\\').pop()
}

function getActionClass(action: string) {
  switch (action) {
    case 'create':
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
    case 'update':
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
    case 'delete':
      return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
  }
}

async function fetchLogs(page = 1) {
  try {
    const response = await axios.get('/api/activity-logs', {
      params: {
        ...filters,
        page
      }
    })
    logs.value = response.data
  } catch (error: any) {
    toast.error('Failed to fetch activity logs')
  }
}

async function fetchOptions() {
  try {
    const [actionsResponse, typesResponse] = await Promise.all([
      axios.get('/api/activity-logs/actions'),
      axios.get('/api/activity-logs/entity-types')
    ])
    actions.value = actionsResponse.data
    entityTypes.value = typesResponse.data
  } catch (error: any) {
    toast.error('Failed to fetch filter options')
  }
}

function getPage(page: number) {
  fetchLogs(page)
}

function showDetails(log: any) {
  selectedLog.value = log
}

async function exportLogs() {
  try {
    const response = await axios.get('/api/activity-logs/export', {
      params: filters,
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'activity-logs.xlsx')
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    
    toast.success('Export completed successfully')
  } catch (error: any) {
    toast.error('Failed to export activity logs')
  }
}

// Watch for filter changes
watch(
  filters,
  () => {
    fetchLogs(1)
  },
  { deep: true }
)

// Lifecycle
onMounted(() => {
  fetchOptions()
  fetchLogs()
})
</script>