<template>
  <div class="clearfix" />

  <div class="content-wrapper">
    <div class="pagetitle">
      <nav class="d-flex justify-content-between mb-3">
        <ol class="breadcrumb bg-transparent">
          <li class="breadcrumb-item text-white">
            Dashboard
          </li>
          <li class="breadcrumb-item text-white">
            cartão de credito
          </li>
          <li class="breadcrumb-item">
            cadastrar de despesa
          </li>
          <li class="breadcrumb-item">
            editar de despesa
          </li>
        </ol>
        <div>
          <button class="btn btn-danger text-whit">
            fatura fechada
          </button>
          <button class="btn btn-danger text-whit">
            fatura aberta
          </button>
          <button class="btn btn-danger text-whit">
            nova despesa
          </button>
        </div>
      </nav>
    </div>





    <div class="container-fluid d-flex">
      <div style="width: 75%; display: flex;">
        <div style="height: 280px; width: 400px; border: 1px solid red;border-radius: 30px; margin-right: 15px;" />
        <div style="height: 280px; width: 400px; border: 1px solid red;border-radius: 30px;" />
      </div>

      <div>
        <div style="height: 95px; width: 380px; border: 1px solid red;border-radius: 20px;" />
        <div
          style="backgrownd: height: 95px; width: 380px; border: 1px solid red;border-radius: 30px; padding: 15px 30px; color: aliceblue;"
        >
          <p>limite disponivel</p>
          <p>R$ 000000,00</p>
        </div>
        <div style="height: 95px; width: 380px;color: aliceblue; padding: 15px 30px;">
          <p>valor total</p>
          <p>R$ 000000,00</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>

</script>
<style scoped></style>