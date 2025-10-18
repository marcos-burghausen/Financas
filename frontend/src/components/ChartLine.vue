<template>
  <div class="chart-wrapper">
    <apexchart
      :options="chartOptions"
      :series="series"
      type="line"
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
  color?: string
  height?: number
}

const props = withDefaults(defineProps<Props>(), {
  color: '#2196F3',
  height: 300
})

const chartOptions = computed(() => ({
  chart: {
    type: 'line',
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
  colors: [props.color],
  stroke: {
    curve: 'smooth',
    width: 2
  },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.7,
      opacityTo: 0.1,
      stops: [0, 100]
    }
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
      }
    }
  },
  tooltip: {
    theme: 'light',
    x: {
      format: 'dd/MM'
    },
    y: {
      formatter: (value: number) => `R$ ${value.toLocaleString('pt-BR')}`
    }
  },
  grid: {
    borderColor: '#e0e0e0',
    opacity: 0.1
  },
  dataLabels: {
    enabled: false
  }
}))
</script>

<style scoped>
.chart-wrapper {
  width: 100%;
  height: 100%;
}
</style>
