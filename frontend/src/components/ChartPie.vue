<template>
  <div class="chart-wrapper">
    <apexchart
      :options="chartOptions"
      :series="series"
      type="pie"
      height="300"
    />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  series: number[]
  labels: string[]
  title?: string
  colors?: string[]
  height?: number
}

const props = withDefaults(defineProps<Props>(), {
  colors: () => [
    '#2196F3',
    '#4caf50',
    '#ff9800',
    '#f44336',
    '#9c27b0',
    '#00bcd4'
  ],
  height: 300
})

const chartOptions = computed(() => ({
  chart: {
    type: 'pie',
    toolbar: {
      show: false
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
  labels: props.labels,
  responsive: [
    {
      breakpoint: 600,
      options: {
        legend: {
          position: 'bottom'
        }
      }
    }
  ],
  legend: {
    position: 'bottom',
    horizontalAlign: 'center',
    fontSize: '12px'
  },
  tooltip: {
    theme: 'light',
    y: {
      formatter: (value: number) => {
        const total = props.series.reduce((a, b) => a + b, 0)
        const percentage = ((value / total) * 100).toFixed(1)
        return `${value} (${percentage}%)`
      }
    }
  },
  dataLabels: {
    enabled: true,
    formatter: (value: number) => `${parseFloat(value.toString()).toFixed(1)}%`
  }
}))
</script>

<style scoped>
.chart-wrapper {
  width: 100%;
  height: 100%;
}
</style>
