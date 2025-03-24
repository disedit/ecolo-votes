<script setup>
import { ref, watch, onUnmounted } from 'vue'
import { Icon } from '@iconify/vue'
import { Vue3Marquee } from 'vue3-marquee'
import { useI18n } from 'vue-i18n'

const props = defineProps({
  vote: { type: Object, default: null }
})

const { t } = useI18n()

watch(() => props.vote, (newVote, prevVote) => {
  if ((newVote && newVote.open && !prevVote) || (newVote && prevVote && !prevVote.open && newVote.open)) {
    announce([['mdi:vote-outline', t('voter.announcer.new_vote')]], 5000)
  } else if (prevVote && !newVote) {
    announce([['ri:hand', t('voter.announcer.vote_closed')], ['ri:bar-chart-horizontal-fill', t('voter.announcer.check_results')]], 10000)
  }
}, { deep: true })

let timeout
const messages = ref(null)
const showMessage = ref(false)

function announce(msgs, duration) {
  timeout && clearTimeout(timeout)
  timeout = null
  messages.value = msgs
  showMessage.value = true

  timeout = setTimeout(() => {
    showMessage.value = false
  }, duration)
}

onUnmounted(() => {
  clearTimeout(timeout)
})
</script>

<template>
  <div>
    <div aria-hidden="true">
      <Transition name="swipe" mode="out-in">
        <Vue3Marquee v-if="showMessage" clone class="bg-yellow font-bold">
          <div v-for="([icon, message], i) in messages" :key="i" class="flex gap-2 items-center p-2 lg:p-4">
            <Icon :icon="icon" />
            {{ message }}
          </div>
        </Vue3Marquee>
      </Transition>
    </div>
    <div class="sr-only" aria-live="assertive">
      <template v-if="showMessage">
        <span v-for="(message, i) in messages" :key="i">
          {{ message }}
        </span>
      </template>
    </div>
  </div>
</template>