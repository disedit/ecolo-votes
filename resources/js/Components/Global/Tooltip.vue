<script setup>
defineProps({
  text: { type: String, default: null },
  centered: { type: Boolean, default: false }
})
</script>

<template>
  <span :class="['tooltip relative', { centered }]">
    <slot />
    <span v-if="text" class="tooltip-content absolute text-sm py-1 px-2">{{ text }}</span>
  </span>
</template>

<style lang="scss">
.tooltip {
  z-index: 10;

  &-content {
    bottom: -.5em;
    visibility: hidden;
    background-color: var(--egp-black);
    color: var(--egp-white);
    transform: translate(var(--translate-x, 0), var(--translate-y, 100%));

    &::before {
      position: absolute;
      content: '';
      display: block;
      height: .75em;
      width: .75em;
      top: 0;
      background-color: var(--egp-black);
      transform: rotate(45deg) translate(var(--translate-x, 0), var(--translate-y, 0));
    }
  }

  &:hover {
    z-index: 1000;

    .tooltip-content {
      visibility: visible;      
    }
  }

  &.centered {
    .tooltip-content {
      left: 50%;
      --translate-x: -50%;

      &::before {
        left: 50%;
        --translate-x: -50%;
      }
    }
  }
}

@media (hover: none) {
  .tooltip-content {
    display: none;
  }
}
</style>