<script setup>
import { Head } from '@inertiajs/vue3'

import GlobalLinks from '@/Components/Global/Links.vue'
import GrayLayout from '@/Layouts/GrayLayout.vue'

defineOptions({
  layout: GrayLayout
})

const props = defineProps({
  edition: { type: Object, required: true },
  auth: { type: Object, required: true },
  attendee: { type: Object, required: true },
})
</script>

<template>
  <Head title="Information" />

  <div class="container padded min-h-fill flex items-center">
    <GlobalLinks
      :links="edition.links"
      :include="{
        badge: true,
        vote: props.attendee.votes > 0,
        votes: true
      }"
    />
  </div>
</template>
