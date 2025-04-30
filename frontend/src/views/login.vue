<template>
  <div class="login-container">
    <div class="login-box">
      <h2 class="title">Login</h2>
      <form @submit.prevent="submitLogin" class="form">
        <input v-model="form.email" placeholder="Email" class="input" />
        <input v-model="form.password" type="password" placeholder="Password" class="input" />
        <button type="submit" class="submit-button">Login</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useUserStore } from '../stores/user';
import { useRouter } from 'vue-router';

const router = useRouter();
const store = useUserStore();

const form = ref({ email: '', password: '' });

const submitLogin = async () => {
  const success = await store.login(form.value);
  if (success) router.push('/groups');
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
  flex-direction: column;      /* Stack title over form */
  align-items: center;         /* Center horizontally */
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
  margin-bottom: 16px;         /* Space below title */
  text-align: center;          /* Center text */
  width: 100%;
}

.form {
  width: 100%;                 /* Full width inside box */
}

.input {
  width: 100%;
  padding: 12px;
  margin-bottom: 12px;
  border: 1px solid #000;
  border-radius: 4px;
  font-size: 1rem;
  outline: none;
  color: #000;
}

.input:focus {
  border-color: #000;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
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
}

.submit-button:hover {
  background-color: #333;
}
</style>
