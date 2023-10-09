import { defineStore } from "pinia";
import { ref } from "vue";

import type { Lancamentos } from "@/types/lancamentos";

export const useRevenuesStore = defineStore("revenues", () => {

    const revenuesData = ref(JSON.parse(localStorage.getItem("revenuesData")));

    function setRevenuesData(revenues: Lancamentos, valueTotalRevenuesMonth: number, valueReceivedRevenues: number, valuePendingRevenues: number, revenuesMonth: Lancamentos): void {
        revenuesData.value = {
            valueTotalRevenuesMonth,
            valueReceivedRevenues,
            valuePendingRevenues,
            revenuesMonth,
            revenues
        };
        localStorage.setItem('revenuesData', JSON.stringify(revenuesData));
    }

    return {
        revenuesData,
        setRevenuesData,
    }
});