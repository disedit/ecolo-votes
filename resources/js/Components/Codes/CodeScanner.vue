<script setup>
import { ref, nextTick, computed, onMounted, onUnmounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import QrScanner from 'qr-scanner'
import CodeScannerFrame from '@/Components/Codes/CodeScannerFrame.vue'
import { Icon } from '@iconify/vue'
import ButtonInput from '@/Components/Inputs/Button.vue'

let qrScanner
const videoElem = ref(null)
const hasCamera = ref(false)
const cameraOpen = ref(false)
const error = ref(null)
const page = usePage()
const domain = computed(() => page.props.base_url)

onMounted(async () => {
  hasCamera.value = await QrScanner.hasCamera()
})

onUnmounted(() => {
  qrScanner && qrScanner.destroy()
  qrScanner = null
})

function openCamera () {
  cameraOpen.value = true
  nextTick(() => {
    qrScanner = new QrScanner(
      videoElem.value,
      (code) => {
        if (code.data.startsWith(domain.value)) {
          const ref = code.data.replace(domain.value + '/code/', '')
          window.location = '/code/' + ref
        } else {
          error.value = true
          setTimeout(() => { error.value = false }, 3000)
        }
       },
       { returnDetailedScanResult: true },
    )
    qrScanner.start()
  })
}

function closeCamera () {
  cameraOpen.value = false
  qrScanner.destroy()
  qrScanner = null
}
</script>

<template>
  <div class="qr-scanner">
    <template v-if="hasCamera">
      <ButtonInput @click="openCamera" size="lg" block icon="ri:qr-code-line">
        {{ $t('code_login.scan_qr') }}
      </ButtonInput>
      <div class="divider">
        <hr />
        <span>{{ $t('code_login.or') }}</span>
      </div>
      <transition name="card">
        <div v-if="cameraOpen" class="video-wrapper">
          <div class="instructions">
            <span>{{ $t('code_login.instructions_qr') }}</span>
            <button class="btn close-button" aria-label="Tancar camera" @click="closeCamera">&times;</button>
          </div>
          <video class="video" ref="videoElem"></video>
          <div v-if="error" class="error" aria-live="assertive">
            {{ $t('code_login.code_invalid') }}
          </div>
          <div class="video-scanner">
            <CodeScannerFrame aria-hidden="true" />
          </div>
        </div>
      </transition>
    </template>
  </div>
</template>

<style lang="scss" scoped>
  .qr-scanner {
    width: 100%;
  }

  .video-wrapper {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    display: flex;
    z-index: 1100;
    background: black;
    flex-direction: column;
  }

  .video {
    width: 100%;
    height: auto;
    height: 100%;
    position: relative;
    object-position: center;
  }

  .video-scanner {
    position: fixed;
    display: flex;
    top: 4rem;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 1600;
    padding: 20vh 18vw;

    svg {
      height: 100%;
      width: 100%;
    }
  }

  .instructions {
    display: flex;
    color: white;
    background: black;
    padding: 1rem;
    font-size: .85rem;
    align-items: center;
  }

  .close-button {
    appearance: none;
    background: transparent;
    color: white;
    font-size: 2rem;
    border: 0;
    margin-left: auto;
    padding: 0;
    padding-left: 1rem;
    line-height: 1;
  }

  .error {
    position: fixed;
    top: 50%;
    left: 15%;
    right: 15%;
    transform: translateY(-50%);
    padding: .75rem;
    background: red;
    color: white;
    text-align: center;
    z-index: 1600;
  }

  .divider {
    position: relative;

    hr {
      border-bottom: 1px gray solid;
      margin: 2rem 0;
    }

    span {
      position: absolute;
      content: 'o';
      background: white;
      left: 50%;
      top: -50%;
      transform: translate(-50%, -50%);
      color: gray;
      padding: 0 .5rem;
    }
  }

  .slide-enter-active, .slide-leave-active {
    transition: transform .5s;
  }
  .slide-enter, .slide-leave-to {
    transform: translateY(100%);
  }
</style>