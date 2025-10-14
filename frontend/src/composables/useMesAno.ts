import { ref, computed } from "vue";
import { useUserStore } from "@/store/user";

export function useMesAno() {
    const useUser = useUserStore();
    
    const mesAno = ref<string>(useUser.mesAno || new Date().toISOString().slice(0, 7));
    
    const mesAnterior = () => {
        const [ano, mes] = mesAno.value.split("-").map(Number);
        const data = new Date(ano, mes - 2, 1); // -2 porque JS começa em 0
        mesAno.value = data.toISOString().slice(0, 7);
        useUser.setMesAno(mesAno.value);
    };
    
    const proximoMes = () => {
        const [ano, mes] = mesAno.value.split("-").map(Number);
        const data = new Date(ano, mes, 1); // mes já é o próximo (JS começa em 0)
        mesAno.value = data.toISOString().slice(0, 7);
        useUser.setMesAno(mesAno.value);
    };
    
    const mesPorExtenso = computed(() => {
        const meses = [
            "Janeiro", "Fevereiro", "Março", "Abril", 
            "Maio", "Junho", "Julho", "Agosto", 
            "Setembro", "Outubro", "Novembro", "Dezembro"
        ];
        const [ano, mes] = mesAno.value.split("-").map(Number);
        return `${meses[mes - 1]} ${ano}`;
    });

    const setMesAno = (novoMesAno: string) => {
        mesAno.value = novoMesAno;
        useUser.setMesAno(novoMesAno);
    };
    
    return { 
        mesAno, 
        mesAnterior, 
        proximoMes, 
        mesPorExtenso,
        setMesAno
    };
}
