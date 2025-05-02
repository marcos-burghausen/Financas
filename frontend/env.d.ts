/// <reference types="vite/client" />
interface ImportMetaEnv {
    readonly VITE_API_URL: string;
    // readonly BASE_URL: string;
    // Adicione outras variáveis de ambiente, se necessário
}

interface ImportMeta {
    readonly env: ImportMetaEnv;
}