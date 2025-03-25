<script setup>
import { Icon } from '@iconify/vue';
</script>

<template>
  <div class="dropdown relative">
    <button class="dropdown-button flex items-center gap-2 font-bold p-2">
      <slot />
      <Icon icon="ri:arrow-drop-down-line" />
    </button>

    <div class="dropdown-menu shadow-md overflow-clip border">
      <hr />
      <slot name="items" />
    </div>
  </div>
</template>

<style lang="scss" scoped>
.dropdown {
  &-menu {
    display: none;
    position: absolute;
    top: 0;
    padding-top: 2.5rem;
    background-color: var(--egp-white);
    color: var(--egp-green-pine);
    min-width: 140px;
    width: 100%;
    right: 0;
    z-index: 5;
    border-radius: .5rem;

    & > :deep(a),
    & > :deep(button) {
      display: flex;
      padding: var(--spacer-2);
      text-decoration: none;
      align-items: center;
      gap: var(--spacer-2);

      &:hover {
        background-color: var(--egp-yellow);
        color: var(--egp-green-pine);
      }
    }
  }

  &:hover,
  &:focus-within {
    .dropdown-button {
      position: relative;
      background-color: transparent;
      color: var(--egp-green-dark);
      z-index: 10;
    }
  
    .dropdown-menu {
      display: flex;
      flex-direction: column;
    }
  }
}
</style>