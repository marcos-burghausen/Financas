<template>
  <Cabecalho />
  <div class="clearfix"></div>

  <div class="content-wrapper">
    <div class="pagetitle">
      <nav class="d-flex justify-content-between">
        <ol class="breadcrumb bg-transparent">
          <li class="breadcrumb-item">
            <router-link :to="{ name: 'dashboard' }" class="text-white"
              >Dashboard</router-link
            >
          </li>
          <li
            :class="{ opaco: !exibir }"
            class="breadcrumb-item text-white"
            @click="exibir = !exibir"
          >
            despesas
          </li>
          <li :class="{ opaco: exibir }" class="breadcrumb-item" v-if="exibir">
            cadastro de despesa
          </li>
        </ol>
        <button
          v-if="!exibir"
          class="btn btn-danger text-whit"
          @click="exibir = !exibir"
        >
          nova despesa
        </button>
      </nav>
    </div>

    <!-- inicio formulario -->
    <div class="container-fluid" v-if="exibir">
      <div class="container d-flex justify-content-center">
        <div class="cadastro">
          <form class="form" @submit.prevent="salvarLancamentos">
            <div class="inputSimples">
              <mdicon class="mdicon" name="account" />
              <input
                v-model="releases.valor"
                class="input"
                id="valor"
                name="valor"
                type="text"
                required
              />
              <label class="label" for="valor">Valor</label>
            </div>
            <div class="error">
              <span v-if="errorsForm['errors'].valor" class="span-error">{{
                errorsForm["errors"].valor[0]
              }}</span>
            </div>
            <div class="inputSimples">
              <mdicon class="mdicon" name="account" />
              <input
                v-model="releases.date"
                type="date"
                name="date"
                class="input"
                id="date"
                required
              />
              <label for="date" class="label">Data</label>
            </div>
            <div class="error">
              <span v-if="errorsForm['errors'].date" class="span-error">{{
                errorsForm["errors"].date[0]
              }}</span>
            </div>
            <div class="inputSimples">
              <mdicon class="mdicon" name="account" />
              <input
                v-model="releases.descricao"
                type="text"
                name="descricao"
                class="input"
                id="descricao"
                required
              />
              <label for="descricao" class="label">Descricao</label>
            </div>
            <div class="error">
              <span v-if="errorsForm['errors'].descricao" class="span-error">{{
                errorsForm["errors"].descricao[0]
              }}</span>
            </div>
            <div class="inputSimples">
              <select
                v-model="releases.categoria"
                class="input"
                name="categoria"
                aria-label="Default select example"
                required
              >
                <option selected></option>
                <option value="Carro">Carro</option>
                <option value="casa">casa</option>
                <option value="Mercado">Mercado</option>
              </select>
              <label for="categoria" class="label">Categoria</label>
            </div>
            <div class="error">
              <span v-if="errorsForm['errors'].categoria" class="span-error">{{
                errorsForm["errors"].categoria[0]
              }}</span>
            </div>
            <div class="inputSimples">
              <select
                v-model="releases.carteira"
                class="input"
                name="carteira"
                aria-label="Default select example"
                required
              >
                <option selected></option>
                <option value="Sicredi">Sicredi</option>
                <option value="BB">BB</option>
                <option value="Caixa">Caixa</option>
              </select>
              <label for="carteira" class="label">Carteira</label>
            </div>
            <div class="error">
              <span v-if="errorsForm['errors'].carteira" class="span-error">{{
                errorsForm["errors"].carteira[0]
              }}</span>
            </div>
            <div
              class="form-group m-0 container d-flex justify-content-around col-12 pb-3"
            >
              <button type="submit" class="btn btn-light px-5">
                Cadastrar
              </button>
              <button
                @click="
                  {
                    (exibir = !exibir), zerarInputs;
                  }
                "
                class="btn btn-danger px-5"
              >
                Cancelar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- fim formulario -->

    <div class="container-fluid" v-if="!exibir">
      <div class="row justify-content-between m-0 mb-4">
        <Card
          class="card col-12 col-md-6 col-lg-6 col-xl-3 bg-transparent"
          titulo="Despesas pendentes"
          valor="3000,00"
        />
        <Card
          class="card col-12 col-md-6 col-lg-6 col-xl-3 bg-transparent"
          titulo="Despesas"
          valor="3000,00"
        />
        <Card
          class="card col-12 col-md-6 col-lg-6 col-xl-3 bg-transparent"
          titulo="Despesas pagas"
          valor="3000,00"
        />
      </div>

      <div class="row">
        <div class="col-12 col-lg-12">
          <div
            class="row justify-content-center card-header mx-0 py-1"
            style="background-color: rgba(0, 0, 0, 0.25)"
          >
            <div class="row align-items-center col-5 ms-2">Despesas</div>
            <div class="d-flex text-center col-2">
              <form>
                @csrf
                <button
                  class="btn btn-outline-table text-white p-0 fs-5 bi bi-caret-left"
                ></button>
              </form>
              <p class="m-0 pt-1 border rounded-pill w-75">$mesAnoBR</p>
              <form
                action="{{ route('despesasMes', $proximoMes + 1) }}"
                method="POST"
              >
                @csrf
                <button
                  class="btn btn-outline-table text-white p-0 fs-5 bi bi-caret-right"
                ></button>
              </form>
            </div>
          </div>
          <div class="table-responsive">
            @if (count($despesasMes) > 0)
            <table
              class="table align-items-center table-flush table-borderless"
              style="background-color: rgba(0, 0, 0, 0.25)"
            >
              <thead>
                <tr>
                  <th>Data</th>
                  <th>Descrição</th>
                  <th>Categoria</th>
                  <th>Conta</th>
                  <th>Valor</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($despesasMes as $despesa)
                <tr>
                  <td class="py-0">despesa.data</td>
                  <td class="py-0">despesa.descricao</td>
                  <td class="py-0">despesa.categoria</td>
                  <td class="py-0">despesa.carteira</td>
                  <td class="py-0">R$ despesa.valor</td>
                  <td class="d-flex py-0">
                    <form
                      action="{{ route('pagarDespesa', despesa->id) }}"
                      method="POST"
                    >
                      @csrf
                      <button
                        class="btn btn-outline-table p-0 fs-4 bi bi-check2-circle border-0 me-1 @if($despesa->status === 'PAGA') { text-success disable } @else { text-white } @endif"
                      >
                        status === 'PAGA') { disabled data-bs-toggle="button" }
                        @endif title="pagar">
                        <a href=""></a>
                      </button>
                    </form>
                    <form action="" method="POST">
                      @csrf
                      <button
                        class="btn btn-outline-table p-0 text-white fs-4 bi bi-paperclip me-1"
                        title="anexar arquivo"
                      >
                        <a href=""></a>
                      </button>
                    </form>
                    <form action="" method="POST">
                      @csrf
                      <button
                        class="btn btn-outline-table p-0 text-white fs-4 bi bi-pencil me-1"
                        title="editar"
                      >
                        <a href=""></a>
                      </button>
                    </form>
                    <form
                      action="{{ route('deletarDespesa', $despesa->id) }}"
                      method="POST"
                    >
                      @csrf
                      <button
                        type="submit"
                        class="btn btn-outline-table p-0 text-white fs-4 bi bi-trash3 me-1"
                        title="deletar"
                      ></button>
                    </form>
                    <form action="" method="POST">
                      @csrf
                      <button
                        class="btn btn-outline-table p-0 text-white fs-4 bi bi-three-dots-vertical"
                        title="mais opções"
                      >
                        <a href=""></a>
                      </button>
                    </form>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
            @else
            <h5 class="card-title text-white text-center">
              Você não possui despesas a serem exibidas
            </h5>
            @endif
          </div>
        </div>
      </div>
    </div>
    <div class="overlay toggle-menu"></div>
  </div>
