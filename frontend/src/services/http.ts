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
        // Sanctum: Token é uma string simples no localStorage
        const token = localStorage.getItem("sanctum_token");
        
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        
        return config;
    },
    error => {
        return Promise.reject(error);
    }
);

axiosInstance.interceptors.response.use(
    response => {
        return response;
    },
    error => {
        const auth = useAuthStore();
        
        // Sanctum retorna 401 quando token está inválido/expirado
        if (error.response?.status === 401) {
            alert("Sessão expirada, vamos te redirecionar para a tela de login");
            auth.expiredToken();
        }
        
        return Promise.reject(error);
    }
);

export default axiosInstance;