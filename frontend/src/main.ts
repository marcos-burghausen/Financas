import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import { VDateInput } from "vuetify/labs/VDateInput";
import "vuetify/styles";
import "./assets/bootstrap.css";

import * as mdijs from "@mdi/js";
import mdiVue from "mdi-vue/v3";
import { createPinia } from "pinia";
import { createApp } from "vue";
import VueApexCharts from "vue3-apexcharts";
import "@mdi/font/css/materialdesignicons.css";
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
        locale: "pt-BR", // Set to Portuguese (Brazil)
        fallback: "en", // Fallback to English if needed
    },
    theme: {
        defaultTheme: "dark",
        themes: {
            light: {
                colors: {
                    primary: "#1976D2", // Blue
                    secondary: "#424242",
                    accent: "#82B1FF",
                    surface: "#E0F7FA", // Custom background (e.g., light cyan)
                    onSurface: "#000000", // Text/icon color on surface
                    background:"#FFFFFF",
                    error:"#B00020",
                    info:"#2196F3",
                    variant:"#EEEEEE",

                    success:"#4CAF50",
                    bright:"#FFFFFF",
                    light:"#EEEEEE",
                    warning:"#FB8C00",

                },
            },
            dark: {
                colors: {
                    surface: "#263238", // Dark theme background (optional)
                    onSurface: "#FFFFFF",
                    primary: "#1E88E5", // Green
                    accent: "#82B1FF",
                    // surface: "#E0F7FA",
                    // onSurface: "#000000",
                    background:"#FFFFFF",
                    error:"#B00020",
                    info:"#2196F3",
                    variant:"#EEEEEE",

                    success:"#4CAF50",
                    bright:"#FFFFFF",
                    light:"#EEEEEE",
                    warning:"#FB8C00",
                    
                },
            },
        },
    },
});
  
createApp(App).use(vuetify).use(createPinia()).use(mdiVue, {
    icons: mdijs
}).use(router).use(VueApexCharts).mount("#app");

// const app = createApp(App);
// app.use(createPinia());
// app.use(mdiVue, {
//     icons: mdijs
// });
// app.use(router);
// app.use(VueApexCharts);
// app.mount("#app");
