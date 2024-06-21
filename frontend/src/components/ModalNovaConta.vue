<template>
  <div style="display: flex; justify-content: flex-end; padding-inline: 10px; margin-top: 10px;">
    <mdicon
      type="button"
      title="adcionar nova categoria"
      name="plus"
      class="mdicon"
      @click="openModal = true"
    />
  </div>

  <div
    v-if="openModal"
    class="container__modal"
  >
    <div class="modal">
      <header class="header__modal">
        <span class="title">Nova Conta</span>
        <mdicon
          class="mdicon__close"
          type="buttom"
          name="close"
          @click="openModal = false; clearInputs()"
        />
      </header>
      <v-form
        v-model="validForm"
        class="form"
        @submit.prevent="salvarConta"
      >
        <ErrorMessage />
        <v-text-field
          v-model="conta.valor"
          autofocus
          density="compact"
          prefix="R$"
          placeholder="0,00"
          variant="outlined"
          type="tel"
          hide-details="auto"
          label="Valor"
          class="mb-7 input"
          @input="formatValueSave()"
        />
        <v-combobox
          v-model="conta.instituicaoFinanceira"
          density="compact"
          variant="outlined"
          :rules="[rules.requiredInstituicaoFinanceira]"
          :items="['BB', 'Sicredi', 'Itaú', 'Nubank']"
          label="Instituição financeira"
          placeholder="Select..."
          class="mb-2 input"
        />
        <v-text-field
          v-model="conta.descricao"
          density="compact"
          variant="outlined"
          type="text"
          hide-details="auto"
          label="Descriçao"
          class="mb-7 input"
        />
        <v-autocomplete
          v-model="conta.tipoConta"
          density="compact"
          variant="outlined"
          :rules="[rules.requiredTipoConta]"
          :items="['Conta Corrente', 'Poupança', 'Investimentos']"
          placeholder="Select..."
          label="Tipo de Conta"
          class="mb-2 input"
        />
        <button
          :disabled="loading || !validForm"
          :loading="loading"
          class="btn btn__modal"
          type="submit"
        >
          Salvar
        </button>
      </v-form>
      
      <!-- <footer class="footer__modal">
        
      </footer> -->
    </div>
  </div>
  <ErrorsForm />
</template>
<script setup lang="ts">
import ErrorsForm from "@/components/ModalErrorsForm.vue";
import ErrorMessage from "@/components/ErrorMessage.vue";

import { useWalletsStore } from "@/store/wallets";
import { useErrorStore } from "@/store/error";
import { useUserStore } from "@/store/user";

const useWallets = useWalletsStore();
const errorStore = useErrorStore();
const useUser = useUserStore();
const emit = defineEmits([
    "updateContas",
]);

let validForm = ref(false);
let loading = ref(false);

let conta = ref({
    user_id: "",
    valor: "",
    instituicaoFinanceira: "",
    descricao: "",
    tipoConta: "",
});

const salvarConta = async() => {
    conta.value.user_id = useUser.user.id;
    try {
        const res = await http.post("/save-wallet", conta.value);
        useWallets.setWalletsData(res.data.wallets);
        if (res.data.wallets) {
            emit("updateContas", res.data.wallets);
        }

        console.log(res.data);
        clearInputs();
        openModal.value = false;
    } catch (error) {
        if (error.response.data.errors) {
            errorStore.setErrorFromForm(error);
        } else {
            errorStore.setErrorFromResponse(error);
        }
    } finally {
        loading.value = false;
    }
};

const clearInputs = () => {
    conta.value.user_id = "";
    conta.value.valor = "";
    conta.value.instituicaoFinanceira = "";
    conta.value.descricao = "";
    conta.value.tipoConta = "";
};

const formatValueSave = () => {
    let novoValor = conta.value.valor.replace(/[^\d]/g, "");

    if (novoValor.length > 1) {
        const parteInteira = novoValor.slice(0, -2).replace(/^0+/, "") || "0";
        const parteDecimal = novoValor.slice(-2);
        const parteInteiraFormatada = parteInteira.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        conta.value.valor = `${parteInteiraFormatada},${parteDecimal}`;
    } else if (novoValor.length === 1) {
        conta.value.valor = `0,0${novoValor}`;
    } else {
        conta.value.valor = "0,00";
    }
};

const rules = {
    requiredInstituicaoFinanceira: (value: string) =>
        !!value || "O campo instituição financeira é obrigatório",
    requiredTipoConta: (value: string) =>
        !!value || "O campo tipo de conta é obrigatório",
};





import ModalColors from "@/components/ModalColors.vue";
import ModalIcons from "@/components/ModalIcons.vue";

import { reactive, ref } from "vue";
import http from "@/services/http";


const selectedColor = ref("");
const selectedIcon = ref("");
const openModal = ref(false);
const nameCategory = ref("");
const props = defineProps({
    color: {
        type: String,
    }
});




