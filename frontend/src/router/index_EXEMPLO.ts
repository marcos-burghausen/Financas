// router/index.ts - EXEMPLO DE ATUALIZAÇÃO COM NOVO LAYOUT

import MainLayout from "@/layouts/MainLayout.vue"; // ← NOVO
import { createRouter, createWebHistory } from "vue-router";
import routes from "./routes";

const router = createRouter({
    history: createWebHistory((import.meta as any).env.BASE_URL),
    routes: [
        // ========================================
        // ROTAS PÚBLICAS (sem layout)
        // ========================================
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

        // ========================================
        // ROTAS AUTENTICADAS (com MainLayout)
        // ========================================
        {
            path: "/dashboard",
            name: "dashboard",
            component: () => import("../views/DashboardView.vue"),
            meta: {
                auth: true,
                layout: MainLayout // ← ADICIONAR ISTO
            }
        },
        {
            path: "/contas",
            name: "contas",
            component: () => import("../views/contas/ContasView.vue"),
            meta: {
                auth: true,
                layout: MainLayout // ← ADICIONAR ISTO
            }
        },
        {
            path: "/despesas",
            name: "despesas",
            component: () => import("../views/despesas/DespesasView.vue"),
            meta: {
                auth: true,
                layout: MainLayout // ← ADICIONAR ISTO
            }
        },
        {
            path: "/receitas",
            name: "receitas",
            component: () => import("../views/receitas/ReceitasView.vue"),
            meta: {
                auth: true,
                layout: MainLayout // ← ADICIONAR ISTO
            }
        },
        {
            path: "/categorias",
            name: "categorias",
            component: () => import("../views/CategoriasView.vue"),
            meta: {
                auth: true,
                layout: MainLayout // ← ADICIONAR ISTO
            }
        },
        {
            path: "/cartoes",
            name: "cartoes",
            component: () => import("../views/cartaoCredito/CartaoCreditoView.vue"),
            meta: {
                auth: true,
                layout: MainLayout // ← ADICIONAR ISTO
            }
        },
        {
            path: "/admin",
            name: "admin",
            component: () => import("../views/admin/AdminPanelView.vue"),
            meta: {
                auth: true,
                requiresAdmin: true,
                layout: MainLayout // ← ADICIONAR ISTO
            }
        },
        {
            path: "/trader",
            name: "trader",
            component: () => import("../views/trader/TraderPanelView.vue"),
            meta: {
                auth: true,
                requiresTrader: true,
                layout: MainLayout // ← ADICIONAR ISTO
            }
        },
        {
            path: "/configuracoes/notificacoes",
            name: "notificacoes",
            component: () => import("../views/configuracoes/NotificacoesView.vue"),
            meta: {
                auth: true,
                layout: MainLayout // ← ADICIONAR ISTO
            }
        },
        {
            path: "/perfil",
            name: "perfil",
            component: () => import("../views/configuracoes/PerfilView.vue"),
            meta: {
                auth: true,
                layout: MainLayout // ← ADICIONAR ISTO
            }
        },
    ]
});

// antes de cada rota vai ser executado...
router.beforeEach(routes);

export default router;
