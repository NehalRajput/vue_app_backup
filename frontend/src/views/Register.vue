<template>
  <div class="auth-container">
    <div class="auth-box">
      <h1 class="title">Register</h1>
      
      <form @submit.prevent="handleRegister" class="form">
        <AuthInput
          id="name"
          label="Name"
          v-model="form.name"
          type="text"
          placeholder="Enter your name"
          required
        />

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

        <AuthInput
          id="confirm_password"
          label="Confirm Password"
          v-model="form.confirm_password"
          type="password"
          placeholder="Confirm your password"
          required
        />

        <button type="submit" class="submit-button">Register</button>
      </form>

      <p class="switch">
        Already have an account?
        <router-link to="/login" class="link">Login here</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useUserStore } from '../stores/User';
import { useRouter } from 'vue-router';
import AuthInput from '@/components/AuthInput.vue';

const store = useUserStore();
const router = useRouter();

const form = ref({
  name: '',
  email: '',
  password: '',
  confirm_password: ''
});

const handleRegister = async () => {
  // Password confirmation validation
  if (form.value.password !== form.value.confirm_password) {
    alert('Passwords do not match');
    return;
  }
  
  const success = await store.register(form.value);
  if (success) {
    router.push('/'); 
    alert('Registration success.');
  } else {
    alert('Registration failed. Please try again.');
  }
};
</script>

<style scoped>
.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #fff;
}

.auth-box {
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
  margin-bottom: 16px;
  text-align: center;
  width: 100%;
}

.form {
  width: 100%;
}

.submit-button {
  width: 100%;
  padding: 12px;
  background-color: #000;
  color: #fff;
  font-size: 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-top: 8px;
}

.submit-button:hover {
  background-color: #333;
}

.switch {
  margin-top: 16px;
  color: #000;
  font-size: 0.9rem;
}

.link {
  color: #000;
  font-weight: 600;
  text-decoration: underline;
  margin-left: 4px;
}
</style>