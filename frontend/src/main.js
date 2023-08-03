import './assets/bootstrap.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import mdiVue from 'mdi-vue/v3'
import * as mdijs from '@mdi/js'
import App from './App.vue'
import router from './router'
import { useAuth } from "@/stores/auth.js";

const app = createApp(App)
app.use(createPinia())
app.use(mdiVue, {
  icons: mdijs
})
app.use(router)

if (localStorage.getItem('token')) {
  (async() => {
    const auth = useAuth();
    try {
      auth.setIsAuth(true);
      await auth.checkToken();
    } catch (error) {
      auth.setIsAuth(false);
    }
  })
}

app.mount('#app')
