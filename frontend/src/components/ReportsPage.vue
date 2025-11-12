<template>
  <div class="reports-container">
    <!-- Header -->
    <header class="header">
      <div class="header-content">
        <div class="header-left">
          <button @click="goBack" class="back-btn">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Retour
          </button>
          <h1>📊 Rapports & Analyses</h1>
        </div>
        <div class="header-right">
          <button @click="exportData" class="export-btn">
            📥 Exporter Excel
          </button>
        </div>
      </div>
    </header>

    <!-- Period Selector -->
    <div class="period-section">
      <div class="period-content">
        <h3>Période d'analyse</h3>
        <div class="period-buttons">
          <button 
            @click="setPeriod('today')"
            :class="['period-btn', { active: selectedPeriod === 'today' }]"
          >
            Aujourd'hui
          </button>
          <button 
            @click="setPeriod('week')"
            :class="['period-btn', { active: selectedPeriod === 'week' }]"
          >
            Cette semaine
          </button>
          <button 
            @click="setPeriod('month')"
            :class="['period-btn', { active: selectedPeriod === 'month' }]"
          >
            Ce mois
          </button>
          <button 
            @click="setPeriod('year')"
            :class="['period-btn', { active: selectedPeriod === 'year' }]"
          >
            Cette année
          </button>
        </div>
      </div>
    </div>

    <!-- Main Reports Content -->
    <div class="reports-content">
      <!-- KPI Cards -->
      <div class="kpi-section">
        <h3>Indicateurs Clés ({{ getPeriodLabel() }})</h3>
        <div class="kpi-grid">
          <div class="kpi-card revenue">
            <div class="kpi-icon">💰</div>
            <div class="kpi-info">
              <h4>Chiffre d'Affaires</h4>
              <p class="kpi-value">{{ formatMoney(currentData.revenue) }} Ar</p>
              <span class="kpi-change positive">+{{ currentData.revenueGrowth }}%</span>
            </div>
          </div>

          <div class="kpi-card profit">
            <div class="kpi-icon">📈</div>
            <div class="kpi-info">
              <h4>Bénéfice</h4>
              <p class="kpi-value">{{ formatMoney(currentData.profit) }} Ar</p>
              <span class="kpi-change positive">+{{ currentData.profitGrowth }}%</span>
            </div>
          </div>

          <div class="kpi-card transactions">
            <div class="kpi-icon">🛍️</div>
            <div class="kpi-info">
              <h4>Transactions</h4>
              <p class="kpi-value">{{ currentData.transactions }}</p>
              <span class="kpi-change positive">+{{ currentData.transactionGrowth }}%</span>
            </div>
          </div>

          <div class="kpi-card customers">
            <div class="kpi-icon">👥</div>
            <div class="kpi-info">
              <h4>Clients</h4>
              <p class="kpi-value">{{ currentData.customers }}</p>
              <span class="kpi-change positive">+{{ currentData.customerGrowth }}%</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="charts-section">
        <div class="chart-row">
          <!-- Sales Chart -->
          <div class="chart-card">
            <h4>Évolution des Ventes</h4>
            <div class="chart-placeholder">
              <!-- Graphique si pas de ventes -->
              <div v-if="currentData.transactions === 0" class="empty-chart">
                <div class="empty-chart-icon">📊</div>
                <p>Aucune donnée de vente</p>
                <small>Les données apparaîtront après vos premières ventes</small>
              </div>
              
              <!-- Graphique avec vraies données dynamiques -->
              <div v-else class="chart-bars">
                <div 
                  v-for="dayData in weeklyChartData" 
                  :key="dayData.day"
                  class="bar" 
                  :style="{ height: dayData.height + '%' }"
                  :class="{ 'today-bar': dayData.isToday }"
                >
                  <span class="bar-value">{{ dayData.revenue > 0 ? (dayData.revenue / 1000).toFixed(0) + 'K' : '0' }}</span>
                </div>
              </div>
              <div class="chart-labels">
                <span 
                  v-for="dayData in weeklyChartData" 
                  :key="dayData.day"
                  :class="{ 'today-label': dayData.isToday }"
                >
                  {{ dayData.day }}
                </span>
              </div>
            </div>
          </div>

          <!-- Category Distribution -->
          <div class="chart-card">
            <h4>Ventes par Catégorie</h4>
            
            <!-- Graphique vide si pas de ventes -->
            <div v-if="currentData.transactions === 0" class="empty-chart">
              <div class="empty-chart-icon">🥧</div>
              <p>Aucune catégorie de vente</p>
              <small>Ajoutez des produits et faites des ventes pour voir la répartition</small>
            </div>
            
            <!-- Graphique avec vraies données calculées -->
            <div v-else>
              <div class="pie-chart">
                <div 
                  v-for="(category, index) in salesCategories" 
                  :key="category.name"
                  class="pie-segment" 
                  :style="{ 
                    '--percentage': category.percentage, 
                    'background': getCategoryColor(index)
                  }"
                ></div>
              </div>
              <div class="pie-legend">
                <div v-for="(category, index) in salesCategories" :key="category.name" class="legend-item">
                  <span class="legend-color" :style="{ background: getCategoryColor(index) }"></span>
                  <span>{{ category.name }} ({{ category.percentage }}%)</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Products -->
      <div class="top-products-section">
        <h3>Top Produits Vendus</h3>
        
        <!-- Section vide si pas de ventes -->
        <div v-if="currentData.transactions === 0" class="empty-products-section">
          <div class="empty-chart-icon">🏆</div>
          <p>Aucun produit vendu</p>
          <small>Vos produits les plus vendus apparaîtront ici après vos premières ventes</small>
        </div>
        
        <!-- Produits avec vraies données calculées -->
        <div v-else class="products-grid">
          <div v-for="(product, index) in topProducts" :key="product.name" class="product-rank-card">
            <div class="rank-badge" :class="{ 'gold': index === 0, 'silver': index === 1, 'bronze': index === 2 }">{{ index + 1 }}</div>
            <div class="product-info">
              <h5>{{ product.name }}</h5>
              <p>{{ product.quantity }} unités vendues</p>
              <span class="revenue">{{ formatMoney(product.revenue) }} Ar</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Financial Summary -->
      <div class="financial-section">
        <h3>Résumé Financier</h3>
        <div class="financial-grid">
          <div class="financial-card">
            <h5>💵 Recettes Totales</h5>
            <p class="amount positive">{{ formatMoney(currentData.revenue) }} Ar</p>
            <small>{{ currentData.transactions }} transactions</small>
          </div>

          <div class="financial-card">
            <h5>💸 Coûts d'Achat</h5>
            <p class="amount negative">{{ formatMoney(currentData.costs) }} Ar</p>
            <small>{{ ((currentData.costs / currentData.revenue) * 100).toFixed(1) }}% du CA</small>
          </div>

          <div class="financial-card">
            <h5>💰 Bénéfice Net</h5>
            <p class="amount positive">{{ formatMoney(currentData.profit) }} Ar</p>
            <small>Marge: {{ ((currentData.profit / currentData.revenue) * 100).toFixed(1) }}%</small>
          </div>

          <div class="financial-card">
            <h5>📊 Ticket Moyen</h5>
            <p class="amount">{{ formatMoney(currentData.averageTicket) }} Ar</p>
            <small>Par transaction</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const currentUser = ref(null)
