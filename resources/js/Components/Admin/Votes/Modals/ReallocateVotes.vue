<script setup>
import { ref, computed } from 'vue'
import GlobalModal from '@/Components/Global/Modal.vue'
import SelectInput from '@/Components/Inputs/SelectInput.vue'
const emit = defineEmits(['close', 'reallocated'])

const props = defineProps({
  voters: { type: Array, required: true },
  attendee: { type: Object, required: true }
})

const delegates = computed(() => {
  return props.voters.filter(voter => voter.organisation === props.attendee.organisation && voter.id !== props.attendee.id).map(voter => ({
    label: `${voter.attendee} - ${voter.votes} votes`,
    value: voter
  }))
})

const reallocateTo = ref(null)
const errors = ref(null)

async function submit() {
  try {
    await window.axios.post('/api/admin/voters/reallocate', {
      reallocate_from: props.attendee.id,
      reallocate_to: reallocateTo.value.id
    })
    emit('reallocated')
  } catch (error) {
    errors.value = error.response.data
  }
}
</script>

<template>
  <GlobalModal :width="650" @close="emit('close')">
    <template #title>
      <h1>Reallocate votes</h1>
    </template>
    <div v-if="delegates.length === 0">
      There are no other delegates in the member party to reallocate votes to.
    </div>
    <form v-else @submit.prevent="submit" class="flex flex-col gap-4 mt-6">
      <p>Reallocate <strong>{{ attendee.votes }} vote(s)</strong> from <strong>{{ attendee.attendee }}</strong> to</p>
      <SelectInput
        label="Reallocate votes"
        label-sr-only
        name="from"
        :options="delegates"
        required
        v-model="reallocateTo"
      />
      <div v-if="reallocateTo">
        <p>These will be the allocated votes after the change:</p>
        <div class="grid grid-cols-2 grid-rows-[auto_auto] my-4 gap-x-4">
          <div class="border-2 border-gray-200 grid grid-rows-subgrid row-span-2">
            <h3 class="font-bold bg-gray-200 py-1 px-2">
              {{ attendee.attendee }}
            </h3>
            <div class="text-lg p-2">
              0 votes
              <span class="text-red text-base">
                ({{ attendee.votes * -1 }})
              </span>
            </div>
          </div>
          <div class="border-2 border-gray-200 grid grid-rows-subgrid row-span-2">
            <h3 class="font-bold bg-gray-200 py-1 px-2">
              {{ reallocateTo.attendee }}
            </h3>
            <div class="text-lg p-2">
              {{ reallocateTo.votes + attendee.votes }} votes
              <span class="text-green-dark text-base">
                (+{{ attendee.votes }})
              </span>
            </div>
          </div>
        </div>
      </div>
      <div v-if="errors" class="text-red font-bold">
        {{ errors.message }}
      </div>
      <InputButton variant="yellow" flat>
        Reallocate
      </InputButton>
    </form>
  </GlobalModal>
</template>
