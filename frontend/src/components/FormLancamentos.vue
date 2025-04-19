<template>
  <div class="container__modal">
    <v-form
      v-model="validFormLancamentos"
      class="px-3 pt-16 w-100"
      @submit.prevent="salvarLancamentos"
    >
      <div class="header__items d-flex justify-content-between fixed-top py-10 pe-2">
        <v-btn
          :disabled="loading"
          :loading="loading"
          class="close fs-5 ms-3"
          prepend-icon="mdi-close"
          @click="closeForm"
        />
        <div class="d-flex flex-column">
          <span class="fs-5">{{ isEditMode ? 'Editar' : 'Nova' }} {{ transactionType === 'receitas' ? 'Receita' : 'Despesa' }}</span>
        </div>
        <v-btn
          :disabled="loading || !validFormLancamentos || formReleases.valor === '0,00'"
          :loading="loading"
          style="background-color: #77d08e"
          class=" px-3 me-2"
          type="submit"
          rounded="xl"
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
        prepend-inner-icon="mdi-text-long"
      >
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
        prepend-inner-icon="mdi-currency-usd"
        @input="formatValueSave"
      >
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
        prepend-inner-icon="mdi-refresh"
        @click="openTipoLancamento = true"
      />

      <div
        v-if="openTipoLancamento"
        class="tipo"
      >
        <div
          class="d-flex flex-column align-start justify-space-around modal__tipo"
        >
          <v-btn
            v-for="(item, index) in tiposLancamento"
            :key="index"
            :disabled="loading"
            :loading="loading"
            :class="formReleases.tipo === item ? 'selected' : ''"
            flat
            :prepend-icon="formReleases.tipo === item ? 'mdi-radiobox-marked' : 'mdi-checkbox-blank-circle-outline'"
            @click="selecionarTipo(item)"
          >
            <span>{{ item }}</span>
          </v-btn>
        </div>
      </div>

      <div
        v-if="openParcelas"
        class="parcelas"
      >
        <div class="container__parcelas">
          <div class="p-3">
            <h2 class="mb-4 text-center">
              Configurar parcelas
            </h2>
            
            <div class="py-2">
              <div class="d-flex align-center justify-space-between">
                <v-icon
                  class="pe-3"
                  icon="mdi-arrow-right"
                  size="24"
                />
                <span class="item-label">
                  Parcela inicial
                </span>
                <div class="item-value">
                  <div class="number-stepper">
                    <v-btn
                      :disabled="tempParcelaInicial <= 1"
                      prepend-icon="mdi-chevron-down"
                      flat
                      variant="text"
                      class="stepper-btn"
                      @click="decrementParcelaInicial"
                    />
                    <input 
                      v-model="tempParcelaInicial" 
                      type="number" 
                      class="stepper-input"
                      min="1"
                    >
                    <v-btn
                      prepend-icon="mdi-chevron-up"
                      flat
                      class="stepper-btn"
                      @click="incrementParcelaInicial"
                    />
                  </div>
                </div>
              </div>
            </div>
            
            <div class="divider" />
            
            <div class="">
              <div class="d-flex align-center justify-space-between">
                <v-icon
                  icon="mdi-plus-circle-outline"
                  name="plus-circle-outline"
                  size="24"
                  class="pe-3"
                />
                <div class="item-label">
                  Quantidade
                </div>
                <div class="item-value">
                  <div class="number-stepper">
                    <v-btn
                      class="stepper-btn"
                      :disabled="tempNumParcelas <= 2"
                      prepend-icon="mdi-chevron-down"
                      variant="text"
                      @click="decrementQuantidade"
                    />
                    <input 
                      v-model="tempNumParcelas" 
                      type="number" 
                      class="stepper-input"
                      min="2"
                    >
                    <v-btn
                      class="stepper-btn"
                      prepend-icon="mdi-chevron-up"
                      variant="text"
                      @click="incrementQuantidade"
                    />
                  </div>
                </div>
              </div>
            </div>
            
            <div class="divider" />
            
            <div class="">
              <div class="d-flex align-center justify-space-between">
                <v-icon
                  icon="mdi-calendar-blank"
                  size="24"
                  class="pe-3"
                />
                <div class="item-label">
                  Periodicidade
                </div>
                <div class="item-value pb-2">
                  <v-select
                    v-model="tempPeriodicidade"
                    :items="['Mensal', 'Semanal', 'Quinzenal', 'Bimestral']"
                    variant="plain"
                    hide-details
                    class="select-dark"
                  />
                </div>
              </div>
            </div>
          </div>
          
          <div class="d-flex justify-space-between align-center  p-3">
            <v-btn
              class="btn-cancelar"
              @click="cancelarConfiguracaoRepeticao"
            >
              Cancelar
            </v-btn>
            <v-btn
              class="btn-concluido"
              @click="concluirParcelas"
            >
              Concluído
            </v-btn>
          </div>
        </div>
      </div>

      <v-date-input
        v-model="formReleases.date"
        variant="underlined"
        hide-details="auto"
        label="Data de Vencimento"
        :rules="[rules.requiredData]"
        class="mb-8 imput"
        show-adjacent-months
        color="#77d08e"
        prepend-icon=""
        prepend-inner-icon="$calendar"
      >
        <template #append-inner>
          <span
            v-if="isToday"
            class="today__label"
          >Hoje</span>
        </template>
        <template #message>
          <div
            v-if="errorsForm.date"
            class="error__message"
          >
            {{ errorsForm.date[0] }}
          </div>
        </template>
      </v-date-input>

      <v-text-field
        v-model="formReleases.status"
        variant="underlined"
        hide-details="auto"
        label="Status"
        type="text"
        class="mb-8 imput"
        readonly
        :prepend-inner-icon="formReleases.status === 'Efetivada' ? 'mdi-check-circle-outline' : 'mdi-clock-time-three-outline'"
        @click="toggleStatus"
      >
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
        prepend-inner-icon="mdi-scatter-plot"
      >
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
        prepend-inner-icon="mdi-bank"
      >
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

