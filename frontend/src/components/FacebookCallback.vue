<template>
  <div class="facebook-callback">
    <div
      v-if="loading"
      class="loading-message"
    >
      Processando login do Facebook...
    </div>
    <div
      v-if="erro"
      class="error-message"
    >
      <span>
        {{ erro }}
      </span>
      <router-link
        class="btn"
        :to="{ name: 'home' }"
      >
        <v-btn
          class="btn"
        >
          voltar para tela de login
        </v-btn>
      </router-link>
    </div>
    <!-- <ErrorMessage /> -->
  </div>
</template>
  
<script setup>
// import ErrorMessage from "@/components/ErrorMessage.vue";

import http from "@/services/http";
import { useAuthStore } from "@/store/auth";
import { useErrorStore } from "@/store/error";
import { useExpensesStore } from "@/store/expenses";
import { useRevenuesStore } from "@/store/revenues";
import { useRolesStore } from "@/store/roles";
import { useUserStore } from "@/store/user";
import { useWalletsStore } from "@/store/wallets";
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const useAuth = useAuthStore();
const useUser = useUserStore();
const useExpenses = useExpensesStore();
const useRevenues = useRevenuesStore();
const useWallets = useWalletsStore();
const errorStore = useErrorStore();
const rolesStore = useRolesStore();

const loading = ref(true);
const erro = ref(null);

const handleFacebookCallback = async () => {
    const urlParams = new URLSearchParams(window.location.search);
    const code = urlParams.get("code");

    if (!code) {
        error.value = "Nenhum código de autenticação encontrado na URL.";
        loading.value = false;
        setTimeout(() => router.push("/login?error=no_code"), 3000);
        return;
    }
    try {
        const response = await http.get("auth/callback", {
            params: { code: code }
        });
   
        if (response.data.token && response.data.user) {
            useAuth.setToken(response.data.token);
            useUser.setUserData(response.data.user);
            useExpenses.setExpensesData(response.data.userData.expensesData);
            useRevenues.setRevenuesData(response.data.userData.revenuesData);
            useWallets.setWalletsData(response.data.userData.walletsData);
            // data.setTotalCreditCard(response.data.userData.totalCreditCard);
            // data.setTotalBalance(response.data.userData.totalBalance);
            
            // Carregar permissões e roles do usuário após login do Facebook
            await rolesStore.fetchMyPermissions();
            
            router.push({name:"dashboard"});
        } else {
            throw new Error("Dados de autenticação incompletos na resposta");
        }
    } catch (error) {
        console.log("Erro ao processar callback do Facebook");
        errorStore.setErrorFromResponse(error);
        erro.value = "Falha na autenticação. Por favor, tente novamente mais tarde.";
        // setTimeout(() => router.push("/login?error=auth_failed"), 3000);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    handleFacebookCallback();
});
</script>

<style scoped>
.facebook-callback {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
  color: aliceblue;
}

.loading-message, .error-message {
  text-align: center;
  padding: 20px;
  border-radius: 5px;
}

.error-message {
  display: flex;
  flex-direction: column;
  background-color: rgba(255, 0, 0, 0.1);
  border: 1px solid red;
}
btn {
    border-radius: 15px;
    text-transform: uppercase;
    color: #fff;
    font-size: 10px;
    padding: 5px;
    cursor: pointer;
    font-weight: bold;
    width: 200px;
    align-self: center;
    border: none;
    margin-top: 1rem;
    font-size: 20px;
    background-color: #77d08e;
    border: 1px solid #77d08e;
    transition: background-color .5s;
}
</style>