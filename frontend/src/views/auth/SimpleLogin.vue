<template>
  <div class="min-h-screen bg-gradient-to-br from-green-800 via-green-700 to-emerald-800 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="flex justify-center">
        <div class="bg-white rounded-full p-4 shadow-lg">
          <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
          </svg>
        </div>
      </div>
      <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
        SmartERP Pro
      </h2>
      <p class="mt-2 text-center text-sm text-green-200">
        Gestion Moderne pour Boutiques Malgaches
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow-2xl rounded-xl sm:px-10 border border-gray-100">
        <form class="space-y-6" @submit.prevent="handleLogin">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Email
            </label>
            <div class="mt-1">
              <input
                id="email"
                v-model="email"
                name="email"
                type="email"
                required
                placeholder="admin@demo.com"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Mot de passe
            </label>
            <div class="mt-1">
              <input
                id="password"
                v-model="password"
                name="password"
                type="password"
                required
                placeholder="password123"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              />
            </div>
          </div>

          <div>
            <button
              type="submit"
              :disabled="isLoading"
              class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 transition-all duration-200 shadow-lg hover:shadow-xl"
            >
              <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                <svg v-if="!isLoading" class="h-5 w-5 text-green-300 group-hover:text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                <svg v-else class="animate-spin h-5 w-5 text-green-300" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </span>
              <span v-if="isLoading">Connexion en cours...</span>
              <span v-else>Se connecter</span>
            </button>
          </div>

          <div class="text-center">
            <div class="bg-green-50 rounded-lg p-3 border border-green-200">
              <p class="text-sm text-green-700 font-medium">
                🏪 Demo Boutique Malgache
              </p>
              <p class="text-xs text-green-600 mt-1">
                admin@demo.com / password123
              </p>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const email = ref('admin@demo.com')
const password = ref('password123')
const isLoading = ref(false)

async function handleLogin() {
  isLoading.value = true
  
  try {
    // Simulation de connexion
    console.log('Tentative de connexion avec:', email.value, password.value)
    
    // Vérification des identifiants de demo
    if (email.value === 'admin@demo.com' && password.value === 'password123') {
      // Simulation d'attente réseau
      await new Promise(resolve => setTimeout(resolve, 1500))
      
      // Redirection vers le dashboard
      window.location.href = '/dashboard'
    } else {
      throw new Error('Identifiants incorrects')
    }
    
  } catch (error) {
    console.error('Erreur de connexion:', error)
    alert('Erreur: Utilisez admin@demo.com / password123')
  } finally {
    isLoading.value = false
  }
}
</script>
