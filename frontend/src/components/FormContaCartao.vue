<template>
  <!-- <div class="container__modal">
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
            <div class="custom__select__wrapper d-flex">
              <v-col
                cols="10"
                class="p-0"
              >
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
              </v-col>
              <div
                ref="cardSelectRef"
                class="custom__select"
                @click="toggleIconDropdown"
              >
                <div class="selection">
                  <v-icon
                    :icon="getBankIcon(form.icon)"
                    size="25"
                    class=""
                  />
                  <v-icon
                    icon="mdi-menu-down"
                    size="20"
                    class=""
                  />
                </div>
                <div
                  v-if="isIconDropdownOpen"
                  ref="cardDropdownContainerRef"
                  class="dropdown"
                >
                  <div
                    v-for="icon in iconsBank"
                    :key="icon.value"
                    class="dropdown__item"
                    :class="{ 'is__selected': icon.name === form.icon }"
                    :data-card-id="icon.name"
                    @click.stop="selectIcon(icon.name)"
                  >
                    <div class="dropdown__item__content">
                      <v-icon
                        :icon="getBankIcon(icon.name)"
                        size="25"
                        class="me-2"
                      />
                      <span>{{ icon.name }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <v-text-field
              v-model="form.saldo"
              label="Saldo"
              variant="underlined"
              class="imput"
              type="tel"
              prefix="R$"
              :rules="[rules.requiredValor]"
              @update:model-value="form.saldo_inicial = formatCurrencyInput($event)"
            >
              <template #prepend-inner>
                <v-icon icon="mdi-currency-usd" />
              </template>
            </v-text-field>
            
            <v-text-field
              v-model="form.limite"
              label="Cheque Especial"
              variant="underlined"
              class="imput"
              type="tel"
              prefix="R$"
              :rules="[rules.requiredValor]"
              @update:model-value="form.limite = formatCurrencyInput($event)"
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
                      label="Colored Picker"
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
              @input="() => formatCurrencyInput('limite')"
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
              @input="() => formatCurrencyInput('saldo')"
            >
              <template #prepend-inner>
                <v-icon icon="mdi-currency-usd" />
              </template>
            </v-text-field>

            <v-select
              v-model="form.icon"
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
              <template #item="{ props: itemProps, item }">
                <v-list-item
                  v-bind="itemProps"
                  :title="item.raw.name"
                >
                  <template #prepend>
                    <component
                      :is="item.raw.icon"
                      class="brand-icon mr-3"
                    />
                  </template>
                </v-list-item>
              </template>
            </v-select>

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
                    :icon="getBankIcon(item.raw.icon)"
                    size="25"
                    class="mr-2"
                  />
                  <span class="text-truncate">{{ item?.raw?.name ?? 'Nenhuma' }}</span>
                </div>
              </template>
              <template #item="{ props, item }">
                <v-list-item
                  v-bind="props"
                  :prepend-icon="getBankIcon(item.raw.icon)"
                  :title="item.raw.name"
                />
              </template>
            </v-select>

            <v-menu
              v-model="menuFechamento"
              :close-on-content-click="false"
              location="bottom"
            >
              <template #activator="{ props }">
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
              <template #activator="{ props }">
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
  </div> -->
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
            <div class="custom__select__wrapper d-flex">
              <v-col
                cols="10"
                class="p-0"
              >
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
              </v-col>
              <div
                ref="cardSelectRef"
                class="custom__select"
                @click="toggleIconDropdown"
              >
                <div class="selection">
                  <v-icon
                    :icon="getBankIcon(form.icon || '')"
                    size="25"
                  />
                  <v-icon
                    icon="mdi-menu-down"
                    size="20"
                  />
                </div>
                <div
                  v-if="isIconDropdownOpen"
                  ref="cardDropdownContainerRef"
                  class="dropdown"
                >
                  <div
                    v-for="icon in iconsBank"
                    :key="icon.value"
                    class="dropdown__item"
                    :class="{ 'is__selected': icon.name === form.icon }"
                    @click.stop="selectIcon(icon.name)"
                  >
                    <div class="dropdown__item__content">
                      <v-icon
                        :icon="getBankIcon(icon.name)"
                        size="25"
                        class="me-2"
                      />
                      <span>{{ icon.name }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <v-text-field
              v-model="form.saldo_inicial"
              label="Saldo Inicial"
              variant="underlined"
              class="imput"
              type="tel"
              prefix="R$"
              :rules="[rules.requiredValor]"
              @update:model-value="form.saldo_inicial = formatCurrencyInput($event)"
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
              label="Apelido do Cartão"
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
                  <template #activator="{ props: menuProps }">
                    <div
                      v-bind="menuProps"
                      class="color-input-activator"
                      :style="{ backgroundColor: form.color }"
                    />
                  </template>
                  <v-card>
                    <v-color-picker
                      v-model="form.color"
                      hide-details
                      mode="hex"
                    />
                    <v-card-actions>
                      <v-spacer />
                      <v-btn @click="menu = false">OK</v-btn>
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
              @update:model-value="form.limite = formatCurrencyInput($event)"
            >
              <template #prepend-inner>
                <v-icon icon="mdi-currency-usd" />
              </template>
            </v-text-field>

            <v-select
              v-model="form.icon"
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
              <template #append-inner>
                <component
                  :is="selectedBrandIcon"
                  v-if="selectedBrandIcon"
                  class="brand-icon"
                />
              </template>
              <template #item="{ props: itemProps, item }">
                <v-list-item
                  v-bind="itemProps"
                  :title="item.raw.name"
                >
                  <template #prepend>
                    <component
                      :is="item.raw.icon"
                      class="brand-icon mr-3"
                    />
                  </template>
                </v-list-item>
              </template>
            </v-select>

            <v-select
              v-model="form.conta_pai_id"
              :items="walletItems"
              item-title="name"
              item-value="id"
              label="Conta Vinculada"
              variant="underlined"
              class="imput"
              :rules="[rules.required]"
            >
              <template #selection="{ item }">
                <div class="d-flex align-center text-truncate">
                  <v-icon
                    :icon="getBankIcon(item.raw.icon || '')"
                    size="25"
                    class="mr-2"
                  />
                  <span class="text-truncate">{{ item?.raw?.name ?? 'Nenhuma' }}</span>
                </div>
              </template>
              <template #item="{ props: itemProps, item }">
                <v-list-item
                  v-bind="itemProps"
                  :prepend-icon="getBankIcon(item.raw.icon || '')"
                  :title="item.raw.name"
                />
              </template>
            </v-select>

            <v-text-field
              v-model="form.dia_fechamento"
              label="Dia do Fechamento"
              variant="underlined"
              class="imput"
              readonly
              :rules="[rules.required]"
              @click="menuFechamento = true"
            >
              <template #prepend-inner>
                <v-icon icon="mdi-calendar-remove-outline" />
              </template>
            </v-text-field>
            
            <v-text-field
              v-model="form.dia_vencimento"
              label="Dia do Vencimento"
              variant="underlined"
              class="imput"
              readonly
              :rules="[rules.required]"
              @click="menuVencimento = true"
            >
              <template #prepend-inner>
                <v-icon icon="mdi-calendar-today-outline" />
              </template>
            </v-text-field>
          </div>
        </div>
      </div>
    </v-form>

    <v-menu
      v-model="menuFechamento"
      :close-on-content-click="false"
      location="top center"
    >
      <v-card max-width="320px">
        <v-card-text>
          <div class="d-flex flex-wrap justify-center">
            <v-btn
              v-for="dia in diasDoMes"
              :key="dia"
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
      location="top center"
    >
      <v-card max-width="320px">
        <v-card-text>
          <div class="d-flex flex-wrap justify-center">
            <v-btn
              v-for="dia in diasDoMes"
              :key="dia"
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
</template>

<script setup lang="ts">
import { useUserStore } from "@/store/user";
import { useWalletsStore } from "@/store/wallets";
import type { Wallet } from "@/types/accounts.types";
import { getBankIcon, iconCardMap, iconsBank } from "@/utils/iconMapper";
import { onClickOutside } from "@vueuse/core";
import { computed, PropType, ref } from "vue";

// --- CONSTANTES ---
const diasDoMes = Array.from({ length: 31 }, (_, i) => i + 1);
const DEFAULT_ITEM = { id: -1, name: "Nenhuma", icon: "mdi-bank-off-outline" };

// --- PROPS E EMITS ---
const props = defineProps({
  walletType: {
    type: String,
    required: true,
  },
  wallets: {
    type: Array as PropType<Wallet[]>,
    required: true,
  }
});
const emit = defineEmits(["closeForm", "updateData"]);

// --- STATE MANAGEMENT ---
const walletsStore = useWalletsStore();
const loading = ref(false);

// --- REFS DO FORMULÁRIO ---
const form = ref<Partial<Wallet>>({
  name: "",
  icon: props.walletType === "Conta" ? "Outros" : "MasterCard",
  color: "#163dc0",
  saldo_inicial: "0,00",
  incluir_em_soma_inicial: true,
  tipo_conta: props.walletType === "Conta" ? "Carteira" : "Cartão de Crédito",
  limite: "0,00",
  conta_pai_id: null,
  dia_fechamento: null,
  dia_vencimento: null,
});

// --- CONTROLES DE UI ---
const menu = ref(false);
const menuFechamento = ref(false);
const menuVencimento = ref(false);
const isIconDropdownOpen = ref(false);
const cardSelectRef = ref<HTMLElement | null>(null);
const cardDropdownContainerRef = ref<HTMLElement | null>(null);
onClickOutside(cardSelectRef, () => { isIconDropdownOpen.value = false; });

// --- DADOS ---
const cards = ref(iconCardMap);

// --- COMPUTED PROPERTIES ---
const walletItems = computed(() => [DEFAULT_ITEM, ...props.wallets]);

const selectedBrandIcon = computed(() => {
  if (!form.value.icon) return null;
  const brand = cards.value.find(b => b.name === form.value.icon);
  return brand?.icon ?? null;
});

// --- VALIDAÇÃO ---
const rules = {
  required: (value: any) => !!value || "Campo obrigatório.",
  requiredValor: (value: string) => !!value || "O campo valor é obrigatório",
};

const isFormValid = computed(() => {
  if (props.walletType === "Conta") {
    return !!form.value.name && !!form.value.tipo_conta;
  }
  if (props.walletType === "Cartão") {
    return !!form.value.name && !!form.value.limite && !!form.value.icon && !!form.value.dia_fechamento && !!form.value.dia_vencimento;
  }
  return false;
});

// --- MÉTODOS ---
const formatCurrencyInput = (value: string): string => {
  if (!value) return "0,00";
  let digits = value.replace(/\D/g, "");
  digits = digits.replace(/^0+/, "") || "0";
  while (digits.length < 3) digits = "0" + digits;
  const integerPart = digits.slice(0, -2);
  const decimalPart = digits.slice(-2);
  const formattedIntegerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  return `${formattedIntegerPart},${decimalPart}`;
};

const selecionarDiaFechamento = (dia: number) => {
  form.value.dia_fechamento = dia;
  menuFechamento.value = false;
};

const selecionarDiaVencimento = (dia: number) => {
  form.value.dia_vencimento = dia;
  menuVencimento.value = false;
};

const toggleIconDropdown = () => {
  isIconDropdownOpen.value = !isIconDropdownOpen.value;
};

const selectIcon = (iconName: string) => {
  form.value.icon = iconName;
  isIconDropdownOpen.value = false;
};

const submitForm = async () => {
  if (!isFormValid.value) return;
  const userId = useUserStore().userData?.id;

  if (!userId) {
    // Handle the error, e.g., show a message to the user
    console.error("User ID is not available.");
    return;
  }
  loading.value = true;
  try {
    const payload: Wallet = {
    ...form.value,
    user_id: userId, // Ensure user_id is assigned here
    // ... other properties
  };
    // Remove o campo 'bandeira' se ele ainda existir acidentalmente
    delete payload.bandeira; 
    
    const response = await walletsStore.saveWallet(payload);
    walletsStore.setWalletsData(response.wallets);
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
.custom__select__wrapper {
  position: relative;
  margin-top: 16px;
  padding-top: 8px;
}
.custom__select__label {
  position: absolute;
  top: 0;
  left: 4px;
  font-size: 12px;
  color: rgba(255, 255, 255, 0.7);
}
.custom__select {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;
  /* padding: 8px 4px; */
  border-bottom: 1px solid rgba(255, 255, 255, 0.7);
  cursor: pointer;
  width: 100%;
  background-color: transparent;
  margin-bottom: 22px;
}
.selection {
  display: flex;
  justify-content: space-between;
  color: #a5a5a5;
  width: 100%;
}
.dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background-color: #2c2c2c;
  border-radius: 4px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
  max-height: 250px;
  overflow-y: auto;
  z-index: 10;
  width: 200px;
  /* margin: 4px auto 0; */
  margin-left: calc(100% - 200px);
}
.dropdown__item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 16px;
  color: white;
  font-size: 0.9rem;
  transition: background-color 0.2s ease;
  border-radius: 4px;
  margin: 2px 4px;
}
.dropdown__item.is__selected {
  background-color: #0c99ed;
  font-weight: bold;
}
.dropdown__item__content {
  display: flex;
  align-items: center;
}
</style>