const validateDate = (date: string | undefined): string => {
    if (!date) return new Date().toISOString().split("T")[0];
    const parsedDate = new Date(date);
    if (isNaN(parsedDate.getTime())) {
        console.warn("Data inválida recebida, usando data atual:", date);
        return new Date().toISOString().split("T")[0];
    }
    return parsedDate.toISOString().split("T")[0];
};

const parcelaInicial = ref(1);
const tempParcelaInicial = ref(1);
const tempNumParcelas = ref(2);
const tempPeriodicidade = ref("Mensal");
const loading = ref(false);
const validFormLancamentos = ref(false);
const openTipoLancamento = ref(false);
const openParcelas = ref(false);
const errorsForm = ref<{ [key: string]: string[] }>({});
const tiposLancamento = ref(["Não recorrente", "Parcelada", "Fixa mensal"]);

const categoriasNames = ref(props.transactionType === "receitas" ? useUser.user.categoriasReceitas.map(categoria => categoria.name) : useUser.user.categoriasDespesas.map(categoria => categoria.name)); 

const contas = ref(useWallets.walletsData.wallets.map(conta => conta.name));

const isEditMode = computed(() => !!props.releases?.id);

const isToday = computed(() => {
    const today = new Date().toISOString().split("T")[0];
    return formReleases.value.date === today;
});



const formReleases = ref<Lancamentos>({
    id: props.releases?.id || null,
    descricao: props.releases?.descricao || "",
    valor: props.releases?.valor || "0,00",
    tipo: props.releases?.tipo || "Não recorrente",
    num_parcelas: props.releases?.num_parcelas || 0,
    periodicidade: props.releases?.periodicidade || null,
    // date: props.releases?.date || new Date().toISOString().split("T")[0],
    date: validateDate(props.releases?.date),
    status: props.releases?.status || "Pendente",
    categoria: props.releases?.categoria || "Outros",
    subCategoria: props.releases?.subCategoria || "",
    conta: props.releases?.conta || contas,
    mesReferencia: props.releases?.mesReferencia || props.mesReferencia,
    dateLancamento: props.releases?.dateLancamento || new Date().toISOString().split("T")[0],
    dateEfetivacao: props.releases?.dateEfetivacao || new Date().toISOString().split("T")[0],
});

const incrementParcelaInicial = () => {
    tempParcelaInicial.value++;
};

const decrementParcelaInicial = () => {
    if (tempParcelaInicial.value > 1) {
        tempParcelaInicial.value--;
    }
};

// Funções para incrementar e decrementar quantidade de parcelas
const incrementQuantidade = () => {
    tempNumParcelas.value++;
};

