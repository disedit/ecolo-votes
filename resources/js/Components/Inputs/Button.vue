<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'
import GlobalArrow from '@/Components/Global/Arrow.vue';

const props = defineProps({
  variant: { type: String, default: 'green-neon' },
  size: { type: String, default: 'md' },
  flat: { type: Boolean, default: false },
  block: { type: Boolean, default: false },
  loading: { type: Boolean, default: false },
  icon: { type: String, default: null },
  href: { type: String, default: null },
  arrow: { type: Boolean, default: false }
})

const tag = computed(() => props.href ? Link : 'button')
</script>

<template>
  <Component
    :is="tag"
    :href="href"
    :class="[
    'button',
    'font-bold',
    `button-${variant}`,
    `button-${size}`,
    { flat, block, arrow }
  ]">
    <span v-if="loading" class="with-icon flex items-center justify-center gap-2">
      <Icon icon="line-md:loading-loop" />
      <slot name="loading" />
    </span>
    <span v-else-if="icon" class="with-icon flex items-center justify-center gap-2">
      <Icon :icon="icon" />
      <slot />
    </span>
    <template v-else>
      <GlobalArrow class="arrow-item" v-if="arrow" />
      <slot />
    </template>
  </Component>
</template>

<style lang="scss" scoped>
.button {
  background: var(--btn-bg-color, var(--egp-gray-200));
  color: var(--btn-text-color, var(--egp-green-pine));
  padding: .5em .75em;
  font-size: var(--btn-text-size, var(--text-base));
  font-weight: bold;
  transition: .25s ease;
  text-decoration: none;
  border-radius: .25em;

  &:hover:not(:disabled) {
    background: var(--btn-bg-color-hover, var(--btn-bg-color, var(--egp-gray-200)));
    color: var(--btn-text-color-hover, var(--btn-text-color, var(--egp-green-pine)));
  }

  &-lg {
    --btn-text-size: var(--text-md);
  }

  &-sm {
    --btn-text-size: var(--text-sm);
  }

  &-gray {
    --btn-bg-color-hover: var(--egp-gray-300);
  }

  &-yellow {
    --btn-bg-color: var(--egp-yellow);
    --focus-color: var(--egp-black);

    &.flat:hover:not(:disabled) {
      --btn-bg-color: var(--egp-orange);
    }
  }

  &-green-neon {
    --btn-bg-color: var(--egp-green-neon);
    --focus-color: var(--egp-purple);

    &.flat:hover:not(:disabled) {
      --btn-bg-color: var(--egp-purple);
      --btn-text-color: var(--egp-white);
    }
  }

  &-pink {
    --btn-bg-color: var(--egp-pink);
    --focus-color: var(--egp-black);
  }

  &-purple {
    --btn-bg-color: var(--egp-purple);
    --focus-color: var(--egp-black);
    --btn-text-color: var(--egp-white);
    --btn-bg-color-hover: var(--egp-black);
  }

  &-green {
    --btn-bg-color: var(--egp-green);
    --focus-color: var(--egp-black);
    --btn-bg-color-hover: var(--egp-green-dark);
    --btn-text-color-hover: var(--egp-white);
  }

  &-red {
    --btn-bg-color: var(--egp-red);
    --focus-color: var(--egp-black);
    --btn-text-color: var(--egp-white);
  }

  &-white {
    --btn-bg-color: var(--egp-white);
    --focus-color: var(--egp-black);
    --btn-text-color: var(--egp-green-pine);
  }

  &-soft-red {
    --btn-bg-color: var(--egp-gray-200);
    --btn-text-color: var(--egp-red);
    --focus-color: var(--egp-black);
    --btn-bg-color-hover: var(--egp-red);
    --btn-text-color-hover: var(--egp-white);
  }

  &-pine {
    --btn-bg-color: var(--egp-green-pine);
    --btn-text-color: var(--egp-white);
    --focus-color: var(--egp-green-pine);

    &.flat:hover:not(:disabled) {
      --btn-text-color: var(--egp-yellow);
    }
  }

  &.block {
    width: 100%;
  }

  &.arrow {
    position: relative;
    padding-inline-start: 2.5em;
    padding-inline-end: 1.75em;

    .arrow-item {
      position: absolute;
      left: -3em;
      top: 50%;
      transform: translateY(-50%);
      transition: .25s ease;
    }

    &:hover .arrow-item {
      left: -2.5em;
    }
  }

  &:hover:not(.flat):not(.arrow):not(:disabled) {
    box-shadow: .5em .5em 0 var(--egp-green-pine);
    translate: -.5em -.5em;
  }

  &:active:not(.flat):not(.arrow):not(:disabled) {
    box-shadow: .25em .25em 0 var(--egp-green-pine);
    translate: -.25em -.25em;
  }

  &:disabled {
    opacity: .5;
  }
}
</style>