<template>
  <v-card class="form-conta-cartao">
    <v-card-title class="pa-6 pb-4">
      {{ editingData?.id ? 'Editar Cartão' : 'Novo Cartão' }}
    </v-card-title>

    <v-card-text class="pa-6 pt-4">
      <v-form ref="formRef">
        <v-row>
          <!-- Nome do Cartão -->
          <v-col cols="12">
            <v-text-field
              v-model="form.name"
              label="Nome do Cartão"
              hint="Ex: Meu Visa"
              variant="outlined"
              :rules="[rules.required]"
              prepend-inner-icon="mdi-credit-card"
            />
          </v-col>

          <!-- Bandeira -->
          <v-col cols="12" sm="6">
            <v-select
              v-model="form.icon"
              :items="cards"
              item-title="name"
              item-value="name"
              label="Bandeira"
              variant="outlined"
              :rules="[rules.required]"
              prepend-inner-icon="mdi-credit-card-outline"
            >
              <template #selection="{ item }">
                <div class="d-flex align-center gap-2">
                  <v-icon :icon="getBankIcon(item.title)" size="20" />
                  <span>{{ item.title }}</span>
                </div>
              </template>
              <template #item="{ props: itemProps, item }">
                <v-list-item
                  v-bind="itemProps"
                  :title="item.raw.name"
                >
                  <template #prepend>
                    <v-icon :icon="getBankIcon(item.raw.name)" size="20" />
                  </template>
                </v-list-item>
              </template>
            </v-select>
          </v-col>

          <!-- Tipo -->
          <v-col cols="12" sm="6">
            <v-select
              v-model="form.tipo_conta"
              :items="['Crédito', 'Débito', 'Múltiplo']"
              label="Tipo"
              variant="outlined"
              :rules="[rules.required]"
              prepend-inner-icon="mdi-folder"
            />
          </v-col>

          <!-- Limite -->
          <v-col cols="12" sm="6">
            <v-text-field
              v-model="form.limite"
              label="Limite"
              type="number"
              variant="outlined"
              :rules="[rules.required]"
              prepend-inner-icon="mdi-cash"
              hint="Limite do cartão"
            />
          </v-col>

          <!-- Saldo Utilizado -->
          <v-col cols="12" sm="6">
            <v-text-field
              v-model="form.saldo"
              label="Utilizado"
              type="number"
              variant="outlined"
              prepend-inner-icon="mdi-currency-usd"
              hint="Valor já gasto"
            />
          </v-col>



          <!-- Cor do Cartão -->
          <v-col cols="12">
            <v-text-field
              v-model="form.color"
              label="Cor do Cartão"
              variant="outlined"
              prepend-inner-icon="mdi-palette"
              hint="Selecione uma cor"
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
                        size="small"
                        @click="menuColor = false; form.color = '#163dc0'"
                      >
                        Cancelar
                      </v-btn>
                      <v-btn
                        color="primary"
                        variant="text"
                        size="small"
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


        </v-row>
      </v-form>
    </v-card-text>

    <v-card-actions class="pa-6 pt-0">
      <v-spacer />
      <v-btn
        variant="outlined"
        @click="closeForm"
      >
        Cancelar
      </v-btn>
      <v-btn
        color="primary"
        @click="submitForm"
        :loading="loading"
      >
        {{ editingData?.id ? 'Atualizar' : 'Salvar' }}
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script setup lang="ts">
import { useUserStore } from '@/store/user'
import { useWalletsStore } from '@/store/wallets'
import type { Wallet } from '@/types/accounts.types'
import { getBankIcon, iconCardMap } from '@/utils/iconMapper'
import { PropType, ref, watch } from 'vue'

const props = defineProps({
  walletType: {
    type: String,
    default: 'Cartão'
  },
  wallets: {
    type: Array as PropType<Wallet[]>,
    default: () => []
  },
  editingData: {
    type: Object as PropType<Partial<Wallet> | null>,
    default: null
  }
})

const emit = defineEmits(['closeForm', 'updateData', 'saved', 'close'])

const formRef = ref()
const loading = ref(false)
const menuColor = ref(false)
const cards = ref(iconCardMap)

const walletsStore = useWalletsStore()

const form = ref<Partial<Wallet>>({
  name: '',
  icon: 'MasterCard',
  color: '#163dc0',
  saldo_inicial: '0,00',
  incluir_em_soma_inicial: true,
  tipo_conta: 'Cartão de Crédito',
  limite: '0,00',
  conta_pai_id: null,
})

const rules = {
  required: (v: any) => !!v || 'Campo obrigatório'
}

watch(() => props.editingData, (newData) => {
  if (newData) {
    form.value = { ...newData }
  }
}, { immediate: true })

const closeForm = () => {
  form.value = {
    name: '',
    icon: 'MasterCard',
    color: '#163dc0',
    saldo_inicial: '0,00',
    incluir_em_soma_inicial: true,
    tipo_conta: 'Cartão de Crédito',
    limite: '0,00',
    conta_pai_id: null,
  }
  emit('closeForm')
  emit('close')
}

const submitForm = async () => {
  if (!formRef.value?.validate()) {
    return
  }

  try {
    loading.value = true
    const userId = useUserStore().userData?.id

    if (!userId) {
      console.error('User ID is not available.')
      return
    }

    const payload: any = {
      ...form.value,
      user_id: userId
    }

    const response = await walletsStore.saveWallet(payload)
    walletsStore.setWalletsData(response.wallets)
    emit('updateData', response.wallets)
    emit('saved', form.value)
    closeForm()
  } catch (error) {
    console.error('Erro ao salvar:', error)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.form-conta-cartao {
  width: 100%;
}

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