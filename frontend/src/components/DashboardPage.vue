<template>
  <div class="dashboard-container">
    <!-- Header -->
    <header class="header">
      <div class="header-content">
        <div class="header-left">
          <div class="logo">
            <svg class="logo-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <h1>SmartERP Pro</h1>
          </div>
        </div>
        <div class="header-right">
          <span class="user-info">
            {{ currentUser?.firstName || 'Admin' }} {{ currentUser?.lastName || '' }} - 
            {{ currentUser?.businessName || 'Boutique Demo' }}
          </span>
          <button @click="refreshDashboard" class="refresh-btn">🔄 Actualiser</button>
          <button @click="handleLogout" class="logout-btn">Déconnexion</button>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Welcome Section -->
      <div class="welcome-section">
        <h2 v-if="isNewUser">
          Bienvenue {{ currentUser?.firstName }} ! 🎉
        </h2>
        <h2 v-else>
          Bienvenue dans votre boutique ! 🏪
        </h2>
        <p v-if="isNewUser">
          Félicitations ! Votre compte {{ currentUser?.businessName }} est prêt. 
          Commencez par ajouter vos premiers produits dans la gestion de stock.
        </p>
        <p v-else>
          Tableau de bord - Gestion moderne pour boutiques malgaches
        </p>
      </div>

      <!-- Stats Cards -->
      <div class="stats-grid">
        <div class="stat-card green clickable" @click="showCADetails">
          <div class="stat-icon">💰</div>
          <div class="stat-info">
            <h3>CA Aujourd'hui</h3>
            <p class="stat-value" v-if="!loading">{{ formatPrice(dashboardData.todayRevenue) }}</p>
            <p class="stat-value loading" v-else>Chargement...</p>
          </div>
        </div>

        <div class="stat-card blue clickable" @click="showSalesDetails">
          <div class="stat-icon">🛍️</div>
          <div class="stat-info">
            <h3>Ventes Aujourd'hui</h3>
            <p class="stat-value">{{ dashboardData.todayTransactions }}</p>
          </div>
        </div>

        <div class="stat-card yellow clickable" @click="showStockDetails">
          <div class="stat-icon">📦</div>
          <div class="stat-info">
            <h3>Produits en Stock</h3>
            <p class="stat-value">{{ dashboardData.totalProducts }}</p>
          </div>
        </div>

        <div class="stat-card purple clickable" @click="showClientsDetails">
          <div class="stat-icon">👥</div>
          <div class="stat-info">
            <h3>Clients Actifs</h3>
            <p class="stat-value">{{ dashboardData.totalClients }}</p>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="actions-grid">
        <div class="action-card">
          <h3>🛍️ Nouvelle Vente</h3>
          <p>Créer une nouvelle facture de vente</p>
          <button @click="showNewSale" class="action-btn green">Nouvelle Vente</button>
        </div>

        <div class="action-card">
          <h3>📦 Gestion Stock</h3>
          <p>Voir et gérer l'inventaire</p>
          <button @click="showStock" class="action-btn blue">Voir Stock</button>
        </div>

        <div class="action-card">
          <h3>📊 Rapports</h3>
          <p>Statistiques et analyses</p>
          <button @click="showReports" class="action-btn purple">Voir Rapports</button>
        </div>
      </div>

      <!-- Recent Sales -->
      <div class="recent-sales">
        <div class="section-header">
          <h3>Ventes Récentes</h3>
        </div>
        
        <!-- Section vide pour nouveaux utilisateurs -->
        <div v-if="isNewUser" class="empty-sales">
          <div class="empty-icon">🧾</div>
          <h4>Aucune vente récente</h4>
          <p>Vos dernières ventes apparaîtront ici</p>
          <button @click="showNewSale" class="start-selling-btn">
            🛍️ Commencer à vendre
          </button>
        </div>
        
        <!-- Ventes pour compte demo -->
        <div v-else class="sales-list">
          <div class="sale-item">
            <div class="sale-info">
              <h4>Facture #001</h4>
              <p>Client: Rakoto Jean</p>
            </div>
            <div class="sale-amount">
              <span class="amount">15,500 Ar</span>
              <span class="time">Il y a 2h</span>
            </div>
          </div>

          <div class="sale-item">
            <div class="sale-info">
              <h4>Facture #002</h4>
              <p>Client: Rabe Marie</p>
            </div>
            <div class="sale-amount">
              <span class="amount">8,200 Ar</span>
              <span class="time">Il y a 3h</span>
            </div>
          </div>

          <div class="sale-item">
            <div class="sale-info">
              <h4>Facture #003</h4>
              <p>Client: Andry Paul</p>
            </div>
            <div class="sale-amount">
              <span class="amount">22,800 Ar</span>
              <span class="time">Il y a 4h</span>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, onActivated, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAppStore } from '../stores/useAppStore.js'

