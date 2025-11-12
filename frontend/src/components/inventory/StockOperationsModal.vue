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
            <DialogPanel class="w-full max-w-2xl transform overflow-hidden rounded-xl bg-white p-6 shadow-xl transition-all dark:bg-gray-800">
              <DialogTitle
                as="h3"
                class="text-lg font-medium leading-6 text-gray-900 dark:text-white"
              >
                Stock Operations - {{ product.name }}
              </DialogTitle>

              <div class="mt-4">
                <!-- Current stock info -->
                <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-700">
                  <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                    <div>
                      <label class="text-sm text-gray-500 dark:text-gray-400">
                        Reference
                      </label>
                      <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ product.reference }}
                      </p>
                    </div>
                    <div>
                      <label class="text-sm text-gray-500 dark:text-gray-400">
                        Barcode
                      </label>
                      <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ product.barcode }}
                      </p>
                    </div>
                    <div>
                      <label class="text-sm text-gray-500 dark:text-gray-400">
                        Minimum Stock
                      </label>
                      <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ product.min_stock }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Stock levels by store -->
                <div class="mt-4">
                  <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                    Current Stock Levels
                  </h4>
                  <div class="mt-2 divide-y divide-gray-200 dark:divide-gray-700">
                    <div
                      v-for="store in stores"
                      :key="store.id"
                      class="flex items-center justify-between py-2"
                    >
                      <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ store.name }}
                      </span>
                      <span class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ getStockQuantity(product.id, store.id) }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Operation type tabs -->
                <div class="mt-6">
                  <div class="sm:hidden">
                    <select
                      v-model="selectedOperation"
                      class="block w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700"
                    >
                      <option value="entry">Stock Entry</option>
                      <option value="adjustment">Stock Adjustment</option>
                      <option value="transfer">Stock Transfer</option>
                    </select>
                  </div>
                  <div class="hidden sm:block">
                    <div class="border-b border-gray-200 dark:border-gray-700">
                      <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button
                          v-for="tab in tabs"
                          :key="tab.value"
                          @click="selectedOperation = tab.value"
                          :class="[
                            selectedOperation === tab.value
                              ? 'border-green-500 text-green-600 dark:text-green-500'
                              : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300',
                            'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium'
                          ]"
                        >
                          {{ tab.name }}
                        </button>
                      </nav>
                    </div>
                  </div>
                </div>

                <!-- Operation forms -->
                <div class="mt-6">
                  <!-- Stock Entry -->
                  <form v-if="selectedOperation === 'entry'" @submit.prevent="handleEntry">
                    <div class="space-y-4">
                      <div>
                        <label
                          class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                          Store
                        </label>
                        <select
                          v-model="entryForm.store_id"
                          required
                          class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
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
                          Quantity
                        </label>
                        <input
                          type="number"
                          v-model="entryForm.quantity"
                          required
                          min="1"
                          class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                        />
                      </div>
                      <div>
                        <label
                          class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                          Notes
                        </label>
                        <textarea
                          v-model="entryForm.notes"
                          rows="2"
                          class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                        ></textarea>
                      </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                      <button
                        type="submit"
                        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                      >
                        Add Stock
                      </button>
                    </div>
                  </form>

                  <!-- Stock Adjustment -->
                  <form
                    v-if="selectedOperation === 'adjustment'"
                    @submit.prevent="handleAdjustment"
                  >
                    <div class="space-y-4">
                      <div>
                        <label
                          class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                          Store
                        </label>
                        <select
                          v-model="adjustmentForm.store_id"
                          required
                          class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
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
                          New Quantity
                        </label>
                        <input
                          type="number"
                          v-model="adjustmentForm.quantity"
                          required
                          min="0"
                          class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                        />
                      </div>
                      <div>
                        <label
                          class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                          Reason
                        </label>
                        <textarea
                          v-model="adjustmentForm.notes"
                          required
                          rows="2"
                          class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                        ></textarea>
                      </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                      <button
                        type="submit"
                        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                      >
                        Adjust Stock
                      </button>
                    </div>
                  </form>

                  <!-- Stock Transfer -->
                  <form
                    v-if="selectedOperation === 'transfer'"
                    @submit.prevent="handleTransfer"
                  >
                    <div class="space-y-4">
                      <div class="grid grid-cols-2 gap-4">
                        <div>
                          <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                          >
                            From Store
                          </label>
                          <select
                            v-model="transferForm.source_store_id"
                            required
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                          >
                            <option value="">Select store</option>
                            <option
                              v-for="store in stores"
                              :key="store.id"
                              :value="store.id"
                            >
                              {{ store.name }}
                            </option>
                          </select>
                        </div>
                        <div>
                          <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                          >
                            To Store
                          </label>
                          <select
                            v-model="transferForm.destination_store_id"
                            required
                            :disabled="!transferForm.source_store_id"
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                          >
                            <option value="">Select store</option>
                            <option
                              v-for="store in availableDestinationStores"
                              :key="store.id"
                              :value="store.id"
                            >
                              {{ store.name }}
                            </option>
                          </select>
                        </div>
                      </div>
                      <div>
                        <label
                          class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                          Quantity
                        </label>
                        <input
                          type="number"
                          v-model="transferForm.quantity"
                          required
                          min="1"
                          :max="
                            transferForm.source_store_id
                              ? getStockQuantity(product.id, transferForm.source_store_id)
                              : 0
                          "
                          class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                        />
                      </div>
                      <div>
                        <label
                          class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                          Notes
                        </label>
                        <textarea
                          v-model="transferForm.notes"
                          rows="2"
                          class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                        ></textarea>
                      </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                      <button
                        type="submit"
                        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                      >
                        Transfer Stock
                      </button>
                    </div>
                  </form>
                </div>
              </div>

              <div class="mt-6 flex justify-end">
                <button
                  type="button"
                  @click="$emit('close')"
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
import type { Product } from '@/stores/inventory'

