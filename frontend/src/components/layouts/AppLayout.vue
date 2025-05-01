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


import { useRouter } from 'vue-router';

const store = useUserStore();
const router = useRouter();

const logout = () => {
  store.logout(); // Clears the token and user from the store
  router.push('/'); // Redirect to home page
};
</script>
<style scoped>
.app-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.app-header {
  background-color: #111827;
  color: white;
  padding: 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.app-header h1 {
  margin: 0;
  font-size: 1.5rem;
}

.nav-links {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.nav-links a {
  color: white;
  text-decoration: none;
  padding: 0.5rem;
  border-radius: 4px;
}

.nav-links a:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

.nav-links a.router-link-exact-active {
  background-color: rgba(255, 255, 255, 0.3);
}

button {
  background: transparent;
  border: 1px solid white;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
}

button:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

.app-main {
  flex: 1;
  padding: 1rem;
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
  color: white;
}

.app-footer {
  background-color: #111827;
  color: white;
  padding: 1rem;
  text-align: center;
}
</style>
