<template>
  <div class="container__modal">
    <v-form
      class="w-100"
      @submit.prevent="submitForm"
    >
      <div>
        <div class="header__items d-flex justify-content-between fixed-top py-10 align-items-center">
          <div class="d-flex align-items-center">
            <v-btn
              :disabled="loading"
              class="close fs-5 ms-2"
              icon="mdi-close"
              @click="closeForm"
            />
            <span class="fs-5 ms-2"> {{ walletType === 'Cartão' ? 'Novo Cartão' : 'Nova Conta' }} </span>
          </div>
          <v-btn
            :disabled="loading || !isFormValid"
            :loading="loading"
            class="btn m-0 me-3 p-0 px-2"
            type="submit"
            rounded="xl"
          >
            Salvar
          </v-btn>
        </div>

        <div class="form-body">
          <div v-if="walletType === 'Conta'">
            <v-text-field
              v-model="form.name"
              label="Nome da Conta"
              variant="underlined"
              class="imput"
              :rules="[rules.required]"
            >
              <template #prepend-inner>
                <v-icon icon="mdi-text" />
              </template>
            </v-text-field>

            <v-text-field
              v-model="form.saldoInicial"
              label="Valor Inicial"
              variant="underlined"
              class="imput"
              type="number"
              prefix="R$"
              :rules="[rules.required]"
            >
              <template #prepend-inner>
                <v-icon icon="mdi-currency-usd" />
              </template>
            </v-text-field>
            
            <v-text-field
              v-model="form.saldoInicial"
              label="cheque especial"
              variant="underlined"
              class="imput"
              type="number"
              prefix="R$"
              :rules="[rules.required]"
            >
              <template #prepend-inner>
                <v-icon icon="mdi-currency-usd" />
              </template>
            </v-text-field>

            <v-select
              v-model="form.tipoConta"
              :items="['Carteira', 'Conta Corrente', 'Poupança', 'Investimento', 'Outro']"
              label="Tipo de Conta"
              variant="underlined"
              class="imput"
              :rules="[rules.required]"
            >
              <template #prepend-inner>
                <v-icon :icon="form.tipoConta === 'Carteira' ? 'mdi-wallet-outline' : form.tipoConta === 'Conta Corrente' ? 'mdi-bank-outline' : form.tipoConta === 'Poupança' ? 'mdi-piggy-bank' : form.tipoConta === 'Investimento' ? 'mdi-chart-line' : 'mdi-currency-usd'" />
              </template>
            </v-select>

            <div class="d-flex justify-content-between align-items-center mt-4">
              <span>Incluir na soma do total?</span>
              <v-switch
                v-model="form.incluirEmSomaInicial"
                color="primary"
                hide-details
              />
            </div>
          </div>

          <div v-if="walletType === 'Cartão'">
            <v-text-field
              v-model="form.name"
              label="Nome do Cartão"
              variant="underlined"
              class="imput"
              :rules="[rules.required]"
            >
              <template #prepend-inner>
                <v-icon icon="mdi-text" />
              </template>
              <template #append-inner>
                <v-menu
                  v-model="menu"
                  :close-on-content-click="false"
                  location="end"
                >
                  <template #activator="{ props }">
                    <div
                      v-bind="props"
                      class="color-input-activator"
                      :style="{ backgroundColor: form.color }"
                    />
                  </template>

                  <v-card>
                    <v-color-picker
                      v-model="form.color"
                      hide-details="auto"
                      label="Colored Pip"
                      :model-value="form.color"
                      mode="hex"
                      color-pip
                    />
                    <v-card-actions>
                      <v-spacer />
                      <v-btn
                        color="primary"
                        variant="text"
                        @click="menu = false, form.color = '#163dc0'"
                      >
                        Cancelar
                      </v-btn>
                      <v-btn
                        color="primary"
                        variant="text"
                        @click="menu = false"
                      >
                        OK
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-menu>
              </template>
            </v-text-field>

            <v-text-field
              v-model="form.limite"
              label="Limite do Cartão"
              variant="underlined"
              class="imput"
              type="number"
              prefix="R$"
              :rules="[rules.required, rules.positive]"
            >
              <template #prepend-inner>
                <v-icon icon="mdi-currency-usd" />
              </template>
            </v-text-field>

            <v-text-field
              v-model="form.saldo"
              label="Fatura Atual"
              variant="underlined"
              class="imput"
              type="number"
              prefix="R$"
              :rules="[rules.required, rules.positive]"
            >
              <template #prepend-inner>
                <v-icon icon="mdi-currency-usd" />
              </template>
            </v-text-field>

            <!-- BANDEIRA (ícone antes do nome no valor e nos itens) -->
            <v-select
              v-model="form.bandeira"
              :items="bandeiras"
              item-title="title"
              item-value="value"
              label="Bandeira"
              variant="underlined"
              class="imput"
              :rules="[rules.required]"
            >
              <template #prepend-inner>
                <v-icon icon="mdi-credit-card-outline" />
              </template>

              <!-- Valor selecionado -->
              <template #selection="{ item }">
                <div class="d-flex align-center text-truncate">
                  <!-- <component
                    :is="isMdiIcon(item.raw.icon) ? 'v-icon' : 'v-img'"
                    v-bind="isMdiIcon(item.raw.icon)
                      ? { icon: item.raw.icon, size: 20, class: 'mr-2' }
                      : { src: item.raw.icon, width: 20, height: 20, class: 'mr-2', cover: true, alt: '' }"
                  /> -->
                  
                  <span class="text-truncate">{{ item.title }}</span>
                </div>
              </template>
              <template #append-inner>
                <component
                  :is="selectedBrandIcon"
                  v-if="selectedBrandIcon"
                  class="brand-icon"
                />
              </template>

              <!-- Itens do menu -->
              <!-- <template #item="{ props, item }">
                <v-list-item v-bind="props">
                  <template #prepend>
                    <component
                      :is="isMdiIcon(item.raw.icon) ? 'v-icon' : 'v-img'"
                      v-bind="isMdiIcon(item.raw.icon)
                        ? { icon: item.raw.icon, size: 20 }
                        : { src: item.raw.icon, width: 20, height: 20, cover: true, alt: '' }"
                    />
                  </template>
                  <v-list-item-title>{{ item.title }}</v-list-item-title>
                </v-list-item>
              </template> -->
            </v-select>

            <!-- CONTA (ícone antes do nome no valor e nos itens) -->
            <v-select
              v-model="form.conta"
              :items="contas"
              label="Conta"
              variant="underlined"
              class="imput"
              :rules="[rules.required]"
            >

              <template #selection="{ item }">
                <div class="d-flex align-center text-truncate">
                  <v-icon
                    :icon="item.raw.icon"
                    size="25"
                    class="mr-2"
                  />
                  <span class="text-truncate">{{ item.title }}</span>
                </div>
              </template>
              <template v-slot:item="{ props, item }">
                <v-list-item
                  v-bind="props"
                  :prepend-icon="item.raw.icon"
                  :title="item.raw.title"
                ></v-list-item>
              </template>

            </v-select>

            <v-text-field
              v-model="form.dia_fechamento"
              label="Dia de Fechamento"
              variant="underlined"
              class="imput"
              type="number"
              :rules="[rules.required, rules.dayOfMonth]"
            >
              <template #prepend-inner>
                <v-icon icon="mdi-calendar-remove-outline" />
              </template>
            </v-text-field>

            <v-text-field
              v-model="form.dia_vencimento"
              label="Dia de Vencimento"
              variant="underlined"
              class="imput"
              type="number"
              :rules="[rules.required, rules.dayOfMonth]"
            >
              <template #prepend-inner>
                <v-icon icon="mdi-calendar-today-outline" />
              </template>
            </v-text-field>
          </div>

          



        </div>
      </div>
    </v-form>
  </div>
