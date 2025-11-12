<template>
  <div class="space-y-6">
    <!-- Page header -->
    <div>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Dashboard</h1>
      <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
        Store performance overview and key metrics
      </p>
    </div>

    <!-- KPI Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
      <div v-for="kpi in kpis" :key="kpi.name" class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <component
                :is="kpi.icon"
                class="h-6 w-6 text-green-600 dark:text-green-500"
                aria-hidden="true"
              />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">
                  {{ kpi.name }}
                </dt>
                <dd>
                  <div class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ kpi.value }}
                  </div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-5 py-3 dark:bg-gray-700">
          <div class="text-sm">
            <a
              href="#"
              class="font-medium text-green-600 hover:text-green-500 dark:text-green-500 dark:hover:text-green-400"
            >
              View details
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      <!-- Sales Chart -->
      <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
        <div class="mb-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            Sales Overview
          </h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">
            Last 7 days performance
          </p>
        </div>
        <div class="relative h-80">
          <LineChart :data="salesChartData" :options="chartOptions" />
        </div>
      </div>

      <!-- Products Chart -->
      <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
        <div class="mb-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            Top Products
          </h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">
            Best selling items
          </p>
        </div>
        <div class="relative h-80">
          <BarChart :data="productsChartData" :options="chartOptions" />
        </div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="rounded-lg bg-white shadow dark:bg-gray-800">
      <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
          Recent Activity
        </h3>
        <div class="mt-6 flow-root">
          <ul role="list" class="-mb-8">
            <li v-for="(activity, i) in recentActivity" :key="activity.id">
              <div class="relative pb-8">
                <span
                  v-if="i !== recentActivity.length - 1"
                  class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700"
                  aria-hidden="true"
                />
                <div class="relative flex space-x-3">
                  <div>
                    <span
                      :class="[
                        activity.type === 'sale' ? 'bg-green-500' : 'bg-blue-500',
                        'flex h-8 w-8 items-center justify-center rounded-full ring-8 ring-white dark:ring-gray-800'
                      ]"
                    >
                      <component
                        :is="activity.icon"
                        class="h-5 w-5 text-white"
                        aria-hidden="true"
                      />
                    </span>
                  </div>
                  <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                    <div>
                      <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ activity.content }}
                      </p>
                    </div>
                    <div class="whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                      <time :datetime="activity.datetime">{{ activity.date }}</time>
                    </div>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import {
  CurrencyDollarIcon,
  ShoppingCartIcon,
  UsersIcon,
  ClipboardDocumentListIcon,
  DocumentCheckIcon,
  TruckIcon
} from '@heroicons/vue/24/outline'
import { Line as LineChart, Bar as BarChart } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'

// Register ChartJS components
ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend
)

// KPI Data
const kpis = [
  {
    name: 'Total Sales',
    value: '$24,563.00',
    icon: CurrencyDollarIcon
  },
  {
    name: 'Products in Stock',
    value: '1,234',
    icon: ClipboardDocumentListIcon
  },
  {
    name: 'Active Customers',
    value: '321',
    icon: UsersIcon
  },
  {
    name: 'Pending Orders',
    value: '12',
    icon: ShoppingCartIcon
  }
]

// Chart Data
const salesChartData = {
  labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
  datasets: [
    {
      label: 'Sales',
      data: [2112, 2343, 2545, 3423, 2365, 1985, 2943],
      borderColor: '#16A34A',
      backgroundColor: 'rgba(22, 163, 74, 0.1)',
      fill: true,
      tension: 0.4
    }
  ]
}

const productsChartData = {
  labels: ['Product A', 'Product B', 'Product C', 'Product D', 'Product E'],
  datasets: [
    {
      label: 'Units Sold',
      data: [123, 234, 345, 456, 567],
      backgroundColor: '#16A34A'
    }
  ]
}

// Chart Options
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      labels: {
        color: '#6B7280'
      }
    }
  },
  scales: {
    y: {
      ticks: {
        color: '#6B7280'
      },
      grid: {
        color: 'rgba(107, 114, 128, 0.1)'
      }
    },
    x: {
      ticks: {
        color: '#6B7280'
      },
      grid: {
        color: 'rgba(107, 114, 128, 0.1)'
      }
    }
  }
}

// Recent Activity
const recentActivity = [
  {
    id: 1,
    content: 'New sale completed - Order #12345',
    type: 'sale',
    icon: DocumentCheckIcon,
    datetime: '2023-11-03T13:00',
    date: '1 hour ago'
  },
  {
    id: 2,
    content: 'Stock delivery received from Supplier XYZ',
    type: 'delivery',
    icon: TruckIcon,
    datetime: '2023-11-03T11:00',
    date: '3 hours ago'
  },
  {
    id: 3,
    content: 'New customer registration - John Doe',
    type: 'customer',
    icon: UsersIcon,
    datetime: '2023-11-03T09:00',
    date: '5 hours ago'
  }
]
</script>