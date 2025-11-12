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
        <!-- Tabs Navigation -->
        <div class="tabs-nav">
          <button 
            @click="activeTab = 'login'" 
            :class="['tab-btn', { active: activeTab === 'login' }]"
          >
            🔑 Connexion
          </button>
          <button 
            @click="activeTab = 'register'" 
            :class="['tab-btn', { active: activeTab === 'register' }]"
          >
            📝 Inscription
          </button>
        </div>

        <!-- Login Form -->
        <form v-if="activeTab === 'login'" @submit.prevent="handleLogin" class="auth-form">
          <div class="form-group">
            <label for="email">Email</label>
            <input
              id="email"
              v-model="loginData.email"
              type="email"
              required
              placeholder="admin@demo.com"
            />
          </div>

          <div class="form-group">
            <label for="password">Mot de passe</label>
            <div class="password-input-container">
              <input
                id="password"
                v-model="loginData.password"
                :type="showLoginPassword ? 'text' : 'password'"
                required
                placeholder="password123"
              />
              <button 
                type="button" 
                class="password-toggle"
                @click="showLoginPassword = !showLoginPassword"
              >
                <span v-if="showLoginPassword">👁️</span>
                <span v-else>🙈</span>
              </button>
            </div>
          </div>

          <button type="submit" :disabled="isLoading" class="auth-btn login">
            <span v-if="isLoading" class="loading">Connexion en cours...</span>
            <span v-else>Se connecter</span>
          </button>

          <div class="demo-info">
            <p class="demo-title">🏪 Demo Boutique Malgache</p>
            <p class="demo-credentials">admin@demo.com / password123</p>
            <button @click="showDebugAccounts" class="debug-btn" type="button">
              🔍 Voir comptes enregistrés
            </button>
          </div>
        </form>

        <!-- Register Form -->
        <form v-if="activeTab === 'register'" @submit.prevent="handleRegister" class="auth-form">
          <div class="form-row">
            <div class="form-group">
              <label for="firstName">Prénom *</label>
              <input
                id="firstName"
                v-model="registerData.firstName"
                type="text"
                required
                placeholder="Rakoto"
              />
            </div>
            <div class="form-group">
              <label for="lastName">Nom *</label>
              <input
                id="lastName"
                v-model="registerData.lastName"
                type="text"
                required
                placeholder="Andry"
              />
            </div>
          </div>

          <div class="form-group">
            <label for="regEmail">Email *</label>
            <input
              id="regEmail"
              v-model="registerData.email"
              type="email"
              required
              placeholder="rakoto@boutique.mg"
            />
          </div>

          <div class="form-group">
            <label for="businessName">Nom de l'entreprise *</label>
            <input
              id="businessName"
              v-model="registerData.businessName"
              type="text"
              required
              placeholder="Boutique Rakoto & Fils"
            />
          </div>

          <div class="form-group">
            <label for="businessType">Type d'entreprise</label>
            <select
              id="businessType"
              v-model="registerData.businessType"
              required
            >
              <option value="">Sélectionner le type</option>
              <option value="epicerie">Épicerie</option>
              <option value="superette">Superette</option>
              <option value="quincaillerie">Quincaillerie</option>
              <option value="depot">Dépôt (Gros/Détail)</option>
              <option value="pharmacie">Pharmacie</option>
              <option value="autre">Autre</option>
            </select>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="phone">Téléphone</label>
              <input
                id="phone"
                v-model="registerData.phone"
                type="tel"
                placeholder="+261 34 12 345 67"
              />
            </div>
            <div class="form-group">
              <label for="city">Ville</label>
              <input
                id="city"
                v-model="registerData.city"
                type="text"
                placeholder="Antananarivo"
              />
            </div>
          </div>

          <div class="form-group">
            <label for="regPassword">Mot de passe *</label>
            <div class="password-input-container">
              <input
                id="regPassword"
                v-model="registerData.password"
                :type="showRegisterPassword ? 'text' : 'password'"
                required
                placeholder="Minimum 6 caractères"
                minlength="6"
              />
              <button 
                type="button" 
                class="password-toggle"
                @click="showRegisterPassword = !showRegisterPassword"
              >
                <span v-if="showRegisterPassword">👁️</span>
                <span v-else>🙈</span>
              </button>
            </div>
          </div>

          <div class="form-group">
            <label for="confirmPassword">Confirmer mot de passe *</label>
            <div class="password-input-container">
              <input
                id="confirmPassword"
                v-model="registerData.confirmPassword"
                :type="showConfirmPassword ? 'text' : 'password'"
                required
                placeholder="Retaper le mot de passe"
              />
              <button 
                type="button" 
                class="password-toggle"
                @click="showConfirmPassword = !showConfirmPassword"
              >
                <span v-if="showConfirmPassword">👁️</span>
                <span v-else>🙈</span>
              </button>
            </div>
          </div>

          <div class="form-group checkbox-group">
            <label class="checkbox-label">
              <input
                v-model="registerData.acceptTerms"
                type="checkbox"
                required
              />
              <span class="checkmark"></span>
              J'accepte les <a href="#" class="terms-link">conditions d'utilisation</a>
            </label>
          </div>

          <button type="submit" :disabled="isLoading || !canRegister" class="auth-btn register">
            <span v-if="isLoading" class="loading">Inscription en cours...</span>
            <span v-else>Créer mon compte</span>
          </button>

          <div class="register-info">
            <p class="info-text">🚀 Démarrez votre gestion moderne dès aujourd'hui !</p>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAppStore } from '@/stores/useAppStore'

