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
import { onMounted, ref } from "vue";
  
const data = {
    labels: ["casa", "transporte", "lazer", "outros"],
    values: [30, 20, 25, 25],
    colors: ["#ff6384", "#36a2eb", "#cc65fe", "#ffce56"]
};
  
const tooltipX = ref(0);
const tooltipY = ref(0);
const showTooltip = ref(false);
let tooltipText = "";
  
onMounted(() => {
    const canvas = document.getElementById("chart");
    const ctx = canvas.getContext("2d");
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;
    const radius = Math.min(canvas.width, canvas.height) / 2;
    
    let startAngle = -Math.PI / 2;
    
    let totalValues = data.values.reduce((acc, cur) => acc + cur, 0);
    
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
});
  
const handleMouseMove = (event: MouseEvent) => {
    const canvas = document.getElementById("chart");
    const rect = canvas.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;
    const distanceFromCenter = Math.sqrt((x - centerX) ** 2 + (y - centerY) ** 2);
    
    if (distanceFromCenter <= canvas.width / 2) {
        const angle = Math.atan2(y - centerY, x - centerX);
        let sliceIndex = Math.floor((angle + Math.PI / 2) / (2 * Math.PI / data.values.length));
        if (sliceIndex < 0) {
            sliceIndex += data.values.length;
        }
        tooltipText = `${data.labels[sliceIndex]}: ${data.values[sliceIndex]}%`;
        tooltipX.value = event.clientX;
        tooltipY.value = event.clientY;
        showTooltip.value = true;
    } else {
        hideTooltip();
    }
};
  
const hideTooltip = () => {
    showTooltip.value = false;
};
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