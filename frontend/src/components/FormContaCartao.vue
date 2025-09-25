<template>
<div class="container__modal">
    <v-form class="w-100" @submit.prevent="submitForm">
      <div>
      <div class="header__items d-flex justify-content-between fixed-top py-10 align-items-center">
        <div class="d-flex align-items-center">
          <v-btn :disabled="loading" class="close fs-5 ms-2" icon="mdi-close" @click="closeForm" />
          <span class="fs-5 ms-2"> {{ walletType === 'Cartão' ? 'Novo Cartão' : 'Nova Conta' }} </span>
        </div>
        <v-btn :disabled="loading || !isFormValid" :loading="loading" class="btn m-0 me-3 p-0 px-2" type="submit"
          rounded="xl">
          Salvar
        </v-btn>
      </div>

      <div class="form-body">
        <div v-if="walletType === 'Conta'">
          <v-text-field
            v-model="form.saldoInicial"
            label="Valor Inicial (Opcional)" variant="underlined"
            class="imput" type="number" prefix="R$"></v-text-field>
          <v-text-field v-model="form.name" label="Nome da Conta" variant="underlined" class="imput"
            :rules="[rules.required]"></v-text-field>
          <v-select v-model="form.tipo" :items="['Conta Corrente', 'Poupança', 'Investimento', 'Outro']"
            label="Tipo de Conta" variant="underlined" class="imput" :rules="[rules.required]"></v-select>
          <div class="d-flex justify-content-between align-items-center mt-4">
            <span>Incluir na soma do total?</span>
            <v-switch v-model="form.incluirEmSomaInicial" color="success" inset hide-details></v-switch>
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
              <v-icon
                icon="mdi-text"
              />
            </template>
            <template
              #append-inner
            >
                    <v-menu
                      v-model="menu"
                      :close-on-content-click="false"
                      location="end"
                    >
                      <template v-slot:activator="{ props }">
                        <div 
                          v-bind="props" 
                          class="color-input-activator"
                          :style="{ backgroundColor: form.color }"
                        >
                        </div>
                      </template>

                      <v-card>
                        <v-color-picker
                          v-model="form.color"
                            hide-details="auto"
                            label="Colored Pip"
                            :model-value="form.color"
                            mode="hex"
                            color-pip
                          ></v-color-picker>
                        <v-card-actions>
                          <v-spacer></v-spacer>
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
              <v-icon
                icon="mdi-currency-usd"
              />
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
              <v-icon
                icon="mdi-currency-usd"
              />
            </template>
          </v-text-field>

          <v-select
            v-model="form.bandeira"
            :items="bandeiras"
            label="Bandeira"
            variant="underlined"
            class="imput"
            :rules="[rules.required]"
          >
            <template #prepend-inner>
              <v-icon
                icon="mdi-credit-card-outline"
              />
            </template>
            <template v-slot:chip="{ props, item }">
                <v-chip
                  v-bind="props"
                  :prepend-icon="item.raw.avatar"
                ></v-chip>
              </template>
            <template #append-inner>
              <component
                v-if="selectedBrandIcon"
                :is="selectedBrandIcon"
                class="brand-icon"
              />
            </template>
          </v-select>
           <v-autocomplete
              v-model="friends"
              :disabled="isUpdating"
              :items="people"
              item-title="name"
              item-value="name"
              label="Select"
              chips
              closable-chips
            >
              <template v-slot:chip="{ props, item }">
                <v-chip
                  v-bind="props"
                  :prepend-icon="item.raw.avatar"
                ></v-chip>
              </template>

              <template v-slot:item="{ props, item }">
                <v-list-item
                  v-bind="props"
                  :prepend-icon="item.raw.avatar"
                  :title="item.raw.name"
                ></v-list-item>
              </template>
            </v-autocomplete>

          <v-select
            v-model="form.conta"
            :items="bandeiras"
            label="Conta"
            variant="underlined"
            class="imput"
            :rules="[rules.required]"
          >
            <template #prepend-inner>
              <v-icon
                icon="mdi-credit-card-outline"
              />
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
              <v-icon
                icon="mdi-calendar-remove-outline"
              />
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
              <v-icon
                icon="mdi-calendar-today-outline"
              />
            </template>
          </v-text-field>
        </div>

        <div class="d-flex justify-content-between mt-4">
          <div class="icon-color-selector" @click="showIconModal = true">
            <span class="label">Ícone</span>
            <div class="selector-box">
              <v-icon :icon="form.icon" size="24"></v-icon>
            </div>
          </div>
        </div>
      </div>
      </div>
    </v-form>
    <!-- <v-card
    :loading="isUpdating"
    class="mx-auto"
    color="blue-grey-darken-1"
    max-width="420"
  > -->


    <!-- <v-form> -->
      <!-- <v-container>
        <v-row dense> -->

          <!-- <v-col cols="12"> -->
           
          <!-- </v-col> -->
        <!-- </v-row>
      </v-container> -->
    <!-- </v-form> -->

    <!-- <v-divider></v-divider> -->

  <!-- </v-card> -->
  </div>
  <!-- <ErrorsForm />
  <ErrorMessage /> -->
</template>

<script setup lang="ts">
import { ref, computed, watch  } from 'vue';
import { useWalletsStore } from '@/store/wallets';
// 1. IMPORTAR OS MODAIS
import ModalIcons from '@/components/ModalIcons.vue';
import ModalColors from '@/components/ModalColors2.vue';

