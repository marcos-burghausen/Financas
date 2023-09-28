<template>
    <div>
        <nav class="menu-lateral" :class="{ expandido: menuExpandido }">
            <div class="container__logo">
                <router-link :to="{ name: 'dashboard' }">
                    <img src="@/assets/img/2.png" alt="logo" />
                </router-link>
                <h1 :class="{ displayNone: !menuExpandido }" class="title">Gerenciador Financeiro</h1>
            </div>
            <ul>
                <li v-for="(item, index) in itensSideBar" :class="{ efeitoClick: elementoAtivoSideBar }" :key="index">
                    <!-- @click="elementoAtivoSideBar = index"> -->
                    <router-link :to="{ name: item.route }">
                        <span class="icon">
                            <mdicon :name="item.icon" />
                        </span>
                        <span class="txt__link" :class="{ displayNone: !menuExpandido }">{{
                            item.name
                        }}</span>
                    </router-link>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { useRoute } from "vue-router";

const route = useRoute()

let rota = ref();
// rota = route.name;
// console.log(rota);
let elementoAtivoSideBar = ref(0);

watch(route, (value) => {
    switch (value.name) {
        case 'dashboard':
            elementoAtivoSideBar.value = 0;
            break;
        case 'receitas':
            elementoAtivoSideBar.value = 1;
            break;
        case 'despesas':
            elementoAtivoSideBar.value = 2;
            break;
    }
    console.log(value.name);

})

const props = defineProps({
    menuExpandido: Boolean
});

const itensSideBar = ref([
    { name: "DashBoard", icon: "view-dashboard", route: "dashboard" },
    { name: "Receitas", icon: "view-dashboard", route: "receitas" },
    { name: "Despesas", icon: "view-dashboard", route: "despesas" },
    { name: "Mais Opçõs", icon: "dots-horizontal", route: 'dashboard' },
]);
</script>

<style scoped>
.menu-lateral {
    background-color: rgba(0, 0, 0, 0.1);
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
