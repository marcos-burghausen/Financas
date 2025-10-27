<template>
  <div class="cadastro-wrapper">
    <v-container fluid class="cadastro-container">
      <v-row class="align-center justify-center">
        <v-col cols="12" sm="10" md="8" lg="6">
          <!-- Card Principal -->
          <v-card elevation="8" rounded="lg" class="cadastro-card">
            <!-- Logo Section -->
            <div class="logo-section text-center pa-8 bg-success">
              <div class="mb-4">
                <v-icon icon="mdi-account-plus" size="64" color="white" />
              </div>
              <h1 class="text-h4 text-white font-weight-bold mb-2">Criar Conta</h1>
              <p class="text-white text-opacity-70">Junte-se à nossa comunidade de financas</p>
            </div>

            <!-- Formulário -->
            <v-card-text class="pa-8">
              <p class="text-subtitle-2 text-medium-emphasis mb-6">
                Preencha os dados abaixo para criar sua conta
              </p>

              <v-form ref="form" @submit.prevent="handleCadastro">
                <!-- Nome Completo -->
                <v-text-field
                  v-model="formData.nome"
                  label="Nome Completo"
                  prepend-inner-icon="mdi-account"
                  variant="outlined"
                  density="compact"
                  :rules="[
                    v => !!v || 'Nome é obrigatório',
                    v => v.length >= 3 || 'Nome deve ter pelo menos 3 caracteres'
                  ]"
                  class="mb-4"
                />

                <!-- Email -->
                <v-text-field
                  v-model="formData.email"
                  label="Email"
                  type="email"
                  prepend-inner-icon="mdi-email"
                  variant="outlined"
                  density="compact"
                  :rules="[
                    v => !!v || 'Email é obrigatório',
                    v => /.+@.+\..+/.test(v) || 'Email deve ser válido'
                  ]"
                  class="mb-4"
                />

                <!-- Senha - Container com posição relativa -->
                <div class="password-field-container">
                  <v-text-field
                    v-model="formData.password"
                    label="Senha"
                    :type="showPassword ? 'text' : 'password'"
                    prepend-inner-icon="mdi-lock"
                    :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                    @click:append-inner="showPassword = !showPassword"
                    variant="outlined"
                    density="compact"
                    :rules="[passwordValidationRule]"
                    hint="Mínimo 8 caracteres, incluindo letra maiúscula, minúscula, número e símbolo"
                    class="mb-4"
                  />

                  <!-- Password Requirements Indicator - Overlay -->
                  <transition name="slide-fade">
                    <v-card
                      v-if="showPasswordRequirements"
                      variant="outlined"
                      class="password-requirements-card pa-3"
                      elevation="8"
                    >
                      <div class="text-caption text-medium-emphasis mb-2 font-weight-bold">
                        Requisitos da senha:
                      </div>
                      
                      <v-list density="compact" class="pa-0">
                        <!-- Mínimo 8 caracteres -->
                        <v-list-item class="pa-1 px-0">
                          <template #prepend>
                            <v-icon
                              :icon="passwordRequirements.minLength ? 'mdi-check-circle' : 'mdi-close-circle'"
                              :color="passwordRequirements.minLength ? 'success' : 'error'"
                              size="small"
                              class="me-2"
                            />
                          </template>
                          <v-list-item-title class="text-caption">
                            Mínimo 8 caracteres
                          </v-list-item-title>
                        </v-list-item>

                        <!-- Letra maiúscula -->
                        <v-list-item class="pa-1 px-0">
                          <template #prepend>
                            <v-icon
                              :icon="passwordRequirements.hasUpperCase ? 'mdi-check-circle' : 'mdi-close-circle'"
                              :color="passwordRequirements.hasUpperCase ? 'success' : 'error'"
                              size="small"
                              class="me-2"
                            />
                          </template>
                          <v-list-item-title class="text-caption">
                            Letra maiúscula (A-Z)
                          </v-list-item-title>
                        </v-list-item>

                        <!-- Letra minúscula -->
                        <v-list-item class="pa-1 px-0">
                          <template #prepend>
                            <v-icon
                              :icon="passwordRequirements.hasLowerCase ? 'mdi-check-circle' : 'mdi-close-circle'"
                              :color="passwordRequirements.hasLowerCase ? 'success' : 'error'"
                              size="small"
                              class="me-2"
                            />
                          </template>
                          <v-list-item-title class="text-caption">
                            Letra minúscula (a-z)
                          </v-list-item-title>
                        </v-list-item>

                        <!-- Número -->
                        <v-list-item class="pa-1 px-0">
                          <template #prepend>
                            <v-icon
                              :icon="passwordRequirements.hasNumber ? 'mdi-check-circle' : 'mdi-close-circle'"
                              :color="passwordRequirements.hasNumber ? 'success' : 'error'"
                              size="small"
                              class="me-2"
                            />
                          </template>
                          <v-list-item-title class="text-caption">
                            Número (0-9)
                          </v-list-item-title>
                        </v-list-item>

                        <!-- Caractere especial -->
                        <v-list-item class="pa-1 px-0">
                          <template #prepend>
                            <v-icon
                              :icon="passwordRequirements.hasSpecialChar ? 'mdi-check-circle' : 'mdi-close-circle'"
                              :color="passwordRequirements.hasSpecialChar ? 'success' : 'error'"
                              size="small"
                              class="me-2"
                            />
                          </template>
                          <v-list-item-title class="text-caption">
                            Caractere especial (!@#$...)
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>

                      <!-- Password Strength Indicator -->
                      <div class="mt-3">
                        <div class="d-flex justify-space-between align-center mb-1">
                          <span class="text-caption text-medium-emphasis">Força:</span>
                          <span class="text-caption font-weight-bold" :class="passwordStrength.class">
                            {{ passwordStrength.text }}
                          </span>
                        </div>
                        <v-progress-linear
                          :model-value="passwordStrength.value"
                          :color="passwordStrength.color"
                          height="4"
                          rounded
                        />
                      </div>
                    </v-card>
                  </transition>
                </div>

                <!-- Confirmar Senha -->
                <v-text-field
                  v-model="formData.confirmPassword"
                  label="Confirmar Senha"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  prepend-inner-icon="mdi-lock-check"
                  :append-inner-icon="showConfirmPassword ? 'mdi-eye-off' : 'mdi-eye'"
                  @click:append-inner="showConfirmPassword = !showConfirmPassword"
                  variant="outlined"
                  density="compact"
                  :rules="[
                    v => !!v || 'Confirmação de senha é obrigatória',
                    v => v === formData.password || 'Senhas não correspondem'
                  ]"
                  class="mb-4"
                />

                <!-- Tipo de Conta -->
                <!-- <v-select
                  v-model="formData.tipo"
                  :items="tiposAccount"
                  label="Tipo de Conta"
                  prepend-inner-icon="mdi-account-convert"
                  variant="outlined"
                  density="compact"
                  :rules="[v => !!v || 'Tipo de conta é obrigatório']"
                  class="mb-6"
                /> -->

                <!-- Termos de Uso -->
                <v-checkbox
                  v-model="formData.termos"
                  :rules="[v => !!v || 'Você deve aceitar os termos']"
                  class="mb-6"
                >
                  <template #label>
                    <span class="text-caption">
                      Eu concordo com os
                      <v-btn 
                        variant="text" 
                        size="x-small" 
                        color="primary"
                        @click.stop="dialogTermos = true"
                      >
                        Termos de Uso
                      </v-btn>
                      e
                      <v-btn 
                        variant="text" 
                        size="x-small" 
                        color="primary"
                        @click.stop="dialogPrivacidade = true"
                      >
                        Política de Privacidade
                      </v-btn>
                    </span>
                  </template>
                </v-checkbox>

                <!-- Botão Cadastro -->
                <v-btn
                  type="submit"
                  color="success"
                  size="large"
                  block
                  class="mb-4"
                  :loading="loading"
                >
                  <v-icon icon="mdi-check-circle" start />
                  Criar Conta
                </v-btn>
              </v-form>

              <!-- Divider -->
              <div class="d-flex align-center gap-2 my-6">
                <v-divider />
                <span class="text-caption text-medium-emphasis">ou</span>
                <v-divider />
              </div>

              <!-- Botão Voltar -->
              <v-btn
                to="/login"
                variant="outlined"
                color="primary"
                size="large"
                block
              >
                <v-icon icon="mdi-login" start />
                Já tenho conta - Entrar
              </v-btn>
            </v-card-text>

            <!-- Footer -->
            <!-- <v-divider />
            <v-card-text class="text-center text-caption text-medium-emphasis pa-4">
              Protegemos seus dados com criptografia de ponta a ponta
            </v-card-text> -->
          </v-card>

          <!-- Benefícios -->
          <v-row class="mt-6 text-center text-white">
            <v-col cols="12" sm="4">
              <div class="d-flex flex-column align-center">
                <v-icon icon="mdi-shield-check" size="32" class="mb-2" />
                <span class="text-caption font-weight-bold">Seguro</span>
              </div>
            </v-col>
            <v-col cols="12" sm="4">
              <div class="d-flex flex-column align-center">
                <v-icon icon="mdi-lightning-bolt" size="32" class="mb-2" />
                <span class="text-caption font-weight-bold">Rápido</span>
              </div>
            </v-col>
            <v-col cols="12" sm="4">
              <div class="d-flex flex-column align-center">
                <v-icon icon="mdi-chart-line" size="32" class="mb-2" />
                <span class="text-caption font-weight-bold">Eficiente</span>
              </div>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-container>

    <!-- Dialog Termos de Uso -->
    <v-dialog v-model="dialogTermos" max-width="800" scrollable>
      <v-card>
        <v-card-title class="d-flex align-center pa-4 bg-primary">
          <v-icon icon="mdi-file-document" class="me-2" />
          <span class="text-h6">Termos de Uso</span>
          <v-spacer />
          <v-btn
            icon="mdi-close"
            variant="text"
            size="small"
            @click="dialogTermos = false"
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
            @click="dialogTermos = false"
          >
            <v-icon icon="mdi-check" start />
            Entendi
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Dialog Política de Privacidade -->
    <v-dialog v-model="dialogPrivacidade" max-width="800" scrollable>
      <v-card>
        <v-card-title class="d-flex align-center pa-4 bg-primary">
          <v-icon icon="mdi-shield-lock" class="me-2" />
          <span class="text-h6">Política de Privacidade</span>
          <v-spacer />
          <v-btn
            icon="mdi-close"
            variant="text"
            size="small"
            @click="dialogPrivacidade = false"
          />
        </v-card-title>

        <v-divider />

        <v-card-text class="pa-6" style="max-height: 500px;">
          <div class="terms-content">
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
            @click="dialogPrivacidade = false"
          >
            <v-icon icon="mdi-check" start />
            Entendi
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Loading Overlay - Redirecionamento -->
    <v-overlay
      v-model="redirecting"
      class="align-center justify-center"
      persistent
      contained
    >
      <div class="text-center">
        <v-progress-circular
          indeterminate
          size="80"
          width="6"
          color="success"
          class="mb-6"
        />
        <div class="text-h6 text-white mb-2">
          Preparando seu painel...
        </div>
        <div class="text-caption text-white-50">
          Você será redirecionado em instantes
        </div>
      </div>
    </v-overlay>
  </div>
