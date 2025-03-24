<script setup>
import { computed } from 'vue'
import { optionClasses } from '@/Composables/useColors.js'

const props = defineProps({
  vote: { type: Object, required: true }
})

const multiple = computed(() => props.vote.max_votes > 1)
const selected = defineModel()
const isSelected = (id) => {
  if (multiple.value) {
    return selected.value.filter(option => option.id === id).length > 0
  } else {
    return selected.value?.id === id
  }
}
const maxVotesReached = computed(() => multiple.value && selected.value && selected.value.length >= props.vote.max_votes)
const abstentionSelected = computed(() => multiple.value && selected.value && selected.value.filter(option => option.is_abstain).length > 0)
const noSelected = computed(() => multiple.value && selected.value && selected.value.filter(option => option.is_no).length > 0)
const regularOptionSelected = computed(() => multiple.value && selected.value && selected.value.length > 0 && !abstentionSelected.value && !noSelected.value)

const isDisabled = (option) => {
  if (!multiple.value) return false
  if (regularOptionSelected.value && (option.is_abstain || option.is_no)) return true
  if (abstentionSelected.value && !option.is_abstain) return true
  if (noSelected.value && !option.is_no) return true
  if (maxVotesReached.value && !isSelected(option.id)) return true
  return false
}
</script>

<template>
  <div :class="['vote-options', `type-${vote.type}`, { multiple, secret: vote.secret, 'max-votes-reached': maxVotesReached }]">
    <ul class="flex flex-col gap-2">
      <li v-for="option in vote.options" :key="option.id" :class="['vote-option', { selected: isSelected(option.id), disabled: isDisabled(option), ...optionClasses(option, vote) }]">
        <label :title="isDisabled(option) ? $t('voter.form.unselect') : null">
          <input
            :type="multiple ? 'checkbox' : 'radio'"
            name="voteOptions"
            :value="option"
            v-model="selected"
            class="sr-only"
            :required="!multiple"
            :disabled="isDisabled(option)"
          />
          <span class="option-handle" />
          <span>
            <span class="block text-balance">
              {{ option.name }}
            </span>
            <span v-if="option.description" class="block opacity-75 font-normal text-sm">
              {{ option.description }}
            </span>
          </span>
        </label>
      </li>
    </ul>
  </div>
</template>

<style lang="scss" scoped>
.vote-option {
  label {
    display: flex;
    background: var(--egp-white);
    padding: var(--spacer-4);
    font-weight: bold;
    font-size: var(--text-base);
    align-items: center;
    gap: .5em;
    border: 2.5px var(--border-color, var(--egp-white)) solid;
    border-radius: .5em;

    &:hover {
      background: var(--egp-gray-50);
    }

    &:focus-within {
      outline: 2.5px var(--egp-green-pine) solid !important;
    }
  }

  &.selected {
    label {
      background: var(--color, var(--egp-green));
      color: var(--text-color);
      
      &:not(:focus-within) {
        --border-color: var(--color);
      }
    }

    .option-handle {
      background: var(--egp-white);
  
      &::before {
        content: '';
        height: .5em;
        width: .5em;
        background: var(--egp-green-pine);
        border-radius: 100%;
      }
    }
  }

  .option-handle {
    display: flex;
    width: 1em;
    height: 1em;
    flex-shrink: 0;
    border-radius: 100%;
    background: var(--color, var(--egp-green));
    justify-content: center;
    align-items: center;
  }
}

.type-yesno {
  .vote-option:not(.option-no):not(.option-abstain) {
    --color: var(--egp-green);
  }

  label {
    font-size: var(--text-md);
  }
}

.multiple .vote-option:not(.option-no):not(.option-abstain) {
  .option-handle {
    border-radius: 0;
  }

  &.selected {
    .option-handle::before {
      border-radius: 0;
    }
  }
}

.secret .vote-option {
  --color: var(--egp-pink) !important;
  --text-color: var(--egp-black) !important;
}

.vote-option.disabled {
  label {
    cursor: not-allowed;
    opacity: .5;
  }
}
</style>