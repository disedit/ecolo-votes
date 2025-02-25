<script setup>
defineProps({
  color: { type: String, default: 'green' },
  still: { type: Boolean, default: false }
})
</script>

<template>
  <span :class="[`color-${color}`, { still }, 'pulse block w-[1em] h-[1em] rounded-full']" />
</template>

<style lang="scss" scoped>
.pulse {
  background-color: var(--color, currentColor);

  &.color-orange:not(.still) {
    animation: pulse-orange 2s infinite;
  }

  &.color-red:not(.still) {
    animation: pulse-red 2s infinite;
  }

  &.color-pine:not(.still) {
    animation: pulse-pine 2s infinite;
  }
}

.pulse:not(.still) {
  animation: var(--animation, pulse-green 2s infinite);
}

@keyframes pulse-green {
  0% {
    transform: scale(0.95);
    box-shadow: 0 0 0 0 rgba(#0F8A54, .75);
  }

  70% {
    transform: scale(1);
    box-shadow: 0 0 0 10px rgba(#0F8A54, 0);
  }

  100% {
    transform: scale(0.95);
    box-shadow: 0 0 0 0 rgba(#0F8A54, 0);
  }
}

@keyframes pulse-orange {
  0% {
    transform: scale(0.95);
    box-shadow: 0 0 0 0 rgba(#feae37, .75);
  }

  70% {
    transform: scale(1);
    box-shadow: 0 0 0 10px rgba(#feae37, 0);
  }

  100% {
    transform: scale(0.95);
    box-shadow: 0 0 0 0 rgba(#feae37, 0);
  }
}

@keyframes pulse-pine {
  0% {
    transform: scale(0.95);
    box-shadow: 0 0 0 0 rgba(#0B3323, .75);
  }

  70% {
    transform: scale(1);
    box-shadow: 0 0 0 10px rgba(#0B3323, 0);
  }

  100% {
    transform: scale(0.95);
    box-shadow: 0 0 0 0 rgba(#0B3323, 0);
  }
}

@keyframes pulse-red {
  0% {
    transform: scale(0.95);
    box-shadow: 0 0 0 0 rgba(#fc574a, .75);
  }

  70% {
    transform: scale(1);
    box-shadow: 0 0 0 10px rgba(#fc574a, 0);
  }

  100% {
    transform: scale(0.95);
    box-shadow: 0 0 0 0 rgba(#fc574a, 0);
  }
}
</style>