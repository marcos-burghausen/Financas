<template>
  <div class="container__table">
    <div class="nav__category">
      <nav class="nav">
        <ul class="ul__local">
          <span class="me-3 text-white">
            <router-link class="link" :to="{ name: 'dashboard' }">
              <v-icon icon="mdi-arrow-left" size="20" />
            </router-link>
          </span>
          <li class="opaco">categorias</li>
        </ul>
        <div>
          <button
            class="btn__dropdown dropdown-toggle"
            :class="
              selectedOption === 'categoria por despesas'
                ? 'despesa'
                : 'receita'
            "
            type="button"
            @click="toggleDropdown"
          >
            {{ selectedOption }}
          </button>
          <ul v-if="isDropdownOpen" class="ul__dropdown">
            <li
              v-for="(item, index) in options"
              :key="index"
              @click="selectOption(item.name)"
            >
              {{ item.name }}
            </li>
          </ul>
        </div>
        <Modal
          :color="
            selectedOption === 'categoria por despesas'
              ? 'color__despesa'
              : 'color__receita'
          "
          @update-categorias-despesas="updateCategoriasDespesas"
          @update-categorias-receitas="updateCategoriasReceitas"
        />
      </nav>
    </div>

    <table v-if="selectedOption === 'categoria por despesas'" class="tabl">
      <thead>
        <tr>
          <th style="padding: 10px" class="text-white text-center first__th">
            Nome
          </th>
          <th class="text-white text-center">Cor</th>
          <th class="text-white text-center">Icone</th>
          <th class="text-white text-center last__th">Ações</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="categoria in categoriasDespesas" :key="categoria.id">
          <td class="text-white ps-3">
            {{ categoria.name }}
          </td>
          <td class="d-flex justify-content-center">
            <div class="cor__forma" :class="categoria.color" />
          </td>
          <td class="text-white text-center">
            <v-icon v-if="categoria.icon" :icon="categoria.icon" />
          </td>
          <td class="d-flex py-0 justify-content-center" v-if ="categoria.editable === true">
            <button
              class="btn btn-outline-table p-0 text-white fs-4 bi bi-pencil mx-2"
              title="editar"
            >
              <v-icon icon="mdi-pencil-outline" size="25" />
            </button>
            <button
              v-if="categoria.editable === true"
              style="color: #fefefe"
              class="btn btn-outline-table p-0 fs-4 bi bi-check2-circle border-0 mx-2"
              title="apagar"
              @click="deleteCategory(categoria)"
            >
              <v-icon icon="mdi-trash-can-outline" size="25" />
            </button>
            <!-- <button @click="" style="color: #fefefe;"
                            class="btn btn-outline-table p-0 fs-4 bi bi-check2-circle border-0 mx-2 " title="relatório">
                            <mdicon name="clipboard-text-outline" />
                        </button> -->
          </td>
        </tr>
      </tbody>
    </table>
    <table v-else class="tabl">
      <thead>
        <tr>
          <th style="padding: 10px" class="text-white text-center first__th">
            Nome
          </th>
          <th class="text-white text-center">Cor</th>
          <th class="text-white text-center">Icone</th>
          <th class="text-white text-center last__th">Ações</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="categoria in categoriasReceitas" :key="categoria.id">
          <td class="text-white text-center">
            {{ categoria.name }}
          </td>
          <td class="text-white d-flex align-items-center">
            <div class="cor__forma" :class="categoria.color" />
          </td>
          <td class="text-white text-center">
            <v-icon v-if="categoria.icon" :icon="categoria.icon" />
          </td>
          <td class="d-flex py-0 justify-content-center">
            <!-- <button @click="" style="color: #fefefe;"
                                            class="btn btn-outline-table p-0 fs-4 bi bi-check2-circle border-0 mx-2 "
                                            title="relatório">
                                            <mdicon name="clipboard-text-outline" />
                                        </button> -->
            <button
              class="btn btn-outline-table p-0 text-white fs-4 bi bi-pencil mx-2"
              title="editar"
            >
              <v-icon icon="mdi-pencil-outline" />
            </button>
            <button
              v-if="categoria.editable === true"
              style="color: #fefefe"
              class="btn btn-outline-table p-0 fs-4 bi bi-check2-circle border-0 mx-2"
              title="apagar"
              @click="deleteCategory(categoria)"
            >
              <v-icon icon="mdi-trash-can-outline" />
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script setup lang="ts">
import Modal from "@/components/ModalCategoria.vue";

