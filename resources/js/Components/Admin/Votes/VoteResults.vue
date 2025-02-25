<script setup>
import { ref } from 'vue'
import { Icon } from '@iconify/vue'
import CheckboxInput from '@/Components/Inputs/CheckboxInput.vue'
import VoteResultsChart from './VoteResultsChart.vue'

const props = defineProps({
  vote: { type: Object, required: true },
})

const hideNotCheckedIn = ref(false)
</script>

<template>
  <div v-if="vote" :class="{ 'hide-not-checked-in': hideNotCheckedIn }">
    <div class="border-b">
      <table class="table table-data w-full">
        <tbody>
          <tr>
            <th width="20%">Checked in</th>
            <td width="5%">{{ vote.results.checked_in }}</td>
            <th width="20%">Delegates voted</th>
            <td width="5%">{{ vote.results.turnout }}</td>
            <th width="20%">Allocated votes</th>
            <td width="5%">{{ vote.results.allocated_votes }}</td>
            <th width="20%">Votes cast</th>
            <td width="5%">{{ vote.results.votes_cast_with_abstentions }}</td>
          </tr>
        </tbody>
      </table>
      <div class="p-4">
        <h3 class="font-bold text-md">
          {{ vote.name }}
        </h3>
        <p v-if="vote.subtitle" class="opacity-75">{{ vote.subtitle }}</p>
      </div>
    </div>
    <div class="grid grid-cols-2">
      <div class="rollcall-grid border-r grid h-[60vh]">
        <ul class="rollcall p-4">
          <li
            v-for="delegate in vote.results.rollcall"
            :key="delegate.attendee_id"
            :class="{ voted: !!delegate.voted_at, disabled: !delegate.checked_in }"
          >
          </li>
        </ul>
        <div class="border-t overflow-auto">
          <ul class="rollcall-full">
            <li
              v-for="delegate in vote.results.rollcall"
              :key="delegate.attendee_id"
              :class="{ voted: !!delegate.voted_at, disabled: !delegate.checked_in }"
            >
              <span>
                {{ delegate.last_name }},
                {{ delegate.first_name }}
                <span v-if="delegate.votes > 1">
                  &times; {{ delegate.votes }}
                </span>
              </span>
              <Icon v-if="!!delegate.voted_at" icon="ri:check-fill" />
              <span v-if="!delegate.checked_in">
                Not checked in
              </span>
            </li>
          </ul>
          <div class="border-t p-4">
            <CheckboxInput
              name="hideNotCheckedIn"
              label="Hide not checked in"
              v-model="hideNotCheckedIn"
            />
          </div>
        </div>
      </div>
      <div class="overflow-auto h-[60vh]">
        <VoteResultsChart :vote="vote" />
      </div>
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
      background: var(--egp-pink);
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
      background-color: var(--egp-pink);
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