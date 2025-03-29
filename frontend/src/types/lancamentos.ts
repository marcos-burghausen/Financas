export interface Lancamentos {
    id?: number;
    // user_id: number;
    descricao?: string;
    valor?: string;
    tipo?: string;
    numParcelas?:number;
    periodicidade?: string;
    date?: string;
    status?: string;
    categoria?: string;
    carteira?: string;
    subCategoria?: string;
    conta?: string;
    mesReferencia?: string;
    dateLancamento?: string;
    dateEfetivacao?: string;
    // created_at: string;
    // updated_at: string;
}
