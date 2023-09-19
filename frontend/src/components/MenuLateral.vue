<template>
  <div class="">
    <nav class="menu-lateral" :class="{ expandido: menuExpandido }">
      <div class="d-flex justify-content-center mb-4">
        <img src="@/assets/img/2.png" alt="" />
        <!-- <mdicon
          name="menu-open"
          @click="menuExpandido = !menuExpandido"
          class="mdicon"
        /> -->
      </div>
      <ul>
        <li v-for="(item, index) in itensSideBar" class="item-menu"
          :class="{ efeitoClick: elementoAtivoSideBar === index }" :key="index" @click="elementoAtivoSideBar = index">
          <a href="#">
            <span class="icon">
              <mdicon :name="item.icon" />
            </span>
            <span class="txt-link" :class="{ displayNone: !menuExpandido }">{{
              item.name
            }}</span>
          </a>
        </li>
      </ul>
    </nav>
  </div>
  <!--menu-lateral-->
</template>

<script setup>
import { ref, inject } from "vue";

const props = defineProps({
  menuExpandido: Boolean
});

let elementoAtivoSideBar = ref(0);

// const menuExpandido = inject("encolherMenu");

const itensSideBar = ref([
  // { name: "Home", icon: "home" },
  { name: "DashBoard", icon: "view-dashboard" },
  { name: "Agenda", icon: "view-dashboard" },
  { name: "Configurações", icon: "view-dashboard" },
  { name: "Conta", icon: "view-dashboard" },
]);
</script>

<style scoped>
.mdicon {
  color: #0097a7;
  cursor: pointer;
  margin: 20px;
  position: absolute;
  top: -12px;
  right: -107px;
  padding: 10px;
  box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
  border-radius: 20px;
}

.menu-lateral {
  margin: 0 15px 0 0;
  width: 70px;
  padding: 10px 8px 40px 0;
  box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
  transition: 0.5s;
  height: calc(100vh - 30px);
}

/* .menu-lateral:hover {
  width: 220px;
} */
.menu-lateral div {
  display: flex;
  position: relative;
  color: #0097a7;
}

.menu-lateral div img {
  margin-left: 10px;
  width: 70px;
}

.menu-lateral div h2 {
  overflow: hidden;
  /* margin-left: 5px; */
}

.menu-lateral ul {
  overflow: hidden;
  height: 100%;
  list-style-type: none;
  padding-left: 0;
}

.menu-lateral ul li {
  transition: 0.2s;
}

.menu-lateral ul li a {
  text-decoration: none;
  font-size: 20px;
  padding: 10px 7%;
  display: flex;
}

.menu-lateral ul li a .txt-link {
  margin-left: 20px;
  transition: 0.5s;
  color: #fefefe;
  text-align: center !important;
}

ul li.item-menu a .icon {
  margin-left: 13px;
  color: #77d08e;
}

.expandido {
  width: 220px;
}

.efeitoClick {
  box-shadow: inset -4px -4px 5px #3e4247, inset 7px 7px 7px #1d1f23;
  border-top-right-radius: 32px;
  border-bottom-right-radius: 32px;
}

.displayNone {
  /* display: none; */
}
</style>
