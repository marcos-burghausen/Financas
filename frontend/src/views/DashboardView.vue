<template>
    <div class="content-wrapper">
        <div class="clearfix"></div>

        <div class="pagetitle">
            <nav class="d-flex justify-content-between">
                <ol class="breadcrumb bg-transparent m-0">
                    <li class="breadcrumb-item opaco">
                        Dashboard
                    </li>
                </ol>
            </nav>
        </div>
        <!-- <div class="container-fluid"> -->
        <!-- <div class="row row-group mt-3 card__container"> -->
        <div class="card__container">
            <Card class="card" titulo="Receitas" :valor="valueTotalRevenuesMonth" rota="receitas" />
            <Card class="card" titulo="Despesas" :valor="valueTotalExpensesMonth" rota="despesas" />
            <!-- <Card class="card col-12 col-md-6 col-lg-6 col-xl-3" titulo="Cartão de crédito" :valor="totalCreditCard"
                    rota="cartao" /> -->
            <Card class="card" titulo="Saldo atual" :valor="totalBalance" rota="despesas" />
        </div>




        <div class="chart__container">


            <div class="container__charts">
                <div class="col-8 chart__des__rev">
                    <apexchart width="100%" height="353" type="bar" :options="options" :series="series">
                    </apexchart>
                </div>
                <div class="col-4 chart1">

                    <apexchart width="100%" height="353" type="pie" :options="options1" :series="series1">
                    </apexchart>
                </div>
            </div>
            <!-- <div class="col-6 p-0">
                    <div class="card">
                        <div class="card-header">Receitas por categoria</div>
                        <div class="card-body">
                            <div class="chart-container-1">
                                <canvas id="Chart2"></canvas>
                            </div>
                        </div>
                    </div>
                </div> -->
            <!-- <div class="col-4 p-0">
                    <div class="card">
                        <div class="card-header">Despesas por categoria</div>
                        <div class="card-body">
                            <div class="chart-container-1">
                                <canvas id="Chart1"></canvas>
                            </div>
                        </div>
                    </div>
                </div> -->
        </div>
        <!--End Row -->

        <!-- </div> -->
        <!--End Row-->

        <!--End Dashboard Content-->

        <!--start overlay-->
        <div class="overlay toggle-menu"></div>
        <!--end overlay-->
    </div>
</template>

<script setup lang="ts">
import Card from "@/components/Card.vue";

import { reactive, ref } from "vue";

import type { Ref } from "vue";

import { useRevenuesStore } from "@/stores/revenues";
import { useUserStore } from "@/stores/user";
import { userData } from "@/stores/data";

const useRevenues = useRevenuesStore();
const data = userData();
const useUser = useUserStore();

let valueTotalExpensesMonth: Ref<number> = ref(0);
let valueTotalRevenuesMonth: Ref<number> = ref(0);
let totalCreditCard: Ref<number> = ref(0);
let totalBalance: Ref<number> = ref(0);

valueTotalExpensesMonth.value = data.valueTotalExpensesMonth;
valueTotalRevenuesMonth.value = useRevenues.valueTotalRevenuesMonth;
totalBalance.value = valueTotalRevenuesMonth.value - valueTotalExpensesMonth.value;
// totalCreditCard = data.getTotalCreditCard;



const options = {
    chart: {
        id: 'vuechart-example',
        foreColor: '#fefefe'
    },
    title: {
        text: 'receitas & despesas',
        align: 'left',
        style: {
            color: '#fefefe'
        }
    },
    dataLabels: {
        enabled: false,
    },
    labels: {
        style: {
            color: '#fefefe'
        }
    },
    colors: ['#fb0404', '#77d08e'],
    xaxis: {
        categories: ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'],
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return "$ " + val
            }
        }, theme: "dark",
    }
}
const series = [
    {
        name: 'despesas',
        data: [30, 40, 45, 50, 49, 60, 70, 91],
    },
    {
        name: 'receitas',
        data: [30, 40, 45, 50, 49, 60, 70, 100]
    }
]
const options1 = {
    chart: {
        id: 'vuechart-example',
        foreColor: '#fefefe'
    },
    title: {
        text: 'receitas & despesas',
        align: 'center',
        style: {
            color: '#fefefe'
        }
    },
    legend: {
        position: 'bottom'
    },
    labels: ['Apple', 'Mango', 'Orange', 'Watermelon']
}
const series1 = [44, 55, 13, 33]

// onMounted(() => {
//   const receitaDespesa = {
//     labels: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
//     datasets: [{
//       label: 'Receitas',
//       data: [65, 59, 80, 81, 196, 55, 40, 69, valueTotalRevenuesMonth],
//       backgroundColor: [
//         '#77d08e',
//       ],
//     },
//     {
//       label: 'Receitas',
//       data: [65, 59, 80, 81, 196, 55, 40, 84, valueTotalExpensesMonth],
//       backgroundColor: [
//         '#fb0404',
//       ],
//     }
//     ]

//   }
//   const configReceitaDespesa = {
//     type: "bar",
//     data: receitaDespesa,
//     options: {
//       scales: {
//         y: {
//           beginAtZero: false
//         }
//       }
//     },
//   }


//   const ctx1 = document.getElementById('Chart1');
//   const Chart1 = new Chart(ctx1, configReceitaDespesa);
//   Chart1;

//   const categoriaReceita = {
//     labels: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
//     datasets: [{
//       label: 'Receitas',
//       data: [65, 59, 80, 81, 196, 55, 40],
//       backgroundColor: [
//         '#77d08e',
//       ],
//     },
//     {
//       label: 'Receitas',
//       data: [65, 59, 80, 81, 196, 55, 40],
//       backgroundColor: [
//         '#fb0404',
//       ],
//     }
//     ]

//   }
//   const configCategoriaReceita = {
//     type: "pie",
//     data: receitaDespesa,
//     options: {
//       responsive: true,
//       plugins: {
//         legend: {
//           position: 'top',
//         },
//         title: {
//           dispĺay: true,
//           text: 'pizza'
//         }
//       }
//     },
//   }


//   const ctx2 = document.getElementById('Chart1');
//   const Chart2 = new Chart(ctx2, configReceitaDespesa);
//   Chart2;
// })

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