const router = useRouter()
const appStore = useAppStore()
const dashboardData = ref({
  todayRevenue: 0,
  todayTransactions: 0,
  totalProducts: 0,
  totalClients: 0
})

// Computed properties
const currentUser = computed(() => appStore.currentUser)
const isNewUser = computed(() => appStore.currentUser?.email !== 'admin@demo.com')
const loading = computed(() => appStore.loading)

// Charger les ventes depuis localStorage
const loadUserSales = () => {
  if (!currentUser.value) return []
  
  const allSales = JSON.parse(localStorage.getItem('smarterp_sales') || '{}')
  const userSales = allSales[currentUser.value.email] || []
  
  console.log('Dashboard - Ventes chargées pour', currentUser.value.email, ':', userSales.length)
  return userSales
}

// Charger les produits depuis localStorage
const loadUserProducts = () => {
  if (!currentUser.value) return []
  
  const allProducts = JSON.parse(localStorage.getItem('smarterp_products') || '{}')
  const userProducts = allProducts[currentUser.value.email] || []
  
  console.log('Dashboard - Produits chargés pour', currentUser.value.email, ':', userProducts.length)
  return userProducts
}

// Calculer les données du dashboard
const calculateDashboardData = () => {
  const sales = loadUserSales()
  const products = loadUserProducts()
  
  // Calculer les ventes d'aujourd'hui
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  
  let todayRevenue = 0
  let todayTransactions = 0
  const todayClients = new Set()
  
  sales.forEach(sale => {
    // Utiliser created_at au lieu de timestamp
    const saleDate = new Date(sale.created_at || sale.timestamp)
    if (saleDate >= today) {
      todayRevenue += (sale.total || 0)
      todayTransactions++
      
      // Compter les clients uniques
      if (sale.customer_name && sale.customer_name.trim() !== '') {
        todayClients.add(sale.customer_name.trim())
      } else if (sale.customerName && sale.customerName.trim() !== '') {
        todayClients.add(sale.customerName.trim())
      } else {
        todayClients.add('Client-' + sale.id)
      }
    }
  })
  
  dashboardData.value = {
    todayRevenue,
    todayTransactions,
    totalProducts: products.length,
    totalClients: todayClients.size
  }
  
  console.log('Dashboard - Données calculées:', dashboardData.value)
}

// Formater les prix
const formatPrice = (amount) => {
  return amount.toLocaleString() + ' Ar'
}

// Fonction pour rafraîchir le dashboard
const refreshDashboard = () => {
  console.log('Rafraîchissement dashboard...')
  calculateDashboardData()
}

// Charger les données au montage du composant
onMounted(async () => {
  // L'utilisateur est déjà chargé via l'authentification API
  
  if (appStore.currentUser) {
    console.log('Utilisateur connecté:', appStore.currentUser)
    
    // Utiliser directement le calcul local (plus fiable)
    calculateDashboardData()
  }
})

// Rafraîchir automatiquement quand on revient sur la page
onActivated(() => {
  if (appStore.currentUser) {
    refreshDashboard()
  }
})

function handleLogout() {
  const confirmLogout = confirm('Voulez-vous vraiment vous déconnecter ?')
  if (confirmLogout) {
    // Effacer l'utilisateur connecté
    localStorage.removeItem('smarterp_current_user')
    router.push('/login')
  }
}

function showNewSale() {
  console.log('Bouton Nouvelle Vente cliqué - Redirection vers page de vente')
  router.push('/sales')
}

function showStock() {
  router.push('/stock')
}

