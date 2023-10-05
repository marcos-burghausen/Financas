import { defineStore } from "pinia";
import { computed, reactive, ref } from "vue";

import type { Lancamentos } from "@/types/lancamentos";
import type { LancamentosPorMes } from "@/types/lancamentosPorMes";
import type { ComputedRef, Ref } from "vue";

export const useRevenuesStore = defineStore('revenues', {
    // state -> propriedades reativas
    state: () => ({
        valueTotalRevenuesMonth: 0 as number,
        valueReceivedRevenues: 0 as number,
        valuePendingRevenues: 0 as number,
        revenues: {},
        revenuesMonth: {},
    }),

    // actions -> methods
    actions: {
        setValueTotalRevenuesMonth(value: number) {
            this.valueTotalRevenuesMonth = value;
        },
        setValueReceivedRevenues(value: number) {
            this.valueReceivedRevenues = value;
        },
        setValuePendingRevenues(value: number) {
            this.valuePendingRevenues = value;
        },
        setRevenues(data: object) {
            this.revenues = data
        },
        setRevenuesMonth(valor: object) {
            this.revenuesMonth = valor;
        },
        separateRevenuesByMonth(revenues: object) {
            const despesasPorMes = {};

            // Itere sobre as despesas e organize-as por mês
            revenues.forEach(revenue => {
                const data = new Date(revenue.date);
                const mesAno = `${data.getMonth() + 1}-${data.getFullYear()}`;
                // @ts-expect-error
                if (!despesasPorMes['mesAno']) {
                    // @ts-expect-error
                    despesasPorMes[mesAno] = [];
                }
                // @ts-expect-error
                despesasPorMes[mesAno].push(revenue);
            })
            return despesasPorMes;
        },
        persist: true,
    },


    // export const useRevenuesStore = defineStore("revenues", () => {
    //     // state
    //     let valueTotalRevenuesMonth: Ref<number> = ref(0);
    //     let valueReceivedRevenues: Ref<number> = ref(0);
    //     let valuePendingRevenues: Ref<number> = ref(0);
    //     let revenues = reactive({});
    //     let despesasPorMes: LancamentosPorMes = {};
    //     let revenuesMonth = reactive({});

    //     // getters

    //     // actions
    //     function setValueTotalRevenuesMonth(value: number) {
    //         valueTotalRevenuesMonth.value = value;
    //     }
    //     function setValueReceivedRevenues(value: number) {
    //         valueReceivedRevenues.value = value;
    //     }
    //     function setValuePendingRevenues(value: number) {
    //         valuePendingRevenues.value = value;
    //     }
    //     function setRevenues(value) {
    //         console.log(value);
    //         // revenues.push(value);
    //         revenues = value;
    //     }
    //     function setRevenuesMonth(value: Lancamentos[]) {
    //         revenuesMonth = value;
    //     }
    //     function separateRevenuesByMonth(revenues: Lancamentos) {
    //         // @ts-expect-error
    //         revenues.forEach((revenue: Lancamentos) => {
    //             const data = new Date(revenue.date);
    //             const mesAno = `${data.getMonth() + 1}-${data.getFullYear()}`;

    //             if (!despesasPorMes[mesAno]) {
    //                 despesasPorMes[mesAno] = [];
    //             }

    //             despesasPorMes[mesAno].push(revenue);
    //         });
    //         return despesasPorMes;
    //     }


    //     return {
    //         valueTotalRevenuesMonth,
    //         valueReceivedRevenues,
    //         valuePendingRevenues,
    //         revenues,
    //         revenuesMonth,
    //         setValueTotalRevenuesMonth,
    //         setValueReceivedRevenues,
    //         setValuePendingRevenues,
    //         setRevenues,
    //         setRevenuesMonth,
    //         separateRevenuesByMonth
    //     }
});