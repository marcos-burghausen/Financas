<template>
  <div>
        <div class="teste">
            <span>
                Saldo de contas
            </span>
            <div style="display:flex; align-items: center;">
                <dir style="background: green; width:5px; height: 45px; margin-inline-end: 10px; margin-top: 5px;"></dir>
                <span>
                    R$ 1.000,00
                </span>
            </div>
        </div>

    <div class="teste2">
        <span>
          Gastos de hoje
        </span>
        <span>
            R$ 1.000,00
        </span>
    </div>
    <div class="teste3">
        <span style="text-align: start;">
          Visão geral do mês
        </span>
        <div style="display:flex; align-items: center;">
            <dir style="border-inline-start: solid 5px green; margin: 5px 0 5px 0; padding: 0 0 0 10px; display: flex; justify-content: space-between; width: 100%;">
                <span>
                    Receitas
                </span>
                <span>
                    R$ 1.000,00
                </span>
            </dir>
        </div>
        <div style="display:flex; align-items: center;">
            <dir style="border-inline-start: solid 5px red; margin: 5px 0 5px 0; padding: 0 0 0 10px; display: flex; justify-content: space-between; width: 100%;">
                <span>
                    Despesas
                </span>
                <span>
                    R$ 1.000,00
                </span>
            </dir>
        </div>
        <div style="display:flex; align-items: center;">
            <dir style="border-inline-start: solid 5px orange; margin: 5px 0 5px 0; padding: 0 0 0 10px; display: flex; justify-content: space-between; width: 100%;">
                <span>
                    Despesas no cartão
                </span>
                <span>
                    R$ 1.000,00
                </span>
            </dir>
        </div>
    </div>

    <div class="teste3">
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
                        R$ 1.000,00
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
                        R$ 1.000,00
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
                        R$ 1.000,00
                    </span>
                </div>
            </dir>
        </div>
    </div>
  </div>
</template>

<script setup lang="ts">

import { ref } from "vue";

import { useExpensesStore } from "@/store/expenses";
import { useRevenuesStore } from "@/store/revenues";
import { formatValue } from "@/utils/formatValue";

const useExpenses = useExpensesStore();
const useRevenues = useRevenuesStore();

// const formatValue = (value: number): string =>{
//     let valueFormatted = (value / 100).toLocaleString("pt-BR", { style: "decimal", minimumFractionDigits: 2, maximumFractionDigits: 2 });
//     return valueFormatted;
// };

let valueTotalExpensesMonth = ref(formatValue(useExpenses.expensesData.expenses?.ValueTotalExpensesMonth));
let valueTotalRevenuesMonth = ref(formatValue(useRevenues.revenuesData.revenues?.ValueTotalRevenuesMonth));
let valuePay = ref(useExpenses.expensesData.expenses?.ValuePayExpenses);
let valueReceived = ref(useRevenues.revenuesData.revenues?.ValueReceivedRevenues);
// let totalBalance = ref(parseFloat(valueReceived.value.replace(",", ".")) - parseFloat(valuePay.value.replace(",", ".")));
let totalBalance = ref(formatValue(valueReceived.value - valuePay.value));
// let totalCreditCard = ref(0);
let expensesAddTotalValueMonth = ref(useExpenses.expensesData.expenses?.ExpensesAddTotalValueMonth);
let revenuesAddTotalValueMonth = ref(useRevenues.revenuesData.revenues?.RevenuesAddTotalValueMonth);
let totalByCategoryExpenses = ref(useExpenses.expensesData.expenses?.TotalByCategoryExpenses);

const isAllZeros = (arr) => {
    return arr.every(value => value === "0,00");
};

// =============================== grafico de barras inicio =============================== //

let totalYearValueExpenses = ref(Object.values(expensesAddTotalValueMonth.value));
let totalYearValueRevenues = ref(Object.values(revenuesAddTotalValueMonth.value));

const options = {
    chart: {
        id: "vuechart-example",
        foreColor: "#fefefe"
    },
    title: {
        text: "receitas & despesas",
        align: "left",
        style: {
            color: "#fefefe"
        }
    },
    dataLabels: {
        enabled: false,
    },
    labels: {
        style: {
            color: "#fefefe"
        }
    },
    colors: ["#fb0404", "#77d08e"],
    xaxis: {
        categories: ["janeiro", "fevereiro", "março", "abril", "maio", "junho", "julho", "agosto", "setembro", "outubro", "novembro", "dezembro"],
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return "R$ " + val;
            }
        }, theme: "dark",
    }
};
const series = [
    {
        name: "despesas",
        data: totalYearValueExpenses.value,
    },
    {
        name: "receitas",
        data: totalYearValueRevenues.value,
    }
];
// =============================== grafico de barras fim =============================== //

// =============================== grafico de pizza inicio =============================== //
let category = ref(Object.keys(totalByCategoryExpenses.value));
let valuesCategory = ref(Object.values(totalByCategoryExpenses.value));

const options1 = {
    chart: {
        id: "vuechart-example",
        foreColor: "#fefefe"
    },
    title: {
        text: "despesas por categoria",
        align: "center",
        style: {
            color: "#fefefe"
        }
    },
    legend: {
        position: "bottom"
    },
    labels: category.value
};
const series1 = valuesCategory.value;
// =============================== grafico de pizza fim =============================== //


</script>

<style scoped>
.teste {
  color: #fefefe;
  font-size: 20px;
  background-color: rgba(0, 0, 0, 0.1);
  /* font-weight: bold; */
  padding: 10px;
  margin-top: 10px;
  /* border: 1px solid #fefefe; */
  /* height: 100px; */
  margin-inline: 10px;
  /* margin-top: -40px; */
  border-radius: 10px;
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
.teste3 {
  color: #fefefe;
  font-size: 20px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  background-color: rgba(0, 0, 0, 0.1);
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
.cards__lancamentos {
  display: flex;
  justify-content: space-between;
  width: 100%;
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
</style>