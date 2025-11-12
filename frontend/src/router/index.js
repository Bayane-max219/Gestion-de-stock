import { createRouter, createWebHistory } from 'vue-router'
import LoginPage from '../components/LoginPage.vue'
import LoginBypass from '../components/LoginBypass.vue'
import DashboardPage from '../components/DashboardPage.vue'
import SalesPage from '../components/SalesPage.vue'
import StockPage from '../components/StockPage.vue'
import ReportsPage from '../components/ReportsPage.vue'

const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginPage
  },
  {
    path: '/bypass',
    name: 'Bypass',
    component: LoginBypass
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: DashboardPage
  },
  {
    path: '/sales',
    name: 'Sales',
    component: SalesPage
  },
  {
    path: '/stock',
    name: 'Stock',
    component: StockPage
  },
  {
    path: '/reports',
    name: 'Reports',
    component: ReportsPage
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
