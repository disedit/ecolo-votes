<script setup>
import { computed } from 'vue'
import { Icon } from '@iconify/vue'
import { majorityName } from '@/Components/Admin/Votes/majorities.js'
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

const percentage = computed(() => {
  return props.vote.results.in_use > 0 ? props.vote.results.totals.turnout / props.vote.results.in_use : 0
})
</script>

<template>
  <div :class="[`vote-${vote.type}`, { 'has-winner': !!vote.results.winner }]">
    <table class="table table-data vote-stats w-full border md:border-r-0">
      <tbody>
        <tr>
          <th width="20%">{{ $t('results.stats.codes_in_use') }}</th>
          <td width="13%">{{ vote.results.in_use }}</td>
          <th width="20%">{{ $t('results.stats.codes_voted') }}</th>
          <td width="13%">{{ vote.results.totals.turnout }} ({{ formatPercentage(percentage) }})</td>
          <th width="20%">{{ $t('results.stats.votes_cast') }}</th>
          <td width="13%">{{ vote.results.totals.votes_cast }}</td>
        </tr>
      </tbody>
    </table>
    <table class="table vote-stats table-data w-full border-x md:border-r-0">
      <tbody>
        <tr>
          <th>{{ $t('results.stats.majority_needed') }}</th>
          <td>{{ majorityName(vote) }}</td>
        </tr>
      </tbody>
    </table>
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
    background-color: var(--egp-green);
  }

  .winner.option-no .result-bar-fill {
    background-color: var(--egp-red);
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
      background-color: var(--egp-orange);
    }
  }
}

@include media('<md') {
  .vote-stats {
    tr {
      display: grid;
      grid-template-columns: 1fr auto;
    }

    td,
    th {
      border-right: 0;
      padding-inline: .5rem;
      width: auto !important;
    }
  }

  .table-results {
    font-size: var(--text-base);

    .result-bar {
      inset: .25rem;
    }

    td,
    th {
      padding-inline: .5rem;
    }

    .percentages,
    .votes {
      width: 4rem !important;
    }
  }
}
</style>