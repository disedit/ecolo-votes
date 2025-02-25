<script setup>
import { ref, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { VueGoodTable } from 'vue-good-table-next'
import { Icon } from '@iconify/vue'
import { useModal } from 'vue-final-modal'
import { formatWeekTime } from '@/Composables/useDate.js'
import { majorities } from '@/Components/Admin/Votes/majorities.js'
import AdminNavigation from '@/Components/Admin/Nav.vue'
import GrayLayout from '@/Layouts/GrayLayout.vue'
import GlobalCard from '@/Components/Global/Card.vue'
import ButtonOptions from '@/Components/Inputs/ButtonOptions.vue'
import CreateVote from '@/Components/Admin/Votes/Modals/CreateVote.vue'
import DeleteVote from '@/Components/Admin/Votes/Modals/DeleteVote.vue'
import VoteDetails from '@/Components/Admin/Votes/Modals/VoteDetails.vue'
import OngoingVote from '@/Components/Admin/Votes/OngoingVote.vue'

defineOptions({ layout: GrayLayout })

const props = defineProps({
  votes: { type: Array, required: true },
  ongoing: { type: Object, default: null }
})

const columns = [
  {
    label: '#',
    field: 'id',
    width: '50px'
  },
  {
    label: 'Name',
    field: 'name',
  },
  {
    label: 'Majority',
    field: 'majority',
    width: '200px'
  },
  {
    label: 'Result',
    field: 'result',
    width: '200px'
  },
  {
    label: 'Closed at',
    field: 'closed_at',
    dateInputFormat: 'yyyy-MM-dd HH:mm:ss',
    dateOutputFormat: 'EEE HH:mm',
    width: '140px'
  },
  {
    label: 'Actions',
    field: 'actions',
    width: '120px'
  },
]

const hideRecentlyClosedVote = ref(false)

/* Vote functions */
const { open, close, patchOptions } = useModal({
  component: CreateVote,
  attrs: {
    onClose () { close() },
    onRefresh () {
      window.Echo.private('Votes').whisper('refreshVotes')
      reload()
      close()
    }
  }
})

async function openVote(id) {
  await window.axios.post(`/api/admin/votes/${id}/open`)
  window.Echo.private('Votes').whisper('refreshVotes')
  reload()
}

async function openDebate(id) {
  await window.axios.post(`/api/admin/votes/${id}/debate`)
  window.Echo.private('Votes').whisper('refreshVotes')
  reload()
}

async function closeDebate(id) {
  await window.axios.post(`/api/admin/votes/${id}/debate/close`)
  window.Echo.private('Votes').whisper('refreshVotes')
  reload()
}

async function closeVote(id) {
  await window.axios.post(`/api/admin/votes/${id}/close`)
  window.Echo.private('Votes').whisper('refreshVotes')
  reload()
}

function createVote() {
  patchOptions({ component: CreateVote })
  open()
}

async function cloneVote(voteId) {
  const { data } = await window.axios.get(`/api/admin/votes/${voteId}`)
  const options = data.options.filter(option => !option.is_abstain)
        .map(option => ({
          name: option.name,
          description: option.description,
          gender: option.gender,
          enabled: true 
        }))
  patchOptions({
    component: CreateVote,
    attrs: {
      cloneVote: {
        name: data.name,
        subtitle: data.subtitle,
        majority: data.majority,
        secret: data.secret,
        options
      }
    }
  })
  open()
}

function deleteVote(vote) {
  patchOptions({ component: DeleteVote, attrs: { vote } })
  open()
}

function openDetails(voteId) {
  patchOptions({ component: VoteDetails, attrs: { voteId } })
  open()
}

function reopenVote(id) {
  if (confirm('Reopening a vote will recalculate vote allocation and may alter the results.') === true) {
    openVote(id)
  }
}

function reload() {
  router.reload({ preserveScroll: true, only: ['ongoing', 'votes'] })
}

onMounted(() => {
  window.Echo.private('Votes').listenForWhisper('refreshVotes', () => {
    reload()
  })
})

/* Reorder votes */
const reordering = ref(false)
function reorder (voteId) {
  reordering.value = voteId
}
function move(voteId, move) {
  router.post(`/admin/votes/${voteId}/reorder`, { move }, { preserveScroll: true })
}
</script>

<template>
  <Head title="Votes" />
  <div class="container-xl">
    <AdminNavigation>Vote manager</AdminNavigation>

    <div class="sticky top-navbar z-50 bg-gray-200 border-b border-gray-300">
      <div class="container padded-x py-4 flex items-center gap-2">  
        <InputButton @click="createVote" icon="ri:add-large-fill">
          Create vote
        </InputButton>
      </div>
    </div>

    <OngoingVote
      v-if="(ongoing && ongoing.open) || (ongoing && !ongoing.open && !hideRecentlyClosedVote)"
      live
      :vote="ongoing"
      @close="closeVote"
      @hide="hideRecentlyClosedVote = true"
      @reset="hideRecentlyClosedVote = false"
    />

    <GlobalCard title="Votes" edge>
      <VueGoodTable
        :columns="columns"
        :rows="votes"
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
        <span v-if="props.column.field == 'actions' && !reordering" class="-m-1 block w-32">
          <ButtonOptions>
            <InputButton v-if="props.row.closed_at && props.row.debate" @click="closeDebate(props.row.id)" size="sm" variant="red" flat block class="whitespace-nowrap">
              Stop
            </InputButton>
            <InputButton v-else-if="props.row.closed_at && !props.row.open" @click="openDetails(props.row.id)" size="sm" variant="gray" flat block class="whitespace-nowrap">
              Results
            </InputButton>
            <InputButton v-else-if="!props.row.open && !props.row.debate" @click="openDebate(props.row.id)" size="sm" variant="yellow" flat block class="whitespace-nowrap">
              Next Up
            </InputButton>
            <InputButton v-else-if="!props.row.open && props.row.debate" @click="openVote(props.row.id)" size="sm" variant="green" flat block class="whitespace-nowrap">
              Vote
            </InputButton>
            <InputButton v-else @click="closeVote(props.row.id)" size="sm" variant="red" flat block class="whitespace-nowrap">
              Close
            </InputButton>
            <template #options>
              <button @click="openVote(props.row.id)" v-if="!props.row.debate && !props.row.closed_at">
                <Icon icon="ri:hand" />
                Open vote
              </button>
              <button @click="closeDebate(props.row.id)" v-if="props.row.debate">
                <Icon icon="ri:stop-circle-line" />
                {{ props.row.closed_at ? 'Stop highlighting' : 'Close debate' }}
              </button>
              <button @click="openDebate(props.row.id)" v-if="!props.row.debate && props.row.closed_at">
                <Icon icon="ri:slideshow-3-line" />
                Highlight
              </button>
              <button @click="reopenVote(props.row.id)" v-if="!!props.row.closed_at">
                <Icon icon="ri:hand" />
                Reopen vote
              </button>
              <button @click="cloneVote(props.row.id)" v-if="props.row.type === 'options'">
                <Icon icon="ri:file-copy-2-line" />
                Clone this vote
              </button>
              <button @click="reorder(props.row.id)">
                <Icon icon="nrk:reorder" />
                Reorder
              </button>
              <button @click="deleteVote(props.row)" class="text-red">
                <Icon icon="ri:delete-bin-2-line" />
                Delete vote
              </button>
            </template>
        </ButtonOptions>
        </span>
        <span v-if="props.column.field == 'actions' && reordering == props.row.id" class="-m-1 flex gap-2 w-32">
          <InputButton @click="move(props.row.id, 'up')" title="Move up" flat variant="gray">
            <Icon icon="ri:arrow-up-line" />
          </InputButton>
          <InputButton @click="move(props.row.id, 'down')" title="Move down" flat variant="gray">
            <Icon icon="ri:arrow-down-line" />
          </InputButton>
          <InputButton @click="reordering = null" title="Finish reordering" flat variant="green">
            <Icon icon="ri:check-fill" />
          </InputButton>
        </span>
        <span v-else-if="props.column.field == 'result'" class="flex gap-2 items-center">
          <template v-if="props.row.winner">
            <span :class="['result-color', `result-${props.row.winner.name}`]" />
            {{ props.row.winner.name }}
          </template>
          <template v-else-if="props.row.closed_at">
            <span :class="['result-color', 'result-None']" />
            <span class="text-gray-600">No winner</span>
          </template>
        </span>
        <span v-else-if="props.column.field == 'name'" class="flex gap-2 items-center">
          {{ props.formattedRow[props.column.field] }}
          <span v-if="props.row.subtitle" class="text-gray-600">({{ props.row.subtitle }})</span>
        </span>
        <span v-else-if="props.column.field == 'majority'">
          {{ majorities[props.formattedRow[props.column.field]] }}
        </span>
        <span v-else-if="props.column.field == 'closed_at'">
          <span v-if="props.row.closed_at" class="bg-gray-100 text-green-dark py-[0.5em] px-2 -m-1 text-sm font-mono uppercase flex gap-2 items-center justify-between font-bold whitespace-nowrap">
            <Icon icon="ri:check-double-line" />
            {{ formatWeekTime(props.formattedRow[props.column.field]) }}
          </span>
          <span v-else-if="props.row.debate" class="text-center block text-green-dark font-bold">
            Debating...
          </span>
          <span v-else-if="props.row.open" class="text-center block text-green-dark font-bold">
            Ongoing...
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
.result-color {
  height: 1.5em;
  width: 1.5em;
  border-radius: 100%;
  background-color: var(--egp-purple);
  flex-shrink: 0;

  &.result-Yes {
    background-color: var(--egp-green);
  }

  &.result-No {
    background-color: var(--egp-red);
  }

  &.result-None {
    background-color: var(--egp-gray-400);
  }
}
</style>