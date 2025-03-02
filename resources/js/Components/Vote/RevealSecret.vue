<script setup>
import { ref, watch, onMounted } from 'vue'
import { Icon } from '@iconify/vue'
import Tooltip from '@/Components/Global/Tooltip.vue'

const props = defineProps({
  name: { type: String, required: true },
  secret: { type: Boolean, default: false },
  optionClasses: { type: Object, default: {} }
})

const emit = defineEmits(['update'])

const reveal = ref(false)
const hideIcon = ref(false)

onMounted(() => {
  if (!props.secret) {
    reveal.value = true
    hideIcon.value = true
  }
})

watch(reveal, () => emit('update', { revealed: reveal.value, hideIcon: hideIcon.value }))
watch(hideIcon, () => emit('update', { revealed: reveal.value, hideIcon: hideIcon.value }))
</script>

<template>
  <Tooltip :text="reveal ? $t('voter.reveal.hide') : $t('voter.reveal.reveal')" centered class="block">
    <button
      @click="reveal = !reveal; hideIcon = false"
      type="button"
      :class="['vote px-4 py-3 text-center font-bold leading-min flex items-center w-full gap-2', { secret, hide: !reveal, ...optionClasses }]">
      <Icon icon="ri:eye-close-line" v-if="!reveal" class="shrink-0" />
      <Icon icon="ri:eye-line" v-else-if="!hideIcon" class="shrink-0" />
      <span v-if="reveal" class="mx-auto text-center min-w-0">
        {{ name }}
      </span>
      <span v-else class="mx-auto text-center translate-y-[.15em]">
        *********
      </span>
    </button>
  </Tooltip>
</template>

<style lang="scss" scoped>
.vote {
  background-color: var(--color, var(--egp-pink));
}

.vote.secret,
.vote.hide {
  background-color: var(--egp-pink);
}
</style>