const isNewUser = ref(false)

// Charger l'utilisateur connecté
onMounted(() => {
  const userStr = localStorage.getItem('smarterp_current_user')
  if (userStr) {
    currentUser.value = JSON.parse(userStr)
    isNewUser.value = currentUser.value.email !== 'admin@demo.com'
    console.log('ReportsPage - Utilisateur:', currentUser.value.email, 'Nouveau:', isNewUser.value)
    
    // Recharger les données à chaque fois
    reloadReportData()
  }
})

// Fonction pour recharger les données de rapport
const reloadReportData = () => {
  reportData.value = getReportDataForUser()
  console.log('ReportsPage - Données rechargées:', reportData.value.today)
  console.log('Ventes aujourd\'hui:', reportData.value.today.transactions)
}

// Data
const selectedPeriod = ref('today')

// Computed properties pour les données dynamiques
const topProducts = computed(() => {
  const sales = loadUserSales()
  return calculateTopProducts(sales)
})

const salesCategories = computed(() => {
  const sales = loadUserSales()
  return calculateSalesCategories(sales)
})

// Fonction pour obtenir les couleurs des catégories
const getCategoryColor = (index) => {
  const colors = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6']
  return colors[index % colors.length]
}

// Charger les ventes depuis localStorage avec migration automatique
const loadUserSales = () => {
  if (!currentUser.value) return []
  
  const allSales = JSON.parse(localStorage.getItem('smarterp_sales') || '{}')
  let userSales = allSales[currentUser.value.email] || []
  
  console.log('ReportsPage - Ventes chargées pour', currentUser.value.email, ':', userSales.length)
  
  // Migration automatique des dates françaises
  let migrationNeeded = false
  userSales.forEach(sale => {
    if (sale.timestamp && sale.timestamp.includes('à')) {
      migrationNeeded = true
      console.log('🔄 Migration automatique de la date:', sale.timestamp)
      
      const [datePart, timePart] = sale.timestamp.split(' à ')
      const [day, month, year] = datePart.split('/')
      const [hour, minute, second] = timePart.split(':')
      
      // Corriger: 10/11/2025 = 10 novembre (pas 11 octobre)
      const correctDate = new Date(
        parseInt(year), 
        parseInt(month) - 1,  // Correction du mois
        parseInt(day),
        parseInt(hour) || 0, 
        parseInt(minute) || 0, 
        parseInt(second) || 0
      )
      
      sale.timestamp = correctDate.toISOString()
      console.log('✅ Date migrée vers:', correctDate.toLocaleDateString('fr-FR'))
    }
  })
  
  // Sauvegarder si migration effectuée
  if (migrationNeeded) {
    allSales[currentUser.value.email] = userSales
    localStorage.setItem('smarterp_sales', JSON.stringify(allSales))
    console.log('💾 Données migrées sauvegardées automatiquement')
  }
  
  return userSales
}

