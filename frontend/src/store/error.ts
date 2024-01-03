// Utilities
import { defineStore } from "pinia";
import { computed, ref } from "vue";

import type { Ref } from "vue";
import type { AxiosError } from "axios";
import type { ErrorCodes } from "@/types/userData";

import errorCodes from "@/assets/errorCodes.json";

export const useErrorStore = defineStore("error", () => {
    // state
    const errorCode: Ref<ErrorCodes | null> = ref(null);

    // getters
    const errorMessage = computed(() =>
        errorCode.value ? errorCodes[errorCode.value] : ""
    );

    // actions
    function setErrorFromResponse(error: AxiosError): void {
        // @ts-expect-error
        if (!error.response?.data?.error_code) {
            errorCode.value = "SP000";
        } else {
            // @ts-expect-error
            errorCode.value = error.response.data.error_code;
        }
    }

    function setCustomError(code: ErrorCodes): void {
        errorCode.value = code;
    }

    function unsetError(): void {
        errorCode.value = null;
    }

    return { errorMessage, setErrorFromResponse, unsetError, setCustomError };
});
