<script setup>
import { onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { Link } from '@inertiajs/vue3'
import DropdownMenu from '@/Components/Inputs/DropdownMenu.vue'

const i18n = useI18n()

const locales = {
  fr: 'Français',
  de: 'Deutsch',
  en: 'English'
}

function setLanguage (locale) {
  i18n.locale.value = locale
  localStorage.setItem('locale', locale)
}

onMounted(() => {
  if (localStorage.getItem('locale')) {
    i18n.locale.value = localStorage.getItem('locale')
  }
})
</script>

<template>
  <DropdownMenu class="nav-dropdown">
    <span class="md:hidden uppercase">
      {{ $i18n.locale }}
    </span>
    <span class="hidden md:inline">
      {{ locales[$i18n.locale] }}
    </span>

    <template #items>
      <template v-for="(name, locale) in locales" :key="`locale-${locale}`">
        <Link
          v-if="locale !== $i18n.locale"
          href="#"
          @click.prevent="setLanguage(locale)"
        >
          {{ name }}
        </Link>
      </template>
    </template>
  </DropdownMenu>
</template>