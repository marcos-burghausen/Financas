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
        <div class="container__parcelas">
          <div class="modal__parcelas">
            <h2 class="mb-4 text-center">
              Configurar parcelas
            </h2>
            
            <div class="parcela-item">
              <div class="item-content">
                <div class="item-icon">
                  <mdicon
                    name="arrow-right"
                    size="24"
                  />
                </div>
                <div class="item-label">
                  Parcela inicial
                </div>
                <div class="item-value">
                  <div class="number-stepper">
                    <v-btn
                      class="stepper-btn"
                      :disabled="tempParcelaInicial <= 1"
                      @click="decrementParcelaInicial"
                    >
                      <mdicon
                        name="chevron-down"
                        size="18"
                      />
                    </v-btn>
                    <input 
                      v-model="tempParcelaInicial" 
                      type="number" 
                      class="stepper-input"
                      min="1"
                    >
                    <v-btn
                      class="stepper-btn"
                      @click="incrementParcelaInicial"
                    >
                      <mdicon
                        name="chevron-up"
                        size="18"
                      />
                    </v-btn>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="divider" />
            
            <div class="parcela-item">
              <div class="item-content">
                <div class="item-icon">
                  <mdicon
                    name="plus-circle-outline"
                    size="24"
                  />
                </div>
                <div class="item-label">
                  Quantidade
                </div>
                <div class="item-value">
                  <div class="number-stepper">
                    <v-btn
                      class="stepper-btn"
                      :disabled="tempNumParcelas <= 2"
                      @click="decrementQuantidade"
                    >
                      <mdicon
                        name="chevron-down"
                        size="18"
                      />
                    </v-btn>
                    <input 
                      v-model="tempNumParcelas" 
                      type="number" 
                      class="stepper-input"
                      min="2"
                    >
                    <v-btn
                      class="stepper-btn"
                      @click="incrementQuantidade"
                    >
                      <mdicon
                        name="chevron-up"
                        size="18"
                      />
                    </v-btn>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="divider" />
            
            <div class="parcela-item">
              <div class="item-content">
                <div class="item-icon">
                  <mdicon
                    name="calendar-blank"
                    size="24"
                  />
                </div>
                <div class="item-label">
                  Periodicidade
                </div>
                <div class="item-value">
                  <v-select
                    v-model="tempPeriodicidade"
                    :items="['Mensal', 'Semanal', 'Quinzenal', 'Bimestral']"
                    variant="plain"
                    hide-details
                    class="select-dark"
                  />
                </div>
                <div class="item-arrow">
                  <mdicon
                    name="chevron-down"
                    size="24"
                  />
                </div>
              </div>
            </div>
          </div>
          
          <div class="botoes__parcelas">
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

      <!-- Campo de Data com v-date-input -->
      <v-date-input
        v-model="formReleases.date"
        variant="underlined"
        hide-details="auto"
        label="Data de Vencimento"
        :rules="[rules.requiredData]"
        class="mb-8 imput"
        prepend-inner-icon="mdi-calendar"
        :style="{ backgroundColor: '#f0f4ff' }"
        show-adjacent-months
      >
        <template #append-inner>
          <span
            v-if="isToday"
            class="today-label"
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
        <template #actions>
          <v-btn
            class="btn-cancelar"
            @click="$emit('cancel')"
          >
            Cancelar
          </v-btn>
          <v-btn
            class="btn-concluido"
            @click="$emit('update')"
          >
            OK
          </v-btn>
        </template>
      </v-date-input>

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

// Validação e formatação da data inicial
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

const categoriasNames = ref(
    props.transactionType === "receitas"
        ? useUser.user.categoriasReceitas.map((categoria) => categoria.name)
        : useUser.user.categoriasDespesas.map((categoria) => categoria.name)
);

const contas = ref(useWallets.walletsData.wallets.map((conta) => conta.name));

const isEditMode = computed(() => !!props.releases?.id);

// Verifica se a data selecionada é o dia atual
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
    date: validateDate(props.releases?.date),
    status: props.releases?.status || "Pendente",
    categoria: props.releases?.categoria || "Outros",
    subCategoria: props.releases?.subCategoria || "",
    conta: props.releases?.conta || "",
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
    formReleases.value.tipo = "Não recorrente";
    formReleases.value.num_parcelas = 0;
    formReleases.value.periodicidade = "";
    openParcelas.value = false;
};

const concluirParcelas = () => {
    parcelaInicial.value = tempParcelaInicial.value;
    formReleases.value.num_parcelas = tempNumParcelas.value;
    formReleases.value.periodicidade = tempPeriodicidade.value;
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
        inicializarValoresTemporarios();
        if (formReleases.value.num_parcelas > 0) {
            tempNumParcelas.value = formReleases.value.num_parcelas;
        }
        if (formReleases.value.periodicidade) {
            tempPeriodicidade.value = formReleases.value.periodicidade;
        }
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
  width: 100%;
  height: 100%;
  min-height: 100%;
  background: rgb(15, 15, 15);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 10px;
}
.form__lancamentos {
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
  width: 50%;
  max-width: 450px;
  height: 200px;
  border-radius: 20px;
  padding: 15px;
}
.container__tipos {
  width: 100%;
  display: flex;
}
.container__tipo {
  cursor: pointer;
  width: 100%;
  display: flex;
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
  border-radius: 50px;
  position: absolute;
  right: 30px;
  bottom: 30px;
  background-color: #77d08e;
  color: #fefefe;
}
.botoes__parcelas {
  display: flex;
  justify-content: space-between;
  padding: 20px;
  background-color: #1e1e1e;
}
.container__parcelas {
  background: #1e1e1e;
  width: 100%;
  max-width: 500px;
  border-radius: 15px;
  overflow: hidden;
  color: #fefefe;
}
.modal__parcelas {
  padding: 20px;
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
.selected {
  color: #77d08e;
}
.parcela-item {
  padding: 15px 0;
}
.item-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 5px 10px;
}
.item-icon {
  width: 40px;
  color: #999;
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
.item-arrow {
  width: 24px;
  color: #999;
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
.number-stepper {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  width: 120px;
}
.stepper-input {
  width: 50px;
  background-color: transparent;
  border: none;
  color: white;
  text-align: center;
  font-size: 18px;
  -moz-appearance: textfield;
}
.stepper-input::-webkit-outer-spin-button,
.stepper-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
.stepper-btn {
  background-color: transparent;
  border: none;
  color: #999;
  cursor: pointer;
  padding: 0px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.stepper-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}
.stepper-btn:hover:not(:disabled) {
  color: #77a7ff;
}
.btn-cancelar {
  color: #77a7ff;
  background-color: transparent;
  border-radius: 25px;
  font-size: 16px;
  padding: 0 30px;
  height: 45px;
}
.btn-concluido {
  background-color: #77a7ff;
  color: white;
  border-radius: 25px;
  font-size: 16px;
  padding: 0 30px;
  height: 45px;
}
:deep(.v-select .v-field) {
  border: none;
  background-color: transparent !important;
}
:deep(.v-select .v-field__input) {
  padding: 0;
  color: white;
}
:deep(.v-field__append-inner) {
  padding: 0;
}
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
.close:hover {
  background-color: #1c1c1e;
}
.today-label {
  font-size: 14px;
  color: #1976d2;
  font-weight: 500;
  margin-right: 8px;
}
</style>