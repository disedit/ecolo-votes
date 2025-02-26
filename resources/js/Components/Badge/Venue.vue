<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'
import Links from './Links.vue'

const page = usePage()
const edition = computed(() => page.props.edition)
const user = computed(() => page.props.user)
const attendee = computed(() => page.props.attendee)
</script>

<template>
  <div class="bg-white max-w-[450px] md:aspect-[1/1.35] mx-auto w-full flex flex-col">
    <img :src="edition.cover" class="w-full h-32 object-cover" />
    <div class="p-4">
      <h1 class="font-headline uppercase text-lg">{{ edition.title }}</h1>
      <a :href="edition.venue_link" target="_blank" rel="noopener" class="underline-offset-2 decoration-green-pine/25 flex gap-2">
        <Icon icon="ri:map-pin-2-line" class="w-6 shrink-0 mt-1" />
        <span>
          {{ edition.venue_name }}
          <span class="text-sm block leading-tight">{{ edition.venue_address }}</span>
        </span>
      </a>
      <div class="flex gap-2 mt-3">
        <Icon icon="ri:calendar-line" class="w-6 shrink-0 mt-1" />
        <span>
          {{ edition.dates }}
        </span>
      </div>
    </div>
    <div class="flex px-4 pb-4 gap-2 mt-auto md:mt-0">
      <a :href="`/pass/${attendee.id}/apple`" title="Add to Apple Wallet">
        <img src="../../../images/pass/add-to-wallet.png" alt="Add to Apple Wallet" class="h-12" />
      </a>
      <a :href="`/pass/${attendee.id}/google`" title="Add to Google Wallet">
        <img src="../../../images/pass/add-to-wallet-google.svg" alt="Add to Google Wallet" class="h-12" />
      </a>
    </div>
    <Links class="p-4 hidden md:mt-auto md:flex" />
  </div>
</template>