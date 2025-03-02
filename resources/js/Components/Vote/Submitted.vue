<script setup>
import { Icon } from '@iconify/vue'
import RevealSecret from './RevealSecret.vue'
import { optionClasses } from '@/Composables/useColors.js';

defineProps({
  vote: { type: Object, required: true }
})
</script>

<template>
  <div>
    <div class="flex justify-center">
      <Icon icon="line-md:circle-to-confirm-circle-transition" class="text-5xl" />
    </div>
    <p class="text-lg my-6 text-center">
      Your vote has been submitted.
    </p>
    <div :class="['flex flex-col gap-4', { 'text-xl': vote.type === 'yesno', 'text-lg': vote.type !== 'yesno' }]">
      <RevealSecret
        v-for="option in vote.voted_for"
        :secret="!!vote.secret"
        :name="option.name"
        :key="option.id"
        :option-classes="optionClasses(option, vote)"
      />
    </div>
  </div>
</template>