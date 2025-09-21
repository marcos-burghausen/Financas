<template>
  <v-icon
    type="button"
    title="adcionar nova categoria"
    icon="mdi-plus"
    class="mdicon"
    :class="props.color"
    @click="openModal = true"
  />

  <div
    v-if="openModal"
    class="container__modal"
  >
    <div class="modal">
      <header class="header__modal">
        <span class="title">Cadastrar nova categoria</span>
        <v-icon
          class="mdicon__close"
          type="buttom"
          icon="mdi-close"
          @click="openModal = false; selectedIcon = ''; selectedColor = ''; nameCategory = ''"
        />
      </header>
      <div class="inputSimples">
        <input
          id="descricao"
          v-model="nameCategory"
          type="text"
          name="categori"
          class="input"
          required
        >
        <label
          for="descricao"
          class="label"
        >Nome</label>
      </div>
      <!-- <div class="error">
            <span v-if="errorsForm['errors'].descricao" class="span-error">{{
                errorsForm["errors"].descricao[0]
            }}</span>
        </div> -->
      <div class="cor__icon">
        <div class="container__cor__categoria">
          <div class="cor__categoria">
            <span>
              cor da categoria
            </span>
            <div
              v-if="selectedColor"
              class="cor__forma"
              :class="selectedColor"
            />
          </div>
          <ModalColors
            :items="colors"
            @atualizarVariavel="updateSelectedColor"
          />
        </div>
        <div class="container__cor__categoria">
          <div class="icon__categoria">
            <span>
              icone da categoria
            </span>
            <v-icon
              v-if="selectedIcon"
              :icon="selectedIcon"
            />
          </div>
          <ModalIcons
            :items="icons"
            @atualizarVariavel="updateSelectedIcon"
          />
        </div>
      </div>
      <footer class="footer__modal">
        <button
          class="btn__modal"
          @click="saveCategory"
        >
          Salvar
        </button>
      </footer>
    </div>
  </div>
</template>
<script setup lang="ts">
import ModalColors from "@/components/ModalColors.vue";
import ModalIcons from "@/components/ModalIcons.vue";

import { useUserStore } from "@/store/user";
import { reactive, ref } from "vue";
import http from "@/services/http";

const useUser = useUserStore();

const selectedColor = ref("");
const selectedIcon = ref("");
const openModal = ref(false);
const nameCategory = ref("");
const props = defineProps({
    color: {
        type: String,
    }
});

const emit = defineEmits([
    "updateCategoriasDespesas",
    "updateCategoriasReceitas"
]);
const colors = reactive([
    { color: "cor__1" },
    { color: "cor__2" },
    { color: "cor__3" },
    { color: "cor__4" },
    { color: "cor__5" },
    { color: "cor__6" },
    { color: "cor__7" },
    { color: "cor__8" },
    { color: "cor__9" },
    { color: "cor__10" },
    { color: "cor__11" },
    { color: "cor__12" },
    { color: "cor__13" },
    { color: "cor__14" },
    { color: "cor__15" },
    { color: "cor__16" },
    { color: "cor__17" },
    { color: "cor__18" },
    { color: "cor__19" },
    { color: "cor__20" },
    { color: "cor__21" },
    { color: "cor__22" },
    { color: "cor__23" },
    { color: "cor__24" },
    { color: "cor__25" }
]);
const icons = reactive([
    { icon: "car-estate" },
    { icon: "umbrella-beach-outline" },
    { icon: "silverware-fork-knife" },
    { icon: "account-school-outline" },
    { icon: "airplane" },
    { icon: "medical-bag" },
    { icon: "dots-horizontal" },
    { icon: "currency-usd" },
    { icon: "finance" },
    { icon: "tshirt-crew-outline" },
    { icon: "heart-pulse" },
    { icon: "home-outline" },
    { icon: "star-outline" },
    { icon: "book-open-variant" },
    { icon: "cash" },
    { icon: "school" },
    { icon: "chart-line-variant" },
    { icon: "gift-outline" },
    { icon: "bag-suitcase-outline" },
    { icon: "bike" },
    { icon: "bus" },
    { icon: "cake-variant-outline" },
    { icon: "calculator" },
    { icon: "video-minus" },
    { icon: "calculator-variant" },
    { icon: "baby-carriage" },
    { icon: "broom" },
    { icon: "bone" },
    { icon: "wallet-bifold-outline" },
    { icon: "cart-outline" },
    { icon: "bank-outline" }
]);


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
    cursor: pointer;
    padding: 10px;
    box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
    border-radius: 20px;
}

.color__despesa {
    color: rgb(255, 82, 82);
}

.color__despesa:hover {
    color: rgb(204, 0, 0);
}

.color__receita {
    color: #00c853;
}

.color__receita:hover {
    color: green;
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
}

/* .formulario {
    border: #0099cc 1px solid;
} */

.modal {
    background: #2c2c2e;
    position: relative;
    color: #fefefe;
    z-index: 999;
    /* top: 20%; */
    /* left: 50%; */
    width: 95%;
    max-width: 450px;
    height: auto;
    /* margin-left: 2.5%; */
    border-radius: 20px;
    padding: 15px;
}

.header__modal {
    display: flex;
    justify-content: space-between;
}

.title {
    text-align: center;
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

.btn__modal {
    border: none;
    border-radius: 20px;
    padding-block: 5px;
    padding-inline: 20px;
    color: rgba(255, 255, 255, 0.3);
    background-color: rgba(255, 255, 255, 0.12);
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