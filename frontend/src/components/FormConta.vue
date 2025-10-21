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
                        label="Selecione a cor"
                        mode="hex"
                        color-pip
                      />
                      <v-card-actions>
                        <v-spacer />
                        <v-btn
                          color="primary"
                          variant="text"
                          @click="menuColor = false; form.color = '#163dc0'"
                        >
                          Cancelar
                        </v-btn>
                        <v-btn
                          color="primary"
                          variant="text"
                          @click="menuColor = false"
                        >
                          OK
                        </v-btn>
                      </v-card-actions>
                    </v-card>
                  </v-menu>
                </template>
              </v-text-field>
            </v-col>

            <v-col cols="12" sm="6">
              <v-select
                v-model="form.bank"
                :items="bancosPossivel"
                label="Banco"
                variant="outlined"
                :rules="[rules.required]"
                prepend-inner-icon="mdi-bank"
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-select
                v-model="form.type"
                :items="tiposContaPossivel"
                label="Tipo de Conta"
                variant="outlined"
                :rules="[rules.required]"
                prepend-inner-icon="mdi-folder"
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-text-field
                v-model.number="form.balance"
                label="Saldo Inicial"
                type="number"
                hint="Saldo atual"
                variant="outlined"
                :rules="[rules.requiredValor]"
                prepend-inner-icon="mdi-currency-brl"
              />
            </v-col>

            <v-col cols="12" sm="6">
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
            </v-col>

            <v-col cols="12" sm="6">
              <v-select
                v-model="form.status"
                :items="['ativa', 'inativa']"
                label="Status"
                variant="outlined"
                :rules="[rules.required]"
                prepend-inner-icon="mdi-check-circle"
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-text-field
                v-model.number="form.limit"
                label="Limite (Opcional)"
                type="number"
                variant="outlined"
                prepend-inner-icon="mdi-credit-card"
              />
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
import contasService from '@/services/contas.service'
import { useToastStore } from '@/store/toast'
import { ref } from 'vue'

interface Conta {
  id?: number
  name: string
  bank: string
  type: 'corrente' | 'poupanca' | 'investimento'
  number: string
  agency: string
  balance: number
  status: 'ativa' | 'inativa'
  description: string
  limit?: number
  color?: string
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
  bank: '',
  type: 'corrente',
  number: '',
  agency: '',
  balance: 0,
  status: 'ativa',
  description: '',
  limit: undefined,
  color: '#163dc0'
})

const tiposContaPossivel = ['corrente', 'poupança', 'investimento']
const bancosPossivel = [
  'Banco do Brasil',
  'Caixa Econômica',
  'Itaú',
  'Bradesco',
  'Santander',
  'Nubank',
  'Inter',
  'Outro'
]

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
    bank: '',
    type: 'corrente',
    number: '',
    agency: '',
    balance: 0,
    status: 'ativa',
    description: '',
    limit: undefined,
    color: '#163dc0'
  }
  formRef.value?.resetValidation()
}

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
import { computed, watch } from 'vue'
export default {
  name: 'FormConta'
}
</script>

<style scoped>
.color-input-activator {
  width: 30px;
  height: 30px;
  border-radius: 4px;
  border: 2px solid rgba(0, 0, 0, 0.2);
  cursor: pointer;
  transition: all 0.2s ease;
}

.color-input-activator:hover {
  transform: scale(1.1);
  border-color: rgba(0, 0, 0, 0.4);
}
</style>
