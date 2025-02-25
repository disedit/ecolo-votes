<script setup>
import { Icon } from '@iconify/vue'
</script>

<template>
  <button class="remove-button relative text-red text-sm uppercase p-4 -m-4 focus-red">
    <span class="absolute overflow-hidden tip"><span>Remove</span></span>
    <Icon icon="ri:delete-bin-2-line" class="text-base" />
  </button>
</template>

<style lang="scss" scoped>
.tip {
  right: 2.5rem;
  max-width: 0;
  transition: .25s ease;
}

.remove-button:hover {
  .tip {
    max-width: 10rem;
  }
}
</style>