</template>

<script setup lang="ts">
import { useWalletsStore } from "@/store/wallets";
import { computed, ref, watch } from "vue";

// Assets das bandeiras
import CaixaIcon from "@/assets/icons/caixa.svg";
import MastercardIcon from "@/assets/icons/mastercard.svg";
import NubankIcon from "@/assets/icons/nubank.svg";
import SicrediIcon from "@/assets/icons/sicredi.svg";
import VisaIcon from "@/assets/icons/visa.svg";
import BbIcon from "@/assets/icons/bb.svg";

import http from "@/services/http";



const menu = ref(false);

// --- PROPS E EMITS ---
const props = defineProps({
  walletType: {
    type: String,
    required: true,
  },
});
const emit = defineEmits(["closeForm", "updateData"]);

// Helper: decide se usa <v-icon> (mdi-*) ou <v-img> (URL/asset)
const isMdiIcon = (val: unknown): val is string =>
  typeof val === "string" && val.startsWith("mdi-");

// Listas
const bandeiras = [
  { title: "Mastercard", value: "Mastercard", icon: MastercardIcon },
  { title: "Visa", value: "Visa", icon: VisaIcon },
  { title: "Elo", value: "Elo", icon: "mdi-credit-card-outline" },
  { title: "American Express", value: "American Express", icon: "mdi-credit-card-outline" },
  { title: "Outra", value: "Outra", icon: "mdi-credit-card-outline" },
];

