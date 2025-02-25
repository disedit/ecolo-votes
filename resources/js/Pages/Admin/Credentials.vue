<script setup>
import { computed, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { VueGoodTable } from 'vue-good-table-next'
import { Icon } from '@iconify/vue'
import { useModal } from 'vue-final-modal'
import AdminNavigation from '@/Components/Admin/Nav.vue'
import ButtonOptions from '@/Components/Inputs/ButtonOptions.vue'
import QrScanner from '@/Components/Admin/Credentials/QrScanner.vue'
import GrayLayout from '@/Layouts/GrayLayout.vue'
import GlobalCard from '@/Components/Global/Card.vue'
import Tooltip from '@/Components/Global/Tooltip.vue'
import HoverButton from '@/Components/Inputs/HoverButton.vue'
import PayModal from '@/Components/Admin/Credentials/Modals/Pay.vue'
import LogModal from '@/Components/Admin/Credentials/Modals/Log.vue'
import DetailsModal from '@/Components/Admin/Credentials/Modals/Details.vue'
import JotformSync from '@/Components/Admin/Credentials/JotformSync.vue'

defineOptions({ layout: GrayLayout })
const props = defineProps({
  attendees: { type: Object, required: true }
})

const columns = [
  {
    label: 'First Name',
    field: 'first_name',
  },
  {
    label: 'Last Name',
    field: 'last_name',
  },
  {
    label: 'Country',
    field: 'country',
  },
  {
    label: 'Role',
    field: 'type',
  },
  {
    label: 'Organisation',
    field: 'group',
  },
  {
    label: 'Votes',
    field: 'votes',
    tdClass: 'text-right',
    type: 'number'
  },
  {
    label: 'CheckedIn',
    field: 'checked_in',
    type: 'date',
    dateInputFormat: 'yyyy-MM-dd HH:mm:ss',
    dateOutputFormat: 'EEE HH:mm',
  },
  {
    label: 'Paid',
    field: 'paid'
  },
  {
    label: 'Actions',
    field: 'actions'
  },
]

const rows = computed(() => props.attendees.map(attendee => ({
  id: attendee.id,
  first_name: attendee.user.first_name,
  last_name: attendee.user.last_name,
  country: attendee.user.country,
  type: attendee.type.name,
  color: attendee.type.color,
  group: attendee.user.group_other ? `Other: ${attendee.user.group_other}` : attendee.user.group.name,
  checked_in: attendee.checked_in,
  first_checked_in: attendee.first_checked_in,
  paid: attendee.paid,
  confirmed: attendee.confirmed,
  votes: parseInt(attendee.votes)
})))

const totalCheckedIn = computed(() => props.attendees.filter(attendee => attendee.checked_in).length)

function checkIn(id, silent) {
  router.post(`/admin/credentials/${id}/check_in`, { silent }, { only: ['attendees'], preserveScroll: true })
  window.Echo.private(`Attendee.Status.${id}`).whisper('checked_in', { checked_in: true })
  window.Echo.private('Attendees.List').whisper('attendees_list_changed')
}

function checkInSilently(id) {
  checkIn(id, true)
}

function checkOut(id) {
  router.post(`/admin/credentials/${id}/check_out`, null, { only: ['attendees'], preserveScroll: true })
  window.Echo.private(`Attendee.Status.${id}`).whisper('checked_out', { checked_in: false })
  window.Echo.private('Attendees.List').whisper('attendees_list_changed')
}

onMounted(() => {
  window.Echo.private('Attendees.List')
    .listenForWhisper('attendees_list_changed', () => reload())
})

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

function openLog (attendeeId) {
  patchOptions({ component: LogModal, attrs: { attendeeId }})
  open()
}

function openDetails (attendeeId) {
  patchOptions({ component: DetailsModal, attrs: { attendeeId }})
  open()
}

function reload () {
  router.reload({ only: ['attendees'], preserveScroll: true })
}
</script>

<template>
  <Head title="Credentials" />
  <div class="container-xl">
    <AdminNavigation>Access control</AdminNavigation>

    <div class="sticky top-navbar z-50 bg-gray-200 border-b border-gray-300">
      <div class="container padded-x py-4 flex gap-4 items-center">  
        <QrScanner @close="reload" />
        <span class="font-mono text-sm uppercase ms-auto md:ms-0">
          <strong>{{ rows.length }}</strong> total, <strong>{{ totalCheckedIn }}</strong> checked in
        </span>
        <span class="text-sm hidden md:block ms-auto">
          <JotformSync @synced="reload" />
        </span>
      </div>
    </div>
    
    <GlobalCard title="Badges" edge>
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
        <span v-if="props.column.field == 'actions'" class="-m-1 block w-32">
          <ButtonOptions>
            <span v-if="!props.row.confirmed" class="text-sm text-gray-700 block p-2 text-center">
              Not conf.
            </span>
            <InputButton v-else-if="!props.row.paid" @click="pay(props.row.id)" size="sm" variant="yellow" flat block class="whitespace-nowrap">
              Pay
            </InputButton>
            <Tooltip v-else-if="!props.row.checked_in" :text="props.row.votes > 0 && !props.row.first_checked_in ? 'Will be notified' : null">
              <InputButton @click="checkIn(props.row.id)" size="sm" variant="green" flat block class="whitespace-nowrap">
                Check in
                <span v-if="props.row.votes > 0 && !props.row.first_checked_in">*</span>
              </InputButton>
            </Tooltip>
            <HoverButton
              v-else
              @click="checkOut(props.row.id)"
              :variant="{ default: 'pine', hover: 'red' }"
              :label="{ default: 'Checked In', hover: 'Check Out' }"
              size="sm"
              flat
              block
              class="whitespace-nowrap"
            />
            <template #options>
              <button v-if="props.row.confirmed && props.row.paid && !props.row.checked_in && props.row.votes > 0 && !props.row.first_checked_in" @click="checkInSilently(props.row.id)">
                <Icon icon="ri:check-double-line" />
                Check in silently
              </button>
              <button @click="openDetails(props.row.id)">
                <Icon icon="ri:user-add-line" />
                Details
              </button>
              <button v-if="props.row.first_checked_in" @click="openLog(props.row.id)">
                <Icon icon="ri:file-shield-2-line" />
                Access log
              </button>
            </template>
        </ButtonOptions>
        </span>
        <span v-else-if="props.column.field == 'type'" class="flex gap-2 items-center">
          <span :class="['attendee-color', `color-${props.row.color}`]" />
          {{ props.formattedRow[props.column.field] }}
        </span>
        <span v-else-if="props.column.field == 'paid'" class="flex justify-center">
          <Icon v-if="props.row.paid" icon="ri:check-fill" class="h-6 w-6 rounded-full flex items-center bg-green-dark text-white" />
        </span>
        <span v-else-if="props.column.field == 'checked_in' && !!props.row.checked_in">
          <span class="bg-gray-100 text-green-dark py-[0.5em] px-2 -m-1 text-sm font-mono uppercase flex gap-2 items-center justify-between font-bold whitespace-nowrap">
            <Icon icon="ri:check-double-line" />
            {{ props.formattedRow[props.column.field] }}
          </span>
        </span>
        <span v-else>
          {{ props.formattedRow[props.column.field] }}
        </span>
      </template>
      </VueGoodTable>
    </GlobalCard>
  </div>
</template>

<style lang="scss" scoped>
.attendee-color {
  height: 1.5rem;
  width: 1.5rem;
  background-color: var(--color);
  border-radius: 100%;
  flex-shrink: 0;
}
</style>