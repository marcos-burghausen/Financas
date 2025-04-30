<template>
  <div>
    <!-- class="mobile" -->
    <!-- <DashboardMobile
      :total-balance="totalBalance"
      :total-expenses-day="totalExpensesDay"
      :receitas="valueTotalRevenuesMonth"
      :despesas="valueTotalExpensesMonth"
    /> -->

  <!-- <div class="dashboard web">
    <div class="">
      <nav class="d-flex justify-content-between pb-2">
        <ol class="breadcrumb bg-transparent m-0">
          <li class="text-white">Dashboard</li>
        </ol>
      </nav>
    </div>
    <div class="card__container">
      <Card
        class="cards"
        titulo="Receitas"
        :valor="valueTotalRevenuesMonth"
        rota="receitas"
      />
      <Card
        class="cards"
        titulo="Despesas"
        :valor="valueTotalExpensesMonth"
        rota="despesas"
      /> -->
    <!-- <Card class="card col-12 col-md-6 col-lg-6 col-xl-3" titulo="Cartão de crédito" :valor="totalCreditCard"
                    rota="cartao" /> -->
    <!-- <Card class="cards" titulo="Saldo atual" :valor="totalBalance" rota="" />
    </div>
    <div class="chart__container">
      <div
        v-if="
          !isAllZeros(totalYearValueExpenses) ||
          !isAllZeros(totalYearValueRevenues)
        "
        class="container__charts"
      >
        <div class="chart1">
          <apexchart
            width="100%"
            height="353"
            type="bar"
            :options="options"
            :series="series"
          />
        </div>
        <div v-if="series1" class="chart2">
          <apexchart
            width="100%"
            height="353"
            type="pie"
            :options="options1"
            :series="series1"
          />
        </div>
      </div>
      <h5 v-else class="card-title text-white text-center">
        Você não possui laçamentos a serem exibidos
      </h5>
    </div>
  </div> -->
  </div>
</template>

<script setup lang="ts">
// import DashboardMobile from "./mobile/DashboardMobileView.vue";
// import Card from "../components/Card.vue";

// import { ref } from "vue";

// import { useExpensesStore } from "@/store/expenses";
// import { useRevenuesStore } from "@/store/revenues";
// import { formatValue } from "@/utils/formatValue";

// const useExpenses = useExpensesStore();
// const useRevenues = useRevenuesStore();

// const formatValue = (value: number): string =>{
//     let valueFormatted = (value / 100).toLocaleString("pt-BR", { style: "decimal", minimumFractionDigits: 2, maximumFractionDigits: 2 });
//     return valueFormatted;
// };

// let valueTotalExpensesMonth = ref(
//     formatValue(useExpenses.expensesData.expenses?.ValueTotalExpensesMonth)
// );
// let totalExpensesDay = ref(
//     formatValue(useExpenses.expensesData.expenses?.totalExpensesDay)
// );
// let valueTotalRevenuesMonth = ref(
//     formatValue(useRevenues.revenuesData.revenues?.ValueTotalRevenuesMonth)
// );
// let valuePay = ref(useExpenses.expensesData.expenses?.ValuePayExpenses);
// let valueReceived = ref(
//     useRevenues.revenuesData.revenues?.ValueReceivedRevenues
// );
// // let totalBalance = ref(parseFloat(valueReceived.value.replace(",", ".")) - parseFloat(valuePay.value.replace(",", ".")));
// let totalBalance = ref(formatValue(valueReceived.value - valuePay.value));
// // let totalCreditCard = ref(0);
// let expensesAddTotalValueMonth = ref(
//     useExpenses.expensesData.expenses?.ExpensesAddTotalValueMonth
// );
// let revenuesAddTotalValueMonth = ref(
//     useRevenues.revenuesData.revenues?.RevenuesAddTotalValueMonth
// );
// let totalByCategoryExpenses = ref(
//     useExpenses.expensesData.expenses?.TotalByCategoryExpenses
// );

// const isAllZeros = (arr) => {
//     return arr.every((value) => value === "0,00");
// };

// // =============================== grafico de barras inicio =============================== //

// let totalYearValueExpenses = ref(
//     Object.values(expensesAddTotalValueMonth.value)
// );
// let totalYearValueRevenues = ref(
//     Object.values(revenuesAddTotalValueMonth.value)
// );

// const options = {
//     chart: {
//         id: "vuechart-example",
//         foreColor: "#fefefe",
//     },
//     title: {
//         text: "receitas & despesas",
//         align: "left",
//         style: {
//             color: "#fefefe",
//         },
//     },
//     dataLabels: {
//         enabled: false,
//     },
//     labels: {
//         style: {
//             color: "#fefefe",
//         },
//     },
//     colors: ["#fb0404", "#77d08e"],
//     xaxis: {
//         categories: [
//             "janeiro",
//             "fevereiro",
//             "março",
//             "abril",
//             "maio",
//             "junho",
//             "julho",
//             "agosto",
//             "setembro",
//             "outubro",
//             "novembro",
//             "dezembro",
//         ],
//     },
//     tooltip: {
//         y: {
//             formatter: function (val) {
//                 return "R$ " + val;
//             },
//         },
//         theme: "dark",
//     },
// };
// const series = [
//     {
//         name: "despesas",
//         data: totalYearValueExpenses.value,
//     },
//     {
//         name: "receitas",
//         data: totalYearValueRevenues.value,
//     },
// ];
// // =============================== grafico de barras fim =============================== //

// // =============================== grafico de pizza inicio =============================== //
// let category = ref(Object.keys(totalByCategoryExpenses.value));
// let valuesCategory = ref(Object.values(totalByCategoryExpenses.value));

// const options1 = {
//     chart: {
//         id: "vuechart-example",
//         foreColor: "#fefefe",
//     },
//     title: {
//         text: "despesas por categoria",
//         align: "center",
//         style: {
//             color: "#fefefe",
//         },
//     },
//     legend: {
//         position: "bottom",
//     },
//     labels: category.value,
// };
// const series1 = valuesCategory.value;
// =============================== grafico de pizza fim =============================== //
</script>

<style scoped>

.dashboard {
  /* padding-inline: 10px; */
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

@media screen and (max-width: 500px) {
  .web {
    display: none;
  }
}
</style>
