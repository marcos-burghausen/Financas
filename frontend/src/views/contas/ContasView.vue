<template>
  <div class="content-wrapper">
    <div class="pagetitle">
      <nav class="d-flex justify-content-between mb-3">
        <ol class="breadcrumb bg-transparent">
          <li class="breadcrumb-item text-white">
            <router-link
              class="link"
              :to="{ name: 'dashboard' }"
            >
              Dashboard
            </router-link>
          </li>
          <li
            class="breadcrumb-item opaco text-white"
          >
            Contas
          </li>
        </ol>
        <!-- <button
          v-if="!formStoreExpense && !formEditExpense"
          class="btn btn-danger text-whit"
          @click="formStoreExpense = !formStoreExpense"
        >
          nova despesa
        </button> -->
      </nav>
    </div>
    <div style="display: flex;">
      <div
        class="container__cards"
      >
        <div class="card__new__conta">
          <div class="btn__new__conta">
            <!-- <div class="plus"> -->
            <!-- <mdicon
                name="plus"
                size="35"
              /> -->
            <ModalNovaConta />
            <!-- </div> -->
            <span class="add__conta">Criar conta</span>
          </div> 
        </div>
        <div class="carteira">
          <div class="header__carteira">
            <div class="container__detalhes">
              <span class="icon">
                <mdicon
                  name="cash"
                  size="35"
                />
              </span>
              carteira
            </div>
            <button class="btn__opcoes">
              <mdicon
                name="dots-vertical"
                size="35"
              />
            </button>
          </div>
          <div class="body__carteira">
            <div class="saldo">
              <span class="saldo__atual">saldo atual</span>
              <span class="valor">R$ 130,00</span>
            </div>
            <div class="saldo">
              <span class="saldo__atual">saldo saldo previsto</span>
              <span class="valor">R$ 130,00</span>
            </div>
          </div>
          <div class="footer__carteira">
            <button class="btn__add__despesa">
              ADICIONAR DESPESA
            </button>
          </div>
        </div>
      </div>
      <div class="container__card__atual_previsto">
        <div class="card__valor">
          <span class="saldo__card">Saldo atual</span>
          <span class="valor__card">R$ 130,00</span>
        </div>
        <div class="card__valor">
          <span class="saldo__card">Saldo previsto</span>
          <span class="valor__card">R$ 130,00</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import ModalNovaConta from "@/components/ModalNovaConta.vue";
</script>

<style scoped>
 .link {
      text-decoration: none;
      color: #fefefe;
  }
  .opaco {
      color: #6c757d !important;
  }
  .container__cards {
    width: 66%;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    padding-inline: 15px;
  }
  .card__new__conta {
    height: 248px;
    width: 49%;
    margin-block: 10px;
    border-radius: 15px;
    box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
    display: flex;
    justify-content: center;
    align-items: center;      
  }
  .btn__new__conta {
    background: transparent;
    height: 50%;
    width: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    color: #77d08e;
    font-weight: 100;
  }
  .plus {
    height: 68px;
    width: 68px;
    border: solid 1px #77d08e;
    border-radius: 50px;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .add__conta {
    font-size: 20px;
    font-weight: 400;
    color: #77d08e;
  }
  .carteira {
    box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
    height: 248px;
    width: 49%;
    margin-block: 10px;
    border-radius: 15px;
    display: flex;
    flex-direction: column;
    padding-inline: 10px;
  }
  .header__carteira {
    height: 25%;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .container__detalhes {
    width: 320px;
    height: 52px;
    background: transparent;
    color: #fefefe;
    font-size: 30px;
    padding-left: 15px;
    cursor: pointer;
  }
  .icon {
    color: #77d08e;
  }
  .btn__opcoes {
    height: 52px;
    width: 52px;
    border-radius: 30px;
    color: white;
  }
  .btn__opcoes:hover {
    background-color: rgba(254, 254, 254, 0.1);
  }
  .body__carteira {
    height: 50%;
    display: flex;
    flex-direction: column;
    justify-content: space-around;
    padding-inline: 15px;
  }
  .saldo {
    display: flex;
    justify-content: space-between;

  }
  .saldo__atual {
    font-size: 20px;
    color: #fefefe;
  }
  .valor {
    font-size: 20px;
    color: #06bb64;
  }
  .footer__carteira {
    height: 25%;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .btn__add__despesa {
    color: #77d08e;
    padding: 10px;
    border-radius: 25px;
  }
  .btn__add__despesa:hover {
    background-color: rgba(254, 254, 254, 0.1);
  }
  .container__card__atual_previsto {
    width: 33%;
    height: 248px;
    margin-block: 10px;
    border-radius: 15px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .card__valor {
    box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
    height: 110px;
    width: 100%;
    border-radius: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-inline: 15px;
  }
  .saldo__card {
    color: #fefefe;
    font-size: 25px;
  }
  .valor__card {
    color: #06bb64;
    font-size: 25px;
  }
  </style>