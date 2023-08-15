import './assets/bootstrap.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import mdiVue from 'mdi-vue/v3'
import * as mdijs from '@mdi/js'
import App from './App.vue'
import router from './router'
import piniaPluginPersistedState from "pinia-plugin-persistedstate"

const app = createApp(App)
const pinia = createPinia();
// app.use(createPinia())
pinia.use(piniaPluginPersistedState)
app.use(pinia)
app.use(mdiVue, {
  icons: mdijs
})
app.use(router)
app.mount('#app')
