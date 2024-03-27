<template>
  <div
    class="fundo__menu"
    :class="{ displayNone: !props.menuExpandido }"
  >
    <nav
      class="menu-lateral"
    >
      <!-- :class="{ expandido: props.menuExpandido }" -->
      <div class="container__logo">
        <router-link :to="{ name: 'dashboard' }">
          <img
            src="@/assets/img/2.png"
            alt="logo"
          >
        </router-link>
        <h1
          :class="{ displayNone: !props.menuExpandido }"
          class="title"
        >
          Gerenciador Financeiro
        </h1>
        <mdicon
          name="menu-open"
          class="mdicon"
          @click="$emit('expandirMenu')"
        />
      </div>
      <ul>
        <li
          v-for="(item, index) in itensSideBar"
          :key="index"
          :class="{ efeitoClick: elementoAtivoSideBar === index }"
          @click="$emit('expandirMenu')"
        >
          <router-link
            :to="{ name: item.route }"
          >
            <span class="icon">
              <mdicon :name="item.icon" />
            </span>
            <span
              class="txt__link"
              :class="{ displayNone: !props.menuExpandido }"
            >{{
              item.name
            }}</span>
          </router-link>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();

let elementoAtivoSideBar = ref();

watch(route, (value) => {
    switch (value.name) {
    case "dashboard":
        elementoAtivoSideBar.value = 0;
        break;
    case "contas":
        elementoAtivoSideBar.value = 1;
        break;
    case "receitas":
        elementoAtivoSideBar.value = 2;
        break;
    case "despesas":
        elementoAtivoSideBar.value = 3;
        break;
    case "categorias":
        elementoAtivoSideBar.value = 4;
        break;
    }

});

const props = defineProps({
    menuExpandido: Boolean
});

const itensSideBar = ref([
    { name: "DashBoard", icon: "view-dashboard", route: "dashboard" },
    { name: "Contas", icon: "bank-outline", route: "contas" },
    { name: "Receitas", icon: "arrow-top-right-bold-outline", route: "receitas" },
    { name: "Despesas", icon: "arrow-bottom-right-bold-outline", route: "despesas" },
    { name: "Categorias", icon: "bookmark-minus-outline", route: "categorias" },
    // { name: "Mais Opçõs", icon: "dots-horizontal", route: "dashboard" },
]);
</script>

<style scoped>
.menu-lateral {
  background-color: rgb(44, 44, 46);
  /* margin: 0 15px 0 0; */
  width: 280px;
  padding: 10px 8px 40px 0;
  /* box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23; */
  /* transition: 0.5s; */
  height: 100%;
  
}
.fundo__menu {
  background-color: rgba(0, 0, 0, 0.6);
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1000;
  /* transition: transform 10s ease;
  transform: translateY(0%); */
}

.container__logo {
    display: flex;
    justify-content: center;
    margin-bottom: 30px;

}

.container__logo img {
    margin-left: 10px;
    width: 70px;
}

.displayNone {
    display: none;
}

.title {
    color: #fefefe;
    font-size: 20px;
    margin: 0;
    text-align: center;
    padding: 10px 0;
}
.mdicon {
    color: #77d08e;
    cursor: pointer;
    /* padding: 10px; */
    box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
    border-radius: 50px;
    width: 70px;
    height: 50px;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 10px 10px 0 0;
}

.efeitoClick {
    box-shadow: inset -4px -4px 5px #3e4247, inset 7px 7px 7px #1d1f23;
    border-top-right-radius: 32px;
    border-bottom-right-radius: 32px;
}

.menu-lateral ul {
    padding-left: 0;
}

.icon {
    margin-left: 13px;
    color: #77d08e;
}

.txt__link {
    margin-left: 20px;
    transition: 0.5s;
    color: #fefefe;
    text-align: center !important;
}

.menu-lateral ul li a {
    text-decoration: none;
    font-size: 20px;
    padding: 10px 7%;
    display: flex;
}

.expandido {
    width: 220px;
}
</style>
