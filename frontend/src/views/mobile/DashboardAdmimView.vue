<template>
  <div class="admin-dashboard">
    <div class="header">
      <router-link
        class="link me-7 d-flex align-items-center opaco"
        :to="{ name: 'dashboard' }"
      >
        <mdicon name="arrow-left" size="25" />
      </router-link>
      <div class="header__items">
        <div class="d-flex flex-column">
          <span class="fs-5"> Dashboard admin </span>
        </div>
      </div>
    </div>
    <ol class="dashboard__stats">
      <li class="stat-item">
        <span>Total de usuários:</span>
      </li>
      <li class="stat-item">
        <span>Total de receitas:</span>
      </li>
      <li class="stat-item">
        <span>Total de despesas:</span>
      </li>
    </ol>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import axios from "axios";

// Definição das variáveis reativas
const totalUsuarios = ref(0);
const totalReceitas = ref(0);
const totalDespesas = ref(0);
const usuarios = ref([
  {
    id: 1,
    nome: "Alice Silva",
    email: "alice@example.com",
    saldoAtual: 1500.5,
  },
  {
    id: 2,
    nome: "Bruno Souza",
    email: "bruno@example.com",
    saldoAtual: -200.75,
  },
  {
    id: 3,
    nome: "Carla Pereira",
    email: "carla@example.com",
    saldoAtual: 750.0,
  },
  {
    id: 4,
    nome: "Daniel Santos",
    email: "daniel@example.com",
    saldoAtual: 1200.0,
  },
  {
    id: 5,
    nome: "Eduardo Oliveira",
    email: "eduardo@example.com",
    saldoAtual: 540.0,
  },
  {
    id: 6,
    nome: "Fernanda Lima",
    email: "fernanda@example.com",
    saldoAtual: -50.0,
  },
  {
    id: 7,
    nome: "Gustavo Almeida",
    email: "gustavo@example.com",
    saldoAtual: 3000.0,
  },
  { id: 8, nome: "Helena Costa", email: "helena@example.com", saldoAtual: 0.0 },
  { id: 9, nome: "Igor Ramos", email: "igor@example.com", saldoAtual: 125.25 },
  {
    id: 10,
    nome: "Juliana Mendes",
    email: "juliana@example.com",
    saldoAtual: 2100.3,
  },
]);

// Função para buscar dados do backend
const carregarDados = async () => {
  try {
    const { data } = await axios.get("/api/admin/usuarios");
    usuarios.value = data.usuarios;
    totalUsuarios.value = data.totalUsuarios;
    totalReceitas.value = data.totalReceitas;
    totalDespesas.value = data.totalDespesas;
  } catch (error) {
    console.error("Erro ao carregar dados:", error);
  }
};

// Função para visualizar detalhes do usuário
const verDetalhes = (user: { id: number; nome: string }) => {
  console.log(`Visualizando detalhes do usuário: ${user.nome}`);
  // Aqui você pode redirecionar para outra rota ou abrir um modal
};

// Função para excluir um usuário
const excluirUsuario = async (id: number) => {
  try {
    await axios.delete(`/api/admin/usuarios/${id}`);
    carregarDados(); // Recarrega os dados após exclusão
  } catch (error) {
    console.error("Erro ao excluir usuário:", error);
  }
};

// Carrega os dados ao montar o componente
onMounted(carregarDados);
</script>

<style scoped>
.admin-dashboard {
  padding: 20px;
}
.header {
  display: flex;
  padding: 10px;
  color: #bdbdbd;
}
.link {
  text-decoration: none;
  color: #fefefe;
}
.opaco {
  color: #757575 !important;
}
.header__items {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
}

.dashboard__stats {
  margin-bottom: 20px;
}

.stat-item {
  text-decoration: none;
  background: #333;
  padding: 15px;
  color: #fff;
  border-radius: 8px;
  text-align: center;
}

.user-table {
  width: 100%;
  border-collapse: collapse;
}

.user-table th,
.user-table td {
  padding: 10px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.user-table tr:nth-child(even) {
  background-color: #f9f9f9;
}

.user-table th {
  background: #333;
  color: #fff;
}

button {
  padding: 5px 10px;
  margin-right: 5px;
}
</style>
