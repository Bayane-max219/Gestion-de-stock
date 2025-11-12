<template>
  <div class="sales-container">
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
          <h1>🛍️ Nouvelle Vente</h1>
        </div>
        <div class="header-right">
          <span class="user-info">Caissier: Admin</span>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <div class="sales-content">
      <!-- Left Panel - Product Search -->
      <div class="left-panel">
        <div class="search-section">
          <h3>Rechercher Produit</h3>
          <div class="search-bar">
            <input 
              v-model="searchQuery" 
              @input="searchProducts"
              placeholder="Nom du produit ou code-barres..."
              class="search-input"
            />
            <button @click="openBarcodeScanner" class="barcode-btn">
              📷 Scanner
            </button>
          </div>
        </div>

        <div class="products-list">
          <!-- Message pour nouveaux utilisateurs -->
          <div v-if="isNewUser && products.length === 0" class="empty-products">
            <div class="empty-icon">📦</div>
            <h3>Aucun produit en stock</h3>
            <p>Commencez par ajouter vos produits dans la <strong>Gestion Stock</strong></p>
            <button @click="router.push('/stock')" class="stock-btn">
              📋 Aller à la Gestion Stock
            </button>
          </div>
          
          <!-- Liste des produits -->
          <h4>Produits Disponibles</h4>
          <div class="product-grid">
            <div 
              v-for="product in filteredProducts" 
              :key="product.id"
              @click="addToCart(product)"
              class="product-card"
            >
              <div class="product-info">
                <h5>{{ product.name }}</h5>
                <p class="product-price">{{ product.price }} Ar</p>
                <p class="product-stock">Stock: {{ product.stock }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Panel - Shopping Cart -->
      <div class="right-panel">
        <div class="cart-section">
          <h3>Panier ({{ cartItems.length }} articles)</h3>
          
          <div class="cart-items">
            <div 
              v-for="item in cartItems" 
              :key="item.id"
              class="cart-item"
            >
              <div class="item-info">
                <h5>{{ item.name }}</h5>
                <p>{{ item.price }} Ar x {{ item.quantity }}</p>
              </div>
              <div class="item-controls">
                <button @click="decreaseQuantity(item)" class="qty-btn">-</button>
                <span class="quantity">{{ item.quantity }}</span>
                <button @click="increaseQuantity(item)" class="qty-btn">+</button>
                <button @click="removeFromCart(item)" class="remove-btn">🗑️</button>
              </div>
              <div class="item-total">
                {{ item.price * item.quantity }} Ar
              </div>
            </div>
          </div>

          <!-- Cart Summary -->
          <div class="cart-summary">
            <div class="summary-line">
              <span>Sous-total:</span>
              <span>{{ subtotal }} Ar</span>
            </div>
            <div class="summary-line">
              <span>TVA (20%):</span>
              <span>{{ tax }} Ar</span>
            </div>
            <div class="summary-line total">
              <span>TOTAL:</span>
              <span>{{ total }} Ar</span>
            </div>
          </div>

          <!-- Payment Section -->
          <div class="payment-section">
            <h4>Mode de Paiement</h4>
            <div class="payment-methods">
              <label class="payment-option">
                <input type="radio" v-model="paymentMethod" value="cash" />
                💰 Espèces
              </label>
              <label class="payment-option">
                <input type="radio" v-model="paymentMethod" value="card" />
                💳 Carte
              </label>
              <label class="payment-option">
                <input type="radio" v-model="paymentMethod" value="credit" />
                📝 Crédit
              </label>
            </div>

            <div v-if="paymentMethod === 'cash'" class="cash-payment">
              <label>💵 Montant reçu par le client:</label>
              <input 
                v-model.number="amountReceived" 
                type="number" 
                placeholder="Exemple: 15000"
                class="amount-input"
                :class="{ 'insufficient': amountReceived > 0 && amountReceived < total }"
              />
              <div v-if="amountReceived > 0" class="payment-status">
                <p v-if="amountReceived < total" class="insufficient-amount">
                  ❌ Montant insuffisant ! Manque: <strong>{{ total - amountReceived }} Ar</strong>
                </p>
                <p v-else-if="change > 0" class="change">
                  ✅ Monnaie à rendre: <strong>{{ formatPrice(change) }}</strong>
                </p>
                <p v-else class="exact-amount">
                  ✅ Montant exact reçu
                </p>
              </div>
              <div v-if="amountReceived === 0" class="payment-help">
                💡 Saisissez le montant que le client vous donne (exemple: 15000 pour 15 000 Ar)
              </div>
            </div>

            <div v-if="paymentMethod === 'credit'" class="credit-payment">
              <label>Nom du client:</label>
              <input 
                v-model="customerName" 
                type="text" 
                placeholder="Nom du client"
                class="customer-input"
              />
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="action-buttons">
            <button @click="clearCart" class="clear-btn">
              🗑️ Vider Panier
            </button>
            <button 
              @click="processPayment" 
              :disabled="!canProcessPayment"
              class="pay-btn"
            >
              💰 Encaisser ({{ total }} Ar)
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Barcode Scanner Modal -->
    <div v-if="showBarcodeScanner" class="scanner-overlay" @click="closeBarcodeScanner">
      <div class="scanner-modal" @click.stop>
        <div class="scanner-header">
          <h3>📷 Scanner Code-Barres</h3>
          <button @click="closeBarcodeScanner" class="close-btn">❌</button>
        </div>
        
        <div class="scanner-body">
          <!-- Camera Simulation -->
          <div class="camera-view">
            <div class="scanner-frame">
              <div class="scan-line" :class="{ active: scannerStatus === 'scanning' }"></div>
              <div class="corner top-left"></div>
              <div class="corner top-right"></div>
              <div class="corner bottom-left"></div>
              <div class="corner bottom-right"></div>
            </div>
            
            <!-- Scanner Status -->
            <div class="scanner-status">
              <div v-if="scannerStatus === 'idle'" class="status-idle">
                <span>📱 Positionnez le code-barres dans le cadre</span>
              </div>
              <div v-else-if="scannerStatus === 'scanning'" class="status-scanning">
                <span>🔍 Scan en cours...</span>
                <div class="progress-bar">
                  <div class="progress-fill" :style="{ width: scanningProgress + '%' }"></div>
                </div>
              </div>
              <div v-else-if="scannerStatus === 'found'" class="status-found">
                <span>✅ Produit trouvé !</span>
                <div class="product-found" v-if="foundProduct">
                  <div class="product-name">{{ foundProduct.name }}</div>
                  <div class="product-price">{{ formatPrice(foundProduct.price) }} Ar</div>
                  <div class="product-stock">Stock: {{ foundProduct.stock }} disponibles</div>
                  <div class="barcode-number">Code: {{ scannedBarcode }}</div>
                  <button @click="addFoundProductToCart" class="add-product-btn">
                    ➕ Ajouter au panier
                  </button>
                </div>
              </div>
              <div v-else-if="scannerStatus === 'error'" class="status-error">
                <span>❌ Code-barres non reconnu</span>
              </div>
            </div>
          </div>
          
          <!-- Manual Input -->
          <div class="manual-input">
            <h4>Ou saisir manuellement :</h4>
            <div class="input-group">
              <input 
                v-model="scannedBarcode" 
                @keyup.enter="searchByBarcode"
                placeholder="Entrez le code-barres..."
                class="barcode-input"
              />
              <button @click="searchByBarcode" class="search-btn">🔍 Rechercher</button>
            </div>
          </div>
        </div>
        
        <div class="scanner-actions">
          <button @click="simulateRandomScan" class="demo-btn">
            🎲 Simuler Scan
          </button>
          <button @click="closeBarcodeScanner" class="cancel-btn">
            Fermer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAppStore } from '../stores/useAppStore.js'

