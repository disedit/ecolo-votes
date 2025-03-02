<script setup>
import { ref, computed, onMounted } from 'vue'
import { Icon } from '@iconify/vue'
import PulseIcon from '@/Components/Global/PulseIcon.vue'

defineProps({
  loading: { type: Boolean, default: false }
})

const emit = defineEmits(['refresh'])

const status = ref('disconnected')

onMounted(() => {
  status.value = window.Echo.connector.pusher.connection.state
  window.Echo.connector.pusher.connection.bind('state_change', (state) => {
    status.value = state.current
  })
})

const offline = computed(() => ['disconnected', 'unavailable'].includes(status.value))
</script>

<template>
  <div :class="['flex items-center gap-2 bg-white py-2 px-4 capitalize font-bold', {
    'text-green-dark': status === 'connected',
    'text-red': offline
  }]" aria-live="assertive">
    <PulseIcon v-if="!loading" :color="offline ? 'red' : 'green'" :still="offline" />
    <Icon v-else icon="line-md:loading-loop" />
    {{ loading ? $t('global.loading') : $t('voter.status.' + status) }}
    <button @click="emit('refresh')" class="ms-auto" title="Refresh" aria-label="Refresh">
      <Icon icon="ooui:reload" />
    </button>
  </div>
</template>