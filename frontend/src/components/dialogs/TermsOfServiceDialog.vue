<template>
  <v-dialog v-model="model" max-width="800" scrollable>
    <v-card>
      <v-card-title class="d-flex align-center pa-4 bg-primary">
        <v-icon icon="mdi-file-document" class="me-2" />
        <span class="text-h6">Termos de Uso</span>
        <v-spacer />
        <v-btn
          icon="mdi-close"
          variant="text"
          size="small"
          @click="model = false"
        />
      </v-card-title>

      <v-divider />

      <v-card-text class="pa-6" style="max-height: 500px;">
        <div class="terms-content">
          <p class="text-caption text-medium-emphasis mb-4">
            Última atualização: {{ new Date().toLocaleDateString('pt-BR') }}
          </p>

          <h3 class="text-h6 mb-3">1. Aceitação dos Termos</h3>
          <p class="mb-4">
            Ao acessar e usar o sistema Finanças, você concorda em cumprir e estar vinculado aos seguintes 
            termos e condições de uso. Se você não concordar com qualquer parte destes termos, não deverá 
            utilizar nosso sistema.
          </p>

          <h3 class="text-h6 mb-3">2. Descrição do Serviço</h3>
          <p class="mb-4">
            O sistema Finanças é uma plataforma de gerenciamento financeiro pessoal que permite aos usuários:
          </p>
          <ul class="mb-4">
            <li>Registrar e acompanhar receitas e despesas</li>
            <li>Gerenciar contas bancárias e cartões de crédito</li>
            <li>Criar e monitorar lançamentos fixos e parcelados</li>
            <li>Visualizar relatórios e análises financeiras</li>
            <li>Navegar entre diferentes períodos (meses/anos)</li>
          </ul>

          <h3 class="text-h6 mb-3">3. Registro e Conta de Usuário</h3>
          <p class="mb-4">
            Para utilizar o sistema, você deve:
          </p>
          <ul class="mb-4">
            <li>Fornecer informações precisas e atualizadas durante o registro</li>
            <li>Manter a confidencialidade de sua senha</li>
            <li>Notificar-nos imediatamente sobre qualquer uso não autorizado de sua conta</li>
            <li>Ser responsável por todas as atividades realizadas em sua conta</li>
          </ul>

          <h3 class="text-h6 mb-3">4. Uso Aceitável</h3>
          <p class="mb-4">
            Você concorda em NÃO:
          </p>
          <ul class="mb-4">
            <li>Usar o sistema para qualquer finalidade ilegal ou não autorizada</li>
            <li>Tentar obter acesso não autorizado ao sistema ou contas de outros usuários</li>
            <li>Interferir ou interromper o funcionamento do sistema</li>
            <li>Copiar, modificar ou distribuir qualquer conteúdo do sistema sem autorização</li>
            <li>Usar o sistema para transmitir vírus, malware ou código malicioso</li>
          </ul>

          <h3 class="text-h6 mb-3">5. Propriedade Intelectual</h3>
          <p class="mb-4">
            Todo o conteúdo, recursos e funcionalidades do sistema (incluindo, mas não limitado a texto, 
            gráficos, logotipos, ícones e código) são propriedade exclusiva do sistema Finanças e estão 
            protegidos por leis de direitos autorais.
          </p>

          <h3 class="text-h6 mb-3">6. Privacidade e Segurança de Dados</h3>
          <p class="mb-4">
            Levamos sua privacidade a sério. Todos os dados financeiros são:
          </p>
          <ul class="mb-4">
            <li>Criptografados durante transmissão e armazenamento</li>
            <li>Acessíveis apenas por você através de autenticação segura</li>
            <li>Nunca compartilhados com terceiros sem seu consentimento</li>
            <li>Utilizados apenas para fornecer e melhorar nossos serviços</li>
          </ul>

          <h3 class="text-h6 mb-3">7. Limitação de Responsabilidade</h3>
          <p class="mb-4">
            O sistema Finanças é fornecido "como está". Não garantimos que:
          </p>
          <ul class="mb-4">
            <li>O serviço estará sempre disponível ou livre de erros</li>
            <li>As informações fornecidas serão sempre precisas ou completas</li>
            <li>O sistema atenderá a todas as suas necessidades específicas</li>
          </ul>
          <p class="mb-4">
            Não nos responsabilizamos por quaisquer perdas ou danos decorrentes do uso do sistema.
          </p>

          <h3 class="text-h6 mb-3">8. Modificações dos Termos</h3>
          <p class="mb-4">
            Reservamos o direito de modificar estes termos a qualquer momento. Alterações significativas 
            serão notificadas por e-mail ou através do sistema. O uso continuado após tais modificações 
            constitui aceitação dos novos termos.
          </p>

          <h3 class="text-h6 mb-3">9. Rescisão</h3>
          <p class="mb-4">
            Podemos suspender ou encerrar sua conta e acesso ao sistema, a qualquer momento, sem aviso prévio, 
            por violação destes termos ou por qualquer outro motivo que considerarmos apropriado.
          </p>

          <h3 class="text-h6 mb-3">10. Contato</h3>
          <p class="mb-4">
            Para questões sobre estes Termos de Uso, entre em contato através de:
          </p>
          <ul class="mb-4">
            <li>Email: suporte@financas.com</li>
            <li>Telefone: (00) 0000-0000</li>
          </ul>
        </div>
      </v-card-text>

      <v-divider />

      <v-card-actions class="pa-4">
        <v-spacer />
        <v-btn
          color="primary"
          variant="flat"
          @click="model = false"
        >
          <v-icon icon="mdi-check" start />
          Entendi
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
  modelValue: boolean
}>()

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
}>()

const model = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})
</script>

<style scoped lang="scss">
.terms-content {
  line-height: 1.6;
  
  h3 {
    margin-top: 1.5rem;
    color: rgb(var(--v-theme-primary));
  }

  h4 {
    margin-top: 1rem;
  }

  p {
    text-align: justify;
    margin-bottom: 1rem;
  }

  ul {
    padding-left: 1.5rem;
    margin-bottom: 1rem;

    li {
      margin-bottom: 0.5rem;
    }
  }

  strong {
    color: rgb(var(--v-theme-primary));
    font-weight: 600;
  }
}
</style>