const router = useRouter()
const appStore = useAppStore()

// Computed properties
const currentUser = computed(() => appStore.currentUser)
const isNewUser = computed(() => appStore.currentUser?.email !== 'admin@demo.com')
const loading = computed(() => appStore.loading)

// Charger l'utilisateur et les données
onMounted(async () => {
  // L'utilisateur est déjà chargé via l'authentification API
  
  if (appStore.currentUser) {
    console.log('SalesPage - Utilisateur:', appStore.currentUser.email, 'Nouveau:', isNewUser.value)
    
    try {
      // Charger les produits depuis l'API
      await appStore.loadProducts()
      console.log('SalesPage - Produits chargés depuis API:', appStore.products.length)
    } catch (error) {
      console.error('Erreur chargement produits:', error)
      // Fallback vers localStorage
      reloadProducts()
    }
  }
})

// Fonction pour recharger les produits
const reloadProducts = () => {
  products.value = getProductsForUser()
  console.log('SalesPage - Produits rechargés:', products.value.length)
  console.log('Codes-barres disponibles:', products.value.map(p => p.barcode))
}

// Data
const searchQuery = ref('')
const cartItems = ref([])
const paymentMethod = ref('cash')
const amountReceived = ref(0)
const customerName = ref('')
const showBarcodeScanner = ref(false)
const scannerStatus = ref('idle') // idle, scanning, found, error
const scannedBarcode = ref('')
const scanningProgress = ref(0)
const foundProduct = ref(null)
// Charger les produits depuis localStorage
const loadUserProducts = () => {
  if (!currentUser.value) return []
  
  const allProducts = JSON.parse(localStorage.getItem('smarterp_products') || '{}')
  const userProducts = allProducts[currentUser.value.email] || []
  
  console.log('SalesPage - Produits chargés depuis localStorage pour', currentUser.value.email, ':', userProducts)
  return userProducts
}

