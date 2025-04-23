import { defineStore } from "pinia";
import { ref } from "vue";

import type { Lancamentos } from "@/types";

export const useRevenuesStore = defineStore("revenues", () => {

    const revenuesData = ref(
        localStorage.getItem("revenuesData")
            ? JSON.parse(localStorage.getItem("revenuesData") as string)
            : "");

    // function setRevenuesData(revenues: Lancamentos, valueTotalRevenuesMonth: number, valueReceivedRevenues: number, valuePendingRevenues: number, revenuesMonth: Lancamentos): void {
    //     revenuesData.value = {
    //         valueTotalRevenuesMonth,
    //         valueReceivedRevenues,
    //         valuePendingRevenues,
    //         revenuesMonth,
    //         revenues
    //     };
    //     localStorage.setItem('revenuesData', JSON.stringify(revenuesData.value));
    // }

    function setRevenuesData(revenues: Array<Lancamentos>): void {
        revenuesData.value = {
            revenues
        };
        localStorage.setItem("revenuesData", JSON.stringify(revenuesData.value));
    }

    return {
        revenuesData,
        setRevenuesData,
    };
});