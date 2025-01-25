<template>
  <mdicon
    type="button"
    title="adcionar nova receita"
    name="plus"
    class="mdicon"
    @click="openModal = true"
  />

  <div v-if="openModal" class="container__modal">
    <v-form
      v-model="validFormLancamentos"
      class="form__lançamentos"
      @submit.prevent="salvarLancamentos"
    >
      <!-- <div class="header"> -->
      <!-- <buttom
          :disabled="loading"
          :loading="loading"
          class="px-5 close"
          @click="
            {
              openModal = !openModal;
            }
            clearInputs();
          "
        >
          <mdicon name="close" size="25" />
        </buttom> -->
      <div class="header__items">
        <buttom
          :disabled="loading"
          :loading="loading"
          class="px-5 close"
          @click="
            {
              openModal = !openModal;
            }
            clearInputs();
          "
        >
          <mdicon name="close" size="25" />
        </buttom>
        <div class="d-flex flex-column">
          <span class="fs-5"> Nova receitas </span>
        </div>
        <v-btn
          :disabled="
            loading || !validFormLancamentos || releases.valor === '0,00'
          "
          :loading="loading"
          style="background-color: #77d08e"
          class="salvar px-5"
          type="submit"
        >
          Salvar
        </v-btn>
        <!-- <div>
                    <mdicon
                        name="magnify"
                        class="mdicon me-3"
                        size="25"
                    />
                    <mdicon
                        name="clipboard-text"
                        class="mdicon me-2"
                        size="25"
                    />
                    <mdicon
                        name="dots-vertical"
                        class="mdicon"
                        size="25"
                    />
                </div> -->
      </div>
      <!-- </div> -->

      <v-textarea
        v-model="releases.descricao"
        variant="underlined"
        type="text"
        hide-details="auto"
        label="Descricao"
        class="mb-5 imput"
        rows="1"
      >
        <template #prepend-inner>
          <mdicon class="icon__modify" name="text-long" />
        </template>
      </v-textarea>

      <v-text-field
        v-model="releases.valor"
        variant="underlined"
        placeholder="0,00"
        hide-details="auto"
        label="Valor"
        type="tel"
        class="mb-5 imput"
        :rules="[rules.requiredValor, rules.requiredValorMaiorQue0]"
        @input="formatValueSave()"
      >
        <template #prepend-inner>
          <mdicon class="icon__modify" name="currency-usd" />
        </template>
      </v-text-field>

      <ModalTipoLancamento
        v-model="openModal"
        :items="items"
        @updateSelectedIcon="updateSelectedIcon"
      />

      <v-text-field
        v-model="releases.data"
        variant="underlined"
        hide-details="auto"
        label="Data"
        type="date"
        :rules="[rules.requiredData]"
        class="mb-5 imput"
      >
        <template #prepend-inner>
          <mdicon class="icon__modify" name="calendar" />
        </template>
      </v-text-field>

      <v-text-field
        v-model="releases.status"
        variant="underlined"
        hide-details="auto"
        type="text"
        class="mb-5 imput cursor__pointer"
        style="cursor: pointer !important"
        @click="toggleStatus"
      >
        <template #prepend-inner>
          <mdicon
            class="icon__modify"
            :name="
              releases.status == 'Efetivada'
                ? 'check-circle-outline'
                : 'clock-time-three-outline'
            "
          />
        </template>
        <template #append-inner>
          <div
            :class="
              releases.status == 'Efetivada'
                ? 'form__check__efetivada'
                : 'form__check'
            "
          >
            <div
              :class="
                releases.status == 'Efetivada'
                  ? 'switch__check__efetivada'
                  : 'switch__check'
              "
            ></div>
          </div>
        </template>
      </v-text-field>

      <!-- <v-select label="Default">
        <template #prepend-inner>
          <mdicon class="icon__modify" name="currency-usd" /> </template
      ></v-select> -->

      <v-checkbox v-model="agreement" color="deep-purple">
        <template v-slot:label>
          I agree to the&nbsp;
          <a href="#" @click.stop.prevent="dialog = true">Terms of Service</a>
          &nbsp;and&nbsp;
          <a href="#" @click.stop.prevent="dialog = true">Privacy Policy</a>*
        </template>
      </v-checkbox>

      <v-switch
        v-model="people"
        color="primary"
        label="John"
        value="John"
        hide-details
      ></v-switch>

      <v-autocomplete
        ref="country"
        v-model="country"
        :items="countries"
        :rules="[() => !!country || 'This field is required']"
        label="Categoria"
        placeholder="Select..."
        required
        style="color: #ccc"
        variant="underlined"
      ></v-autocomplete>
      <v-autocomplete
        ref="country"
        v-model="country"
        :items="countries"
        :rules="[() => !!country || 'This field is required']"
        label="Subcategoria"
        placeholder="Select..."
        required
        style="color: #ccc"
        variant="underlined"
      ></v-autocomplete>
      <v-autocomplete
        ref="country"
        v-model="country"
        :items="countries"
        :rules="[() => !!country || 'This field is required']"
        label="Conta"
        placeholder="Select..."
        required
        style="color: #ccc"
        variant="underlined"
      ></v-autocomplete>

      <div
        class="form-group p-0 container d-flex justify-content-between col-12 mt-2 mb-4"
      >
        <v-btn
          :disabled="loading"
          :loading="loading"
          style="background-color: #dc3545; color: #fefefe"
          class="px-5"
          @click="
            {
              openModal = !openModal;
            }
            clearInputs();
          "
        >
          <mdicon name="close" size="25" />
        </v-btn>
        <v-btn
          :disabled="
            loading || !validFormLancamentos || releases.valor === '0,00'
          "
          :loading="loading"
          style="background-color: #77d08e"
          class="btn-light px-5"
          type="submit"
        >
          Salvar
        </v-btn>
      </div>
    </v-form>
  </div>
