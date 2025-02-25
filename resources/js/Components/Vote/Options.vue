<script setup>
defineProps({
  vote: { type: Object, required: true }
})

const selected = defineModel()
const isSelected = (id) => id === selected.value?.id
</script>

<template>
  <div :class="['vote-options', `type-${vote.type}`, { secret: vote.secret }]">
    <ul class="flex flex-col gap-2">
      <li v-for="option in vote.options" :key="option.id" :class="['vote-option', `option-${option.name.replaceAll(' ', '-')}`, { selected: isSelected(option.id) }]">
        <label>
          <input
            type="radio"
            name="voteOptions"
            :value="option"
            v-model="selected"
            class="sr-only"
            required
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
    border: 2.5px var(--egp-white) solid;

    &:hover {
      background: var(--egp-gray-50);
    }
  }

  &.selected {
    label {
      outline: 2.5px var(--egp-green-pine) solid;
      background: var(--color, var(--egp-pink));
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
    background: var(--color, var(--egp-pink));
    justify-content: center;
    align-items: center;
  }
}

.type-yesno {
  .vote-option {
    label {
      font-size: var(--text-md);
    }
  }
}

.secret .vote-option {
  --color: var(--egp-pink) !important;
}
</style>