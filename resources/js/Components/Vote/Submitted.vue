<script setup>
import { Icon } from '@iconify/vue'
import RevealSecret from './RevealSecret.vue'

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
    <div :class="{ 'text-xl': vote.type === 'yesno', 'text-lg': vote.type !== 'yesno' }">
      <RevealSecret :secret="!!vote.secret" :name="vote.voted_for" />
    </div>
  </div>
</template>