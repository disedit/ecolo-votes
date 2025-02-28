<script setup>
import { computed, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { VueGoodTable } from 'vue-good-table-next'
import { Icon } from '@iconify/vue'
import { useModal } from 'vue-final-modal'
import AdminNavigation from '@/Components/Admin/Nav.vue'
import QrScanner from '@/Components/Admin/Credentials/QrScanner.vue'
import GrayLayout from '@/Layouts/GrayLayout.vue'
import GlobalCard from '@/Components/Global/Card.vue'
import HoverButton from '@/Components/Inputs/HoverButton.vue'
import CreateCodes from '@/Components/Admin/Codes/Modals/CreateCodes.vue'

defineOptions({ layout: GrayLayout })
const props = defineProps({
  codes: { type: Array, required: true }
})

const columns = [
  {
    label: 'Code',
    field: 'code',
  },
  {
    label: 'Picked up at',
    field: 'pickedup_at',
    type: 'date',
    dateInputFormat: 'yyyy-MM-dd HH:mm:ss',
    dateOutputFormat: 'EEE HH:mm',
  },
  {
    label: 'Used at',
    field: 'used_at',
    type: 'date',
    dateInputFormat: 'yyyy-MM-dd HH:mm:ss',
    dateOutputFormat: 'EEE HH:mm',
  },
  {
    label: 'Actions',
    field: 'actions'
  },
]

const totalPickedUp = computed(() => props.codes.filter(code => code.pickedup_at).length)
const totalUsed = computed(() => props.codes.filter(code => code.used_at).length)

function pickup(id) {
  router.post(`/admin/codes/${id}/pickup`, null, { only: ['codes'], preserveScroll: true })
  window.Echo.private('Codes.List').whisper('codes_list_changed')
}

function leavedown(id) {
  router.post(`/admin/codes/${id}/leavedown`, null, { only: ['codes'], preserveScroll: true })
  window.Echo.private('Codes.List').whisper('codes_list_changed')
}

onMounted(() => {
  window.Echo.private('Codes.List')
    .listenForWhisper('codes_list_changed', () => reload())
})

function reload () {
  router.reload({ only: ['codes'], preserveScroll: true })
}

/* Vote functions */
const { open, close, patchOptions } = useModal({
  component: CreateCodes,
  attrs: {
    onClose () { close() },
    onRefresh () {
      window.Echo.private('Codes.List').whisper('codes_list_changed')
      reload()
      close()
    }
  }
})
</script>

<template>
  <Head title="Voting Codes" />
  <div class="container-xl">
    <AdminNavigation>Vote Codes</AdminNavigation>

    <div class="sticky top-navbar z-50 bg-gray-200 border-b border-gray-300">
      <div class="container padded-x py-4 flex gap-4 items-center">  
        <QrScanner scanning="codes" @close="reload" />
        <InputButton @click="open" icon="ri:add-large-fill">
          Create codes
        </InputButton>
        <span class="font-mono text-sm uppercase ms-auto md:ms-0">
          <strong>{{ codes.length }}</strong> total, <strong>{{ totalPickedUp }}</strong> picked up, <strong>{{ totalUsed }}</strong> used
        </span>
      </div>
    </div>
    
    <GlobalCard title="Vote Codes" edge>
      <VueGoodTable
        :columns="columns"
        :rows="codes"
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
          <InputButton v-if="!props.row.pickedup_at" @click="pickup(props.row.id)" size="sm" variant="green" flat block class="whitespace-nowrap">
            Activate
          </InputButton>
          <HoverButton
            v-else
            @click="leavedown(props.row.id)"
            :variant="{ default: 'pine', hover: 'red' }"
            :label="{ default: 'Activate', hover: 'Deactivate' }"
            size="sm"
            flat
            block
            class="whitespace-nowrap"
          />
        </span>
        <span v-else-if="props.column.field == 'pickedup_at' && !!props.row.pickedup_at">
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