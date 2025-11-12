# 🏗️ SmartERP Pro - Architecture Complète

## 📋 PRÉSENTATION PROJET

**SmartERP Pro** est une application de gestion de stock moderne développée pour les boutiques et magasins à Madagascar, remplaçant la gestion papier traditionnelle.

### 🎯 **STACK TECHNIQUE**
- **Frontend** : Vue.js 3 + Composition API
- **Backend** : Laravel 10 + API REST
- **Base de données** : PostgreSQL
- **Authentification** : Laravel Sanctum
- **Serveur** : WAMP64

---

## 📁 STRUCTURE DU PROJET

```
📁 Projet gestion de stock/
├── 📁 frontend/                    ← Application Vue.js
│   ├── 📁 src/
│   │   ├── 📁 components/
│   │   │   ├── DashboardPage.vue
│   │   │   ├── SalesPage.vue
│   │   │   ├── StockPage.vue
│   │   │   └── ReportsPage.vue
│   │   ├── 📁 services/
│   │   │   └── api.js             ← Service API
│   │   └── App.vue
│   └── package.json
│
├── 📁 backend-laravel/             ← API Laravel
│   ├── 📁 app/
│   │   ├── 📁 Http/Controllers/Api/
│   │   │   ├── ProductController.php
│   │   │   ├── SaleController.php
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   └── ReportController.php
│   │   └── 📁 Models/
│   │       ├── Product.php
│   │       ├── Sale.php
│   │       ├── SaleItem.php
│   │       └── Customer.php
│   ├── 📁 routes/
│   │   └── api.php
│   ├── .env
│   └── composer.json
│
└── 📁 database/                    ← Scripts PostgreSQL
    └── migrations/
```

---

## 🌐 API ENDPOINTS

### **🔐 Authentification**
```
POST   /api/v1/auth/login
POST   /api/v1/auth/register
GET    /api/v1/auth/me
POST   /api/v1/auth/logout
```

### **📦 Gestion Produits**
```
GET    /api/v1/products              ← Liste tous les produits
POST   /api/v1/products              ← Créer un produit
GET    /api/v1/products/{id}         ← Détails d'un produit
PUT    /api/v1/products/{id}         ← Modifier un produit
DELETE /api/v1/products/{id}         ← Supprimer un produit
GET    /api/v1/products/search/barcode/{code}  ← Recherche par code-barres
GET    /api/v1/products/low-stock    ← Produits en rupture
```

### **🛍️ Gestion Ventes**
```
GET    /api/v1/sales                 ← Liste des ventes
POST   /api/v1/sales                 ← Créer une vente
GET    /api/v1/sales/{id}            ← Détails d'une vente
GET    /api/v1/sales/today           ← Ventes du jour
GET    /api/v1/sales/stats           ← Statistiques de ventes
```

### **📊 Dashboard & Rapports**
```
GET    /api/v1/dashboard             ← Données tableau de bord
GET    /api/v1/reports/sales         ← Rapport des ventes
GET    /api/v1/reports/top-products  ← Top produits vendus
GET    /api/v1/reports/categories    ← Ventes par catégorie
GET    /api/v1/reports/revenue       ← Rapport de revenus
```

---

## 🗄️ BASE DE DONNÉES POSTGRESQL

### **Tables principales :**

```sql
-- Utilisateurs
users (
    id, name, email, password, role, created_at, updated_at
)

-- Produits
products (
    id, name, description, price, purchase_price, 
    category, stock, barcode, image, user_id, 
    created_at, updated_at
)

-- Ventes
sales (
    id, user_id, customer_name, customer_phone,
    payment_method, total, status, created_at, updated_at
)

-- Détails des ventes
sale_items (
    id, sale_id, product_id, quantity, price,
    created_at, updated_at
)

-- Clients
customers (
    id, name, email, phone, address, user_id,
    created_at, updated_at
)
```

---

## 🔧 CONFIGURATION

### **Backend Laravel (.env)**
```env
APP_NAME="SmartERP Pro API"
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=smarterp_pro
DB_USERNAME=postgres
DB_PASSWORD=postgres

SANCTUM_STATEFUL_DOMAINS=localhost:5173
```

### **Frontend Vue.js (api.js)**
```javascript
const API_BASE_URL = 'http://localhost:8000/api/v1'

// Authentification automatique avec tokens
// CORS configuré pour localhost:5173
// Gestion des erreurs 401/403
```

---

## 🚀 DÉMARRAGE

### **1. Backend Laravel**
```bash
cd backend-laravel
php artisan serve
# API disponible sur http://localhost:8000
```

### **2. Frontend Vue.js**
```bash
cd frontend
npm run dev
# Application disponible sur http://localhost:5173
```

### **3. Base PostgreSQL**
- Serveur : localhost:5432
- Base : smarterp_pro
- Utilisateur : postgres

---

## 🎯 FONCTIONNALITÉS IMPLÉMENTÉES

### ✅ **Backend Laravel**
- API REST complète
- Authentification Sanctum
- Modèles Eloquent
- Validation des données
- Gestion des erreurs
- Configuration PostgreSQL

### ✅ **Frontend Vue.js**
- Interface moderne responsive
- Gestion de stock en temps réel
- Système de ventes avec factures
- Dashboard avec graphiques
- Rapports et analyses
- Scanner code-barres

### ✅ **Base de données**
- Structure PostgreSQL optimisée
- Relations entre tables
- Index de performance
- Contraintes d'intégrité

---

## 🎨 INTERFACE UTILISATEUR

- **Design** : Moderne, vert dominant
- **Responsive** : Mobile et desktop
- **UX** : Intuitive pour boutiquiers malgaches
- **Performance** : Chargement rapide
- **Accessibilité** : Interface claire

---

## 🔒 SÉCURITÉ

- **Authentification** : Laravel Sanctum (tokens)
- **Validation** : Côté backend et frontend
- **CORS** : Configuration stricte
- **Sanitisation** : Données utilisateur
- **Logs** : Traçabilité des actions

---

## 📈 POUR LE RECRUTEUR

Ce projet démontre :
- **Architecture moderne** : Séparation frontend/backend
- **Bonnes pratiques** : Code structuré, documenté
- **Stack technique** : Laravel + Vue.js + PostgreSQL
- **API REST** : Endpoints complets et sécurisés
- **Base de données** : Modélisation professionnelle
- **Interface** : UX/UI moderne et responsive

**Projet prêt pour la production avec une base solide pour l'évolutivité.**
