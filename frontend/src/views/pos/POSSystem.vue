<template>
  <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
    <!-- Left side: Product search and grid -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Search header -->
      <div class="p-4 bg-white dark:bg-gray-800 shadow">
        <div class="flex gap-4">
          <div class="flex-1">
            <input
              type="text"
              v-model="searchQuery"
              @input="handleSearch"
              placeholder="Search products by name, reference or scan barcode..."
              class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            />
          </div>
          <button
            @click="openCustomerModal"
            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
          >
            <UserIcon class="h-5 w-5 inline-block mr-2" />
            {{ selectedCustomer ? selectedCustomer.name : 'Select Customer' }}
          </button>
        </div>
      </div>

      <!-- Products grid -->
      <div class="flex-1 overflow-y-auto p-4">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <div
            v-for="product in searchResults"
            :key="product.id"
            @click="addToCart(product)"
            class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 cursor-pointer hover:shadow-md transition-shadow"
          >
            <div class="aspect-w-1 aspect-h-1 bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
              <img
                :src="product.image_url || '/placeholder.png'"
                :alt="product.name"
                class="object-cover"
              />
            </div>
            <div class="mt-4">
              <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                {{ product.name }}
              </h3>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ product.reference }}
              </p>
              <div class="mt-2 flex justify-between items-center">
                <span class="text-lg font-bold text-green-600 dark:text-green-500">
                  ${{ product.selling_price.toFixed(2) }}
                </span>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                  Stock: {{ product.quantity }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right side: Cart and payment -->
    <div class="w-96 flex flex-col bg-white dark:bg-gray-800 shadow-lg">
      <!-- Cart header -->
      <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
          Current Sale
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">
          {{ cart.length }} items
        </p>
      </div>

      <!-- Cart items -->
      <div class="flex-1 overflow-y-auto p-4">
        <div v-for="item in cart" :key="item.product.id" class="mb-4">
          <div class="flex justify-between">
            <div class="flex-1">
              <h4 class="font-medium text-gray-900 dark:text-white">
                {{ item.product.name }}
              </h4>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ item.product.reference }}
              </p>
            </div>
            <button
              @click="removeFromCart(item.product.id)"
              class="text-red-500 hover:text-red-600"
            >
              <XMarkIcon class="h-5 w-5" />
            </button>
          </div>
          <div class="mt-2 flex items-center gap-4">
            <div class="flex items-center">
              <button
                @click="updateQuantity(item.product.id, item.quantity - 1)"
                class="p-1 text-gray-500 hover:text-gray-600 dark:text-gray-400"
                :disabled="item.quantity <= 1"
              >
                <MinusIcon class="h-4 w-4" />
              </button>
              <input
                type="number"
                v-model.number="item.quantity"
                min="1"
                :max="item.product.quantity"
                class="w-16 text-center border-0 p-0 focus:ring-0 dark:bg-gray-800 dark:text-white"
              />
              <button
                @click="updateQuantity(item.product.id, item.quantity + 1)"
                class="p-1 text-gray-500 hover:text-gray-600 dark:text-gray-400"
                :disabled="item.quantity >= item.product.quantity"
              >
                <PlusIcon class="h-4 w-4" />
              </button>
            </div>
            <div class="flex-1 text-right">
              <span class="font-medium text-gray-900 dark:text-white">
                ${{ (item.product.selling_price * item.quantity).toFixed(2) }}
              </span>
            </div>
          </div>
          <!-- Discount input -->
          <div class="mt-2 flex items-center gap-2">
            <select
              v-model="item.discount_type"
              class="text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            >
              <option value="amount">$</option>
              <option value="percentage">%</option>
            </select>
            <input
              type="number"
              v-model.number="item.discount"
              placeholder="Discount"
              class="flex-1 text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            />
          </div>
        </div>
      </div>

      <!-- Cart totals -->
      <div class="border-t border-gray-200 dark:border-gray-700">
        <div class="p-4 space-y-2">
          <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
            <span>Subtotal</span>
            <span>${{ subtotal.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white">
            <span>Total</span>
            <span>${{ total.toFixed(2) }}</span>
          </div>
        </div>
      </div>

      <!-- Payment actions -->
      <div class="p-4 border-t border-gray-200 dark:border-gray-700">
        <button
          @click="openPaymentModal"
          class="w-full py-3 px-4 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
          :disabled="!cart.length"
        >
          Process Payment
        </button>
      </div>
    </div>

    <!-- Customer selection modal -->
    <CustomerModal
      v-if="showCustomerModal"
      @close="showCustomerModal = false"
      @select="selectCustomer"
    />

    <!-- Payment modal -->
    <PaymentModal
      v-if="showPaymentModal"
      :total="total"
      @close="showPaymentModal = false"
      @process="processPayment"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { UserIcon, XMarkIcon, MinusIcon, PlusIcon } from '@heroicons/vue/24/outline'
import { usePosStore } from '@/stores/pos'
import CustomerModal from '@/components/pos/CustomerModal.vue'
import PaymentModal from '@/components/pos/PaymentModal.vue'
import type { Customer, PaymentMethod } from '@/stores/pos'
import { useToast } from 'vue-toastification'

const toast = useToast()
const pos = usePosStore()

// Local state
const searchQuery = ref('')
const showCustomerModal = ref(false)
const showPaymentModal = ref(false)

// Computed
const cart = computed(() => pos.cart)
const searchResults = computed(() => pos.searchResults)
const selectedCustomer = computed(() => pos.selectedCustomer)
const subtotal = computed(() => pos.subtotal)
const total = computed(() => pos.total)

// Methods
function handleSearch() {
  if (searchQuery.value.length >= 2) {
    pos.searchProducts(searchQuery.value)
  }
}

function addToCart(product: any) {
  if (product.quantity > 0) {
    pos.addToCart(product)
    toast.success(`${product.name} added to cart`)
  } else {
    toast.error(`${product.name} is out of stock`)
  }
}

function updateQuantity(productId: number, quantity: number) {
  pos.updateCartItem(productId, quantity)
}

function removeFromCart(productId: number) {
  pos.removeFromCart(productId)
}

function openCustomerModal() {
  showCustomerModal.value = true
}

function selectCustomer(customer: Customer) {
  pos.setCustomer(customer)
  showCustomerModal.value = false
}

function openPaymentModal() {
  if (cart.value.length > 0) {
    showPaymentModal.value = true
  }
}

async function processPayment(payments: PaymentMethod[]) {
  try {
    const sale = await pos.processSale(payments)
    toast.success('Sale completed successfully')
    showPaymentModal.value = false
    
    // Generate and download receipt
    const receipt = await pos.generateReceipt(sale.id)
    const url = window.URL.createObjectURL(receipt)
    const link = document.createElement('a')
    link.href = url
    link.download = `receipt-${sale.id}.pdf`
    link.click()
  } catch (error: any) {
    toast.error(error.response?.data?.message || 'Failed to process sale')
  }
}

// Initialize
onMounted(async () => {
  // Start cashier shift if not already started
  if (!pos.cashierShift) {
    try {
      await pos.startShift()
      toast.success('Shift started successfully')
    } catch (error: any) {
      toast.error('Failed to start shift')
    }
  }
})
</script>