// Calculer les top produits vendus
const calculateTopProducts = (sales) => {
  const productStats = {}
  
  sales.forEach(sale => {
    if (sale.items) {
      sale.items.forEach(item => {
        const productName = item.name
        if (!productStats[productName]) {
          productStats[productName] = {
            name: productName,
            quantity: 0,
            revenue: 0
          }
        }
        productStats[productName].quantity += item.quantity
        productStats[productName].revenue += item.price * item.quantity
      })
    }
  })
  
  // Convertir en array et trier par revenus
  return Object.values(productStats)
    .sort((a, b) => b.revenue - a.revenue)
    .slice(0, 5) // Top 5 produits
}

// Calculer les catégories de ventes
const calculateSalesCategories = (sales) => {
  const categoryStats = {}
  let totalRevenue = 0
  
  sales.forEach(sale => {
    if (sale.items) {
      sale.items.forEach(item => {
        // Déterminer la catégorie basée sur le nom du produit
        let category = 'Autres'
        const productName = item.name.toLowerCase()
        
        // OUTILS
        if (productName.includes('tournevis') || productName.includes('marteau') || productName.includes('clé') || 
            productName.includes('outil') || productName.includes('vis') || productName.includes('clou') ||
            productName.includes('scie') || productName.includes('perceuse') || productName.includes('pince')) {
          category = 'Outils'
        } 
        // ÉLECTRICITÉ
        else if (productName.includes('ampoule') || productName.includes('cable') || productName.includes('électr') || 
                 productName.includes('lampe') || productName.includes('led') || productName.includes('fil') ||
                 productName.includes('prise') || productName.includes('interrupteur') || productName.includes('néon')) {
          category = 'Électricité'
        } 
        // PEINTURE & FINITION
        else if (productName.includes('peinture') || productName.includes('vernis') || productName.includes('enduit') ||
                 productName.includes('rouleau') || productName.includes('pinceau') || productName.includes('bâche')) {
          category = 'Peinture'
        }
        // CONSTRUCTION & MATÉRIAUX
        else if (productName.includes('ciment') || productName.includes('sable') || productName.includes('gravier') ||
                 productName.includes('brique') || productName.includes('parpaing') || productName.includes('béton')) {
          category = 'Construction'
        }
        // PLOMBERIE
        else if (productName.includes('tuyau') || productName.includes('robinet') || productName.includes('joint') ||
                 productName.includes('colle pvc') || productName.includes('raccord') || productName.includes('siphon')) {
          category = 'Plomberie'
        }
        
        console.log(`Produit: "${item.name}" → Catégorie: ${category}`)
        
        const itemRevenue = item.price * item.quantity
        if (!categoryStats[category]) {
          categoryStats[category] = 0
        }
        categoryStats[category] += itemRevenue
        totalRevenue += itemRevenue
      })
    }
  })
  
  // Convertir en pourcentages
  const categories = []
  Object.keys(categoryStats).forEach(category => {
    const percentage = Math.round((categoryStats[category] / totalRevenue) * 100)
    if (percentage > 0) {
      categories.push({
        name: category,
        percentage,
        revenue: categoryStats[category]
      })
    }
  })
  
  return categories.sort((a, b) => b.percentage - a.percentage)
}

