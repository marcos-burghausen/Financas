<template>
    <header>
        <div class="d-flex">
            <mdicon v-if="auth.isAuthenticated" name="menu-open" @click="$emit('expandirMenu')" class="mdicon" />
            <template v-if="auth.isAuthenticated">
                <h3 class="text-white me-3">{{ name }}</h3>
                <button @click="logout">sair</button>
            </template>
            <template v-if="!auth.isAuthenticated">
                <router-link class="router-link" :to="{ name: 'login' }">Login</router-link>
                <router-link class="router-link" :to="{ name: 'cadastro' }">Cadastro</router-link>
            </template>
        </div>
    </header>
</template>

<script setup>
import http from "@/services/http.js";
import { useAuth } from "@/stores/auth.js";
import { useRouter } from "vue-router";
import { userData } from "@/stores/data.js";
import { ref, computed } from "vue";

const props = defineProps({
    menuExpandido: Boolean
});
const titulo = computed(() => props.name);

const router = useRouter();
const auth = useAuth();
const data = userData();

let name = ref('');

name = auth.user;


async function logout() {
    try {
        const { data } = await http.post("/logout");
        auth.clear();
        router.push({ name: "home" });
    } catch (error) {
        console.log(error);
    }
}
</script>

<style scoped>
.mdicon {
    color: #77d08e;
    cursor: pointer;
    padding: 10px;
    box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
    border-radius: 20px;
}

header {
    padding-left: 10px;
    margin: 0 0 15px 0;
    display: flex;
    align-items: center;
    justify-content: flex-start;
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