<script setup>
import { ref, watch, nextTick, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  vote: { type: Object, required: true }
})

const element = ref() 
const overflowing = ref(false)
const scrollBy = ref(0)
const changing = ref(false)
const voteName = ref(null)
let timeout

onMounted(() => setOverflowing())
watch(() => props.vote.open + props.vote.name, () => {
  if (overflowing.value) {
    changing.value = true
    scrollBy.value = 0
  } else {
    voteName.value = props.vote.name
  }
  timeout = setTimeout(() => setOverflowing(), 1100)
})

function setOverflowing() {
  voteName.value = props.vote.name
  nextTick(() => {
    overflowing.value = element.value.offsetWidth < element.value.scrollWidth
    scrollBy.value = element.value.offsetWidth - element.value.scrollWidth
    changing.value = false
  })
}

function duration(scrollBy) {
  return `${10 + (Math.abs(scrollBy) / 100)}s`
}

onUnmounted(() => clearTimeout(timeout))
</script>

<template>
  <h1 ref="element" :class="['wrapper', { overflowing, changing }]" :style="{ '--scroll-by': `${scrollBy}px`, '--duration': duration(scrollBy) }">
    <div class="element">
      {{ voteName }}
    </div>
  </h1>
</template>

<style lang="scss" scoped>
.wrapper {
  display: flex;
  overflow-x: clip;

  &.overflowing .element {
    animation: scroll var(--duration, 10s) cubic-bezier(.11,0,.88,1) infinite;
  }

  &.changing .element {
    opacity: 0;
    animation: none;
  }
}

.element {
  white-space: nowrap;
  padding-right: var(--screen-padding);
  transition: opacity .5s ease;
}

@keyframes scroll {
  0%, 8%, 95%, 100% {
    transform: translateX(0);
  }

  40%,
  50% {
    transform: translateX(var(--scroll-by));
  }
}
</style>