// Calculer les données de rapport à partir des vraies ventes
const calculateReportData = (sales) => {
  const now = new Date()
  const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
  const weekStart = new Date(today.getTime() - (today.getDay() * 24 * 60 * 60 * 1000))
  const monthStart = new Date(now.getFullYear(), now.getMonth(), 1)
  const yearStart = new Date(now.getFullYear(), 0, 1)

  const periods = {
    today: { start: today, revenue: 0, profit: 0, costs: 0, transactions: 0, averageTicket: 0, customers: new Set() },
    week: { start: weekStart, revenue: 0, profit: 0, costs: 0, transactions: 0, averageTicket: 0, customers: new Set() },
    month: { start: monthStart, revenue: 0, profit: 0, costs: 0, transactions: 0, averageTicket: 0, customers: new Set() },
    year: { start: yearStart, revenue: 0, profit: 0, costs: 0, transactions: 0, averageTicket: 0, customers: new Set() }
  }

  sales.forEach(sale => {
    // Parser la date avec gestion automatique des formats
    let saleDate
    try {
      if (sale.timestamp.includes('à')) {
        // Format français : "10/11/2025 à 12:03:45" 
        // CORRECTION: 10/11/2025 = 10 novembre, pas 11 octobre !
        const [datePart, timePart] = sale.timestamp.split(' à ')
        const [day, month, year] = datePart.split('/')
        const [hour, minute, second] = timePart.split(':')
        
        // Créer la date correctement : 10/11/2025 = 10 novembre 2025
        saleDate = new Date(
          parseInt(year), 
          parseInt(month) - 1,  // 11-1 = 10 (novembre en JavaScript)
          parseInt(day),        // 10 (jour)
          parseInt(hour) || 0, 
          parseInt(minute) || 0, 
          parseInt(second) || 0
        )
        
        console.log(`Correction date française: ${sale.timestamp} → ${saleDate.toLocaleDateString('fr-FR')}`)
      } else {
        // Format ISO ou autre
        saleDate = new Date(sale.timestamp)
      }
      
      // Vérifier si la date est valide
      if (isNaN(saleDate.getTime())) {
        console.warn('Date invalide, utilisation d\'aujourd\'hui:', sale.timestamp)
        saleDate = new Date()
      }
    } catch (e) {
      console.error('Erreur parsing date:', sale.timestamp, e)
      saleDate = new Date() // Fallback à aujourd'hui
    }
    
    console.log('Vente:', sale.id, 'Date finale:', saleDate, 'Total:', sale.total)
    
    Object.keys(periods).forEach(period => {
      const isInPeriod = saleDate >= periods[period].start
      
      if (isInPeriod) {
        periods[period].revenue += sale.total
        periods[period].transactions += 1
        console.log(`✅ Vente ${sale.id} comptée pour ${period}: +${sale.total} Ar`)
        
        // Compter les clients uniques
        if (sale.customerName && sale.customerName.trim() !== '') {
          periods[period].customers.add(sale.customerName.trim())
        } else {
          // Client anonyme (paiement cash sans nom)
          periods[period].customers.add('Client-' + sale.id)
        }
        
        // Calculer les coûts et profits basés sur les items
        if (sale.items) {
          sale.items.forEach(item => {
            const itemCost = (item.buyPrice || item.price * 0.6) * item.quantity
            periods[period].costs += itemCost
          })
        }
      }
    })
  })

  // Calculer les profits, tickets moyens et convertir les clients en nombre
  Object.keys(periods).forEach(period => {
    const data = periods[period]
    data.profit = data.revenue - data.costs
    data.averageTicket = data.transactions > 0 ? Math.round(data.revenue / data.transactions) : 0
    data.customers = data.customers.size // Convertir Set en nombre
  })

  return {
    today: periods.today,
    week: periods.week,
    month: periods.month,
    year: periods.year
  }
}