function showReports() {
  router.push('/reports')
}

function showCADetails() {
  const revenue = dashboardData.value.todayRevenue || 0
  const transactions = dashboardData.value.todayTransactions || 0
  
  if (revenue === 0) {
    alert('💰 Chiffre d\'Affaires\n\n🎯 Votre boutique démarre !\n\n• Aucune vente aujourd\'hui\n• Commencez par créer votre première vente\n• Ajoutez vos produits dans la gestion de stock')
  } else {
    const avgTicket = transactions > 0 ? Math.round(revenue / transactions) : 0
    alert(`💰 Détails CA Aujourd\'hui\n\n• Total: ${revenue.toLocaleString()} Ar\n• Nombre de transactions: ${transactions}\n• Ticket moyen: ${avgTicket.toLocaleString()} Ar\n• Statut: ${revenue > 50000 ? 'Excellente journée !' : 'Bon début !'}`)
  }
}

function showSalesDetails() {
  const transactions = dashboardData.value.todayTransactions || 0
  const sales = loadUserSales()
  
  if (transactions === 0) {
    alert('🛍️ Ventes\n\n🚀 Prêt à commencer !\n\n• Aucune vente enregistrée\n• Cliquez sur "Nouvelle Vente" pour commencer\n• Scannez ou saisissez vos produits')
  } else {
    const todaySales = sales.filter(sale => {
      const saleDate = new Date(sale.created_at || sale.timestamp)
      return saleDate.toDateString() === new Date().toDateString()
    })
    
    let details = `🛍️ Détails Ventes Aujourd\'hui\n\n• Total ventes: ${transactions}\n`
    
    if (todaySales.length > 0) {
      details += `• Première vente: ${new Date(todaySales[0].created_at || todaySales[0].timestamp).toLocaleTimeString()}\n`
      details += `• Dernière vente: ${new Date(todaySales[todaySales.length - 1].created_at || todaySales[todaySales.length - 1].timestamp).toLocaleTimeString()}\n`
      details += `• Clients servis: ${new Set(todaySales.map(s => s.customer_name || s.customerName).filter(n => n)).size}`
    }
    
    alert(details)
  }
}

function showStockDetails() {
  const totalProducts = dashboardData.value.totalProducts || 0
  const products = loadUserProducts()
  
  if (totalProducts === 0) {
    alert('📦 Stock\n\n📋 Votre inventaire est vide\n\n• Aucun produit en stock\n• Allez dans "Gestion Stock" pour ajouter vos produits\n• Définissez vos catégories et prix')
  } else {
    // Calculer par catégorie
    const categories = {}
    let totalValue = 0
    
    products.forEach(product => {
      const category = product.category || 'Divers'
      categories[category] = (categories[category] || 0) + 1
      totalValue += (product.price || 0) * (product.stock || 0)
    })
    
    let details = `📦 Détails Stock\n\n• Total produits: ${totalProducts}\n• Catégories:\n`
    
    Object.entries(categories).forEach(([cat, count]) => {
      details += `  - ${cat}: ${count}\n`
    })
    
    details += `• Valeur totale: ${totalValue.toLocaleString()} Ar`
    
    alert(details)
  }
}

function showClientsDetails() {
  const totalClients = dashboardData.value.totalClients || 0
  const sales = loadUserSales()
  
  if (totalClients === 0) {
    alert('👥 Clients\n\n👋 Aucun client enregistré\n\n• Vos clients apparaîtront ici après les premières ventes\n• Gérez les ventes à crédit\n• Suivez les paiements')
  } else {
    // Analyser les clients
    const clientNames = sales
      .map(s => s.customer_name || s.customerName)
      .filter(name => name && name.trim() !== '')
    
    const uniqueClients = [...new Set(clientNames)]
    const clientFrequency = {}
    
    clientNames.forEach(name => {
      clientFrequency[name] = (clientFrequency[name] || 0) + 1
    })
    
    const topClients = Object.entries(clientFrequency)
      .sort(([,a], [,b]) => b - a)
      .slice(0, 3)
    
    let details = `👥 Détails Clients Actifs\n\n• Total clients: ${totalClients}\n`
    
    if (topClients.length > 0) {
      details += `• Clients les plus actifs:\n`
      topClients.forEach(([name, count]) => {
        details += `  - ${name}: ${count} achats\n`
      })
    }
    
    details += `• Ventes totales: ${sales.length}`
    
    alert(details)
  }
}

