<template>
  <v-card>
    <v-layout>
      <v-navigation-drawer
        v-model="drawer"
        class="menu__lateral"
      >
        <v-list-item
          prepend-avatar="https://randomuser.me/api/portraits/men/78.jpg"
          title="John Leider"
        />
  
        <v-divider />
  
        <v-list
          density="compact"
          nav
        >
          <v-list-item
            v-for="(item, index) in itensSideBar"
            :key="index"
            :class="{ efeitoClick: elementoAtivoSideBar === index }"
            @click.stop="drawer = !drawer"
          >
            <router-link :to="{ name: item.route }">
              <span class="icon">
                <mdicon :name="item.icon" />
              </span>
              <span
                class="txt__link"
              >{{
                item.name
              }}</span>
            </router-link>
          </v-list-item>
        </v-list>
      </v-navigation-drawer>
      <!-- <v-main style="height: 250px">
        <div class="d-flex justify-center align-center h-100">
          <v-btn
            color="primary"
            @click.stop="drawer = !drawer"
          >
            Toggle
          </v-btn>
        </div>
      </v-main> -->
    </v-layout>
  </v-card>
</template>

<script setup>
import { ref } from "vue";

let drawer = ref(props.menuExpandido);
let elementoAtivoSideBar = ref();
const props = defineProps({
    menuExpandido: Boolean
});

const itensSideBar = ref([
    { name: "DashBoard", icon: "view-dashboard", route: "dashboard" },
    { name: "Receitas", icon: "arrow-top-right-bold-outline", route: "receitas" },
    { name: "Despesas", icon: "arrow-bottom-right-bold-outline", route: "despesas" },
    { name: "Categorias", icon: "bookmark-minus-outline", route: "categorias" },
    { name: "Mais Opçõs", icon: "dots-horizontal", route: "dashboard" },
]);
</script>

<style scoped>
.menu__lateral {
    background: rgb(0, 0, 0);
    margin: 0 15px 0 0;
    width: 70px;
    padding: 10px 8px 40px 0;
    box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
    transition: 0.5s;
    height: calc(100vh - 30px);
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