const router = useRouter()

// Tab management
const activeTab = ref('login')

// Login data
const loginData = ref({
  email: 'admin@demo.com',
  password: 'password123'
})

// Password visibility
const showLoginPassword = ref(false)
const showRegisterPassword = ref(false)
const showConfirmPassword = ref(false)

// Stockage des comptes créés (avec localStorage pour persistance)
const getStoredAccounts = () => {
  const stored = localStorage.getItem('smarterp_accounts')
  console.log('Récupération localStorage:', stored)
  if (stored) {
    const accounts = JSON.parse(stored)
    console.log('Comptes récupérés:', accounts)
    return accounts
  }
  const defaultAccounts = [{ email: 'admin@demo.com', password: 'password123' }]
  console.log('Comptes par défaut:', defaultAccounts)
  return defaultAccounts
}

const registeredAccounts = ref(getStoredAccounts())
console.log('Comptes initialisés:', registeredAccounts.value)

// Sauvegarder les comptes dans localStorage
const saveAccounts = () => {
  localStorage.setItem('smarterp_accounts', JSON.stringify(registeredAccounts.value))
}

// Register data
const registerData = ref({
  firstName: '',
  lastName: '',
  email: '',
  businessName: '',
  businessType: '',
  phone: '',
  city: '',
  password: '',
  confirmPassword: '',
  acceptTerms: false
})

const isLoading = ref(false)

// Computed properties
const canRegister = computed(() => {
  return registerData.value.firstName.trim() !== '' &&
         registerData.value.lastName.trim() !== '' &&
         registerData.value.email.trim() !== '' &&
         registerData.value.businessName.trim() !== '' &&
         registerData.value.businessType !== '' &&
         registerData.value.password.length >= 6 &&
         registerData.value.password === registerData.value.confirmPassword &&
         registerData.value.acceptTerms
})

// Login function - UTILISE L'API LARAVEL
async function handleLogin() {
  isLoading.value = true
  
  try {
    // Appel API Laravel via le store
    const { login } = useAppStore()
    const user = await login(loginData.value.email, loginData.value.password)
    
    console.log('Connexion réussie via API Laravel:', user)
    router.push('/dashboard')
    
  } catch (error) {
    console.error('Erreur de connexion API:', error)
    alert('Erreur: ' + error.message)
  } finally {
    isLoading.value = false
  }
}

