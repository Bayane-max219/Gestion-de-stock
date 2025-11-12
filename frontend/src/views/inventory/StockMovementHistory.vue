<template>
  <div class="space-y-6 p-6">
    <header class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
          Stock Movement History
        </h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          Track and analyze all stock movements across stores
        </p>
      </div>
      <button
        @click="exportToExcel"
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
              v-model="filters.startDate"
              class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
            />
            <input
              type="date"
              v-model="filters.endDate"
              class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
            />
          </div>
        </div>

        <!-- Store -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Store
          </label>
          <select
            v-model="filters.store"
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
          >
            <option value="">All Stores</option>
            <option v-for="store in stores" :key="store.id" :value="store.id">
              {{ store.name }}
            </option>
          </select>
        </div>

        <!-- Movement Type -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Movement Type
          </label>
          <select
            v-model="filters.type"
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
          >
            <option value="">All Types</option>
            <option value="entry">Stock Entry</option>
            <option value="sale">Sale</option>
            <option value="purchase">Purchase</option>
            <option value="transfer">Transfer</option>
            <option value="adjustment">Adjustment</option>
            <option value="audit">Inventory Audit</option>
          </select>
        </div>

        <!-- Product Search -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Product
          </label>
          <div class="relative mt-1">
            <input
              type="text"
              v-model="filters.search"
              class="block w-full rounded-lg border-gray-300 pl-10 focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
              placeholder="Search by name, SKU, barcode..."
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

    <!-- Movement List -->
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead>
            <tr class="bg-gray-50 dark:bg-gray-700">
              <th
                v-for="header in tableHeaders"
                :key="header.value"
                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400"
                :class="{ 'text-right': header.align === 'right' }"
              >
                <button
                  v-if="header.sortable"
                  @click="sort(header.value)"
                  class="inline-flex items-center space-x-1"
                >
                  <span>{{ header.text }}</span>
                  <svg
                    v-if="sortBy === header.value"
                    class="h-4 w-4"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      v-if="sortOrder === 'asc'"
                      fill-rule="evenodd"
                      d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 13.586l3.293-3.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                    />
                    <path
                      v-else
                      fill-rule="evenodd"
                      d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 6.414 6.707 9.707a1 1 0 01-1.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
                <span v-else>{{ header.text }}</span>
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
            <tr v-for="movement in paginatedMovements" :key="movement.id">
              <td class="whitespace-nowrap px-6 py-4">
                <div class="flex items-center">
                  <div class="h-10 w-10 flex-shrink-0">
                    <img
                      :src="movement.product.image_url"
                      :alt="movement.product.name"
                      class="h-10 w-10 rounded-full object-cover"
                    />
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ movement.product.name }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      SKU: {{ movement.product.sku }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="whitespace-nowrap px-6 py-4">
                <span
                  :class="[
                    'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                    getTypeClass(movement.type)
                  ]"
                >
                  {{ formatType(movement.type) }}
                </span>
              </td>
              <td class="whitespace-nowrap px-6 py-4">
                {{ movement.store.name }}
              </td>
              <td class="whitespace-nowrap px-6 py-4">
                <span
                  :class="[
                    movement.quantity > 0
                      ? 'text-green-600 dark:text-green-400'
                      : 'text-red-600 dark:text-red-400'
                  ]"
                >
                  {{ formatQuantity(movement.quantity) }}
                </span>
              </td>
              <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                {{ formatDateTime(movement.created_at) }}
              </td>
              <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                {{ movement.user.name }}
              </td>
              <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                <button
                  @click="showDetails(movement)"
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
            v-model="perPage"
            class="rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
          >
            <option v-for="n in [10, 25, 50, 100]" :key="n" :value="n">
              {{ n }}
            </option>
          </select>
        </div>
        <div class="flex items-center space-x-2">
          <button
            :disabled="currentPage === 1"
            @click="currentPage--"
            class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
          >
            Previous
          </button>
          <span class="text-sm text-gray-700 dark:text-gray-300">
            Page {{ currentPage }} of {{ totalPages }}
          </span>
          <button
            :disabled="currentPage === totalPages"
            @click="currentPage++"
            class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Movement Details Modal -->
    <TransitionRoot appear :show="!!selectedMovement" as="template">
      <Dialog as="div" @close="selectedMovement = null" class="relative z-50">
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
                v-if="selectedMovement"
                class="w-full max-w-2xl transform overflow-hidden rounded-xl bg-white p-6 shadow-xl transition-all dark:bg-gray-800"
              >
                <DialogTitle
                  as="h3"
                  class="text-lg font-medium leading-6 text-gray-900 dark:text-white"
                >
                  Movement Details
                </DialogTitle>

                <div class="mt-4">
                  <dl class="grid grid-cols-2 gap-x-4 gap-y-6">
                    <div>
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Reference
                      </dt>
                      <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ selectedMovement.reference }}
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Type
                      </dt>
                      <dd class="mt-1">
                        <span
                          :class="[
                            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                            getTypeClass(selectedMovement.type)
                          ]"
                        >
                          {{ formatType(selectedMovement.type) }}
                        </span>
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Product
                      </dt>
                      <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ selectedMovement.product.name }}
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Store
                      </dt>
                      <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ selectedMovement.store.name }}
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Quantity
                      </dt>
                      <dd
                        class="mt-1 text-sm"
                        :class="[
                          selectedMovement.quantity > 0
                            ? 'text-green-600 dark:text-green-400'
                            : 'text-red-600 dark:text-red-400'
                        ]"
                      >
                        {{ formatQuantity(selectedMovement.quantity) }}
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Date & Time
                      </dt>
                      <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ formatDateTime(selectedMovement.created_at) }}
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        User
                      </dt>
                      <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ selectedMovement.user.name }}
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Status
                      </dt>
                      <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ selectedMovement.status }}
                      </dd>
                    </div>
                    <div class="col-span-2">
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Notes
                      </dt>
                      <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ selectedMovement.notes || 'No notes provided' }}
                      </dd>
                    </div>
                  </dl>
                </div>

                <div class="mt-6 flex justify-end">
                  <button
                    type="button"
                    @click="selectedMovement = null"
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
import { ref, computed } from 'vue'
import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle
} from '@headlessui/vue'
import { useInventoryStore } from '@/stores/inventory'
import { useToast } from 'vue-toastification'

