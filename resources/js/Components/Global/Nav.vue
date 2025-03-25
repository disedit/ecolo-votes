<script setup>
import { computed, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'
import { useWindowScroll } from '@vueuse/core'
import EcoloLogo from '@/Components/Global/EcoloLogo.vue'
import DropdownMenu from '@/Components/Inputs/DropdownMenu.vue'
import Language from './Language.vue'

const props = defineProps({
  details: { type: Boolean, default: false },
  eager: { type: Boolean, default: false },
  solid: { type: Boolean, default: false }
})

const page = usePage()
const user = computed(() => page.props.auth.user)
const edition = computed(() => page.props.edition)

const { y } = useWindowScroll()
const threshold = props.eager ? 0 : 50
const scrolled = computed(() => y.value > threshold)

watch(y, () => {
  if (scrolled.value) {
    document.querySelector('meta[name="theme-color"]').setAttribute('content', '#0F8A54')
  } else {
    document.querySelector('meta[name="theme-color"]').setAttribute('content', '#47B972')
  }
})
</script>

<template>
  <nav :class="['nav flex items-center gap-4', { scrolled, solid, 'show-details': scrolled || details }]">
    <Link href="/">
      <EcoloLogo class="nav-logo text-white" />
    </Link>
    <div class="reveal-on-scroll nav-title font-bold md:mx-6">
      <span class="hidden md:inline">{{ edition.title }}</span>
      <span class="md:hidden">{{ edition.title_short }}</span>
    </div>
    <div class="nav-details font-bold gap-6">
      <div v-if="edition.location" class="flex items-center gap-2 reveal-on-scroll" style="--delay: .15s">
        <Icon icon="ri:map-pin-2-line" />
        {{ edition.location }}
      </div>
      <div v-if="edition.dates" class="flex items-center gap-2 reveal-on-scroll" style="--delay: .25s">
        <Icon icon="ri:calendar-line" />
        {{ edition.dates }}
      </div>
    </div>
    <div class="nav-lang ms-auto">
      <Language />
    </div>
    <div v-if="user" class="nav-user">
      <DropdownMenu class="nav-dropdown">
        {{ $t('nav.user') }}

        <template #items>
          <div class="font-mono text-gray-700 text-sm px-4 py-3">
            {{ user.first_name }}
          </div>
          <Link v-if="['admin', 'credentials'].includes(user.role)" href="/admin">
            <Icon icon="ri:lock-line" />
            {{ $t('admin.title') }}
          </Link>
          <hr />
          <Link href="/logout" method="post" as="button" type="button">
            <Icon icon="ri:logout-box-r-line" />
            {{ $t('nav.logout') }}
          </Link>
        </template>
      </DropdownMenu>
    </div>
  </nav>
  <div class="h-safe-area" />
</template>

<style lang="scss" scoped>
.nav {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  padding: var(--spacer-2) var(--spacer-4);
  color: var(--egp-black);
  z-index: 1000;
  transition: .25s ease;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
  background-color: var(--egp-white);

  &-logo {
    height: 3.25rem;
  }

  &-details {
    display: flex;
  }

  .reveal-on-scroll {
    opacity: 0;
    transition: .25s ease;
    transition-delay: var(--delay, 0);
    translate: 10% 0;
  }

  &.solid:not(.scrolled) {
    background-color: var(--egp-white);
    color: var(--egp-black);
  }

  &.show-details {
    .reveal-on-scroll {
      opacity: 1;
      translate: 0;
    }
  }
}

@include media('<lg') {
  .nav {
    &-logo {
      height: 2.5rem;
    }

    &-details {
      display: none;
    }

    &-title {
      font-size: var(--text-sm);
      line-height: 1;
    }

    &-user,
    &-lang {
      font-size: var(--text-sm);
      margin-right: -.5rem;
    }
  }
}
</style>