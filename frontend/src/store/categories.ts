import { defineStore } from "pinia";
import { ref } from "vue";

import type { CategoryData } from "@/types";

export const useCategoriesStore = defineStore("categories", () => {

    const categories = ref<CategoryData>(
        localStorage.getItem("categories")
            ? JSON.parse(localStorage.getItem("categories") as string)
            : "");


    function setCategories(categories: Array<CategoryData>): void {
        categories = {
            ...categories
        };
        localStorage.setItem("categories", JSON.stringify(categories));
    }

    return {
        categories,
        setCategories,
    };
});