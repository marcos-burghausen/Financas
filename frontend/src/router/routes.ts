import { useAuthStore } from "@/store/auth";

export default async function routes(to, from, next) {
    //se existir o meta para a rota que estou indo
    const useAuth = useAuthStore();

    if (to.meta?.auth && !useAuth.isAuthenticated) {
        return next({ name: "home"});
    }

    next();
}