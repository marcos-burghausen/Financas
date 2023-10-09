import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

export const useAuth = defineStore('auth', () => {
  const token = ref(localStorage.getItem("token"));
  const user = ref(JSON.parse(localStorage.getItem("user")));

  function setToken(tokenValue: string) {
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
    localStorage.removeItem('revenuesData');
    localStorage.removeItem('expensesData');
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
