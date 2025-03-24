<script setup>
import { Link } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'

defineProps({
  show: { type: Array, default: ['badge', 'votes', 'menu'] }
})
</script>

<template>
  <div class="sticky flex bottom-6 justify-center z-[1000]">
    <ul class="menu shadow-lg bg-white flex divide-x border rounded">
      <li v-if="show.includes('vote')">
        <Link href="/vote">
          <Icon icon="ri:hand" class="shrink-0" />
          {{ $t('menu.vote') }}
        </Link>
      </li>
      <li v-if="show.includes('votes')">
        <Link href="/votes">
          <Icon icon="ri:file-list-3-line" class="shrink-0" />
          {{ $t('menu.results') }}
        </Link>
      </li>
    </ul>
  </div>
</template>

<style lang="scss" scoped>
.menu {
  a {
    display: flex;
    align-items: center;
    padding: .75em 1em;
    text-decoration: none;
    font-weight: bold;
    gap: var(--spacer-2);
  }
}
</style>
