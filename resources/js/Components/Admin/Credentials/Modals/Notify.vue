<script setup>
import GlobalModal from '@/Components/Global/Modal.vue'

const emit = defineEmits(['close', 'refresh'])
</script>

<template>
  <GlobalModal :width="600" @close="emit('close')">
    <template #title>
      <h1>{{ $t('credentials.notify.title') }}</h1>
    </template>
    
  </GlobalModal>
</template>
