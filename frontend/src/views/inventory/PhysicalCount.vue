<template>
  <div class="space-y-6 p-6">
    <header class="space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
            Physical Count - {{ store.name }}
          </h1>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Audit started {{ formatDate(audit.created_at) }}
          </p>
        </div>
        <div class="flex items-center space-x-4">
          <span
            class="inline-flex items-center rounded-full bg-green-100 px-3 py-0.5 text-sm font-medium text-green-800 dark:bg-green-900 dark:text-green-100"
          >
            {{ getProgressPercentage }}% Complete
          </span>
          <button
            v-if="uncountedProducts.length === 0"
            @click="completeAudit"
            class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
          >
            Complete Audit
          </button>
        </div>
      </div>

      <!-- Search and filters -->
      <div class="flex space-x-4">
        <div class="flex-1">
          <label for="search" class="sr-only">Search</label>
          <div class="relative">
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
            <input
              id="search"
              v-model="filters.search"
              class="block w-full rounded-lg border-gray-300 pl-10 focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
              placeholder="Search products..."
              type="search"
            />
          </div>
        </div>
        <select
          v-model="filters.category"
          class="rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
        >
          <option value="">All Categories</option>
          <option
            v-for="category in categories"
            :key="category.id"
            :value="category.id"
          >
            {{ category.name }}
          </option>
        </select>
        <select
          v-model="filters.countStatus"
          class="rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
        >
          <option value="all">All Products</option>
          <option value="counted">Counted</option>
          <option value="uncounted">Not Counted</option>
        </select>
      </div>

      <!-- Barcode scanner -->
      <div class="flex space-x-4">
        <div class="flex-1">
          <label for="barcode" class="sr-only">Scan Barcode</label>
          <input
            id="barcode"
            ref="barcodeInput"
            v-model="barcode"
            @keydown.enter="handleBarcodeScan"
            class="block w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
            placeholder="Scan barcode or enter manually..."
            type="text"
          />
        </div>
        <button
          @click="focusBarcode"
          class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
        >
          <svg
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M3 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 2V5h1v1H5zM3 13a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm2 2v-1h1v1H5zM13 3a1 1 0 00-1 1v3a1 1 0 001 1h3a1 1 0 001-1V4a1 1 0 00-1-1h-3zm1 2v1h1V5h-1z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>
    </header>

    <!-- Product list -->
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
      <ul class="divide-y divide-gray-200 dark:divide-gray-700">
        <li
          v-for="product in filteredProducts"
          :key="product.id"
          :class="[
            'p-4 sm:p-6',
            product.counted ? 'bg-green-50 dark:bg-green-900/20' : ''
          ]"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <img
                :src="product.image_url"
                :alt="product.name"
                class="h-16 w-16 rounded-lg object-cover"
              />
              <div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                  {{ product.name }}
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                  SKU: {{ product.sku }} | Barcode: {{ product.barcode }}
                </p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                  Category: {{ product.category.name }}
                </p>
              </div>
            </div>
            <div class="text-right">
              <p class="text-sm text-gray-900 dark:text-white">
                Expected: {{ product.theoretical_quantity }}
              </p>
              <div class="mt-2 flex items-center justify-end space-x-3">
                <input
                  v-if="!product.counted"
                  v-model="product.physical_quantity"
                  type="number"
                  min="0"
                  class="w-24 rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                  @keydown.enter="saveCount(product)"
                />
                <template v-else>
                  <span
                    :class="[
                      'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                      getDifferenceClass(product.difference_percentage)
                    ]"
                  >
                    {{ formatCount(product.physical_quantity) }}
                  </span>
                  <button
                    @click="editCount(product)"
                    class="text-sm font-medium text-green-600 hover:text-green-700 dark:text-green-500"
                  >
                    Edit
                  </button>
                </template>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>

    <!-- Completion confirmation modal -->
    <TransitionRoot appear :show="showCompletionModal" as="template">
      <Dialog as="div" @close="showCompletionModal = false" class="relative z-50">
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
                  Complete Inventory Audit
                </DialogTitle>

                <div class="mt-4">
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    Are you sure you want to complete this audit? This action cannot be
                    undone.
                  </p>

                  <div class="mt-4">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                      Summary
                    </h4>
                    <dl class="mt-2 space-y-2">
                      <div class="flex justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">
                          Total Products
                        </dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">
                          {{ totalProducts }}
                        </dd>
                      </div>
                      <div class="flex justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">
                          Products with Differences
                        </dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">
                          {{ productsWithDifferences }}
                        </dd>
                      </div>
                      <div class="flex justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">
                          Average Difference
                        </dt>
                        <dd
                          :class="[
                            'text-sm font-medium',
                            getDifferenceClass(averageDifference)
                          ]"
                        >
                          {{ formatDifference(averageDifference) }}
                        </dd>
                      </div>
                    </dl>
                  </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                  <button
                    type="button"
                    @click="showCompletionModal = false"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                  >
                    Cancel
                  </button>
                  <button
                    type="button"
                    @click="confirmCompletion"
                    class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                  >
                    Complete Audit
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
import { ref, computed, onMounted, nextTick } from 'vue'
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

