import { createRouter, createWebHistory } from 'vue-router'
import LoginPage from '../components/LoginPage.vue'
import DashboardPage from '../components/DashboardPage.vue'
import SalesPage from '../components/SalesPage.vue'
import StockPage from '../components/StockPage.vue'
import ReportsPage from '../components/ReportsPage.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      redirect: '/login'
    },
    {
      path: '/login',
      name: 'login',
      component: LoginPage
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: DashboardPage
    },
    {
      path: '/sales',
      name: 'sales',
      component: SalesPage
    },
    {
      path: '/stock',
      name: 'stock',
      component: StockPage
    },
    {
      path: '/reports',
      name: 'reports',
      component: ReportsPage
    }
  ]
})

export default router