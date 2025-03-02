<script setup>
import { ref } from 'vue'
import { VueFinalModal } from 'vue-final-modal'
import { Icon } from '@iconify/vue'

defineProps({
  width: { type: Number, default: null },
  edge: { type: Boolean, default: false },
  bottom: { type: Boolean, default: false },
  swipeToClose: { type: String, default: null }
})

const emit = defineEmits(['close', 'opened'])

const prevThemeColor = ref('#47B972')

function onBeforeOpen() {
  const themeColor = document.querySelector('meta[name="theme-color"]')
  prevThemeColor.value = themeColor.getAttribute('content')
  themeColor.setAttribute('content', '#1a5437')
}

function onBeforeClose() {
  document.querySelector('meta[name="theme-color"]').setAttribute('content', prevThemeColor.value)
}
</script>

<template>
  <VueFinalModal
    :class="['base-modal', { edge, bottom }]"
    :style="width ? `--max-modal-width: ${width}px` : null"
    content-class="base-modal-content"
    overlay-class="base-modal-overlay"
    overlay-transition="vfm-fade"
    content-transition="modal-swipe"
    :swipe-to-close="swipeToClose"
    @opened="emit('opened')"
    @before-open="onBeforeOpen"
    @before-close="onBeforeClose"
  >
    <div class="base-modal-card">
      <div class="base-modal-title flex gap-2 justify-between mb-4">
        <slot name="title" />
        <button @click="emit('close')" :title="$t('global.close_modal')" class="base-modal-close text-lg hover:text-red">
          <Icon icon="ri:close-fill" />
        </button>
      </div>
      <slot />
    </div>
  </VueFinalModal>
</template>

<style lang="scss">
.base-modal {
  overflow: auto;

  &-content {
    padding: var(--site-padding);
    margin: 0 auto;
    padding-top: calc(var(--site-padding) + 5vh);
    padding-bottom: 20vh;
    width: 100%;
    max-width: var(--max-modal-width, 1000px);

    &:focus-visible {        
        outline: 3px var(--egp-yellow) solid;
    }

    h1 {
      font-size: var(--text-md);
      font-family: var(--font-mono);
      text-transform: uppercase;
      font-weight: bold;
    }
  }

  &-card {
    background: var(--egp-white);
    box-shadow: 1rem 1rem 0 0 var(--egp-green-pine);
    width: 100%;
    color: var(--egp-green-pine);
    position: relative;
    padding: var(--site-padding);
  }

  &-overlay {
    position: fixed;
    background-color: rgba($egp-green-pine, .75);
  }

  &.edge {
    .base-modal-card {
      padding: 0;
    }

    .base-modal-close {
      padding-right: 1rem;
    }
  }

  &.bottom {
    display: flex;
    align-items: flex-end;

    .base-modal-content {
      padding: 1.5rem 1rem;
    }

    .base-modal-card {
      box-shadow: .75rem .75rem 0 0 var(--egp-green-pine);
    }
  }
}

.modal-swipe-enter-active,
.modal-swipe-leave-active {
  transition: .25s ease;

  .base-modal-card {
    transition: .25s ease;
  }
}

.modal-swipe-enter-from,
.modal-swipe-leave-to {
  opacity: 0;

  .base-modal-card {
    transform: translateY(50%);
  }
}
</style>