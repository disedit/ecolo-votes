<script setup>
import { formatCurrency } from '@/Composables/useCurrency.js'

defineProps({
  amount: { type: [Number, String], required: true }
})
</script>

<template>
  <div class="flex bg-gray-200 py-2 md:py-3 px-4 font-mono text-md font-bold">
    <span>TOTAL</span>
    <span class="ms-auto">{{ formatCurrency(amount) }}</span>
  </div>
</template>