</template>

<script setup lang="ts">
import authService, { type RegisterRequest } from '@/services/auth.service'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/toast'
import { useUserStore } from '@/store/user'
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const authStore = useAuthStore()
const userStore = useUserStore()
const toastStore = useToastStore()

const formData = ref({
  nome: '',
  email: '',
  password: '',
  confirmPassword: '',
  termos: false
})

const showPassword = ref(false)
const showConfirmPassword = ref(false)
const loading = ref(false)
const redirecting = ref(false)
const form = ref()
const validationErrors = ref<Record<string, string[]>>({})
const dialogTermos = ref(false)
const dialogPrivacidade = ref(false)

const tiposAccount = [
  { title: 'Usuário Comum', value: 'USER' },
  { title: 'Trader', value: 'TRADER' },
  { title: 'Usuário + Trader', value: 'USER_TRADER' }
]

// Password Requirements
const passwordRequirements = computed(() => {
  const password = formData.value.password
  return {
    minLength: password.length >= 8,
    hasUpperCase: /[A-Z]/.test(password),
    hasLowerCase: /[a-z]/.test(password),
    hasNumber: /\d/.test(password),
    hasSpecialChar: /[^a-zA-Z0-9]/.test(password)
  }
})

// Verifica se todos os requisitos foram atendidos
const allRequirementsMet = computed(() => {
  const reqs = passwordRequirements.value
  return reqs.minLength && reqs.hasUpperCase && reqs.hasLowerCase && reqs.hasNumber && reqs.hasSpecialChar
})

