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
            <DialogPanel class="w-full max-w-md transform overflow-hidden rounded-xl bg-white dark:bg-gray-800 p-6 shadow-xl transition-all">
              <DialogTitle
                as="h3"
                class="text-lg font-medium leading-6 text-gray-900 dark:text-white"
              >
                Process Payment
              </DialogTitle>

              <div class="mt-4">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                  Total Amount:
                  <span class="ml-2 text-lg font-bold text-gray-900 dark:text-white">
                    ${{ props.total.toFixed(2) }}
                  </span>
                </div>

                <!-- Payment methods -->
                <div class="mt-4 space-y-4">
                  <div v-for="(payment, index) in payments" :key="index" class="space-y-3">
                    <div class="flex gap-3">
                      <select
                        v-model="payment.type"
                        class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                      >
                        <option value="cash">Cash</option>
                        <option value="mobile_money">Mobile Money</option>
                        <option value="bank">Bank Transfer</option>
                      </select>
                      <button
                        v-if="index > 0"
                        @click="removePayment(index)"
                        class="rounded-lg p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30"
                      >
                        <XMarkIcon class="h-5 w-5" />
                      </button>
                    </div>

                    <!-- Mobile money providers -->
                    <div v-if="payment.type === 'mobile_money'" class="flex gap-3">
                      <select
                        v-model="payment.provider"
                        class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                      >
                        <option value="orange">Orange Money</option>
                        <option value="airtel">Airtel Money</option>
                        <option value="telma">Telma Mvola</option>
                      </select>
                    </div>

                    <!-- Amount and reference -->
                    <div class="flex gap-3">
                      <div class="flex-1">
                        <input
                          type="number"
                          v-model.number="payment.amount"
                          placeholder="Amount"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-700"
                        />
                      </div>
                      <div class="flex-1" v-if="payment.type !== 'cash'">
                        <input
                          type="text"
                          v-model="payment.reference"
                          placeholder="Reference"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-700"
                        />
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Add payment method -->
                <button
                  @click="addPayment"
                  class="mt-4 w-full rounded-lg border border-gray-300 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                >
                  Add Payment Method
                </button>

                <!-- Totals -->
                <div class="mt-4 space-y-2 border-t border-gray-200 pt-4 dark:border-gray-700">
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Total Due:</span>
                    <span class="font-medium text-gray-900 dark:text-white">
                      ${{ props.total.toFixed(2) }}
                    </span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Total Paid:</span>
                    <span
                      :class="[
                        totalPaid === props.total
                          ? 'text-green-600 dark:text-green-500'
                          : 'text-gray-900 dark:text-white'
                      ]"
                    >
                      ${{ totalPaid.toFixed(2) }}
                    </span>
                  </div>
                  <div class="flex justify-between text-sm font-medium">
                    <span class="text-gray-500 dark:text-gray-400">Remaining:</span>
                    <span
                      :class="[
                        remaining === 0
                          ? 'text-green-600 dark:text-green-500'
                          : 'text-red-600 dark:text-red-500'
                      ]"
                    >
                      ${{ remaining.toFixed(2) }}
                    </span>
                  </div>
                </div>
              </div>

              <div class="mt-6 flex justify-end gap-3">
                <button
                  @click="$emit('close')"
                  class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                >
                  Cancel
                </button>
                <button
                  @click="handleProcess"
                  :disabled="!isValid"
                  class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 dark:focus:ring-offset-gray-800"
                >
                  Complete Sale
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
import { XMarkIcon } from '@heroicons/vue/24/outline'
import type { PaymentMethod } from '@/stores/pos'

// Props & Emits
const props = defineProps<{
  total: number
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'process', payments: PaymentMethod[]): void
}>()

// State
const payments = ref<PaymentMethod[]>([
  {
    type: 'cash',
    amount: props.total,
    provider: undefined,
    reference: undefined
  }
])

// Computed
const totalPaid = computed(() => {
  return payments.value.reduce((sum, payment) => sum + (payment.amount || 0), 0)
})

const remaining = computed(() => {
  return props.total - totalPaid.value
})

const isValid = computed(() => {
  return (
    totalPaid.value === props.total &&
    payments.value.every(payment => {
      if (payment.type === 'mobile_money') {
        return payment.provider && payment.reference
      }
      if (payment.type === 'bank') {
        return payment.reference
      }
      return true
    })
  )
})

// Methods
function addPayment() {
  payments.value.push({
    type: 'cash',
    amount: remaining.value > 0 ? remaining.value : 0,
    provider: undefined,
    reference: undefined
  })
}

function removePayment(index: number) {
  payments.value.splice(index, 1)
}

function handleProcess() {
  if (isValid.value) {
    emit('process', payments.value)
  }
}
</script>