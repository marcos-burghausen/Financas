import MainLayout from "@/layouts/MainLayout.vue";
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
            component: () => import("../views/acesso/LoginView.vue"),
        },
        {
            path: "/login",
            name: "login",
            component: () => import("../views/acesso/LoginView.vue"),
        },
        {
            path: "/cadastro",
            name: "cadastro",
            component: () => import("../views/acesso/CadastroView.vue"),
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
            path: "/dashboard-admim",
            name: "dashAdmim",
            component: () => import("../views/admin/AdminPanelView.vue"),
            meta: {
                auth: true,
                layout: MainLayout
            }
        },
        {
            path: "/dashboard",
            name: "dashboard",
            component: () => import("../views/DashboardView.vue"),
            meta: {
                auth: true,
                layout: MainLayout
            }
        },
        {
            path: "/contas",
            name: "contas",
            component: () => import("../views/contas/ContasView.vue"),
            meta: {
                auth: true,
                layout: MainLayout
            }
        },
        {
            path: "/despesas",
            name: "despesas",
            component: () => import("../views/despesas/DespesasView.vue"),
            meta: {
                auth: true,
                layout: MainLayout
            }
        },
        {
            path: "/receitas",
            name: "receitas",
            component: () => import("../views/receitas/ReceitasView.vue"),
            meta: {
                auth: true,
                layout: MainLayout
            }
        },
        {
            path: "/categorias",
            name: "categorias",
            component: () => import("../views/CategoriasView.vue"),
            meta: {
                auth: true,
                layout: MainLayout
            }
        },
        {
            path: "/cartoes",
            name: "cartoes",
            component: () => import("../views/cartaoCredito/CartaoCreditoView.vue"),
            meta: {
                auth: true,
                layout: MainLayout
            }
        },
        {
            path: "/veiculos",
            name: "veiculos",
            component: () => import("../views/veiculo/VeiculosView.vue"),
            meta: {
                auth: true,
                layout: MainLayout
            }
        },
        {
            path: "/admin",
            name: "admin",
            component: () => import("../views/admin/AdminPanelView.vue"),
            meta: {
                auth: true,
                requiresAdmin: true,
                layout: MainLayout
            }
        },
        {
            path: "/trader",
            name: "trader",
            component: () => import("../views/trader/TraderPanelView.vue"),
            meta: {
                auth: true,
                requiresTrader: true,
                layout: MainLayout
            }
        },
        {
            path: "/configuracoes/notificacoes",
            name: "notificacoes",
            component: () => import("../views/configuracoes/NotificacoesView.vue"),
            meta: {
                auth: true,
                layout: MainLayout
            }
        },
        {
            path: "/perfil",
            name: "perfil",
            component: () => import("../views/configuracoes/PerfilView.vue"),
            meta: {
                auth: true,
                layout: MainLayout
            }
        },
    ]
});

// antes de cada rota vai ser executado...
router.beforeEach(routes);

export default router;
