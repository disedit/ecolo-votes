<script setup>
import { Head, router } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'
import { VueGoodTable } from 'vue-good-table-next'
import { useModal } from 'vue-final-modal'
import AdminNavigation from '@/Components/Admin/Nav.vue'
import GrayLayout from '@/Layouts/GrayLayout.vue'
import GlobalCard from '@/Components/Global/Card.vue'
import ReallocateVotesModal from '@/Components/Admin/Votes/Modals/ReallocateVotes.vue'

defineOptions({ layout: GrayLayout })

const props = defineProps({
  voters: { type: Array, required: true }
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
    label: 'Organisation',
    field: 'organisation',
  },
  {
    label: 'Checked in',
    field: 'checked_in',
  },
  {
    label: 'Votes',
    field: 'votes',
  },
  {
    label: 'Actions',
    field: 'actions'
  },
]

const { open, close, patchOptions } = useModal({
  component: ReallocateVotesModal,
  attrs: {
    voters: props.voters,
    attendee: null,
    onClose () { close() },
    onReallocated () {
      reload()
      close()
    }
  }
})

function reallocateVotes (attendee) {
  patchOptions({
    component: ReallocateVotesModal,
    attrs: { attendee, voters: props.voters }
  })
  open()
}

function reload () {
  router.reload({ only: ['voters'], preserveScroll: true })
}
</script>

<template>
  <Head title="Tickets" />
  <div class="container-xl">
    <AdminNavigation>Allocated votes</AdminNavigation>

    <GlobalCard title="Allocated votes" edge>
      <VueGoodTable
        :columns="columns"
        :rows="voters"
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
          <InputButton @click="reallocateVotes(props.row)" flat block variant="yellow" size="sm">
            Reallocate
          </InputButton>
        </span>
        <span v-else-if="props.column.field == 'checked_in' && !!props.row.checked_in">
          <span class="bg-gray-100 text-green-dark py-[0.5em] px-2 -m-1 text-sm font-mono uppercase flex gap-2 items-center justify-between font-bold whitespace-nowrap">
            <Icon icon="ri:check-double-line" />
            {{ props.formattedRow[props.column.field] }}
          </span>
        </span>
      </template>
      </VueGoodTable>
    </GlobalCard>
  </div>
</template>
