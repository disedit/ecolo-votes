<script setup>
import { ref } from 'vue'
import { onClickOutside } from '@vueuse/core'
import { Icon } from '@iconify/vue'

defineProps({
  variant: { type: String, default: 'gray' }
})

const menuOpen = ref(false)
const dropdown = ref(null)

onClickOutside(dropdown, () => menuOpen.value = false)

function toggleMenu() {
  menuOpen.value = !menuOpen.value
}
</script>

<template>
  <div :class="['button-options focus-black', `variant-${variant}`]" ref="dropdown">
    <div class="button-options-main grow">
      <slot />
    </div>
    <button @click="toggleMenu" class="shrink-0 button-options-handle">
      <Icon icon="ri:arrow-down-s-line" />
    </button>
    <div v-if="menuOpen" class="button-options-menu" @click="menuOpen = false">
      <slot name="options" />
    </div>
  </div>
</template>

<style lang="scss">
.button-options {
  display: flex;
  position: relative;

  &-menu {
    position: absolute;
    z-index: 100;
    background: var(--egp-white);
    top: 2.2em;
    right: 0;
    border: 2px var(--egp-gray-300) solid;
    min-width: 100%;

    button {
      display: flex;
      align-items: center;
      padding: var(--spacer-2);
      border-bottom: 1px var(--egp-gray-300) solid;
      gap: var(--spacer-2);
      text-align: left;
      white-space: nowrap;
      width: 100%;

      &:hover {
        background: var(--egp-gray-100);
      }
    }
  }

  &-handle {
    background: var(--button-background);
    color: var(--text-color);
    padding: var(--spacer-1);
  }

  &.variant-yellow {
    --button-background: var(--egp-yellow);
    --text-color: var(--egp-green-pine);
  }

  &.variant-gray {
    --button-background: var(--egp-gray-300);
    --text-color: var(--egp-green-pine);
  }
}
</style>