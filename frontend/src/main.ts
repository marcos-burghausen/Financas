import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import { VDateInput } from "vuetify/labs/VDateInput";
import "vuetify/styles";
import "./assets/bootstrap.css";
import { defineComponent } from 'vue';

import "@mdi/font/css/materialdesignicons.css";
import * as mdijs from "@mdi/js";
import mdiVue from "mdi-vue/v3";
import { createPinia } from "pinia";
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';
import { createApp } from "vue";
import VueApexCharts from "vue3-apexcharts";
import { aliases, mdi } from "vuetify/iconsets/mdi";
import App from "./App.vue";
import router from "./router";

import "dayjs/locale/pt-br";

const vuetify = createVuetify({
    components: {
        ...components,
        VDateInput,
    },
    directives,
    icons: {
        defaultSet: "mdi",
        aliases,
        sets: {
            mdi,
        },
    },
    locale: {
        locale: "pt-BR",
        fallback: "en",
    },
    theme: {
        defaultTheme: "dark",
        themes: {
            light: {
                colors: {
                    primary: "#1976D2",
                    secondary: "#424242",
                    accent: "#82B1FF",
                    surface: "#E0F7FA",
                    onSurface: "#000000",
                    background: "#FFFFFF",
                    error: "#B00020",
                    info: "#2196F3",
                    variant: "#EEEEEE",
                    success: "#4CAF50",
                    bright: "#FFFFFF",
                    light: "#EEEEEE",
                    warning: "#FB8C00",
                },
            },
            dark: {
                colors: {
                    surface: "#263238",
                    onSurface: "#FFFFFF",
                    primary: "#1E88E5",
                    accent: "#82B1FF",
                    background: "#FFFFFF",
                    error: "#B00020",
                    info: "#2196F3",
                    variant: "#EEEEEE",
                    success: "#4CAF50",
                    bright: "#FFFFFF",
                    light: "#EEEEEE",
                    warning: "#FB8C00",
                },
            },
        },
    },
});

// Cria a instância principal da aplicação
const app = createApp(App);

// Configura o Pinia com o plugin de persistência para salvar o estado
const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);

// Registra todos os plugins na instância da aplicação
app.use(pinia);
app.use(router);
app.use(vuetify);
app.component("apexchart", VueApexCharts);
app.use(mdiVue, {
    icons: mdijs
});

// Monta a aplicação no elemento #app do seu index.html
app.mount("#app");