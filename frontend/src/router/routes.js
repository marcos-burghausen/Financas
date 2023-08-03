import { useAuth } from "@/stores/auth.js";

export default async function routes(to, from, next) {
  //se existir o meta para a rota que estou indo
  if (to.meta?.auth) {
    const auth = useAuth();
    if (auth.isAuth) {
      next();
    } else {
      next({ name: "login" });
    }
    console.log(to.name);
  } else {
    next();
  }
}