</template>

<script setup>
// import FormLancamentos from "../components/FormLancamentos.vue";
import Cabecalho from "@/components/Cabecalho.vue";
import Card from "../components/Card.vue";
// import { useRouter } from "vue-router";
import { ref, reactive } from "vue";
import http from "@/services/http.js";
// import { useAuth } from "@/stores/auth";

// const router = useRouter();
// const auth = useAuth();
const exibir = ref(false);

const releases = reactive({
  valor: "",
  date: "",
  descricao: "",
  categoria: "",
  carteira: "",
});

const errorsForm = reactive({ errors: {} });

function zerarInputs() {
  releases.valor = "";
  releases.date = "";
  releases.descricao = "";
  releases.categoria = "";
  releases.carteira = "";
}

async function salvarLancamentos() {
  try {
    // console.log(releases);
    const { data } = await http.post("/save-release", releases);
    console.log(data);
  } catch (error) {
    console.log(error);
  }
}
</script>

<style>
.opaco {
  color: #6c757d !important;
}
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
  margin: 20px 0 0 0;
  display: flex;
  align-items: center;
  padding-left: 5px;
  position: relative;
  border-radius: 5px;
}
.error {
  height: 20px;
}
.span-error {
  color: rgb(194, 4, 4);
  position: relative;
  top: 0;
  left: 0;
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
  transform: translateY(-35px);
  opacity: 0.9;
}
.input:internal-autofill-selected {
  background-color: transparent;
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
.card {
  height: 140px;
  box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
  color: #ccc;
  font-size: 30px;
}
.btn {
  height: 40px;
  margin-top: 15px;
}
</style>