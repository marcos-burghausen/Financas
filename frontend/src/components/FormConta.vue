<template>
  <v-dialog
    v-model="dialogOpen"
    max-width="600px"
    persistent
  >
    <v-card>
      <v-card-title class="pa-6 pb-4">
        {{ editingId ? 'Editar Conta' : 'Nova Conta' }}
      </v-card-title>

      <v-card-text class="pa-6 pt-4">
        <v-form ref="formRef">
          <v-row>
            <v-col cols="12">
              <v-text-field
                v-model="form.name"
                label="Nome da Conta"
                hint="Ex: Minha Conta Corrente"
                variant="outlined"
                :rules="[rules.required, rules.minLength3]"
                prepend-inner-icon="mdi-text"
              >
                <template #append-inner>
                  <v-menu
                    v-model="menuColor"
                    :close-on-content-click="false"
                    location="bottom"
                  >
                    <template #activator="{ props }">
                      <div
                        v-bind="props"
                        class="color-input-activator"
                      >
                        <img
                          :src="getBankIconPath(form.icon || '')"
                          :alt="form.icon || 'Bank icon'"
                          class="bank-icon-lg"
                        />
                      </div>
                    </template>

                    <div class="dropdown">
                      <div
                        v-for="icon in iconsBank"
                        :key="icon.value"
                        class="dropdown__item"
                        :class="{ 'is__selected': icon.name === form.icon }"
                        @click.stop="selectIcon(icon.name)"
                      >
                        <div class="dropdown__item__content">
                          <img
                            :src="getBankIconPath(icon.name)"
                            :alt="icon.name"
                            class="bank-icon-sm"
                          />
                          <span>{{ icon.name }}</span>
                        </div>
                      </div>
                    </div>

                  </v-menu>
                </template>
              </v-text-field>
            </v-col>

            <v-col cols="12">
              <v-text-field
                v-model="form.saldo_inicial"
                label="Saldo"
                type="tel"
                variant="outlined"
                :rules="[rules.requiredValor]"
                prepend-inner-icon="mdi-currency-brl"
                @update:model-value="form.saldo_inicial = formatCurrencyInput($event)"
              />
            </v-col>

            <v-col cols="12">
              <v-text-field
                v-model.number="form.limit"
                label="Limite (Opcional)"
                type="tel"
                variant="outlined"
                prepend-inner-icon="mdi-currency-brl"
                @update:model-value="form.limit = formatCurrencyInput($event)"
              />
            </v-col>

            <v-col cols="12">
              <v-select
                v-model="form.tipo_conta"
                :items="tiposContaPossivel"
                label="Tipo de Conta"
                variant="outlined"
                :rules="[rules.required]"
              >
                <template #prepend-inner>
                  <v-icon :icon="form.tipo_conta === 'Carteira' ? 'mdi-wallet-outline' : form.tipo_conta === 'Conta Corrente' ? 'mdi-bank-outline' : form.tipo_conta === 'Poupança' ? 'mdi-piggy-bank' : form.tipo_conta === 'Investimento' ? 'mdi-chart-line' : 'mdi-currency-usd'" />
                </template>
              </v-select>
            </v-col>

            <!-- <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.agency"
                label="Agência"
                placeholder="Ex: 1234"
                variant="outlined"
                prepend-inner-icon="mdi-pound"
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.number"
                label="Número da Conta"
                placeholder="Ex: 123456-7"
                variant="outlined"
                prepend-inner-icon="mdi-hash"
              />
            </v-col> -->

            <v-col
                cols="12"
                md="6"
              >
                <v-text-field
                  :model-value="form.status"
                  label="Status"
                  variant="outlined"
                  hide-details="auto"
                  type="text"
                  readonly
                  class="mb-4"
                  :prepend-inner-icon="
                    form.status === 'ATIVA'
                      ? 'mdi-check-circle-outline'
                      : 'mdi-clock-time-three-outline'
                  "
                  @click="toggleStatus"
                >
                  <template #append-inner>
                    <div :class="form.status === 'ATIVA' ? 'switch__check__efetivada' : 'switch__check'">
                      <div :class="form.status === 'ATIVA' ? 'switch__check__efetivada--inner' : 'switch__check--inner'" />
                    </div>
                  </template>
                </v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <div class="d-flex justify-content-between align-items-center">
                  <span>Incluir na soma do total?</span>
                  <v-switch
                    v-model="form.incluir_em_soma_inicial"
                    color="primary"
                    hide-details
                  />
                </div>
            </v-col>

            

            <v-col cols="12">
              <v-textarea
                v-model="form.description"
                label="Observação"
                placeholder="Adicione observações sobre esta conta..."
                variant="outlined"
                rows="3"
                counter
                maxlength="500"
              />
            </v-col>
          </v-row>
        </v-form>
      </v-card-text>

      <v-card-actions class="pa-6 pt-0">
        <v-spacer />
        <v-btn
          variant="outlined"
          @click="closeDialog"
        >
          Cancelar
        </v-btn>
        <v-btn
          color="primary"
          @click="submitForm"
          :loading="loading"
        >
          {{ editingId ? 'Atualizar' : 'Salvar' }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import contasService from '@/services/contas.service';
import { useToastStore } from '@/store/toast';
import { iconsBank } from "@/utils/iconMapper";
import { ref } from 'vue';

// Importar SVGs corretamente para produção
import SicrediIcon from "@/assets/icons/sicredi.svg";
import NubankIcon from "@/assets/icons/nubank.svg";
import CaixaIcon from "@/assets/icons/caixa.svg";
import BBIcon from "@/assets/icons/bb.svg";
import MasterCardIcon from "@/assets/icons/mastercard.svg";
import VisaIcon from "@/assets/icons/visa.svg";

interface Conta {
  id?: number
  name: string
  icon: string
  saldo_inicial?: string
  incluir_em_soma_inicial?: boolean
  tipo_conta: string
  limit?: string
  conta_pai_id?: number | null
  status: String
  description: string | null
  dia_fechamento: number | null
  dia_vencimento?: number | null
}

const props = defineProps({
  modelValue: Boolean,
  editingData: {
    type: Object as () => Conta | null,
    default: null
  }
})

const emit = defineEmits(['update:modelValue', 'saved'])

const toastStore = useToastStore()
const formRef = ref()
const loading = ref(false)
const editingId = ref<number | null>(null)
const menuColor = ref(false)



const form = ref<Conta>({
  name: '',
  icon: '',
  saldo_inicial: "0,00",
  incluir_em_soma_inicial: true,
  tipo_conta: 'Carteira',
  limit: "0,00",
  status: 'ATIVA',
  description: '',
  conta_pai_id: null,
  dia_fechamento: null,
  dia_vencimento: null,
})

const tiposContaPossivel = ['Carteira', 'Conta Corrente', 'Poupança', 'Investimento', 'Outro']

const rules = {
  required: (v: any) => !!v || 'Campo obrigatório',
  minLength3: (v: string) => (v && v.length >= 3) || 'Mínimo 3 caracteres',
  requiredValor: (v: any) => v !== null || 'Valor obrigatório'
}

const dialogOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

// Watchers
watch(() => props.editingData, (newData) => {
  if (newData) {
    editingId.value = newData.id || null
    form.value = { ...newData }
  } else {
    resetForm()
  }
})

const resetForm = () => {
  editingId.value = null
  form.value = {
    name: '',
    icon: '',
    saldo_inicial: "0,00",
    status: 'ATIVA',
    description: '',
    limit: "0,00",
    tipo_conta: 'Corrente',
    incluir_em_soma_inicial: true
  }
  formRef.value?.resetValidation()
}

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

const selectIcon = (iconName: string) => {
  form.value.icon = iconName;
  menuColor.value = false;
};

const getBankIconPath = (bankName: string): string => {
  const iconMap: Record<string, string> = {
    'Sicredi': SicrediIcon,
    'Nubank': NubankIcon,
    'Caixa Economica': CaixaIcon,
    'Banco do Brasil': BBIcon,
    'MasterCard': MasterCardIcon,
    'Visa': VisaIcon,
  };
  return iconMap[bankName] || BBIcon;
};

const toggleStatus = () => {
  form.value.status = form.value.status === "ATIVA" ? "INATIVA" : "ATIVA";
};

const closeDialog = () => {
  emit('update:modelValue', false)
  resetForm()
}

const submitForm = async () => {
  if (!formRef.value?.validate()) {
    return
  }

  try {
    loading.value = true
    console.log(form.value);

    if (editingId.value) {
      await contasService.update(editingId.value, form.value)
      toastStore.success('Conta atualizada com sucesso!')
    } else {
      await contasService.create(form.value)
      toastStore.success('Conta criada com sucesso!')
    }

    emit('saved')
    closeDialog()
  } catch (error: any) {
    console.error('Erro ao salvar conta:', error)
    toastStore.error(error.message || 'Erro ao salvar conta')
  } finally {
    loading.value = false
  }
}
</script>

<script lang="ts">
import { computed, watch } from 'vue';
export default {
  name: 'FormConta'
}
</script>

<style scoped>
.color-input-activator {
  width: 45px;
  height: 45px;
  border-radius: 50%;
  /* border: 2px solid rgba(0, 0, 0, 0.2); */
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.color-input-activator img {
  width: 40px;
  height: 40px;
  object-fit: contain;
  object-position: center;
}

.color-input-activator:hover {
  transform: scale(1.1);
  border-color: rgba(0, 0, 0, 0.4);
}

/* Tamanhos de ícone com img */
.bank-icon-sm {
  width: 24px;
  height: 24px;
  object-fit: contain;
  object-position: center;
}

.bank-icon-md {
  width: 50px;
  height: 50px;
  object-fit: contain;
  object-position: center;
}

.bank-icon-lg {
  width: 45px;
  height: 45px;
  object-fit: contain;
  object-position: center;
}

.dropdown {
  background-color: #2c2c2c;
  border-radius: 4px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
  max-height: 250px;
  overflow-y: auto;
  z-index: 10;
  width: 200px;
  margin-left: calc(100% - 200px);
}

.dropdown__item {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding: 10px 16px;
  color: white;
  font-size: 0.95rem;
  transition: background-color 0.2s ease;
  border-radius: 4px;
  margin: 2px 4px;
}

.dropdown__item:hover {
  background-color: rgba(0, 0, 0, 0.3);
  transform: scale(1.02);
}

.dropdown__item.is__selected {
  background-color: #0c99ed;
  font-weight: bold;
}

.dropdown__item__content {
  display: flex;
  align-items: center;
  gap: 8px;
}
</style>
