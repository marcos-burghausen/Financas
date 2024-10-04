<template>
  <div class="no-data-container">
    <img src="../../assets/img/nadaAqui.svg" alt="No Data" class="no-data-image" />
    <p class="no-data-text">Nenhum dado disponível no momento.</p>
  </div>
</template>

<script>
export default {
  name: 'NoDataComponent'
}
</script>

<style scoped>
.no-data-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  text-align: center;
}

.no-data-image {
  max-width: 200px;
  margin-bottom: 20px;
}

.no-data-text {
  font-size: 1.2em;
  color: #6c757d; /* Cor de texto suave */
}
</style>
