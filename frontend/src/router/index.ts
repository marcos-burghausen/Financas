import { createRouter, createWebHistory } from "vue-router";
import routes from "../router/routes.ts";
import HomeView from "../views/HomeView.vue";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: "/",
            name: "home",
            component: HomeView
        },
        {
            path: "/auth/callback",
            name: "facebookCallback",
            component: () => import("../components/FacebookCallback.vue"),
        },
        {
            path: "/dashboard",
            name: "dashboard",
            component: () => import("../views/mobile/DashboardMobileView copy.vue"),
            meta: {
                auth: true
            }
        },
        {
            path: "/contas",
            name: "contas",
            component: () => import("../views/contas/ContasView.vue"),
            meta: {
                auth: true
            }
        },
        {
            path: "/despesas",
            name: "despesas",
            component: () => import("../views/despesas/DespesasView.vue"),
            meta: {
                auth: true
            }
        },
        {
            path: "/receitas",
            name: "receitas",
            component: () => import("../views/receitas/ReceitasView.vue"),
            meta: {
                auth: true
            }
        },
        {
            path: "/categorias",
            name: "categorias",
            component: () => import("../views/CategoriasView.vue"),
            meta: {
                auth: true
            }
        },
        {
            path: "/cartao",
            name: "cartao",
            component: () => import("../views/cartaoCredito/CartaoCreditoView.vue"),
            meta: {
                auth: true
            }
        },
    ]
});

// antes de cada rota vai ser executado...
router.beforeEach(routes);

export default router;
