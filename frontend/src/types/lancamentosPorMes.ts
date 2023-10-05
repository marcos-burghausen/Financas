import type { Lancamentos } from "@/types/lancamentos";

export interface LancamentosPorMes {
    [mesAno: string]: Lancamentos[];
}
