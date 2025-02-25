<script setup>
import { Head } from '@inertiajs/vue3'
import { formatCurrency } from '@/Composables/useCurrency.js'
import { formatDate } from '@/Composables/useDate.js'
import GlobalCard from '@/Components/Global/Card.vue'

defineProps({
  invoices: { type: Array, required: true }
})

function formatStatus(status) {
  const statuses = {
    paid: 'Paid',
    declined: 'Declined',
    pending: 'Pending',
    waiting: 'Pending',
    refunded: 'Refunded'
  }
  return statuses[status]
}
</script>

<template>
    <Head title="Invoices" />
    <GlobalCard title="Invoices" icon="ri:file-text-line" edge class="mt-10">
      <table v-if="invoices.length > 0" class="invoices">
          <thead>
            <tr>
              <th class="text-left">#</th>
              <th class="text-left">Date</th>
              <th class="text-right">Amount</th>
              <th class="text-left">Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="invoice in invoices" :key="invoice.id">
              <td>EGPE-{{ invoice.id }}</td>
              <td>{{ formatDate(invoice.created_at) }}</td>
              <td class="text-right">{{ formatCurrency(invoice.amount) }}</td>
              <td>
                <span :class="['status text-base py-1 px-2 rounded font-bold text-rbase', invoice.status]">{{ formatStatus(invoice.status) }}</span>
              </td>
              <td class="text-right">
                <a :href="`/invoice/${invoice.id}`" class="download-link" download>Download</a>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-else class="p-6">
          You don't have any invoices yet.
        </div>
    </GlobalCard>
</template>

<style lang="scss" scoped>
.invoices {
  width: 100%;

  th {
    border-bottom: 1px var(--egp-gray-200) solid;
    padding: var(--spacer-4) var(--spacer-6);
  }

  td {
    border-bottom: 1px var(--egp-gray-200) solid;
    padding: var(--spacer-4) var(--spacer-6);
  }

  tr:hover td {
    background-color: var(--egp-gray-50);
  }

  .status {
    background: var(--egp-gray-800);
    color: var(--egp-white);

    &.paid {
      background-color: var(--egp-green-dark);
    }

    &.pending {
      background-color: var(--egp-purple);
    }

    &.declined {
      background-color: var(--egp-red);
    }

    &.refunded {
      background-color: var(--egp-blue);
    }
  }
}
</style>