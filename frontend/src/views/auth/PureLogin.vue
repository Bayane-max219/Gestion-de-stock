<template>
  <div class="login-container">
    <div class="login-card">
      <div class="logo-section">
        <div class="logo-circle">
          <svg class="logo-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
          </svg>
        </div>
        <h1 class="app-title">SmartERP Pro</h1>
        <p class="app-subtitle">Gestion Moderne pour Boutiques Malgaches</p>
      </div>

      <div class="form-section">
        <form @submit.prevent="handleLogin" class="login-form">
          <div class="form-group">
            <label for="email">Email</label>
            <input
              id="email"
              v-model="email"
              type="email"
              required
              placeholder="admin@demo.com"
            />
          </div>

          <div class="form-group">
            <label for="password">Mot de passe</label>
            <input
              id="password"
              v-model="password"
              type="password"
              required
              placeholder="password123"
            />
          </div>

          <button type="submit" :disabled="isLoading" class="login-btn">
            <span v-if="isLoading" class="loading">Connexion en cours...</span>
            <span v-else>Se connecter</span>
          </button>

          <div class="demo-info">
            <p class="demo-title">🏪 Demo Boutique Malgache</p>
            <p class="demo-credentials">admin@demo.com / password123</p>
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
    console.log('Tentative de connexion avec:', email.value, password.value)
    
    if (email.value === 'admin@demo.com' && password.value === 'password123') {
      await new Promise(resolve => setTimeout(resolve, 1500))
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

<style scoped>
.login-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #065f46 0%, #047857 50%, #059669 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.login-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  overflow: hidden;
  width: 100%;
  max-width: 400px;
}

.logo-section {
  background: linear-gradient(135deg, #065f46, #047857);
  padding: 40px 20px;
  text-align: center;
  color: white;
}

.logo-circle {
  width: 80px;
  height: 80px;
  background: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.logo-icon {
  width: 40px;
  height: 40px;
  color: #047857;
  stroke-width: 2;
}

.app-title {
  font-size: 28px;
  font-weight: 700;
  margin: 0 0 8px 0;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.app-subtitle {
  font-size: 14px;
  opacity: 0.9;
  margin: 0;
}

.form-section {
  padding: 40px 30px;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
}

.form-group input {
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 16px;
  transition: all 0.2s;
  outline: none;
}

.form-group input:focus {
  border-color: #047857;
  box-shadow: 0 0 0 3px rgba(4, 120, 87, 0.1);
}

.login-btn {
  background: linear-gradient(135deg, #047857, #059669);
  color: white;
  border: none;
  padding: 14px 20px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 4px 12px rgba(4, 120, 87, 0.3);
}

.login-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(4, 120, 87, 0.4);
}

.login-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.loading {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.demo-info {
  background: #f0fdf4;
  border: 2px solid #bbf7d0;
  border-radius: 8px;
  padding: 16px;
  text-align: center;
  margin-top: 10px;
}

.demo-title {
  font-size: 14px;
  font-weight: 600;
  color: #15803d;
  margin: 0 0 4px 0;
}

.demo-credentials {
  font-size: 12px;
  color: #16a34a;
  margin: 0;
  font-family: monospace;
}

@media (max-width: 480px) {
  .login-container {
    padding: 10px;
  }
  
  .form-section {
    padding: 30px 20px;
  }
  
  .logo-section {
    padding: 30px 20px;
  }
}
</style>