// Controla quando o card de requisitos deve aparecer
const showPasswordRequirements = computed(() => {
  return formData.value.password.length > 0 && !allRequirementsMet.value
})

// Password Strength
const passwordStrength = computed(() => {
  const password = formData.value.password
  if (!password) return { value: 0, color: 'grey', text: '', class: '' }

  const reqs = passwordRequirements.value
  let strength = 0
  
  if (reqs.minLength) strength++
  if (reqs.hasUpperCase && reqs.hasLowerCase) strength++
  if (reqs.hasNumber) strength++
  if (reqs.hasSpecialChar) strength++

  const levels = [
    { value: 25, color: 'error', text: 'Fraca', class: 'text-error' },
    { value: 50, color: 'warning', text: 'Regular', class: 'text-warning' },
    { value: 75, color: 'info', text: 'Boa', class: 'text-info' },
    { value: 100, color: 'success', text: 'Forte', class: 'text-success' }
  ]

  return levels[strength - 1] || levels[0]
})

// Password Validation Rule
const passwordValidationRule = (value: string) => {
  if (!value) return 'Senha é obrigatória'
  
  const reqs = passwordRequirements.value
  
  if (!reqs.minLength) return 'Senha deve ter no mínimo 8 caracteres'
  if (!reqs.hasUpperCase) return 'Senha deve conter letra maiúscula'
  if (!reqs.hasLowerCase) return 'Senha deve conter letra minúscula'
  if (!reqs.hasNumber) return 'Senha deve conter número'
  if (!reqs.hasSpecialChar) return 'Senha deve conter caractere especial'
  
  return true
}

