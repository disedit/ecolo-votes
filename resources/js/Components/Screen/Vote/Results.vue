<script setup>
import { ref, computed, onMounted } from 'vue'
import ResultsThreeOptions from './ResultsThreeOptions.vue'
import ResultsMultipleOptions from './ResultsMultipleOptions.vue'

const props = defineProps({
  vote: { type: Object, required: true },
})

const showResults = ref(false)
onMounted(() => setTimeout(() => showResults.value = true, 500))
const results = computed(() => props.vote.results)
</script>

<template>
  <div class="results">
    <Transition name="reveal">
      <div v-if="showResults">
        <ResultsThreeOptions v-if="results.options.length === 3" :vote="vote" />
        <ResultsMultipleOptions v-else :vote="vote" />
      </div>
    </Transition>
    <Transition name="swipe-bottom">
      <div v-if="showResults" class="vote-participation">
        <template v-if="vote.relative_to == 'turnout'">
          <div>
            {{ $t('screen.votes_cast') }}
            <span>
              {{ results.totals.votes_cast }}
            </span>
          </div>
          <div>
            {{ $t('screen.turnout') }}
            <span>
              {{ results.totals.turnout }}
            </span>
          </div>
        </template>
        <template v-else>
          <div>
            {{ $t('screen.votes_cast') }}
            <span>
              {{ results.totals.votes_cast }}
            </span>
          </div>
        </template>
      </div>
    </Transition>
  </div>
</template>

<style lang="scss" scoped>
.results {
  display: flex;
  flex-direction: column;
  padding: var(--screen-padding);
}

.vote-participation {
  position: fixed;
  left: 0;
  right: 0;
  bottom: 0;
  border-top: 3px var(--egp-green-pine) solid;
  background: var(--egp-gray-200);
  font-size: 2.5vw;
  font-weight: 600;
  display: flex;
  z-index: 50;

  div {
    display: flex;
    flex-grow: 1;
    padding: 1vh var(--screen-padding);
    justify-content: space-between;

    &:not(:last-child) {
      border-right: 3px var(--egp-green-pine) solid;
    }
  }
}
</style>