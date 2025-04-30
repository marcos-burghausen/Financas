import type { NavigationGuardNext, RouteLocationNormalized } from "vue-router";


import { useAuthStore } from "@/store/auth";

export default async function routes(
  to: RouteLocationNormalized,
  from: RouteLocationNormalized,
  next: NavigationGuardNext
) {
    //se existir o meta para a rota que estou indo
    const useAuth = useAuthStore();

    if (to.meta?.auth && !useAuth.isAuthenticated) {
        return next({ name: "home"});
    }

    next();
}