</template>
<script setup lang="ts">
import { useUserStore } from "@/store/user";
import { reactive, ref } from "vue";
import http from "@/services/http";
import type { Lancamentos } from "@/types/lancamentos";
import { useWalletsStore } from "@/store/wallets";
import ModalTipoLancamento from "@/components/ModalTipoLancamento.vue";

const userStore = useUserStore();
const useWallets = useWalletsStore();

let categorias = reactive(userStore.user.categoriasDespesas);
let mesAnoReferencia = ref(useWallets.walletsData?.mes_ano_referencia);
const selectedColor = ref("");
let releases: Ref<Lancamentos> = ref({
  valor: "",
  date: "",
  descricao: "",
  categoria: "",
  carteira: "",
  status: "Efetivada",
  mesReferencia: mesAnoReferencia.value,
});

const items = ref(["Não recorrente", "parcelar ou repetir", "Fixa mensal"]);
const selectedIcon = ref("");
const openModal = ref(false);
let validFormLancamentos = ref(false);
let status = ref(true);
const nameCategory = ref("");
// const props = defineProps({
//     categoria: {
//         type: Array,
//     }
// });
const toggleStatus = () => {
  releases.value.status =
    releases.value.status === "Efetivada" ? "Pendente" : "Efetivada";
};
const emit = defineEmits([
  "updateCategoriasDespesas",
  "updateCategoriasReceitas",
]);

const updateSelectedIcon = (novoValor: string) => {
  selectedIcon.value = novoValor;
};
const updateSelectedColor = (novoValor: string) => {
  selectedColor.value = novoValor;
};

const clearInputs = () => {
  releases.value.valor = "";
  releases.value.date = "";
  releases.value.descricao = "";
  releases.value.categoria = "";
  releases.value.carteira = "";
};

const saveCategory = async () => {
  const data = ref({
    name: nameCategory.value,
    color: selectedColor.value,
    icon: selectedIcon.value,
    typeCategory: "",
    edit: true,
  });
  try {
    data.value.typeCategory =
      props.color === "color__despesa" ? "despesa" : "receita";

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

const formatValueSave = () => {
  let novoValor = releases.value.valor.replace(/[^\d]/g, "");

  if (novoValor.length > 1) {
    const parteInteira = novoValor.slice(0, -2).replace(/^0+/, "") || "0";
    const parteDecimal = novoValor.slice(-2);
    const parteInteiraFormatada = parteInteira.replace(
      /\B(?=(\d{3})+(?!\d))/g,
      "."
    );
    releases.value.valor = `${parteInteiraFormatada},${parteDecimal}`;
  } else if (novoValor.length === 1) {
    releases.value.valor = `$ 0,0${novoValor}`;
  } else {
    releases.value.valor = "$ 0,00";
  }
};

const rules = {
  requiredValor: (value: string) => !!value || "O campo valor é obrigatório",
  requiredValorMaiorQue0: (value: string) =>
    parseFloat(value.replace(",", ".")) > 0 ||
    "O campo valor deve ser maior que zero",
  requiredData: (value: string) => !!value || "O campo data é obrigatório",
  requiredDescricao: (value: string) =>
    !!value || "O campo escriçãp é obrigatório",
  requiredCatagoria: (value: string) =>
    !!value || "O campo categoria é obrigatório",
  requiredCarteira: (value: string) =>
    !!value || "O campo categoria é obrigatório",
};
</script>

<style scoped>
.form__check {
  width: 40px;
  height: 20px;
  border-radius: 15px;
  background-color: rgba(255, 255, 255, 0.3);
}
.form__check__efetivada {
  width: 40px;
  height: 20px;
  border-radius: 15px;
  background-color: rgba(119, 208, 142, 0.4);
  display: flex;
  justify-content: flex-end;
}

.switch__check {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background-color: #fefefe;
}
.switch__check__efetivada {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background-color: #77d08e;
}
.mdicon {
  cursor: pointer;
  padding: 10px;
  /* box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23; */
  border-radius: 20px;
  position: absolute;
  right: 15px;
  bottom: 15px;
  background-color: #77d08e;
  color: #fefefe;
}
.container__modal {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  /* background: rgba(0, 0, 0, 0.5); */
  background: rgb(15, 15, 15);
  display: flex;
  justify-content: center;
  align-items: center;
}
.form__lançamentos {
  width: 100%;
  padding: 0 10px;
}
.v-field__prefix.custom-prefix {
  color: red !important;
}
.close {
  cursor: pointer;
  border-radius: 50%;
  height: 35px;
  width: 35px;
  color: #fefefe;
  display: flex;
  justify-content: center;
  align-items: center;
}
.salvar {
  border-radius: 20px;
  background-color: #77d08e;
}
.close:hover {
  background-color: #1c1c1e;
}
.header__items {
  color: #fefefe;
  margin-bottom: 20px;
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

.modal {
  background: #2c2c2e;
  position: fixed;
  color: #fefefe;
  z-index: 999;
  top: 20%;
  left: 50%;
  width: 450px;
  height: auto;
  margin-left: -200px;
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
  background-color: transparent;
  border: solid 1px rgba(255, 255, 255, 0.4);
  height: 55px;
  margin: 20px 0 0 0;
  display: flex;
  align-items: center;
  padding-left: 5px;
  position: relative;
  border-radius: 3px;
}

.imput {
  /* background-color: #1e1e1e !important; */
  height: 40px;
  color: #ccc;
  width: 100%;
  border: none;
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

.input:focus ~ label,
.input:valid ~ label {
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
</style>