// Produits selon le type d'utilisateur
const getProductsForUser = () => {
  // D'abord essayer de charger depuis localStorage
  const savedProducts = loadUserProducts()
  if (savedProducts.length > 0) {
    // Adapter le format pour SalesPage (price au lieu de sellPrice)
    return savedProducts.map(product => ({
      ...product,
      price: product.sellPrice || product.price
    }))
  }
  
  // Si pas de produits sauvegardés, utiliser les produits par défaut
  if (isNewUser.value) {
    return [] // Aucun produit pour les nouveaux utilisateurs
  }
  
  // Produits par défaut selon le type d'entreprise
  if (currentUser.value?.businessType === 'quincaillerie') {
    return [
      { id: 1, name: 'Marteau 500g', price: 12000, stock: 25, barcode: '1001001001' },
      { id: 2, name: 'Ciment Portland 50kg', price: 25000, stock: 40, barcode: '1001001002' },
      { id: 3, name: 'Ampoule LED 12W', price: 5500, stock: 60, barcode: '1001001003' },
      { id: 4, name: 'Tuyau PVC Ø32mm', price: 7000, stock: 30, barcode: '1001001004' },
      { id: 5, name: 'Peinture Murale 4L', price: 22000, stock: 20, barcode: '1001001005' }
    ]
  } else {
    // Produits par défaut pour épicerie
    return [
      { id: 1, name: 'Riz 25kg', price: 45000, stock: 50, barcode: '123456789' },
      { id: 2, name: 'Huile 1L', price: 8500, stock: 30, barcode: '987654321' },
      { id: 3, name: 'Savon Lux', price: 2500, stock: 100, barcode: '456789123' },
      { id: 4, name: 'Pâtes Teza 500g', price: 3200, stock: 75, barcode: '789123456' },
      { id: 5, name: 'Lait concentré', price: 4500, stock: 25, barcode: '321654987' },
      { id: 6, name: 'Sucre 1kg', price: 3800, stock: 40, barcode: '654987321' },
      { id: 7, name: 'Café Arabica 250g', price: 12000, stock: 20, barcode: '147258369' },
      { id: 8, name: 'Thé Lipton 50 sachets', price: 6500, stock: 35, barcode: '963852741' }
    ]
  }
}

const products = ref(getProductsForUser())

// Computed
const filteredProducts = computed(() => {
  if (!searchQuery.value) return products.value
  return products.value.filter(product => 
    product.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    product.barcode.includes(searchQuery.value)
  )
})

const subtotal = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
})

const tax = computed(() => {
  return Math.round(subtotal.value * 0.2)
})

const total = computed(() => {
  return subtotal.value + tax.value
})

const change = computed(() => {
  return paymentMethod.value === 'cash' ? Math.max(0, amountReceived.value - total.value) : 0
})

const canProcessPayment = computed(() => {
  if (cartItems.value.length === 0) return false
  if (paymentMethod.value === 'cash') {
    return amountReceived.value >= total.value
  }
  if (paymentMethod.value === 'credit') {
    return customerName.value.trim() !== ''
  }
  return true
})

// Methods
function goBack() {
  router.push('/dashboard')
}

