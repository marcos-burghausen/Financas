// Utilities
import { defineStore } from "pinia";
import { computed, ref } from "vue";

import type { ErrorCodes } from "@/types";
import type { AxiosError } from "axios";
import type { Ref } from "vue";

import errorCodes from "@/assets/errorCodes.json";

export const useErrorStore = defineStore("error", () => {
    // state
    const errorCode: Ref<ErrorCodes | null> = ref(null);

    const errorsForm = ref(null);
    const success = ref("");

    // getters
    const errorMessage = computed(() =>
        errorCode.value ? errorCodes[errorCode.value] : ""
    );

    const errorsFormMessage = computed(() =>
        errorsForm.value
    );
    const successMessage = computed(() =>
        success.value
    );

    // actions
    function setErrorFromResponse(error: AxiosError): void {
        console.log(error);
        // @ts-expect-error
        if (!error.response?.data?.error_code) {
            errorCode.value = "SP000";
        } else {
            // @ts-expect-error
            errorCode.value = error.response.data.error_code;
        }
        console.log(errorCode.value);
    }

    function setSuccessFromResponse(message: string): void {
        success.value = message;
        console.log(success.value);
    }

    function setErrorFromForm(error): void {
        console.log(error.response.data.errors);
        errorsForm.value = error.response.data.errors;
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
    
    function unsetErrorsForm() {
        // console.log("2");
        errorsForm.value = null;
    }

    return { success, errorMessage, errorsFormMessage, successMessage, setErrorFromResponse, setSuccessFromResponse, setErrorFromForm, unsetError, unsetSuccess, setCustomError, unsetErrorsForm };
});
