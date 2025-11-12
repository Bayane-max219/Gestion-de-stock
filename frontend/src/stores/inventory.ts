import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from '@/utils/api'

export interface Product {
  id: number
  name: string
  reference: string
  barcode: string
  category_id: number
  subcategory_id: number | null
  description: string
  cost_price: number
  selling_price: number
  min_stock: number
  image_url: string | null
  created_at: string
  updated_at: string
}

export interface StoreProduct {
  store_id: number
  product_id: number
  quantity: number
  reserved_quantity: number
}

export interface Category {
  id: number
  name: string
  parent_id: number | null
  subcategories?: Category[]
}

export interface Store {
  id: number
  name: string
  location: string
  is_active: boolean
}

export interface StockMovement {
  id: number
  product_id: number
  store_id: number
  type: 'entry' | 'adjustment' | 'transfer' | 'sale' | 'purchase'
  quantity: number
  previous_quantity: number
  reference: string
  notes: string
  created_by: number
  created_at: string
  product?: Product
  store?: Store
  user?: { id: number; name: string }
}

export interface StockTransfer {
  id: number
  source_store_id: number
  destination_store_id: number
  status: 'pending' | 'completed' | 'cancelled'
  items: {
    product_id: number
    quantity: number
    product?: Product
  }[]
  created_by: number
  created_at: string
}

export const useInventoryStore = defineStore('inventory', () => {
  // State
  const products = ref<Product[]>([])
  const categories = ref<Category[]>([])
  const stores = ref<Store[]>([])
  const stockMovements = ref<StockMovement[]>([])
  const stockTransfers = ref<StockTransfer[]>([])
  const storeProducts = ref<StoreProduct[]>([])
  const selectedStore = ref<Store | null>(null)
  const loading = ref(false)

  // Getters
  const productsByStore = computed(() => {
    return (storeId: number) => {
      return storeProducts.value.filter(sp => sp.store_id === storeId)
    }
  })

  const getStockQuantity = computed(() => {
    return (productId: number, storeId: number) => {
      const sp = storeProducts.value.find(
        sp => sp.product_id === productId && sp.store_id === storeId
      )
      return sp?.quantity ?? 0
    }
  })

  // Actions
  async function fetchProducts(params?: { category?: number; query?: string }) {
    loading.value = true
    try {
      const response = await api.get('/products', { params })
      products.value = response.data
    } finally {
      loading.value = false
    }
  }

  async function fetchCategories() {
    const response = await api.get('/categories')
    categories.value = response.data
  }

  async function fetchStores() {
    const response = await api.get('/stores')
    stores.value = response.data
  }

  async function fetchStockMovements(params?: {
    product_id?: number
    store_id?: number
    type?: string
    start_date?: string
    end_date?: string
  }) {
    const response = await api.get('/stock/movements', { params })
    stockMovements.value = response.data
  }

  async function createProduct(data: FormData) {
    const response = await api.post('/products', data)
    products.value.push(response.data)
    return response.data
  }

  async function updateProduct(id: number, data: FormData) {
    const response = await api.post(`/products/${id}`, data)
    const index = products.value.findIndex(p => p.id === id)
    if (index !== -1) {
      products.value[index] = response.data
    }
    return response.data
  }

  async function deleteProduct(id: number) {
    await api.delete(`/products/${id}`)
    const index = products.value.findIndex(p => p.id === id)
    if (index !== -1) {
      products.value.splice(index, 1)
    }
  }

  async function createStockEntry(data: {
    store_id: number
    product_id: number
    quantity: number
    notes: string
  }) {
    const response = await api.post('/stock/entry', data)
    return response.data
  }

  async function createStockAdjustment(data: {
    store_id: number
    product_id: number
    quantity: number
    notes: string
  }) {
    const response = await api.post('/stock/adjust', data)
    return response.data
  }

  async function createStockTransfer(data: {
    source_store_id: number
    destination_store_id: number
    items: { product_id: number; quantity: number }[]
    notes: string
  }) {
    const response = await api.post('/stock/transfer', data)
    return response.data
  }

  async function validateStockTransfer(id: number) {
    const response = await api.post(`/stock/transfer/${id}/validate`)
    return response.data
  }

  async function cancelStockTransfer(id: number) {
    const response = await api.post(`/stock/transfer/${id}/cancel`)
    return response.data
  }

  async function startInventoryCount(storeId: number) {
    const response = await api.post(`/stock/inventory-count/${storeId}/start`)
    return response.data
  }

  async function submitInventoryCount(storeId: number, data: {
    items: { product_id: number; counted_quantity: number }[]
  }) {
    const response = await api.post(`/stock/inventory-count/${storeId}/submit`, data)
    return response.data
  }

  async function validateInventoryCount(storeId: number, countId: number) {
    const response = await api.post(
      `/stock/inventory-count/${storeId}/${countId}/validate`
    )
    return response.data
  }

  function setSelectedStore(store: Store | null) {
    selectedStore.value = store
  }

  // Initialize
  async function initialize() {
    await Promise.all([
      fetchStores(),
      fetchCategories()
    ])
  }

  return {
    // State
    products,
    categories,
    stores,
    stockMovements,
    stockTransfers,
    storeProducts,
    selectedStore,
    loading,

    // Getters
    productsByStore,
    getStockQuantity,

    // Actions
    fetchProducts,
    fetchCategories,
    fetchStores,
    fetchStockMovements,
    createProduct,
    updateProduct,
    deleteProduct,
    createStockEntry,
    createStockAdjustment,
    createStockTransfer,
    validateStockTransfer,
    cancelStockTransfer,
    startInventoryCount,
    submitInventoryCount,
    validateInventoryCount,
    setSelectedStore,
    initialize
  }
})