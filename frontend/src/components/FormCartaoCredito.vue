<template>
  <div class="container__modal">
    <v-form
      class="w-100"
      @submit.prevent="submitForm"
    >
      <div class="mb-3">
        <label
          for="tipo_conta"
          class="form-label"
        >Tipo de Conta</label>
        <select
          id="tipo_conta"
          v-model="form.tipo_conta"
          class="form-select"
        >
          <option value="Conta Corrente">
            Conta Corrente
          </option>
          <option value="Carteira">
            Carteira
          </option>
          <option value="Poupança">
            Poupança
          </option>
          <option value="Cartão de Crédito">
            Cartão de Crédito
          </option>
        </select>
      </div>

      <div v-if="form.tipo_conta === 'Cartão de Crédito'">
        <div class="mb-3">
          <label
            for="bandeira"
            class="form-label"
          >Bandeira</label>
          <input
            id="bandeira"
            v-model="form.bandeira"
            type="text"
            class="form-control"
          >
        </div>
        <div class="mb-3">
          <label
            for="limite"
            class="form-label"
          >Limite</label>
          <input
            id="limite"
            v-model="form.limite"
            type="number"
            step="0.01"
            class="form-control"
          >
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label
              for="dia_fechamento"
              class="form-label"
            >Dia de Fechamento</label>
            <input
              id="dia_fechamento"
              v-model="form.dia_fechamento"
              type="number"
              class="form-control"
            >
          </div>
          <div class="col-md-6 mb-3">
            <label
              for="dia_vencimento"
              class="form-label"
            >Dia de Vencimento</label>
            <input
              id="dia_vencimento"
              v-model="form.dia_vencimento"
              type="number"
              class="form-control"
            >
          </div>
        </div>
      </div>
    
      <div class="mb-3">
        <label
          for="saldo"
          class="form-label"
        >{{ form.tipo_conta === 'Cartão de Crédito' ? 'Fatura Atual' : 'Saldo Inicial' }}</label>
        <input
          id="saldo"
          v-model="form.saldo"
          type="number"
          step="0.01"
          class="form-control"
        >
      </div>

      <button
        type="submit"
        class="btn btn-primary"
      >
        Salvar
      </button>
    </v-form>
  </div>
  <!-- <ErrorsForm />
  <ErrorMessage /> -->
</template>

<script setup lang="ts">
import { useWalletsStore } from "@/store/wallets";
import { ref } from "vue";

const useWallets = useWalletsStore();
const form = ref({
  name: "",
  tipo_conta: "Conta Corrente", // Valor padrão
  saldo: 0,
  bandeira: "",
  limite: null,
  dia_fechamento: null,
  dia_vencimento: null,
});

const submitForm = async () => {
  // O saldo para o cartão de crédito representa a fatura em aberto
  // e deve ser negativo para ser tratado como despesa.
  if (form.value.tipo_conta === "Cartão de Crédito" && form.value.saldo > 0) {
      form.value.saldo = form.value.saldo * -1;
  }
    
  await useWallets.store(form.value);
  // Lógica para fechar o modal aqui
};
</script>
<style scoped>
.container__modal {
  width: 100%;
  max-width: 600px;
  height: 100%;
  min-height: 100%;
  background: rgb(15, 15, 15);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 10px;
}
.header__items {
  background-color: rgb(15, 15, 15);
  color: #fefefe;
  height: 70px;
}
.close {
  cursor: pointer;
  border-radius: 50%;
  height: 40px;
  width: 40px;
  background-color: transparent;
  color: #fefefe;
  display: flex;
  justify-content: center;
  align-items: center;
}
.btn {
  color: #fff;
  cursor: pointer;
  font-weight: bold;
  align-self: center;
  border: none;
  margin-top: 1rem;
  font-size: 20px;
  background-color: #77d08e;
  border: 1px solid #77d08e;
  transition: background-color 0.5s;
}
.imput {
  height: 40px;
  color: #ccc;
  width: 100%;
}
.tipo {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 999;
  display: flex;
  justify-content: center;
  align-items: center;
}
.modal__tipo {
  background: #2c2c2e;
  color: #fefefe;
  height: 200px;
  border-radius: 20px;
  padding: 15px;
}
.detalhe-parcela {
  display: flex;
  align-items: center;
  /* Remove o justify-content para que o lápis fique ao lado do texto */
  
  color: #b0b0b0; /* Cor mais suave para o texto de detalhe */
  
  /* Ajuste fino na margem para ficar logo abaixo do campo, sem sobrepor */
  margin-top: 2px;
  padding-bottom: 8px; /* Espaçamento abaixo da linha de detalhe */

  /* Alinha o texto com o início do input (após o ícone) */
  margin-left: 40px; 
  font-size: 14px;
  height: 24px; /* Garante altura consistente */
}

