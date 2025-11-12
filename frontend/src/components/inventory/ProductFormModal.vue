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
                {{ product ? 'Edit Product' : 'New Product' }}
              </DialogTitle>

              <form @submit.prevent="handleSubmit" class="mt-4 space-y-6">
                <!-- Image upload -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Product Image
                  </label>
                  <div
                    class="mt-1 flex justify-center rounded-lg border-2 border-dashed border-gray-300 px-6 pt-5 pb-6 dark:border-gray-600"
                    @dragover.prevent
                    @drop.prevent="handleDrop"
                  >
                    <div class="space-y-1 text-center">
                      <div v-if="imagePreview" class="mb-4">
                        <img
                          :src="imagePreview"
                          alt="Preview"
                          class="mx-auto h-32 w-32 rounded-lg object-cover"
                        />
                      </div>
                      <div class="flex text-sm text-gray-600 dark:text-gray-400">
                        <label
                          for="image-upload"
                          class="relative cursor-pointer rounded-md font-medium text-green-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-green-500 focus-within:ring-offset-2 hover:text-green-500 dark:text-green-500 dark:hover:text-green-400"
                        >
                          <span>Upload a file</span>
                          <input
                            id="image-upload"
                            name="image"
                            type="file"
                            accept="image/*"
                            class="sr-only"
                            @change="handleImageChange"
                          />
                        </label>
                        <p class="pl-1">or drag and drop</p>
                      </div>
                      <p class="text-xs text-gray-500 dark:text-gray-400">
                        PNG, JPG, GIF up to 10MB
                      </p>
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                  <!-- Name -->
                  <div>
                    <label
                      for="name"
                      class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                      Product Name
                    </label>
                    <input
                      type="text"
                      id="name"
                      v-model="form.name"
                      required
                      class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                    />
                  </div>

                  <!-- Reference -->
                  <div>
                    <label
                      for="reference"
                      class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                      Reference
                    </label>
                    <input
                      type="text"
                      id="reference"
                      v-model="form.reference"
                      required
                      class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                    />
                  </div>

                  <!-- Barcode -->
                  <div>
                    <label
                      for="barcode"
                      class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                      Barcode
                    </label>
                    <div class="mt-1 flex rounded-lg shadow-sm">
                      <input
                        type="text"
                        id="barcode"
                        v-model="form.barcode"
                        class="block w-full flex-1 rounded-none rounded-l-lg border border-gray-300 px-3 py-2 focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                      />
                      <button
                        type="button"
                        @click="generateBarcode"
                        class="relative -ml-px inline-flex items-center rounded-r-lg border border-gray-300 bg-gray-50 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500"
                      >
                        Generate
                      </button>
                    </div>
                  </div>

                  <!-- Category -->
                  <div>
                    <label
                      for="category"
                      class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                      Category
                    </label>
                    <select
                      id="category"
                      v-model="form.category_id"
                      required
                      class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                    >
                      <option value="">Select a category</option>
                      <option
                        v-for="category in mainCategories"
                        :key="category.id"
                        :value="category.id"
                      >
                        {{ category.name }}
                      </option>
                    </select>
                  </div>

                  <!-- Subcategory -->
                  <div>
                    <label
                      for="subcategory"
                      class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                      Subcategory
                    </label>
                    <select
                      id="subcategory"
                      v-model="form.subcategory_id"
                      :disabled="!form.category_id"
                      class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                    >
                      <option value="">None</option>
                      <option
                        v-for="subcategory in subcategories"
                        :key="subcategory.id"
                        :value="subcategory.id"
                      >
                        {{ subcategory.name }}
                      </option>
                    </select>
                  </div>

                  <!-- Cost Price -->
                  <div>
                    <label
                      for="cost_price"
                      class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                      Cost Price
                    </label>
                    <div class="mt-1 relative rounded-lg shadow-sm">
                      <div
                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                      >
                        <span class="text-gray-500 sm:text-sm">$</span>
                      </div>
                      <input
                        type="number"
                        id="cost_price"
                        v-model="form.cost_price"
                        required
                        min="0"
                        step="0.01"
                        class="block w-full rounded-lg border border-gray-300 pl-7 pr-12 focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                      />
                    </div>
                  </div>

                  <!-- Selling Price -->
                  <div>
                    <label
                      for="selling_price"
                      class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                      Selling Price
                    </label>
                    <div class="mt-1 relative rounded-lg shadow-sm">
                      <div
                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                      >
                        <span class="text-gray-500 sm:text-sm">$</span>
                      </div>
                      <input
                        type="number"
                        id="selling_price"
                        v-model="form.selling_price"
                        required
                        min="0"
                        step="0.01"
                        class="block w-full rounded-lg border border-gray-300 pl-7 pr-12 focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                      />
                    </div>
                  </div>

                  <!-- Min Stock -->
                  <div>
                    <label
                      for="min_stock"
                      class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                      Minimum Stock Level
                    </label>
                    <input
                      type="number"
                      id="min_stock"
                      v-model="form.min_stock"
                      required
                      min="0"
                      class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                    />
                  </div>
                </div>

                <!-- Description -->
                <div>
                  <label
                    for="description"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                  >
                    Description
                  </label>
                  <textarea
                    id="description"
                    v-model="form.description"
                    rows="3"
                    class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                  ></textarea>
                </div>

                <!-- Form actions -->
                <div class="flex justify-end space-x-3">
                  <button
                    type="button"
                    @click="$emit('close')"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                  >
                    {{ product ? 'Update' : 'Create' }}
                  </button>
                </div>
              </form>
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
import type { Product, Category } from '@/stores/inventory'

