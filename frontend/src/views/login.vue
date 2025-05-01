<template>
  <div class="login-container">
    <div class="login-box">
      <h2 class="title">Login</h2>
      <form @submit.prevent="submitLogin" class="form">
        <AuthInput
          id="email"
          label="Email"
          v-model="form.email"
          type="email"
          placeholder="Enter your email"
          required
        />
        <AuthInput
          id="password"
          label="Password"
          v-model="form.password"
          type="password"
          placeholder="Enter your password"
          required
        />
        <button type="submit" class="submit-button">Login</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useUserStore } from '../stores/User';
import { useRouter } from 'vue-router';
import AuthInput from '@/components/AuthInput.vue';

const router = useRouter();
const store = useUserStore();

const form = ref({
  email: '',
  password: ''
});

const submitLogin = async () => {
  try {
    const success = await store.login(form.value);
    if (success) {
      router.push('/groups');
    }
  } catch (error) {
    console.error('Login failed:', error);
    // Here you could add error handling logic
  }
};
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #fff;
}

.login-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  background-color: #fff;
  padding: 24px;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
}

.title {
  font-size: 2rem;
  color: #000;
  margin-bottom: 24px;
  text-align: center;
  width: 100%;
}

.form {
  width: 100%;
}

.submit-button {
  width: 100%;
  padding: 12px;
  margin-top: 12px;
  background-color: #000;
  color: #fff;
  font-size: 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.submit-button:hover {
  background-color: #333;
}
</style>