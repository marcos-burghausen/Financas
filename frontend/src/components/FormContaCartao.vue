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
              v-model="form.saldo_inicial"
              label="Valor Inicial"
              variant="underlined"
              class="imput"
              type="number"
              prefix="R$"
              :rules="[rules.requiredValor, rules.requiredValorMaiorQue0]"
              @input="formatValueSave"
            >
              <template #prepend-inner>
                <v-icon icon="mdi-currency-usd" />
              </template>
            </v-text-field>
            
            <v-text-field
              v-model="form.saldo_inicial"
              label="cheque especial"
              variant="underlined"
              class="imput"
              type="number"
              prefix="R$"
              :rules="[rules.requiredValor]"
              @input="formatValueSave"
            >
              <template #prepend-inner>
                <v-icon icon="mdi-currency-usd" />
              </template>
            </v-text-field>

            <v-select
              v-model="form.tipo_conta"
              :items="['Carteira', 'Conta Corrente', 'Poupança', 'Investimento', 'Outro']"
              label="Tipo de Conta"
              variant="underlined"
              class="imput"
              :rules="[rules.required]"
            >
              <template #prepend-inner>
                <v-icon :icon="form.tipo_conta === 'Carteira' ? 'mdi-wallet-outline' : form.tipo_conta === 'Conta Corrente' ? 'mdi-bank-outline' : form.tipo_conta === 'Poupança' ? 'mdi-piggy-bank' : form.tipo_conta === 'Investimento' ? 'mdi-chart-line' : 'mdi-currency-usd'" />
              </template>
            </v-select>

            <div class="d-flex justify-content-between align-items-center mt-4">
              <span>Incluir na soma do total?</span>
              <v-switch
                v-model="form.incluir_em_soma_inicial"
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
              type="tel"
              prefix="R$"
              :rules="[rules.requiredValor]"
              @input="() => formatValueSave('limite')"
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
              type="tel"
              prefix="R$"
              :rules="[rules.requiredValor]"
              @input="() => formatValueSave('saldo')"
            >
              <template #prepend-inner>
                <v-icon icon="mdi-currency-usd" />
              </template>
            </v-text-field>

            <!-- BANDEIRA (ícone antes do nome no valor e nos itens) -->
            <v-select
              v-model="form.bandeira"
              :items="cards"
              item-title="name"
              item-value="name"
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
              <template #item="{ props, item }">
                <v-list-item
                  v-bind="props"
                  :prepend-icon="item.raw.icon"
                  :title="item.raw.name"
                />
              </template>
            </v-select>

            <!-- CONTA (ícone antes do nome no valor e nos itens) -->
            <v-select
              v-model="form.conta_id"
              :items="walletItems"
              item-title="name"
              item-value="id"
              label="Conta"
              variant="underlined"
              class="imput"
              :rules="[rules.required]"
            >
              <template #selection="{ item }">
                <div class="d-flex align-center text-truncate">
                  <v-icon
                    :icon="getBankIcon(item.raw.name)"
                    size="25"
                    class="mr-2"
                  />
                  <span class="text-truncate">{{ item?.raw?.name ?? 'Nenhuma' }}</span>
                </div>
              </template>
              <template #item="{ props, item }">
                <v-list-item
                  v-bind="props"
                  :prepend-icon="getBankIcon(item.raw.name)"
                  :title="item.raw.name"
                />
              </template>
            </v-select>

            <v-menu
              v-model="menuFechamento"
              :close-on-content-click="false"
              location="bottom"
            >
              <template v-slot:activator="{ props }">
                <v-text-field
                  v-model="form.dia_fechamento"
                  label="Dia do Fechamento"
                  variant="underlined"
                  class="imput"
                  readonly
                  v-bind="props"
                  :rules="[rules.required]"
                >
                  <template #prepend-inner>
                    <v-icon icon="mdi-calendar-remove-outline" />
                  </template>
                </v-text-field>
              </template>

              <v-card max-width="290px">
                <v-card-text>
                  <div class="d-flex flex-wrap justify-center">
                    <v-btn
                      v-for="dia in diasDoMes"
                      :key="dia"
                      class=""
                      :active="form.dia_fechamento === dia"
                      size="small"
                      @click="selecionarDiaFechamento(dia)"
                    >
                      {{ dia }}
                    </v-btn>
                  </div>
                </v-card-text>
              </v-card>
            </v-menu>
            
            <v-menu
              v-model="menuVencimento"
              :close-on-content-click="false"
              location="bottom"
            >
              <template v-slot:activator="{ props }">
                <v-text-field
                  v-model="form.dia_vencimento"
                  label="Dia do Fechamento"
                  variant="underlined"
                  class="imput"
                  readonly
                  v-bind="props"
                  :rules="[rules.required]"
                >
                  <template #prepend-inner>
                    <v-icon icon="mdi-calendar-today-outline" />
                  </template>
                </v-text-field>
              </template>

              <v-card max-width="290px">
                <v-card-text>
                  <div class="d-flex flex-wrap justify-center">
                    <v-btn
                      v-for="dia in diasDoMes"
                      :key="dia"
                      class=""
                      :active="form.dia_vencimento === dia"
                      size="small"
                      @click="selecionarDiaVencimento(dia)"
                    >
                      {{ dia }}
                    </v-btn>
                  </div>
                </v-card-text>
              </v-card>
            </v-menu>
          </div>
        </div>
      </div>
    </v-form>
  </div>
</template>