// Données selon le type d'utilisateur
const getReportDataForUser = () => {
  // Charger les vraies ventes pour tous les utilisateurs
  const sales = loadUserSales()
  
  if (sales.length === 0) {
    // Pas de ventes - données vides
    return {
      today: { revenue: 0, profit: 0, costs: 0, transactions: 0, averageTicket: 0 },
      week: { revenue: 0, profit: 0, costs: 0, transactions: 0, averageTicket: 0 },
      month: { revenue: 0, profit: 0, costs: 0, transactions: 0, averageTicket: 0 },
      year: { revenue: 0, profit: 0, costs: 0, transactions: 0, averageTicket: 0 }
    }
  }
  
  // Calculer les données à partir des vraies ventes
  return calculateReportData(sales)
}

const reportData = ref(getReportDataForUser())

// Computed
const currentData = computed(() => {
  return reportData.value[selectedPeriod.value]
})

// Calculer les données du graphique par jour de la semaine
const weeklyChartData = computed(() => {
  const sales = loadUserSales()
  const today = new Date()
  const weekData = []
  
  // Générer les 7 derniers jours
  for (let i = 6; i >= 0; i--) {
    const date = new Date(today)
    date.setDate(today.getDate() - i)
    date.setHours(0, 0, 0, 0)
    
    const dayName = date.toLocaleDateString('fr-FR', { weekday: 'short' })
    const dayRevenue = sales
      .filter(sale => {
        const saleDate = new Date(sale.created_at || sale.timestamp)
        saleDate.setHours(0, 0, 0, 0)
        return saleDate.getTime() === date.getTime()
      })
      .reduce((total, sale) => total + (sale.total || 0), 0)
    
    weekData.push({
      day: dayName,
      revenue: dayRevenue,
      isToday: date.toDateString() === today.toDateString()
    })
  }
  
  // Calculer la hauteur relative (max = 100%)
  const maxRevenue = Math.max(...weekData.map(d => d.revenue), 1)
  weekData.forEach(day => {
    day.height = maxRevenue > 0 ? (day.revenue / maxRevenue) * 100 : 0
  })
  
  return weekData
})

// Methods
function goBack() {
  router.push('/dashboard')
}

function setPeriod(period) {
  selectedPeriod.value = period
}

function getPeriodLabel() {
  const labels = {
    today: "Aujourd'hui",
    week: "Cette semaine",
    month: "Ce mois",
    year: "Cette année"
  }
  return labels[selectedPeriod.value]
}

function formatMoney(amount) {
  return new Intl.NumberFormat('fr-FR').format(amount)
}

