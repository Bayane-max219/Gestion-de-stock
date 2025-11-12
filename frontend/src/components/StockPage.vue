<template>
  <div class="stock-container">
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
          <h1>📦 Gestion de Stock</h1>
        </div>
        <div class="header-right">
          <div class="header-actions">
            <button @click="exportStockData" class="export-btn">
              📥 Export Excel
            </button>
            <button @click="addNewProduct" class="add-btn">
              ➕ Nouveau Produit
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Filters and Search -->
    <div class="filters-section">
      <div class="filters-content">
        <div class="search-bar">
          <input 
            v-model="searchQuery" 
            @input="filterProducts"
            placeholder="Rechercher un produit..."
            class="search-input"
          />
        </div>
        
        <div class="filter-buttons">
          <button 
            @click="filterByCategory('all')"
            :class="['filter-btn', { active: selectedCategory === 'all' }]"
          >
            Tous ({{ products.length }})
          </button>
          
          <!-- Boutons dynamiques selon le type d'entreprise -->
          <template v-if="currentUser?.businessType === 'quincaillerie'">
            <button 
              @click="filterByCategory('outils')"
              :class="['filter-btn', { active: selectedCategory === 'outils' }]"
            >
              Outils ({{ getCategoryCount('outils') }})
            </button>
            <button 
              @click="filterByCategory('materiaux')"
              :class="['filter-btn', { active: selectedCategory === 'materiaux' }]"
            >
              Matériaux ({{ getCategoryCount('materiaux') }})
            </button>
            <button 
              @click="filterByCategory('electricite')"
              :class="['filter-btn', { active: selectedCategory === 'electricite' }]"
            >
              Électricité ({{ getCategoryCount('electricite') }})
            </button>
            <button 
              @click="filterByCategory('plomberie')"
              :class="['filter-btn', { active: selectedCategory === 'plomberie' }]"
            >
              Plomberie ({{ getCategoryCount('plomberie') }})
            </button>
            <button 
              @click="filterByCategory('peinture')"
              :class="['filter-btn', { active: selectedCategory === 'peinture' }]"
            >
              Peinture ({{ getCategoryCount('peinture') }})
            </button>
          </template>
          
          <!-- Boutons par défaut pour épicerie -->
          <template v-else>
            <button 
              @click="filterByCategory('alimentaire')"
              :class="['filter-btn', { active: selectedCategory === 'alimentaire' }]"
            >
              Alimentaire ({{ getCategoryCount('alimentaire') }})
            </button>
            <button 
              @click="filterByCategory('hygiene')"
              :class="['filter-btn', { active: selectedCategory === 'hygiene' }]"
            >
              Hygiène ({{ getCategoryCount('hygiene') }})
            </button>
            <button 
              @click="filterByCategory('menager')"
              :class="['filter-btn', { active: selectedCategory === 'menager' }]"
            >
              Ménager ({{ getCategoryCount('menager') }})
            </button>
          </template>
        </div>

        <div class="stock-alerts">
          <button @click="showLowStock" class="alert-btn">
            ⚠️ Stock Faible ({{ lowStockCount }})
          </button>
        </div>
      </div>
    </div>

    <!-- Products Table -->
    <div class="table-section">
      <div class="table-container">
        <table class="products-table">
          <thead>
            <tr>
              <th>Produit</th>
              <th>Catégorie</th>
              <th>Code-barres</th>
              <th>Prix Achat</th>
              <th>Prix Vente</th>
              <th>Stock</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in filteredProducts" :key="product.id" class="product-row">
              <td class="product-info">
                <div class="product-name">{{ product.name }}</div>
                <div class="product-desc">{{ product.description }}</div>
              </td>
              <td>
                <span :class="['category-badge', product.category]">
                  {{ getCategoryLabel(product.category) }}
                </span>
              </td>
              <td class="barcode">{{ product.barcode }}</td>
              <td class="price">{{ product.buyPrice }} Ar</td>
              <td class="price">{{ product.sellPrice }} Ar</td>
              <td class="stock">
                <span :class="['stock-badge', getStockStatus(product.stock, product.minStock)]">
                  {{ product.stock }}
                </span>
              </td>
              <td>
                <span :class="['status-badge', getStockStatus(product.stock, product.minStock)]">
                  {{ getStockStatusLabel(product.stock, product.minStock) }}
                </span>
              </td>
              <td class="actions">
                <button @click="openEditProduct(product)" class="action-btn edit">
                  ✏️
                </button>
                <button @click="adjustStock(product)" class="action-btn adjust">
                  📊
                </button>
                <button @click="deleteProduct(product)" class="action-btn delete">
                  🗑️
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add Product Modal -->
    <div v-if="showAddProductModal" class="modal-overlay" @click="closeAddProductModal">
      <div class="modal-content large" @click.stop>
        <h3>➕ Nouveau Produit</h3>
        <div class="modal-body">
          <div class="form-grid">
            <!-- Basic Info -->
            <div class="form-section">
              <h4>Informations Générales</h4>
              
              <div class="form-group">
                <label>Nom du produit *</label>
                <input v-model="newProduct.name" type="text" placeholder="Ex: Riz 25kg" class="form-input" />
              </div>

              <div class="form-group">
                <label>Description</label>
                <textarea v-model="newProduct.description" placeholder="Description du produit..." class="form-textarea"></textarea>
              </div>

              <div class="form-group">
                <label>Catégorie *</label>
                <select v-model="newProduct.category" class="form-select">
                  <option value="">Sélectionner une catégorie</option>
                  
                  <!-- Catégories dynamiques selon le type d'entreprise -->
                  <template v-if="currentUser?.businessType === 'quincaillerie'">
                    <option value="outils">Outils</option>
                    <option value="materiaux">Matériaux</option>
                    <option value="electricite">Électricité</option>
                    <option value="plomberie">Plomberie</option>
                    <option value="peinture">Peinture</option>
                  </template>
                  
                  <template v-else-if="currentUser?.businessType === 'pharmacie'">
                    <option value="medicaments">Médicaments</option>
                    <option value="parapharmacie">Parapharmacie</option>
                    <option value="cosmetiques">Cosmétiques</option>
                    <option value="hygiene">Hygiène</option>
                    <option value="materiel_medical">Matériel Médical</option>
                  </template>
                  
                  <template v-else-if="currentUser?.businessType === 'superette'">
                    <option value="alimentaire">Alimentaire</option>
                    <option value="boissons">Boissons</option>
                    <option value="hygiene">Hygiène</option>
                    <option value="menager">Ménager</option>
                    <option value="papeterie">Papeterie</option>
                  </template>
                  
                  <template v-else-if="currentUser?.businessType === 'depot'">
                    <option value="alimentaire_gros">Alimentaire Gros</option>
                    <option value="boissons_gros">Boissons Gros</option>
                    <option value="hygiene_gros">Hygiène Gros</option>
                    <option value="menager_gros">Ménager Gros</option>
                  </template>
                  
                  <!-- Catégories par défaut pour épicerie et autres -->
                  <template v-else>
                    <option value="alimentaire">Alimentaire</option>
                    <option value="hygiene">Hygiène</option>
                    <option value="menager">Ménager</option>
                    <option value="boissons">Boissons</option>
                  </template>
                </select>
              </div>
            </div>

            <!-- Pricing -->
            <div class="form-section">
              <h4>Prix et Stock</h4>
              
              <div class="form-row">
                <div class="form-group">
                  <label>Prix d'achat (Ar) *</label>
                  <input v-model.number="newProduct.buyPrice" type="number" min="0" placeholder="0" class="form-input" />
                </div>

                <div class="form-group">
                  <label>Prix de vente (Ar) *</label>
                  <input v-model.number="newProduct.sellPrice" type="number" min="0" placeholder="0" class="form-input" />
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label>Stock initial</label>
                  <input v-model.number="newProduct.stock" type="number" min="0" placeholder="0" class="form-input" />
                </div>

                <div class="form-group">
                  <label>Stock minimum</label>
                  <input v-model.number="newProduct.minStock" type="number" min="0" placeholder="10" class="form-input" />
                </div>
              </div>

              <div class="profit-info" v-if="newProduct.buyPrice && newProduct.sellPrice">
                <span class="profit-label">Marge bénéficiaire:</span>
                <span class="profit-value">{{ calculateProfit() }} Ar ({{ calculateProfitPercent() }}%)</span>
              </div>
            </div>

            <!-- Barcode -->
            <div class="form-section">
              <h4>Code-barres</h4>
              
              <div class="form-group">
                <label>Code-barres</label>
                <div class="barcode-input">
                  <input v-model="newProduct.barcode" type="text" placeholder="Code-barres du produit" class="form-input" />
                  <button @click="generateBarcode" type="button" class="generate-btn">
                    🎲 Générer
                  </button>
                </div>
              </div>

              <div class="barcode-preview" v-if="newProduct.barcode">
                <div class="barcode-display">
                  <div class="barcode-lines"></div>
                  <span class="barcode-number">{{ newProduct.barcode }}</span>
                </div>
              </div>
            </div>

            <!-- Photo Upload -->
            <div class="form-section">
              <h4>Photo du Produit</h4>
              
              <div class="photo-upload">
                <div class="photo-preview">
                  <div v-if="!newProduct.photo" class="photo-placeholder">
                    <svg class="photo-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                      <circle cx="8.5" cy="8.5" r="1.5"/>
                      <polyline points="21,15 16,10 5,21"/>
                    </svg>
                    <span>Aucune photo</span>
                  </div>
                  <div v-else class="photo-container">
                    <img :src="newProduct.photo" alt="Photo produit" class="product-photo" />
                    <button @click="removePhoto" type="button" class="remove-photo-btn">
                      ❌
                    </button>
                  </div>
                </div>
                <div class="photo-actions">
                  <button @click="uploadPhoto" type="button" class="upload-btn">
                    📷 {{ newProduct.photo ? 'Changer Photo' : 'Ajouter Photo' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal-actions">
          <button @click="closeAddProductModal" class="cancel-btn">Annuler</button>
          <button @click="saveNewProduct" :disabled="!isProductValid" class="save-btn">
            ✅ Créer Produit
          </button>
        </div>
      </div>
    </div>

    <!-- Edit Product Modal -->
    <div v-if="showEditProductModal" class="modal-overlay" @click="closeEditProductModal">
      <div class="modal-content large" @click.stop>
        <h3>✏️ Modifier Produit</h3>
        <div class="modal-body">
          <div class="form-grid">
            <!-- Basic Info -->
            <div class="form-section">
              <h4>Informations Générales</h4>
              
              <div class="form-group">
                <label>Nom du produit *</label>
                <input v-model="editProduct.name" type="text" placeholder="Ex: Riz 25kg" class="form-input" />
              </div>

              <div class="form-group">
                <label>Description</label>
                <textarea v-model="editProduct.description" placeholder="Description du produit..." class="form-textarea"></textarea>
              </div>

              <div class="form-group">
                <label>Catégorie *</label>
                <select v-model="editProduct.category" class="form-select">
                  <option value="">Sélectionner une catégorie</option>
                  
                  <!-- Catégories dynamiques selon le type d'entreprise -->
                  <template v-if="currentUser?.businessType === 'quincaillerie'">
                    <option value="outils">Outils</option>
                    <option value="materiaux">Matériaux</option>
                    <option value="electricite">Électricité</option>
                    <option value="plomberie">Plomberie</option>
                    <option value="peinture">Peinture</option>
                  </template>
                  
                  <template v-else-if="currentUser?.businessType === 'pharmacie'">
                    <option value="medicaments">Médicaments</option>
                    <option value="parapharmacie">Parapharmacie</option>
                    <option value="cosmetiques">Cosmétiques</option>
                    <option value="hygiene">Hygiène</option>
                    <option value="materiel_medical">Matériel Médical</option>
                  </template>
                  
                  <template v-else-if="currentUser?.businessType === 'superette'">
                    <option value="alimentaire">Alimentaire</option>
                    <option value="boissons">Boissons</option>
                    <option value="hygiene">Hygiène</option>
                    <option value="menager">Ménager</option>
                    <option value="papeterie">Papeterie</option>
                  </template>
                  
                  <template v-else-if="currentUser?.businessType === 'depot'">
                    <option value="alimentaire_gros">Alimentaire Gros</option>
                    <option value="boissons_gros">Boissons Gros</option>
                    <option value="hygiene_gros">Hygiène Gros</option>
                    <option value="menager_gros">Ménager Gros</option>
                  </template>
                  
                  <!-- Catégories par défaut pour épicerie et autres -->
                  <template v-else>
                    <option value="alimentaire">Alimentaire</option>
                    <option value="hygiene">Hygiène</option>
                    <option value="menager">Ménager</option>
                    <option value="boissons">Boissons</option>
                  </template>
                </select>
              </div>
            </div>

            <!-- Pricing -->
            <div class="form-section">
              <h4>Prix et Stock</h4>
              
              <div class="form-row">
                <div class="form-group">
                  <label>Prix d'achat (Ar) *</label>
                  <input v-model.number="editProduct.buyPrice" type="number" min="0" placeholder="0" class="form-input" />
                </div>

                <div class="form-group">
                  <label>Prix de vente (Ar) *</label>
                  <input v-model.number="editProduct.sellPrice" type="number" min="0" placeholder="0" class="form-input" />
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label>Stock actuel</label>
                  <input v-model.number="editProduct.stock" type="number" min="0" placeholder="0" class="form-input" />
                </div>

                <div class="form-group">
                  <label>Stock minimum</label>
                  <input v-model.number="editProduct.minStock" type="number" min="0" placeholder="10" class="form-input" />
                </div>
              </div>

              <div class="profit-info" v-if="editProduct.buyPrice && editProduct.sellPrice">
                <span class="profit-label">Marge bénéficiaire:</span>
                <span class="profit-value">{{ calculateEditProfit() }} Ar ({{ calculateEditProfitPercent() }}%)</span>
              </div>
            </div>

            <!-- Barcode -->
            <div class="form-section">
              <h4>Code-barres</h4>
              
              <div class="form-group">
                <label>Code-barres</label>
                <div class="barcode-input">
                  <input v-model="editProduct.barcode" type="text" placeholder="Code-barres du produit" class="form-input" />
                  <button @click="generateEditBarcode" type="button" class="generate-btn">
                    🎲 Générer
                  </button>
                </div>
              </div>

              <div class="barcode-preview" v-if="editProduct.barcode">
                <div class="barcode-display">
                  <div class="barcode-lines"></div>
                  <span class="barcode-number">{{ editProduct.barcode }}</span>
                </div>
              </div>
            </div>

            <!-- Photo Upload -->
            <div class="form-section">
              <h4>Photo du Produit</h4>
              
              <div class="photo-upload">
                <div class="photo-preview">
                  <div v-if="!editProduct.photo" class="photo-placeholder">
                    <svg class="photo-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                      <circle cx="8.5" cy="8.5" r="1.5"/>
                      <polyline points="21,15 16,10 5,21"/>
                    </svg>
                    <span>Aucune photo</span>
                  </div>
                  <div v-else class="photo-container">
                    <img :src="editProduct.photo" alt="Photo produit" class="product-photo" />
                    <button @click="removeEditPhoto" type="button" class="remove-photo-btn">
                      ❌
                    </button>
                  </div>
                </div>
                <div class="photo-actions">
                  <button @click="uploadEditPhoto" type="button" class="upload-btn">
                    📷 {{ editProduct.photo ? 'Changer Photo' : 'Ajouter Photo' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal-actions">
          <button @click="closeEditProductModal" class="cancel-btn">Annuler</button>
          <button @click="saveEditProduct" :disabled="!isEditProductValid" class="save-btn">
            ✅ Sauvegarder
          </button>
        </div>
      </div>
    </div>

    <!-- Stock Movement Modal -->
    <div v-if="showStockModal" class="modal-overlay" @click="closeStockModal">
      <div class="modal-content" @click.stop>
        <h3>Ajustement de Stock - {{ selectedProduct?.name }}</h3>
        <div class="modal-body">
          <div class="current-stock">
            Stock actuel: <strong>{{ selectedProduct?.stock }}</strong>
          </div>
          
          <div class="adjustment-type">
            <label>Type d'ajustement:</label>
            <select v-model="adjustmentType" class="select-input">
              <option value="add">Entrée de stock</option>
              <option value="remove">Sortie de stock</option>
              <option value="set">Définir stock exact</option>
            </select>
          </div>

          <div class="adjustment-quantity">
            <label>{{ adjustmentType === 'set' ? 'Nouveau stock:' : 'Quantité:' }}</label>
            <input v-model.number="adjustmentQuantity" type="number" min="0" class="number-input" />
          </div>

          <div class="adjustment-reason">
            <label>Motif:</label>
            <select v-model="adjustmentReason" class="select-input">
              <option value="purchase">Achat</option>
              <option value="sale">Vente</option>
              <option value="damage">Produit endommagé</option>
              <option value="theft">Vol</option>
              <option value="inventory">Inventaire physique</option>
              <option value="other">Autre</option>
            </select>
          </div>
        </div>
        
        <div class="modal-actions">
          <button @click="closeStockModal" class="cancel-btn">Annuler</button>
          <button @click="confirmStockAdjustment" class="confirm-btn">Confirmer</button>
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
const userCategories = ref([])

// Charger l'utilisateur connecté
onMounted(() => {
  const userStr = localStorage.getItem('smarterp_current_user')
  if (userStr) {
    currentUser.value = JSON.parse(userStr)
    isNewUser.value = currentUser.value.email !== 'admin@demo.com'
    console.log('StockPage - Utilisateur:', currentUser.value.email, 'Nouveau:', isNewUser.value)
    console.log('Type d\'entreprise:', currentUser.value.businessType)
    
    // Mettre à jour la liste des produits selon l'utilisateur
    products.value = getProductsForUser()
    console.log('Produits stock chargés:', products.value.length)
  }
})

// Data
const searchQuery = ref('')
const selectedCategory = ref('all')
const showStockModal = ref(false)
const showAddProductModal = ref(false)
const showEditProductModal = ref(false)
const selectedProduct = ref(null)
const adjustmentType = ref('add')
const adjustmentQuantity = ref(0)
const adjustmentReason = ref('purchase')

// New Product Form
const newProduct = ref({
  name: '',
  description: '',
  category: '',
  buyPrice: 0,
  sellPrice: 0,
  stock: 0,
  minStock: 10,
  barcode: '',
  photo: null
})

// Edit Product Form
const editProduct = ref({
  id: null,
  name: '',
  description: '',
  category: '',
  buyPrice: 0,
  sellPrice: 0,
  stock: 0,
  minStock: 10,
  barcode: '',
  photo: null
})

// Charger les produits depuis localStorage
const loadUserProducts = () => {
  if (!currentUser.value) return []
  
  const allProducts = JSON.parse(localStorage.getItem('smarterp_products') || '{}')
  const userProducts = allProducts[currentUser.value.email] || []
  
  console.log('Produits chargés depuis localStorage pour', currentUser.value.email, ':', userProducts)
  return userProducts
}

// Sauvegarder les produits dans localStorage
const saveUserProducts = () => {
  if (!currentUser.value) return
  
  const allProducts = JSON.parse(localStorage.getItem('smarterp_products') || '{}')
  allProducts[currentUser.value.email] = products.value
  localStorage.setItem('smarterp_products', JSON.stringify(allProducts))
  
  console.log('Produits sauvegardés pour', currentUser.value.email, ':', products.value.length, 'produits')
}

// Produits selon le type d'utilisateur
const getProductsForUser = () => {
  // D'abord essayer de charger depuis localStorage
  const savedProducts = loadUserProducts()
  if (savedProducts.length > 0) {
    return savedProducts
  }
  
  // Si pas de produits sauvegardés, utiliser les produits par défaut
  if (isNewUser.value) {
    return [] // Aucun produit pour les nouveaux utilisateurs
  }
  
  // Produits par défaut selon le type d'entreprise
  if (currentUser.value?.businessType === 'quincaillerie') {
    return [
      {
        id: 1,
        name: 'Marteau 500g',
        description: 'Marteau à panne fendue, manche bois',
        category: 'outils',
        barcode: '1001001001',
        buyPrice: 8000,
        sellPrice: 12000,
        stock: 25,
        minStock: 5
      },
      {
        id: 2,
        name: 'Ciment Portland 50kg',
        description: 'Sac de ciment Portland qualité supérieure',
        category: 'materiaux',
        barcode: '1001001002',
        buyPrice: 18000,
        sellPrice: 25000,
        stock: 40,
        minStock: 10
      },
      {
        id: 3,
        name: 'Ampoule LED 12W',
        description: 'Ampoule LED économique blanc chaud',
        category: 'electricite',
        barcode: '1001001003',
        buyPrice: 3500,
        sellPrice: 5500,
        stock: 60,
        minStock: 15
      },
      {
        id: 4,
        name: 'Tuyau PVC Ø32mm',
        description: 'Tuyau PVC évacuation 32mm - 4m',
        category: 'plomberie',
        barcode: '1001001004',
        buyPrice: 4500,
        sellPrice: 7000,
        stock: 30,
        minStock: 8
      },
      {
        id: 5,
        name: 'Peinture Murale 4L Blanc',
        description: 'Peinture acrylique mate pour intérieur',
        category: 'peinture',
        barcode: '1001001005',
        buyPrice: 15000,
        sellPrice: 22000,
        stock: 20,
        minStock: 5
      }
    ]
  } else {
    // Produits par défaut pour épicerie/demo
    return [
  {
    id: 1,
    name: 'Riz 25kg',
    description: 'Riz blanc de qualité supérieure',
    category: 'alimentaire',
    barcode: '123456789',
    buyPrice: 38000,
    sellPrice: 45000,
    stock: 50,
    minStock: 10
  },
  {
    id: 2,
    name: 'Huile tournesol 1L',
    description: 'Huile de tournesol pure',
    category: 'alimentaire',
    barcode: '987654321',
    buyPrice: 7000,
    sellPrice: 8500,
    stock: 5,
    minStock: 15
  },
  {
    id: 3,
    name: 'Savon Lux',
    description: 'Savon de toilette parfumé',
    category: 'hygiene',
    barcode: '456789123',
    buyPrice: 2000,
    sellPrice: 2500,
    stock: 3,
    minStock: 20
  },
  {
    id: 4,
    name: 'Pâtes Teza 500g',
    description: 'Pâtes alimentaires de qualité',
    category: 'alimentaire',
    barcode: '789123456',
    buyPrice: 2500,
    sellPrice: 3200,
    stock: 75,
    minStock: 25
  },
  {
    id: 5,
    name: 'Lait concentré',
    description: 'Lait concentré sucré',
    category: 'alimentaire',
    barcode: '321654987',
    buyPrice: 3500,
    sellPrice: 4500,
    stock: 6,
    minStock: 12
  },
  {
    id: 6,
    name: 'Détergent Omo 1kg',
    description: 'Lessive en poudre',
    category: 'menager',
    barcode: '654987321',
    buyPrice: 8000,
    sellPrice: 9500,
    stock: 25,
    minStock: 8
  },
  {
    id: 7,
    name: 'Dentifrice Signal',
    description: 'Dentifrice protection complète',
    category: 'hygiene',
    barcode: '147258369',
    buyPrice: 3000,
    sellPrice: 3800,
    stock: 40,
    minStock: 15
  },
  {
    id: 8,
    name: 'Éponges (pack de 5)',
    description: 'Éponges de cuisine',
    category: 'menager',
    barcode: '963852741',
    buyPrice: 1500,
    sellPrice: 2200,
    stock: 2,
    minStock: 10
  }
    ]
  }
}

const products = ref(getProductsForUser())

// Computed
const filteredProducts = computed(() => {
  let filtered = products.value

  // Filter by category
  if (selectedCategory.value !== 'all') {
    filtered = filtered.filter(product => product.category === selectedCategory.value)
  }

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(product => 
      product.name.toLowerCase().includes(query) ||
      product.barcode.includes(query) ||
      product.description.toLowerCase().includes(query)
    )
  }

  return filtered
})

const lowStockCount = computed(() => {
  return products.value.filter(product => product.stock <= product.minStock).length
})

const isProductValid = computed(() => {
  return newProduct.value.name.trim() !== '' &&
         newProduct.value.category !== '' &&
         newProduct.value.buyPrice > 0 &&
         newProduct.value.sellPrice > 0
})

const isEditProductValid = computed(() => {
  return editProduct.value.name.trim() !== '' &&
         editProduct.value.category !== '' &&
         editProduct.value.buyPrice > 0 &&
         editProduct.value.sellPrice > 0
})

// Methods
function goBack() {
  router.push('/dashboard')
}

function getCategoryCount(category) {
  return products.value.filter(product => product.category === category).length
}

function getCategoryLabel(category) {
  const labels = {
    // Catégories épicerie/superette
    alimentaire: 'Alimentaire',
    hygiene: 'Hygiène',
    menager: 'Ménager',
    boissons: 'Boissons',
    papeterie: 'Papeterie',
    
    // Catégories quincaillerie
    outils: 'Outils',
    materiaux: 'Matériaux',
    electricite: 'Électricité',
    plomberie: 'Plomberie',
    peinture: 'Peinture',
    
    // Catégories pharmacie
    medicaments: 'Médicaments',
    parapharmacie: 'Parapharmacie',
    cosmetiques: 'Cosmétiques',
    materiel_medical: 'Matériel Médical',
    
    // Catégories dépôt/gros
    alimentaire_gros: 'Alimentaire Gros',
    boissons_gros: 'Boissons Gros',
    hygiene_gros: 'Hygiène Gros',
    menager_gros: 'Ménager Gros',
    
    // Catégories générales
    general: 'Général',
    services: 'Services',
    accessoires: 'Accessoires'
  }
  return labels[category] || category
}

function getStockStatus(stock, minStock) {
  if (stock === 0) return 'out'
  if (stock <= minStock) return 'low'
  return 'good'
}

function getStockStatusLabel(stock, minStock) {
  if (stock === 0) return 'Rupture'
  if (stock <= minStock) return 'Stock faible'
  return 'En stock'
}

function filterByCategory(category) {
  selectedCategory.value = category
}

function filterProducts() {
  // Filtering is handled by computed property
}

function showLowStock() {
  selectedCategory.value = 'all'
  searchQuery.value = ''
  // Filter to show only low stock items
  alert(`⚠️ Produits en stock faible:\n\n${products.value
    .filter(p => p.stock <= p.minStock)
    .map(p => `• ${p.name}: ${p.stock} restant(s)`)
    .join('\n')}`)
}

function addNewProduct() {
  // Reset form
  newProduct.value = {
    name: '',
    description: '',
    category: '',
    buyPrice: 0,
    sellPrice: 0,
    stock: 0,
    minStock: 10,
    barcode: '',
    photo: null
  }
  showAddProductModal.value = true
}

function closeAddProductModal() {
  showAddProductModal.value = false
}

function generateBarcode() {
  // Generate a random 12-digit barcode
  const barcode = Math.floor(Math.random() * 900000000000) + 100000000000
  newProduct.value.barcode = barcode.toString()
}

function calculateProfit() {
  if (!newProduct.value.buyPrice || !newProduct.value.sellPrice) return 0
  return newProduct.value.sellPrice - newProduct.value.buyPrice
}

function calculateProfitPercent() {
  if (!newProduct.value.buyPrice || !newProduct.value.sellPrice) return 0
  const profit = newProduct.value.sellPrice - newProduct.value.buyPrice
  return ((profit / newProduct.value.buyPrice) * 100).toFixed(1)
}

function uploadPhoto() {
  // Create a file input element
  const fileInput = document.createElement('input')
  fileInput.type = 'file'
  fileInput.accept = 'image/*'
  fileInput.style.display = 'none'
  
  fileInput.onchange = (event) => {
    const file = event.target.files[0]
    if (file) {
      // Check file size (max 5MB)
      if (file.size > 5 * 1024 * 1024) {
        alert('❌ Erreur\n\nLa taille de l\'image ne doit pas dépasser 5MB')
        return
      }
      
      // Check file type
      if (!file.type.startsWith('image/')) {
        alert('❌ Erreur\n\nVeuillez sélectionner un fichier image valide')
        return
      }
      
      // Create FileReader to convert to base64
      const reader = new FileReader()
      reader.onload = (e) => {
        newProduct.value.photo = e.target.result
        alert('✅ Photo ajoutée avec succès !\n\nLa photo sera sauvegardée avec le produit.')
      }
      reader.readAsDataURL(file)
    }
  }
  
  // Trigger file selection
  document.body.appendChild(fileInput)
  fileInput.click()
  document.body.removeChild(fileInput)
}

function removePhoto() {
  if (confirm('Voulez-vous vraiment supprimer cette photo ?')) {
    newProduct.value.photo = null
  }
}

function saveNewProduct() {
  if (!isProductValid.value) return

  // Generate ID
  const newId = Math.max(...products.value.map(p => p.id)) + 1
  
  // Create new product
  const productToAdd = {
    id: newId,
    name: newProduct.value.name,
    description: newProduct.value.description,
    category: newProduct.value.category,
    barcode: newProduct.value.barcode || generateBarcode(),
    buyPrice: newProduct.value.buyPrice,
    sellPrice: newProduct.value.sellPrice,
    stock: newProduct.value.stock,
    minStock: newProduct.value.minStock
  }

  // Add to products list
  products.value.push(productToAdd)
  
  // Sauvegarder dans localStorage
  saveUserProducts()

  // Show success message
  alert(`✅ Produit créé avec succès !\n\n${productToAdd.name}\nCatégorie: ${getCategoryLabel(productToAdd.category)}\nPrix: ${productToAdd.sellPrice} Ar\nStock: ${productToAdd.stock}`)

  // Close modal
  closeAddProductModal()
}

function openEditProduct(product) {
  // Copier les données du produit dans le formulaire d'édition
  editProduct.value = {
    id: product.id,
    name: product.name,
    description: product.description,
    category: product.category,
    buyPrice: product.buyPrice,
    sellPrice: product.sellPrice,
    stock: product.stock,
    minStock: product.minStock,
    barcode: product.barcode,
    photo: product.photo || null
  }
  
  showEditProductModal.value = true
}

function adjustStock(product) {
  selectedProduct.value = product
  adjustmentQuantity.value = 0
  adjustmentType.value = 'add'
  adjustmentReason.value = 'purchase'
  showStockModal.value = true
}

function deleteProduct(product) {
  if (confirm(`Voulez-vous vraiment supprimer "${product.name}" ?`)) {
    const index = products.value.findIndex(p => p.id === product.id)
    if (index > -1) {
      products.value.splice(index, 1)
      
      // Sauvegarder dans localStorage
      saveUserProducts()
      
      alert(`✅ Produit "${product.name}" supprimé`)
    }
  }
}

function closeStockModal() {
  showStockModal.value = false
  selectedProduct.value = null
}

function confirmStockAdjustment() {
  if (selectedProduct.value && adjustmentQuantity.value !== 0) {
    const oldStock = selectedProduct.value.stock
    selectedProduct.value.stock += adjustmentQuantity.value
    
    // Sauvegarder dans localStorage
    saveUserProducts()
    
    alert(`✅ Stock ajusté !\n\n📦 ${selectedProduct.value.name}\n📊 Ancien stock: ${oldStock}\n📊 Nouveau stock: ${selectedProduct.value.stock}\n🔄 Ajustement: ${adjustmentQuantity.value > 0 ? '+' : ''}${adjustmentQuantity.value}`)
    
    closeStockModal()
  }
}

// Edit Product Functions
function closeEditProductModal() {
  showEditProductModal.value = false
}

function calculateEditProfit() {
  if (!editProduct.value.buyPrice || !editProduct.value.sellPrice) return 0
  return editProduct.value.sellPrice - editProduct.value.buyPrice
}

function calculateEditProfitPercent() {
  if (!editProduct.value.buyPrice || !editProduct.value.sellPrice) return 0
  const profit = editProduct.value.sellPrice - editProduct.value.buyPrice
  return ((profit / editProduct.value.buyPrice) * 100).toFixed(1)
}

function generateEditBarcode() {
  const barcode = Math.floor(Math.random() * 900000000000) + 100000000000
  editProduct.value.barcode = barcode.toString()
}

function uploadEditPhoto() {
  const fileInput = document.createElement('input')
  fileInput.type = 'file'
  fileInput.accept = 'image/*'
  fileInput.style.display = 'none'
  
  fileInput.onchange = (event) => {
    const file = event.target.files[0]
    if (file) {
      if (file.size > 5 * 1024 * 1024) {
        alert('❌ Erreur\n\nLa taille de l\'image ne doit pas dépasser 5MB')
        return
      }
      
      if (!file.type.startsWith('image/')) {
        alert('❌ Erreur\n\nVeuillez sélectionner un fichier image valide')
        return
      }
      
      const reader = new FileReader()
      reader.onload = (e) => {
        editProduct.value.photo = e.target.result
        alert('✅ Photo mise à jour avec succès !')
      }
      reader.readAsDataURL(file)
    }
  }
  
  document.body.appendChild(fileInput)
  fileInput.click()
  document.body.removeChild(fileInput)
}

function removeEditPhoto() {
  if (confirm('Voulez-vous vraiment supprimer cette photo ?')) {
    editProduct.value.photo = null
  }
}

function saveEditProduct() {
  if (!isEditProductValid.value) return

  // Trouver le produit dans la liste
  const productIndex = products.value.findIndex(p => p.id === editProduct.value.id)
  
  if (productIndex > -1) {
    // Mettre à jour le produit
    products.value[productIndex] = {
      id: editProduct.value.id,
      name: editProduct.value.name,
      description: editProduct.value.description,
      category: editProduct.value.category,
      barcode: editProduct.value.barcode,
      buyPrice: editProduct.value.buyPrice,
      sellPrice: editProduct.value.sellPrice,
      stock: editProduct.value.stock,
      minStock: editProduct.value.minStock,
      photo: editProduct.value.photo
    }

    // Sauvegarder dans localStorage
    saveUserProducts()

    alert(`✅ Produit modifié avec succès !\n\n${editProduct.value.name}\nCatégorie: ${getCategoryLabel(editProduct.value.category)}\nPrix: ${editProduct.value.sellPrice} Ar\nStock: ${editProduct.value.stock}`)
    
    closeEditProductModal()
  }
}

function exportStockData() {
  try {
    // Préparer les données de stock pour l'export
    const stockData = prepareStockExportData()
    
    // Générer le contenu CSV
    const csvContent = generateStockCSV(stockData)
    
    // Télécharger le fichier
    downloadCSVFile(csvContent, `SmartERP_Stock_${new Date().toISOString().split('T')[0]}.csv`)
    
    alert('✅ Export Stock réussi !\n\nLe fichier Excel a été téléchargé avec succès.')
    
  } catch (error) {
    console.error('Erreur lors de l\'export stock:', error)
    alert('❌ Erreur lors de l\'export\n\nVeuillez réessayer.')
  }
}

function prepareStockExportData() {
  return {
    metadata: {
      titre: 'SmartERP Pro - Inventaire Stock',
      dateExport: new Date().toLocaleDateString('fr-FR'),
      heureExport: new Date().toLocaleTimeString('fr-FR'),
      totalProduits: products.value.length,
      stockFaible: products.value.filter(p => p.stock <= p.minStock).length
    },
    produits: products.value.map(product => ({
      nom: product.name,
      description: product.description,
      categorie: getCategoryLabel(product.category),
      codeBarres: product.barcode,
      prixAchat: product.buyPrice,
      prixVente: product.sellPrice,
      stock: product.stock,
      stockMinimum: product.minStock,
      statut: getStockStatusLabel(product.stock, product.minStock),
      valeurStock: product.stock * product.buyPrice,
      margeUnitaire: product.sellPrice - product.buyPrice,
      margePercent: ((product.sellPrice - product.buyPrice) / product.buyPrice * 100).toFixed(1)
    }))
  }
}

function generateStockCSV(data) {
  let csv = ''
  
  // En-tête
  csv += `${data.metadata.titre}\n`
  csv += `Date d'export: ${data.metadata.dateExport} à ${data.metadata.heureExport}\n`
  csv += `Total produits: ${data.metadata.totalProduits}\n`
  csv += `Produits en stock faible: ${data.metadata.stockFaible}\n`
  csv += '\n'
  
  // Tableau des produits
  csv += 'INVENTAIRE DÉTAILLÉ\n'
  csv += 'Nom,Description,Catégorie,Code-barres,Prix Achat (Ar),Prix Vente (Ar),Stock,Stock Min,Statut,Valeur Stock (Ar),Marge Unitaire (Ar),Marge (%)\n'
  
  data.produits.forEach(produit => {
    csv += `"${produit.nom}","${produit.description}","${produit.categorie}","${produit.codeBarres}",${produit.prixAchat},${produit.prixVente},${produit.stock},${produit.stockMinimum},"${produit.statut}",${produit.valeurStock},${produit.margeUnitaire},${produit.margePercent}%\n`
  })
  
  csv += '\n'
  
  // Résumé par catégorie
  csv += 'RÉSUMÉ PAR CATÉGORIE\n'
  csv += 'Catégorie,Nombre de produits,Valeur totale (Ar)\n'
  
  const categories = ['alimentaire', 'hygiene', 'menager']
  categories.forEach(cat => {
    const produitsCategorie = data.produits.filter(p => p.categorie === getCategoryLabel(cat))
    const valeurTotale = produitsCategorie.reduce((sum, p) => sum + p.valeurStock, 0)
    csv += `${getCategoryLabel(cat)},${produitsCategorie.length},${formatNumber(valeurTotale)}\n`
  })
  
  csv += '\n'
  csv += `Rapport généré par SmartERP Pro\n`
  csv += `© ${new Date().getFullYear()} - Système de Gestion pour Boutiques Malgaches\n`
  
  return csv
}

function formatNumber(number) {
  return new Intl.NumberFormat('fr-FR').format(Math.round(number))
}

function downloadCSVFile(csvContent, filename) {
  const BOM = '\uFEFF'
  const csvWithBOM = BOM + csvContent
  const blob = new Blob([csvWithBOM], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  const url = URL.createObjectURL(blob)
  
  link.setAttribute('href', url)
  link.setAttribute('download', filename)
  link.style.visibility = 'hidden'
  
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  
  URL.revokeObjectURL(url)
}
</script>

<style scoped>
.stock-container {
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

.header-actions {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.export-btn {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: background 0.2s;
}

.export-btn:hover {
  background: #2563eb;
}

.add-btn {
  background: #10b981;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
}

.add-btn:hover {
  background: #059669;
}

.filters-section {
  background: white;
  border-bottom: 1px solid #e2e8f0;
  padding: 1.5rem 0;
}

.filters-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
  display: flex;
  gap: 2rem;
  align-items: center;
  flex-wrap: wrap;
}

.search-bar {
  flex: 1;
  min-width: 300px;
}

.search-input {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 16px;
}

.search-input:focus {
  outline: none;
  border-color: #10b981;
}

.filter-buttons {
  display: flex;
  gap: 0.5rem;
}

.filter-btn {
  padding: 0.5rem 1rem;
  border: 2px solid #e5e7eb;
  background: white;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s;
}

.filter-btn:hover {
  border-color: #10b981;
}

.filter-btn.active {
  background: #10b981;
  color: white;
  border-color: #10b981;
}

.alert-btn {
  background: #f59e0b;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
}

.alert-btn:hover {
  background: #d97706;
}

.table-section {
  max-width: 1400px;
  margin: 0 auto;
  padding: 2rem;
}

.table-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.products-table {
  width: 100%;
  border-collapse: collapse;
}

.products-table th {
  background: #f8fafc;
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}

.product-row {
  border-bottom: 1px solid #f1f5f9;
}

.product-row:hover {
  background: #f8fafc;
}

.products-table td {
  padding: 1rem;
  vertical-align: middle;
}

.product-info {
  min-width: 200px;
}

.product-name {
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.25rem;
}

.product-desc {
  font-size: 14px;
  color: #6b7280;
}

.category-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.category-badge.alimentaire {
  background: #dbeafe;
  color: #1d4ed8;
}

.category-badge.hygiene {
  background: #fce7f3;
  color: #be185d;
}

.category-badge.menager {
  background: #f3e8ff;
  color: #7c3aed;
}

.barcode {
  font-family: monospace;
  color: #6b7280;
}

.price {
  font-weight: 600;
  color: #059669;
}

.stock-badge {
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
  font-weight: 600;
  font-size: 14px;
}

.stock-badge.good {
  background: #d1fae5;
  color: #065f46;
}

.stock-badge.low {
  background: #fef3c7;
  color: #92400e;
}

.stock-badge.out {
  background: #fee2e2;
  color: #991b1b;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.status-badge.good {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.low {
  background: #fef3c7;
  color: #92400e;
}

.status-badge.out {
  background: #fee2e2;
  color: #991b1b;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.action-btn.edit {
  background: #dbeafe;
  color: #1d4ed8;
}

.action-btn.adjust {
  background: #fef3c7;
  color: #92400e;
}

.action-btn.delete {
  background: #fee2e2;
  color: #991b1b;
}

.action-btn:hover {
  transform: scale(1.1);
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-content.large {
  max-width: 800px;
  width: 95%;
}

/* Form Styles */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.form-section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-section h4 {
  color: #1f2937;
  font-weight: 600;
  margin-bottom: 0.5rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #e5e7eb;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group label {
  font-weight: 600;
  color: #374151;
  font-size: 14px;
}

.form-input,
.form-textarea,
.form-select {
  padding: 0.75rem;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 16px;
  transition: border-color 0.2s;
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
  outline: none;
  border-color: #10b981;
}

.form-textarea {
  resize: vertical;
  min-height: 80px;
}

.profit-info {
  background: #f0fdf4;
  padding: 1rem;
  border-radius: 8px;
  border: 1px solid #bbf7d0;
  margin-top: 0.5rem;
}

.profit-label {
  color: #15803d;
  font-weight: 600;
  margin-right: 0.5rem;
}

.profit-value {
  color: #059669;
  font-weight: 700;
}

.barcode-input {
  display: flex;
  gap: 0.5rem;
}

.barcode-input .form-input {
  flex: 1;
}

.generate-btn {
  background: #6b7280;
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  white-space: nowrap;
}

.generate-btn:hover {
  background: #4b5563;
}

.barcode-preview {
  margin-top: 1rem;
}

.barcode-display {
  background: white;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  padding: 1rem;
  text-align: center;
}

.barcode-lines {
  height: 40px;
  background: repeating-linear-gradient(
    to right,
    #000 0px,
    #000 2px,
    #fff 2px,
    #fff 4px
  );
  margin-bottom: 0.5rem;
}

.barcode-number {
  font-family: monospace;
  font-weight: 600;
  color: #1f2937;
}

.photo-upload {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.photo-preview {
  width: 120px;
  height: 120px;
  border: 2px dashed #d1d5db;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f9fafb;
  position: relative;
}

.photo-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  color: #6b7280;
}

.photo-icon {
  width: 32px;
  height: 32px;
}

.photo-placeholder span {
  font-size: 12px;
}

.photo-container {
  position: relative;
  width: 100%;
  height: 100%;
}

.product-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 6px;
}

.remove-photo-btn {
  position: absolute;
  top: -8px;
  right: -8px;
  width: 24px;
  height: 24px;
  background: #ef4444;
  color: white;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.remove-photo-btn:hover {
  background: #dc2626;
}

.photo-actions {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.upload-btn {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  transition: background 0.2s;
}

.upload-btn:hover {
  background: #2563eb;
}

.save-btn {
  background: #10b981;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
}

.save-btn:hover:not(:disabled) {
  background: #059669;
}

.save-btn:disabled {
  background: #d1d5db;
  cursor: not-allowed;
}

.modal-content h3 {
  margin-bottom: 1.5rem;
  color: #1f2937;
}

.modal-body {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 2rem;
}

.current-stock {
  padding: 1rem;
  background: #f3f4f6;
  border-radius: 8px;
  font-size: 16px;
}

.adjustment-type label,
.adjustment-quantity label,
.adjustment-reason label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #374151;
}

.select-input,
.number-input {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 16px;
}

.select-input:focus,
.number-input:focus {
  outline: none;
  border-color: #10b981;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

.cancel-btn {
  background: #6b7280;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
}

.cancel-btn:hover {
  background: #4b5563;
}

.confirm-btn {
  background: #10b981;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
}

.confirm-btn:hover {
  background: #059669;
}

@media (max-width: 768px) {
  .filters-content {
    flex-direction: column;
    align-items: stretch;
  }

  .filter-buttons {
    justify-content: center;
    flex-wrap: wrap;
  }

  .products-table {
    font-size: 14px;
  }

  .products-table th,
  .products-table td {
    padding: 0.5rem;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .modal-content.large {
    width: 98%;
    padding: 1rem;
  }
}
</style>
