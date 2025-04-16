<template>
  <div class="container__modal">
    <v-form
      v-model="validFormLancamentos"
      class="form__lancamentos pt-16 w-100"
      @submit.prevent="salvarLancamentos"
    >
      <div class="header__items d-flex justify-content-between fixed-top py-10 pe-2">
        <v-btn
          :disabled="loading"
          :loading="loading"
          class="close"
          @click="closeForm"
        >
          <mdicon
            name="close"
            size="30"
          />
        </v-btn>
        <div class="d-flex flex-column">
          <span class="fs-5">{{ isEditMode ? 'Editar' : 'Nova' }} {{ transactionType === 'receitas' ? 'Receita' : 'Despesa' }}</span>
        </div>
        <v-btn
          :disabled="loading || !validFormLancamentos || formReleases.valor === '0,00'"
          :loading="loading"
          style="background-color: #77d08e"
          class="salvar px-5 me-2"
          type="submit"
        >
          Salvar
        </v-btn>
      </div>

      <v-textarea
        v-model="formReleases.descricao"
        variant="underlined"
        type="text"
        hide-details="auto"
        label="Descrição"
        required
        class="mb-8 imput"
        :rules="[rules.requiredDescricao]"
        rows="1"
      >
        <template #prepend-inner>
          <mdicon
            class="icon__modify"
            name="text-long"
          />
        </template>
        <template #message>
          <div
            v-if="errorsForm.descricao"
            class="error-message"
          >
            {{ errorsForm.descricao[0] }}
          </div>
        </template>
      </v-textarea>

      <v-text-field
        v-model="formReleases.valor"
        variant="underlined"
        placeholder="0,00"
        hide-details="auto"
        label="Valor"
        type="tel"
        class="mb-8 imput"
        :rules="[rules.requiredValor, rules.requiredValorMaiorQue0]"
        @input="formatValueSave"
      >
        <template #prepend-inner>
          <mdicon
            class="icon__modify"
            name="currency-usd"
          />
        </template>
        <template #message>
          <div
            v-if="errorsForm.valor"
            class="error-message"
          >
            {{ errorsForm.valor[0] }}
          </div>
        </template>
      </v-text-field>

      <v-text-field
        v-model="formReleases.tipo"
        variant="underlined"
        label="Tipo"
        type="text"
        class="mb-8 imput"
        readonly
        @click="openTipoLancamento = true"
      >
        <template #prepend-inner>
          <mdicon
            class="icon__modify"
            name="refresh"
          />
        </template>
      </v-text-field>

      <div
        v-if="openTipoLancamento"
        class="tipo"
      >
        <div class="modal__tipo">
          <div
            v-for="(item, index) in tiposLancamento"
            :key="index"
            class="cor__icon"
          >
            <div class="container__tipos">
              <div
                class="container__tipo"
                @click="selecionarTipo(item)"
              >
                <mdicon
                  :class="formReleases.tipo === item ? 'selected' : ''"
                  :name="formReleases.tipo === item ? 'radiobox-marked' : 'checkbox-blank-circle-outline'"
                />
                <span class="ms-3">{{ item }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div
        v-if="openParcelas"
        class="parcelas"
      >
        <div class="container__parcelas pb-5">
          <div class="modal__parcelas">
            <v-text-field
              v-model="formReleases.num_parcelas"
              variant="underlined"
              type="number"
              class="mb-8 imput"
              label="Quantidade de Parcelas"
            >
              <template #prepend-inner>
                <mdicon
                  class="me-2"
                  name="refresh"
                />
                <span class="me-2">Quantidade</span>
              </template>
            </v-text-field>
            <v-text-field
              v-model="formReleases.periodicidade"
              variant="underlined"
              type="text"
              class="mb-8 imput"
              label="Periodicidade"
            >
              <template #prepend-inner>
                <mdicon
                  class="me-2"
                  name="refresh"
                />
                <span class="me-2">Periodicidade</span>
              </template>
            </v-text-field>
          </div>
          <div class="botoes__parcelas mx-5">
            <v-btn
              class="px-5 me-5 cancelar"
              @click="openParcelas = false"
            >
              Cancelar
            </v-btn>
            <v-btn
              class="btn__concluido px-5"
              @click="openParcelas = false"
            >
              Concluído
            </v-btn>
          </div>
        </div>
      </div>

      <v-text-field
        v-model="formReleases.date"
        variant="underlined"
        hide-details="auto"
        label="Data de Vencimento"
        type="date"
        :rules="[rules.requiredData]"
        class="mb-8 imput"
      >
        <template #prepend-inner>
          <mdicon
            class="icon__modify"
            name="calendar"
          />
        </template>
        <template #message>
          <div
            v-if="errorsForm.date"
            class="error__message"
          >
            {{ errorsForm.date[0] }}
          </div>
        </template>
      </v-text-field>

      <v-text-field
        v-model="formReleases.status"
        variant="underlined"
        hide-details="auto"
        label="Status"
        type="text"
        class="mb-8 imput cursor__pointer"
        readonly
        @click="toggleStatus"
      >
        <template #prepend-inner>
          <mdicon
            class="icon__modify"
            :name="formReleases.status === 'Efetivada' ? 'check-circle-outline' : 'clock-time-three-outline'"
          />
        </template>
        <template #append-inner>
          <div
            :class="formReleases.status === 'Efetivada' ? 'form__check__efetivada' : 'form__check'"
          >
            <div
              :class="formReleases.status === 'Efetivada' ? 'switch__check__efetivada' : 'switch__check'"
            />
          </div>
        </template>
      </v-text-field>

      <v-autocomplete
        v-model="formReleases.categoria"
        :items="categoriasNames"
        :rules="[rules.requiredCatagoria]"
        label="Categoria"
        placeholder="Selecione..."
        required
        variant="underlined"
        class="mb-8 imput"
      >
        <template #prepend-inner>
          <mdicon
            class="icon__modify"
            name="scatter-plot"
          />
        </template>
        <template #message>
          <div
            v-if="errorsForm.categoria"
            class="error-message"
          >
            {{ errorsForm.categoria[0] }}
          </div>
        </template>
      </v-autocomplete>

      <v-autocomplete
        v-model="formReleases.conta"
        :items="contas"
        :rules="[rules.requiredCarteira]"
        label="Conta"
        placeholder="Selecione..."
        required
        variant="underlined"
        class="mb-5 imput"
      >
        <template #prepend-inner>
          <mdicon
            class="icon__modify"
            name="bank"
          />
        </template>
        <template #message>
          <div
            v-if="errorsForm.conta"
            class="error-message"
          >
            {{ errorsForm.conta[0] }}
          </div>
        </template>
      </v-autocomplete>
    </v-form>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import http from "../services/http";
import type { Lancamentos } from "../types/lancamentos";
import { useWalletsStore } from "../store/wallets";
import { useRevenuesStore } from "../store/revenues";
import { useUserStore } from "../store/user";

const useWallets = useWalletsStore();
const useRevenues = useRevenuesStore();
const useUser = useUserStore();

const emit = defineEmits(["updateData", "closeForm"]);

const props = defineProps<{
  releases?: Lancamentos;
  rota: string;
  mesReferencia: string;
  transactionType: "receitas" | "despesas";
}>();

const loading = ref(false);
const validFormLancamentos = ref(false);
const openTipoLancamento = ref(false);
const openParcelas = ref(false);
const errorsForm = ref<{ [key: string]: string[] }>({});
const tiposLancamento = ref(["Não recorrente", "Parcelada", "Fixa mensal"]);

const categoriasNames = ref(props.transactionType === "receitas" ? useUser.user.categoriasReceitas.map(categoria => categoria.name) : useUser.user.categoriasDespesas.map(categoria => categoria.name)); 

// const contas = ref(useWallets.walletsData.walllets.map(conta => conta.name));
const contas = ref(useWallets.walletsData.wallets.map(conta => conta.name));
console.log(contas.value);

const formReleases = ref<Lancamentos>({
    id: props.releases?.id || null,
    descricao: props.releases?.descricao || "",
    valor: props.releases?.valor || "0,00",
    tipo: props.releases?.tipo || "Não recorrente",
    num_parcelas: props.releases?.num_parcelas || 0,
    periodicidade: props.releases?.periodicidade || null,
    date: props.releases?.date || new Date().toISOString().split("T")[0],
    status: props.releases?.status || "Pendente",
    categoria: props.releases?.categoria || categoriasNames.value[0],
    subCategoria: props.releases?.subCategoria || "",
    conta: props.releases?.conta || contas,
    mesReferencia: props.releases?.mesReferencia || props.mesReferencia,
    dateLancamento: props.releases?.dateLancamento || new Date().toISOString().split("T")[0],
    dateEfetivacao: props.releases?.dateEfetivacao || new Date().toISOString().split("T")[0],
});

const isEditMode = computed(() => !!props.releases?.id);

const toggleStatus = () => {
    formReleases.value.status = formReleases.value.status === "Efetivada" ? "Pendente" : "Efetivada";
};

const closeForm = () => {
    emit("closeForm");
    clearInputs();
};

const selecionarTipo = (item: string) => {
    formReleases.value.tipo = item;
    openTipoLancamento.value = false;
    if (item === "Parcelada") {
        openParcelas.value = true;
    } else {
        formReleases.value.num_parcelas = 0;
        formReleases.value.periodicidade = "";
    }
};

const salvarLancamentos = async () => {
    try {
        loading.value = true;
        const valorStr = formReleases.value.valor.replace(/\./g, "").replace(",", ".");
        const valorNum = parseFloat(valorStr);
        if (isNaN(valorNum)) {
            errorsForm.value.valor = ["O valor deve ser um número válido"];
            return;
        }
        const payload = {
            valor: valorNum,
            date: formReleases.value.date,
            descricao: formReleases.value.descricao,
            categoria: formReleases.value.categoria,
            conta: formReleases.value.conta,
            status: formReleases.value.status,
            mesReferencia: formReleases.value.mesReferencia,
            num_parcelas: formReleases.value.num_parcelas,
            periodicidade: formReleases.value.periodicidade,
            tipo: formReleases.value.tipo,
            tipoTransacao: props.transactionType,
        };
        console.log("Payload being sent:", payload);
        const method = isEditMode.value ? "put" : "post";
        const url = isEditMode.value ? `/revenue/${formReleases.value.id}` : "/revenue";
        const res = await http[method](url, payload);

        useRevenues.setRevenuesData(res.data.revenuesData);
        useWallets.setSaldoInicial(res.data.walletsData.saldoInicial);
        useWallets.setWallets(res.data.walletsData.wallets);
        emit("updateData", res.data.revenuesData);
        closeForm();
    } catch (error) {
        errorsForm.value = error.response?.data?.errors || {};
        console.error("Erro ao salvar lançamento:", error.response?.data);
        console.error("Validation errors:", errorsForm.value);
    } finally {
        loading.value = false;
    }
};

const clearInputs = () => {
    formReleases.value = {
        id: null,
        descricao: "",
        valor: "0,00",
        tipo: "Não recorrente",
        num_parcelas: 0,
        periodicidade: null,
        date: new Date().toISOString().split("T")[0],
        status: "Pendente",
        categoria: "",
        subCategoria: "",
        conta: "",
        mesReferencia: props.mesReferencia,
        dateLancamento: new Date().toISOString().split("T")[0],
        dateEfetivacao: new Date().toISOString().split("T")[0],
    };
    errorsForm.value = {};
};

const formatValueSave = () => {
    let novoValor = formReleases.value.valor.replace(/[^\d]/g, "");
    if (novoValor.length > 1) {
        const parteInteira = novoValor.slice(0, -2).replace(/^0+/, "") || "0";
        const parteDecimal = novoValor.slice(-2);
        const parteInteiraFormatada = parteInteira.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        formReleases.value.valor = `${parteInteiraFormatada},${parteDecimal}`;
    } else if (novoValor.length === 1) {
        formReleases.value.valor = `0,0${novoValor}`;
    } else {
        formReleases.value.valor = "0,00";
    }
};

const rules = {
    requiredValor: (value: string) => !!value || "O campo valor é obrigatório",
    requiredValorMaiorQue0: (value: string) => {
        if (!value) return "O campo valor é obrigatório";
        const numericValue = parseFloat(value.replace(/\./g, "").replace(",", "."));
        return (!isNaN(numericValue) && numericValue > 0) || "O campo valor deve ser maior que zero";
    },
    requiredData: (value: string) => !!value || "O campo data é obrigatório",
    requiredDescricao: (value: string) => !!value || "O campo descrição é obrigatório",
    requiredCatagoria: (value: string) => !!value || "O campo categoria é obrigatório",
    requiredCarteira: (value: string) => !!value || "O campo conta é obrigatório",
};
</script>

<style scoped>
.container__modal {
  /* position: absolute;
  top: 0;
  left: 0; */
  width: 100%;
  height: 100%;
  min-height: 100%;
  /* background: rgba(0, 0, 0, 0.5); */
  background: rgb(15, 15, 15);
  display: flex;
  justify-content: center;
  align-items: center;
  /* overflow: auto; */
  padding: 10px;
}
.form__lancamentos {
  /* width: 100%; */
  padding: 0 10px;
}
.header__items {
  background-color: rgb(15, 15, 15);
  color: #fefefe;
  height: 70px;
}

.salvar {
  border-radius: 20px;
}
.imput {
  height: 40px;
  color: #ccc;
  width: 100%;
  /* border: none; */
}
.tipo {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.8);
  z-index: 999;
  display: flex;
  justify-content: center;
  align-items: center;
}
.modal__tipo {
  background: #2c2c2e;
  /* position: relative; */
  color: #fefefe;
  /* z-index: 999; */
  /* top: 20%; */
  /* left: 50%; */
  width: 50%;
  max-width: 450px;
  height: 200px;
  /* margin-left: 2.5%; */
  border-radius: 20px;
  padding: 15px;
}
.container__tipos {
  width: 100%;
  display: flex;
  /* flex-direction: column; */
  /* align-items: center; */
}

