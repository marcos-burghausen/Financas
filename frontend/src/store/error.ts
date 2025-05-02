import { defineStore } from "pinia";
import { computed, ref } from "vue";

import type { ApiErrorResponse, ErrorCodes } from "@/types";
import type { AxiosError } from "axios";
import type { Ref } from "vue";

import errorCodes from "@/assets/errorCodes.json";

export const useErrorStore = defineStore("error", () => {
    // state
    const errorCode = ref<ErrorCodes | null>(null);
    const errorsForm: Ref<{ [key: string]: string | string[] } | null> = ref(null);
    const success = ref("");

    // getters
    const errorMessage = computed(() =>
        errorCode.value ? errorCodes[errorCode.value] : ""
    );

    const errorsFormMessage = computed(() => errorsForm.value);
    const successMessage = computed(() => success.value);

    // actions
    function setErrorFromResponse(error: AxiosError<ApiErrorResponse>): void {
        if (!error.response?.data?.error_code) {
            errorCode.value = "SP000";
        } else {
            errorCode.value = error.response.data.error_code || null;
        }
    }

    function setSuccessFromResponse(message: string): void {
        success.value = message;
        console.log(success.value);
    }

    function setErrorFromForm(error: unknown): void {
        const axiosError = error as AxiosError<ApiErrorResponse>;
        if (axiosError.response && axiosError.response.data) {
            errorsForm.value = axiosError.response.data.errors || null;
        } else {
            errorsForm.value = null;
        }
    }

    function setCustomError(code: ErrorCodes): void {
        errorCode.value = code;
    }

    function unsetError(): void {
        errorCode.value = null;
    }

    function unsetSuccess(): void {
        success.value = "";
    }

    function unsetErrorsForm(): void {
        errorsForm.value = null;
    }

    return {
        success,
        errorMessage,
        errorsFormMessage,
        successMessage,
        setErrorFromResponse,
        setSuccessFromResponse,
        setErrorFromForm,
        unsetError,
        unsetSuccess,
        setCustomError,
        unsetErrorsForm,
    };
});