const decrementQuantidade = () => {
    if (tempNumParcelas.value > 2) {
        tempNumParcelas.value--;
    }
};

const inicializarValoresTemporarios = () => {
    tempParcelaInicial.value = 1;
    tempNumParcelas.value = 2;
    tempPeriodicidade.value = "Mensal";
};

const cancelarConfiguracaoRepeticao = () => {
    // Retorna tipo para "Não recorrente"
    formReleases.value.tipo = "Não recorrente";
    formReleases.value.num_parcelas = 0;
    formReleases.value.periodicidade = "";
  
    // Fecha o modal
    openParcelas.value = false;
};

const concluirParcelas = () => {
    // Salva os valores temporários nos valores finais
    parcelaInicial.value = tempParcelaInicial.value;
    formReleases.value.num_parcelas = tempNumParcelas.value;
    formReleases.value.periodicidade = tempPeriodicidade.value;
  
    // Fecha o modal
    openParcelas.value = false;
};

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
    // Inicializa valores para parcelamento
        inicializarValoresTemporarios();
    
        // Se já existirem valores salvos, usa-os como valores temporários
        if (formReleases.value.num_parcelas > 0) {
            tempNumParcelas.value = formReleases.value.num_parcelas;
        }
    
        if (formReleases.value.periodicidade) {
            tempPeriodicidade.value = formReleases.value.periodicidade;
        }
    
        // Abre o modal
        openParcelas.value = true;
    } else {
    // Para outros tipos, limpa os valores de parcelamento
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
.v-btn {
  background-color: transparent;
  cursor: pointer;
}

.container__modal {
  width: 100%;
  height: 100%;
  min-height: 100%;
  background: rgb(15, 15, 15);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 10px;
}
.header__items {
  background-color: rgb(15, 15, 15);
  color: #fefefe;
  height: 70px;
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
.imput {
  height: 40px;
  color: #ccc;
  width: 100%;
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
  color: #fefefe;
  height: 200px;
  border-radius: 20px;
  padding: 15px;
}
.selected {
  color: #77d08e;
}
.parcelas {
  position: fixed;
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
.container__parcelas {
  background: #1e1e1e;
  width: 100%;
  max-width: 500px;
  border-radius: 15px;
  overflow: hidden;
  color: #fefefe;
}
.item-label {
  flex-grow: 1;
  font-size: 18px;
  font-weight: 400;
}
.item-value {
  margin-right: 20px;
  font-size: 18px;
  font-weight: 500;
}
.number-stepper {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  width: 120px;
}
.stepper-btn {
  background-color: transparent;
  border: none;
  width: 30px;
  color: #999;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stepper-btn:hover:not(:disabled) {
  color: #77d08e;
}
.stepper-input {
  width: 50px;
  background-color: transparent;
  border: none;
  color: white;
  text-align: center;
  font-size: 18px;
  -moz-appearance: textfield; /* Firefox */
}
.stepper-input::-webkit-outer-spin-button,
.stepper-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
.divider {
  height: 1px;
  background-color: #333;
  margin: 5px 0;
}
.select-dark {
  color: white;
  width: 120px;
  text-align: right;
}
.btn-cancelar {
  color: #77d08e;
  background-color: transparent;
  border-radius: 25px;
  font-size: 16px;
  padding: 0 30px;
  height: 45px;
}
.btn-concluido {
  background-color: #77d08e;
  color: white;
  border-radius: 25px;
  font-size: 16px;
  padding: 0 30px;
  height: 45px;
}
.today__label {
  font-size: 16px;
  color: #77d08e;
  font-weight: 500;
  margin-right: 8px;
}
.form__check__efetivada {
  width: 40px;
  height: 20px;
  border-radius: 15px;
  background-color: rgba(119, 208, 142, 0.4);
  display: flex;
  justify-content: flex-end;
}
.form__check {
  width: 40px;
  height: 20px;
  border-radius: 15px;
  background-color: rgba(255, 255, 255, 0.3);
}
.switch__check__efetivada {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background-color: #77d08e;
}
.switch__check {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background-color: #fefefe;
}
.error__message {
  color: red;
  font-size: 12px;
  margin-top: 4px;
}
h2 {
  font-size: 28px;
  font-weight: 500;
  color: white;
  margin-bottom: 30px;
}
</style>