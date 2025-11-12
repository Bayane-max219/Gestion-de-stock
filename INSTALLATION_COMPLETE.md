# 🚀 INSTALLATION COMPLÈTE - Backend Laravel + Frontend Vue.js + PostgreSQL

## 📋 PRÉREQUIS :
- ✅ PHP 8.1+ avec Composer
- ✅ Node.js 18+ avec npm
- ✅ PostgreSQL 14+
- ✅ Git

## 🗄️ ÉTAPE 1 : Configurer PostgreSQL

### 1.1 Créer la base de données :
```sql
-- Dans pgAdmin ou psql
CREATE DATABASE smarterp_pro;
```

## 🎯 ÉTAPE 2 : Créer le Backend Laravel

### 2.1 Exécuter le script de création :
```bash
# Double-cliquer sur SETUP_BACKEND_LARAVEL.bat
# OU exécuter manuellement :
cd "C:\Users\Miguel\Desktop\Applikcation Octobre\Projet gestion de stock"
composer create-project laravel/laravel backend-laravel
```

### 2.2 Configurer l'environnement :
```bash
cd backend-laravel
cp .env.example .env
# Copier la configuration PostgreSQL depuis CONFIG_POSTGRESQL.env
php artisan key:generate
```

### 2.3 Installer les dépendances :
```bash
composer require laravel/sanctum fruitcake/laravel-cors
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 2.4 Créer les migrations :
```bash
php artisan migrate:fresh
php artisan db:seed
```

## 🎨 ÉTAPE 3 : Configurer le Frontend Vue.js

### 3.1 Installer Axios pour les API :
```bash
cd ../frontend
npm install axios
```

### 3.2 Modifier les composants pour utiliser l'API :
- Remplacer localStorage par appels API
- Utiliser le service api.js créé

## 🔗 ÉTAPE 4 : Tester la communication

### 4.1 Démarrer le backend :
```bash
cd backend-laravel
php artisan serve
# API disponible sur : http://localhost:8000
```

### 4.2 Démarrer le frontend :
```bash
cd ../frontend  
npm run dev
# Frontend disponible sur : http://localhost:5173
```

### 4.3 Tester les endpoints :
```bash
# Test API
curl http://localhost:8000/api/v1/products
curl http://localhost:8000/api/v1/dashboard
```

## ✅ RÉSULTAT ATTENDU :

```
📁 Projet gestion de stock/
├── 📁 backend-laravel/     ← API Laravel (Port 8000)
├── 📁 frontend/            ← Vue.js (Port 5173)  
└── 🗄️ PostgreSQL          ← Base smarterp_pro
```

**Frontend Vue.js → API Laravel → PostgreSQL**

## 🎯 PROCHAINES ÉTAPES :
1. Créer les migrations Laravel
2. Créer les contrôleurs API
3. Modifier les composants Vue.js
4. Tester la synchronisation complète
