<template>
  <div class="dashboard-container">
    <!-- Header -->
    <header class="header">
      <div class="header-content">
        <div class="header-left">
          <div class="logo">
            <svg class="logo-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <h1>SmartERP Pro</h1>
          </div>
        </div>
        <div class="header-right">
          <span class="user-info">Admin - Boutique Demo</span>
          <button @click="logout" class="logout-btn">Déconnexion</button>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Welcome Section -->
      <div class="welcome-section">
        <h2>Bienvenue dans votre boutique ! 🏪</h2>
        <p>Tableau de bord - Gestion moderne pour boutiques malgaches</p>
      </div>

      <!-- Stats Cards -->
      <div class="stats-grid">
        <div class="stat-card green">
          <div class="stat-icon">💰</div>
          <div class="stat-info">
            <h3>CA Aujourd'hui</h3>
            <p class="stat-value">125,000 Ar</p>
          </div>
        </div>

        <div class="stat-card blue">
          <div class="stat-icon">🛍️</div>
          <div class="stat-info">
            <h3>Ventes Aujourd'hui</h3>
            <p class="stat-value">47</p>
          </div>
        </div>

        <div class="stat-card yellow">
          <div class="stat-icon">📦</div>
          <div class="stat-info">
            <h3>Produits en Stock</h3>
            <p class="stat-value">1,234</p>
          </div>
        </div>

        <div class="stat-card red">
          <div class="stat-icon">⚠️</div>
          <div class="stat-info">
            <h3>Alertes Stock</h3>
            <p class="stat-value">8</p>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="actions-grid">
        <div class="action-card">
          <h3>🛍️ Nouvelle Vente</h3>
          <p>Créer une nouvelle facture de vente</p>
          <button class="action-btn green">Nouvelle Vente</button>
        </div>

        <div class="action-card">
          <h3>📦 Gestion Stock</h3>
          <p>Voir et gérer l'inventaire</p>
          <button class="action-btn blue">Voir Stock</button>
        </div>

        <div class="action-card">
          <h3>📊 Rapports</h3>
          <p>Statistiques et analyses</p>
          <button class="action-btn purple">Voir Rapports</button>
        </div>
      </div>

      <!-- Recent Sales -->
      <div class="recent-sales">
        <div class="section-header">
          <h3>Ventes Récentes</h3>
        </div>
        <div class="sales-list">
          <div class="sale-item">
            <div class="sale-info">
              <h4>Facture #001</h4>
              <p>Client: Rakoto Jean</p>
            </div>
            <div class="sale-amount">
              <span class="amount">15,500 Ar</span>
              <span class="time">Il y a 2h</span>
            </div>
          </div>

          <div class="sale-item">
            <div class="sale-info">
              <h4>Facture #002</h4>
              <p>Client: Rabe Marie</p>
            </div>
            <div class="sale-amount">
              <span class="amount">8,200 Ar</span>
              <span class="time">Il y a 3h</span>
            </div>
          </div>

          <div class="sale-item">
            <div class="sale-info">
              <h4>Facture #003</h4>
              <p>Client: Andry Paul</p>
            </div>
            <div class="sale-amount">
              <span class="amount">22,800 Ar</span>
              <span class="time">Il y a 4h</span>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
function logout() {
  alert('Déconnexion simulée - Retour à la page de connexion')
  window.location.href = '/login'
}
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.dashboard-container {
  min-height: 100vh;
  background: #f8fafc;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Header */
.header {
  background: white;
  border-bottom: 1px solid #e2e8f0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.header-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-left {
  display: flex;
  align-items: center;
}

.logo {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo-icon {
  width: 32px;
  height: 32px;
  color: #047857;
  background: #d1fae5;
  padding: 6px;
  border-radius: 8px;
}

.logo h1 {
  font-size: 20px;
  font-weight: 700;
  color: #1f2937;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.user-info {
  font-size: 14px;
  color: #6b7280;
}

.logout-btn {
  background: #ef4444;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  transition: background 0.2s;
}

.logout-btn:hover {
  background: #dc2626;
}

/* Main Content */
.main-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.welcome-section {
  margin-bottom: 2rem;
}

.welcome-section h2 {
  font-size: 28px;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 8px;
}

.welcome-section p {
  color: #6b7280;
  font-size: 16px;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
  border-left: 4px solid;
}

.stat-card.green { border-left-color: #10b981; }
.stat-card.blue { border-left-color: #3b82f6; }
.stat-card.yellow { border-left-color: #f59e0b; }
.stat-card.red { border-left-color: #ef4444; }

.stat-icon {
  font-size: 2rem;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: #f8fafc;
}

.stat-info h3 {
  font-size: 14px;
  color: #6b7280;
  margin-bottom: 4px;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
  color: #1f2937;
}

/* Actions Grid */
.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.action-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.action-card h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
}

.action-card p {
  color: #6b7280;
  margin-bottom: 1rem;
}

.action-btn {
  width: 100%;
  padding: 12px 20px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  color: white;
}

.action-btn.green {
  background: #10b981;
}

.action-btn.blue {
  background: #3b82f6;
}

.action-btn.purple {
  background: #8b5cf6;
}

.action-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Recent Sales */
.recent-sales {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.section-header {
  padding: 1.5rem;
  border-bottom: 1px solid #e2e8f0;
}

.section-header h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.sales-list {
  padding: 1.5rem;
}

.sale-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 0;
  border-bottom: 1px solid #f1f5f9;
}

.sale-item:last-child {
  border-bottom: none;
}

.sale-info h4 {
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.sale-info p {
  font-size: 14px;
  color: #6b7280;
}

.sale-amount {
  text-align: right;
}

.amount {
  display: block;
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.time {
  font-size: 14px;
  color: #6b7280;
}

/* Responsive */
@media (max-width: 768px) {
  .header-content {
    padding: 1rem;
    flex-direction: column;
    gap: 1rem;
  }

  .main-content {
    padding: 1rem;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .actions-grid {
    grid-template-columns: 1fr;
  }
}
</style>
