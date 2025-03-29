<template>
  <div class="chart-container">
    <canvas
      id="chart"
      class="chart"
      width="300"
      height="300"
      @mousemove="handleMouseMove"
      @mouseleave="hideTooltip"
    />
    <div
      v-if="showTooltip"
      class="tooltip"
      :style="{ top: tooltipY + 'px', left: tooltipX + 'px' }"
    >
      {{ tooltipText }}
    </div>
  </div>
</template>
  
<script setup lang="ts">
import { onMounted, ref, computed } from "vue";
  
const data = {
    labels: ["casa", "transporte", "lazer", "outros"],
    values: [30, 20, 25, 25],
    colors: ["#ff6384", "#36a2eb", "#cc65fe", "#ffce56"]
};
  
const tooltipX = ref(0);
const tooltipY = ref(0);
const showTooltip = ref(false);
const tooltipText = ref("");
const canvasRef = ref<HTMLCanvasElement | null>(null);
  
const drawPieChart = () => {
    if (!canvasRef.value) return;
  
    const ctx = canvasRef.value.getContext("2d");
    if (!ctx) return;

    const centerX = canvasRef.value.width / 2;
    const centerY = canvasRef.value.height / 2;
    const radius = Math.min(canvasRef.value.width, canvasRef.value.height) / 2;
  
    let startAngle = -Math.PI / 2;
    const totalValues = data.values.reduce((acc, cur) => acc + cur, 0);

    // Limpa o canvas antes de redesenhar
    ctx.clearRect(0, 0, canvasRef.value.width, canvasRef.value.height);

    // Desenha cada fatia do gráfico
    for (let i = 0; i < data.values.length; i++) {
        const sliceAngle = (2 * Math.PI * data.values[i]) / totalValues;
        ctx.beginPath();
        ctx.fillStyle = data.colors[i];
        ctx.moveTo(centerX, centerY);
        ctx.arc(centerX, centerY, radius, startAngle, startAngle + sliceAngle);
        ctx.lineTo(centerX, centerY);
        ctx.fill();
        startAngle += sliceAngle;
        ctx.closePath();
    }
};

// Manipula o movimento do mouse para mostrar o tooltip
const handleMouseMove = (event: MouseEvent) => {
    if (!canvasRef.value) return;
  
    const rect = canvasRef.value.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;
    const centerX = canvasRef.value.width / 2;
    const centerY = canvasRef.value.height / 2;
    const distanceFromCenter = Math.sqrt((x - centerX) ** 2 + (y - centerY) ** 2);
  
    if (distanceFromCenter <= radius.value) {
        const angle = Math.atan2(y - centerY, x - centerX);
        let sliceIndex = Math.floor((angle + Math.PI / 2) / (2 * Math.PI / data.values.length));
    
        if (sliceIndex < 0) sliceIndex += data.values.length;
        if (sliceIndex >= data.values.length) sliceIndex = 0;
    
        tooltipText.value = `${data.labels[sliceIndex]}: ${data.values[sliceIndex]}%`;
        tooltipX.value = event.clientX;
        tooltipY.value = event.clientY;
        showTooltip.value = true;
    } else {
        hideTooltip();
    }
};

// Esconde o tooltip
const hideTooltip = () => {
    showTooltip.value = false;
};

// Variável computada para o raio
const radius = computed(() => {
    if (!canvasRef.value) return 0;
    return Math.min(canvasRef.value.width, canvasRef.value.height) / 2;
});

// Inicializa o gráfico quando o componente é montado
onMounted(drawPieChart);
</script>
  
  <style scoped>
  .chart-container {
      width: 300px;
      height: 300px;
      position: relative;
  }
  
  .chart {
      width: 100%;
      height: 100%;
      border-radius: 50%;
      position: absolute;
  }
  
  .tooltip {
      position: absolute;
      padding: 10px;
      background-color: rgba(0, 0, 0, 0.7);
      color: white;
      border-radius: 5px;
      pointer-events: none;
      display: none;
  }
  </style>