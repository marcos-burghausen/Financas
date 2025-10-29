<template>
  <div class="home-wrapper">
    <!-- Hero Section -->
    <section class="hero-section">
      <v-container class="py-12 py-md-16">
        <v-row class="align-center">
          <v-col cols="12" md="6" class="text-center text-md-start">
            <h1 class="text-h3 text-md-h2 font-weight-bold mb-4">MrFinanças</h1>
            <p class="text-h6 text-medium-emphasis mb-6">
              Gerencie suas finanças com inteligência e simplicidade
            </p>
            <p class="text-body-1 mb-8">
              Uma solução moderna para o controle de finanças pessoais. Organizar receitas, despesas, 
              cartões de crédito e metas financeiras nunca foi tão fácil.
            </p>
            <div class="d-flex gap-4 flex-wrap">
              <v-btn
                to="/login"
                size="large"
                color="primary"
                class="px-8"
              >
                <v-icon icon="mdi-login" start />
                Entrar
              </v-btn>
              <v-btn
                to="/cadastro"
                size="large"
                variant="outlined"
                color="primary"
                class="px-8"
              >
                <v-icon icon="mdi-account-plus" start />
                Criar Conta
              </v-btn>
            </div>
          </v-col>
          <v-col cols="12" md="6" class="text-center">
            <v-icon icon="mdi-finance" :size="$vuetify.display.xs ? 200 : 300" color="primary" />
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Funcionalidades Principais -->
    <section class="features-section pa-12 bg-surface">
      <v-container>
        <div class="text-center mb-12">
          <h2 class="text-h4 font-weight-bold mb-4">Funcionalidades Principais</h2>
          <p class="text-body-1 text-medium-emphasis">Tudo que você precisa para controlar suas finanças</p>
        </div>

        <v-row>
          <!-- Feature 1 -->
          <v-col cols="12" sm="6" md="4" class="mb-8">
            <v-card class="feature-card h-100" elevation="0" variant="outlined">
              <v-card-text class="text-center">
                <v-icon icon="mdi-cash-multiple" :size="64" color="success" class="mb-4" />
                <h3 class="text-h6 font-weight-bold mb-2">Lançamentos Financeiros</h3>
                <p class="text-caption text-medium-emphasis">
                  Registre receitas e despesas de forma organizada e rastreável
                </p>
              </v-card-text>
            </v-card>
          </v-col>

          <!-- Feature 2 -->
          <v-col cols="12" sm="6" md="4" class="mb-8">
            <v-card class="feature-card h-100" elevation="0" variant="outlined">
              <v-card-text class="text-center">
                <v-icon icon="mdi-credit-card" :size="64" color="info" class="mb-4" />
                <h3 class="text-h6 font-weight-bold mb-2">Cartões de Crédito</h3>
                <p class="text-caption text-medium-emphasis">
                  Gerencie múltiplos cartões e acompanhe faturas facilmente
                </p>
              </v-card-text>
            </v-card>
          </v-col>

          <!-- Feature 3 -->
          <v-col cols="12" sm="6" md="4" class="mb-8">
            <v-card class="feature-card h-100" elevation="0" variant="outlined">
              <v-card-text class="text-center">
                <v-icon icon="mdi-chart-box" :size="64" color="warning" class="mb-4" />
                <h3 class="text-h6 font-weight-bold mb-2">Dashboard Analytics</h3>
                <p class="text-caption text-medium-emphasis">
                  Visualize gráficos e relatórios em tempo real
                </p>
              </v-card-text>
            </v-card>
          </v-col>

          <!-- Feature 4 -->
          <v-col cols="12" sm="6" md="4" class="mb-8">
            <v-card class="feature-card h-100" elevation="0" variant="outlined">
              <v-card-text class="text-center">
                <v-icon icon="mdi-repeat" :size="64" color="primary" class="mb-4" />
                <h3 class="text-h6 font-weight-bold mb-2">Transações Recorrentes</h3>
                <p class="text-caption text-medium-emphasis">
                  Configure despesas fixas e receitas periódicas automaticamente
                </p>
              </v-card-text>
            </v-card>
          </v-col>

          <!-- Feature 5 -->
          <v-col cols="12" sm="6" md="4" class="mb-8">
            <v-card class="feature-card h-100" elevation="0" variant="outlined">
              <v-card-text class="text-center">
                <v-icon icon="mdi-tag-multiple" :size="64" color="error" class="mb-4" />
                <h3 class="text-h6 font-weight-bold mb-2">Categorização</h3>
                <p class="text-caption text-medium-emphasis">
                  Organize suas transações com categorias e subcategorias
                </p>
              </v-card-text>
            </v-card>
          </v-col>

          <!-- Feature 6 -->
          <v-col cols="12" sm="6" md="4" class="mb-8">
            <v-card class="feature-card h-100" elevation="0" variant="outlined">
              <v-card-text class="text-center">
                <v-icon icon="mdi-lock" :size="64" color="success" class="mb-4" />
                <h3 class="text-h6 font-weight-bold mb-2">Segurança</h3>
                <p class="text-caption text-medium-emphasis">
                  Seus dados são protegidos com as melhores práticas de segurança
                </p>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Status do Projeto -->
    <section class="status-section pa-12">
      <v-container>
        <div class="text-center mb-12">
          <h2 class="text-h4 font-weight-bold mb-4">Sobre o Projeto</h2>
        </div>

        <v-row>
          <v-col cols="12" md="6">
            <v-card class="pa-8" elevation="0" variant="outlined">
              <h3 class="text-h6 font-weight-bold mb-4">🎯 Objetivo</h3>
              <p class="text-body-2 text-medium-emphasis mb-4">
                Fornecer uma ferramenta abrangente para o controle de finanças pessoais que resolve 
                problemas comuns como falta de visibilidade sobre despesas, dificuldade em planejar 
                orçamentos e acompanhamento de faturas de cartão de crédito.
              </p>
              <p class="text-body-2 text-medium-emphasis">
                Futuras versões incluirão um módulo de controle de investimentos para profissionais 
                do mercado financeiro.
              </p>
            </v-card>
          </v-col>

          <v-col cols="12" md="6">
            <v-card class="pa-8" elevation="0" variant="outlined">
              <h3 class="text-h6 font-weight-bold mb-4">📊 Status Atual</h3>
              <div class="mb-4">
                <v-chip
                  size="small"
                  variant="flat"
                  color="info"
                  text-color="white"
                  class="me-2 mb-2"
                >
                  Versão 0.0.5
                </v-chip>
                <v-chip
                  size="small"
                  variant="flat"
                  color="warning"
                  text-color="white"
                  class="me-2 mb-2"
                >
                  Em Desenvolvimento
                </v-chip>
                <v-chip
                  size="small"
                  variant="flat"
                  color="success"
                  text-color="white"
                >
                  Documentação Completa
                </v-chip>
              </div>
              <p class="text-body-2 text-medium-emphasis">
                O projeto está em desenvolvimento ativo com funcionalidades implementadas e testadas. 
                Acesse a documentação completa para mais detalhes.
              </p>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Tecnologias -->
    <section class="tech-section pa-12 bg-surface">
      <v-container>
        <div class="text-center mb-12">
          <h2 class="text-h4 font-weight-bold mb-4">Tecnologias Utilizadas</h2>
          <p class="text-body-1 text-medium-emphasis">Construído com as melhores ferramentas modernas</p>
        </div>

        <v-row class="justify-center">
          <!-- Frontend -->
          <v-col cols="12" md="6" lg="4">
            <v-card class="tech-card pa-6" elevation="0" variant="outlined">
              <div class="text-center">
                <v-icon icon="mdi-vuetify" :size="48" color="primary" class="mb-3" />
                <h4 class="text-h6 font-weight-bold mb-2">Frontend</h4>
                <div class="d-flex flex-wrap gap-2 justify-center">
                  <v-chip size="small" variant="flat" label>Vue 3</v-chip>
                  <v-chip size="small" variant="flat" label>Vuetify 3</v-chip>
                  <v-chip size="small" variant="flat" label>TypeScript</v-chip>
                  <v-chip size="small" variant="flat" label>Pinia</v-chip>
                  <v-chip size="small" variant="flat" label>ApexCharts</v-chip>
                </div>
              </div>
            </v-card>
          </v-col>

          <!-- Backend -->
          <v-col cols="12" md="6" lg="4">
            <v-card class="tech-card pa-6" elevation="0" variant="outlined">
              <div class="text-center">
                <v-icon icon="mdi-laravel" :size="48" color="error" class="mb-3" />
                <h4 class="text-h6 font-weight-bold mb-2">Backend</h4>
                <div class="d-flex flex-wrap gap-2 justify-center">
                  <v-chip size="small" variant="flat" label>Laravel 10</v-chip>
                  <v-chip size="small" variant="flat" label>Eloquent ORM</v-chip>
                  <v-chip size="small" variant="flat" label>Sanctum</v-chip>
                  <v-chip size="small" variant="flat" label>MySQL</v-chip>
                  <v-chip size="small" variant="flat" label>Redis</v-chip>
                </div>
              </div>
            </v-card>
          </v-col>

          <!-- DevOps -->
          <v-col cols="12" md="6" lg="4">
            <v-card class="tech-card pa-6" elevation="0" variant="outlined">
              <div class="text-center">
                <v-icon icon="mdi-docker" :size="48" color="info" class="mb-3" />
                <h4 class="text-h6 font-weight-bold mb-2">DevOps</h4>
                <div class="d-flex flex-wrap gap-2 justify-center">
                  <v-chip size="small" variant="flat" label>Docker</v-chip>
                  <v-chip size="small" variant="flat" label>Docker Compose</v-chip>
                  <v-chip size="small" variant="flat" label>GitHub</v-chip>
                  <v-chip size="small" variant="flat" label>Vite</v-chip>
                  <v-chip size="small" variant="flat" label>nginx</v-chip>
                </div>
              </div>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Call to Action -->
    <section class="cta-section pa-12">
      <v-container>
        <v-card class="pa-12 bg-primary text-white text-center" elevation="8">
          <h2 class="text-h4 font-weight-bold mb-6">Pronto para começar?</h2>
          <p class="text-body-1 mb-8">
            Crie sua conta agora e comece a controlar suas finanças
          </p>
          <div class="d-flex gap-4 flex-wrap justify-center">
            <v-btn
              to="/cadastro"
              size="large"
              color="white"
              class="px-8"
            >
              <v-icon icon="mdi-account-plus" start />
              Criar Conta Gratuita
            </v-btn>
            <v-btn
              to="/login"
              size="large"
              variant="outlined"
              color="white"
              class="px-8"
            >
              <v-icon icon="mdi-login" start />
              Já tenho conta
            </v-btn>
          </div>
        </v-card>
      </v-container>
    </section>

    <!-- Footer -->
    <footer class="footer-section pa-8 bg-surface text-center">
      <v-container>
        <p class="text-caption text-medium-emphasis mb-2">
          © 2025 MrFinanças. Todos os direitos reservados.
        </p>
        <p class="text-caption text-medium-emphasis mb-4">
          Licença MIT
        </p>
        <div class="d-flex gap-4 justify-center flex-wrap">
          <v-btn
            href="https://github.com/marcos-burghausen/Financas"
            target="_blank"
            icon="mdi-github"
            size="small"
            variant="text"
          />
          <v-btn
            href="https://github.com/marcos-burghausen/Financas/issues"
            target="_blank"
            icon="mdi-bug"
            size="small"
            variant="text"
          />
          <v-btn
            icon="mdi-file-document"
            size="small"
            variant="text"
            @click="scrollToTop"
          />
        </div>
      </v-container>
    </footer>
  </div>
</template>

<script setup lang="ts">
function scrollToTop() {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>

<style scoped lang="scss">
.home-wrapper {
  background: rgb(var(--v-theme-background));
}

.hero-section {
  background: linear-gradient(135deg, rgba(var(--v-theme-primary), 0.1) 0%, rgba(var(--v-theme-primary), 0.05) 100%);
  border-bottom: 1px solid rgba(var(--v-theme-on-background), 0.1);
}

.features-section {
  .feature-card {
    transition: all 0.3s ease;
    border-radius: 12px;

    &:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1) !important;
    }
  }
}

.status-section {
  border-bottom: 1px solid rgba(var(--v-theme-on-background), 0.1);
}

.tech-section {
  .tech-card {
    transition: all 0.3s ease;
    border-radius: 12px;

    &:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1) !important;
    }
  }
}

.cta-section {
  background: linear-gradient(135deg, rgba(var(--v-theme-background), 1) 0%, rgba(var(--v-theme-background), 0.95) 100%);
}

.footer-section {
  border-top: 1px solid rgba(var(--v-theme-on-background), 0.1);
}
</style>
