<script setup>
import { ref } from 'vue'
import { Icon } from '@iconify/vue'
import TextInput from '@/Components/Inputs/TextInput.vue'
import GlobalModal from '@/Components/Global/Modal.vue'
import RevealSecret from '../RevealSecret.vue'

const props = defineProps({
  vote: { type: Object, required: true },
  ballot: { type: Object, required: true },
  codeException: { type: Boolean, default: false }
})

const emit = defineEmits(['close', 'submitted'])

const code = ref('')
const submitting = ref(false)
const errors = ref(null)

async function submit() {
  submitting.value = true
  try {
    await window.axios.post('/api/vote/cast', {
      vote_id: props.vote.id,
      option_id: props.ballot.id,
      code: code.value
    })

    emit('submitted')
  } catch (error) {
    errors.value = error.response?.data?.message
  } finally {
    submitting.value = false
  }
}

const codeInput = ref(null)
function focusCode () {
  if (!props.codeException) {
    codeInput.value.$refs.input.focus()
  }
}

const revealed = ref(false)
const hideIcon = ref(false)
function updateReveal (status) {
  revealed.value = status.revealed
  hideIcon.value = status.hideIcon
}
</script>

<template>
  <GlobalModal @close="emit('close')" @opened="focusCode" bottom swipe-to-close="down">
    <template #title>
      <h1>Confirm your vote</h1>
    </template>
    <div v-if="!ballot">
      Please select an option
    </div>
    <form v-else @submit.prevent="submit" class="flex flex-col gap-4 mt-6">
      <div :class="['option-block py-6 flex flex-col items-center font-bold', `option-${ballot.name}`, { secret: vote.secret || !revealed, 'text-lg': vote.type !== 'yesno', 'text-xl': vote.type === 'yesno' }]">
        <div class="flex">
          <div class="w-[2.6rem]" v-if="!hideIcon" />
          <Icon v-if="!vote.secret" icon="ri:hand" class="vote-icon" />
          <Icon v-else icon="mdi:vote-outline" class="vote-icon secret" />
        </div>
        <RevealSecret :secret="!!vote.secret" :name="ballot.name" @update="updateReveal" class="w-full" />
      </div>
      <div v-if="!codeException">
        <p class="mb-2">Enter the code displayed on screen</p>
        <div class="relative">
          <Icon icon="icons8:key" class="absolute text-xl top-3 left-2" />
          <TextInput
            label="Code"
            name="code"
            type="text"
            pattern="\d*"
            size="xl"
            required
            input-class="font-mono text-center ps-12"
            label-sr-only
            :error="errors"
            :maxlength="4"
            v-model="code"
            placeholder="0000"
            autocomplete="off"
            ref="codeInput"
          />
        </div>
      </div>
      <InputButton type="submit" size="lg" :loading="submitting" :disabled="submitting" flat>
        Cast Vote <span v-if="vote.votes > 1">&times; {{ vote.votes }}</span>
        <template #loading>
          Submitting...
        </template>
      </InputButton>
    </form>
  </GlobalModal>
</template>

<style lang="scss" scoped>
.option-block {
  background-color: var(--color, var(--egp-pink));
}

.secret.option-block {
  background-color: var(--egp-pink);
}

.option-name {
  animation: option-name 1s ease;
}

.vote-icon {
  animation: thumbs-up 1s ease;

  &.secret {
    animation: ballot-box 1s ease;
  }
}

@keyframes thumbs-up {
  0% {
    opacity: 0;
    transform: translate(0, 20%) rotate(20deg);
  }
  50% {
    opacity: 1;
    transform: translate(0, -50%) rotate(-20deg);
  }
  100% {
    opacity: 1;
    transform: translate(0, 0) rotate(0);
  }
}

@keyframes ballot-box {
  0% {
    opacity: 0;
    transform: translate(0, 20%);
  }
  50% {
    opacity: 1;
    transform: translate(0, -50%);
  }
  100% {
    opacity: 1;
    transform: translate(0, 0);
  }
}

@keyframes option-name {
  0% {
    opacity: 0;
    transform: translate(0, -20%);
  }
  75% {
    opacity: 1;
    transform: translate(0, 10%);
  }
  100% {
    opacity: 1;
    transform: translate(0, 0);
  }
}
</style>