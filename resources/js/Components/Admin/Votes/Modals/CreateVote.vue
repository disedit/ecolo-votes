<script setup>
import { reactive, ref, watch, onMounted } from 'vue'
import { useRemember } from '@inertiajs/vue3'
import { majorities } from '@/Components/Admin/Votes/majorities'
import GlobalModal from '@/Components/Global/Modal.vue'
import TextInput from '@/Components/Inputs/TextInput.vue'
import SelectInput from '@/Components/Inputs/SelectInput.vue'
import CheckboxInput from '@/Components/Inputs/CheckboxInput.vue'
import VoteOptions from '@/Components/Inputs/VoteOptions.vue'

const props = defineProps({
  cloneVote: { type: Object, default: null }
})

const emit = defineEmits(['close', 'refresh', 'loadOptions'])

const form = reactive({
  name: '',
  subtitle: '',
  type: 'yesno',
  secret: 0,
  options: [
    { name: '', description: '', gender: null, enabled: true }
  ],
  abstain: true,
  majority: 'absolute',
  open_immediately: true
})

watch(() => form.type, (newType) => {
  form.secret = newType !== 'yesno'
})

const timer = useRemember({
  enabled: true,
  time: '00:30'
}, 'Vote/Timer')

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
    form.secret = props.cloneVote.secret
    form.majority = props.cloneVote.majority
    form.options = props.cloneVote.options
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
        label="Title"
        required
        v-model="form.name"
        input-class="text-lg"
        ref="voteNameInput"
      />
      <TextInput
        name="subtitle"
        label="Subtitle"
        placeholder="For example, 2nd round"
        v-model="form.subtitle"
      />
      <div class="flex gap-4">
        <SelectInput
          name="voteType"
          label="Type of vote"
          required
          :options="[
            { value: 'yesno', label: 'Yes / No / Abstain' },
            { value: 'options', label: 'Candidates / Custom options' },
          ]"
          v-model="form.type"
          class="grow"
        />
        <SelectInput
          name="majority"
          label="Type of majority"
          required
          :options="majorityOptions"
          class="grow"
          v-model="form.majority"
        />
      </div>
      <VoteOptions
        v-if="form.type === 'options'"
        v-model="form.options"
        v-model:abstain="form.abstain"
        @load-options="emit('loadOptions')"
      />
      <CheckboxInput
        name="secretVote"
        label="Vote is secret"
        v-model="form.secret"
      />
      <CheckboxInput
        name="openVote"
        label="Open vote immediately upon creation"
        v-model="form.open_immediately"
      />
      <div v-if="form.open_immediately" class="flex items-center gap-4 -mt-1">
        <CheckboxInput
          name="timer"
          label="Set a timer"
          v-model="timer.enabled"
        />
        <TextInput
          type="time"
          name="timer"
          label="Timer"
          required
          label-sr-only
          v-model="timer.time"  
          input-class="p-1 w-32"
          :disabled="!timer.enabled"
        />
      </div>
      <ul v-if="errors" class="text-red font-bold mb-4">
        <li v-for="error in errors.errors">{{ error[0] }}</li>
      </ul>
      <InputButton type="submit" flat>
        Create vote
      </InputButton>
    </form>
  </GlobalModal>
</template>
