import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../views/Dashboard.vue';
import Expenses from '../views/Expenses.vue';
import Groups from '../views/Groups.vue';
import { useUserStore } from '../stores/User';  

const routes = [
  { path: '/', component: Dashboard},
  { path: '/expenses', component: Expenses, meta: { requiresAuth: true } },  // Protect this route
  { path: '/groups', component: Groups, meta: { requiresAuth: true } },      // Protect this route
  { path: '/login', component: () => import('../views/login.vue') },
  { path: '/register', component: () => import('../views/Register.vue') },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Route guard to check authentication
router.beforeEach((to, from, next) => {
  const store = useUserStore();

  // If the route requires authentication, and the user is not logged in, redirect to login
  if (to.meta.requiresAuth && !store.token) {
    next('/login');
  } else {
    next();
  }
});

export default router;
