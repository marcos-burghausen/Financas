<template>
  <v-dialog v-model="dialog" max-width="500" persistent>
    <v-card>
      <v-card-title class="text-h5">
        <v-icon icon="mdi-account-edit" class="mr-2" color="primary" />
        Editar Usuário
      </v-card-title>

      <v-divider />

      <v-card-text class="py-6">
        <v-alert v-if="error" type="error" class="mb-4" closable @click:close="error = null">
          {{ error }}
        </v-alert>

        <v-form ref="form" v-model="valid">
          <v-text-field
            v-model="formData.name"
            label="Nome"
            prepend-inner-icon="mdi-account"
            variant="outlined"
            :rules="[rules.required, rules.minLength(3)]"
            class="mb-4"
          />

          <v-text-field
            v-model="formData.email"
            label="E-mail"
            prepend-inner-icon="mdi-email"
            variant="outlined"
            type="email"
            :rules="[rules.required, rules.email]"
          />
        </v-form>
      </v-card-text>

      <v-divider />

      <v-card-actions class="pa-4">
        <v-spacer />
        <v-btn variant="text" @click="closeDialog" :disabled="loading">
          Cancelar
        </v-btn>
        <v-btn
          color="primary"
          variant="elevated"
          @click="saveUser"
          :loading="loading"
          :disabled="!valid || !hasChanges"
        >
          Salvar
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import axiosInstance from '@/services/http';
import type { UserWithRoles } from '@/types/roles.types';
import { computed, reactive, ref, watch } from 'vue';

// Props & Emits
const props = defineProps<{
  modelValue: boolean;
  user: UserWithRoles | null;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void;
  (e: 'updated'): void;
}>();

// State
const loading = ref(false);
const error = ref<string | null>(null);
const valid = ref(false);
const form = ref<any>(null);

const formData = reactive({
  name: '',
  email: '',
});

const initialData = reactive({
  name: '',
  email: '',
});

// Validation Rules
const rules = {
  required: (v: string) => !!v || 'Campo obrigatório',
  minLength: (min: number) => (v: string) => 
    (v && v.length >= min) || `Mínimo de ${min} caracteres`,
  email: (v: string) => {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return pattern.test(v) || 'E-mail inválido';
  },
};

// Computed
const dialog = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});

const hasChanges = computed(() => {
  return formData.name !== initialData.name || formData.email !== initialData.email;
});

// Methods
const loadUserData = () => {
  if (!props.user) return;
  
  formData.name = props.user.name;
  formData.email = props.user.email;
  initialData.name = props.user.name;
  initialData.email = props.user.email;
};

const saveUser = async () => {
  if (!props.user || !form.value) return;
  
  const { valid: isValid } = await form.value.validate();
  if (!isValid) return;
  
  loading.value = true;
  error.value = null;
  
  try {
    await axiosInstance.put(`/admin/users/${props.user.id}`, {
      name: formData.name,
      email: formData.email,
    });
    
    emit('updated');
    closeDialog();
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erro ao atualizar usuário';
  } finally {
    loading.value = false;
  }
};

const closeDialog = () => {
  dialog.value = false;
  error.value = null;
  form.value?.reset();
};

// Watchers
watch(() => props.user, (newUser) => {
  if (newUser) {
    loadUserData();
  }
}, { immediate: true });

watch(dialog, (isOpen) => {
  if (!isOpen) {
    setTimeout(() => {
      formData.name = '';
      formData.email = '';
      error.value = null;
    }, 300);
  }
});
</script>
