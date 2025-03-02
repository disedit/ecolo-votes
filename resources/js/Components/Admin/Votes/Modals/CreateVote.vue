<script setup>
import { reactive, ref, watch, onMounted } from 'vue'
import { majorities } from '@/Components/Admin/Votes/majorities'
import GlobalModal from '@/Components/Global/Modal.vue'
import TextInput from '@/Components/Inputs/TextInput.vue'
import SelectInput from '@/Components/Inputs/SelectInput.vue'
import CheckboxInput from '@/Components/Inputs/CheckboxInput.vue'
import VoteOptions from '@/Components/Inputs/VoteOptions.vue'

const props = defineProps({
  cloneVote: { type: Object, default: null },
  regions: { type: Array, required: true }
})

const emit = defineEmits(['close', 'refresh', 'loadOptions'])

const form = reactive({
  name: '',
  subtitle: '',
  type: 'yesno',
  options: [
    { name: '', description: '', gender: null, region: null, enabled: true }
  ],
  abstain: true,
  no: true,
  majority: '50',
  with_abstentions: true,
  relative_to: 'turnout',
  max_votes: 1,
  secret: false,
  open_immediately: true
})

watch(() => form.type, (newType) => {
  form.secret = newType !== 'yesno'
})

const errors = ref(null)
const submitting = ref(false)
async function createVote () {
  submitting.value = true
  try {
    const { data } = await window.axios.post('/api/admin/votes/create', form)
    emit('refresh')
  } catch (e) {
    errors.value = e.response.data
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  if (props.cloneVote) {
    form.type = 'options'
    form.name = props.cloneVote.name
    form.subtitle = props.cloneVote.subtitle
    form.majority = props.cloneVote.majority
    form.with_abstentions = props.cloneVote.with_abstentions
    form.relative_to = props.cloneVote.relative_to
    form.max_votes = props.cloneVote.max_votes
    form.secret = props.cloneVote.secret
    form.options = props.cloneVote.options
  } else {
    form.name = ''
    form.subtitle = ''
    form.type = 'yesno'
    form.options = [{ name: '', description: '', gender: null, region: null, enabled: true }]
    form.abstain = true
    form.no = true
    form.majority = '50'
    form.with_abstentions = true
    form.relative_to = 'turnout'
    form.max_votes = 1
    form.secret = false
    form.open_immediately = true
  }
})

const majorityOptions = Object.entries(majorities).map(([value, label]) => ({ value, label }))

const voteNameInput = ref(null)
function focusVoteNameInput () {
  voteNameInput.value.$refs.input.focus()
}
</script>

<template>
  <GlobalModal :width="1050" @close="emit('close')" @opened="focusVoteNameInput">
    <template #title>
      <h1>Create new vote</h1>
    </template>
    <form @submit.prevent="createVote" class="flex flex-col gap-4 text-rbase">
      <TextInput
        name="name"
        :label="$t('admin.votes.create.fields.title')"
        required
        v-model="form.name"
        input-class="text-lg"
        ref="voteNameInput"
      />
      <TextInput
        name="subtitle"
        :label="$t('admin.votes.create.fields.subtitle')"
        :placeholder="$t('admin.votes.create.fields.subtitle_placeholder')"
        v-model="form.subtitle"
      />
      <div class="grid grid-cols-3 gap-4">
        <SelectInput
          name="voteType"
          :label="$t('admin.votes.create.fields.type')"
          required
          :options="[
            { value: 'yesno', label: $t('admin.votes.create.fields.types.yesno') },
            { value: 'options', label: $t('admin.votes.create.fields.types.options') },
          ]"
          v-model="form.type"
        />
        <SelectInput
          name="majority"
          :label="$t('admin.votes.create.fields.majority')"
          required
          :options="majorityOptions"
          v-model="form.majority"
        />
        <SelectInput
          v-if="form.majority !== 'simple'"
          name="voteType"
          :label="$t('admin.votes.create.fields.abstentions')"
          required
          :options="[
            { value: true, label: $t('admin.votes.create.fields.abstention_options.with') },
            { value: false, label: $t('admin.votes.create.fields.abstention_options.without') },
          ]"
          v-model="form.with_abstentions"
        />
      </div>
      <div v-if="form.type === 'options'" class="grid grid-cols-3 gap-4">
        <TextInput
          name="maxVotes"
          :label="$t('admin.votes.create.fields.max_votes')"
          type="number"
          min="1"
          v-model="form.max_votes"
          required
        />
        <SelectInput
          v-if="form.majority !== 'simple'"
          name="voteType"
          :label="$t('admin.votes.create.fields.relative_to')"
          required
          :options="[
            { value: 'turnout', label: $t('admin.votes.create.fields.relative_to_options.turnout') },
            { value: 'votes_cast', label: $t('admin.votes.create.fields.relative_to_options.votes_cast') },
          ]"
          v-model="form.relative_to"
          class="col-span-2"
        />
      </div>
      <VoteOptions
        v-if="form.type === 'options'"
        v-model="form.options"
        v-model:no="form.no"
        v-model:abstain="form.abstain"
        :regions="regions"
        @load-options="emit('loadOptions')"
      />
      <CheckboxInput
        name="openVote"
        :label="$t('admin.votes.create.fields.secret')"
        v-model="form.secret"
      />
      <CheckboxInput
        name="openVote"
        :label="$t('admin.votes.create.fields.open_immediately')"
        v-model="form.open_immediately"
      />
      <ul v-if="errors" class="text-red font-bold mb-4">
        <li v-for="error in errors.errors">{{ error[0] }}</li>
      </ul>
      <InputButton type="submit" flat>
        {{ $t('admin.votes.actions.create') }}
      </InputButton>
    </form>
  </GlobalModal>
</template>
