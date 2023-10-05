import { computed, ref } from 'vue';
import { defineStore } from 'pinia';

export const useDetectRouter = defineStore('detectRouter', () => {

    let routerCurrent = ref('');

    function setRouterCurrent(router) {
        routerCurrent.value = router;
    }

    const route = computed(() => {
        return routerCurrent.value;
    })

    return {
        setRouterCurrent,
    }

})
