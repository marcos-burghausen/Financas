<template>
  <div class="fundo__menu" :class="{ displayNone: !props.menuExpandido }">
    <nav class="menu-lateral">
      <div class="container__logo">
        <figure class="avatar">
          <img src="@/assets/img/profile-img.jpg" alt="logo" />
        </figure>
        <!-- prepend-avatar="https://randomuser.me/api/portraits/women/85.jpg" -->
        <v-list-item
          :subtitle="useUser.user.email"
          :title="name"
          class="white"
        ></v-list-item>
        <button class="close" @click="$emit('expandirMenu')">X</button>
      </div>
      <ul>
        <li
          v-for="(item, index) in filteredItensSideBar"
          :key="index"
          :class="{ efeitoClick: elementoAtivoSideBar === index }"
          @click="$emit('expandirMenu')"
        >
          <router-link :to="{ name: item.route }">
            <span class="icon">
              <mdicon :name="item.icon" />
            </span>
            <span
              class="txt__link"
              :class="{ displayNone: !props.menuExpandido }"
              >{{ item.name }}</span
            >
          </router-link>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, computed } from "vue";
import { useRoute } from "vue-router";
import { useUserStore } from "@/store/user";

const useUser = useUserStore();
console.log(useUser.user.user_tipe);

const route = useRoute();

let elementoAtivoSideBar = ref();

let name = ref(useUser.user.name.split(" ")[0]);

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
  menuExpandido: Boolean,
});

const itensSideBar = ref([
  // {
  //   name: "Admin",
  //   icon: "view-dashboard",
  //   route: "dashAdmim",
  //   adminOnly: true,
  // },
  // { name: "Trader", icon: "chart-line", route: "dashAdmim", traderOnly: true },
  // {
  //   name: "Dashboard",
  //   icon: "view-dashboard",
  //   route: "dashboard",
  //   adminOnly: false,
  //   traderOnly: false,
  // },
  {
    name: "Contas",
    icon: "bank-outline",
    route: "contas",
    adminOnly: false,
    traderOnly: false,
  },
  {
    name: "Receitas",
    icon: "arrow-top-right-bold-outline",
    route: "receitas",
    adminOnly: false,
    traderOnly: false,
  },
  {
    name: "Despesas",
    icon: "arrow-bottom-right-bold-outline",
    route: "despesas",
    adminOnly: false,
    traderOnly: false,
  },
  {
    name: "Categorias",
    icon: "bookmark-minus-outline",
    route: "categorias",
    adminOnly: false,
    traderOnly: false,
  },
  // { name: "Mais Opçõs", icon: "dots-horizontal", route: "dashboard" },
]);

// Computed para filtrar os itens com base no user_tipe
const filteredItensSideBar = computed(() => {
  return itensSideBar.value.filter((item) => {
    if (item.adminOnly) {
      return (
        useUser.user.user_tipe === "ADMIM" || useUser.user.user_tipe === "FULL"
      );
    } else if (item.traderOnly) {
      return (
        useUser.user.user_tipe === "TRADER" ||
        useUser.user.user_tipe === "USER_TRADER" ||
        useUser.user.user_tipe === "FULL"
      );
    }
    return true; // Exibe os outros itens normalmente
  });
});
</script>

<style scoped>
.fundo__menu {
  background-color: rgba(0, 0, 0, 0.6);
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1000;
  /* transition: transform 10s ease;
    transform: translateY(0%); */
}
.menu-lateral {
  background-color: rgb(44, 44, 46);
  /* margin: 0 15px 0 0; */
  width: 80%;
  padding: 10px 8px 40px 0;
  /* box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23; */
  /* transition: 0.5s; */
  height: 100%;
}
.menu-lateral ul {
  list-style-type: none;
}
.white {
  color: #fefefe;
}

.container__logo {
  display: flex;
  justify-content: start;
  margin-bottom: 30px;
  padding: 0 0 0 15px;
}
.avatar {
  background: #fefefe;
  border-radius: 50%;
  height: 55px;
  width: 55px;
  margin: 0;
}

.avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover; /* Garante que a imagem seja cortada corretamente para caber dentro do container */
  border-radius: 50%; /* Garante que a imagem siga o formato circular do container */
}
.name__email {
  display: flex;
  flex-direction: column;
  justify-content: center;
  margin-left: 20px;
}
.close {
  position: absolute;
  right: 20px;
  top: 20px;
  background: #be121269;
  color: #fefefe;
  font-size: 30px;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
}

.displayNone {
  display: none;
}

.title {
  color: #fefefe;
  font-size: 20px;
  margin: 0;
  text-align: center;
  padding: 0 0 0 20px;
}
.mdicon {
  color: #77d08e;
  cursor: pointer;
  /* padding: 10px; */
  /* box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23; */
  border-radius: 50px;
  width: 70px;
  height: 50px;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 10px 10px 0 0;
  position: absolute;
  right: 50px;
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
