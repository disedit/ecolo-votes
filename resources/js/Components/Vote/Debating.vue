<script setup>
import { optionClasses } from '@/Composables/useColors.js'

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
      {{ $t('voter.debate.available_options') }}
    </p>
    <div class="flex gap-2 flex-wrap">
      <span v-for="option in vote.options" :key="option.id" :class="['flex items-center gap-2 bg-white py-1 px-2 font-bold', optionClasses(option, vote)]">
        <span class="circle block shrink-0 h-[1em] w-[1em] rounded-full" />
        {{ option.name }}
      </span>
    </div>

    <p class="mt-12 opacity-50" v-if="!vote.closed_at">
      {{ $t('voter.debate.vote_will_open') }}
    </p>
    <p class="mt-12 opacity-50" v-else>
      {{ $t('voter.debate.vote_will_open') }}
    </p>
  </div>
</template>

<style lang="scss" scoped>
.circle {
  background-color: var(--color, var(--egp-blue));
}
</style>