import axios from "axios";
import { useAuthStore } from "@/store/auth";

const axiosInstance = axios.create({
    baseURL: "http://localhost:4080/api",
    // baseURL: "https://mrfinancas.burghausen.com.br/api",
    headers: {
        "Accept": "application/json",
        "Content-type": "application/json"
    }
});


axiosInstance.interceptors.request.use(
    config => {
        const useAuth = useAuthStore();
        const token = useAuth.token.token;
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        // console.log("Interceptando o request antes do envio", config);
        return config;
    },
    error => {
        // console.log("Erro na requisição: ", error);
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
        // console.log("Erro na resposta: ", error);
        if (error.response.data.message === "Token has expired") {
            alert("sessão expirada, vamos te redirecionar para a tela de login");
            auth.expiredTokem();
        }
        return Promise.reject(error);
    }
);

export default axiosInstance;