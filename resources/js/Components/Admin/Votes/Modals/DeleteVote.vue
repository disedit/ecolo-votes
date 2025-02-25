<script setup>
import GlobalModal from '@/Components/Global/Modal.vue'

const props = defineProps({
  vote: { type: Object, required: true }
})

const emit = defineEmits(['close', 'refresh'])

async function submit () {
  const { data } = await window.axios.post('/api/admin/votes/' + props.vote.id + '/delete')
  emit('refresh')
}
</script>

<template>
  <GlobalModal :width="600" @close="emit('close')">
    <template #title>
      <h1 class="text-red">Delete vote</h1>
    </template>
    <form @submit.prevent="submit" class="flex flex-col gap-4 mt-6">
      <div class="bg-gray-200 p-4">
        {{ vote.name }} <span v-if="vote.subtitle">({{ vote.subtitle }})</span>
      </div>
      <p>
        <strong>
          Are you sure you want to delete this vote?
          All ballots cast will also be removed.
        </strong>
      </p>
      <div class="flex mt-6 justify-between">
        <InputButton type="submit" variant="red" flat>
          Delete vote
        </InputButton>
        <InputButton type="button" variant="gray" @click="emit('close')" flat>
          Cancel
        </InputButton>
      </div>
    </form>
  </GlobalModal>
</template>
