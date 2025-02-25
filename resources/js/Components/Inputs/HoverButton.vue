<script setup>
import { ref } from 'vue'

defineProps({
  label: { type: Object, required: true },
  variant: { type: Object, required: true }
})

const hover = ref(false)
</script>

<template>
  <InputButton
    :variant="hover ? variant.hover : variant.default"
    @mouseenter="hover = true"
    @mouseleave="hover = false"
    v-bind="$attrs"
  >
    {{ hover ? label.hover : label.default }}
  </InputButton>
</template>