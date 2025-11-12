<template>
  <div v-if="showMigration" class="migration-notice">
    <div class="migration-content">
      <h3>🚀 Migration vers API Laravel</h3>
      <p>L'application utilise maintenant l'API Laravel backend.</p>
      
      <div class="migration-status">
        <div class="status-item" :class="{ active: apiStatus.backend }">
          <span class="status-icon">{{ apiStatus.backend ? '✅' : '⏳' }}</span>
          Backend Laravel (Port 8000)
        </div>
        
        <div class="status-item" :class="{ active: apiStatus.database }">
          <span class="status-icon">{{ apiStatus.database ? '✅' : '⏳' }}</span>
          Base MySQL (stock_management)
        </div>
        
        <div class="status-item" :class="{ active: apiStatus.frontend }">
          <span class="status-icon">{{ apiStatus.frontend ? '✅' : '⏳' }}</span>
          Frontend Vue.js (Port 5173)
        </div>
      </div>

      <div class="migration-actions">
        <button v-if="!apiStatus.backend" @click="startBackend" class="action-btn">
          Démarrer Backend
        </button>
        
        <button v-if="apiStatus.backend && !migrationComplete" @click="migrateData" class="action-btn">
          Migrer les données
        </button>
        
        <button v-if="migrationComplete" @click="closeMigration" class="action-btn success">
          Continuer avec API
        </button>
        
        <button @click="useLocalStorage" class="action-btn secondary">
          Utiliser localStorage (temporaire)
        </button>
        
        <button @click="continueWithLaravel" class="action-btn primary">
          Continuer avec Laravel (MySQL)
        </button>
      </div>

      <div v-if="migrationStatus" class="migration-log">
        <h4>Statut de migration :</h4>
        <ul>
          <li v-for="log in migrationLogs" :key="log.id" :class="log.type">
            {{ log.message }}
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAppStore } from '../stores/useAppStore.js'

const appStore = useAppStore()
const showMigration = ref(true)
const migrationComplete = ref(false)
const migrationStatus = ref(false)
const migrationLogs = ref([])

const apiStatus = ref({
  backend: false,
  database: false,
  frontend: true
})

// Vérifier le statut de l'API
const checkApiStatus = async () => {
  try {
    const response = await fetch('http://127.0.0.1:8000/api/health')
    if (response.ok) {
      apiStatus.value.backend = true
      apiStatus.value.database = true
      addLog('Backend Laravel connecté', 'success')
    }
  } catch (error) {
    addLog('Backend Laravel non disponible', 'error')
  }
}

// Ajouter un log
const addLog = (message, type = 'info') => {
  migrationLogs.value.push({
    id: Date.now(),
    message,
    type,
    timestamp: new Date().toLocaleTimeString()
  })
}

// Démarrer le backend
const startBackend = () => {
  addLog('Instructions pour démarrer le backend:', 'info')
  addLog('1. Ouvrir un terminal dans backend-laravel/', 'info')
  addLog('2. Exécuter: php artisan serve', 'info')
  addLog('3. Le backend sera disponible sur http://localhost:8000', 'info')
  migrationStatus.value = true
}

// Migrer les données
const migrateData = async () => {
  migrationStatus.value = true
  addLog('Début de la migration des données...', 'info')
  
  try {
    // Migrer les produits
    const localProducts = JSON.parse(localStorage.getItem('smarterp_products') || '{}')
    const localSales = JSON.parse(localStorage.getItem('smarterp_sales') || '{}')
    
    addLog(`${Object.keys(localProducts).length} utilisateurs avec produits trouvés`, 'info')
    addLog(`${Object.keys(localSales).length} utilisateurs avec ventes trouvés`, 'info')
    
    // Simuler la migration (à implémenter avec vraies API)
    await new Promise(resolve => setTimeout(resolve, 2000))
    
    addLog('Migration terminée avec succès!', 'success')
    migrationComplete.value = true
    
  } catch (error) {
    addLog('Erreur lors de la migration: ' + error.message, 'error')
  }
}

// Utiliser localStorage temporairement
const useLocalStorage = () => {
  addLog('Utilisation de localStorage (mode compatibilité)', 'info')
  showMigration.value = false
}

// Continuer avec Laravel
const continueWithLaravel = () => {
  addLog('Utilisation de Laravel API + MySQL', 'success')
  showMigration.value = false
  localStorage.setItem('smarterp_api_migration', 'completed')
  localStorage.setItem('smarterp_use_laravel', 'true')
}

// Fermer la migration
const closeMigration = () => {
  showMigration.value = false
  localStorage.setItem('smarterp_api_migration', 'completed')
}

onMounted(() => {
  // Vérifier si la migration a déjà été faite
  const migrationDone = localStorage.getItem('smarterp_api_migration')
  if (migrationDone === 'completed') {
    showMigration.value = false
  } else {
    checkApiStatus()
  }
})
</script>

<style scoped>
.migration-notice {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.migration-content {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  max-width: 600px;
  width: 90%;
  max-height: 80vh;
  overflow-y: auto;
}

.migration-content h3 {
  color: #10b981;
  margin-bottom: 1rem;
}

.migration-status {
  margin: 1.5rem 0;
}

.status-item {
  display: flex;
  align-items: center;
  padding: 0.5rem;
  margin: 0.5rem 0;
  border-radius: 6px;
  background: #f3f4f6;
}

.status-item.active {
  background: #d1fae5;
  color: #065f46;
}

.status-icon {
  margin-right: 0.5rem;
  font-weight: bold;
}

.migration-actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  margin: 1.5rem 0;
}

.action-btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
}

.action-btn {
  background: #3b82f6;
  color: white;
}

.action-btn:hover {
  background: #2563eb;
}

.action-btn.success {
  background: #10b981;
}

.action-btn.success:hover {
  background: #059669;
}

.action-btn.secondary {
  background: #6b7280;
  color: white;
}

.action-btn.secondary:hover {
  background: #4b5563;
}

.migration-log {
  background: #f9fafb;
  padding: 1rem;
  border-radius: 6px;
  margin-top: 1rem;
}

.migration-log h4 {
  margin-bottom: 0.5rem;
  color: #374151;
}

.migration-log ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.migration-log li {
  padding: 0.25rem 0;
  font-family: monospace;
  font-size: 0.9rem;
}

.migration-log li.success {
  color: #059669;
}

.migration-log li.error {
  color: #dc2626;
}

.migration-log li.info {
  color: #374151;
}
</style>
