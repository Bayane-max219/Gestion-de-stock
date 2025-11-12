// 🌐 SERVICE API - Communication Frontend Vue.js ↔ Backend Laravel
import axios from 'axios'

// Configuration de base - Fallback localStorage si API non disponible
const API_BASE_URL = 'http://127.0.0.1:8000/api'

// Fonction utilitaire pour obtenir l'utilisateur actuel
const getCurrentUser = () => {
  const userStr = localStorage.getItem('smarterp_current_user')
  return userStr ? JSON.parse(userStr) : null
}

// Instance Axios avec configuration
const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Intercepteur pour ajouter le token d'authentification
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('smarterp_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Intercepteur pour gérer les erreurs
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token expiré - rediriger vers login
      localStorage.removeItem('smarterp_token')
      localStorage.removeItem('smarterp_current_user')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

// 🔐 AUTHENTIFICATION
export const authAPI = {
  async login(email, password) {
    const response = await apiClient.post('/login', { email, password })
    return response.data
  },

  async register(userData) {
    const response = await apiClient.post('/register', userData)
    return response.data
  },

  async logout() {
    const response = await apiClient.post('/logout')
    return response.data
  },

  async me() {
    const response = await apiClient.get('/me')
    return response.data
  }
}

// 📦 PRODUITS
export const productsAPI = {
  async getAll() {
    const response = await apiClient.get('/products')
    return response.data
  },

  async getById(id) {
    const response = await apiClient.get(`/products/${id}`)
    return response.data
  },

  async create(productData) {
    const response = await apiClient.post('/products', productData)
    return response.data
  },

  async update(id, productData) {
    const response = await apiClient.put(`/products/${id}`, productData)
    return response.data
  },

  async delete(id) {
    const response = await apiClient.delete(`/products/${id}`)
    return response.data
  },

  async searchByBarcode(barcode) {
    const response = await apiClient.get(`/products/search/barcode/${barcode}`)
    return response.data
  }
}

// 🛍️ VENTES
export const salesAPI = {
  async getAll() {
    const response = await apiClient.get('/sales')
    return response.data
  },

  async getById(id) {
    const response = await apiClient.get(`/sales/${id}`)
    return response.data
  },

  async create(saleData) {
    const response = await apiClient.post('/sales', saleData)
    return response.data
  },

  async getTodaySales() {
    const response = await apiClient.get('/sales/today')
    return response.data
  }
}

// 👥 CLIENTS
export const customersAPI = {
  async getAll() {
    const response = await apiClient.get('/customers')
    return response.data
  },

  async create(customerData) {
    const response = await apiClient.post('/customers', customerData)
    return response.data
  }
}

// 📊 RAPPORTS
export const reportsAPI = {
  async getDashboard() {
    const response = await apiClient.get('/dashboard')
    return response.data
  },

  async getSalesReport(period = 'today') {
    const response = await apiClient.get(`/reports/sales?period=${period}`)
    return response.data
  },

  async getTopProducts(limit = 5) {
    const response = await apiClient.get(`/reports/top-products?limit=${limit}`)
    return response.data
  },

  async getSalesCategories() {
    const response = await apiClient.get('/reports/categories')
    return response.data
  }
}

// Export par défaut
export default apiClient
