<script setup>
import { ref, onMounted } from 'vue'
import { Icon } from '@iconify/vue'
import VoteResults from '@/Components/Votes/Results.vue'
import GlobalModal from '@/Components/Global/Modal.vue'

const props = defineProps({
  vote: { type: Object, required: true },
})

const fullVote = ref(null)

onMounted(async () => {
  const { data } = await axios.get(`/api/votes/${props.vote.id}/results`)
  fullVote.value = data
})

const emit = defineEmits(['close'])
</script>

<template>
  <GlobalModal @close="emit('close')" swipe-to-close="down">
    <template #title>
      <div>
        <h2 class="font-headline text-lg uppercase">{{ vote.name }}</h2>
        <p v-if="vote.subtitle" class="opacity-75">{{ vote.subtitle }}</p>
      </div>
    </template>

    <div v-if="!fullVote" class="text-lg text-gray-500 flex gap-2 items-center">
      <Icon icon="line-md:loading-loop" />
      Loading...
    </div>
    <div v-else-if="fullVote.open" class="text-lg text-green-dark flex gap-2 items-center">
      <Icon icon="line-md:loading-loop" />
      Vote ongoing...
    </div>
    <div v-else-if="!fullVote.closed_at">
      <p class="text-lg text-gray-500 flex gap-2 items-center">
        Vote pending...
      </p>

      <p class="mt-6 mb-3">
      Available options:
      </p>
      <div class="flex gap-2 flex-wrap">
        <span v-for="option in fullVote.options" :key="option.id" :class="['flex items-center gap-2 bg-gray-100 py-1 px-2 font-bold', `option-${option.name.replaceAll(' ', '-')}`]">
          <span class="circle block shrink-0 h-[1em] w-[1em] rounded-full" />
          {{ option.name }}
        </span>
      </div>
    </div>
    <div v-else-if="fullVote.abridgedResults">
      <VoteResults :vote="fullVote" />
    </div>
  </GlobalModal>
</template>

<style lang="scss" scoped>
.circle {
  background-color: var(--color, var(--egp-pink));
}
</style>