const props = defineProps<{
  product: Product
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'refresh'): void
}>()

const inventory = useInventoryStore()
const toast = useToast()

// State
const selectedOperation = ref<'entry' | 'adjustment' | 'transfer'>('entry')
const entryForm = ref({
  store_id: '',
  quantity: 1,
  notes: ''
})
const adjustmentForm = ref({
  store_id: '',
  quantity: 0,
  notes: ''
})
const transferForm = ref({
  source_store_id: '',
  destination_store_id: '',
  quantity: 1,
  notes: ''
})

// Computed
const stores = computed(() => inventory.stores)
const availableDestinationStores = computed(() =>
  stores.value.filter(store => store.id !== transferForm.value.source_store_id)
)

const tabs = [
  { name: 'Stock Entry', value: 'entry' },
  { name: 'Stock Adjustment', value: 'adjustment' },
  { name: 'Stock Transfer', value: 'transfer' }
]

// Methods
function getStockQuantity(productId: number, storeId: number) {
  return inventory.getStockQuantity(productId, storeId)
}

async function handleEntry() {
  try {
    await inventory.createStockEntry({
      product_id: props.product.id,
      store_id: parseInt(entryForm.value.store_id),
      quantity: entryForm.value.quantity,
      notes: entryForm.value.notes
    })
    toast.success('Stock entry recorded successfully')
    emit('refresh')
    emit('close')
  } catch (error: any) {
    toast.error(error.response?.data?.message || 'Failed to record stock entry')
  }
}

async function handleAdjustment() {
  try {
    await inventory.createStockAdjustment({
      product_id: props.product.id,
      store_id: parseInt(adjustmentForm.value.store_id),
      quantity: adjustmentForm.value.quantity,
      notes: adjustmentForm.value.notes
    })
    toast.success('Stock adjusted successfully')
    emit('refresh')
    emit('close')
  } catch (error: any) {
    toast.error(error.response?.data?.message || 'Failed to adjust stock')
  }
}

async function handleTransfer() {
  try {
    await inventory.createStockTransfer({
      source_store_id: parseInt(transferForm.value.source_store_id),
      destination_store_id: parseInt(transferForm.value.destination_store_id),
      items: [
        {
          product_id: props.product.id,
          quantity: transferForm.value.quantity
        }
      ],
      notes: transferForm.value.notes
    })
    toast.success('Stock transfer initiated successfully')
    emit('refresh')
    emit('close')
  } catch (error: any) {
    toast.error(error.response?.data?.message || 'Failed to create stock transfer')
  }
}
</script>