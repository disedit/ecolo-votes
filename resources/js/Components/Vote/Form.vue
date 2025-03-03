<script setup>
import { ref, computed } from 'vue'
import { useModal } from 'vue-final-modal'
import { Icon } from '@iconify/vue'
import VoteOptions from './Options.vue'
import VoteConfirm from './Modals/Confirm.vue'

const props = defineProps({
  codeException: { type: Boolean, default: false }, 
  vote: { type: Object, required: true }
})

const emit = defineEmits(['refresh'])

const { open, close, patchOptions } = useModal({
  component: VoteConfirm,
  attrs: {
    ballot: null,
    pinToBottom: true,
    vote: props.vote,
    codeException: props.codeException,
    onClose () { close() },
    onSubmitted () {
      close()
      emit('refresh')
      window.scrollTo(0, 0)
    }
  }
})

const selected = ref(props.vote.max_votes > 1 ? [] : null)
const ballot = computed(() => {
  if (!selected.value) return []
  return (props.vote.max_votes > 1) ? selected.value : [selected.value]
})

const ballotIsGoodToGo = computed(() => ballot.value && ballot.value.length === props.vote.max_votes)

function openConfirmModal() {
  patchOptions({ attrs: { ballot }})
  open()
}
</script>

<template>
  <div class="w-full">
    <h2 class="font-headline uppercase text-xl leading-tight mt-9">
      {{ vote.name }}
    </h2>
    <p class="opacity-75 mb-2" v-if="vote.subtitle">
      {{ vote.subtitle }}
    </p>
    <p class="opacity-75 mb-2">
      {{ $t('voter.form.select_max', vote.max_votes, { max: vote.max_votes }) }}
    </p>
    <form @submit.prevent="openConfirmModal" class="flex flex-col">
      <VoteOptions
        :vote="vote"
        v-model="selected"
        class="my-10"
      />
      <div :class="[{ 'sticky bottom-site shadow-mario': ballotIsGoodToGo }]">
        <InputButton type="submit" variant="yellow" block size="lg" class="mt-auto" flat>
          {{ $t('voter.form.button') }}
          <span v-if="vote.votes > 1">&times; {{ vote.votes }}</span>
        </InputButton>
      </div>
    </form>

    <div class="mt-4 text-sm flex gap-1 items-center justify-center">
      <Icon icon="ri:lock-line" />
      {{ $t('voter.form.secret') }}
    </div>
  </div>
</template>