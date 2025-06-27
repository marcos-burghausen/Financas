// router/index.ts
import { createRouter, createWebHistory } from "vue-router";
import routes from "./routes";

const router = createRouter({
    history: createWebHistory((import.meta as any).env.BASE_URL),
    routes: [
        {
            path: "/",
            name: "home",
            component: () => import("../views/HomeView.vue"),
        },
        {
            path: "/auth/callback",
            name: "facebookCallback",
            component: () => import("../components/FacebookCallback.vue"),
        },
        {
            path: "/dashboard-admim",
            name: "dashAdmim",
            component: () => import("../views/mobile/DashboardAdmimView.vue"),
            meta: {
                auth: true
            }
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
            // component: () => import("../views/receitas/ReceitasView copy.vue"),
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