function searchProducts() {
  // Search is handled by computed property
}

function openBarcodeScanner() {
  showBarcodeScanner.value = true
  scannerStatus.value = 'idle'
  scannedBarcode.value = ''
  scanningProgress.value = 0
  foundProduct.value = null
}

function closeBarcodeScanner() {
  showBarcodeScanner.value = false
  scannerStatus.value = 'idle'
  scannedBarcode.value = ''
  scanningProgress.value = 0
  foundProduct.value = null
}

function startBarcodeScanning() {
  scannerStatus.value = 'scanning'
  scanningProgress.value = 0
  
  // Simuler le processus de scan avec une barre de progression
  const scanInterval = setInterval(() => {
    scanningProgress.value += 10
    
    if (scanningProgress.value >= 100) {
      clearInterval(scanInterval)
      // Simuler la détection d'un code-barres aléatoire
      const randomProduct = products.value[Math.floor(Math.random() * products.value.length)]
      scannedBarcode.value = randomProduct.barcode
      foundProduct.value = randomProduct
      scannerStatus.value = 'found'
    }
  }, 200)
}

function simulateRandomScan() {
  startBarcodeScanning()
}

function addFoundProductToCart() {
  if (foundProduct.value) {
    addToCart(foundProduct.value)
    alert(`✅ Produit ajouté au panier !\n\n📦 ${foundProduct.value.name}\n💰 Prix: ${formatPrice(foundProduct.value.price)} Ar`)
    closeBarcodeScanner()
  }
}

// Fonction pour formater les prix
function formatPrice(price) {
  return new Intl.NumberFormat('fr-FR').format(price)
}

function searchByBarcode() {
  if (!scannedBarcode.value.trim()) {
    scannerStatus.value = 'error'
    foundProduct.value = null
    setTimeout(() => {
      scannerStatus.value = 'idle'
    }, 2000)
    return
  }
  
  // Debug: afficher le code-barres recherché
  console.log('Recherche du code-barres:', scannedBarcode.value.trim())
  console.log('Produits disponibles:', products.value.map(p => p.barcode))
  
  // Rechercher le produit par code-barres
  const product = products.value.find(p => p.barcode === scannedBarcode.value.trim())
  
  if (product) {
    // Produit trouvé - stocker les informations pour affichage
    console.log('Produit trouvé:', product)
    foundProduct.value = product
    scannerStatus.value = 'found'
    
    // L'utilisateur peut maintenant voir le produit et choisir de l'ajouter
    
  } else {
    // Produit non trouvé
    console.log('Aucun produit trouvé pour le code:', scannedBarcode.value.trim())
    foundProduct.value = null
    scannerStatus.value = 'error'
    setTimeout(() => {
      scannerStatus.value = 'idle'
      scannedBarcode.value = ''
    }, 2000)
  }
}

function addToCart(product) {
  const existingItem = cartItems.value.find(item => item.id === product.id)
  
  if (existingItem) {
    if (existingItem.quantity < product.stock) {
      existingItem.quantity++
    } else {
      alert(`Stock insuffisant pour ${product.name}`)
    }
  } else {
    cartItems.value.push({
      ...product,
      quantity: 1
    })
  }
}

function increaseQuantity(item) {
  const product = products.value.find(p => p.id === item.id)
  if (item.quantity < product.stock) {
    item.quantity++
  } else {
    alert(`Stock insuffisant pour ${item.name}`)
  }
}

function decreaseQuantity(item) {
  if (item.quantity > 1) {
    item.quantity--
  } else {
    removeFromCart(item)
  }
}

function removeFromCart(item) {
  const index = cartItems.value.findIndex(cartItem => cartItem.id === item.id)
  if (index > -1) {
    cartItems.value.splice(index, 1)
  }
}

function clearCart() {
  if (confirm('Voulez-vous vraiment vider le panier ?')) {
    cartItems.value = []
    amountReceived.value = 0
    customerName.value = ''
  }
}