/**
 * Obtém a mensagem de erro para um campo
 */
function getFieldError(field: string): string {
  const errors = validationErrors.value[field]
  return errors ? errors[0] : ''
}

/**
 * Limpar erros de validação
 */
function clearValidationErrors() {
  validationErrors.value = {}
}

/**
 * Handler para o cadastro
 */
async function handleCadastro() {
  const { valid } = await (form.value as any).validate()
  if (!valid) return

  clearValidationErrors()
  loading.value = true

  try {
    // Preparar dados para a API
    const registerData: RegisterRequest = {
      name: formData.value.nome,
      email: formData.value.email,
      password: formData.value.password,
      password_confirmation: formData.value.confirmPassword,
      type: formData.value.tipo
    }

    // Chamar o serviço de autenticação
    const response = await authService.register(registerData)

    // Validar resposta da API
    if (!response) {
      throw new Error('Resposta inválida do servidor')
    }

    // Salvar token no localStorage se fornecido (será interceptado pelo http.ts)
    if (response.token) {
      localStorage.setItem('sanctum_token', response.token)
      authStore.setToken(response.token)
    }
    
    // Extrair dados do usuário da resposta (com fallback seguro)
    const userData = response.user || {
      id: undefined,
      name: formData.value.nome,
      email: formData.value.email,
      type: formData.value.tipo
    }
    
    // Incluir summary nos dados do usuário se fornecido
    if (response.summary) {
      (userData as any).summary = response.summary
    }
    
    userStore.setUserData(userData as any)
    
    // Também salvar mesAno se fornecido
    if (response.mesAno) {
      userStore.setMesAno(response.mesAno)
    }

    // Mostrar notificação de sucesso
    toastStore.addToast({
      message: 'Sua conta foi criada com sucesso! Redirecionando...',
      color: 'success',
      timeout: 2000,
      icon: 'mdi-check-circle'
    })

    // Ativar estado de redirecionamento
    redirecting.value = true

    // Aguardar um pouco antes de redirecionar
    setTimeout(() => {
      router.push({ name: 'dashboard' })
    }, 1500)
  } catch (error: any) {
    console.error('Erro no cadastro:', error)
    
    // Importar códigos de erro
    const errorCodes = await import('@/assets/errorCodes.json')
    
    // Extrair informações do erro
    const errorData = error || {}
    let errorMessage = 'Ocorreu um erro ao criar sua conta. Tente novamente.'
    
    // Prioridade 1: Verificar se há error_code e buscar mensagem amigável
    if (errorData.error_code && errorCodes.default[errorData.error_code as keyof typeof errorCodes.default]) {
      errorMessage = errorCodes.default[errorData.error_code as keyof typeof errorCodes.default]
    }
    // Prioridade 2: Usar message se disponível
    else if (errorData.message) {
      errorMessage = errorData.message
    } 
    // Prioridade 3: Usar error se disponível
    else if (errorData.error) {
      errorMessage = errorData.error
    }
    
    // Se houver erros de validação específicos do campo
    if (errorData.validation_errors && typeof errorData.validation_errors === 'object') {
      validationErrors.value = errorData.validation_errors

      // Mostrar toast com o primeiro erro de validação
      const firstField = Object.keys(errorData.validation_errors)[0]
      const firstError = errorData.validation_errors[firstField]?.[0]

      toastStore.addToast({
        message: firstError || errorMessage,
        color: 'warning',
        timeout: 5000,
        icon: 'mdi-alert-circle'
      })
    } else {
      // Erro geral - sempre mostrar algo para o usuário
      toastStore.addToast({
        message: errorMessage,
        color: 'error',
        timeout: 5000,
        icon: 'mdi-alert'
      })
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped lang="scss">
.cadastro-wrapper {
  min-height: 100vh;
  background: linear-gradient(135deg, rgb(var(--v-theme-success)) 0%, rgba(var(--v-theme-success), 0.7) 100%);
  padding: 2rem 0;
  position: relative;
}

.cadastro-container {
  padding: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
}

.cadastro-card {
  transition: all 0.3s ease;
  border: none;

  &:hover {
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2) !important;
  }
}

.logo-section {
  background: linear-gradient(135deg, rgb(var(--v-theme-success)) 0%, rgba(var(--v-theme-success), 0.8) 100%);
  border-radius: 8px 8px 0 0;
}

// Container do campo de senha com posição relativa
.password-field-container {
  position: relative;
  margin-bottom: 1rem;
}

// Card de requisitos como overlay
.password-requirements-card {
  position: absolute;
  top: 100%;
  left: 0;
  right: auto;
  margin-top: 0.5rem;
  z-index: 10;
  background: white;
  width: auto;
  min-width: 280px;
  max-width: 400px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
}

// Animação de entrada/saída
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.2s ease-in;
}

.slide-fade-enter-from {
  transform: translateY(-10px);
  opacity: 0;
}

.slide-fade-leave-to {
  transform: translateY(-5px);
  opacity: 0;
}

// Estilos para o conteúdo dos termos e políticas
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
