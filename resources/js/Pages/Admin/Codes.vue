<script setup>
import { computed, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
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

const { t } = useI18n()

const columns = [
  {
    label: t('admin.codes.columns.code'),
    field: 'code',
  },
  {
    label: t('admin.codes.columns.pickedup_at'),
    field: 'pickedup_at',
    type: 'date',
    dateInputFormat: 'yyyy-MM-dd HH:mm:ss',
    dateOutputFormat: 'EEE HH:mm',
    width: '200px',
  },
  {
    label: t('admin.codes.columns.used_at'),
    field: 'used_at',
    type: 'date',
    dateInputFormat: 'yyyy-MM-dd HH:mm:ss',
    dateOutputFormat: 'EEE HH:mm',
    width: '200px',
  },
  {
    label: t('admin.codes.columns.actions'),
    field: 'actions',
    width: '100px',
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
    <AdminNavigation>
      {{ $t('admin.codes.title') }}
    </AdminNavigation>

    <div class="sticky top-navbar z-50 bg-gray-200 border-b border-gray-300">
      <div class="container padded-x py-4 flex gap-4 items-center">  
        <QrScanner scanning="codes" @close="reload" />
        <span class="font-mono text-sm uppercase ms-auto md:ms-0">
          <strong>{{ codes.length }}</strong> {{ $t('admin.codes.stats.total') }},
          <strong>{{ totalPickedUp }}</strong> {{ $t('admin.codes.stats.picked_up') }},
          <strong>{{ totalUsed }}</strong> {{ $t('admin.codes.stats.used') }}
        </span>
        <InputButton @click="open" icon="ri:add-large-fill" variant="white" class="ms-auto">
          {{ $t('admin.codes.actions.create') }}
        </InputButton>
        <InputButton href="/admin/codes/print" target="_blank" variant="white" icon="ri:printer-line">
          {{ $t('admin.codes.actions.print') }}
        </InputButton>
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
          placeholder: $t('admin.codes.search.placeholder')
        }"
        :sort-options="{
          enabled: true,
        }"
      >
      <template #table-row="props">
        <span v-if="props.column.field == 'code'" class="font-mono">
          {{ props.formattedRow[props.column.field] }}
        </span>
        <span v-else-if="props.column.field == 'actions'" class="-m-1 block w-32">
          <InputButton v-if="!props.row.pickedup_at" @click="pickup(props.row.id)" size="sm" variant="green" flat block class="whitespace-nowrap">
            {{ $t('admin.codes.actions.activate') }}
          </InputButton>
          <HoverButton
            v-else
            @click="leavedown(props.row.id)"
            :variant="{ default: 'pine', hover: 'red' }"
            :label="{
              default: $t('admin.codes.actions.activated'),
              hover: $t('admin.codes.actions.deactivate')
            }"
            size="sm"
            flat
            block
            class="whitespace-nowrap"
          />
        </span>
        <span v-else-if="props.column.field == 'pickedup_at' && props.row.pickedup_at">
          <span class="bg-gray-100 text-green-dark py-[0.5em] px-2 -m-1 text-sm font-mono uppercase flex gap-2 items-center justify-between font-bold whitespace-nowrap">
            <Icon icon="ri:check-double-line" />
            {{ props.formattedRow[props.column.field] }}
          </span>
        </span>
        <span v-else-if="props.column.field == 'used_at' && props.row.used_at">
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