# 🏪 SmartERP Pro - Système de Gestion de Stock

Un système ERP complet pour la gestion de stock et ventes, développé avec **Vue.js 3** et **Laravel** (avec support localStorage pour fonctionnement hors-ligne).

## 🎯 Fonctionnalités Principales

- ✅ **Gestion de Stock Complète** - Ajout, modification, suppression de produits
- ✅ **Point de Vente (POS)** - Interface de vente rapide et intuitive  
- ✅ **Dashboard Analytics** - Graphiques et statistiques en temps réel
- ✅ **Gestion Multi-Utilisateurs** - Comptes séparés par email
- ✅ **Rapports Détaillés** - CA, ventes, stock par période
- ✅ **Sauvegarde Automatique** - Données persistantes en localStorage
- ✅ **Interface Responsive** - Compatible mobile et desktop
- ✅ **Support Hors-Ligne** - Fonctionne sans connexion internet

## 🛠️ Technologies Utilisées

### Frontend
- **Vue.js 3** (Composition API)
- **Vite** (Build tool)
- **Vue Router** (Navigation)
- **Pinia** (State management)
- **TailwindCSS** (Styling)
- **Chart.js** (Graphiques)

### Backend (Optionnel)
- **Laravel 10** (API REST)
- **MySQL** (Base de données)
- **Sanctum** (Authentification)

## 📦 Installation Rapide

### Prérequis
- **Node.js 16+** (pour le frontend Vue.js)
- **PHP 8.0+** (optionnel, pour Laravel)
- **MySQL** (optionnel, pour la base de données)

### Installation Frontend (Principal)

```bash
# 1. Cloner le projet
git clone https://github.com/Bayane-max215/BelloCode.git
cd BelloCode

# 2. Installer les dépendances
cd frontend
npm install

# 3. Lancer le serveur de développement
npm run dev

# 4. Ouvrir dans le navigateur
# http://localhost:5174
```

### Comptes de Démonstration

```
👤 Franco (Pharmacie)
Email: franco@gmail.com
Mot de passe: I love teko.

👤 Fatima (Quincaillerie)  
Email: fatima@gmail.com
Mot de passe: quincaillerie
```

### Installation Backend Laravel (Optionnel)

```bash
# Si vous voulez utiliser l'API Laravel
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

## 📸 Screenshots

### 01 – Page d’inscription
![01 – Inscription](screenshots/01-Inscrition.png)

### 02 – Page de connexion
![02 – Connexion](screenshots/02-Connexion.png)

### 03 – Tableau de bord (vue globale)
![03 – Tableau de bord](screenshots/03-Tableau_de_bord.png)

### 04 – Ajout de stock
![04 – Ajout de stock](screenshots/04-Ajout_de_stock.png)

### 05 – Vente (écran 1)
![05 – Vente 1](screenshots/05-Vente1.png)

### 06 – Scan du produit (code-barres)
![06 – Scan produit](screenshots/06-Scan_produit.png)

### 07 – Vente (écran 2 / confirmation)
![07 – Vente 2](screenshots/07-Vente2.png)

### 08 – Rapport de ventes (vue 1)
![08 – Rapport 1](screenshots/08-Rapport1.png)

### 09 – Rapport de ventes (vue 2)
![09 – Rapport 2](screenshots/09-Rapport2.png)

### 10 – Rapport détaillé (vue 3)
![10 – Rapport 3](screenshots/10-Rapport3.png)

### 11 – Vue détaillée du stock
![11 – Stock](screenshots/11-Stock.png)

## 🎯 Fonctionnalités Détaillées

### 📊 Dashboard
- Vue d'ensemble des ventes du jour
- Graphiques d'évolution hebdomadaire
- Statistiques en temps réel
- Alertes de stock faible

### 📦 Gestion de Stock
- Ajout/modification de produits
- Catégorisation automatique
- Gestion des codes-barres
- Suivi des quantités

### 🛍️ Point de Vente
- Interface rapide de vente
- Calcul automatique des totaux
- Gestion des clients
- Historique des transactions

### 📈 Rapports
- Chiffre d'affaires par période
- Analyse des ventes par catégorie
- Graphiques interactifs
- Export des données

## 📝 Licence

Ce projet est développé par **Bayane-max219** pour des fins éducatives et de démonstration.

## 🤝 Contact

- **GitHub**: [Bayane-max219](https://github.com/Bayane-max219)
- **Email**: baymi312@gmail.com

---

⭐ **N'hésitez pas à mettre une étoile si ce projet vous plaît !** ⭐