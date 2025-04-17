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

      <!--   <div
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
      </div> -->

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
                    <button
                      class="stepper-btn"
                      :disabled="tempNumParcelas <= 2"
                      @click="decrementQuantidade"
                    >
                      <mdicon
                        name="chevron-down"
                        size="18"
                      />
                    </button>
                    <input 
                      v-model="tempNumParcelas" 
                      type="number" 
                      class="stepper-input"
                      min="2"
                    >
                    <button
                      class="stepper-btn"
                      @click="incrementQuantidade"
                    >
                      <mdicon
                        name="chevron-up"
                        size="18"
                      />
                    </button>
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

      <!-- <v-text-field
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
      </v-text-field> -->

      <div class="text-center">
        <v-menu>
          <template #activator="{ props: activatorProps }">
            <v-text-field
              v-model="data1"
              variant="underlined"
              hide-details="auto"
              label="Data de Vencimento"
              :rules="[rules.requiredData]"
              class="mb-8 imput"
              readonly
              v-bind="activatorProps"
              @click="handleDateClick"
            >
              <template #prepend-inner>
                <mdicon
                  class="icon__modify"
                  name="calendar"
                />
              </template>
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
            </v-text-field>
          </template>

          <v-date-picker
            v-model="data"
            color="primary"
            :style="{ backgroundColor: '#f0f4ff' }"
            @update:modelValue="dateMenu = false"
          />
        </v-menu>
      </div>

      <!-- <v-container>
        <v-row justify="space-around">
          <v-date-picker show-adjacent-months />
        </v-row>
      </v-container> -->


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

function onClick () {
    // Perform an action
}

let data = ref();
let data1 = ref(data.value);
console.log(data.value);

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

// const contas = ref(useWallets.walletsData.walllets.map(conta => conta.name));
const contas = ref(useWallets.walletsData.wallets.map(conta => conta.name));
console.log(contas.value);

const isEditMode = computed(() => !!props.releases?.id);

const formReleases = ref<Lancamentos>({
    id: props.releases?.id || null,
    descricao: props.releases?.descricao || "",
    valor: props.releases?.valor || "0,00",
    tipo: props.releases?.tipo || "Não recorrente",
    num_parcelas: props.releases?.num_parcelas || 0,
    periodicidade: props.releases?.periodicidade || null,
    date: props.releases?.date || new Date().toISOString().split("T")[0],
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
/*.container__parcelas {
  background: #2c2c2e;
  color: #fefefe;
  width: 90%;
  margin: 15px auto;
  border-radius: 20px;
}*/
.container__parcelas {
  background: #1e1e1e;
  width: 100%;
  max-width: 500px;
  border-radius: 15px;
  overflow: hidden;
  color: #fefefe;
}
.modal__parcelas {
  /* background: #2c2c2e; */
  /* color: #fefefe; */
  /* width: 90%; */
  /* height: 200px; */
  /* margin: 15px auto; */
  /* border-radius: 20px; */
  padding: 20px;
}
/*.parcelas {
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
}*/
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
  -moz-appearance: textfield; /* Firefox */
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
.v-btn--size-default {
      --v-btn-size: 0.875rem;
    --v-btn-height: 36px;
    font-size: var(--v-btn-size);
    min-width: 64px;
    padding: 0 16px;
}








.botoes__parcelas {
  display: flex;
  justify-content: space-between;
  padding: 20px;
  background-color: #1e1e1e;
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
/* Sobrescreve estilos do Vuetify para que os selects fiquem com aparência correta */
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
h2 {
  font-size: 28px;
  font-weight: 500;
  color: white;
  margin-bottom: 30px;
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