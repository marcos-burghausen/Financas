<template>
  <button
    class="btn__selec__icon"
    @click="openModal = true"
  >
    Selecionar icone
  </button>

  <div
    v-if="openModal"
    class="modal__icons"
    @click="openModal = false"
  >
    <ul class="ul__dropdown">
      <li
        v-for="(item, index) in items"
        :key="index"
        @click.stop="selectItem(item.icon)"
      >
        <mdicon :name="item.icon" />
      </li>
    </ul>
  </div>
</template>
<script setup lang="ts">
import { ref } from "vue";
import type { PropType } from "vue";

interface IconItem {
  icon: string;
}

const openModal = ref(false);
const props = defineProps({
    items: {
        type: Array as PropType<IconItem[]>,
        required: true,
        default: () => []
    }
});
const emit = defineEmits(["atualizarVariavel"]);
const items = ref(props.items);

const selectItem = (value: string) => {
    emit("atualizarVariavel", value);
    openModal.value = false; // Fecha o modal após selecionar
};

</script>

<style scoped>
.btn__selec__icon {
    border: none;
    border-radius: 20px;
    padding-block: 5px;
    padding-inline: 20px;
    color: rgba(255, 255, 255, 0.3);
    background-color: rgba(255, 255, 255, 0.12);
}

.modal__icons {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.50);
}

.ul__dropdown {
    position: absolute;
    top: 20%;
    left: 25%;
    padding: 5px;
    list-style: none;
    border-radius: 10px;
    width: 230px;
    height: 150px;
    display: flex;
    flex-wrap: wrap;
    overflow: hidden;
    overflow-y: scroll;
    background-color: rgb(44, 44, 46);
}

.ul__dropdown::-webkit-scrollbar {
    width: 5px;
    background-color: rgba(255, 255, 255, 0.12);
    border-radius: 5px;
}

.ul__dropdown::-webkit-scrollbar-thumb {
    background-color: rgba(255, 255, 255, 0.3);
    border-radius: 5px;
}

.ul__dropdown li {
    margin: 3px;
}
</style>