const props = defineProps<{
  auditId: string
}>()

const router = useRouter()
const inventory = useInventoryStore()
const toast = useToast()

// State
const barcode = ref('')
const barcodeInput = ref<HTMLInputElement | null>(null)
const showCompletionModal = ref(false)
const filters = ref({
  search: '',
  category: '',
  countStatus: 'all'
})

// Computed
const audit = computed(() => inventory.getAudit(parseInt(props.auditId)))
const store = computed(() => audit.value.store)
const categories = computed(() => inventory.categories)

const products = computed(() => audit.value.products)
const filteredProducts = computed(() => {
  let filtered = [...products.value]

  if (filters.value.search) {
    const searchLower = filters.value.search.toLowerCase()
    filtered = filtered.filter(
      product =>
        product.name.toLowerCase().includes(searchLower) ||
        product.sku.toLowerCase().includes(searchLower) ||
        product.barcode.toLowerCase().includes(searchLower)
    )
  }

  if (filters.value.category) {
    filtered = filtered.filter(
      product => product.category.id === parseInt(filters.value.category)
    )
  }

  if (filters.value.countStatus !== 'all') {
    filtered = filtered.filter(product =>
      filters.value.countStatus === 'counted' ? product.counted : !product.counted
    )
  }

  return filtered
})

const uncountedProducts = computed(() =>
  products.value.filter(product => !product.counted)
)

const getProgressPercentage = computed(() =>
  Math.round(
    (products.value.filter(p => p.counted).length / products.value.length) * 100
  )
)

// For completion summary
const totalProducts = computed(() => products.value.length)
const productsWithDifferences = computed(() =>
  products.value.filter(p => p.difference_percentage > 0).length
)
const averageDifference = computed(() => {
  const differences = products.value
    .filter(p => p.counted)
    .map(p => p.difference_percentage)
  return differences.reduce((a, b) => a + b, 0) / differences.length || 0
})

// Methods
function formatDate(date: string) {
  return new Date(date).toLocaleDateString()
}

function formatCount(count: number) {
  return count.toLocaleString()
}

function getDifferenceClass(percentage: number) {
  if (percentage <= 1) return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
  if (percentage <= 3) return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
  return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
}

function formatDifference(percentage: number) {
  return `${percentage.toFixed(2)}% Difference`
}

function focusBarcode() {
  nextTick(() => {
    barcodeInput.value?.focus()
  })
}

async function handleBarcodeScan() {
  if (!barcode.value) return

  const product = products.value.find(p => p.barcode === barcode.value)
  if (product) {
    // Scroll product into view
    const element = document.getElementById(`product-${product.id}`)
    element?.scrollIntoView({ behavior: 'smooth', block: 'center' })

    // Focus quantity input if not counted
    if (!product.counted) {
      nextTick(() => {
        const input = document.getElementById(`quantity-${product.id}`)
        ;(input as HTMLInputElement)?.focus()
      })
    }
  } else {
    toast.error('Product not found')
  }

  barcode.value = ''
}

async function saveCount(product: any) {
  try {
    await inventory.saveAuditCount({
      audit_id: parseInt(props.auditId),
      product_id: product.id,
      physical_quantity: product.physical_quantity
    })
    toast.success('Count saved successfully')
  } catch (error: any) {
    toast.error(error.response?.data?.message || 'Failed to save count')
  }
}

function editCount(product: any) {
  product.counted = false
}

function completeAudit() {
  showCompletionModal.value = true
}

async function confirmCompletion() {
  try {
    await inventory.completeAudit(parseInt(props.auditId))
    toast.success('Audit completed successfully')
    router.push('/inventory/audit')
  } catch (error: any) {
    toast.error(error.response?.data?.message || 'Failed to complete audit')
  }
}

// Lifecycle
onMounted(() => {
  focusBarcode()
})
</script>