function showAlertsDetails() {
  if (isNewUser.value) {
    alert('⚠️ Alertes\n\n✅ Tout va bien !\n\n• Aucune alerte pour le moment\n• Les alertes de stock faible apparaîtront ici\n• Configurez vos seuils d\'alerte')
  } else {
    alert('⚠️ Alertes Stock Faible\n\n• Riz 25kg: 2 sacs restants\n• Huile tournesol 1L: 5 bouteilles\n• Savon Lux: 3 unités\n• Pâtes Teza: 4 paquets\n• Lait concentré: 6 boîtes\n\n→ Réapprovisionnement urgent!')
  }
}
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.dashboard-container {
  min-height: 100vh;
  background: #f8fafc;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Header */
.header {
  background: white;
  border-bottom: 1px solid #e2e8f0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.header-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-left {
  display: flex;
  align-items: center;
}

.logo {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo-icon {
  width: 32px;
  height: 32px;
  color: #047857;
  background: #d1fae5;
  padding: 6px;
  border-radius: 8px;
}

.logo h1 {
  font-size: 20px;
  font-weight: 700;
  color: #1f2937;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.user-info {
  font-size: 14px;
  color: #6b7280;
}

.logout-btn {
  background: #ef4444;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  transition: background 0.2s;
}

.logout-btn:hover {
  background: #dc2626;
}

/* Main Content */
.main-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.welcome-section {
  margin-bottom: 2rem;
}

.welcome-section h2 {
  font-size: 28px;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 8px;
}

.welcome-section p {
  color: #6b7280;
  font-size: 16px;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
  border-left: 4px solid;
}

.stat-card.green { border-left-color: #10b981; }
.stat-card.blue { border-left-color: #3b82f6; }
.stat-card.yellow { border-left-color: #f59e0b; }
.stat-card.red { border-left-color: #ef4444; }

.stat-card.clickable {
  cursor: pointer;
  transition: all 0.2s;
}

.stat-card.clickable:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stat-icon {
  font-size: 2rem;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: #f8fafc;
}

.stat-info h3 {
  font-size: 14px;
  color: #6b7280;
  margin-bottom: 4px;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
  color: #1f2937;
}

/* Actions Grid */
.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.action-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.action-card h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
}

.action-card p {
  color: #6b7280;
  margin-bottom: 1rem;
}

.action-btn {
  width: 100%;
  padding: 12px 20px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  color: white;
}

.action-btn.green {
  background: #10b981;
}

.action-btn.blue {
  background: #3b82f6;
}

.action-btn.purple {
  background: #8b5cf6;
}

.action-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Recent Sales */
.recent-sales {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.section-header {
  padding: 1.5rem;
  border-bottom: 1px solid #e2e8f0;
}

.section-header h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.sales-list {
  padding: 1.5rem;
}

/* Empty sales section */
.empty-sales {
  padding: 3rem 2rem;
  text-align: center;
  background: #f8fafc;
  border-top: 1px solid #e2e8f0;
}

.empty-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
  opacity: 0.6;
}

.empty-sales h4 {
  color: #374151;
  font-size: 1.2rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.empty-sales p {
  color: #6b7280;
  margin-bottom: 1.5rem;
}

.start-selling-btn {
  background: #10b981;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.start-selling-btn:hover {
  background: #059669;
}

.sale-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 0;
  border-bottom: 1px solid #f1f5f9;
}

.sale-item:last-child {
  border-bottom: none;
}

.sale-info h4 {
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.sale-info p {
  font-size: 14px;
  color: #6b7280;
}

.sale-amount {
  text-align: right;
}

.amount {
  display: block;
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.time {
  font-size: 14px;
  color: #6b7280;
}

/* Responsive */
@media (max-width: 768px) {
  .header-content {
    padding: 1rem;
    flex-direction: column;
    gap: 1rem;
  }

  .main-content {
    padding: 1rem;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .actions-grid {
    grid-template-columns: 1fr;
  }
}
</style>
