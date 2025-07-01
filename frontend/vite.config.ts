import vue from "@vitejs/plugin-vue";
import { fileURLToPath, URL } from "node:url";
import { defineConfig, loadEnv } from "vite";

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), "");

  return {
    plugins: [vue()],
    server: {
    host: "0.0.0.0", // Diz ao Vite para escutar em todas as interfaces de rede
    strictPort: true, // Garante que o Vite use a porta que definimos
    proxy: {
        "/api": {
        target: env.VITE_URL, 
        changeOrigin: true,
        }
    }
},
    resolve: {
      alias: {
        "@": fileURLToPath(new URL("./src", import.meta.url)),
        "@services": fileURLToPath(new URL("./src/services", import.meta.url)),
        "@types": fileURLToPath(new URL("./src/types", import.meta.url)),
        "@store": fileURLToPath(new URL("./src/store", import.meta.url))
      }
    }
  };
});