function processPayment() {
  if (!canProcessPayment.value) return

  // Vérifier le paiement en espèces
  if (paymentMethod.value === 'cash' && amountReceived.value < total.value) {
    alert(`❌ Montant insuffisant !\n\nTotal à payer: ${total.value} Ar\nMontant reçu: ${amountReceived.value} Ar\nManque: ${total.value - amountReceived.value} Ar`)
    return
  }

  // Créer la facture
  const invoiceId = 'INV-' + Date.now()
  const invoice = {
    id: invoiceId,
    items: cartItems.value.map(item => ({
      ...item,
      totalPrice: item.price * item.quantity
    })),
    subtotal: subtotal.value,
    tax: tax.value,
    total: total.value,
    paymentMethod: paymentMethod.value,
    amountReceived: amountReceived.value,
    change: change.value,
    customerName: customerName.value,
    timestamp: new Date().toISOString(),
    cashier: currentUser.value?.firstName || 'Admin'
  }

  // Sauvegarder la vente
  saveSale(invoice)
  
  // Mettre à jour le stock des produits vendus
  updateProductStock()

  // Message de confirmation détaillé
  let message = `✅ VENTE ENREGISTRÉE AVEC SUCCÈS !\n\n`
  message += `📄 Facture: ${invoiceId}\n`
  message += `💰 Total: ${formatPrice(total.value)}\n`
  message += `💳 Mode: ${getPaymentMethodLabel(paymentMethod.value)}\n`
  
  if (paymentMethod.value === 'cash') {
    message += `💵 Reçu: ${formatPrice(amountReceived.value)}\n`
    if (change.value > 0) {
      message += `🔄 Monnaie: ${formatPrice(change.value)}\n`
    }
  }
  
  if (customerName.value) {
    message += `👤 Client: ${customerName.value}\n`
  }
  
  message += `\n🖨️ Impression de la facture...`
  
  alert(message)
  
  // Reset form
  resetSaleForm()
}

// Sauvegarder la vente dans localStorage
function saveSale(invoice) {
  if (!currentUser.value) return
  
  const allSales = JSON.parse(localStorage.getItem('smarterp_sales') || '{}')
  if (!allSales[currentUser.value.email]) {
    allSales[currentUser.value.email] = []
  }
  
  allSales[currentUser.value.email].push(invoice)
  localStorage.setItem('smarterp_sales', JSON.stringify(allSales))
  
  console.log('Vente sauvegardée:', invoice.id)
}

// Mettre à jour le stock après vente
function updateProductStock() {
  cartItems.value.forEach(cartItem => {
    const productIndex = products.value.findIndex(p => p.id === cartItem.id)
    if (productIndex > -1) {
      products.value[productIndex].stock -= cartItem.quantity
      console.log(`Stock mis à jour: ${cartItem.name} - Nouveau stock: ${products.value[productIndex].stock}`)
    }
  })
  
  // Sauvegarder les produits mis à jour
  saveUpdatedProducts()
}

// Sauvegarder les produits avec stock mis à jour
function saveUpdatedProducts() {
  if (!currentUser.value) return
  
  const allProducts = JSON.parse(localStorage.getItem('smarterp_products') || '{}')
  allProducts[currentUser.value.email] = products.value
  localStorage.setItem('smarterp_products', JSON.stringify(allProducts))
  
  console.log('Stock des produits sauvegardé après vente')
}

// Réinitialiser le formulaire de vente
function resetSaleForm() {
  cartItems.value = []
  amountReceived.value = 0
  customerName.value = ''
  searchQuery.value = ''
  paymentMethod.value = 'cash'
}

// Obtenir le libellé du mode de paiement
function getPaymentMethodLabel(method) {
  const labels = {
    cash: 'Espèces',
    card: 'Carte',
    credit: 'Crédit'
  }
  return labels[method] || method
}
</script>

<style scoped>
.sales-container {
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

.user-info {
  font-size: 14px;
  color: #6b7280;
}

.sales-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 2rem;
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 2rem;
  height: calc(100vh - 100px);
}

/* Left Panel */
.left-panel {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow-y: auto;
}

.search-section h3 {
  margin-bottom: 1rem;
  color: #1f2937;
}

