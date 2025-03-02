<script setup>
import { onMounted, ref } from 'vue'
import GlobalModal from '@/Components/Global/Modal.vue'
import VoteResults from '@/Components/Admin/Votes/VoteResults.vue'

const props = defineProps({
  voteId: { type: Number, required: true }
})

const emit = defineEmits(['close'])

const vote = ref(null)
onMounted(async () => {
  const { data } = await window.axios.get(`/api/admin/votes/${props.voteId}`)
  vote.value = data
})
</script>

<template>
  <GlobalModal v-if="vote" :width="1200" @close="emit('close')" edge>
    <template #title>
      <h1 class="p-4">
        {{ $t('results.page.title') }}
      </h1>
    </template>
    <VoteResults :vote="vote" />
  </GlobalModal>
</template>