function exportData() {
  try {
    // Préparer les données pour l'export
    const data = prepareExportData()
    
    // Créer le contenu CSV (compatible Excel)
    const csvContent = generateCSVContent(data)
    
    // Créer et télécharger le fichier
    downloadCSVFile(csvContent, `SmartERP_Rapport_${selectedPeriod.value}_${new Date().toISOString().split('T')[0]}.csv`)
    
    alert('✅ Export Excel réussi !\n\nLe fichier a été téléchargé avec succès.\nOuvrez-le avec Excel ou LibreOffice.')
    
  } catch (error) {
    console.error('Erreur lors de l\'export:', error)
    alert('❌ Erreur lors de l\'export\n\nVeuillez réessayer.')
  }
}

function prepareExportData() {
  const period = getPeriodLabel()
  const data = currentData.value
  
  return {
    // Informations générales
    metadata: {
      titre: 'SmartERP Pro - Rapport de Gestion',
      periode: period,
      dateExport: new Date().toLocaleDateString('fr-FR'),
      heureExport: new Date().toLocaleTimeString('fr-FR')
    },
    
    // KPI principaux
    kpi: {
      chiffreAffaires: data.revenue,
      benefice: data.profit,
      couts: data.costs,
      transactions: data.transactions,
      clients: data.customers,
      ticketMoyen: data.averageTicket,
      margePercent: ((data.profit / data.revenue) * 100).toFixed(1)
    },
    
    // Croissance
    croissance: {
      revenueGrowth: data.revenueGrowth,
      profitGrowth: data.profitGrowth,
      transactionGrowth: data.transactionGrowth,
      customerGrowth: data.customerGrowth
    },
    
    // Top produits (données simulées)
    topProduits: [
      { rang: 1, nom: 'Riz 25kg', unites: 156, revenue: 7020000 },
      { rang: 2, nom: 'Huile 1L', unites: 89, revenue: 756500 },
      { rang: 3, nom: 'Savon Lux', unites: 234, revenue: 585000 },
      { rang: 4, nom: 'Pâtes Teza', unites: 67, revenue: 214400 },
      { rang: 5, nom: 'Lait concentré', unites: 45, revenue: 202500 }
    ],
    
    // Ventes par catégorie
    categories: [
      { categorie: 'Alimentaire', pourcentage: 45, montant: data.revenue * 0.45 },
      { categorie: 'Hygiène', pourcentage: 30, montant: data.revenue * 0.30 },
      { categorie: 'Ménager', pourcentage: 25, montant: data.revenue * 0.25 }
    ]
  }
}

function generateCSVContent(data) {
  let csv = ''
  
  // En-tête du rapport
  csv += `${data.metadata.titre}\n`
  csv += `Période: ${data.metadata.periode}\n`
  csv += `Date d'export: ${data.metadata.dateExport} à ${data.metadata.heureExport}\n`
  csv += '\n'
  
  // Section KPI
  csv += 'INDICATEURS CLÉS\n'
  csv += 'Métrique,Valeur,Unité\n'
  csv += `Chiffre d'Affaires,${formatNumber(data.kpi.chiffreAffaires)},Ar\n`
  csv += `Bénéfice,${formatNumber(data.kpi.benefice)},Ar\n`
  csv += `Coûts,${formatNumber(data.kpi.couts)},Ar\n`
  csv += `Marge bénéficiaire,${data.kpi.margePercent},%\n`
  csv += `Nombre de transactions,${data.kpi.transactions},unités\n`
  csv += `Nombre de clients,${data.kpi.clients},personnes\n`
  csv += `Ticket moyen,${formatNumber(data.kpi.ticketMoyen)},Ar\n`
  csv += '\n'
  
  // Section Croissance
  csv += 'CROISSANCE\n'
  csv += 'Métrique,Croissance\n'
  csv += `Chiffre d'Affaires,+${data.croissance.revenueGrowth}%\n`
  csv += `Bénéfice,+${data.croissance.profitGrowth}%\n`
  csv += `Transactions,+${data.croissance.transactionGrowth}%\n`
  csv += `Clients,+${data.croissance.customerGrowth}%\n`
  csv += '\n'
  
  // Section Top Produits
  csv += 'TOP PRODUITS\n'
  csv += 'Rang,Produit,Unités vendues,Chiffre d\'affaires (Ar)\n'
  data.topProduits.forEach(produit => {
    csv += `${produit.rang},${produit.nom},${produit.unites},${formatNumber(produit.revenue)}\n`
  })
  csv += '\n'
  
  // Section Catégories
  csv += 'VENTES PAR CATÉGORIE\n'
  csv += 'Catégorie,Pourcentage,Montant (Ar)\n'
  data.categories.forEach(cat => {
    csv += `${cat.categorie},${cat.pourcentage}%,${formatNumber(cat.montant)}\n`
  })
  csv += '\n'
  
  // Pied de page
  csv += `Rapport généré par SmartERP Pro\n`
  csv += `© ${new Date().getFullYear()} - Système de Gestion pour Boutiques Malgaches\n`
  
  return csv
}

