<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'
import { useWindowScroll } from '@vueuse/core'

const page = usePage()
const edition = computed(() => page.props.edition)
const { y } = useWindowScroll()
</script>

<template>
  <header :class="['congress-header text-black', { scrolled: y > 20 }]">
    <div class="container padded relative">
      <h1 class="congress-header-name hide-on-scroll font-bold relative z-10">
        {{ edition.title }}
      </h1>
      <div class="flex flex-col md:flex-row font-bold md:gap-6 relative z-10 mt-4 md:mt-0">
        <div v-if="edition.location" class="flex items-center gap-2 hide-on-scroll" style="--delay: .15s">
          <Icon icon="ri:map-pin-2-line" />
          {{ edition.location }}
        </div>
        <div v-if="edition.dates" class="flex items-center gap-2 hide-on-scroll" style="--delay: .25s">
          <Icon icon="ri:calendar-line" />
          {{ edition.dates }}
        </div>
      </div>
    </div>
  </header>
</template>

<style lang="scss" scoped>
.congress-header {
  &-name {
    font-size: var(--text-4xl);
    line-height: 1;
  }

  &-illustration {
    position: absolute;
    z-index: -1;
    width: 600px;
    top: -120%;
    right: -30%;
    rotate: -7deg;
  }

  .hide-on-scroll {
    transition: .25s ease;
    transition-delay: var(--delay, 0);
  }

  &.scrolled {
    .hide-on-scroll {
      opacity: 0;
      translate: 0% -20%;
    }
  }
}

@include media('<md') {
  .congress-header {
    overflow: clip;
    margin-top: calc(var(--navbar-safe-area) * -1);
    padding-top: var(--navbar-safe-area);

    &-name {
      font-size: var(--text-3xl);
    }
  }
}
</style>