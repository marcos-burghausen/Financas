export interface Lancamentos {
    id?: number | null;
    // user_id: number;
    descricao?: string;
    valor?: string;
    tipo?: string;
    num_parcelas?:number;
    periodicidade?: string;
    date?: string;
    data?: Date;
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
