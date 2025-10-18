<template>
  <v-snackbar
    v-model="visible"
    :timeout="duration"
    :color="colorMap[type]"
    :elevation="24"
    rounded="lg"
    location="bottom right"
    class="notification-snackbar"
  >
    <div class="d-flex align-center gap-3">
      <v-icon :icon="iconMap[type]" size="24" />
      <div class="text-body2">{{ message }}</div>
      <v-btn
        icon="mdi-close"
        size="x-small"
        variant="text"
        @click="visible = false"
      />
    </div>
  </v-snackbar>
</template>

<script setup lang="ts">
import { defineEmits, defineProps, ref, watch } from 'vue'

interface Props {
  modelValue: boolean
  message: string
  type?: 'success' | 'error' | 'warning' | 'info'
  duration?: number
}

const props = withDefaults(defineProps<Props>(), {
  type: 'info',
  duration: 3000
})

const emit = defineEmits(['update:modelValue'])

const visible = ref(props.modelValue)

const colorMap = {
  success: 'success',
  error: 'error',
  warning: 'warning',
  info: 'info'
}

const iconMap = {
  success: 'mdi-check-circle',
  error: 'mdi-alert-circle',
  warning: 'mdi-alert',
  info: 'mdi-information'
}

watch(
  () => props.modelValue,
  (newVal) => {
    visible.value = newVal
  }
)

watch(visible, (newVal) => {
  emit('update:modelValue', newVal)
})
</script>

<style scoped>
.notification-snackbar {
  font-family: 'Roboto', sans-serif;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.notification-snackbar :deep(.v-snackbar__content) {
  padding: 16px 24px;
}
</style>
