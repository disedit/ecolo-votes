<script setup>
import { computed } from 'vue'
import { Icon } from '@iconify/vue'
import { majorities, percentages } from './majorities'
import { formatPercentage } from '@/Composables/usePercentage.js'

const props = defineProps({
  vote: { type: Object, required: true },
})

function isWinner(option) {
  if (!props.vote.results.winner) return false
  return props.vote.results.winner.id === option.id
}

const highestPercentage = computed(() => {
  const highest = props.vote.results.results
    .reduce((highestOption, option) => option.percentage.votes_cast > highestOption.percentage.votes_cast ? option : highestOption, props.vote.results.results[0])
  return highest.percentage.votes_cast
})

const typeOfPercentage = computed(() => {
  return percentages[props.vote.majority]
})

function relativePercentage(percentage) {
  if (!highestPercentage.value) return 0
  return `${percentage * 100 / highestPercentage.value}%`;
}
</script>

<template>
  <table class="table table-data w-full">
    <tbody>
      <tr>
        <th>Majority needed</th>
        <td>{{ majorities[vote.majority] }}</td>
      </tr>
    </tbody>
  </table>
  <div :class="[`vote-${vote.type}`, { 'has-winner': !!vote.results.winner }]">
    <table class="w-full table table-results">
      <colgroup>
        <col />
        <col width="100" />
        <col width="150" />
      </colgroup>
      <tbody>
        <tr
          v-for="option in vote.results.results"
          :class="{ winner: isWinner(option) }"
        >
          <td class="relative align-middle">
            <div class="relative z-10 flex items-center gap-2">
              <span>
                {{ option.name }}
                <span v-if="option.gender" class="gender">
                  {{ option.gender }}
                </span>
              </span>
              <Icon v-if="isWinner(option)" icon="ri:check-fill" class="shrink-0" />
            </div>
            <div class="result-bar">
              <div class="result-bar-fill" :style="{ width: relativePercentage(option.percentage.votes_cast) }" />
            </div>
          </td>
          <td class="text-right">
            {{ option.votes_cast }}
          </td>
          <td class="text-right">
            <span v-if="option.is_abstain">
              --
            </span>
            <span v-else>
              {{ formatPercentage(option.percentage[typeOfPercentage]) }}
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
    inset: .5rem;
    z-index: 1;

    &-fill {
      background-color: var(--egp-gray-200);
      height: 100%;
    }
  }

  .winner {
    font-weight: bold;
  }

  .gender {
    font-family: var(--font-mono);
    text-transform: uppercase;
    font-size: .5em;
    transform: translateY(-.5em)
  }
}

.vote-options {
  .winner .result-bar-fill {
    background-color: var(--egp-green);
  }
}

.vote-yesno {
  .table-results {
    tr:first-child .result-bar-fill {
      background-color: var(--egp-green);
    }

    tr:nth-child(2) .result-bar-fill {
      background-color: var(--egp-red);
    }

    tr:nth-child(3) .result-bar-fill {
      background-color: var(--egp-yellow);
    }
  }
}

.vote-options:not(.has-winner) {
  tr:first-child .result-bar-fill,
  tr:nth-child(2) .result-bar-fill {
    background-color: var(--egp-yellow);
  }
}
</style>