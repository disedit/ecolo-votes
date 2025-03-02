<script setup>
import { Icon } from '@iconify/vue'

defineProps({
  title: { type: String, default: '' },
  icon: { type: String, default: '' },
  edge: { type: Boolean, default: false }
})
</script>

<template>
  <section class="card container padded">
    <div class="flex items-center gap-2">
      <h2 v-if="title" class="bg-pink font-mono inline-flex items-center gap-2 font-bold py-2 px-site">
        <Icon v-if="icon" :icon="icon" />
        {{ title }}
      </h2>
      <div v-if="$slots.toolbar" class="grow">
        <slot name="toolbar" />
      </div>
    </div>
    <div :class="['bg-white', {'p-site': !edge}]">
      <slot />
    </div>
  </section>
</template>
