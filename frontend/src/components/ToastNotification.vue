<template>
  <!-- Top Toasts -->
  <Teleport to="body">
    <TransitionGroup
      name="toast-fade"
      class="toasts-container toasts-top"
    >
      <div
        v-for="toast in toastStore.topToasts"
        :key="toast.id"
        :class="`toast-wrapper toast-${toast.color}`"
      >
        <v-snackbar
          v-model="toast.show"
          :color="toast.color"
          :timeout="toast.timeout"
          location="top"
          variant="elevated"
          elevation="8"
        >
          <div class="d-flex align-center">
            <v-icon
              v-if="toast.icon"
              :icon="toast.icon"
              class="mr-2"
            />
            {{ toast.message }}
          </div>
          <template #actions>
            <v-btn
              variant="text"
              color="white"
              @click="toastStore.removeToast(toast.id)"
            >
              <v-icon icon="mdi-close" size="small" />
            </v-btn>
          </template>
        </v-snackbar>
      </div>
    </TransitionGroup>

    <!-- Bottom Toasts -->
    <TransitionGroup
      name="toast-fade"
      class="toasts-container toasts-bottom"
    >
      <div
        v-for="toast in toastStore.bottomToasts"
        :key="toast.id"
        :class="`toast-wrapper toast-${toast.color}`"
      >
        <v-snackbar
          v-model="toast.show"
          :color="toast.color"
          :timeout="toast.timeout"
          location="bottom"
          variant="elevated"
          elevation="8"
        >
          <div class="d-flex align-center">
            <v-icon
              v-if="toast.icon"
              :icon="toast.icon"
              class="mr-2"
            />
            {{ toast.message }}
          </div>
          <template #actions>
            <v-btn
              variant="text"
              color="white"
              @click="toastStore.removeToast(toast.id)"
            >
              <v-icon icon="mdi-close" size="small" />
            </v-btn>
          </template>
        </v-snackbar>
      </div>
    </TransitionGroup>
  </Teleport>
</template>

<script setup lang="ts">
import { useToastStore } from '@/store/toast';

const toastStore = useToastStore()
</script>

<style scoped>
.toasts-container {
  position: fixed;
  left: 16px;
  right: 16px;
  z-index: 2000;
  pointer-events: none;
}

.toasts-top {
  top: 16px;
}

.toasts-bottom {
  bottom: 16px;
}

.toast-wrapper {
  margin-bottom: 8px;
  pointer-events: auto;
}

/* Transitions */
.toast-fade-enter-active,
.toast-fade-leave-active {
  transition: all 0.3s ease;
}

.toast-fade-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-fade-leave-to {
  opacity: 0;
  transform: translateX(-100%);
}

.toast-fade-move {
  transition: transform 0.3s ease;
}

/* Colors */
.toast-success {
  --toast-color: #4caf50;
}

.toast-error {
  --toast-color: #f44336;
}

.toast-warning {
  --toast-color: #ff9800;
}

.toast-info {
  --toast-color: #2196f3;
}
</style>