// Register function - UTILISE L'API LARAVEL
async function handleRegister() {
  isLoading.value = true
  
  try {
    // Appel API Laravel via le store
    const { register } = useAppStore()
    const user = await register(registerData.value)
    
    console.log('Inscription réussie via API Laravel:', user)
    alert(`✅ Inscription réussie !\n\nBienvenue ${registerData.value.firstName} ${registerData.value.lastName} !\n\nVous pouvez maintenant vous connecter.`)
    
    // Basculer vers l'onglet de connexion
    loginData.value.email = registerData.value.email
    loginData.value.password = registerData.value.password
    activeTab.value = 'login'
    resetRegisterForm()
    
  } catch (error) {
    console.error('Erreur d\'inscription API:', error)
    alert('Erreur: ' + error.message)
  } finally {
    isLoading.value = false
  }
}

function resetRegisterForm() {
  registerData.value = {
    firstName: '',
    lastName: '',
    email: '',
    businessName: '',
    businessType: '',
    phone: '',
    city: '',
    password: '',
    confirmPassword: '',
    acceptTerms: false
  }
}

function getBusinessTypeLabel(type) {
  const labels = {
    epicerie: 'Épicerie',
    superette: 'Superette',
    quincaillerie: 'Quincaillerie',
    depot: 'Dépôt (Gros/Détail)',
    pharmacie: 'Pharmacie',
    autre: 'Autre'
  }
  return labels[type] || type
}

function showDebugAccounts() {
  const accountsList = registeredAccounts.value.map((acc, index) => 
    `${index + 1}. ${acc.email} / ${acc.password} (${acc.firstName || 'Admin'} ${acc.lastName || ''})`
  ).join('\n')
  
  const message = `📋 Comptes enregistrés (${registeredAccounts.value.length}):\n\n${accountsList}\n\n🔧 Actions:\n• OK = Fermer\n• Annuler = Vider localStorage`
  
  if (!confirm(message)) {
    // L'utilisateur a cliqué Annuler - vider localStorage
    localStorage.removeItem('smarterp_accounts')
    localStorage.removeItem('smarterp_categories')
    registeredAccounts.value = [{ email: 'admin@demo.com', password: 'password123' }]
    alert('🗑️ localStorage vidé ! Seul le compte admin@demo.com reste.')
  }
}

// Créer les catégories par défaut selon le type d'entreprise
function createDefaultCategories(account) {
  const categoriesByType = {
    epicerie: [
      { name: 'Alimentaire', description: 'Produits alimentaires de base' },
      { name: 'Boissons', description: 'Boissons et rafraîchissements' },
      { name: 'Hygiène', description: 'Produits d\'hygiène personnelle' },
      { name: 'Ménager', description: 'Produits d\'entretien ménager' }
    ],
    superette: [
      { name: 'Alimentaire', description: 'Produits alimentaires' },
      { name: 'Boissons', description: 'Boissons et jus' },
      { name: 'Hygiène', description: 'Hygiène et cosmétiques' },
      { name: 'Ménager', description: 'Entretien ménager' },
      { name: 'Papeterie', description: 'Fournitures de bureau' }
    ],
    quincaillerie: [
      { name: 'Outils', description: 'Outils de bricolage et construction' },
      { name: 'Matériaux', description: 'Matériaux de construction' },
      { name: 'Électricité', description: 'Matériel électrique' },
      { name: 'Plomberie', description: 'Matériel de plomberie' },
      { name: 'Peinture', description: 'Peintures et accessoires' }
    ],
    depot: [
      { name: 'Alimentaire Gros', description: 'Produits alimentaires en gros' },
      { name: 'Boissons Gros', description: 'Boissons en gros' },
      { name: 'Hygiène Gros', description: 'Produits d\'hygiène en gros' },
      { name: 'Ménager Gros', description: 'Produits ménagers en gros' }
    ],
    pharmacie: [
      { name: 'Médicaments', description: 'Médicaments sur ordonnance' },
      { name: 'Parapharmacie', description: 'Produits de parapharmacie' },
      { name: 'Cosmétiques', description: 'Produits cosmétiques' },
      { name: 'Hygiène', description: 'Produits d\'hygiène' },
      { name: 'Matériel Médical', description: 'Matériel médical et orthopédique' }
    ],
    autre: [
      { name: 'Général', description: 'Produits généraux' },
      { name: 'Services', description: 'Services proposés' },
      { name: 'Accessoires', description: 'Accessoires divers' }
    ]
  }
  
  const defaultCategories = categoriesByType[account.businessType] || categoriesByType.autre
  
  // Sauvegarder les catégories dans localStorage
  const existingCategories = JSON.parse(localStorage.getItem('smarterp_categories') || '{}')
  existingCategories[account.email] = defaultCategories
  localStorage.setItem('smarterp_categories', JSON.stringify(existingCategories))
  
  console.log(`Catégories créées pour ${account.businessType}:`, defaultCategories)
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
  max-width: 500px;
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
  padding: 30px;
}

