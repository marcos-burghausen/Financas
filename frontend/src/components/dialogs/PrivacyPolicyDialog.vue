<template>
  <v-dialog v-model="model" max-width="800" scrollable>
    <v-card>
      <v-card-title class="d-flex align-center pa-4 bg-primary">
        <v-icon icon="mdi-shield-lock" class="me-2" />
        <span class="text-h6">Política de Privacidade</span>
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
        <div class="policy-content">
          <p class="text-caption text-medium-emphasis mb-4">
            Última atualização: {{ new Date().toLocaleDateString('pt-BR') }}
          </p>

          <h3 class="text-h6 mb-3">1. Introdução</h3>
          <p class="mb-4">
            Esta Política de Privacidade descreve como o sistema Finanças coleta, usa, armazena e protege 
            suas informações pessoais e financeiras. Ao usar nosso sistema, você concorda com as práticas 
            descritas nesta política.
          </p>

          <h3 class="text-h6 mb-3">2. Informações que Coletamos</h3>
          <p class="mb-4">
            Coletamos as seguintes informações:
          </p>
          
          <h4 class="text-subtitle-1 font-weight-bold mb-2">2.1 Informações de Registro</h4>
          <ul class="mb-3">
            <li>Nome completo</li>
            <li>Endereço de e-mail</li>
            <li>Senha (armazenada de forma criptografada)</li>
            <li>Tipo de conta escolhida</li>
          </ul>

          <h4 class="text-subtitle-1 font-weight-bold mb-2">2.2 Dados Financeiros</h4>
          <ul class="mb-3">
            <li>Informações de contas bancárias (nome, tipo, saldo)</li>
            <li>Detalhes de cartões de crédito (nome, bandeira, limite)</li>
            <li>Registros de receitas e despesas</li>
            <li>Lançamentos fixos e parcelados</li>
            <li>Categorias e descrições de transações</li>
          </ul>

          <h4 class="text-subtitle-1 font-weight-bold mb-2">2.3 Dados de Uso</h4>
          <ul class="mb-4">
            <li>Endereço IP</li>
            <li>Tipo de navegador e dispositivo</li>
            <li>Páginas visitadas e ações realizadas</li>
            <li>Data e hora de acesso</li>
            <li>Preferências de navegação entre meses</li>
          </ul>

          <h3 class="text-h6 mb-3">3. Como Usamos Suas Informações</h3>
          <p class="mb-4">
            Utilizamos suas informações para:
          </p>
          <ul class="mb-4">
            <li>Fornecer, operar e manter o sistema Finanças</li>
            <li>Processar e gerenciar suas transações financeiras</li>
            <li>Enviar notificações importantes sobre sua conta</li>
            <li>Melhorar e personalizar sua experiência no sistema</li>
            <li>Gerar relatórios e análises financeiras personalizadas</li>
            <li>Detectar, prevenir e resolver problemas técnicos</li>
            <li>Cumprir obrigações legais e regulatórias</li>
          </ul>

          <h3 class="text-h6 mb-3">4. Compartilhamento de Informações</h3>
          <p class="mb-4">
            Não vendemos, alugamos ou compartilhamos suas informações pessoais com terceiros, exceto:
          </p>
          <ul class="mb-4">
            <li><strong>Com seu consentimento:</strong> Quando você autoriza explicitamente</li>
            <li><strong>Prestadores de serviços:</strong> Empresas que nos ajudam a operar o sistema (hospedagem, análise)</li>
            <li><strong>Exigências legais:</strong> Quando obrigados por lei, ordem judicial ou processo legal</li>
            <li><strong>Proteção de direitos:</strong> Para proteger nossos direitos, privacidade, segurança ou propriedade</li>
          </ul>

          <h3 class="text-h6 mb-3">5. Segurança de Dados</h3>
          <p class="mb-4">
            Implementamos medidas rigorosas de segurança:
          </p>
          <ul class="mb-4">
            <li><strong>Criptografia:</strong> SSL/TLS para transmissão de dados</li>
            <li><strong>Senhas:</strong> Hash bcrypt com salt para armazenamento seguro</li>
            <li><strong>Autenticação:</strong> Sistema de tokens seguros (Sanctum)</li>
            <li><strong>Acesso restrito:</strong> Apenas pessoal autorizado tem acesso aos servidores</li>
            <li><strong>Backups:</strong> Backups regulares e seguros dos dados</li>
            <li><strong>Monitoramento:</strong> Detecção de atividades suspeitas 24/7</li>
          </ul>

          <h3 class="text-h6 mb-3">6. Seus Direitos</h3>
          <p class="mb-4">
            De acordo com a LGPD (Lei Geral de Proteção de Dados), você tem o direito de:
          </p>
          <ul class="mb-4">
            <li><strong>Acesso:</strong> Solicitar cópias de seus dados pessoais</li>
            <li><strong>Retificação:</strong> Corrigir informações imprecisas ou incompletas</li>
            <li><strong>Exclusão:</strong> Solicitar a exclusão de seus dados</li>
            <li><strong>Portabilidade:</strong> Receber seus dados em formato estruturado</li>
            <li><strong>Revogação:</strong> Retirar consentimento a qualquer momento</li>
            <li><strong>Oposição:</strong> Opor-se a determinado processamento de dados</li>
          </ul>

          <h3 class="text-h6 mb-3">7. Retenção de Dados</h3>
          <p class="mb-4">
            Mantemos suas informações pelo tempo necessário para:
          </p>
          <ul class="mb-4">
            <li>Fornecer nossos serviços enquanto sua conta estiver ativa</li>
            <li>Cumprir obrigações legais (ex: registros fiscais por 5 anos)</li>
            <li>Resolver disputas e fazer cumprir nossos acordos</li>
          </ul>
          <p class="mb-4">
            Após o encerramento da conta, os dados serão deletados ou anonimizados dentro de 90 dias, 
            exceto quando a lei exigir retenção por período maior.
          </p>

          <h3 class="text-h6 mb-3">8. Cookies e Tecnologias Similares</h3>
          <p class="mb-4">
            Utilizamos cookies e tecnologias similares para:
          </p>
          <ul class="mb-4">
            <li>Manter você conectado ao sistema</li>
            <li>Lembrar suas preferências (ex: mês/ano selecionado)</li>
            <li>Analisar como você usa o sistema</li>
            <li>Melhorar o desempenho e a segurança</li>
          </ul>
          <p class="mb-4">
            Você pode configurar seu navegador para recusar cookies, mas isso pode afetar a funcionalidade do sistema.
          </p>

          <h3 class="text-h6 mb-3">9. Links para Sites de Terceiros</h3>
          <p class="mb-4">
            Nosso sistema pode conter links para sites de terceiros. Não somos responsáveis pelas práticas 
            de privacidade desses sites. Recomendamos que você leia as políticas de privacidade de cada site 
            que visitar.
          </p>

          <h3 class="text-h6 mb-3">10. Alterações nesta Política</h3>
          <p class="mb-4">
            Podemos atualizar esta Política de Privacidade periodicamente. Notificaremos você sobre alterações 
            significativas por e-mail ou através de um aviso destacado no sistema. A data de "Última atualização" 
            no topo indica quando a política foi revisada pela última vez.
          </p>

          <h3 class="text-h6 mb-3">11. Contato</h3>
          <p class="mb-4">
            Para questões sobre esta Política de Privacidade ou para exercer seus direitos, entre em contato:
          </p>
          <ul class="mb-4">
            <li><strong>Email:</strong> privacidade@financas.com</li>
            <li><strong>Telefone:</strong> (00) 0000-0000</li>
            <li><strong>Endereço:</strong> Rua Exemplo, 123 - Cidade/UF</li>
            <li><strong>DPO (Encarregado de Dados):</strong> dpo@financas.com</li>
          </ul>

          <h3 class="text-h6 mb-3">12. Consentimento</h3>
          <p class="mb-4">
            Ao usar o sistema Finanças, você consente com a coleta e uso de informações de acordo com 
            esta Política de Privacidade.
          </p>
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
.policy-content {
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
