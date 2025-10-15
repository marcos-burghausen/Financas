import type { NavigationGuardNext, RouteLocationNormalized } from "vue-router";

import { useAuthStore } from "@/store/auth";
import { useRolesStore } from "@/store/roles";

export default async function routes(
  to: RouteLocationNormalized,
  from: RouteLocationNormalized,
  next: NavigationGuardNext
) {
    //se existir o meta para a rota que estou indo
    const useAuth = useAuthStore();
    const rolesStore = useRolesStore();

    // Verificar autenticação
    if (to.meta?.auth && !useAuth.isAuthenticated) {
        return next({ name: "home"});
    }

    // Verificar se a rota requer permissão de admin
    if (to.meta?.requiresAdmin) {
        // Buscar permissões se ainda não foram carregadas
        if (rolesStore.myRoles.length === 0) {
            try {
                await rolesStore.fetchMyPermissions();
            } catch (error) {
                console.error('Erro ao carregar permissões:', error);
                return next({ name: "dashboard" });
            }
        }

        // Verificar se tem permissão de admin
        if (!rolesStore.isAdmin) {
            return next({ name: "dashboard" });
        }
    }

    // Verificar se a rota requer permissão de trader
    if (to.meta?.requiresTrader) {
        // Buscar permissões se ainda não foram carregadas
        if (rolesStore.myRoles.length === 0) {
            try {
                await rolesStore.fetchMyPermissions();
            } catch (error) {
                console.error('Erro ao carregar permissões:', error);
                return next({ name: "dashboard" });
            }
        }

        // Verificar se tem role de TRADER, USER_TRADER ou FULL
        const hasTraderRole = rolesStore.hasAnyRole(['TRADER', 'USER_TRADER', 'FULL']);
        if (!hasTraderRole) {
            return next({ name: "dashboard" });
        }
    }

    next();
}