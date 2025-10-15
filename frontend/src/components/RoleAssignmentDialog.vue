<template>
  <v-dialog v-model="dialog" max-width="600" persistent>
    <v-card>
      <v-card-title class="text-h5 d-flex align-center">
        <v-icon icon="mdi-shield-edit" class="mr-2" color="primary" />
        Gerenciar Roles
      </v-card-title>
      
      <v-card-subtitle v-if="user" class="mt-2">
        Usuário: <strong>{{ user.name }}</strong> ({{ user.email }})
      </v-card-subtitle>

      <v-divider />

      <v-card-text class="py-6">
        <v-alert v-if="error" type="error" class="mb-4" closable @click:close="error = null">
          {{ error }}
        </v-alert>

        <p class="text-subtitle-2 mb-4">
          Selecione as roles que deseja atribuir ao usuário:
        </p>

        <v-chip-group v-model="selectedRoles" column multiple>
          <v-chip
            v-for="role in rolesStore.roles"
            :key="role.id"
            :value="role.id"
            :color="getRoleColor(role.name)"
            filter
            variant="outlined"
            size="large"
            class="ma-1"
          >
            <v-icon :icon="getRoleIcon(role.name)" size="small" class="mr-2" />
            {{ role.display_name }}
          </v-chip>
        </v-chip-group>

        <v-divider class="my-4" />

        <div v-if="selectedPermissions.length > 0">
          <p class="text-subtitle-2 mb-3">
            Permissões que o usuário terá:
          </p>
          <v-chip-group column>
            <v-chip
              v-for="permission in selectedPermissions.slice(0, 15)"
              :key="permission"
              size="small"
              variant="tonal"
              color="primary"
            >
              {{ getPermissionLabel(permission) }}
            </v-chip>
            <v-chip
              v-if="selectedPermissions.length > 15"
              size="small"
              variant="outlined"
            >
              +{{ selectedPermissions.length - 15 }} mais
            </v-chip>
          </v-chip-group>
        </div>
        <v-alert v-else type="info" variant="tonal" density="compact" class="mt-2">
          Nenhuma role selecionada. O usuário não terá permissões.
        </v-alert>
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
          @click="saveRoles"
          :loading="loading"
          :disabled="!hasChanges"
        >
          Salvar Alterações
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import { useRolesStore } from '@/store/roles';
import type { UserWithRoles } from '@/types/roles.types';
import { PERMISSION_DESCRIPTIONS, ROLE_COLORS, ROLE_ICONS } from '@/types/roles.types';
import { computed, ref, watch } from 'vue';

// Props & Emits
const props = defineProps<{
  modelValue: boolean;
  user: UserWithRoles | null;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void;
  (e: 'updated'): void;
}>();

// Store
const rolesStore = useRolesStore();

// State
const loading = ref(false);
const error = ref<string | null>(null);
const selectedRoles = ref<number[]>([]);
const initialRoles = ref<number[]>([]);

// Computed
const dialog = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});

const selectedPermissions = computed(() => {
  const permissions = new Set<string>();
  
  selectedRoles.value.forEach(roleId => {
    const role = rolesStore.getRoleById(roleId);
    if (role) {
      role.permissions.forEach(p => permissions.add(p));
    }
  });
  
  return Array.from(permissions);
});

const hasChanges = computed(() => {
  if (selectedRoles.value.length !== initialRoles.value.length) return true;
  return !selectedRoles.value.every(id => initialRoles.value.includes(id));
});

// Methods
const getRoleColor = (roleName: string): string => {
  return ROLE_COLORS[roleName] || 'grey';
};

const getRoleIcon = (roleName: string): string => {
  return ROLE_ICONS[roleName] || 'mdi-account';
};

const getPermissionLabel = (permission: string): string => {
  return PERMISSION_DESCRIPTIONS[permission] || permission;
};

const loadUserRoles = () => {
  if (!props.user) return;
  
  const roleIds = props.user.roles.map(r => r.id);
  selectedRoles.value = [...roleIds];
  initialRoles.value = [...roleIds];
};

const saveRoles = async () => {
  if (!props.user) return;
  
  loading.value = true;
  error.value = null;
  
  try {
    await rolesStore.assignRolesToUser(props.user.id, selectedRoles.value);
    emit('updated');
    closeDialog();
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erro ao salvar roles';
  } finally {
    loading.value = false;
  }
};

const closeDialog = () => {
  dialog.value = false;
  error.value = null;
};

// Watchers
watch(() => props.user, (newUser) => {
  if (newUser) {
    loadUserRoles();
  }
}, { immediate: true });

watch(dialog, (isOpen) => {
  if (!isOpen) {
    // Reset ao fechar
    setTimeout(() => {
      selectedRoles.value = [];
      initialRoles.value = [];
      error.value = null;
    }, 300);
  }
});
</script>

<style scoped>
:deep(.v-chip-group) {
  gap: 8px;
}
</style>
