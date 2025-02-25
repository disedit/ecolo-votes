<script setup>
import { Icon } from '@iconify/vue'

defineProps({
  code: { type: Object, required: true }
})
</script>

<template>
  <div :class="['code', { 'next-alert': code.nextAlert}]">
    <Icon icon="icons8:key" />
    {{ code.password }}
  </div>
</template>

<style lang="scss" scoped>
.code {
  display: flex;
  position: fixed;
  background: var(--egp-pink);
  bottom: 0;
  left: 0;
  right: 0;
  padding: 2vw var(--screen-padding);
  font-size: 12vh;
  font-weight: 900;
  letter-spacing: .2em;
  line-height: 1;
  justify-content: center;
  gap: 2vw;
  z-index: 1;

  &.next-alert {
    animation: alert 1s infinite;
  }
}

@keyframes alert {
    0% {
        background: var(--egp-orange);
    }
    50% {
        background: var(--egp-pink);
    }
    100% {
        background: var(--egp-orange);
    }
}
</style>