const props = defineProps<{
  product?: Product
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', formData: FormData): void
}>()

const inventory = useInventoryStore()

// Form state
const form = ref({
  name: props.product?.name || '',
  reference: props.product?.reference || '',
  barcode: props.product?.barcode || '',
  category_id: props.product?.category_id || '',
  subcategory_id: props.product?.subcategory_id || '',
  description: props.product?.description || '',
  cost_price: props.product?.cost_price || 0,
  selling_price: props.product?.selling_price || 0,
  min_stock: props.product?.min_stock || 0
})

const imageFile = ref<File | null>(null)
const imagePreview = ref<string>(props.product?.image_url || '')

// Computed
const mainCategories = computed(() =>
  inventory.categories.filter(c => !c.parent_id)
)

const subcategories = computed(() =>
  inventory.categories.filter(c => c.parent_id === form.value.category_id)
)

// Methods
function handleImageChange(event: Event) {
  const input = event.target as HTMLInputElement
  if (input.files?.length) {
    const file = input.files[0]
    handleImageFile(file)
  }
}

function handleDrop(event: DragEvent) {
  const file = event.dataTransfer?.files[0]
  if (file) {
    handleImageFile(file)
  }
}

function handleImageFile(file: File) {
  if (!file.type.startsWith('image/')) {
    alert('Please upload an image file')
    return
  }

  imageFile.value = file
  const reader = new FileReader()
  reader.onload = e => {
    imagePreview.value = e.target?.result as string
  }
  reader.readAsDataURL(file)
}

function generateBarcode() {
  // Generate EAN-13 barcode
  const prefix = '200' // Country code
  const random = Math.floor(Math.random() * 1000000000)
    .toString()
    .padStart(9, '0')
  const partial = prefix + random

  // Calculate check digit
  let sum = 0
  for (let i = 0; i < 12; i++) {
    sum += parseInt(partial[i]) * (i % 2 === 0 ? 1 : 3)
  }
  const check = (10 - (sum % 10)) % 10

  form.value.barcode = partial + check
}

function handleSubmit() {
  const formData = new FormData()

  // Append form fields
  Object.entries(form.value).forEach(([key, value]) => {
    formData.append(key, value.toString())
  })

  // Append image if changed
  if (imageFile.value) {
    formData.append('image', imageFile.value)
  }

  emit('save', formData)
}
</script>