const inventory = useInventoryStore()
const toast = useToast()

// State
const filters = ref({
  search: '',
  store: '',
  type: '',
  startDate: '',
  endDate: ''
})
const sortBy = ref('created_at')
const sortOrder = ref<'asc' | 'desc'>('desc')
const currentPage = ref(1)
const perPage = ref(25)
const selectedMovement = ref(null)

// Table headers
const tableHeaders = [
  { text: 'Product', value: 'product', sortable: true },
  { text: 'Type', value: 'type', sortable: true },
  { text: 'Store', value: 'store', sortable: true },
  { text: 'Quantity', value: 'quantity', sortable: true },
  { text: 'Date & Time', value: 'created_at', sortable: true },
  { text: 'User', value: 'user', sortable: true },
  { text: 'Actions', value: 'actions', sortable: false, align: 'right' }
]

// Computed
const stores = computed(() => inventory.stores)

const filteredMovements = computed(() => {
  let filtered = inventory.movements

  if (filters.value.search) {
    const searchLower = filters.value.search.toLowerCase()
    filtered = filtered.filter(
      movement =>
        movement.product.name.toLowerCase().includes(searchLower) ||
        movement.product.sku.toLowerCase().includes(searchLower) ||
        movement.product.barcode.toLowerCase().includes(searchLower)
    )
  }

  if (filters.value.store) {
    filtered = filtered.filter(
      movement => movement.store.id === parseInt(filters.value.store)
    )
  }

  if (filters.value.type) {
    filtered = filtered.filter(movement => movement.type === filters.value.type)
  }

  if (filters.value.startDate) {
    filtered = filtered.filter(
      movement => movement.created_at >= filters.value.startDate
    )
  }

  if (filters.value.endDate) {
    filtered = filtered.filter(
      movement => movement.created_at <= filters.value.endDate
    )
  }

  // Sort
  filtered.sort((a, b) => {
    let comparison = 0
    if (typeof a[sortBy.value] === 'string') {
      comparison = a[sortBy.value].localeCompare(b[sortBy.value])
    } else {
      comparison = a[sortBy.value] - b[sortBy.value]
    }
    return sortOrder.value === 'desc' ? -comparison : comparison
  })

  return filtered
})

const totalPages = computed(() =>
  Math.ceil(filteredMovements.value.length / perPage.value)
)

const paginatedMovements = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  const end = start + perPage.value
  return filteredMovements.value.slice(start, end)
})

// Methods
function formatDateTime(datetime: string) {
  return new Date(datetime).toLocaleString()
}

function formatQuantity(quantity: number) {
  const prefix = quantity > 0 ? '+' : ''
  return `${prefix}${quantity.toLocaleString()}`
}

function formatType(type: string) {
  return type.charAt(0).toUpperCase() + type.slice(1)
}

function getTypeClass(type: string) {
  switch (type) {
    case 'entry':
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
    case 'sale':
      return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
    case 'purchase':
      return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
    case 'transfer':
      return 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300'
    case 'adjustment':
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
    case 'audit':
      return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
  }
}

function sort(column: string) {
  if (sortBy.value === column) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = column
    sortOrder.value = 'asc'
  }
}

function showDetails(movement: any) {
  selectedMovement.value = movement
}

async function exportToExcel() {
  try {
    await inventory.exportMovements({
      ...filters.value,
      sortBy: sortBy.value,
      sortOrder: sortOrder.value
    })
    toast.success('Export started. You will be notified when it\'s ready.')
  } catch (error: any) {
    toast.error(error.response?.data?.message || 'Failed to start export')
  }
}
</script>