.detalhe-parcela .v-icon {
  cursor: pointer;
  color: #77d08e;
}
.selected {
  color: #77d08e;
}
.parcelas {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(12, 12, 12, 0.8);
  z-index: 999;
  display: flex;
  justify-content: center;
  align-items: flex-end;
}
.container__parcelas {
  background: #161616ff;
  width: 100%;
  max-width: 500px;
  border-radius: 15px;
  overflow: hidden;
  color: #fefefe;
  padding-bottom: 20px;
}
.item-label {
  flex-grow: 1;
  font-size: 18px;
  font-weight: 400;
}
.item-value {
  margin-right: 20px;
  font-size: 18px;
  font-weight: 500;
}
.number-stepper {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  width: 120px;
}
.stepper-btn {
  background-color: transparent;
  border: none;
  width: 30px;
  color: #999;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stepper-input {
  width: 50px;
  background-color: transparent;
  border: none;
  color: white;
  text-align: center;
  font-size: 18px;
  -moz-appearance: textfield;
}
.stepper-input::-webkit-outer-spin-button,
.stepper-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
.divider {
  height: 1px;
  background-color: #333;
  margin: 5px 0;
}
.select-dark {
  color: white;
  width: 120px;
  text-align: right;
}
.btn-cancelar {
  color: #77d08e;
  background-color: transparent;
  border-radius: 25px;
  font-size: 16px;
  padding: 0 30px;
  height: 45px;
}
.btn-concluido {
  background-color: #77d08e;
  color: white;
  border-radius: 25px;
  font-size: 16px;
  padding: 0 30px;
  height: 45px;
}
.today__label {
  font-size: 16px;
  color: #77d08e;
  font-weight: 500;
  margin-right: 8px;
}
.form__check__efetivada {
  width: 40px;
  height: 20px;
  border-radius: 15px;
  background-color: rgba(119, 208, 142, 0.4);
  display: flex;
  justify-content: flex-end;
}
.form__check {
  width: 40px;
  height: 20px;
  border-radius: 15px;
  background-color: rgba(255, 255, 255, 0.3);
}
.switch__check__efetivada {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background-color: #77d08e;
}
.switch__check {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background-color: #fefefe;
}
.error__message {
  color: red;
  font-size: 12px;
  margin-top: 4px;
}
h2 {
  font-size: 28px;
  font-weight: 500;
  color: white;
  margin-bottom: 30px;
}
.custom__input__container {
  position: relative;
  padding-top: 20px;
  padding-bottom: 4px; /* Espaço antes da linha de baixo */
}

.custom-input-label {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 4px;
}

.custom-input-content {
  display: flex;
  align-items: center;
  color: #fff;
  cursor: pointer;
}

.detalhe-parcela-interno {
  font-size: 14px;
  color: #e0e0e0;
  line-height: 1.2;
  margin-top: 4px;
}

.edit-icon {
  color: #77d08e;
}

/* Linha de baixo do input */
.custom__underline {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 1px;
  background-color: rgba(255, 255, 255, 0.7);
}

/* ESTILOS NOVOS PARA OS BOTÕES TOGGLE */
.parcela-toggle {
  display: flex;
  border-radius: 10px;
  border: 1px solid #4F4F4F;
  background-color: transparent;
  overflow: hidden;
}

.parcela-toggle .toggle-btn {
  flex: 1;
  text-transform: none;
  font-size: 14px;
  color: #bdbdbd;
  background-color: transparent;
}

.parcela-toggle .v-btn--active {
  background-color: #77d08e;
  color: #121212 !important; /* Cor do texto do botão ativo */
  font-weight: bold;
}

.custom__display__input {
  display: flex;
  align-items: center;
  border-bottom: 1px solid rgba(255, 255, 255, 0.7);
  padding: 8px 0;
  cursor: pointer;
  color: #fff;
}

.modal-recorrente-card {
  background-color: #2c2c2e;
  color: white;
  border-radius: 16px;
}
.modal-option-btn {
  text-transform: none;
  justify-content: start;
  padding: 12px 16px !important;
  min-height: 48px;
  color: #77d08e;
}
</style>
