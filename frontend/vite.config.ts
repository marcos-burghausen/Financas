// import { fileURLToPath, URL } from "node:url";

// import vue from "@vitejs/plugin-vue";
// import { defineConfig } from "vite";

// // https://vitejs.dev/config/
// export default defineConfig({
//     plugins: [
//         vue(),
//     ],
//     resolve: {
//         alias: {
//             "@": fileURLToPath(new URL("./src", import.meta.url))
//         }
//     }
// });
import vue from "@vitejs/plugin-vue";
import { fileURLToPath, URL } from "node:url";
import { defineConfig } from "vite";

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [
        vue({
            template: {
                compilerOptions: {
                    // Opções de compilação do Vue
                }
            }
        }),
    ],
    resolve: {
        alias: {
            "@": fileURLToPath(new URL("./src", import.meta.url)),
            // Adicione outros aliases conforme necessário
        }
    },
    build: {
        outDir: "dist",
        assetsDir: "assets",
        emptyOutDir: true,
        rollupOptions: {
            output: {
                manualChunks: (id) => {
                    if (id.includes("node_modules")) {
                        // Separação de chunks para bibliotecas grandes
                        if (id.includes("vue")) {
                            return "vendor-vue";
                        }
                        if (id.includes("chart.js")) {
                            return "vendor-chartjs";
                        }
                        if (id.includes("lodash")) {
                            return "vendor-lodash";
                        }
                        return "vendor"; // Outras dependências
                    }
                },
                chunkFileNames: "assets/js/[name]-[hash].js",
                entryFileNames: "assets/js/[name]-[hash].js",
                assetFileNames: "assets/[ext]/[name]-[hash].[ext]"
            }
        },
        chunkSizeWarningLimit: 1000, // Aumenta o limite de aviso de tamanho
        minify: "terser", // Minificação mais agressiva
        sourcemap: false // Desativa sourcemaps em produção
    },
    server: {
        port: 3000,
        strictPort: true,
        hmr: {
            overlay: false // Desativa overlay de erros no HMR
        }
    },
    preview: {
        port: 3000,
        strictPort: true
    },
    optimizeDeps: {
        include: [
            "vue",
            "vue-router",
            // Adicione outras dependências que raramente mudam
        ],
        exclude: []
    }
});
