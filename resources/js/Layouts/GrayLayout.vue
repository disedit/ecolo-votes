<script setup>
import { ModalsContainer } from 'vue-final-modal'
import GlobalNav from '@/Components/Global/Nav.vue'
import GlobalFooter from '@/Components/Global/Footer.vue'

defineProps({
    auth: { type: Object, required: true },
    edition: { type: Object, required: true }
})
</script>

<template>
  <div>
    <GlobalNav details eager solid />
    <div class="bg-sand text-green-pine">
      <main class="min-h-fill">
        <slot />
      </main>
      <GlobalFooter />
    </div>
    <ModalsContainer />
  </div>
</template>