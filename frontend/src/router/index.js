import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import routes from '@/router/routes.js';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/LoginView.vue')
    },
    {
      path: '/cadastro',
      name: 'cadastro',
      component: () => import('@/views/RegisterView.vue')
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('@/views/DashboardView.vue'),
      meta: {
        auth:true
      }
    },
    // {
    //   path: '/despesas',
    //   name: 'despesas',
    //   component: () => import('@/views/DespesasView.vue'),
    //   meta: {
    //     auth:true
    //   }
    // },
  ]
})

// antes de cada rota vai ser executado...
router.beforeEach(routes);

export default router


// //antes de cada rota
// router.beforeEach(async(to, from, next) => {
//   //se existir o meta para a rota que estou indo
//   if (to.meta?.auth) {
//     const auth = useAuth();
//     //se existir o token e o user
//     if (auth.token && auth.user) {
//       //checar se o token esta autenticado
//       const isAuthenticated = await auth.checkToken();
//       //se estiver autenticado
//       if (isAuthenticated) {
//         //deixa prosseguir
//         next();
//       } else {
//         next({name: 'login'});
//       }
//     //se não existir token e user redirecionar para rota login
//     } else {
//       next({nome: 'login'});
//     }
//     console.log(to.name);
//   } else {
//     next();
//   }
// })

// export default router
