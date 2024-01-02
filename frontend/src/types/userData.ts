import errorCodes from "@/assets/errorCodes.json";

export type ErrorCodes = keyof typeof errorCodes;
export interface UserDAta {
    id: number;
    name: string;
    email: string;
    carteiras: [];
    categoriasDespesas: [];
    categoriasReceitas: [];
}