/* Tabs Navigation */
.tabs-nav {
  display: flex;
  margin-bottom: 30px;
  border-bottom: 2px solid #f3f4f6;
}

.tab-btn {
  flex: 1;
  background: none;
  border: none;
  padding: 15px 20px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  color: #6b7280;
  border-bottom: 3px solid transparent;
  transition: all 0.3s ease;
}

.tab-btn:hover {
  color: #047857;
  background: #f0fdf4;
}

.tab-btn.active {
  color: #047857;
  border-bottom-color: #047857;
  background: #f0fdf4;
}

/* Forms */
.auth-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
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

.form-group input,
.form-group select {
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 16px;
  transition: border-color 0.2s;
  background: white;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #047857;
}

.form-group select {
  cursor: pointer;
}

/* Checkbox styles */
.checkbox-group {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.checkbox-label {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  cursor: pointer;
  font-size: 14px;
  line-height: 1.5;
}

.checkbox-label input[type="checkbox"] {
  display: none;
}

.checkmark {
  width: 20px;
  height: 20px;
  border: 2px solid #e5e7eb;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
  flex-shrink: 0;
  margin-top: 2px;
}

.checkbox-label input[type="checkbox"]:checked + .checkmark {
  background: #047857;
  border-color: #047857;
}

.checkbox-label input[type="checkbox"]:checked + .checkmark::after {
  content: '✓';
  color: white;
  font-weight: bold;
  font-size: 12px;
}

.terms-link {
  color: #047857;
  text-decoration: none;
  font-weight: 600;
}

.terms-link:hover {
  text-decoration: underline;
}

/* Auth buttons */
.auth-btn {
  border: none;
  padding: 14px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
}

.auth-btn.login {
  background: #047857;
  color: white;
}

.auth-btn.login:hover:not(:disabled) {
  background: #065f46;
}

.auth-btn.register {
  background: #3b82f6;
  color: white;
}

.auth-btn.register:hover:not(:disabled) {
  background: #2563eb;
}

.auth-btn:disabled {
  background: #9ca3af;
  cursor: not-allowed;
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
  color: #6b7280;
  margin: 0 0 10px 0;
  font-family: monospace;
}

.debug-btn {
  background: #6b7280;
  color: white;
  border: none;
  padding: 5px 10px;
  border-radius: 4px;
  font-size: 11px;
  cursor: pointer;
  margin-top: 5px;
}

.debug-btn:hover {
  background: #4b5563;
}

/* Password input with toggle */
.password-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.password-input-container input {
  width: 100%;
  padding-right: 45px; /* Espace pour le bouton œil */
}

.password-toggle {
  position: absolute;
  right: 12px;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 16px;
  padding: 4px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.2s;
  z-index: 1;
}

.password-toggle:hover {
  background-color: rgba(0, 0, 0, 0.05);
}

.password-toggle:focus {
  outline: 2px solid #10b981;
  outline-offset: 2px;
}

.password-toggle span {
  display: block;
  line-height: 1;
}

/* Register info */
.register-info {
  background: linear-gradient(135deg, #eff6ff, #dbeafe);
  padding: 15px;
  border-radius: 8px;
  text-align: center;
  border: 1px solid #bfdbfe;
}

.info-text {
  color: #1e40af;
  font-weight: 600;
  margin: 0;
  font-size: 14px;
}

/* Responsive */
@media (max-width: 640px) {
  .login-card {
    max-width: 100%;
    margin: 10px;
  }
  
  .form-section {
    padding: 20px;
  }
  
  .form-row {
    grid-template-columns: 1fr;
    gap: 15px;
  }
  
  .tab-btn {
    padding: 12px 15px;
    font-size: 14px;
  }
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
