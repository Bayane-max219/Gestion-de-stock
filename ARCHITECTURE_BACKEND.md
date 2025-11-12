# 🏗️ ARCHITECTURE BACKEND - Laravel API

## 📋 SITUATION ACTUELLE :
- ❌ **Frontend Vue.js** : Utilise localStorage uniquement
- ❌ **Backend Laravel** : Pas connecté/utilisé
- ❌ **Base de données** : Pas utilisée (tout en localStorage)

## ✅ ARCHITECTURE PRÉVUE :

### 🎯 **Backend Laravel (API REST) :**
```
/api/auth/login          → Authentification
/api/products           → CRUD Produits  
/api/sales              → CRUD Ventes
/api/customers          → CRUD Clients
/api/reports            → Rapports & Analytics
/api/dashboard          → Données tableau de bord
```

### 🎯 **Frontend Vue.js :**
```
- Appels API au lieu de localStorage
- Gestion des tokens JWT
- Synchronisation temps réel
- Cache intelligent
```

### 🎯 **Base de données PostgreSQL :**
```
- users (utilisateurs)
- products (produits) 
- sales (ventes)
- sale_items (détails ventes)
- customers (clients)
- categories (catégories)
```

## 🚀 MIGRATION NÉCESSAIRE :

### **Étape 1 : Configurer Laravel API**
```bash
# Créer les migrations
php artisan make:migration create_products_table
php artisan make:migration create_sales_table
php artisan make:migration create_sale_items_table

# Créer les modèles
php artisan make:model Product
php artisan make:model Sale  
php artisan make:model SaleItem

# Créer les contrôleurs API
php artisan make:controller Api/ProductController --api
php artisan make:controller Api/SaleController --api
php artisan make:controller Api/ReportController
```

### **Étape 2 : Migrer les données localStorage → Database**
```javascript
// Script de migration des données existantes
const migrateToBackend = async () => {
  // 1. Récupérer données localStorage
  const products = JSON.parse(localStorage.getItem('smarterp_products'))
  const sales = JSON.parse(localStorage.getItem('smarterp_sales'))
  
  // 2. Envoyer vers API Laravel
  await fetch('/api/products/migrate', {
    method: 'POST',
    body: JSON.stringify(products)
  })
  
  await fetch('/api/sales/migrate', {
    method: 'POST', 
    body: JSON.stringify(sales)
  })
}
```

### **Étape 3 : Modifier Vue.js pour utiliser API**
```javascript
// Au lieu de localStorage
const products = JSON.parse(localStorage.getItem('smarterp_products'))

// Utiliser API
const products = await fetch('/api/products').then(r => r.json())
```

## 🎯 AVANTAGES BACKEND :
- ✅ **Données persistantes** (pas de perte)
- ✅ **Multi-utilisateurs** réel
- ✅ **Synchronisation** entre appareils
- ✅ **Sauvegardes** automatiques
- ✅ **Sécurité** renforcée
- ✅ **Rapports** plus performants

## ⚡ SOLUTION IMMÉDIATE :
Pour l'instant, améliorons la détection des catégories dans le frontend, puis on pourra migrer vers Laravel plus tard.
