<script setup>
import { computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { VueGoodTable } from 'vue-good-table-next'
import { useModal } from 'vue-final-modal'
import { formatCurrency } from '@/Composables/useCurrency.js'
import { formatDateTime } from '@/Composables/useDate.js'
import AdminNavigation from '@/Components/Admin/Nav.vue'
import GrayLayout from '@/Layouts/GrayLayout.vue'
import GlobalCard from '@/Components/Global/Card.vue'
import EditInvoiceModal from '@/Components/Admin/Invoicing/Modals/EditInvoice.vue'
import DeleteInvoiceModal from '@/Components/Admin/Invoicing/Modals/DeleteInvoice.vue'


defineOptions({ layout: GrayLayout })

const props = defineProps({
  payments: { type: Object, required: true }
})

const columns = [
  {
    label: '#',
    field: 'id',
  },
  {
    label: 'Date',
    field: 'created_at',
  },
  {
    label: 'Attendee',
    field: 'attendee',
  },
  {
    label: 'Invoice',
    field: 'invoice',
  },
  {
    label: 'Amount',
    field: 'amount',
    tdClass: 'text-right',
    thClass: 'text-right',
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

const rows = computed(() => props.payments.map(payment => ({
  ...payment,
  attendee: `${payment.attendee?.user?.first_name} ${payment.attendee?.user?.last_name}`,
  items: payment.receipt.cart.length,
  invoice: `/invoice/${payment.id}`
})))

const { open: openEditModal, close: closeEditModal, patchOptions: patchEditModal } = useModal({
  component: EditInvoiceModal,
  attrs: {
    invoiceId: null,
    onClose () { closeEditModal() },
    onRefresh () {
      router.reload({ preserveScroll: true })
      closeEditModal()
    }
  }
})

function editInvoice (invoiceId) {
  patchEditModal({attrs: { invoiceId }})
  openEditModal()
}

const { open: openDeleteModal, close: closeDeleteModal, patchOptions: patchDeleteModal } = useModal({
  component: DeleteInvoiceModal,
  attrs: {
    invoiceId: null,
    onClose () { closeDeleteModal() },
    onRefresh () {
      router.reload({ preserveScroll: true })
      closeDeleteModal()
    }
  }
})

function deleteInvoice (invoiceId) {
  patchDeleteModal({attrs: { invoiceId }})
  openDeleteModal()
}
</script>

<template>
  <Head title="Invoicing" />
  <div class="container-xl">
    <AdminNavigation>Invoicing</AdminNavigation>

    <GlobalCard title="Invoices" edge>
      <VueGoodTable
        :columns="columns"
        :rows="rows"
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
        <span v-if="props.column.field === 'actions'" class="flex gap-2 -m-1 w-28">
          <InputButton @click="editInvoice(props.row.id)" flat size="sm">Edit</InputButton>
          <InputButton @click="deleteInvoice(props.row.id)" flat variant="red" size="sm">Delete</InputButton>
        </span>
        <span v-else-if="props.column.field == 'created_at'">
          {{ formatDateTime(props.row.created_at) }}
        </span>
        <span v-else-if="props.column.field == 'amount'">
          {{ formatCurrency(props.row.amount) }}
        </span>
        <span v-else-if="props.column.field == 'attendee'">
          {{ props.row.attendee }}
          <span v-if="props.row.items > 1">+ {{ props.row.items - 1 }}</span>
        </span>
        <span v-else-if="props.column.field == 'invoice'">
          <a :href="props.row.invoice" download>Download</a>
        </span>
        <span v-else-if="props.column.field == 'status'">
          <span :class="['status', props.row.status]">{{ props.row.status }}</span>
          <span v-if="!props.row.checkout_session_id" class="status bg-gray-500 ms-2">Manual</span>
        </span>
      </template>
      </VueGoodTable>
    </GlobalCard>
  </div>
</template>

<style lang="scss" scoped>
.status {
  color: var(--egp-white);
  font-weight: bold;
  padding: .2em .4em;
  border-radius: .25em;
  text-transform: capitalize;

  &.paid {
    background-color: var(--egp-green);
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
</style>