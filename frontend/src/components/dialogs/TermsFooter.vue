<template>
  <div>
    <!-- Footer -->
    <v-divider />
    <v-card-text class="text-center text-caption text-medium-emphasis pa-4">
      Ao continuar, você concorda com nossos
      <v-btn 
        variant="text" 
        size="x-small" 
        color="primary"
        @click="showTerms = true"
      >
        Termos de Uso
      </v-btn>
      e
      <v-btn 
        variant="text" 
        size="x-small" 
        color="primary"
        @click="showPrivacy = true"
      >
        Política de Privacidade
      </v-btn>
    </v-card-text>

    <!-- Dialogs -->
    <TermsOfServiceDialog v-model="showTerms" />
    <PrivacyPolicyDialog v-model="showPrivacy" />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import PrivacyPolicyDialog from './PrivacyPolicyDialog.vue'
import TermsOfServiceDialog from './TermsOfServiceDialog.vue'

const showTerms = ref(false)
const showPrivacy = ref(false)
</script>

<style scoped></style>
