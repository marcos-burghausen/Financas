// File: src/services/http.ts
import { useAuthStore } from "@/store";
import axios from "axios";

const axiosInstance = axios.create({
    baseURL: (import.meta as any).env.VITE_API_URL,
    timeout: 30000,
    headers: {
        "Accept": "application/json",
        "Content-type": "application/json"
    }
});


axiosInstance.interceptors.request.use(
    config => {
        // Tenta buscar o token diretamente do localStorage primeiro
        let token = '';
        
        console.log('=== INTERCEPTOR REQUEST DEBUG ===');
        console.log('URL:', config.url);
        
        try {
            const storedToken = localStorage.getItem("token");
            console.log('storedToken RAW:', storedToken);
            
            if (storedToken) {
                const tokenData = JSON.parse(storedToken);
                console.log('tokenData parsed:', tokenData);
                token = tokenData.token;
                console.log('token extraído:', token);
            } else {
                console.warn('Nenhum token encontrado no localStorage');
            }
        } catch (error) {
            console.error('Erro ao ler token do localStorage:', error);
        }
        
        // Se não encontrou no localStorage, tenta pelo store
        if (!token) {
            console.log('Tentando buscar token pelo store...');
            const useAuth = useAuthStore();
            if (!useAuth.token.token) {
                useAuth.loadFromSession();
            }
            token = useAuth.token.token;
            console.log('Token do store:', token);
        }
        
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
            console.log('Authorization header setado:', config.headers.Authorization);
        } else {
            console.warn('ATENÇÃO: Nenhum token disponível para setar no header!');
        }
        
        console.log('=== FIM DEBUG ===');
        return config;
    },
    error => {
        return Promise.reject(error);
    }
);

axiosInstance.interceptors.response.use(
    response => {
        // console.log("Interceptando o response antes da aplicação", response);
        return response;
    },
    error => {
        // console.error("Erro na resposta:", error.response ? error.response.data : error.message);
        const auth = useAuthStore();
        console.log("Erro na resposta: ", error);
        if (error.response?.data?.message === "Token has expired") {
            alert("sessão expirada, vamos te redirecionar para a tela de login");
            auth.expiredToken();
        }
        return Promise.reject(error);
    }
);

export default axiosInstance;