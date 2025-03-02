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
      <h1 class="text-red">
        {{ $t('admin.votes.delete.title') }}
      </h1>
    </template>
    <form @submit.prevent="submit" class="flex flex-col gap-4 mt-6">
      <div class="bg-gray-200 p-4">
        {{ vote.name }} <span v-if="vote.subtitle">({{ vote.subtitle }})</span>
      </div>
      <p>
        <strong>
          {{ $t('admin.votes.delete.warning') }}
        </strong>
      </p>
      <div class="flex mt-6 justify-between">
        <InputButton type="submit" variant="red" flat>
          {{ $t('admin.votes.delete.button') }}
        </InputButton>
        <InputButton type="button" variant="gray" @click="emit('close')" flat>
          {{ $t('global.cancel') }}
        </InputButton>
      </div>
    </form>
  </GlobalModal>
</template>
