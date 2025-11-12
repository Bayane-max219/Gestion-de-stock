import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from '@/utils/api'

export interface Product {
  id: number
  name: string
  reference: string
  barcode: string
  selling_price: number
  cost_price: number
  quantity: number
  image_url?: string
}

export interface Customer {
  id: number
  name: string
  email: string
  phone: string
}

export interface CartItem {
  product: Product
  quantity: number
  discount: number
  discount_type: 'percentage' | 'amount'
}

export interface PaymentMethod {
  type: 'cash' | 'mobile_money' | 'bank'
  provider?: 'orange' | 'airtel' | 'telma'
  amount: number
  reference?: string
}

export const usePosStore = defineStore('pos', () => {
  // State
  const cart = ref<CartItem[]>([])
  const selectedCustomer = ref<Customer | null>(null)
  const searchResults = ref<Product[]>([])
  const isSearching = ref(false)
  const cashierShift = ref<any>(null)

  // Getters
  const subtotal = computed(() => {
    return cart.value.reduce((total, item) => {
      const itemTotal = item.product.selling_price * item.quantity
      if (item.discount_type === 'percentage') {
        return total + (itemTotal * (1 - item.discount / 100))
      }
      return total + (itemTotal - item.discount)
    }, 0)
  })

  const total = computed(() => subtotal.value)

  // Actions
  async function searchProducts(query: string) {
    isSearching.value = true
    try {
      const response = await api.get('/pos/products/search', {
        params: { query }
      })
      searchResults.value = response.data
    } catch (error) {
      console.error('Failed to search products:', error)
      searchResults.value = []
    } finally {
      isSearching.value = false
    }
  }

  function addToCart(product: Product, quantity = 1) {
    const existingItem = cart.value.find(item => item.product.id === product.id)
    
    if (existingItem) {
      existingItem.quantity += quantity
    } else {
      cart.value.push({
        product,
        quantity,
        discount: 0,
        discount_type: 'amount'
      })
    }
  }

  function updateCartItem(productId: number, quantity: number) {
    const item = cart.value.find(item => item.product.id === productId)
    if (item) {
      item.quantity = quantity
    }
  }

  function removeFromCart(productId: number) {
    const index = cart.value.findIndex(item => item.product.id === productId)
    if (index !== -1) {
      cart.value.splice(index, 1)
    }
  }

  function setDiscount(productId: number, discount: number, type: 'percentage' | 'amount') {
    const item = cart.value.find(item => item.product.id === productId)
    if (item) {
      item.discount = discount
      item.discount_type = type
    }
  }

  function setCustomer(customer: Customer | null) {
    selectedCustomer.value = customer
  }

  async function startShift() {
    const response = await api.post('/pos/shifts/start')
    cashierShift.value = response.data
  }

  async function endShift() {
    const response = await api.post('/pos/shifts/end')
    cashierShift.value = null
    return response.data
  }

  async function processSale(payments: PaymentMethod[]) {
    try {
      const response = await api.post('/pos/sales', {
        customer_id: selectedCustomer.value?.id,
        items: cart.value.map(item => ({
          product_id: item.product.id,
          quantity: item.quantity,
          discount: item.discount,
          discount_type: item.discount_type
        })),
        payments
      })

      // Clear cart after successful sale
      cart.value = []
      selectedCustomer.value = null

      return response.data
    } catch (error) {
      throw error
    }
  }

  async function generateReceipt(saleId: number) {
    const response = await api.get(`/pos/sales/${saleId}/receipt`, {
      responseType: 'blob'
    })
    return response.data
  }

  return {
    // State
    cart,
    selectedCustomer,
    searchResults,
    isSearching,
    cashierShift,
    
    // Getters
    subtotal,
    total,
    
    // Actions
    searchProducts,
    addToCart,
    updateCartItem,
    removeFromCart,
    setDiscount,
    setCustomer,
    startShift,
    endShift,
    processSale,
    generateReceipt
  }
})