<template>
    <div>
        <router-link class="link" :to="{ name: rota }">
            <h5 class="title">{{ titulo }}</h5>
            <div class="progres">

                <div class="progress__bar"></div>
            </div>
            <p class="value">R$ {{ valor }}</p>
        </router-link>
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
}
</style>