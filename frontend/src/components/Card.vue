<template>
  <div class="card__uni">
    <router-link
      class="link"
      :to="{ name: rota }"
    >
      <h5 class="title">
        {{ titulo }}
      </h5>
      <div class="progres">
        <div class="progress__bar" />
      </div>
      <p class="value">
        R$ {{ valor }}
      </p>
    </router-link>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
const props = defineProps({
    titulo: {
        type: String,
        required: true,
    },
    valor: {
        type: Number,
        required: true,
    },
    rota: {
        type: String,
        required: true,
    }
});

const titulo = computed(() => props.titulo);
const valor = computed(() => props.valor);
const rota = computed(() => props.rota);
</script>

<style scoped>
.card__uni {
  box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
  margin-bottom: 10px;
}
.link {
    text-decoration: none;
}

.title {
    color: #fefefe;
    text-align: center;
}

.progres {
    padding: 0 10px;
}

.progress__bar {
    width: 100%;
    height: 3px;
    background: #77d08e;
}

.value {
    color: #fefefe;
    margin-bottom: 0;
    text-align: center;
    font-size: 20px;
}

@media screen and (min-width: 601px) and (max-width: 700px) {
    .value {
        font-size: 1,5rem;
    }
  /* Estilos para telas entre 501px e 700px de largura */
}
</style>