<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'

const page = usePage()
const links = computed(() => page.props.edition.links)
</script>

<template>
  <ul class="flex gap-2 flex-wrap">
    <li v-for="(link, i) in links" :key="i">
      <a
        :href="link.href"
        :target="link.target"
        class="flex items-center gap-2 bg-gray-100 no-underline py-1 px-3 hover:bg-yellow hover:text-green-pine"
      >
        <Icon v-if="link.icon" :icon="link.icon" />
        {{ link.label }}
      </a>
    </li>
  </ul>
</template>