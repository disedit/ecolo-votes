<script setup>
import { formatCurrency } from '@/Composables/useCurrency.js'

defineProps({
  payment: { type: Object, required: true }
})
</script>

<template>
  <table class="w-full bg-gray-100 mt-4">
    <thead>
      <tr>
        <th class="border-b text-left px-4 py-2 bg-gray-200">Item</th>
        <th class="border-b text-right px-4 py-2 bg-gray-200">Amount</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(item, i) in payment.receipt.cart" :key="i">
        <td class="px-4 py-2 border-b">{{ item.name }}<br>{{ item.description }}</td>
        <td class="px-4 py-2 border-b text-right font-mono">{{ formatCurrency(item.amount) }}</td>
      </tr>
    </tbody>
  </table>
</template>