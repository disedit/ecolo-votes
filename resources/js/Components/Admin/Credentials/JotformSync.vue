<script setup>
import { ref } from 'vue'
import { Icon } from '@iconify/vue'

const emit = defineEmits(['synced'])
const loading = ref(false)

async function sync() {
  loading.value = true
  try {
    await window.axios.post('/api/credentials/sync')
    emit('synced')
  } catch(error) {
    alert('There was an error syncing with JotForm')
  } finally {
    loading.value = false
  }  
}
</script>

<template>
  <button @click="sync" :disabled="loading" class="flex items-center gap-1">
    <template v-if="loading">
      <Icon icon="line-md:loading-twotone-loop" />
      Syncing with JotForm...
    </template>
    <template v-else>
      <Icon icon="material-symbols:refresh" />
      Sync with JotForm
    </template>
  </button>
</template>