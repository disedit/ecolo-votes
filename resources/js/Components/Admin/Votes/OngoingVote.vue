<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue'
import { useDraggable } from '@vueuse/core'
import { Icon } from '@iconify/vue'
import VoteResults from '@/Components/Admin/Votes/VoteResults.vue'

const props = defineProps({
  vote: { type: Object, required: true },
  live: { type: Boolean, default: false }
})

const emit = defineEmits(['close', 'hide', 'reset'])

const draggable = ref(null)
const results = ref(null)
const minimized = ref(false)
let interval

async function refresh() {
  const { data } = await window.axios.get(`/api/admin/votes/${props.vote.id}`)
  results.value = data

  if (!data.open) {
    clearInterval(interval)
  }
}

watch(() => props.vote.id, () => {
  emit('reset')
  results.value = props.vote
})
watch(() => props.vote.open, (isOpen) => {
  if (isOpen) {
    interval = setInterval(() => { refresh() }, 2000)
  } else {
    clearInterval(interval)
  }
})

onMounted(() => {
  emit('reset')
  results.value = props.vote

  if (props.live) {
    interval = setInterval(() => { refresh() }, 2000)
  }
})

onUnmounted(() => {
  clearInterval(interval)
})

const { x, y, style } = useDraggable(draggable, {
  initialValue: { x: 140, y: 140 },
})
</script>

<template>
  <Teleport to="#teleports">
    <div ref="draggable" :style="style" :class="['fixed z-[1000] w-[70vw] max-w-[1200px] bg-white border-4 shadow-window', { 'border-green-dark': vote.open, 'border-gray-700': !vote.open }]">
      <div :class="['flex items-center text-white', { 'bg-green-dark': vote.open, 'bg-gray-700': !vote.open }]">
        <h2 class="font-mono font-bold uppercase px-4 grow cursor-move">
          <span v-if="vote.open" class="flex items-center gap-2">
            <Icon icon="svg-spinners:ring-resize" />
            {{ $t('admin.votes.ongoing.title') }}
            <span v-if="minimized">
              &gt;
              {{ vote.name }}
            </span>
          </span>
          <span v-else>Just closed</span>
        </h2>
        <div class="ms-auto flex gap-1">
          <button @click="minimized = !minimized" :title="minimized ? $t('admin.votes.actions.maximize') : $t('admin.votes.actions.minimize')" class="text-lg">
            <Icon v-if="!minimized" icon="la:window-minimize" />
            <Icon v-else icon="la:window-maximize" />
          </button>
          <InputButton v-if="vote.open" variant="red" @click="emit('close', vote.id)" flat>
            {{ $t('admin.votes.actions.close_vote') }}
          </InputButton>
          <InputButton v-else variant="yellow" @click="emit('hide')" flat>
            {{ $t('admin.votes.actions.hide') }}
          </InputButton>
        </div>
      </div>

      <div v-if="!minimized" :class="['bg-white border-t-4', {'border-green-dark': vote.open, 'border-gray-700': !vote.open}]">
        <VoteResults v-if="results" :vote="results" :open="!!vote.open" />
      </div>
    </div>
  </Teleport>
</template>