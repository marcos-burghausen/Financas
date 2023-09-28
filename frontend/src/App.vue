<template>
    <div class="d-flex h-100">
        <template v-if="auth.isAuthenticated">
            <MenuLateral :menuExpandido="menuExpandido" />
        </template>
        <div class="containerApp">
            <template v-if="auth.isAuthenticated">
                <Cabecalho v-on:expandirMenu="menuExpandido = !menuExpandido" :menuExpandido="menuExpandido" />
            </template>
            <router-view />
        </div>
    </div>
</template>

<script setup>
import MenuLateral from "@/components/MenuLateral.vue"
import Cabecalho from "@/components/Cabecalho.vue";

import { ref } from "vue";
import { useAuth } from "@/stores/auth.js";

const auth = useAuth();

const menuExpandido = ref(true);
window.addEventListener('resize', function () {
    let width = this.window.innerWidth;
    console.log(width);
    if (width < 900) {
        menuExpandido.value = false;
    } else {
        menuExpandido.value = true;
    }
})
// let uri = ref('');
// uri = window.location.pathname;
// console.log(uri);
</script>

<style scoped>
.containerApp {
    width: 100%;
}
</style>
