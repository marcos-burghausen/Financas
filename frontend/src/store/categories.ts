import { defineStore } from "pinia";
import { ref } from "vue";

import type { Lancamentos } from "@/types";

export const useCategoriesStore = defineStore("categories", () => {

    const categories = ref(
        localStorage.getItem("categories")
            ? JSON.parse(localStorage.getItem("categories") as string)
            : "");


    function setCategories(expenses: Array<Lancamentos>): void {
        categories.value = {
            expenses
        };
        localStorage.setItem("categories", JSON.stringify(categories.value));
    }

    return {
        categories,
        setCategories,
    };
});