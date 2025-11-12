// 🏪 STORE PINIA - Gestion d'état global de l'application
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authAPI, productsAPI, salesAPI, reportsAPI } from '../services/api.js'

export const useAppStore = defineStore('app', () => {
  // État
  const currentUser = ref(null)
  const products = ref([])
  const sales = ref([])
  const loading = ref(false)
  const error = ref(null)

  // Computed
  const isAuthenticated = computed(() => !!currentUser.value)
  const totalProducts = computed(() => products.value.length)
  const totalSales = computed(() => sales.value.length)
  const todayRevenue = computed(() => {
    const today = new Date().toDateString()
    return sales.value
      .filter(sale => new Date(sale.created_at).toDateString() === today)
      .reduce((total, sale) => total + sale.total, 0)
  })

  // Actions
  const setUser = (user) => {
    currentUser.value = user
    localStorage.setItem('smarterp_current_user', JSON.stringify(user))
  }

  const logout = async () => {
    try {
      await authAPI.logout()
    } catch (error) {
      console.error('Erreur logout:', error)
    }
    currentUser.value = null
    localStorage.removeItem('smarterp_current_user')
    localStorage.removeItem('smarterp_token')
  }

  const login = async (email, password) => {
    try {
      loading.value = true
      error.value = null
      
      // FORCER localStorage en premier (API désactivée)
      console.log('Utilisation localStorage pour login')
      
      // Fallback localStorage - comptes par défaut + comptes créés
      const defaultAccounts = [
        {
          id: 1,
          firstName: 'Franco',
          lastName: 'Glory',
          email: 'franco@gmail.com',
          password: 'I love teko.',
          businessName: 'franco pharmacie',
          businessType: 'pharmacie'
        },
        {
          id: 2,
          firstName: 'Fatima',
          lastName: 'Goulfrane',
          email: 'fatima@gmail.com',
          password: 'quincaillerie',
          businessName: 'fatima quincaillerie',
          businessType: 'quincaillerie'
        }
      ]
      
      // Charger les comptes créés depuis localStorage
      const storedAccounts = JSON.parse(localStorage.getItem('smarterp_accounts') || '[]')
      const allAccounts = [...defaultAccounts, ...storedAccounts]
      
      const account = allAccounts.find(acc => acc.email === email && acc.password === password)
      if (account) {
        const userData = {
          id: account.id,
          firstName: account.firstName,
          lastName: account.lastName,
          email: account.email,
          businessName: account.businessName,
          businessType: account.businessType
        }
        setUser(userData)
        localStorage.setItem('smarterp_token', 'localStorage-token-' + account.id)
        return userData
      } else {
        throw new Error('Identifiants incorrects')
      }
    } catch (err) {
      error.value = err.message || 'Erreur de connexion'
      throw err
    } finally {
      loading.value = false
    }
  }

  const register = async (userData) => {
    try {
      loading.value = true
      error.value = null
      
      // FORCER localStorage (API désactivée)
      console.log('Utilisation localStorage pour inscription')
      
      // Fallback localStorage - Sauvegarder le nouveau compte
      const storedAccounts = JSON.parse(localStorage.getItem('smarterp_accounts') || '[]')
      
      // Vérifier si email existe déjà
      const existingAccount = storedAccounts.find(acc => acc.email === userData.email)
      if (existingAccount) {
        throw new Error('Email déjà utilisé')
      }
      
      // Créer nouveau compte
      const newAccount = {
        id: Date.now(),
        firstName: userData.firstName,
        lastName: userData.lastName,
        email: userData.email,
        password: userData.password,
        businessName: userData.businessName,
        businessType: userData.businessType
      }
      
      // Sauvegarder en localStorage
      storedAccounts.push(newAccount)
      localStorage.setItem('smarterp_accounts', JSON.stringify(storedAccounts))
      
      // Auto-sauvegarde
      autoSave()
      
      // Connecter l'utilisateur
      const userDataForLogin = {
        id: newAccount.id,
        firstName: newAccount.firstName,
        lastName: newAccount.lastName,
        email: newAccount.email,
        businessName: newAccount.businessName,
        businessType: newAccount.businessType
      }
      setUser(userDataForLogin)
      localStorage.setItem('smarterp_token', 'localStorage-token-' + newAccount.id)
      
      return userDataForLogin
    } catch (err) {
      error.value = err.message || 'Erreur d\'inscription'
      throw err
    } finally {
      loading.value = false
    }
  }

  // Fonction de sauvegarde automatique
  const autoSave = () => {
    const backup = {
      accounts: JSON.parse(localStorage.getItem('smarterp_accounts') || '[]'),
      products: JSON.parse(localStorage.getItem('smarterp_products') || '{}'),
      sales: JSON.parse(localStorage.getItem('smarterp_sales') || '{}'),
      timestamp: new Date().toISOString()
    }
    localStorage.setItem('smarterp_backup', JSON.stringify(backup))
    console.log('✅ Auto-sauvegarde effectuée')
  }

  // Fonction de restauration automatique
  const autoRestore = () => {
    const backup = localStorage.getItem('smarterp_backup')
    if (backup) {
      const data = JSON.parse(backup)
      
      // Restaurer si les données principales sont vides
      const accounts = JSON.parse(localStorage.getItem('smarterp_accounts') || '[]')
      const products = JSON.parse(localStorage.getItem('smarterp_products') || '{}')
      const sales = JSON.parse(localStorage.getItem('smarterp_sales') || '{}')
      
      if (accounts.length === 0 && data.accounts.length > 0) {
        localStorage.setItem('smarterp_accounts', JSON.stringify(data.accounts))
        console.log('🔄 Comptes restaurés depuis backup')
      }
      
      if (Object.keys(products).length === 0 && Object.keys(data.products).length > 0) {
        localStorage.setItem('smarterp_products', JSON.stringify(data.products))
        console.log('🔄 Produits restaurés depuis backup')
      }
      
      if (Object.keys(sales).length === 0 && Object.keys(data.sales).length > 0) {
        localStorage.setItem('smarterp_sales', JSON.stringify(data.sales))
        console.log('🔄 Ventes restaurées depuis backup')
      }
    }
  }

  const loadUser = () => {
    // Restaurer automatiquement au démarrage
    autoRestore()
    
    const userStr = localStorage.getItem('smarterp_current_user')
    if (userStr) {
      currentUser.value = JSON.parse(userStr)
    }
  }

  // Gestion des produits
  const loadProducts = async () => {
    try {
      loading.value = true
      error.value = null
      
      // Essayer l'API d'abord, puis fallback localStorage
      try {
        const response = await productsAPI.getAll()
        products.value = response.data
        return
      } catch (apiError) {
        console.log('API non disponible, utilisation localStorage pour produits')
      }
      
      // Fallback localStorage - Format par utilisateur
      const allProducts = JSON.parse(localStorage.getItem('smarterp_products') || '{}')
      const userEmail = currentUser.value?.email || 'default'
      products.value = allProducts[userEmail] || []
    } catch (err) {
      error.value = 'Erreur lors du chargement des produits'
      products.value = []
    } finally {
      loading.value = false
    }
  }

  const addProduct = async (productData) => {
    try {
      loading.value = true
      
      // Essayer l'API d'abord, puis fallback localStorage
      try {
        const response = await productsAPI.create(productData)
        products.value.push(response.data)
        return response.data
      } catch (apiError) {
        console.log('API non disponible, sauvegarde localStorage pour produit')
      }
      
      // Fallback localStorage
      const newProduct = {
        id: Date.now(),
        ...productData,
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString()
      }
      
      products.value.push(newProduct)
      
      // Sauvegarder par utilisateur
      const allProducts = JSON.parse(localStorage.getItem('smarterp_products') || '{}')
      const userEmail = currentUser.value?.email || 'default'
      allProducts[userEmail] = products.value
      localStorage.setItem('smarterp_products', JSON.stringify(allProducts))
      
      // Auto-sauvegarde
      autoSave()
      return newProduct
    } catch (err) {
      error.value = 'Erreur lors de la création du produit'
      console.error('Erreur création produit:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateProduct = async (id, productData) => {
    try {
      loading.value = true
      const response = await productsAPI.update(id, productData)
      const index = products.value.findIndex(p => p.id === id)
      if (index !== -1) {
        products.value[index] = response.data
      }
      return response.data
    } catch (err) {
      error.value = 'Erreur lors de la mise à jour du produit'
      console.error('Erreur mise à jour produit:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  // Gestion des ventes
  const loadSales = async () => {
    try {
      loading.value = true
      error.value = null
      
      // Essayer l'API d'abord, puis fallback localStorage
      try {
        const response = await salesAPI.getAll()
        sales.value = response.data
        return
      } catch (apiError) {
        console.log('API non disponible, utilisation localStorage pour ventes')
      }
      
      // Fallback localStorage - Format par utilisateur
      const allSales = JSON.parse(localStorage.getItem('smarterp_sales') || '{}')
      const userEmail = currentUser.value?.email || 'default'
      sales.value = allSales[userEmail] || []
    } catch (err) {
      error.value = 'Erreur lors du chargement des ventes'
      sales.value = []
    } finally {
      loading.value = false
    }
  }

  const addSale = async (saleData) => {
    try {
      loading.value = true
      
      // Essayer l'API d'abord, puis fallback localStorage
      try {
        const response = await salesAPI.create(saleData)
        sales.value.unshift(response.data)
        
        // Mettre à jour le stock des produits
        saleData.items.forEach(item => {
          const product = products.value.find(p => p.id === item.product_id)
          if (product) {
            product.stock -= item.quantity
          }
        })
        
        return response.data
      } catch (apiError) {
        console.log('API non disponible, sauvegarde vente en localStorage')
      }
      
      // Fallback localStorage
      const newSale = {
        id: Date.now(),
        ...saleData,
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString()
      }
      
      sales.value.unshift(newSale)
      
      // Sauvegarder ventes par utilisateur
      const allSales = JSON.parse(localStorage.getItem('smarterp_sales') || '{}')
      const userEmail = currentUser.value?.email || 'default'
      allSales[userEmail] = sales.value
      localStorage.setItem('smarterp_sales', JSON.stringify(allSales))
      
      // Mettre à jour le stock des produits en localStorage
      saleData.items.forEach(item => {
        const product = products.value.find(p => p.id === item.product_id)
        if (product) {
          product.stock -= item.quantity
        }
      })
      
      // Sauvegarder produits par utilisateur
      const allProducts = JSON.parse(localStorage.getItem('smarterp_products') || '{}')
      allProducts[userEmail] = products.value
      localStorage.setItem('smarterp_products', JSON.stringify(allProducts))
      
      return newSale
    } catch (err) {
      error.value = 'Erreur lors de la création de la vente'
      console.error('Erreur création vente:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  // Gestion des rapports
  const getDashboardData = async () => {
    try {
      loading.value = true
      
      // Essayer l'API d'abord, puis fallback localStorage
      try {
        const response = await reportsAPI.getDashboard()
        return response.data
      } catch (apiError) {
        console.log('API non disponible, calcul dashboard depuis localStorage')
      }
      
      // Fallback localStorage - Calcul direct depuis les données
      await loadProducts() // Charger les produits
      await loadSales()    // Charger les ventes
      
      const today = new Date().toDateString()
      const todaySales = sales.value.filter(s => 
        new Date(s.created_at).toDateString() === today
      )
      
      const todayRevenue = todaySales.reduce((total, sale) => total + (sale.total || 0), 0)
      const todayTransactions = todaySales.length
      const totalProducts = products.value.length
      const totalClients = new Set(sales.value.map(s => s.customer_name).filter(name => name)).size
      
      return {
        todayRevenue,
        todayTransactions,
        totalProducts,
        totalClients
      }
    } catch (err) {
      error.value = 'Erreur lors du chargement du dashboard'
      console.error('Erreur dashboard:', err)
      return {
        todayRevenue: 0,
        todayTransactions: 0,
        totalProducts: 0,
        totalClients: 0
      }
    } finally {
      loading.value = false
    }
  }

  const getTopProducts = async () => {
    try {
      const response = await reportsAPI.getTopProducts()
      return response.data
    } catch (err) {
      console.error('Erreur top produits:', err)
      // Fallback vers calcul local
      const productStats = {}
      sales.value.forEach(sale => {
        if (sale.items) {
          sale.items.forEach(item => {
            if (!productStats[item.product_id]) {
              productStats[item.product_id] = {
                name: item.name,
                quantity: 0,
                revenue: 0
              }
            }
            productStats[item.product_id].quantity += item.quantity
            productStats[item.product_id].revenue += item.quantity * item.price
          })
        }
      })
      return Object.values(productStats)
        .sort((a, b) => b.revenue - a.revenue)
        .slice(0, 5)
    }
  }

  return {
    // État
    currentUser,
    products,
    sales,
    loading,
    error,
    
    // Computed
    isAuthenticated,
    totalProducts,
    totalSales,
    todayRevenue,
    
    // Actions
    setUser,
    logout,
    login,
    register,
    loadUser,
    loadProducts,
    addProduct,
    updateProduct,
    loadSales,
    addSale,
    getDashboardData,
    getTopProducts
  }
})
