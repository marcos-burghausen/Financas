<template>
    <div class="error" v-show="errorStore.errorMessage" role="alert">
        <!-- <p class="error__message">{{ errorCode }}</p> -->
        <p class="error__message">{{ errorStore.errorMessage }}</p>
    </div>
</template>

<script setup lang="ts">
import { useErrorStore } from "@/stores/error";
import { onMounted } from "vue";
const errorStore = useErrorStore();
import errorCodes from "@/assets/errorCodes.json";

onMounted(() => {
    errorStore.unsetError();
});

</script>

<style scoped>
.error {
    color: rgb(194, 4, 4);
    text-align: center;
}
</style>