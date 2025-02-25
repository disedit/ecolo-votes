<script setup>
import { useFullscreen } from '@vueuse/core'
const { isFullscreen, toggle } = useFullscreen()
</script>

<template>
  <button v-if="!isFullscreen" @click="toggle" class="fixed top-2 right-2 z-[9000]">Fullscreen</button>
</template>