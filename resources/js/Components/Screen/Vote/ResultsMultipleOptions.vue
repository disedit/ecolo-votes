<script setup>
import { computed, onMounted, onUnmounted } from 'vue'
import gsap from 'gsap'
import { percentages } from '@/Components/Admin/Votes/majorities.js'
import { Icon } from '@iconify/vue'
import VueNumberAnimation from 'vue-number-animation'

const props = defineProps({
  vote: { type: Object, required: true },
})

const options = computed(() => {
  return props.vote.abridgedResults.results.filter(option => !option.is_abstain)
})

const highestPercentage = computed(() => {
  return options.value[0].percentage[percentages[props.vote.majority]]
})

function isWinner (option) {
  return props.vote.winner_id === option.id
}

function percentage (percentage) {
  return `${percentage * 100 / highestPercentage.value}%`
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
  <div :class="['multiple-options relative', `majority-${vote.majority}`, { 'no-winner': !vote.winner_id }]">
    <div :class="['options', `options-${options.length}`]">
      <div v-for="option in options" :key="option.id" :class="['option', `option-${option.name.replaceAll(' ','-')}`, `votes-${option.votes_cast}`, { winner: isWinner(option) }]">
        <span class="flex items-center gap-[.1em]">
          <span class="truncate">{{ option.name }}</span>
          <Icon icon="ri:check-line" v-if="isWinner(option)" />
        </span>
        <span class="ms-auto percentage result shrink-0 whitespace-nowrap">
          <VueNumberAnimation
            :from="0"
            :to="option.percentage[percentages[vote.majority]]"
            :format="rounded"
            :duration="1"
            autoplay
          />%
        </span>
        <span class="number result shrink-0">
          <VueNumberAnimation
            :from="0"
            :to="option.votes_cast"
            :format="whole"
            :duration="1"
            autoplay
          />
        </span>
        <div class="bar" :style="{ width: percentage(option.percentage[percentages[vote.majority]]) }" />
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.multiple-options {
  height: 55vh;
  display: flex;
  align-items: center;
}

.options {
  display: grid;
  grid-auto-flow: column;
  gap: var(--screen-padding);
  grid-template-columns: repeat(4, 1fr); 
  grid-template-rows: repeat(5, auto);
  flex-grow: 1;
}

.option {
  display: flex;
  background: var(--egp-white);
  padding: .4em .6em;
  font-size: 2vw;
  gap: .25em;
  position: relative;
  border: .15em var(--egp-white) solid;
  font-weight: 500;
  align-items: center;
  line-height: 1.1;
  min-width: 0;
  transform: translateY(30%);
  opacity: 0;

  & > span {
    position: relative;
    z-index: 10;
    min-width: 0;
  }

  .bar {
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    background: var(--color, var(--egp-gray-400));
    z-index: 1;
    transform: scaleX(0);
    transform-origin: left;
  }

  .number {
    font-variant-numeric: tabular-nums;
  }

  .result {
    width: min-content;
    text-align: right;
    font-variant-numeric: tabular-nums;
    letter-spacing: -.02em;
  }

  .percentage {
    width: min-content;
    font-variant-numeric: tabular-nums;
    letter-spacing: -.02em;
    opacity: .6;
    font-size: .75em;
  }

  &.winner {
    outline: .15em var(--egp-green-pine) solid;
    font-weight: 700;
    animation: blink 2.5s;

    .bar {
      --color: var(--egp-green);
    }

    :deep(path) {
      stroke: var(--egp-green-pine);
      stroke-width: 2px;
    }
  }
}

.no-winner {
  .option:first-child,
  .option:nth-child(2) {
    outline: .15em var(--egp-green-pine) solid;
    font-weight: 700;

    .bar {
      --color: var(--egp-yellow);
    }
  }

  .votes-0 {
    outline: 0 !important;
  }
}

.options-4,
.options-3 {
  grid-template-columns: repeat(2, 1fr); 
  grid-template-rows: repeat(2, auto);

  .option {
    font-size: 2.75vw;
    padding-block: .75em;
  }
}

.options-5,
.options-6 {
  grid-template-columns: repeat(2, 1fr); 
  grid-template-rows: repeat(3, auto);

  .option {
    font-size: 2.5vw;
  }
}

.options-7,
.options-8,
.options-9 {
  grid-template-columns: repeat(3, 1fr); 
  grid-template-rows: repeat(3, auto);

  .option {
    font-size: 2.75vw;
  }
}

.options-10,
.options-11,
.options-12 {
  grid-template-columns: repeat(3, 1fr); 
  grid-template-rows: repeat(4, auto);

  .option {
    font-size: 2.75vw;
  }
}

.options-13,
.options-14,
.options-15 {
  grid-template-columns: repeat(3, 1fr); 
  grid-template-rows: repeat(5, auto);
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