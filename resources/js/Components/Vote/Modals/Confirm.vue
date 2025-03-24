<script setup>
import { ref } from 'vue'
import { Icon } from '@iconify/vue'
import { optionClasses } from '@/Composables/useColors.js'
import TextInput from '@/Components/Inputs/TextInput.vue'
import GlobalModal from '@/Components/Global/Modal.vue'
import RevealSecret from '../RevealSecret.vue'

const props = defineProps({
  vote: { type: Object, required: true },
  ballot: { type: Array, required: true },
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
      option_ids: props.ballot.map(option => option.id),
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
      <h1>{{ $t('voter.confirm.title') }}</h1>
    </template>
    <div v-if="!ballot">
      {{ $t('voter.confirm.empty') }}
    </div>
    <form v-else @submit.prevent="submit" class="flex flex-col gap-4 mt-6">
      <div
        v-if="ballot.length === 1"
        :class="[
          'option-block rounded-lg py-6 flex flex-col items-center font-bold',
          { secret: vote.secret || !revealed, 'text-lg': vote.type !== 'yesno', 'text-xl': vote.type === 'yesno' },
          optionClasses(ballot[0], vote)
        ]"
      >
        <div class="flex">
          <div class="w-[2.6rem]" v-if="!hideIcon" />
          <Icon v-if="!vote.secret" icon="ri:hand" class="vote-icon" />
          <Icon v-else icon="mdi:vote-outline" class="vote-icon secret" />
        </div>
        <RevealSecret :secret="!!vote.secret" :name="ballot[0].name" @update="updateReveal" class="w-full" />
      </div>
      <div v-else-if="ballot.length > 1">
        <ul class="flex flex-wrap gap-4 text-md">
          <li v-for="option in ballot" :key="option.id">
            <RevealSecret :secret="!!vote.secret" :name="option.name" />
          </li>
        </ul>
      </div>
      <div v-else class="bg-pink p-2 font-bold">
        {{ $t('voter.confirm.empty') }}
      </div>
      <div v-if="!codeException && ballot.length > 0">
        <p class="mb-2">
          {{ $t('voter.confirm.code') }}
        </p>
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
      <InputButton v-if="ballot.length > 0" variant="purple" type="submit" size="lg" :loading="submitting" :disabled="submitting" flat>
        {{ $t('voter.confirm.button') }} <span v-if="vote.votes > 1">&times; {{ vote.votes }}</span>
        <template #loading>
          {{ $t('voter.confirm.submitting') }}
        </template>
      </InputButton>
    </form>
  </GlobalModal>
</template>

<style lang="scss" scoped>
.option-block {
  background-color: var(--color, var(--egp-green));
  color: var(--text-color);
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