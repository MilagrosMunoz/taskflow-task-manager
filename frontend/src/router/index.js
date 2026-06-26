import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import DashboardView from '../views/DashboardView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: LoginView,
      meta: { requiresAuth: false }
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: DashboardView,
      meta: { requiresAuth: true }
    }
  ]
})

// Guardián de seguridad: Verifica el token antes de cambiar de página
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('TOKEN')

  if (to.meta.requiresAuth && !token) {
    next({ name: 'login' })
  } else if (!to.meta.requiresAuth && token && to.name === 'login') {
    next({ name: 'dashboard' })
  } else {
    next()
  }
})

export default router