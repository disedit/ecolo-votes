<script setup>
import { Link } from '@inertiajs/vue3'
import GlobalNav from '@/Components/Global/Nav.vue'
import GlobalFooter from '@/Components/Global/Footer.vue'
</script>

<template>
  <div>
    <GlobalNav />
    <main class="min-h-fill">
      <slot />
    </main>
    <GlobalFooter />
  </div>
</template>
