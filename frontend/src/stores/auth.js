import { computed, ref } from 'vue';
import { defineStore } from 'pinia';
import http from '@/services/http.js';

export const useAuth = defineStore('auth', () => {
  //criando o token no localStorage 
  const token = ref(localStorage.getItem("token"));
  //criando o user no localStorage como string
  const user = ref(JSON.parse(localStorage.getItem("user")));
  const isAuth = ref(false);
  
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

  function setIsAuth(auth) {
    isAuth.value = auth;
  }

  const isAuthenticated = computed(() => {
      return token.value && user.value;
  })

  const fullName = computed(() => {
    if (user.value) {
      return user.value.name;
    }
    return '';
  })

  //checando se o token é valido
  async function checkToken(token) {
    try {
      const tokenAuth = 'Bearer ' + token.value;
      const { data } = await http.get("/auth/verify", {
        headers: {
          Authorization: tokenAuth,
        },
      });
      return data;
    } catch (error) {
      isAuth.value = false;
      console.log(error.response.data);
    }
  }

  function clear() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    isAuth.value = false;
    token.value = '';
    user.value = '';
  }

  return {
    token,
    user,
    setToken,
    setUser,
    checkToken,
    isAuthenticated,
    fullName,
    clear,
    setIsAuth,
    isAuth
  }

})
