<template>
    <div class="containe__mobile">
        <MenuLateralMobile
            :menu-expandido="menuExpandido"
            @expandirMenu="menuExpandido = !menuExpandido"
        />
    
        <CabecalhoMobile
            :menu-expandido="menuExpandido"
            :mes-referencia="mesPorExtenso"
            @expandirMenu="menuExpandido = !menuExpandido"
            @mesAnterior="mesAnterior"
            @proximoMes="proximoMes"
        />
        
        <div class="container__saldo__conta d-flex justify-content-center">
            <div class="me-4 d-flex flex-column align-center justify-content-end">
                    <span class="icon__text">
                    <mdicon name="check-circle-outline" size="18"/>
                    Inicial
                    </span>
                <div style="display:flex; align-items: center;">
                    <span :style="{ color: saldoInicial < 0 ? 'red' : '#757575' }" style="font-size: 13px;">
                        R$ {{ formatValue(saldoInicial) }}
                    </span>
                </div>
            </div>
            <div class=" d-flex flex-column align-center justify-content-end">
                <span class="fs-5" style=" color: #757575;">
                    <mdicon :name="totalBalance < 0 ? 'minus-circle-outline' : totalBalance > 0 ? 'heart-circle' : 'circle-outline'" size="22"/>
                    Saldo
                </span>
                <div style="display:flex; align-items: center;">
                    <!-- <dir style="background: green; width:5px; height: 45px; margin-inline-end: 10px; margin-top: 5px;"></dir> -->
                    <span :style="{ color: totalBalance < 0 ? 'red' : totalBalance > 0 ? 'green' : '#757575' }"  style="font-size: 18px;">
                        R$ {{ formatValue(totalBalance) }}
                    </span>
                </div>
            </div>
            <div class="ms-4 d-flex flex-column align-center justify-content-end">
                <span class="icon__text">
                    <mdicon name="clock-outline" size="20"/>
                    <!-- <mdicon name="plus-circle-outline" />
                    <mdicon name="minus-circle-outline" />
                    <mdicon name="clock-outline" /> -->
                    Previsto
                </span>
                <div style="display:flex; align-items: center;">
                    <span :style="{ color: valorPrevisto < 0 ? 'red' : '#757575' }" style="font-size: 13px;">
                        R$ {{ formatValue(valorPrevisto) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="container__visao__geral">
            <div class="header__visao_geral">
                <span style="text-align: start;">
                    Visão geral
                </span>
                <mdicon
                    name="dots-vertical"
                    class="mdicon"
                    size="25"
                />
            </div>
            <router-link :to="{name: 'receitas'}" style="display:flex; align-items: center; text-decoration: none;">
                <div style="border-inline-start: solid 5px green; margin: 5px 0 5px 0; padding: 0 0 0 10px; display: flex; justify-content: space-between; width: 100%;">
                    <div class="tipo__lancamento">
                        <span class="lancamento">
                            Receitas
                        </span>
                        <span class="previsto">
                            previsto
                        </span>
                    </div>
                    <div class="tipo__lancamento">
                        <span class="lancamento">
                            R$ {{formatValue(valueReceived)}}
                        </span>
                        <span class="valor__previsto">
                            R$ {{ formatValue(valueTotalRevenuesMonth) }}
                        </span>
                    </div>
                </div>
            </router-link>
            <router-link :to="{name: 'despesas'}" style="display:flex; align-items: center; text-decoration: none;">
                <div style="border-inline-start: solid 5px red; margin: 5px 0 5px 0; padding: 0 0 0 10px; display: flex; justify-content: space-between; width: 100%;">
                    <div class="tipo__lancamento">
                        <span class="lancamento">
                            Despesas
                        </span>
                        <span class="previsto">
                            previsto
                        </span>
                    </div>
                    <div class="tipo__lancamento">
                        <span class="lancamento">
                            R$ {{ formatValue(valuePay) }}
                        </span>
                        <span class="valor__previsto">
                            R$ {{ formatValue(valueTotalExpensesMonth) }}
                        </span>
                    </div>
                </div>
            </router-link>
            <!-- <div style="display:flex; align-items: center;">
                <div style="border-inline-start: solid 5px orange; margin: 5px 0 5px 0; padding: 0 0 0 10px; display: flex; justify-content: space-between; width: 100%;">
                    <div class="tipo__lancamento">
                        <span class="lancamento">
                            Despesas no cartão
                        </span>
                        <span class="previsto">
                            previsto
                        </span>
                    </div>
                    <div class="tipo__lancamento">
                        <span class="lancamento">
                            R$ {{receitas}}
                        </span>
                        <span class="previsto">
                            R$ {{receitas}}
                        </span>
                    </div>
                </div>
            </div> -->
        </div>

        <!-- <div class="teste3">
            <span style="text-align: start;">
            Pendências e alertas
            </span>
            <div style="display:flex; align-items: center;">
                <dir style="border-inline-start: solid 5px blue; margin: 5px 0 5px 0; padding: 0 0 0 10px; display: flex; justify-content: space-between; width: 100%;">
                    <div class="cards__lancamentos">
                        <div style="display: flex; flex-direction: column;">
                            <span>
                                Receitas pendentes
                            </span>
                            <span style="font-size: 12px; font-weight: 100;">
                                Total desse mês e dos anteriores
                            </span>
                        </div>
                        <span class="valor">
                            R$ {{ valuePendingRevenues }}
                        </span>
                    </div>
                </dir>
            </div>
            <div style="display:flex; align-items: center;">
                <dir style="border-inline-start: solid 5px orange; margin: 5px 0 5px 0; padding: 0 0 0 10px; display: flex; justify-content: space-between; width: 100%;">
                    <div class="cards__lancamentos">
                        <div style="display: flex; flex-direction: column;">
                            <span>
                                Despesas pendentes
                            </span>
                            <span style="font-size: 12px; font-weight: 100;">
                                Total desse mês e dos anteriores
                            </span>
                        </div>
                        <span>
                            R$ {{ valuePendingExpenses }}
                        </span>
                    </div>
                </dir>
            </div>
            <div style="display:flex; align-items: center;">
                <dir style="border-inline-start: solid 5px yellow; margin: 5px 0 5px 0; padding: 0 0 0 10px; display: flex; justify-content: space-between; width: 100%;">
                    <div class="cards__lancamentos">
                        <div style="display: flex; flex-direction: column;">
                            <span>
                                Faturas de cartão
                            </span>
                            <span style="font-size: 12px; font-weight: 100;">
                                Faturas abertas que vencem esse mês
                            </span>
                        </div>
                        <span>
                            R$ 1.000,00
                        </span>
                    </div>
                </dir>
            </div>
            <div style="display:flex; align-items: center;">
                <dir style="border-inline-start: solid 5px cadetblue; margin: 5px 0 5px 0; padding: 0 0 0 10px; display: flex; justify-content: space-between; width: 100%;">
                    <div class="cards__lancamentos">
                        <div style="display: flex; flex-direction: column;">
                            <span>
                                Saldo
                            </span>
                            <span style="font-size: 12px; font-weight: 100;">
                                Total das contas menos despesas pendentes
                            </span>
                        </div>
                        <span>
                            R$ {{ totalBalance }}
                        </span>
                    </div>
                </dir>
            </div>
        </div> -->
    </div>
</template>

<script setup lang="ts">
import CabecalhoMobile from "@/components/mobile/CabecalhoMobile.vue";
import MenuLateralMobile from "@/components/mobile/MenuLateralMobile.vue";

import { computed, ref } from "vue";

import { useExpensesStore } from "@/store/expenses";
import { useRevenuesStore } from "@/store/revenues";
import { formatValue } from "@/utils/formatValue";
import { useAuthStore } from "@/store/auth.js";
import { useWalletsStore } from "@/store/wallets";
import http from "@/services/http";

const useAuth = useAuthStore();

const useExpenses = useExpensesStore();
const useRevenues = useRevenuesStore();
const useWallets = useWalletsStore();

const menuExpandido = ref(false);


// const formatValue = (value: number): string =>{
//     let valueFormatted = (value / 100).toLocaleString("pt-BR", { style: "decimal", minimumFractionDigits: 2, maximumFractionDigits: 2 });
//     return valueFormatted;
// };
// console.log(mesAnoReferencia.value);
// const mesPorExtenso = mesAnoReferencia ? mesAnoReferencia.value.split(" ")[0] : '';
let mesAnoReferencia = ref(useWallets.walletsData?.mes_ano_referencia);
let saldoInicial = ref(useWallets.walletsData?.saldoInicial);
let valueTotalExpensesMonth = ref(useExpenses.expensesData.expenses?.ValueTotalExpensesMonth);
let valueTotalRevenuesMonth = ref(useRevenues.revenuesData.revenues?.ValueTotalRevenuesMonth);
let totalBalance = ref(useWallets.walletsData?.wallets[0].saldo);
let valorPrevisto = ref(saldoInicial.value + valueTotalRevenuesMonth.value - valueTotalExpensesMonth.value);
let valuePay = ref(useExpenses.expensesData.expenses?.ValuePayExpenses);
let valueReceived = ref(useRevenues.revenuesData.revenues?.ValueReceivedRevenues);
// let totalCreditCard = ref(0);
// let expensesAddTotalValueMonth = ref(useExpenses.expensesData.expenses?.ExpensesAddTotalValueMonth);
// let revenuesAddTotalValueMonth = ref(useRevenues.revenuesData.revenues?.RevenuesAddTotalValueMonth);
let totalByCategoryExpenses = ref(useExpenses.expensesData.expenses?.TotalByCategoryExpenses);
let valuePendingRevenues = ref(useRevenues.revenuesData.revenues?.ValuePendingRevenues);
let valuePendingExpenses = ref(useExpenses.expensesData.expenses?.ValuePendingExpenses);

const isAllZeros = (arr) => {
    return arr.every(value => value === "0,00");
};


const mesPorExtenso = computed(() => {
    if (!mesAnoReferencia.value) return '';

    const [ano, mes] = mesAnoReferencia.value.split("-");

    const mesesPorExtenso = [
        "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
        "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
    ];

    return mesesPorExtenso[parseInt(mes, 10) - 1];
});

const mesAnterior = () => {
    const [ano, mes] = mesAnoReferencia.value.split("-");
    const dataAtual = new Date(ano, mes - 1);
    dataAtual.setMonth(dataAtual.getMonth() - 1);
    mesAnoReferencia.value = `${dataAtual.getFullYear()}-${String(dataAtual.getMonth() + 1).padStart(2, '0')}`;
    buscarDadosMes(mesAnoReferencia.value, 'anterior');
};

const proximoMes = () => {
    const [ano, mes] = mesAnoReferencia.value.split("-");
    const dataAtual = new Date(ano, mes - 1);
    dataAtual.setMonth(dataAtual.getMonth() + 1);
    mesAnoReferencia.value = `${dataAtual.getFullYear()}-${String(dataAtual.getMonth() + 1).padStart(2, '0')}`;
    buscarDadosMes(mesAnoReferencia.value, 'proximo');
};

const buscarDadosMes = async (data: string, buscar: string) => {
    try {
        const res = await http.post('/buscar-dados-mes', {
            'mes': data,
            'buscar': buscar
        });   
        useWallets.setMesReferencia(res.data.walletsData.mes_ano_referencia);
        useExpenses.setExpensesData(res.data.expensesData);
        useRevenues.setRevenuesData(res.data.revenuesData);
        useWallets.setWalletsData(res.data.walletsData);

        mesAnoReferencia.value = res.data.walletsData.mes_ano_referencia;

        saldoInicial.value = res.data.walletsData.saldoInicial;
        totalBalance.value = res.data.walletsData.wallets[0].saldo;

        valueTotalExpensesMonth.value = res.data.expensesData.ValueTotalExpensesMonth;
        valueTotalRevenuesMonth.value = res.data.revenuesData.ValueTotalRevenuesMonth;

        valorPrevisto.value = saldoInicial.value + valueTotalRevenuesMonth.value - valueTotalExpensesMonth.value;
        
        valuePay.value = res.data.expensesData.ValuePayExpenses;
        valueReceived.value = res.data.revenuesData.ValueReceivedRevenues;

        totalByCategoryExpenses.value = res.data.expensesData.TotalByCategoryExpenses;

        valuePendingRevenues.value = res.data.revenuesData.ValuePendingRevenues;
        valuePendingExpenses.value = res.data.expensesData.ValuePendingExpenses;
    } catch (error) {
        // 
    }
};

// =============================== grafico de barras inicio =============================== //

// let totalYearValueExpenses = ref(Object.values(expensesAddTotalValueMonth.value));
// let totalYearValueRevenues = ref(Object.values(revenuesAddTotalValueMonth.value));

// const options = {
//     chart: {
//         id: "vuechart-example",
//         foreColor: "#fefefe"
//     },
//     title: {
//         text: "receitas & despesas",
//         align: "left",
//         style: {
//             color: "#fefefe"
//         }
//     },
//     dataLabels: {
//         enabled: false,
//     },
//     labels: {
//         style: {
//             color: "#fefefe"
//         }
//     },
//     colors: ["#fb0404", "#77d08e"],
//     xaxis: {
//         categories: ["janeiro", "fevereiro", "março", "abril", "maio", "junho", "julho", "agosto", "setembro", "outubro", "novembro", "dezembro"],
//     },
//     tooltip: {
//         y: {
//             formatter: function (val) {
//                 return "R$ " + val;
//             }
//         }, theme: "dark",
//     }
// };
// const series = [
//     {
//         name: "despesas",
//         data: totalYearValueExpenses.value,
//     },
//     {
//         name: "receitas",
//         data: totalYearValueRevenues.value,
//     }
// ];
// =============================== grafico de barras fim =============================== //

// =============================== grafico de pizza inicio =============================== //
// let category = ref(Object.keys(totalByCategoryExpenses.value));
// let valuesCategory = ref(Object.values(totalByCategoryExpenses.value));

// const options1 = {
//     chart: {
//         id: "vuechart-example",
//         foreColor: "#fefefe"
//     },
//     title: {
//         text: "despesas por categoria",
//         align: "center",
//         style: {
//             color: "#fefefe"
//         }
//     },
//     legend: {
//         position: "bottom"
//     },
//     labels: category.value
// };
// const series1 = valuesCategory.value;
// =============================== grafico de pizza fim =============================== //


</script>

<style scoped>
.containe__mobile {
    position: relative;
    height: 100%;
}

.container__saldo__conta {
  color: #fefefe;
  font-size: 20px;
  background: rgba(150, 150, 150, 0.02);
  /* font-weight: bold; */
  padding: 10px;
  margin-top: 10px;
  margin-inline: 10px;
  border-radius: 10px;
}
.icon__text {
      font-size: 15px;
      color: #757575;
    }
.teste2 {
  color: #fefefe;
  font-size: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: rgba(0, 0, 0, 0.1);
  /* font-weight: bold; */
  /* text-align: center; */
  padding-inline: 10px;
  margin-top: 10px;
  /* border: 1px solid #fefefe; */
  height: 60px;
  margin-inline: 10px;
  /* margin-top: -40px; */
  border-radius: 10px;
}
.container__visao__geral {
  color: #fefefe;
  font-size: 20px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  background: rgba(150, 150, 150, 0.03);
  /* align-items: center; */
  /* font-weight: bold; */
  /* text-align: center; */
  padding: 10px;
  margin-top: 10px;
  /* border: 1px solid #fefefe; */
  /* height: 60px; */
  margin-inline: 10px;
  /* margin-top: -40px; */
  border-radius: 10px;
}
.header__visao_geral {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
  color: #BDBDBD;
}
.cards__lancamentos {
  display: flex;
  justify-content: space-between;
  width: 100%;
}

.tipo__lancamento {
  display: flex;
  flex-direction: column;
}
.lancamento {
    font-size: 15px;
    color: #BDBDBD;
}
.previsto {
    font-size: 12px;
    color: #757575;
}
.valor__previsto {
    font-size: 12px;
    color: #757575;
    display: flex;
    justify-content: end;
}
.valor {
    text-align: center;
}
.opaco {
    color: #6c757d !important;
}

.card__container {
    display: flex;
}

.cards {
    width: 33.33%;
    color: #ccc;
    font-size: 30px;
    background-color: rgba(0, 0, 0, 0.1);
}
.chart__container {
    box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
    margin-top: 10px;
    height: calc(100% - 125px);
    padding: 10px 0 0 0;
}


.chart1 {
    background: transparent;
}

.container__charts {
    /* box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23; */
    background-color: rgba(0, 0, 0, 0.1);
    display: flex;
    padding: 0;
    height: 100%;
}

@media screen and (max-width: 600px) {
  .card__container {
    flex-direction: column;
  }
  .cards {
    width: 100%;
  }
    
}
  
@media screen and (max-width: 650px) {
  .container__charts {
      display: flex;
      flex-direction: column;
    }
}
@media screen and (min-width: 651px) {
   .chart1 {
    width: 65%;
   }
   .chart2 {
    width: 35%;
   }
}

@media screen and (min-width: 501px) {
  .mobile {
    display: none;
  }
}
</style>