const contas = [
  { title: "Nenhuma", value: "Nenhuma", icon: props.walletType === "Conta" ? "mdi-bank" : "mdi-bank-off" },
  { title: "Sicredi", value: "sicredi", icon: SicrediIcon },
  { title: "Nubank", value: "nubank", icon: NubankIcon },
  { title: "Caixa", value: "caixa", icon: CaixaIcon },
  { title: "Banco do Brasil", value: "bb", icon: BbIcon },
];

// --- STATE MANAGEMENT ---
const useWallets = useWalletsStore();
const loading = ref(false);

// 2. CONTROLES DE VISIBILIDADE DOS MODAIS
const showIconModal = ref(false);
const showColorModal = ref(false);

const form = ref({
  name: "",
  icon: props.walletType === "Conta" ? "mdi-bank" : "mdi-bank-off",
  color: "#0c99ed",
  tipoConta: props.walletType === "Conta" ? "Carteira" : "Cartão de Crédito",
  saldoInicial: null,
  incluirEmSomaInicial: true,
  limite: null,
  conta: "Nenhuma",
  bandeira: "Mastercard",
  dia_fechamento: null,
  dia_vencimento: null,
});

const selectedBrandIcon = computed(() => {
  const brand = bandeiras.find(b => b.value === form.value.bandeira);
  return brand ? brand.icon : null;
});

// --- VALIDAÇÃO ---
const rules = {
  required: (value: any) => !!value || "Campo obrigatório.",
  positive: (value: number) => value > 0 || "O valor deve ser positivo.",
  dayOfMonth: (value: number) => (value >= 1 && value <= 31) || "Dia inválido.",
};

const isFormValid = computed(() => {
  if (props.walletType === "Conta") {
    return !!form.value.name && !!form.value.tipoConta;
  }
  if (props.walletType === "Cartão") {
    const diaFechamentoValido =
      form.value.dia_fechamento && form.value.dia_fechamento >= 1 && form.value.dia_fechamento <= 31;
    const diaVencimentoValido =
      form.value.dia_vencimento && form.value.dia_vencimento >= 1 && form.value.dia_vencimento <= 31;
    return !!form.value.name && !!form.value.limite && !!form.value.bandeira && diaFechamentoValido && diaVencimentoValido;
  }
  return false;
});

// --- MÉTODOS ---
async function create() {
  errorStore.unsetError();
  try {
    loading.value = true;
    await http.post("/create", user.value);
    emits("nextStep");
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
}





const submitForm = async () => {
  if (!isFormValid.value) return;
  loading.value = true;
  try {
    const payload = {
      ...form.value,
      tipoConta: props.walletType === "Cartão" ? "Cartão de Crédito" : form.value.tipoConta,
      saldo: form.value.saldoInicial,
    };
    const res = await http.post("/wallet", payload);
    emit("updateData");
    closeForm();
  } catch (error) {
    console.error("Erro ao salvar:", error);
  } finally {
    loading.value = false;
  }
};

const closeForm = () => {
  emit("closeForm");
};

// 3. MÉTODOS PARA ATUALIZAR FORMULÁRIO COM DADOS DOS MODAIS
const handleIconSelect = (iconName: string) => {
  form.value.icon = iconName;
  showIconModal.value = false;
};

const handleColorSelect = (colorHex: string) => {
  form.value.color = colorHex;
  showColorModal.value = false;
};
</script>

<style scoped>
.color-input-activator {
  border: 1px solid rgba(255, 255, 255, 0.5);
  border-radius: 50%;
  width: 30px;
  height: 30px;
  cursor: pointer;
  background-color: transparent;
}

.color-input-activator .label {
  font-size: 1rem;
}

.color-dot {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  border: 1px solid white;
}

.brand-icon {
  height: 24px;
  width: auto;
}

.v_color_picker_modal {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  position: fixed;
  top: 0;
  left: 0;
  background-color: rgba(0, 0, 0, 0.11);
  z-index: 1000;
}

.container__modal {
  background-color: #1e1e1e;
  color: white;
  height: 100%;
  width: 100%;
}

.header__items {
  background-color: #1e1e1e;
  width: 100%;
  z-index: 10;
}

.form-body {
  padding: 120px 16px 16px 16px;
}

.close {
  background: transparent;
  color: white;
  box-shadow: none;
}

.btn {
  background-color: #0c99ed;
  color: #1e1e1e;
  text-transform: none;
  font-weight: bold;
}

.imput {
  margin-top: 10px;
}

/* Ícone + label alinhados no valor selecionado */
.text-truncate {
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}

/* Estilos para a seção de ícone e cor */
.icon-color-selector {
  display: flex;
  flex-direction: column;
}

.icon-color-selector .label {
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.7);
  margin-bottom: 4px;
}

.icon-color-selector .selector-box {
  border: 1px solid rgba(255, 255, 255, 0.5);
  border-radius: 8px;
  padding: 8px;
  width: 80px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.color-dot {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  border: 1px solid white;
}
</style>