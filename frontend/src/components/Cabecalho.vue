<template>
    <header class="header">
        <mdicon v-if="auth.isAuthenticated" name="menu-open" @click="$emit('expandirMenu')" class="mdicon" />
        <template v-if="auth.isAuthenticated">
            <h3 class="text-white me-3">{{ name }}</h3>
            <button @click="logout">sair</button>
        </template>
    </header>
</template>

<script setup>
import { userData } from "@/stores/data.js";
import { useAuth } from "@/stores/auth.js";
import { useRouter } from "vue-router";
import http from "@/services/http.js";
import { ref, computed } from "vue";

const props = defineProps({
    menuExpandido: Boolean
});
const titulo = computed(() => props.name);
const router = useRouter();
const data = userData();
const auth = useAuth();

let name = ref(null);

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
.header {
    padding-left: 10px;
    margin: 0 0 15px 0;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
    background-color: rgba(0, 0, 0, 0.1);
    height: 60px;
}

.mdicon {
    color: #77d08e;
    cursor: pointer;
    padding: 10px;
    box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
    border-radius: 20px;
}
</style>