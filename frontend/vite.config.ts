import vue from "@vitejs/plugin-vue";
import { fileURLToPath } from "node:url";
import path from "path";
import { defineConfig, loadEnv } from "vite";

// https://vitejs.dev/config/
export default defineConfig(({ mode }) => {

    const env = loadEnv(mode, process.cwd(), "");

    return {
        plugins: [vue()],
    server: {
        host: true, // Importante para ser acessível dentro do Docker
        allowedHosts: [
            env.VITE_URL_DEV,
        ],
        proxy: {
            // Qualquer requisição para /api será redirecionada
            "/api": {
                // target: 'http://backend_dev:80',
                target: env.VITE_URL, // 
                changeOrigin: true,
            }
        }
    },
    resolve: {
        alias: {
            "@": fileURLToPath(new URL("./src", import.meta.url)),
            "@services": path.resolve(__dirname, "./src/services"),
            "@types": path.resolve(__dirname, "./src/types"),
            "@store": path.resolve(__dirname, "./src/store")
        }
    }
};
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