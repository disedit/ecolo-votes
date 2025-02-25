<script setup>
import { ref, computed, onMounted } from 'vue'

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
  <div class="fixed top-5 left-5 z-[9000]">
    <div v-if="offline" class="offline">
      OFFLINE
    </div>
  </div>
</template>

<style lang="scss" scoped>
.offline {
  background: var(--egp-black);
  color: var(--egp-white);
  font-size: 4vw;
  display: grid;
  place-items: center;
  line-height: 1;
}
</style>