const updateSelectedIcon = (novoValor: string) => {
    selectedIcon.value = novoValor;
};
const updateSelectedColor = (novoValor: string) => {
    selectedColor.value = novoValor;
};
const saveCategory = async () => {
    const data = ref({
        name: nameCategory.value,
        color: selectedColor.value,
        icon: selectedIcon.value,
        typeCategory: "",
        edit: true
    });
    try {
        data.value.typeCategory = props.color === "color__despesa" ? "despesa" : "receita";

        const res = await http.post("/save-category", data.value);
        useUser.setUserData(res.data.user);
        if (res.data.categoriasDespesas) {
            emit("updateCategoriasDespesas", res.data.categoriasDespesas);
        }
        if (res.data.categoriasReceitas) {
            emit("updateCategoriasReceitas", res.data.categoriasReceitas);
        }
        nameCategory.value = "";
        selectedColor.value = "";
        selectedIcon.value = "";
        openModal.value = false;
    } catch (error) {
        // console.log(error);
    }
};



</script>

<style scoped>
.mdicon {
    width: 65px;
    height: 65px;
    cursor: pointer;
    padding: 10px;
    border: 1px solid #77d08e;
    border-radius: 40px;
    color: #77d08e;
    display: flex;
    justify-content: center;
    align-items: center
}


.container__modal {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.50);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 999;
}


.modal {
    background: #2c2c2e;
    position: relative;
    color: #fefefe;
    z-index: 999;
    /* top: 20%; */
    /* left: 50%; */
    width: 95%;
    max-width: 500px;
    height: auto;
    /* margin-left: 2.5%; */
    border-radius: 20px;
    padding: 15px;
}

.header__modal {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.title {
    text-align: center;
    color: #fefefe;
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 10px
}

.mdicon__close {
    cursor: pointer;
    color: rgba(255, 255, 255, 0.3);
}

.mdicon__close:hover {
    color: #fefefe;
}

.inputSimples {
    background-color: #1e1e1e;
    margin: 20px 0 0 0;
    display: flex;
    align-items: center;
    padding-left: 5px;
    position: relative;
    border-radius: 5px;
}

input {
    color: #ccc;
    width: 300px;
    height: 40px;
    background-color: transparent;
    border: 0;
    outline: 0;
}

.input:internal-autofill-selected {
    background-color: transparent;
}

.label {
    color: #ccc;
    position: absolute;
    left: 10px;
    top: 8px;
    opacity: 0.4;
    cursor: text;
    transition: 0.5s ease-in-out;
}

.input:focus~label,
.input:valid~label {
    transform: translateY(-30px);
    opacity: 0.9;
}

.error {
    height: 20px;
}

.span__error {
    color: rgb(194, 4, 4);
    position: relative;
    top: 0;
    left: 0;
}

.cor__icon {
    display: flex;
}
.form {
    display: flex;
    flex-direction: column;
    justify-content: center
}

.container__cor__categoria {
    width: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.cor__categoria {
    width: 100%;
    display: flex;
    justify-content: space-around;
    margin-block: 10px;
}

.cor__forma {
    width: 25px;
    height: 25px;
    border-radius: 50%;
}

.icon__categoria {
    width: 100%;
    display: flex;
    justify-content: space-around;
    margin-block: 10px;
}

.footer__modal {
    margin-top: 20px;
    display: flex;
    justify-content: end;
}
.btn {
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


.v-btn--disabled.v-btn--variant-elevated {
    background: rgba(255, 255, 255, 0.12) !important;
    color: rgba(255, 255, 255, 0.3);
    border: none
  }
.cor__1 {
    background: #ff8a00;
}

.cor__2 {
    background: #cc0000;
}

.cor__3 {
    background: #2cb1e1;
}

.cor__4 {
    background: #c58be2;
}

.cor__5 {
    background: #99cc00;
}

.cor__6 {
    background: #c5e26d;
}

.cor__7 {
    background: #9933cc;
}

.cor__8 {
    background: #3b3b3b;
}

.cor__9 {
    background: #686868;
}

.cor__10 {
    background: #ff4444;
}

.cor__11 {
    background: #2a14ff;
}

.cor__12 {
    background: #d6adeb;
}

.cor__13 {
    background: #bcbcbc;
}

.cor__14 {
    background: #669900;
}

.cor__15 {
    background: #439996;
}

.cor__16 {
    background: #ffbd21;
}

.cor__17 {
    background: #ff9494;
}

.cor__18 {
    background: #8f8f8f;
}

.cor__19 {
    background: #8ad5f0;
}

.cor__20 {
    background: #000000;
}

.cor__21 {
    background: #24847a;
}

.cor__22 {
    background: #a2b6c2;
}

.cor__23 {
    background: #930101;
}

.cor__24 {
    background: #bb6e00;
}

.cor__25 {
    background: #0099cc;
}
</style>