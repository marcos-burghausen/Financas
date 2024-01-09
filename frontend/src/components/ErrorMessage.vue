<template>
  <div
    v-show="errorStore.errorMessage"
    class="error"
    role="alert"
  >
    <!-- <p class="error__message">{{ errorCode }}</p> -->
    <p class="error__message">
      {{ errorStore.errorMessage }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { useErrorStore } from "@/store/error";
import { onMounted } from "vue";
const errorStore = useErrorStore();

onMounted(() => {
    errorStore.unsetError();
});

</script>

<style scoped>
.error {
  color: rgb(247, 20, 20);
    text-align: center;
}
</style>