import MastercardIcon from '@/assets/icons/mastercard.svg';
import VisaIcon from '@/assets/icons/visa.svg';

const menu = ref(false);
const selectedColor = ref('#4CAF50'); // Cor inicial





  const srcs = {
    1: 'https://cdn.vuetifyjs.com/images/lists/1.jpg',
    2: 'https://cdn.vuetifyjs.com/images/lists/2.jpg',
    3: 'https://cdn.vuetifyjs.com/images/lists/3.jpg',
    4: 'https://cdn.vuetifyjs.com/images/lists/4.jpg',
    5: 'https://cdn.vuetifyjs.com/images/lists/5.jpg',
  }
  const people = [
    { name: 'mastercard', avatar: MastercardIcon },
    { name: 'Ali Connors', avatar: VisaIcon },
    { name: 'Trevor Hansen', avatar: srcs[3] },
    { name: 'Tucker Smith', avatar: srcs[2] },
    { name: 'Britta Holt', avatar: srcs[4] },
    { name: 'Jane Smith ', avatar: srcs[5] },
    { name: 'John Smith', avatar: srcs[1] },
    { name: 'Sandra Williams', avatar: srcs[3] },
  ]

  const autoUpdate = ref(true)
  const friends = ref(['mastercard'])
  const isUpdating = ref(false)
  const name = ref('Midnight Crew')
  const title = ref('The summer breeze')

  let timeout = -1
  watch(isUpdating, val => {
    clearTimeout(timeout)
    if (val) {
      timeout = setTimeout(() => (isUpdating.value = false), 3000)
    }
  })








// --- PROPS E EMITS ---
const props = defineProps({
  walletType: {
    type: String,
    required: true,
  },
});
const emit = defineEmits(['closeForm', 'updateData']);

const bandeiras = [
  { title: 'Mastercard', value: 'Mastercard', icon: MastercardIcon },
  { title: 'Visa', value: 'Visa', icon: VisaIcon },
  { title: 'Elo', value: 'Elo', icon: 'mdi-credit-card-outline' }, // Usando MDI como fallback
  { title: 'American Express', value: 'American Express', icon: 'mdi-credit-card-outline' },
  { title: 'Outra', value: 'Outra', icon: 'mdi-credit-card-outline' },
];

const selectedBrandIcon = computed(() => {
  const brand = bandeiras.find(b => b.value === form.value.bandeira);
  return brand ? brand.icon : null;
});

// --- STATE MANAGEMENT ---
const useWallets = useWalletsStore();
const loading = ref(false);

// 2. ADICIONAR CONTROLES DE VISIBILIDADE DOS MODAIS
const showIconModal = ref(false);
const showColorModal = ref(false);

const form = ref({
  name: '',
  icon: 'mdi-wallet', // Ícone padrão
  color: '#163dc0', // Cor padrão
  tipo: props.walletType === 'Conta' ? 'Conta Corrente' : 'Cartão de Crédito',
  saldoInicial: null,
  incluirEmSomaInicial: true,
  limite: null,
  conta: "Nenhuma",
  bandeira: 'Mastercard',
  dia_fechamento: null,
  dia_vencimento: null,
});

// --- VALIDAÇÃO (sem alterações) ---
const rules = {
  required: (value: any) => !!value || 'Campo obrigatório.',
  positive: (value: number) => value > 0 || 'O valor deve ser positivo.',
  dayOfMonth: (value: number) => (value >= 1 && value <= 31) || 'Dia inválido.',
};

const isFormValid = computed(() => {
  if (props.walletType === 'Conta') {
    return !!form.value.name && !!form.value.tipo;
  }
  if (props.walletType === 'Cartão') {
    const diaFechamentoValido = form.value.dia_fechamento && form.value.dia_fechamento >= 1 && form.value.dia_fechamento <= 31;
    const diaVencimentoValido = form.value.dia_vencimento && form.value.dia_vencimento >= 1 && form.value.dia_vencimento <= 31;
    return !!form.value.name && !!form.value.limite && !!form.value.bandeira && diaFechamentoValido && diaVencimentoValido;
  }
  return false;
});

// --- MÉTODOS ---
const submitForm = async () => {
  // ... (lógica de submit sem alterações)
  if (!isFormValid.value) return;
  loading.value = true;
  try {
    const payload = {
        ...form.value,
        tipo: props.walletType === 'Cartão' ? 'Cartão de Crédito' : form.value.tipo,
        saldo: form.value.saldoInicial,
    };
    await useWallets.store(payload);
    emit('updateData');
    closeForm();
  } catch (error) {
    console.error("Erro ao salvar:", error);
  } finally {
    loading.value = false;
  }
};

const closeForm = () => {
  emit('closeForm');
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
  /* padding: 8px; */
  width: 30px;
  height: 30px;
  /* display: flex;
  align-items: center;
  justify-content: space-between; */
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
  height: 24px; /* Ajuste a altura conforme necessário */
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
  /* Garante que o header fique sobre o conteúdo */
}

.form-body {
  padding: 120px 16px 16px 16px;
  /* Padding no topo para não ficar embaixo do header */
}

.close {
  background: transparent;
  color: white;
  box-shadow: none;
}

.btn {
  background-color: #77d08e;
  color: #1e1e1e;
  text-transform: none;
  font-weight: bold;
}

.imput {
  margin-top: 10px;
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