.container__tipo {
  cursor: pointer;
  width: 100%;
  display: flex;
  /* justify-content: center; */
  margin-block: 10px;
}
.error__message {
  color: red;
  font-size: 12px;
  margin-top: 4px;
}
.mdicon {
  cursor: pointer;
  padding: 10px;
  /* box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23; */
  border-radius: 50px;
  position: absolute;
  right: 30px;
  bottom: 30px;
  background-color: #77d08e;
  color: #fefefe;
}

.botoes__parcelas {
  display: flex;
  justify-content: end;
  margin-top: 20px;
}
.container__parcelas {
  background: #2c2c2e;
  color: #fefefe;
  width: 90%;
  /* height: 200px; */
  margin: 15px auto;
  border-radius: 20px;
  /* padding: 15px; */
}
.modal__parcelas {
  /* background: #2c2c2e; */
  color: #fefefe;
  width: 90%;
  /* height: 200px; */
  margin: 15px auto;
  /* border-radius: 20px; */
  /* padding: 15px; */
}
.parcelas {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.8);
  z-index: 999;
  display: flex;
  justify-content: center;
  align-items: end;
}

.selected {
  color: #77d08e;
}





/* .container__tipo {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
} */

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
  height: 40px;
  width: 40px;
  background-color: transparent;
  color: #fefefe;
  display: flex;
  justify-content: center;
  align-items: center;
}
.cancelar {
  border-radius: 20px;
  background-color: transparent;
  color: #77d08e;
}
.btn__concluido {
  border-radius: 20px;
  background-color: #77d08e;
}
.close:hover {
  background-color: #1c1c1e;
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