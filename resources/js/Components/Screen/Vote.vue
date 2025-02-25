<script setup>
import { computed, ref, onMounted } from 'vue'
import { majoritiesOnScreen, percentages, thresholds } from '@/Components/Admin/Votes/majorities.js'
import { Icon } from '@iconify/vue'
import NumberFlow from '@number-flow/vue'
import VoteName from './Vote/Name.vue'
import PulseIcon from '@/Components/Global/PulseIcon.vue'
import VoteCode from './Vote/Code.vue'
import VoteResults from './Vote/Results.vue'

const props = defineProps({
  vote: { type: Object, required: true },
  code: { type: Object, required: true }
})

const colors = {
  debate: 'orange',
  voting: 'green',
  results: 'pine'
}

const status = computed(() => {
  if (props.vote.open) {
    return 'voting'
  }

  if (props.vote.debate && !props.vote.closed_at) {
    return 'debate'
  }

  return 'results'
})
const ongoing = computed(() => ['debate', 'voting'].includes(status.value))
const showCode = ref(false)

onMounted(() => setTimeout(() => showCode.value = true, 250))

const requiredVotes = computed(() => {
  const relativeTo = percentages[props.vote.majority]
  const total = props.vote.abridgedResults[relativeTo]
  const minimum = Math.ceil(total * thresholds[props.vote.majority])
  return (props.vote.majority === 'absolute' && total % 2 == 0) ? minimum + 1 : minimum
})
</script>

<template>
  <div :class="['vote', { ongoing }]">
    <div class="vote-header">
      <h2 class="vote-status">
        <PulseIcon :color="colors[status]" :still="status === 'results'" />
        <div class="overflow-clip">
          <Transition name="interchange" mode="out-in">
            <span v-if="status === 'debate'" key="debate">
              Next up
            </span>
            <span v-else-if="status === 'voting'" key="voting">
              Vote ongoing
            </span>
            <span v-else-if="status === 'results'" key="results">
              Results
            </span>
          </Transition>
        </div>
      </h2>
      <div class="vote-info overflow-clip">
        <Transition name="interchange" mode="out-in">
          <span v-if="status === 'voting'" key="particiption" class="flex items-center gap-[.5em] tabular-nums">
            <Icon icon="ri:user-line" />
            <NumberFlow :value="vote.abridgedResults.turnout" continuous />
          </span>
          <span v-else-if="status === 'results'" key="majority" class="flex">
            {{ majoritiesOnScreen[vote.majority] }}
            <span v-if="vote.majority !== 'simple'" class="ms-2">(≥{{ requiredVotes }} votes) </span>
          </span>
        </Transition>
      </div>
    </div>
    
    <div class="vote-name">
      <VoteName :vote="vote" />
      <p class="vote-description">{{ vote.subtitle }}</p>
    </div>

    <VoteResults v-if="status === 'results'" :vote="vote" :key="`results-${vote.id}`" />

    <Transition name="swipe-bottom">
      <VoteCode v-if="status === 'voting' && showCode" :code="code" />
    </Transition>
  </div>
</template>

<style lang="scss" scoped>
.vote {
  padding-top: calc(var(--screen-nav-height) + var(--screen-padding));
  height: 100vh;
  overflow: hidden;

  &-header {
    display: flex;
    align-items: center;
    font-size: 2.4vw;
    justify-content: space-between;
    line-height: 1.1;
    padding: 0 var(--screen-padding);
  }

  &-status {
    display: flex;
    align-items: center;
    gap: .5em;
    font-family: var(--font-headline);
    text-transform: uppercase;

    span {
      display: block;
    }
  }

  &-info {
    font-weight: 700;
    font-size: 1em;
    overflow: clip;
  }

  &-name {
    font-size: 6.5vw;
    font-weight: bold;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    line-height: 1.2;
    transition: 1s ease;
    height: 1em;

    h1 {
      padding: 0 var(--screen-padding);
      max-width: 100vw;
    }

    .vote-description {
      font-size: .28em;
      padding: 0 var(--screen-padding);
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      transition: .5s ease;
      opacity: 0;
      height: 0;
      line-height: 1;
    }
  }

  &.ongoing {
    .vote-name {
      font-size: 8vw;
      height: 62vh;
    }

    .vote-description {
      opacity: 1;
      height: 1em;
    }
  }
}
</style>