<template>
  <mdicon
    type="button"
    title="adcionar nova categoria"
    name="plus"
    class="mdicon"
    @click="openModal = true"
  />

  <div
    v-if="openModal"
    class="container__modal"
  >
    <div
      class="container-fluid"
    >
      <div class="container d-flex justify-content-center">
        <div class="cadastro">
          <!-- <form class="form" @submit.prevent="salvarLancamentos"> -->
          <v-form class="modal">
            <v-text-field
              v-model="releases.valor"
              variant="outlined"
              type="password"
              hide-details="auto"
              label="Valor"
              class="mb-5 input"
            />

            <v-checkbox
              label="paga"
              color="success"
              hide-details
            />
            
            <!-- <div class="form-check form-switch text-white">
              <input
                id="flexSwitchCheckChecked"
                v-model="status"
                class="form-check-input"
                type="checkbox"
                checked
              >
              <label
                class="form-check-label"
                for="flexSwitchCheckChecked"
              >Foi paga</label>
            </div> -->
            <v-text-field
              v-model="releases.valor"
              variant="outlined"
              type="date"
              hide-details="auto"
              label="Data"
              class="mb-5 input"
            />
            <!-- <div class="inputSimples">
              <input
                id="date"
                v-model="releases.date"
                type="text"
                onfocus="this.type='date'"
                onblur="this.type='text'"
                name="date"
                class="input"
                required
              >
              <label
                for="date"
                class="label"
              >Data</label>
            </div> -->
            <v-text-field
              v-model="releases.valor"
              variant="outlined"
              type="text"
              hide-details="auto"
              label="Descricao"
              class="mb-5 input"
            />
            <!-- <div class="inputSimples">
            <input
              id="descricao"
              v-model="releases.descricao"
              type="text"
              name="descricao"
              class="input"
              required
            >
            <label
              for="descricao"
              class="label"
            >Descricao</label>
          </v-form>
        </div> -->
            <!-- <v-select
              v-model="releases.descricao"
              :items="categorias"
              label="Categoria"
              required
            /> -->
            <div class="inputSimples">
              <select
                v-model="releases.categoria"
                class="input"
                name="categoria"
                aria-label="Default select example"
                required
              >
                <!-- <option class="options" selected></option> -->
                <option
                  v-for="(categoria, index) in categorias"
                  :key="index"
                  class="options"
                  :value="categoria.name"
                >
                  {{
                    categoria.name
                  }}
                </option>
              </select>
              <label
                for="categoria"
                class="label"
              >Categoria</label>
            </div>
            <div class="inputSimples">
              <select
                v-model="releases.carteira"
                class="input"
                name="carteira"
                aria-label="Default select example"
                required
              >
                <!-- <option class="options" selected></option> -->
                <option
                  v-for="carteira in carteiras"
                  class="options"
                  :value="carteira"
                >
                  {{ carteira
                  }}
                </option>
              </select>
              <label
                for="carteira"
                class="label"
              >Carteira</label>
            </div>
            <div class="form-group p-0 container d-flex justify-content-between col-12 mt-2 mb-4">
              <button
                class="btn btn-danger px-5"
                @click="{ formStoreExpense = !formStoreExpense }; clearInputs()"
              >
                Cancelar
              </button>
              <button
                class="btn btn-light px-5"
                @click.prevent="salvarLancamentos"
              >
                Salvar
              </button>
            </div>
          </v-form>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { useUserStore } from "@/store/user";
import { reactive, ref } from "vue";
import http from "@/services/http";

const userStore = useUserStore();

let releases = reactive({
    valor: "",
    date: "",
    status: "",
    descricao: "",
    categoria: "",
    carteira: "",
});

let categorias = reactive(userStore.user.categoriasDespesas);
const selectedColor = ref("");
const selectedIcon = ref("");
const openModal = ref(false);
const nameCategory = ref("");
// const props = defineProps({
//     categoria: {
//         type: Array,
//     }
// });

const emit = defineEmits([
    "updateCategoriasDespesas",
    "updateCategoriasReceitas"
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
        console.log(error);
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
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.50);
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

</style>