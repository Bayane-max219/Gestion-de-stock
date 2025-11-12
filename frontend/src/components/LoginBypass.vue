<template>
  <div class="bypass-container">
    <div class="bypass-card">
      <h2>🚀 Accès Direct - Demo Recruteur</h2>
      <p>Bypass temporaire pour tester l'application</p>
      
      <div class="bypass-options">
        <button @click="loginAsAdmin" class="bypass-btn admin">
          👨‍💼 Connexion Admin
        </button>
        
        <button @click="loginAsUser" class="bypass-btn user">
          👤 Connexion Utilisateur
        </button>
        
        <button @click="directAccess" class="bypass-btn direct">
          🎯 Accès Direct Dashboard
        </button>
      </div>

      <div class="bypass-info">
        <p><strong>Mode Demo :</strong> Architecture Laravel + Vue.js + PostgreSQL</p>
        <p><strong>Backend :</strong> http://localhost:8000</p>
        <p><strong>Frontend :</strong> http://localhost:5173</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAppStore } from '../stores/useAppStore.js'

const router = useRouter()
const appStore = useAppStore()

// Connexion admin
const loginAsAdmin = () => {
  const adminUser = {
    id: 1,
    name: 'Administrateur',
    email: 'admin@smarterp.com',
    role: 'admin'
  }
  
  appStore.setUser(adminUser)
  localStorage.setItem('smarterp_bypass_mode', 'admin')
  router.push('/dashboard')
}

// Connexion utilisateur
const loginAsUser = () => {
  const normalUser = {
    id: 2,
    name: 'Utilisateur Demo',
    email: 'user@smarterp.com',
    role: 'user'
  }
  
  appStore.setUser(normalUser)
  localStorage.setItem('smarterp_bypass_mode', 'user')
  router.push('/dashboard')
}

// Accès direct
const directAccess = () => {
  const demoUser = {
    id: 999,
    name: 'Demo Recruteur',
    email: 'demo@recruteur.com',
    role: 'demo'
  }
  
  appStore.setUser(demoUser)
  localStorage.setItem('smarterp_bypass_mode', 'demo')
  localStorage.setItem('smarterp_api_migration', 'completed')
  router.push('/dashboard')
}
</script>

<style scoped>
.bypass-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #10b981, #059669);
  padding: 20px;
}

.bypass-card {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  max-width: 500px;
  width: 100%;
  text-align: center;
}

.bypass-card h2 {
  color: #10b981;
  margin-bottom: 1rem;
}

.bypass-options {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin: 2rem 0;
}

.bypass-btn {
  padding: 1rem 2rem;
  border: none;
  border-radius: 8px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.bypass-btn.admin {
  background: #dc2626;
  color: white;
}

.bypass-btn.admin:hover {
  background: #b91c1c;
}

.bypass-btn.user {
  background: #3b82f6;
  color: white;
}

.bypass-btn.user:hover {
  background: #2563eb;
}

.bypass-btn.direct {
  background: #10b981;
  color: white;
}

.bypass-btn.direct:hover {
  background: #059669;
}

.bypass-info {
  background: #f3f4f6;
  padding: 1rem;
  border-radius: 8px;
  margin-top: 1.5rem;
}

.bypass-info p {
  margin: 0.5rem 0;
  font-size: 0.9rem;
  color: #374151;
}
</style>
