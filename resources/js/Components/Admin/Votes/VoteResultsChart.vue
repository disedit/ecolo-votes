<script setup>
import { computed } from 'vue'
import { Icon } from '@iconify/vue'
import { formatPercentage } from '@/Composables/usePercentage.js'
import { optionClasses } from '@/Composables/useColors.js'

const props = defineProps({
  vote: { type: Object, required: true },
})

function isWinner(option) {
  if (!props.vote.results.winner) return false
  return props.vote.results.winner.id === option.id
}

const absKey = computed(() => {
  return props.vote.with_abstentions ? 'with_abstentions' : 'without_abstentions'
})

const highestPercentage = computed(() => {
  const highest = Object.values(props.vote.results.options)
    .reduce((highestOption, option) => option.percentages[absKey.value][props.vote.relative_to] > highestOption.percentages[absKey.value][props.vote.relative_to] ? option : highestOption, props.vote.results.options[0])
    return highest.percentages[absKey.value][props.vote.relative_to]
})

function relativePercentage(percentage) {
  if (!highestPercentage.value) return 0
  return `${percentage * 100 / highestPercentage.value}%`;
}
</script>

<template>
  <div :class="[`vote-${vote.type}`, { 'has-winner': !!vote.results.winner }]">
    <table class="w-full table table-results">
      <colgroup>
        <col />
        <col width="100" />
        <col width="150" />
      </colgroup>
      <tbody>
        <tr
          v-for="option in vote.results.options"
          :class="{ winner: isWinner(option), ...optionClasses(option, vote) }"
        >
          <td class="relative align-middle">
            <div class="relative z-10 flex items-center gap-2">
              <span>
                {{ option.name }}
                <span v-if="option.gender" class="gender">
                  {{ option.gender }}
                </span>
                <span v-if="option.region" class="region">
                  {{ option.region }}
                </span>
              </span>
              <Icon v-if="isWinner(option)" icon="ri:check-fill" class="shrink-0" />
            </div>
            <div class="result-bar">
              <div class="result-bar-fill" :style="{ width: relativePercentage(option.percentages[absKey][vote.relative_to]) }" />
            </div>
          </td>
          <td class="text-right">
            {{ option.votes }}
          </td>
          <td class="text-right">
            <span v-if="option.is_abstain && !vote.with_abstentions">
              --
            </span>
            <span v-else>
              {{ formatPercentage(option.percentages[absKey][vote.relative_to]) }}
            </span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style lang="scss" scoped>
.table-results {
  font-size: var(--text-lg);

  .result-bar {
    position: absolute;
    left: .5rem;
    bottom: .5rem;
    right: .5rem;
    height: .5rem;
    z-index: 1;

    &-fill {
      background-color: var(--egp-gray-400);
      height: 100%;
      border-radius: .25rem;
    }
  }

  .winner {
    font-weight: bold;
  }

  .gender,
  .region {
    font-family: var(--font-mono);
    text-transform: uppercase;
    font-size: .5em;
    transform: translateY(-.5em)
  }
}

.vote-options {
  .winner .result-bar-fill {
    background-color: var(--egp-green-neon);
  }

  .winner.option-no .result-bar-fill {
    background-color: var(--egp-red);
  }
}

.vote-yesno {
  .table-results {
    tr:first-child .result-bar-fill {
      background-color: var(--egp-blue);
    }

    tr:nth-child(2) .result-bar-fill {
      background-color: var(--egp-red);
    }

    tr:nth-child(3) .result-bar-fill {
      background-color: var(--egp-orange);
    }
  }
}
</style>