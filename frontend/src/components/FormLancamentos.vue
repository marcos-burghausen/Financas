<template>
  <!-- <div class="content-wrapper"> -->
  <div class="container-fluid">
    <!-- End Page Title -->
    <div class="container d-flex justify-content-center">
      <div class="cadastro">
        <form class="form">
          <div class="inputSimples">
            <mdicon
              class="mdicon"
              name="account"
            />
            <input
              id="valor"
              v-model="lancamentos.valor"
              class="input"
              name="valor"
              type="text"
            >
            <label
              class="label"
              for="valor"
            >Valor</label>
          </div>
          <div class="inputSimples">
            <mdicon
              class="mdicon"
              name="account"
            />
            <input
              id="data"
              v-model="lancamentos.data"
              type="date"
              name="data"
              class="input"
            >
            <label
              for="data"
              class="label"
            >Data</label>
          </div>
          <div class="inputSimples">
            <mdicon
              class="mdicon"
              name="account"
            />
            <input
              id="descricao"
              v-model="lancamentos.descricao"
              type="text"
              name="descricao"
              class="input"
            >
            <label
              for="descricao"
              class="label"
            >Descricao</label>
          </div>
          <div class="inputSimples">
            <select
              v-model="lancamentos.categoria"
              class="input"
              name="categoria"
              aria-label="Default select example"
            >
              <option selected />
              <option value="Carro">
                Carro
              </option>
              <option value="casa">
                casa
              </option>
              <option value="Mercado">
                Mercado
              </option>
            </select>
            <label
              for="categoria"
              class="label"
            >Categoria</label>
          </div>
          <div class="inputSimples">
            <select
              v-model="lancamentos.carteira"
              class="input"
              name="carteira"
              aria-label="Default select example"
            >
              <option selected />
              <option value="Sicredi">
                Sicredi
              </option>
              <option value="BB">
                BB
              </option>
              <option value="Caixa">
                Caixa
              </option>
            </select>
            <label
              for="carteira"
              class="label"
            >Carteira</label>
          </div>
          <div
            class="form-group m-0 container d-flex justify-content-around col-12 pb-3"
          >
            <button
              type="submit"
              class="btn btn-light px-5"
              @click="$emit(salvar)"
            >
              Cadastrar
            </button>
            <button
              class="btn btn-danger px-5"
              @click="$emit('esconder')"
            >
              Cancelar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- </div> -->
  <!-- End container-fluid-->
</template>

<script setup lang="ts">
import { inject } from "vue";

const lancamentos = inject("lancamentos");

// const lancamentos = reactive({
//   valor: "",
//   data: "",
//   descricao: "",
//   categoria: "",
//   carteira: "",
// });

// async function salvarLancamentos() {
//   try {
//     const { data } = await http.post("/salvar-despesa", lancamentos);
//     console.log(data);
//   } catch (error) {}
// }
</script>


<style scoped>
.cadastro {
  box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
  padding-top: 15px;
  width: 400px;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.inputSimples {
  background-color: #1e1e1e;
  margin: 20px 0;
  display: flex;
  align-items: center;
  padding-left: 5px;
  position: relative;
  border-radius: 5px;
}
.mdicon {
  color: white;
}
.input {
  color: #ccc;
  width: 300px;
  height: 40px;
  background-color: transparent;
  border: 0;
  outline: 0;
}
.input:focus ~ label,
.input:valid ~ label {
  transform: translateY(-26px);
  opacity: 0.9;
}
.label {
  color: #ccc;
  position: absolute;
  left: 30px;
  top: 12px;
  opacity: 0.4;
  cursor: text;
  transition: 0.5s ease-in-out;
}
</style>