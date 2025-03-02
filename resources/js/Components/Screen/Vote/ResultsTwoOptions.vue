<script setup>
import { computed, onMounted, onUnmounted } from 'vue'
import gsap from 'gsap'
import { percentages } from '@/Components/Admin/Votes/majorities.js'
import { optionClasses } from '@/Composables/useColors.js'
import { Icon } from '@iconify/vue'
import VueNumberAnimation from 'vue-number-animation'

const props = defineProps({
  vote: { type: Object, required: true },
})

const options = computed(() => {
  return props.vote.abridgedResults.results.filter(option => !option.is_abstain)
})

function isWinner (option) {
  return props.vote.winner_id === option.id
}

function percentage (percentage) {
  return `${percentage * 100}%`
}

function rounded (percentage) {
  return Math.round((percentage + Number.EPSILON) * 10000) / 100
}

function whole (number) {
  return number.toFixed(0)
}

let animations
onMounted(() => {
  animations = gsap.context(() => {
    gsap.to('.option', {
      y: 0,
      opacity: 1,
      stagger: .2
    })

    gsap.to('.bar', {
      stagger: .2,
      scaleX: 1,
      duration: 1
    })
  })
})

onUnmounted(() => {
  animations && animations.kill()
})
</script>

<template>
  <div :class="['two-options relative', `majority-${vote.majority}`]">
    <div :class="`options vote-${vote.type}`">
      <div v-for="option in options" :key="option.id" :class="['option', { winner: isWinner(option), ...optionClasses(option, vote) }]">
        <span class="flex items-center gap-[.25em] min-w-0">
          <span class="truncate min-w-0">{{ option.name }}</span>
          <Icon icon="ri:check-line" v-if="isWinner(option)" class="tick min-w-0 shrink-0" />
        </span>
        <span class="ms-auto percentage result shrink-0 whitespace-nowrap number">
          <VueNumberAnimation
            :from="0"
            :to="option.percentage[percentages[vote.majority]]"
            :format="rounded"
            :duration="1"
            easing="easeOutSine"
            autoplay
          />%
        </span>
        <span class="number result shrink-0">
          <VueNumberAnimation
            :from="0"
            :to="option.votes_cast"
            :format="whole"
            :duration="1"
            easing="easeOutSine"
            autoplay
          />
        </span>
        <div class="bar" :style="{ width: percentage(option.percentage[percentages[vote.majority]]) }" />
      </div>
    </div>
    <div class="majority-line" />
  </div>
</template>

<style lang="scss" scoped>
.options {
  display: flex;
  flex-direction: column;
  gap: var(--screen-padding);
  justify-content: center;
  height: 55vh;
}

.option {
  position: relative;
  background: var(--egp-white);
  font-size: 4vw;
  font-weight: 600;
  height: 20vh;
  border: .4vw var(--egp-white) solid;
  display: flex;
  align-items: center;
  gap: 2vw;
  padding: var(--screen-padding);
  transform: translateY(30%);
  opacity: 0;

  &.winner {
    outline: .4vw var(--egp-green-pine) solid;
    font-weight: 800;
    animation: blink 2.5s;
    
    &:not(.option-No) .bar {
      background: var(--egp-green);
    }
  }

  .tick {
    stroke: var(--egp-green-pine);
    stroke-width: 2px;
  }

  .result {
    width: 8vw;
    text-align: right;
  }

  .percentage {
    width: 15vw;
  }

  span {
    position: relative;
    z-index: 10;
  }
}

.bar {
  background: var(--egp-yellow);
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  z-index: 1;
  transform: scaleX(0);
  transform-origin: left;
}

.number {
  font-variant-numeric: tabular-nums;
}

.option-Yes .bar {
  background: var(--egp-green);
}

.option-No .bar {
  background: var(--egp-red);
}

.majority-line {
  width: .4vw;
  position: absolute;
  top: 0;
  bottom: 0;
  background: var(--egp-gray-900);
  opacity: .5;
  z-index: 100;
}

.majority-absolute .majority-line {
  left: 50%;
}

.majority-simple .majority-line {
  display: none;
}

.majority-2\/3_all .majority-line,
.majority-2\/3_cast .majority-line {
  left: 66.66%;
}

.majority-3\/4_all .majority-line,
.majority-3\/4_cast .majority-line {
  left: 75%;
}

@keyframes blink {
  0%, 30%, 60%, 90% {
    outline-color: transparent;
  }

  15%, 45%, 75%, 100% {
    outline-color: var(--egp-green-pine);
  }
}
</style>