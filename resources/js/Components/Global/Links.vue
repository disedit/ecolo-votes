<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'

const props = defineProps({
  links: { type: Array, required: true },
  include: { type: Object, default: {}}
})

const pages = computed(() => {
  return Object.keys(props.include).filter(key => props.include[key])  
})
</script>

<template>
  <ul class="info-menu grow">
    <li v-if="pages.includes('badge')">
      <Link href="/badge" class="info-menu-link">
        <Icon icon="ri:account-box-line" class="info-menu-icon" />
        Badge
      </Link>
    </li>
    <li v-if="pages.includes('vote')">
      <Link href="/vote" class="info-menu-link">
        <Icon icon="ri:hand" class="info-menu-icon" />
        Cast your votes
      </Link>
    </li>
    <li v-if="pages.includes('votes')">
      <Link href="/votes" class="info-menu-link">
        <Icon icon="ri:file-list-3-line" class="info-menu-icon" />
        Voting list
      </Link>
    </li>
    <li v-for="(link, i) in links" :key="i">
      <div v-if="link.notifications" class="flex gap-1 items-center text-sm leading-tight my-3">
        <Icon icon="ri:notification-2-line" class="shrink-0" />
        Receive important notifications on your phone
      </div>
      <a :href="link.href" :target="link.target" class="info-menu-link">
        <Icon v-if="link.icon" :icon="link.icon" class="info-menu-icon" />
        {{ link.label }}
        <Icon icon="ri:external-link-line" class="ms-auto opacity-50" />
      </a>
    </li>
  </ul>
</template>

<style lang="scss" scoped>
.info-menu {
  display: flex;
  flex-direction: column;
  gap: var(--spacer-4);

  &-link {
    background-color: var(--egp-white);
    display: flex;
    align-items: center;
    gap: var(--spacer-2);
    padding: var(--spacer-4);
    font-size: var(--text-md);
    text-decoration: none;
    font-weight: 600;
    transition: .25s ease;
    border: 2px var(--egp-white) solid;

    &:hover {
      color: var(--egp-green-pine);
    }

    @media (hover: hover) {
      &:hover {
        box-shadow: .5rem .5rem 0 0 var(--egp-green-pine);
        translate: -.5rem -.5rem;
        border-color: var(--egp-green-pine);
        background-color: var(--egp-pink);
      }
    }
  }

  &-icon {
    flex-shrink: 0;
  }
}
</style>
