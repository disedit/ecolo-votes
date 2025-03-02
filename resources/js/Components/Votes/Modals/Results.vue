<script setup>
import { ref, onMounted } from 'vue'
import { Icon } from '@iconify/vue'
import { Link } from '@inertiajs/vue3'
import { optionClasses } from '@/Composables/useColors.js'
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
      {{ $t('global.loading') }}
    </div>
    <div v-else-if="fullVote.open">
      <div class="text-lg text-green-dark flex gap-2 items-center">
        <Icon icon="line-md:loading-loop" />
        {{ $t('voter.status.ongoing') }}
      </div>
      <div>
        <Link href="/vote">
          {{ $t('voter.form.button_long') }}
        </Link>
      </div>
    </div>
    <div v-else-if="!fullVote.closed_at">
      <p class="text-lg text-gray-500 flex gap-2 items-center">
        {{ $t('voter.status.pending') }}
      </p>

      <p class="mt-6 mb-3">
        {{ $t('voter.debate.available_options') }}
      </p>
      <div class="flex gap-2 flex-wrap">
        <span v-for="option in fullVote.options" :key="option.id" :class="['flex items-center gap-2 bg-gray-100 py-1 px-2 font-bold', optionClasses(option, fullVote)]">
          <span class="circle block shrink-0 h-[1em] w-[1em] rounded-full" />
          {{ option.name }}
        </span>
      </div>
    </div>
    <div v-else-if="fullVote.results">
      <VoteResults :vote="fullVote" />

      <div v-if="fullVote.voted_for">
        <h3 class="uppercase font-headline text-md mt-6">{{ $t('results.your_vote') }}</h3>

        <ul class="flex flex-wrap gap-4">
          <li
            v-for="option in fullVote.voted_for"
            :key="option.id"
            :class="['vote-option', optionClasses(option, fullVote)]"
          >
            {{ option.name }}
          </li>
        </ul>
      </div>
    </div>
  </GlobalModal>
</template>

<style lang="scss" scoped>
.circle {
  background-color: var(--color, var(--egp-pink));
}

.vote-option {
  padding: .25em .5em;
  font-weight: bold;
  background-color: var(--color, var(--egp-pink));
}
</style>
