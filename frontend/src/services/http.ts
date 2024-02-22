import axios from "axios";
import { useAuth } from "@/store/auth";

const axiosInstance = axios.create({
    baseURL: "http://localhost:4080/api",
    headers: {
        "Accept": "application/json",
        "Content-type": "application/json"
    }
});


axiosInstance.interceptors.request.use(
    config => {
        const auth = useAuth();
        const token = auth.token;
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        console.log("Interceptando o request antes do envio", config);
        return config;
    },
    error => {
        console.log("Erro na requisição: ", error);
        return Promise.reject(error);
    }
);

axiosInstance.interceptors.response.use(
    response => {
        console.log("Interceptando o response antes da aplicação", response);
        return response;
    },
    error => {
        console.log("Erro na resposta: ", error);
        return Promise.reject(error);
    }
);

export default axiosInstance;