<script setup lang="ts">
import { useWalletsStore } from "@/store/wallets";
import { computed, PropType, ref } from "vue";

// Assets das bandeiras
import type { Wallet } from "@/types/accounts.types";
import { formatValue } from "@/utils/formatValue";
import { getBankIcon, iconCardMap } from '@/utils/iconMapper';

const diasDoMes = Array.from({ length: 30 }, (_, i) => i + 1);

const menuFechamento = ref(false);

const selecionarDiaFechamento = (dia: number) => {
  form.value.dia_fechamento = dia; // Atualiza o valor no formulário
  menuFechamento.value = false;   // Fecha o menu
};

const menuVencimento = ref(false);
const selecionarDiaVencimento = (dia: number) => {
  form.value.dia_vencimento = dia; // Atualiza o valor no formulário
  menuVencimento.value = false;   // Fecha o menu
};

const menu = ref(false);

// --- PROPS E EMITS ---
const props = defineProps({
  walletType: {
    type: String,
    required: true,
  },
  wallets: {
    type: Array as PropType<Array<Wallet>>,
    required: true,
  }
});

/** Item padrão (nenhuma conta selecionada) */
const DEFAULT_ITEM = { id: -1, name: "Nenhuma", icon: "mdi-bank-off-outline" };

/** Lista exibida no select: item padrão + carteiras reais */
const walletItems = computed(() => [DEFAULT_ITEM, ...props.wallets]);

/** Conta selecionada (objeto) a partir do id do form */
const selectedWallet = computed<Wallet>(() => {
  if (form.value.conta_id == null) return null;
  return walletItems.value.find(w => w.id === form.value.conta_id) ?? null;
});

/** Ícone que aparece no input quando abrir/selecionar */

const selectedIcon = computed(() => {
  const w = walletItems.value.find(w => Number(w.id) === form.value.conta_id);
  return w?.icon ?? "mdi-wallet-outline";
});

const cards =  ref(iconCardMap);

const selectedBrandIcon = computed(() => {
  const brand = cards.value.find(b => b.name === form.value.bandeira);
  return brand?.icon ?? null;
});

// --- STATE MANAGEMENT ---
const useWallets = useWalletsStore();
const loading = ref(false);

// 2. CONTROLES DE VISIBILIDADE DOS MODAIS
const showIconModal = ref(false);
const showColorModal = ref(false);

const form = ref<Partial<Wallet>>({
  name: "",
  icon: props.walletType === "Conta" ? "mdi-bank" : "mdi-bank-off",
  color: "#0c99ed",
  tipo_conta: props.walletType === "Conta" ? "Carteira" : "Cartão de Crédito",
  saldo: formatValue(Number("0,00")) || "0,00",
  incluir_em_soma_inicial: true,
  limite: formatValue(Number("0,00")) || "0,00",
  conta_id: DEFAULT_ITEM.id as number | null,
  bandeira: "MasterCard",
  dia_fechamento: null,
  dia_vencimento: null,
});



type MoneyKeys = "saldo" | "limite";

const formatValueSave = (campo: MoneyKeys) => {

  const raw = String(form.value[campo] ?? "");
  // 1. Pega apenas os dígitos do valor
  let digits = raw.replace(/\D/g, "");

  // 2. Remove zeros à esquerda, tratando o caso de ser tudo zero
  digits = digits.replace(/^0+/, "") || "0";

  // 3. Garante que o valor tenha pelo menos 3 dígitos para a formatação (ex: 50 vira 050)
  while (digits.length < 3) {
    digits = "0" + digits;
  }

  // 4. Separa a parte inteira e a decimal
  const integerPart = digits.slice(0, -2);
  const decimalPart = digits.slice(-2);

  // 5. Formata a parte inteira com pontos como separadores de milhar
  const formattedIntegerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

  // 6. Monta o valor final formatado
  form.value[campo] = `${formattedIntegerPart},${decimalPart}`;
};

// --- VALIDAÇÃO ---
const rules = {
  required: (value: any) => !!value || "Campo obrigatório.",
  positive: (value: number) => value > 0 || "O valor deve ser positivo.",
  dayOfMonth: (value: number) => (value >= 1 && value <= 31) || "Dia inválido.",
  requiredValor: (value: string) => !!value || "O campo valor é obrigatório",
  requiredValorMaiorQue0: (value: string) => {
    if (!value) return "O campo valor é obrigatório";
    const numericValue = parseFloat(value.replace(/\./g, "").replace(",", "."));
    return (
      (!isNaN(numericValue) && numericValue > 0) ||
      "O campo valor deve ser maior que zero"
    );
  }
};

const isFormValid = computed(() => {
  if (props.walletType === "Conta") {
    return !!form.value.name && !!form.value.tipo_conta;
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
const emit = defineEmits(["closeForm", "updateData"]);

const submitForm = async () => {
  if (!isFormValid.value) return;
  loading.value = true;
  try {
    const payload = {
      ...form.value,
      tipo_conta: props.walletType === "Cartão" ? "Cartão de Crédito" : form.value.tipo_conta,
      conta_id: form.value.conta_id === DEFAULT_ITEM.id ? null : form.value.conta_id,
      // saldo: form.value.saldoInicial,
    };
    const response = await useWallets.saveWallet(payload);
    console.log("Resposta do servidor:", response);
    emit("updateData", response.wallets);
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
.v-btn--active {
  background-color: rgb(var(--v-theme-primary));
  color: white;
}

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