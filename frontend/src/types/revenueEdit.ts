import type { Lancamentos } from "@/types/lancamentos";

export interface RevenueEdit extends Lancamentos {
        user_id?: number,
        created_at?: string,
        updated_at?: string,
        status?: string,
        mesReferencia?: string,
}
