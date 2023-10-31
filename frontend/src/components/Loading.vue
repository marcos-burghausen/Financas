<template>
    <div class="loading">

        <div class="loader"></div>
        <p>Carregando...</p>
    </div>
</template>

<style scoped>
.loading {
    background: rgba(0, 0, 0, 0.50);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #fefefe;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.loader {
    /* Estilize sua animação de carregamento aqui */
    border: 10px solid #f3f3f3;
    border-top: 10px solid #77d08e;
    border-radius: 50%;
    width: 100px;
    height: 100px;
    animation: spin 1s linear infinite;
}

.loading p {
    color: #fefefe;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}
</style>