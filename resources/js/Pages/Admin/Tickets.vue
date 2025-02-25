<script setup>
import { computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { VueGoodTable } from 'vue-good-table-next'
import { useModal } from 'vue-final-modal'
import { formatCurrency } from '@/Composables/useCurrency.js'
import AdminNavigation from '@/Components/Admin/Nav.vue'
import GrayLayout from '@/Layouts/GrayLayout.vue'
import GlobalCard from '@/Components/Global/Card.vue'
import PayModal from '@/Components/Admin/Credentials/Modals/Pay.vue'
import JotformSync from '@/Components/Admin/Credentials/JotformSync.vue'

defineOptions({ layout: GrayLayout })

const props = defineProps({
  tickets: { type: Object, required: true }
})

const columns = [
  {
    label: '#',
    field: 'id',
  },
  {
    label: 'Attendee',
    field: 'attendee',
  },
  {
    label: 'Role',
    field: 'role.name',
  },
  {
    label: 'Organisation',
    field: 'organisation',
  },
  {
    label: 'Links',
    field: 'links',
  },
  {
    label: 'Applicable fees',
    field: 'fees',
  },
  {
    label: 'Payment',
    field: 'info',
  },
  {
    label: 'Status',
    field: 'status',
  },
  {
    label: 'Actions',
    field: 'actions'
  },
]

const { open, close, patchOptions } = useModal({
  component: PayModal,
  attrs: {
    attendeeId: null,
    onClose () { close() },
    onRefresh () {
      reload()
      close()
    }
  }
})

function pay (attendeeId) {
  patchOptions({ component: PayModal, attrs: { attendeeId }})
  open()
}

function reload () {
  router.reload({ only: ['tickets'], preserveScroll: true })
}

const totalPaid = computed(() => props.tickets.filter(ticket => ticket.paid).length)
const totalUnconfirmed = computed(() => props.tickets.filter(ticket => !ticket.confirmed).length)
const totalRemaining = computed(() => props.tickets.length - totalPaid.value)
const totalToBePaid = computed(() => totalRemaining.value - totalUnconfirmed.value)
</script>

<template>
  <Head title="Tickets" />
  <div class="container-xl">
    <AdminNavigation>Tickets</AdminNavigation>
    <div class="sticky top-navbar z-50 bg-gray-200 border-b border-gray-300">
      <div class="container padded-x py-4 flex gap-4 items-center">
        <span class="font-mono text-sm uppercase ms-auto md:ms-0">
          <strong>{{ tickets.length }}</strong> tickets total, <strong>{{ totalPaid }}</strong> issued, <strong>{{ totalRemaining }}</strong> to be issued (<strong>{{ totalUnconfirmed }}</strong> unconfirmed, <strong>{{ totalToBePaid }}</strong> to be paid)
        </span>
        <span class="text-sm hidden md:block ms-auto">
          <JotformSync @synced="reload" />
        </span>
      </div>
    </div>

    <GlobalCard title="Tickets" edge>
      <VueGoodTable
        :columns="columns"
        :rows="tickets"
        :search-options="{
          enabled: true,
          trigger: 'enter',
          skipDiacritics: true,
          placeholder: 'Search this table'
        }"
        :sort-options="{
          enabled: true,
        }"
      >
      <template #table-row="props">
        <span v-if="props.column.field === 'actions'" class="flex gap-2 w-28">
          <InputButton v-if="!props.row.paid && props.row.confirmed" @click="pay(props.row.id)" flat block variant="yellow" size="sm">
            Pay
          </InputButton>
        </span>
        <span v-else-if="props.column.field == 'role.name'" class="flex gap-2 items-center">
          <span :class="['h-[1.5em] w-[1.5em] bg-[var(--color)] shrink-0 rounded-full', `color-${props.row.role.color}`]" />
          {{ props.row.role.name }}
        </span>
        <span v-else-if="props.column.field == 'fees'" class="flex flex-wrap gap-2">
          <span v-for="fee in props.row.fees" :key="fee.id" class="bg-gray-100 py-1 px-2">
            {{ formatCurrency(fee.amount) }}
          </span>
        </span>
        <span v-else-if="props.column.field == 'info'" class="flex flex-wrap gap-2">
          <span
            v-for="payment in props.row.payments"
            :key="payment.id"
            :class="[
              'py-1 px-2 text-white font-bold',
              {
                'bg-green-dark': payment.status === 'paid',
                'bg-purple': payment.status === 'pending',
                'bg-blue': payment.status === 'refunded',
                'bg-red': payment.status === 'declined'
              }
            ]"
          >
            {{ formatCurrency(payment.amount) }}
          </span>
          <span v-if="props.row.fees.length === 0" class="bg-blue text-white py-1 px-2 font-bold">
            Free
          </span>
        </span>
        <span v-else-if="props.column.field == 'links'">
          <span v-if="props.row.subdelegates">
            <h4 class="font-bold text-sm text-gray-800">Paying for:</h4>
            <ul class="list-disc">
              <li v-for="delegate in props.row.subdelegates" :key="delegate.email" :class="['ms-4', {'text-red font-bold': !delegate.linked}]">
                {{ delegate.first_name }} {{ delegate.last_name }}
              </li>
            </ul>
          </span>
          <span v-if="props.row.registered_by" class="opacity-60">
            <h4 class="font-bold text-sm text-gray-800">Paid by:</h4>
            {{ props.row.registered_by.first_name }} {{ props.row.registered_by.last_name }}
          </span>
        </span>
        <span v-else-if="props.column.field == 'status'" class="flex">
          <span v-if="!props.row.confirmed" class="bg-gray-300 text-gray-900 py-1 px-2 font-bold">
            Unconfirmed
          </span>
          <span v-else-if="!props.row.notifiable" class="bg-gray-300 text-gray-900 py-1 px-2 font-bold">
            Not notified
          </span>
          <span v-else-if="props.row.ticket_issued" class="bg-green-dark text-white py-1 px-2 font-bold">
            Issued
          </span>
        </span>
      </template>
      </VueGoodTable>
    </GlobalCard>
  </div>
</template>
