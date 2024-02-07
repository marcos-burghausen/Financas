<template>
  <header class="header">
    <mdicon
      v-if="auth.isAuthenticated"
      name="menu-open"
      class="mdicon"
      @click="$emit('expandirMenu')"
    />
    <div style="display: flex; align-items: center;">
      <template v-if="auth.isAuthenticated">
        <span class="text-white me-3 fs-3">
          {{ name }}
        </span>
        <v-btn
          @click="logout"
        >
          sair
        </v-btn>
      </template>
      <mdicon
        v-if="auth.isAuthenticated"
        name="account"
        class="mdicon ms-3"
        @click="$emit('expandirMenu')"
      />
    </div>
  </header>
</template>

<script setup lang="ts">
import { useAuth } from "@/store/auth";
import { useRouter } from "vue-router";
import http from "@/services/http";
import { ref } from "vue";
import { useUserStore } from "@/store/user";

// const props = defineProps({
//     menuExpandido: Boolean
// });
// const titulo = computed(() => props.name);
const router = useRouter();
const auth = useAuth();
const useUser = useUserStore();

let name = ref(useUser.user.name);

async function logout() {
    try {
        await http.post("/logout");
        auth.clear();
        router.push({ name: "home" });
    } catch (error) {
        console.log(error);
    }
}
</script>

<style scoped>
.header {
    padding-inline: 10px;
    margin: 0 0 15px 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
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