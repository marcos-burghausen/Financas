export interface Lancamentos {
    id?: number | null;
    descricao?: string;
    valor?: string;
    tipo?: string;
    numParcelas?:number;
    periodicidade?: string;
    data?: string;
    status?: string;
    categoria?: string;
    carteira?: string;
    subCategoria?: string;
    conta?: string;
    mesReferencia?: string;
    dateLancamento?: string;
    dateEfetivacao?: string;
}