.search-bar {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.search-input {
  flex: 1;
  padding: 0.75rem;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 16px;
}

.search-input:focus {
  outline: none;
  border-color: #10b981;
}

.barcode-btn {
  background: #10b981;
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  white-space: nowrap;
}

.barcode-btn:hover {
  background: #059669;
}

.products-list h4 {
  margin-bottom: 1rem;
  color: #1f2937;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1rem;
}

.product-card {
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  padding: 1rem;
  cursor: pointer;
  transition: all 0.2s;
}

.product-card:hover {
  border-color: #10b981;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.product-info h5 {
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #1f2937;
}

.product-price {
  color: #10b981;
  font-weight: 600;
  font-size: 16px;
}

.product-stock {
  color: #6b7280;
  font-size: 14px;
}

/* Right Panel */
.right-panel {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
}

.cart-section h3 {
  margin-bottom: 1rem;
  color: #1f2937;
}

.cart-items {
  flex: 1;
  max-height: 300px;
  overflow-y: auto;
  margin-bottom: 1rem;
}

.cart-item {
  display: grid;
  grid-template-columns: 1fr auto auto;
  gap: 1rem;
  padding: 0.75rem;
  border-bottom: 1px solid #f1f5f9;
  align-items: center;
}

.item-info h5 {
  font-weight: 600;
  margin-bottom: 0.25rem;
}

.item-controls {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.qty-btn {
  width: 30px;
  height: 30px;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.qty-btn:hover {
  background: #f3f4f6;
}

.quantity {
  min-width: 30px;
  text-align: center;
  font-weight: 600;
}

.remove-btn {
  background: #ef4444;
  color: white;
  border: none;
  width: 30px;
  height: 30px;
  border-radius: 4px;
  cursor: pointer;
}

.item-total {
  font-weight: 600;
  color: #10b981;
}

.cart-summary {
  border-top: 2px solid #e5e7eb;
  padding-top: 1rem;
  margin-bottom: 1rem;
}

.summary-line {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.summary-line.total {
  font-size: 18px;
  font-weight: 700;
  color: #1f2937;
  border-top: 1px solid #e5e7eb;
  padding-top: 0.5rem;
}

.payment-section h4 {
  margin-bottom: 1rem;
  color: #1f2937;
}

.payment-methods {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.payment-option {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.cash-payment, .credit-payment {
  margin-bottom: 1rem;
}

.amount-input, .customer-input {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  margin-top: 0.5rem;
}

.change {
  color: #10b981;
  font-weight: 600;
  margin-top: 0.5rem;
}

/* Payment status styles */
.payment-status {
  margin-top: 10px;
}

.insufficient-amount {
  color: #ef4444;
  font-weight: 600;
  background: #fef2f2;
  padding: 8px 12px;
  border-radius: 6px;
  border: 1px solid #fecaca;
}

.exact-amount {
  color: #10b981;
  font-weight: 600;
  background: #f0fdf4;
  padding: 8px 12px;
  border-radius: 6px;
  border: 1px solid #bbf7d0;
}

.payment-help {
  color: #6b7280;
  font-size: 14px;
  font-style: italic;
  margin-top: 8px;
  padding: 8px 12px;
  background: #f9fafb;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
}

.amount-input.insufficient {
  border-color: #ef4444;
  background-color: #fef2f2;
}

.amount-input.insufficient:focus {
  outline: none;
  border-color: #ef4444;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.action-buttons {
  display: flex;
  gap: 1rem;
}

.clear-btn {
  flex: 1;
  background: #6b7280;
  color: white;
  border: none;
  padding: 0.75rem;
  border-radius: 8px;
  cursor: pointer;
}

.clear-btn:hover {
  background: #4b5563;
}

.pay-btn {
  flex: 2;
  background: #10b981;
  color: white;
  border: none;
  padding: 0.75rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
}

.pay-btn:hover:not(:disabled) {
  background: #059669;
}

.pay-btn:disabled {
  background: #d1d5db;
  cursor: not-allowed;
}

/* Barcode Scanner Styles */
.scanner-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.scanner-modal {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

.scanner-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #e5e7eb;
}

.scanner-header h3 {
  color: #1f2937;
  font-size: 20px;
  font-weight: 700;
  margin: 0;
}

.close-btn {
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  transition: background 0.2s;
}

.close-btn:hover {
  background: #f3f4f6;
}

.scanner-body {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.camera-view {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.scanner-frame {
  position: relative;
  width: 300px;
  height: 200px;
  background: linear-gradient(45deg, #1f2937, #374151);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.scan-line {
  position: absolute;
  width: 100%;
  height: 3px;
  background: linear-gradient(90deg, transparent, #10b981, transparent);
  top: 50%;
  transform: translateY(-50%);
  opacity: 0;
}

.scan-line.active {
  opacity: 1;
  animation: scanAnimation 2s ease-in-out infinite;
}

@keyframes scanAnimation {
  0%, 100% { transform: translateY(-50%) translateX(-100%); }
  50% { transform: translateY(-50%) translateX(100%); }
}

.corner {
  position: absolute;
  width: 30px;
  height: 30px;
  border: 3px solid #10b981;
}

.corner.top-left {
  top: 20px;
  left: 20px;
  border-right: none;
  border-bottom: none;
}

.corner.top-right {
  top: 20px;
  right: 20px;
  border-left: none;
  border-bottom: none;
}

.corner.bottom-left {
  bottom: 20px;
  left: 20px;
  border-right: none;
  border-top: none;
}

.corner.bottom-right {
  bottom: 20px;
  right: 20px;
  border-left: none;
  border-top: none;
}

.scanner-status {
  text-align: center;
  min-height: 80px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.status-idle span {
  color: #6b7280;
  font-size: 16px;
}

.status-scanning span {
  color: #3b82f6;
  font-weight: 600;
  font-size: 16px;
}

.status-found span {
  color: #10b981;
  font-weight: 600;
  font-size: 16px;
}

.status-error span {
  color: #ef4444;
  font-weight: 600;
  font-size: 16px;
}

.progress-bar {
  width: 200px;
  height: 6px;
  background: #e5e7eb;
  border-radius: 3px;
  margin: 1rem auto;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: #3b82f6;
  border-radius: 3px;
  transition: width 0.2s ease;
}

.barcode-result {
  background: #f0fdf4;
  color: #15803d;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-family: monospace;
  font-weight: 600;
  margin-top: 0.5rem;
  display: inline-block;
}

.product-found {
  background: linear-gradient(135deg, #f0fdf4, #dcfce7);
  border: 2px solid #16a34a;
  border-radius: 12px;
  padding: 1.5rem;
  margin-top: 1rem;
  text-align: center;
  box-shadow: 0 4px 12px rgba(22, 163, 74, 0.15);
}

.product-name {
  font-size: 18px;
  font-weight: 700;
  color: #15803d;
  margin-bottom: 0.5rem;
}

.product-price {
  font-size: 16px;
  font-weight: 600;
  color: #059669;
  margin-bottom: 0.5rem;
}

.product-stock {
  font-size: 14px;
  color: #6b7280;
  margin-bottom: 0.75rem;
}

.barcode-number {
  font-size: 12px;
  color: #6b7280;
  font-family: monospace;
  background: #f9fafb;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  display: inline-block;
  margin-bottom: 1rem;
}

.add-product-btn {
  background: #10b981;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
  font-size: 14px;
}

.add-product-btn:hover {
  background: #059669;
}

/* Empty products message */
.empty-products {
  text-align: center;
  padding: 3rem 2rem;
  background: #f8fafc;
  border-radius: 12px;
  border: 2px dashed #d1d5db;
  margin: 2rem 0;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.empty-products h3 {
  color: #374151;
  margin-bottom: 0.5rem;
  font-size: 1.5rem;
}

.empty-products p {
  color: #6b7280;
  margin-bottom: 1.5rem;
  font-size: 1rem;
}

.stock-btn {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.stock-btn:hover {
  background: #2563eb;
}

.manual-input {
  border-top: 1px solid #e5e7eb;
  padding-top: 1.5rem;
}

.manual-input h4 {
  color: #374151;
  margin-bottom: 1rem;
  font-size: 16px;
}

.input-group {
  display: flex;
  gap: 0.5rem;
}

.barcode-input {
  flex: 1;
  padding: 0.75rem;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 16px;
  font-family: monospace;
}

.barcode-input:focus {
  outline: none;
  border-color: #10b981;
}

.search-btn {
  background: #10b981;
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  white-space: nowrap;
}

.search-btn:hover {
  background: #059669;
}

.scanner-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.demo-btn {
  background: #f59e0b;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
}

.demo-btn:hover {
  background: #d97706;
}

.cancel-btn {
  background: #6b7280;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
}

.cancel-btn:hover {
  background: #4b5563;
}

@media (max-width: 1024px) {
  .sales-content {
    grid-template-columns: 1fr;
    grid-template-rows: auto 1fr;
  }
  
  .right-panel {
    order: -1;
  }
}
</style>