function formatNumber(number) {
  return new Intl.NumberFormat('fr-FR').format(Math.round(number))
}

function downloadCSVFile(csvContent, filename) {
  // Ajouter le BOM UTF-8 pour Excel
  const BOM = '\uFEFF'
  const csvWithBOM = BOM + csvContent
  
  // Créer un blob avec le contenu CSV
  const blob = new Blob([csvWithBOM], { type: 'text/csv;charset=utf-8;' })
  
  // Créer un lien de téléchargement
  const link = document.createElement('a')
  const url = URL.createObjectURL(blob)
  
  link.setAttribute('href', url)
  link.setAttribute('download', filename)
  link.style.visibility = 'hidden'
  
  // Ajouter au DOM, cliquer et supprimer
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  
  // Nettoyer l'URL
  URL.revokeObjectURL(url)
}
</script>

<style scoped>
.reports-container {
  min-height: 100vh;
  background: #f8fafc;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.header {
  background: white;
  border-bottom: 1px solid #e2e8f0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.header-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.back-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: #6b7280;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
}

.back-btn:hover {
  background: #4b5563;
}

.header-left h1 {
  font-size: 24px;
  font-weight: 700;
  color: #1f2937;
}

.export-btn {
  background: #10b981;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
}

.export-btn:hover {
  background: #059669;
}

.period-section {
  background: white;
  border-bottom: 1px solid #e2e8f0;
  padding: 1.5rem 0;
}

.period-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

.period-content h3 {
  margin-bottom: 1rem;
  color: #1f2937;
}

.period-buttons {
  display: flex;
  gap: 0.5rem;
}

.period-btn {
  padding: 0.75rem 1.5rem;
  border: 2px solid #e5e7eb;
  background: white;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
}

.period-btn:hover {
  border-color: #10b981;
}

.period-btn.active {
  background: #10b981;
  color: white;
  border-color: #10b981;
}

.reports-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 2rem;
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

/* KPI Section */
.kpi-section h3 {
  margin-bottom: 1.5rem;
  color: #1f2937;
}

.kpi-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.kpi-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.kpi-icon {
  font-size: 2.5rem;
  width: 70px;
  height: 70px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
}

