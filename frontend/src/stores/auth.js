import { computed, ref } from 'vue';
import { defineStore } from 'pinia';
import { userData } from "@/stores/data.js";
import http from '@/services/http.js';

export const useAuth = defineStore('auth', () => {
  //criando o token no localStorage 
  const token = ref(localStorage.getItem("token"));
  //criando o user no localStorage como string
  const user = ref(JSON.parse(localStorage.getItem("user")));

  //armazenando o token
  function setToken(tokenValue) {
    localStorage.setItem('token', tokenValue);
    token.value = tokenValue;
  }

  //armazenando o user
  function setUser(userValue) {
    localStorage.setItem('user', JSON.stringify(userValue));
    user.value = userValue;
  }

  const isAuthenticated = computed(() => {
    return token.value && user.value;
  })

  function clear() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    localStorage.removeItem('data');
    token.value = '';
    user.value = '';
  }

  return {
    token,
    user,
    setToken,
    setUser,
    isAuthenticated,
    clear,
  }

})
