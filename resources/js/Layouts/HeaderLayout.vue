<script setup>
import GlobalNav from '@/Components/Global/Nav.vue'
import GlobalHeader from '@/Components/Global/Header.vue'
import GlobalFooter from '@/Components/Global/Footer.vue'
</script>

<template>
  <div>
    <GlobalNav />
    <main class="min-h-fill pb-5">
      <GlobalHeader />
      <slot />
    </main>
    <GlobalFooter />
  </div>
</template>