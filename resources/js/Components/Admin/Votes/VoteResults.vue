<script setup>
import { computed } from 'vue'
import { formatPercentage } from '@/Composables/usePercentage.js'
import { majorityName } from './majorities'
import VoteResultsChart from './VoteResultsChart.vue'

const props = defineProps({
  vote: { type: Object, required: true },
  open: { type: Boolean, default: false }
})

const percentage = computed(() => {
  return props.vote.results.in_use > 0 ? props.vote.results.totals.turnout / props.vote.results.in_use : 0
})
</script>

<template>
  <div v-if="vote">
    <div class="p-4 border-b">
      <h3 class="font-bold text-md">
        {{ vote.name }}
      </h3>
      <p v-if="vote.subtitle" class="opacity-75">{{ vote.subtitle }}</p>
    </div>
    <div class="border-b">
      <table class="table table-data w-full">
        <tbody>
          <tr>
            <th width="25%">
              {{ $t('results.stats.codes_in_use') }}
            </th>
            <td width="15%">
              {{ vote.results.in_use }} / {{ vote.results.codes }}
            </td>
            <th width="25%">
              {{ $t('results.stats.codes_voted') }}
            </th>
            <td width="15%">
              {{ vote.results.totals.turnout }} ({{ formatPercentage(percentage) }})
            </td>
            <th width="25%">
              {{ $t('results.stats.votes_cast') }}
            </th>
            <td width="15%">
              {{ vote.results.totals.votes_cast }}
            </td>
          </tr>
        </tbody>
      </table>
      <div class="bg-gray-200">
        <div :class="['h-4', { 'bg-pink': open, 'bg-gray-400': !open }]" :style="{ width: `${percentage * 100}%` }"></div>
      </div>
    </div>
    <table class="table table-data w-full">
      <tbody>
        <tr>
          <th>{{ $t('results.stats.majority_needed') }}</th>
          <td>{{ majorityName(vote) }}</td>
          <th>{{ $t('results.stats.to_select') }}</th>
          <td>{{ vote.max_votes }}</td>
        </tr>
      </tbody>
    </table>
    <div v-if="!open" class="overflow-auto max-h-[60vh]">
      <VoteResultsChart :vote="vote" />
    </div>
  </div>
</template>

<style lang="scss" scoped>
.rollcall {
  display: flex;
  flex-wrap: wrap;
  gap: .5rem;

  li {
    height: 1rem;
    width: 1rem;
    border-radius: 100%;
    background: var(--egp-gray-200);

    &.voted {
      background: var(--egp-blue);
    }

    &.disabled {
      opacity: .25;
    }
  }
}

.rollcall-grid {
  grid-template-rows: auto 1fr;
}

.rollcall-full {
  display: grid;
  grid-template-columns: 1fr;

  li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px var(--egp-gray-300) solid;
    padding: .25rem 1rem;

    &.voted {
      background-color: var(--egp-blue);
      font-weight: bold;
    }

    &.disabled {
      background: rgba(11, 51, 35, .05);
    }

    &.disabled > * {
      opacity: .5;
    }
  }
}

.hide-not-checked-in .disabled {
  display: none;
}
</style>