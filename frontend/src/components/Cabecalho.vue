<template>
  <header>
    <div>
      <!-- <template v-if="!auth.isAuthenticated"> -->
      <router-link class="router-link" :to="{ name: 'login' }"
        >Login</router-link
      >
      <router-link class="router-link" :to="{ name: 'cadastro' }"
        >Cadastro</router-link
      >
      <!-- </template> -->
    </div>
  </header>
</template>

<script setup>
import { ref } from "vue";
</script>

<style scoped>
header {
  /* margin: 0 0 15px 0; */
  display: flex;
  align-items: center;
  justify-content: flex-end;
  box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
  height: 60px;
}
header .router-link {
  color: #0097a7;
  margin-right: 15px;
  text-decoration: none;
  font-size: 20px;
}
/* .btn-expandir {
  color: #0097a7;
  cursor: pointer;
  margin: 20px;
  padding: 10px;
  box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
  border-radius: 20px;
} */
</style>