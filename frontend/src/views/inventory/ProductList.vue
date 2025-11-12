<template>
  <div class="space-y-6">
    <!-- Page header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
          Products
        </h1>
        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
          Manage your inventory products and stock levels
        </p>
      </div>
      <div>
        <button
          @click="showCreateModal = true"
          class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
        >
          <PlusIcon class="mr-2 h-5 w-5" />
          Add Product
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <!-- Store filter -->
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Store
        </label>
        <select
          v-model="filters.store_id"
          class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
        >
          <option :value="null">All Stores</option>
          <option v-for="store in stores" :key="store.id" :value="store.id">
            {{ store.name }}
          </option>
        </select>
      </div>

      <!-- Category filter -->
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Category
        </label>
        <select
          v-model="filters.category_id"
          class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
        >
          <option :value="null">All Categories</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
      </div>

      <!-- Search -->
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Search
        </label>
        <div class="mt-1 relative rounded-lg shadow-sm">
          <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
          </div>
          <input
            type="text"
            v-model="filters.search"
            class="block w-full pl-10 rounded-lg border border-gray-300 focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
            placeholder="Search by name, reference or barcode..."
          />
        </div>
      </div>
    </div>

    <!-- Products table -->
    <div class="overflow-hidden bg-white shadow-sm ring-1 ring-black ring-opacity-5 dark:bg-gray-800 sm:rounded-lg">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th
                v-for="column in columns"
                :key="column.key"
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300"
              >
                <div class="flex items-center space-x-1">
                  <span>{{ column.label }}</span>
                  <button
                    v-if="column.sortable"
                    @click="toggleSort(column.key)"
                    class="group inline-flex"
                  >
                    <ChevronUpDownIcon
                      class="h-4 w-4 text-gray-400 group-hover:text-gray-500"
                    />
                  </button>
                </div>
              </th>
              <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">Actions</span>
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr
              v-for="product in sortedProducts"
              :key="product.id"
              class="hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              <!-- Image -->
              <td class="whitespace-nowrap px-6 py-4">
                <div class="h-10 w-10">
                  <img
                    :src="product.image_url || '/placeholder.png'"
                    :alt="product.name"
                    class="h-10 w-10 rounded-lg object-cover"
                  />
                </div>
              </td>
              <!-- Name -->
              <td class="whitespace-nowrap px-6 py-4">
                <div class="text-sm font-medium text-gray-900 dark:text-white">
                  {{ product.name }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                  {{ product.reference }}
                </div>
              </td>
              <!-- Category -->
              <td class="whitespace-nowrap px-6 py-4">
                <div class="text-sm text-gray-900 dark:text-white">
                  {{ getCategoryName(product.category_id) }}
                </div>
                <div
                  v-if="product.subcategory_id"
                  class="text-sm text-gray-500 dark:text-gray-400"
                >
                  {{ getCategoryName(product.subcategory_id) }}
                </div>
              </td>
              <!-- Stock -->
              <td class="whitespace-nowrap px-6 py-4">
                <div class="text-sm text-gray-900 dark:text-white">
                  {{ getStockQuantity(product.id, filters.store_id) }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                  Min: {{ product.min_stock }}
                </div>
              </td>
              <!-- Prices -->
              <td class="whitespace-nowrap px-6 py-4">
                <div class="text-sm font-medium text-gray-900 dark:text-white">
                  ${{ product.selling_price.toFixed(2) }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                  Cost: ${{ product.cost_price.toFixed(2) }}
                </div>
              </td>
              <!-- Actions -->
              <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                <div class="flex justify-end space-x-3">
                  <button
                    @click="showStockModal(product)"
                    class="text-green-600 hover:text-green-700 dark:text-green-500 dark:hover:text-green-400"
                  >
                    <ArrowPathIcon class="h-5 w-5" />
                  </button>
                  <button
                    @click="editProduct(product)"
                    class="text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                  >
                    <PencilSquareIcon class="h-5 w-5" />
                  </button>
                  <button
                    @click="deleteProduct(product)"
                    class="text-red-600 hover:text-red-700 dark:text-red-500 dark:hover:text-red-400"
                  >
                    <TrashIcon class="h-5 w-5" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Product form modal -->
    <ProductFormModal
      v-if="showCreateModal"
      :product="selectedProduct"
      @close="closeProductModal"
      @save="handleSaveProduct"
    />

    <!-- Stock operations modal -->
    <StockOperationsModal
      v-if="showStockModal"
      :product="selectedProduct"
      @close="selectedProduct = null"
      @refresh="fetchProducts"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useInventoryStore } from '@/stores/inventory'
import {
  PlusIcon,
  MagnifyingGlassIcon,
  ChevronUpDownIcon,
  ArrowPathIcon,
  PencilSquareIcon,
  TrashIcon
} from '@heroicons/vue/24/outline'
import type { Product } from '@/stores/inventory'
import { useToast } from 'vue-toastification'
import ProductFormModal from '@/components/inventory/ProductFormModal.vue'
import StockOperationsModal from '@/components/inventory/StockOperationsModal.vue'

const toast = useToast()
const inventory = useInventoryStore()

// State
const filters = ref({
  store_id: null as number | null,
  category_id: null as number | null,
  search: ''
})
const sortBy = ref<{ key: string; direction: 'asc' | 'desc' } | null>(null)
const showCreateModal = ref(false)
const selectedProduct = ref<Product | null>(null)

// Computed
const stores = computed(() => inventory.stores)
const categories = computed(() => inventory.categories)
const products = computed(() => inventory.products)
const loading = computed(() => inventory.loading)

const columns = [
  { key: 'image', label: '', sortable: false },
  { key: 'name', label: 'Product', sortable: true },
  { key: 'category', label: 'Category', sortable: true },
  { key: 'stock', label: 'Stock', sortable: true },
  { key: 'price', label: 'Price', sortable: true }
]

const sortedProducts = computed(() => {
  let result = [...products.value]

  // Apply filters
  if (filters.value.search) {
    const search = filters.value.search.toLowerCase()
    result = result.filter(
      product =>
        product.name.toLowerCase().includes(search) ||
        product.reference.toLowerCase().includes(search) ||
        product.barcode.toLowerCase().includes(search)
    )
  }

  if (filters.value.category_id) {
    result = result.filter(
      product =>
        product.category_id === filters.value.category_id ||
        product.subcategory_id === filters.value.category_id
    )
  }

  // Apply sorting
  if (sortBy.value) {
    result.sort((a, b) => {
      let aVal: any = a[sortBy.value!.key as keyof Product]
      let bVal: any = b[sortBy.value!.key as keyof Product]

      if (sortBy.value!.key === 'stock') {
        aVal = inventory.getStockQuantity(a.id, filters.value.store_id ?? 0)
        bVal = inventory.getStockQuantity(b.id, filters.value.store_id ?? 0)
      }

      if (sortBy.value!.direction === 'desc') {
        ;[aVal, bVal] = [bVal, aVal]
      }

      return aVal < bVal ? -1 : aVal > bVal ? 1 : 0
    })
  }

  return result
})

// Methods
function toggleSort(key: string) {
  if (sortBy.value?.key === key) {
    if (sortBy.value.direction === 'asc') {
      sortBy.value.direction = 'desc'
    } else {
      sortBy.value = null
    }
  } else {
    sortBy.value = { key, direction: 'asc' }
  }
}

function getCategoryName(id: number) {
  return categories.value.find(c => c.id === id)?.name ?? ''
}

function getStockQuantity(productId: number, storeId: number | null) {
  if (!storeId) return '—'
  return inventory.getStockQuantity(productId, storeId)
}

function showStockModal(product: Product) {
  selectedProduct.value = product
}

function editProduct(product: Product) {
  selectedProduct.value = product
  showCreateModal.value = true
}

async function deleteProduct(product: Product) {
  if (!confirm('Are you sure you want to delete this product?')) return

  try {
    await inventory.deleteProduct(product.id)
    toast.success('Product deleted successfully')
  } catch (error: any) {
    toast.error(error.response?.data?.message || 'Failed to delete product')
  }
}

function closeProductModal() {
  showCreateModal.value = false
  selectedProduct.value = null
}

async function handleSaveProduct(formData: FormData) {
  try {
    if (selectedProduct.value) {
      await inventory.updateProduct(selectedProduct.value.id, formData)
      toast.success('Product updated successfully')
    } else {
      await inventory.createProduct(formData)
      toast.success('Product created successfully')
    }
    closeProductModal()
  } catch (error: any) {
    toast.error(error.response?.data?.message || 'Failed to save product')
  }
}

// Initialize
onMounted(async () => {
  await inventory.initialize()
  await inventory.fetchProducts()
})
</script>