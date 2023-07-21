import './assets/bootstrap.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import mdiVue from 'mdi-vue/v3'
import * as mdijs from '@mdi/js'
import App from './App.vue'
import router from './router'

const app = createApp(App)
app.use(createPinia())
app.use(mdiVue, {
  icons: mdijs
})
app.use(router)
app.mount('#app')
