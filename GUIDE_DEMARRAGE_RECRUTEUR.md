# 🚀 GUIDE DE DÉMARRAGE - SmartERP Pro

## 📋 POUR LE RECRUTEUR

Ce guide vous permet de tester rapidement l'application complète avec l'architecture **Laravel + Vue.js + PostgreSQL**.

---

## 🎯 ARCHITECTURE DU PROJET

```
📁 SmartERP Pro/
├── 📁 frontend/           ← Vue.js 3 (Port 5173)
├── 📁 backend-laravel/    ← Laravel 10 API (Port 8000)
└── 🗄️ PostgreSQL         ← Base smarterp_pro (Port 5432)
```

**Communication :** Frontend Vue.js ↔ API Laravel ↔ PostgreSQL

---

## ⚡ DÉMARRAGE RAPIDE (5 minutes)

### **1. Base de données PostgreSQL** ✅
```
✅ Déjà configurée
✅ Base: smarterp_pro
✅ Connexion: localhost:5432
```

### **2. Backend Laravel (Terminal 1)**
```bash
cd "backend-laravel"
php artisan serve
```
**Résultat :** API disponible sur `http://localhost:8000`

### **3. Frontend Vue.js (Terminal 2)**
```bash
cd "frontend"
npm run dev
```
**Résultat :** Application sur `http://localhost:5173`

---

## 🌐 ENDPOINTS API DISPONIBLES

### **Test de santé**
```
GET http://localhost:8000/api/health
→ {"status": "OK", "message": "SmartERP Pro API is running"}
```

### **Gestion des produits**
```
GET    /api/v1/products              ← Liste des produits
POST   /api/v1/products              ← Créer un produit
GET    /api/v1/products/{id}         ← Détails produit
PUT    /api/v1/products/{id}         ← Modifier produit
DELETE /api/v1/products/{id}         ← Supprimer produit
```

### **Gestion des ventes**
```
GET    /api/v1/sales                 ← Liste des ventes
POST   /api/v1/sales                 ← Créer une vente
GET    /api/v1/sales/today           ← Ventes du jour
```

### **Dashboard et rapports**
```
GET    /api/v1/dashboard             ← Données tableau de bord
GET    /api/v1/reports/sales         ← Rapport des ventes
GET    /api/v1/reports/top-products  ← Top produits
```

---

## 🧪 TEST DE L'APPLICATION

### **1. Accéder à l'application**
- Ouvrir `http://localhost:5173`
- **Login :** admin@demo.com
- **Mot de passe :** admin123

### **2. Fonctionnalités à tester**

#### **📊 Dashboard**
- Chiffre d'affaires en temps réel
- Nombre de ventes du jour
- Stock total des produits
- Clients actifs

#### **📦 Gestion de stock**
- Ajouter un produit avec code-barres
- Scanner un code-barres
- Gérer les catégories (Outils, Électricité, etc.)
- Alertes stock faible

#### **🛍️ Ventes**
- Créer une facture
- Scanner des produits
- Paiement comptant/crédit
- Génération PDF (simulé)

#### **📈 Rapports & Analyses**
- Évolution des ventes (graphique)
- Top produits vendus
- Ventes par catégorie (camembert)
- Statistiques financières

---

## 🔧 MIGRATION AUTOMATIQUE

L'application détecte automatiquement si l'API Laravel est disponible :

### **Si API disponible :**
- ✅ Utilise Laravel + PostgreSQL
- ✅ Données persistantes
- ✅ Performance optimale

### **Si API non disponible :**
- ⚠️ Fallback vers localStorage
- ⚠️ Mode compatibilité
- ⚠️ Données temporaires

**Interface de migration intégrée** pour une transition transparente.

---

## 📱 INTERFACE UTILISATEUR

### **Design moderne**
- Thème vert professionnel
- Interface responsive (mobile/desktop)
- UX optimisée pour boutiquiers malgaches

### **Fonctionnalités avancées**
- Scanner code-barres intégré
- Graphiques interactifs (Chart.js)
- Notifications en temps réel
- Gestion multi-utilisateurs

---

## 🔒 SÉCURITÉ & PERFORMANCE

### **Backend Laravel**
- Authentification Sanctum (tokens)
- Validation des données
- Protection CSRF
- Logs d'audit

### **Base PostgreSQL**
- Relations optimisées
- Index de performance
- Contraintes d'intégrité
- Transactions ACID

### **Frontend Vue.js**
- Composition API moderne
- Store Pinia (gestion d'état)
- Lazy loading des composants
- Cache intelligent

---

## 🎯 POINTS TECHNIQUES À ÉVALUER

### **Architecture**
- ✅ Séparation frontend/backend claire
- ✅ API REST bien structurée
- ✅ Base de données relationnelle
- ✅ Gestion d'état centralisée

### **Code Quality**
- ✅ Code documenté et commenté
- ✅ Structure modulaire
- ✅ Bonnes pratiques Laravel/Vue
- ✅ Gestion d'erreurs robuste

### **Fonctionnalités**
- ✅ CRUD complet (produits, ventes)
- ✅ Dashboard temps réel
- ✅ Rapports et analytics
- ✅ Scanner code-barres
- ✅ Gestion multi-utilisateurs

---

## 📞 SUPPORT TECHNIQUE

### **En cas de problème :**

1. **Backend ne démarre pas :**
   ```bash
   # Vérifier PHP
   php --version
   
   # Installer dépendances
   composer install
   ```

2. **Frontend ne démarre pas :**
   ```bash
   # Vérifier Node.js
   node --version
   
   # Installer dépendances
   npm install
   ```

3. **Base de données :**
   - Vérifier que PostgreSQL est démarré
   - Base `smarterp_pro` existe
   - Utilisateur `postgres` configuré

---

## 🏆 RÉSUMÉ POUR ÉVALUATION

**Ce projet démontre :**

- **Maîtrise technique :** Laravel 10 + Vue.js 3 + PostgreSQL
- **Architecture moderne :** API REST + SPA
- **Bonnes pratiques :** Code structuré, sécurisé, documenté
- **UX/UI :** Interface professionnelle et intuitive
- **Fonctionnalités métier :** Gestion complète de stock/ventes
- **Scalabilité :** Architecture évolutive et maintenable

**Projet prêt pour la production avec une base technique solide.**

---

## 🚀 DÉMARRAGE IMMÉDIAT

```bash
# Terminal 1 - Backend
cd backend-laravel && php artisan serve

# Terminal 2 - Frontend  
cd frontend && npm run dev

# Navigateur
http://localhost:5173
```

**L'application est prête à être testée !** 🎉
