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
        let hasAdminAccess = false;
        
        // Primeiro tenta verificar via rolesStore
        if (rolesStore.isAdmin) {
            hasAdminAccess = true;
        } else if (rolesStore.myRoles.length === 0) {
            // Se myRoles está vazio, tenta carregar
            try {
                console.log('🔍 Buscando permissões de admin...');
                await rolesStore.fetchMyPermissions();
                console.log('✅ Permissões carregadas:', rolesStore.myRoles);
                if (rolesStore.isAdmin) {
                    hasAdminAccess = true;
                }
            } catch (error) {
                console.error('❌ Erro ao carregar permissões:', error);
            }
        }
        
        // Se ainda não tem acesso, verifica fallback com userData (localStorage)
        if (!hasAdminAccess) {
            try {
                const storedUser = localStorage.getItem('userData');
                const userData = storedUser ? JSON.parse(storedUser) : null;
                const userRole = (userData?.type || "user").toUpperCase();
                hasAdminAccess = (userRole === "ADMIN" || userRole === "FULL");
                if (hasAdminAccess) {
                    console.log('✅ Acesso concedido via userData:', userRole);
                } else {
                    console.log('❌ userData type:', userData?.type, 'não é ADMIN ou FULL');
                }
            } catch (error) {
                console.error('❌ Erro ao carregar userData:', error);
            }
        }
        
        // Se não tem acesso, redireciona
        if (!hasAdminAccess) {
            console.log('❌ Usuário não tem permissão de admin');
            return next({ name: "dashboard" });
        }
    }

    // Verificar se a rota requer permissão de trader
    if (to.meta?.requiresTrader) {
        let hasTraderAccess = false;
        
        // Primeiro tenta verificar via rolesStore
        if (rolesStore.hasAnyRole(['TRADER', 'USER_TRADER', 'FULL'])) {
            hasTraderAccess = true;
        } else if (rolesStore.myRoles.length === 0) {
            // Se myRoles está vazio, tenta carregar
            try {
                console.log('🔍 Buscando permissões de trader...');
                await rolesStore.fetchMyPermissions();
                console.log('✅ Permissões carregadas:', rolesStore.myRoles);
                if (rolesStore.hasAnyRole(['TRADER', 'USER_TRADER', 'FULL'])) {
                    hasTraderAccess = true;
                }
            } catch (error) {
                console.error('❌ Erro ao carregar permissões:', error);
            }
        }
        
        // Se ainda não tem acesso, verifica fallback com userData (localStorage)
        if (!hasTraderAccess) {
            try {
                const storedUser = localStorage.getItem('userData');
                const userData = storedUser ? JSON.parse(storedUser) : null;
                const userRole = (userData?.type || "user").toUpperCase();
                hasTraderAccess = ['TRADER', 'USER_TRADER', 'FULL'].includes(userRole);
                if (hasTraderAccess) {
                    console.log('✅ Acesso concedido via userData:', userRole);
                } else {
                    console.log('❌ userData type:', userData?.type, 'não é TRADER, USER_TRADER ou FULL');
                }
            } catch (error) {
                console.error('❌ Erro ao carregar userData:', error);
            }
        }
        
        // Se não tem acesso, redireciona
        if (!hasTraderAccess) {
            console.log('❌ Usuário não tem permissão de trader');
            return next({ name: "dashboard" });
        }
    }

    next();
}