<template>
  <div class="app-layout">
    <header class="app-header">
      <h1>Expense Tracker</h1>
      <nav class="nav-links">
        <router-link to="/">Dashboard</router-link>

        <!-- Show these links only if the user is logged in -->
        <router-link to="/expenses" v-if="store.token">Expenses</router-link>
        <router-link to="/groups" v-if="store.token">Groups</router-link>

        <!-- Show Login/Register links if the user is not logged in -->
        <router-link to="/login" v-if="!store.token">Login</router-link>
        <router-link to="/register" v-if="!store.token">Register</router-link>

        <!-- Show Logout button if the user is logged in -->
        <button @click="logout" v-if="store.token">Logout</button>
      </nav>
    </header>
    
    <main class="app-main">
      <slot></slot>
    </main>
    
    <footer class="app-footer">
      <p>© 2025 Expense Tracker</p>
    </footer>
  </div>
</template>

<script setup>
import { useUserStore } from '@/stores/User';
import '@/assets/css/AppLayout.css';


import { useRouter } from 'vue-router';

const store = useUserStore();
const router = useRouter();

const logout = () => {
  store.logout(); // Clears the token and user from the store
  router.push('/'); // Redirect to home page
};
</script>
