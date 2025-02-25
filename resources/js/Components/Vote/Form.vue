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

const ballot = ref(null)
const ballotHasOption = computed(() => !!ballot.value)

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
    <p class="opacity-75" v-if="vote.subtitle">
      {{ vote.subtitle }}
    </p>
    <form @submit.prevent="openConfirmModal" class="flex flex-col">
      <VoteOptions
        :vote="vote"
        v-model="ballot"
        class="my-10"
      />
      <div :class="[{ 'sticky bottom-site shadow-mario': ballotHasOption }]">
        <InputButton type="submit" variant="yellow" block size="lg" class="mt-auto" flat>
          Vote <span v-if="vote.votes > 1">&times; {{ vote.votes }}</span>
        </InputButton>
      </div>
    </form>

    <div v-if="vote.secret" class="mt-4 text-sm flex gap-1 items-center justify-center">
      <Icon icon="ri:lock-line" />
      Your vote is secret
    </div>
  </div>
</template>