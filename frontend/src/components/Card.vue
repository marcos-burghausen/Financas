<template>
  <div>
    <div class="">
      <router-link to="/despesas">
        <h5 class="text-white mb-0">{{ titulo }}</h5>
        <div class="progress my-3" style="height: 3px">
          <div class="progress-bar" style="width: 100%"></div>
        </div>
        <p class="mb-0 text-white small-font">R$ {{ valor }}</p>
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
const props = defineProps({
  titulo: String,
  valor: Number,
  rota: String,
});

const titulo = computed(() => props.titulo);
const valor = computed(() => props.valor);
const rota = computed(() => props.rota);
</script>

<style scoped>
.card {
  /* height: 140px; */
  box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
  padding: 15px 15px;
  color: #ccc;
  font-size: 30px;
  background-color: transparent;
}
</style>