import http from "@/services/http";
import { useErrorStore, useExpensesStore, useRevenuesStore } from "@/store";
import type { ApiErrorResponse, CategoryData } from "@/types";
import type { AxiosError } from "axios";
import { reactive, ref } from "vue";

const useExpenses = useExpensesStore();
const useRevenues = useRevenuesStore();
const errorStore = useErrorStore();

const categoriasDespesas = ref(useExpenses.expensesData?.categories || []);
const categoriasReceitas = ref(useRevenues.revenuesData?.categories || []);

const isDropdownOpen = ref(false);
const selectedOption = ref("categoria por despesas");

const options = reactive([
  { name: "categoria por despesas" },
  { name: "categoria por receitas" },
]);
const updateCategoriasDespesas = (novoValor: Array<CategoryData>) => {
  categoriasDespesas.value = novoValor;
};
const updateCategoriasReceitas = (novoValor: Array<CategoryData>) => {
  categoriasReceitas.value = novoValor;
};

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value;
};

const selectOption = (option: string) => {
  selectedOption.value = option;
  isDropdownOpen.value = false;
};
let loading = ref(false);

const deleteCategory = async (category: CategoryData) => {
  errorStore.unsetError();
  try {
    loading.value = true;
    const res = await http.post("delete-category", category);
    if (res.data.categoriasDespesas) {
      useExpenses.setExpensesData(res.data.categories);
      // categoriasDespesas.value = res.data.categoriasDespesas;
    }
    if (res.data.categoriasReceitas) {
      // useUser.setCategoriasReceitas(res.data.categoriasReceitas);
      // categoriasReceitas.value = res.data.categoriasReceitas;
    }
  } catch (error) {
    const axiosError = error as AxiosError<ApiErrorResponse>;
    if (axiosError.response?.data.errors) {
      errorStore.setErrorFromForm(axiosError);
    } else {
      errorStore.setErrorFromResponse(axiosError);
    }
  } finally {
    loading.value = false;
  }
};
</script>
<style scoped>
.nav__category {
  width: 100%;
}
.cor__forma {
  width: 25px;
  height: 25px;
  border-radius: 50%;
}

.nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.ul__local {
  --bs-breadcrumb-item-padding-x: 0.5rem;
  display: flex;
  padding: 0;
  margin: 0;
  height: 100%;
  list-style: none;
}

.link {
  text-decoration: none;
  color: #fefefe;
}

.li__separador {
  color: #fefefe;
  margin-inline: 5px;
}

.opaco {
  color: #6c757d !important;
}

.btn__dropdown {
  border: none;
  border-radius: 10px;
  color: #fefefe !important;
  padding: 5px;
  width: 200px;
}

.ul__dropdown {
  position: absolute;
  padding: var(--bs-dropdown-padding-y) var(--bs-dropdown-padding-x);
  list-style: none;
  border-radius: 10px;
  width: 200px;
  background-color: rgb(44, 44, 46);
}

.ul__dropdown li {
  text-align: center;
  color: #fefefe;
  cursor: pointer;
}

.ul__dropdown li:hover {
  background: rgba(0, 0, 0, 0.25);
}

.mdicon {
  color: #77d08e;
  cursor: pointer;
  padding: 10px;
  box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
  border-radius: 20px;
}

.despesa {
  background: rgb(255, 82, 82);
}

.despesa:hover {
  background: rgb(204, 0, 0);
}

.receita {
  background: #00c853;
}

.receita:hover {
  background: green;
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

.container__table {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding-inline: 15px;
}

.tabl {
  border-radius: 20px;
  background-color: rgba(0, 0, 0, 0.25);
  width: 100%;
  max-width: 700px;
}

.tabl thead tr {
  background: rgba(0, 0, 0, 0.25);
  border-bottom: 1px solid rgba(219, 218, 218, 0.35);
}

.first__th {
  border-top-left-radius: 20px;
}

.last__th {
  border-top-right-radius: 20px;
}

.tabl tr:not(:last-child) {
  border-bottom: 1px solid rgba(219, 218, 218, 0.35);
}

/* tr:nth-child(even) {
    background: rgba(0, 0, 0, 0.35);
} */
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
  background: #04af0f;
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