.kpi-card.revenue .kpi-icon { background: #d1fae5; }
.kpi-card.profit .kpi-icon { background: #dbeafe; }
.kpi-card.transactions .kpi-icon { background: #fef3c7; }
.kpi-card.customers .kpi-icon { background: #f3e8ff; }

.kpi-info h4 {
  font-size: 14px;
  color: #6b7280;
  margin-bottom: 0.5rem;
}

.kpi-value {
  font-size: 24px;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.25rem;
}

.kpi-change {
  font-size: 12px;
  font-weight: 600;
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
}

.kpi-change.positive {
  background: #d1fae5;
  color: #065f46;
}

/* Charts Section */
.charts-section h3 {
  margin-bottom: 1.5rem;
  color: #1f2937;
}

.chart-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.chart-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.chart-card h4 {
  margin-bottom: 1rem;
  color: #1f2937;
}

/* Bar Chart */
.chart-placeholder {
  height: 200px;
  display: flex;
  flex-direction: column;
}

.chart-bars {
  flex: 1;
  display: flex;
  align-items: end;
  gap: 0.5rem;
  padding: 1rem 0;
}

.bar {
  flex: 1;
  background: linear-gradient(to top, #10b981, #34d399);
  border-radius: 4px 4px 0 0;
  position: relative;
  min-height: 20px;
  display: flex;
  align-items: start;
  justify-content: center;
  padding-top: 0.25rem;
}

.bar-value {
  font-size: 10px;
  font-weight: 600;
  color: white;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.today-bar {
  background: linear-gradient(to top, #f59e0b, #fbbf24) !important;
  box-shadow: 0 0 10px rgba(245, 158, 11, 0.5);
}

.today-label {
  color: #f59e0b;
  font-weight: 600;
}

.chart-labels {
  display: flex;
  justify-content: space-between;
  padding: 0.5rem 0;
  font-size: 12px;
  color: #6b7280;
}

/* Pie Chart */
.pie-chart {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  background: conic-gradient(
    #3b82f6 0deg 162deg,
    #ec4899 162deg 270deg,
    #8b5cf6 270deg 360deg
  );
  margin: 1rem auto;
}

.pie-legend {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 14px;
}

.legend-color {
  width: 12px;
  height: 12px;
  border-radius: 2px;
}

.legend-color.alimentaire { background: #3b82f6; }
.legend-color.hygiene { background: #ec4899; }
.legend-color.menager { background: #8b5cf6; }

/* Top Products */
.top-products-section h3 {
  margin-bottom: 1.5rem;
  color: #1f2937;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.product-rank-card {
  background: white;
  padding: 1rem;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.rank-badge {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  color: white;
  background: #6b7280;
}

.rank-badge.gold { background: #f59e0b; }
.rank-badge.silver { background: #6b7280; }
.rank-badge.bronze { background: #d97706; }

.product-info h5 {
  font-weight: 600;
  margin-bottom: 0.25rem;
  color: #1f2937;
}

.product-info p {
  font-size: 14px;
  color: #6b7280;
  margin-bottom: 0.25rem;
}

.revenue {
  font-size: 14px;
  font-weight: 600;
  color: #10b981;
}

/* Empty chart styles */
.empty-chart {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 2rem;
  text-align: center;
  background: #f8fafc;
  border-radius: 8px;
  border: 2px dashed #d1d5db;
  min-height: 200px;
}

.empty-chart-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
  opacity: 0.6;
}

.empty-chart p {
  color: #374151;
  font-weight: 600;
  margin-bottom: 0.5rem;
  font-size: 1.1rem;
}

.empty-chart small {
  color: #6b7280;
  font-size: 0.9rem;
}

.empty-products-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 2rem;
  text-align: center;
  background: #f8fafc;
  border-radius: 8px;
  border: 2px dashed #d1d5db;
  margin-top: 1rem;
}

.empty-products-section .empty-chart-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
  opacity: 0.6;
}

.empty-products-section p {
  color: #374151;
  font-weight: 600;
  margin-bottom: 0.5rem;
  font-size: 1.1rem;
}

.empty-products-section small {
  color: #6b7280;
  font-size: 0.9rem;
}

/* Financial Section */
.financial-section h3 {
  margin-bottom: 1.5rem;
  color: #1f2937;
}

.financial-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.financial-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.financial-card h5 {
  margin-bottom: 1rem;
  color: #6b7280;
  font-size: 14px;
}

.amount {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.amount.positive { color: #10b981; }
.amount.negative { color: #ef4444; }
.amount:not(.positive):not(.negative) { color: #1f2937; }

.financial-card small {
  color: #6b7280;
  font-size: 12px;
}

@media (max-width: 768px) {
  .chart-row {
    grid-template-columns: 1fr;
  }
  
  .period-buttons {
    flex-wrap: wrap;
  }
  
  .kpi-grid,
  .products-grid,
  .financial-grid {
    grid-template-columns: 1fr;
  }
}
</style>
