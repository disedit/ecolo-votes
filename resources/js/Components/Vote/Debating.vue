<script setup>
import { Icon } from '@iconify/vue'
import PulseIcon from '@/Components/Global/PulseIcon.vue'

defineProps({
  vote: { type: Object, required: true }
})
</script>

<template>
  <div class="w-full h-full">
    <h2 class="font-headline uppercase text-xl leading-tight">
      {{ vote.name }}
    </h2>
    <p class="opacity-75" v-if="vote.subtitle">
      {{ vote.subtitle }}
    </p>

    <p class="mt-6 mb-3">
      Available options:
    </p>
    <div class="flex gap-2 flex-wrap">
      <span v-for="option in vote.options" :key="option.id" :class="['flex items-center gap-2 bg-white py-1 px-2 font-bold', `option-${option.name.replaceAll(' ', '-')}`]">
        <span class="circle block shrink-0 h-[1em] w-[1em] rounded-full" />
        {{ option.name }}
      </span>
    </div>

    <p class="mt-12 opacity-50" v-if="!vote.closed_at">
      Voting will open shortly...
    </p>
    <p class="mt-12 opacity-50" v-else>
      Voting concluded.
    </p>
  </div>
</template>

<style lang="scss" scoped>
.circle {
  background-color: var(--color, var(--egp-pink));
}
</style>