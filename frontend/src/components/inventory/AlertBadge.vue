<template>
  <span
    :class="[
      'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
      getAlertClass(type)
    ]"
  >
    <svg
      v-if="showIcon"
      class="-ml-0.5 mr-1.5 h-2 w-2"
      :class="{ 'animate-ping': animated }"
      fill="currentColor"
      viewBox="0 0 8 8"
    >
      <circle cx="4" cy="4" r="3" />
    </svg>
    {{ label }}
  </span>
</template>

<script setup lang="ts">
const props = withDefaults(
  defineProps<{
    type: 'low-stock' | 'out-of-stock' | 'expiring-soon'
    showIcon?: boolean
    animated?: boolean
  }>(),
  {
    showIcon: true,
    animated: false
  }
)

const getAlertClass = (type: string) => {
  switch (type) {
    case 'low-stock':
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
    case 'out-of-stock':
      return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
    case 'expiring-soon':
      return 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300'
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
  }
}

const label = computed(() => {
  switch (props.type) {
    case 'low-stock':
      return 'Low Stock'
    case 'out-of-stock':
      return 'Out of Stock'
    case 'expiring-soon':
      return 'Expiring Soon'
    default:
      return ''
  }
})</script>