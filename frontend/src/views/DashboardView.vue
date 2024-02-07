<template>
  <div class="content-wrapper">
    <div class="clearfix" />

    <div class="pagetitle">
      <nav class="d-flex justify-content-between">
        <ol class="breadcrumb bg-transparent m-0">
          <li class="breadcrumb-item opaco">
            Dashboard
          </li>
        </ol>
      </nav>
    </div>
    <div class="card__container">
      <Card
        class="card"
        titulo="Receitas"
        :valor="valueTotalRevenuesMonth"
        rota="receitas"
      />
      <Card
        class="card"
        titulo="Despesas"
        :valor="valueTotalExpensesMonth"
        rota="despesas"
      />
      <!-- <Card class="card col-12 col-md-6 col-lg-6 col-xl-3" titulo="Cartão de crédito" :valor="totalCreditCard"
                    rota="cartao" /> -->
      <Card
        class="card"
        titulo="Saldo atual"
        :valor="totalBalance"
        rota=""
      />
    </div>
    <div class="chart__container">
      <div
        v-if="!isAllZeros(totalYearValueExpenses) || !isAllZeros(totalYearValueRevenues)"
        class="container__charts"
      >
        <div class="col-8 chart__des__rev">
          <apexchart
            width="100%"
            height="353"
            type="bar"
            :options="options"
            :series="series"
          />
        </div>
        <div class="col-4 chart1">
          <apexchart
            width="100%"
            height="353"
            type="pie"
            :options="options1"
            :series="series1"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import Card from "@/components/Card.vue";

import { ref } from "vue";

import { useExpensesStore } from "@/store/expenses";
import { useRevenuesStore } from "@/store/revenues";

const useExpenses = useExpensesStore();
const useRevenues = useRevenuesStore();

let valueTotalExpensesMonth = ref(useExpenses.expensesData.expenses.valueTotalExpensesMonth);
let valueTotalRevenuesMonth = ref(useRevenues.revenuesData.revenues.valueTotalRevenuesMonth);
let valuePay = ref(useExpenses.expensesData.expenses.valuePayExpenses);
let valueReceived = ref(useRevenues.revenuesData.revenues.valueReceivedRevenues);
let totalBalance = ref(valueReceived.value - valuePay.value);
console.log(totalBalance.value);
let totalCreditCard = ref(0);
let expensesAddTotalVelueMonth = ref(useExpenses.expensesData.expenses.expensesAddTotalVelueMonth);
let revenuesAddTotalVelueMonth = ref(useRevenues.revenuesData.revenues.revenuesAddTotalVelueMonth);
let totalByCategoryExpnses = ref(useExpenses.expensesData.expenses.totalByCategoryExpnses);

const isAllZeros = (arr) => {
    return arr.every(value => value === 0);
};


// =============================== grafico de barras inicio =============================== //

let totalYearValueExpenses = ref(Object.values(expensesAddTotalVelueMonth.value));
let totalYearValueRevenues = ref(Object.values(revenuesAddTotalVelueMonth.value));

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
                return "$ " + val;
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
let category = ref(Object.keys(totalByCategoryExpnses.value));
let valuesCategory = ref(Object.values(totalByCategoryExpnses.value));

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
.dashboard {
    width: 100%;
    background: #fb0404;
}

.opaco {
    color: #6c757d !important;
}

.card__container {
    box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
    display: flex;
}

.chart__container {
    box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
    margin-top: 15px;
    height: calc(100% - 125px);
}

.card {
    width: 33.33%;
    color: #ccc;
    font-size: 30px;
    background-color: rgba(0, 0, 0, 0.1);
}

.chart__des__rev {
    background: transparent;
}

.container__charts {
    /* box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23; */
    background-color: rgba(0, 0, 0, 0.1);
    display: flex;
    padding: 0;
    height: 100%;
}

@media screen and (max-width: 1280px) {
    .card {
        width: 33.33%;
    }
}
</style>