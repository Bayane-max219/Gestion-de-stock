<template>
  <div class="h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Sidebar -->
    <aside
      class="fixed inset-y-0 left-0 z-20 flex h-full w-64 flex-shrink-0 flex-col transition-transform"
      :class="{ '-translate-x-full': !sidebarOpen }"
    >
      <!-- Sidebar content -->
      <div class="flex h-full flex-col border-r border-gray-200 bg-white pt-5 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-shrink-0 items-center px-6">
          <img class="h-8 w-auto" src="@/assets/logo.svg" alt="Logo" />
          <span class="ml-3 text-xl font-semibold text-gray-900 dark:text-white">
            Inventory Pro
          </span>
        </div>

        <!-- Navigation -->
        <nav class="mt-6 flex-1 space-y-1 px-3">
          <RouterLink
            v-for="item in navigation"
            :key="item.name"
            :to="item.to"
            :class="[
              $route.name === item.name
                ? 'bg-green-50 text-green-600 border-l-4 border-green-500 dark:bg-green-900/30 dark:text-green-500 dark:border-green-500'
                : 'text-gray-700 hover:bg-gray-50 hover:text-green-600 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-green-400',
              'group flex items-center px-3 py-2 text-sm font-medium transition-all duration-200 ease-in-out'
            ]"
          >
            <component
              :is="item.icon"
              :class="[
                $route.name === item.name
                  ? 'text-green-600 transform scale-110 dark:text-green-500'
                  : 'text-gray-400 group-hover:text-green-600 dark:text-gray-400 dark:group-hover:text-green-400',
                'mr-3 h-5 w-5 flex-shrink-0 transition-all duration-200 ease-in-out'
              ]"
              aria-hidden="true"
            />
            {{ item.text }}
          </RouterLink>
        </nav>

        <!-- User menu -->
        <div class="border-t border-gray-200 p-4 dark:border-gray-700">
          <Menu as="div" class="relative">
            <MenuButton class="group flex w-full items-center text-left">
              <img
                class="h-8 w-8 rounded-full bg-gray-50"
                :src="userAvatar"
                alt=""
              />
              <span class="ml-3 flex-1 truncate">
                <span class="block text-sm font-medium text-gray-900 dark:text-white">
                  {{ user?.name }}
                </span>
                <span class="block text-xs text-gray-500 dark:text-gray-400">
                  {{ user?.role }}
                </span>
              </span>
            </MenuButton>

            <transition
              enter-active-class="transition ease-out duration-100"
              enter-from-class="transform opacity-0 scale-95"
              enter-to-class="transform opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75"
              leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95"
            >
              <MenuItems
                class="absolute bottom-full left-0 z-10 mb-2 w-full overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 dark:bg-gray-700"
              >
                <MenuItem v-slot="{ active }">
                  <RouterLink
                    to="/profile"
                    :class="[
                      active ? 'bg-gray-50 dark:bg-gray-600' : '',
                      'block px-4 py-2 text-sm text-gray-700 dark:text-gray-200'
                    ]"
                  >
                    Your Profile
                  </RouterLink>
                </MenuItem>
                <MenuItem v-slot="{ active }">
                  <button
                    @click="handleLogout"
                    :class="[
                      active ? 'bg-gray-50 dark:bg-gray-600' : '',
                      'block w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-200'
                    ]"
                  >
                    Sign out
                  </button>
                </MenuItem>
              </MenuItems>
            </transition>
          </Menu>
        </div>
      </div>
    </aside>

    <!-- Mobile sidebar overlay -->
    <div
      v-show="sidebarOpen"
      class="fixed inset-0 z-10 bg-gray-900/50"
      @click="sidebarOpen = false"
    />

    <!-- Main content -->
    <div class="flex flex-1 flex-col lg:pl-64">
      <!-- Top bar -->
      <div class="sticky top-0 z-10 flex h-16 flex-shrink-0 border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <button
          @click="sidebarOpen = true"
          class="px-4 text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-green-500 lg:hidden dark:text-gray-400 dark:hover:text-gray-200"
        >
          <span class="sr-only">Open sidebar</span>
          <Bars3Icon class="h-6 w-6" />
        </button>

        <!-- Search -->
        <div class="flex flex-1 items-center justify-between px-4 sm:px-6 lg:px-8">
          <div class="flex flex-1">
            <div class="w-full max-w-2xl">
              <label for="search" class="sr-only">Search</label>
              <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                  <MagnifyingGlassIcon
                    class="h-5 w-5 text-gray-400"
                    aria-hidden="true"
                  />
                </div>
                <input
                  id="search"
                  name="search"
                  class="block w-full rounded-md border-0 py-1.5 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-500 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:placeholder:text-gray-400 dark:focus:ring-green-500"
                  placeholder="Search"
                  type="search"
                />
              </div>
            </div>
          </div>

          <div class="ml-4 flex items-center space-x-4">
            <!-- Dark mode toggle -->
            <button
              @click="toggleDarkMode"
              class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:text-gray-400 dark:hover:bg-gray-700"
            >
              <SunIcon v-if="isDarkMode" class="h-5 w-5" />
              <MoonIcon v-else class="h-5 w-5" />
            </button>

            <!-- Notifications -->
            <button
              class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:text-gray-400 dark:hover:bg-gray-700"
            >
              <BellIcon class="h-5 w-5" />
            </button>
          </div>
        </div>
      </div>

      <!-- Page content -->
      <main class="flex-1">
        <div class="py-6">
          <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <slot></slot>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import {
  Bars3Icon,
  BellIcon,
  MoonIcon,
  SunIcon,
  MagnifyingGlassIcon,
  ChartBarIcon,
  ShoppingCartIcon,
  ClipboardDocumentListIcon,
  UserGroupIcon,
  Cog6ToothIcon,
  HomeIcon
} from '@heroicons/vue/24/outline'
import { useAuthStore } from '@/stores/auth'
import { useDarkMode } from '@/composables/useDarkMode'

const router = useRouter()
const auth = useAuthStore()
const { isDarkMode, toggleDarkMode } = useDarkMode()

const sidebarOpen = ref(false)
const user = computed(() => auth.user)
const userAvatar = computed(() => `https://ui-avatars.com/api/?name=${encodeURIComponent(user.value?.name || '')}&background=16A34A&color=fff`)

const navigation = [
  { name: 'dashboard', to: '/dashboard', text: 'Dashboard', icon: HomeIcon },
  { name: 'pos', to: '/pos', text: 'POS', icon: ShoppingCartIcon },
  { name: 'inventory', to: '/inventory', text: 'Inventory', icon: ClipboardDocumentListIcon },
  { name: 'sales', to: '/sales', text: 'Sales', icon: ChartBarIcon },
  { name: 'clients', to: '/clients', text: 'Clients', icon: UserGroupIcon },
  { name: 'settings', to: '/settings', text: 'Settings', icon: Cog6ToothIcon }
]

async function handleLogout() {
  await auth.logout()
  router.push('/login')
}
</script>