<script setup>
import { computed, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { VueGoodTable } from 'vue-good-table-next'
import { Icon } from '@iconify/vue'
import { formatDateTime } from '@/Composables/useDate.js'
import AdminNavigation from '@/Components/Admin/Nav.vue'
import GrayLayout from '@/Layouts/GrayLayout.vue'
import GlobalCard from '@/Components/Global/Card.vue'


defineOptions({ layout: GrayLayout })

const props = defineProps({
  extras: { type: Object, required: true }
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
    label: 'Email',
    field: 'email',
  },
  {
    label: 'Signed up',
    field: 'signed_up',
  },
  {
    label: 'Actions',
    field: 'actions'
  },
]
</script>

<template>
  <Head title="Extras" />
  <div class="container-xl">
    <AdminNavigation>Extras</AdminNavigation>

    <GlobalCard
      v-for="extra in extras"
      :key="extra.id"
      :title="extra.name"
      edge
    >
      <VueGoodTable
        :columns="columns"
        :rows="extra.list"
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
        <span v-if="props.column.field === 'actions'" class="-m-1 block w-28">
          <InputButton variant="red" size="sm" flat>Delete</InputButton>
        </span>
        <span v-if="props.column.field === 'signed_up'" class="-m-1 block w-28 whitespace-nowrap">
          {{ formatDateTime(props.row.signed_up) }}
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
}
</style>