<template>
  <button
    class="btn__selec__cor"
    @click="openModal = true"
  >
    Selecionar cor
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
        :class="item.color"
        @click="selectItem(item.color)"
      />
    </ul>
  </div>
</template>
<script setup lang="ts">
import { ref } from "vue";

interface IColor {
    color: string
}

const openModal = ref(false);
const props = defineProps({
    items: {
        type: Array<IColor> 
    }
});
const emit = defineEmits(["atualizarVariavel"]);
const items = ref(props.items);

const selectItem = (value: string) => {
    emit("atualizarVariavel", value);
};

</script>

<style scoped>
.btn__selec__cor {
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
    width: 13%;
    height: 28px;
    border-radius: 50%;
    margin: 3px;
    text-align: center;
    color: #fefefe;
    cursor: pointer;
}

.cor__1 {
    background: #ff8a00;
}

.cor__2 {
    background: #cc0000;
}

.cor__3 {
    background: #2cb1e1;
}

.cor__4 {
    background: #c58be2;
}

.cor__5 {
    background: #99cc00;
}

.cor__6 {
    background: #c5e26d;
}

.cor__7 {
    background: #9933cc;
}

.cor__8 {
    background: #3b3b3b;
}

.cor__9 {
    background: #686868;
}

.cor__10 {
    background: #ff4444;
}

.cor__11 {
    background: #2a14ff;
}

.cor__12 {
    background: #d6adeb;
}

.cor__13 {
    background: #bcbcbc;
}

.cor__14 {
    background: #669900;
}

.cor__15 {
    background: #439996;
}

.cor__16 {
    background: #ffbd21;
}

.cor__17 {
    background: #ff9494;
}

.cor__18 {
    background: #8f8f8f;
}

.cor__19 {
    background: #8ad5f0;
}

.cor__20 {
    background: #000000;
}

.cor__21 {
    background: #24847a;
}

.cor__22 {
    background: #a2b6c2;
}

.cor__23 {
    background: #930101;
}

.cor__24 {
    background: #bb6e00;
}

.cor__25 {
    background: #0099cc;
}
</style>