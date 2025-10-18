<template>
  <div class="chart-wrapper">
    <apexchart
      :options="chartOptions"
      :series="series"
      type="bar"
      height="300"
    />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  series: Array<{
    name: string
    data: number[]
  }>
  categories: string[]
  title?: string
  colors?: string[]
  height?: number
  horizontal?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  colors: () => ['#2196F3', '#4caf50'],
  height: 300,
  horizontal: false
})

const chartOptions = computed(() => ({
  chart: {
    type: 'bar',
    toolbar: {
      show: false
    },
    sparkline: {
      enabled: false
    },
    animations: {
      enabled: true,
      speed: 800,
      animateGradually: {
        enabled: true,
        delay: 150
      },
      dynamicAnimation: {
        enabled: true,
        speed: 150
      }
    }
  },
  colors: props.colors,
  plotOptions: {
    bar: {
      horizontal: props.horizontal,
      dataLabels: {
        position: 'top'
      },
      borderRadius: 4
    }
  },
  dataLabels: {
    enabled: false
  },
  xaxis: {
    categories: props.categories,
    labels: {
      style: {
        fontSize: '12px'
      }
    }
  },
  yaxis: {
    labels: {
      style: {
        fontSize: '12px'
      },
      formatter: (value: number) => `R$ ${value.toLocaleString('pt-BR')}`
    }
  },
  tooltip: {
    theme: 'light',
    y: {
      formatter: (value: number) => `R$ ${value.toLocaleString('pt-BR')}`
    }
  },
  grid: {
    borderColor: '#e0e0e0',
    opacity: 0.1
  },
  legend: {
    position: 'top',
    horizontalAlign: 'right'
  }
}))
</script>

<style scoped>
.chart-wrapper {
  width: 100%;
  height: 100%;
}
</style>
