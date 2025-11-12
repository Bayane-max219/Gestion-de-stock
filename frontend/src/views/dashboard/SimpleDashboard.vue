<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <div class="flex items-center">
            <div class="bg-green-600 rounded-lg p-2 mr-3">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
              </svg>
            </div>
            <h1 class="text-xl font-bold text-gray-900">SmartERP Pro</h1>
          </div>
          <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600">Admin - Boutique Demo</span>
            <button @click="logout" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
              Déconnexion
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Dashboard Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Welcome Message -->
      <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Bienvenue dans votre boutique ! 🏪</h2>
        <p class="text-gray-600">Tableau de bord - Gestion moderne pour boutiques malgaches</p>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- CA du jour -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">CA Aujourd'hui</p>
              <p class="text-2xl font-bold text-gray-900">125,000 Ar</p>
            </div>
          </div>
        </div>

        <!-- Ventes du jour -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Ventes Aujourd'hui</p>
              <p class="text-2xl font-bold text-gray-900">47</p>
            </div>
          </div>
        </div>

        <!-- Produits en stock -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Produits en Stock</p>
              <p class="text-2xl font-bold text-gray-900">1,234</p>
            </div>
          </div>
        </div>

        <!-- Alertes stock -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Alertes Stock</p>
              <p class="text-2xl font-bold text-gray-900">8</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">🛍️ Nouvelle Vente</h3>
          <p class="text-gray-600 mb-4">Créer une nouvelle facture de vente</p>
          <button class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition-colors">
            Nouvelle Vente
          </button>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">📦 Gestion Stock</h3>
          <p class="text-gray-600 mb-4">Voir et gérer l'inventaire</p>
          <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors">
            Voir Stock
          </button>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">📊 Rapports</h3>
          <p class="text-gray-600 mb-4">Statistiques et analyses</p>
          <button class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg transition-colors">
            Voir Rapports
          </button>
        </div>
      </div>

      <!-- Recent Sales -->
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Ventes Récentes</h3>
        </div>
        <div class="p-6">
          <div class="space-y-4">
            <div class="flex items-center justify-between py-3 border-b border-gray-100">
              <div>
                <p class="font-medium text-gray-900">Facture #001</p>
                <p class="text-sm text-gray-500">Client: Rakoto Jean</p>
              </div>
              <div class="text-right">
                <p class="font-medium text-gray-900">15,500 Ar</p>
                <p class="text-sm text-gray-500">Il y a 2h</p>
              </div>
            </div>
            
            <div class="flex items-center justify-between py-3 border-b border-gray-100">
              <div>
                <p class="font-medium text-gray-900">Facture #002</p>
                <p class="text-sm text-gray-500">Client: Rabe Marie</p>
              </div>
              <div class="text-right">
                <p class="font-medium text-gray-900">8,200 Ar</p>
                <p class="text-sm text-gray-500">Il y a 3h</p>
              </div>
            </div>

            <div class="flex items-center justify-between py-3">
              <div>
                <p class="font-medium text-gray-900">Facture #003</p>
                <p class="text-sm text-gray-500">Client: Andry Paul</p>
              </div>
              <div class="text-right">
                <p class="font-medium text-gray-900">22,800 Ar</p>
                <p class="text-sm text-gray-500">Il y a 4h</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

function logout() {
  alert('Déconnexion simulée - Retour à la page de connexion')
  // Ici on redirigerait vers la page de login
  window.location.href = '/login'
}
</script>
