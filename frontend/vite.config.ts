import vue from "@vitejs/plugin-vue";
import { fileURLToPath } from "node:url";
import { defineConfig } from "vite";

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [
        vue(),
    ],
    resolve: {
        alias: {
            "@": fileURLToPath(new URL("./src", import.meta.url))
        }
    }
});
// import vue from "@vitejs/plugin-vue";
// import { defineConfig } from "vite";

// export default defineConfig({
//     plugins: [vue()],
//     resolve: {
//         alias: {
//             "@": path.resolve(__dirname, "./src"),
//             // "@": fileURLToPath(new URL("./src", import.meta.url)),
//             // "@components": fileURLToPath(new URL("./src/components", import.meta.url))
//         }
//     },
//     build: {
//         rollupOptions: {
//             external: [], // Adicione aqui módulos que devem ser externalizados
//             output: {
//                 manualChunks: {
//                     // Sua configuração de chunks...
//                 }
//             }
//         }
//     }
// });