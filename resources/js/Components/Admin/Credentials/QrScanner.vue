<script setup>
import { ref, watch, nextTick, onMounted, onUnmounted } from 'vue'
import QrScanner from 'qr-scanner'
import { Icon } from '@iconify/vue'

const props = defineProps({
  label: { type: String, default: 'Scan QR codes' },
  scanning: { type: String, default: 'credentials' }
})

const emit = defineEmits(['close'])

let qrScanner
let timeout
let cardTimeout
let loadingTimeout
const videoElem = ref(null)
const hasCamera = ref(false)
const cameraOpen = ref(false)
const mode = ref('IN')
const qrCode = ref('')
const loading = ref(false)
const card = ref(null)

onMounted(async () => {
  hasCamera.value = await QrScanner.hasCamera()
})

onUnmounted(() => {
  qrScanner && qrScanner.destroy()
  qrScanner = null
})

watch(qrCode, async (newQrCode) => {
  if (newQrCode) card.value = null
  updateStatus(newQrCode)
})

function openCamera () {
  cameraOpen.value = true
  nextTick(() => {
    qrScanner = new QrScanner(
      videoElem.value,
      (result) => { qrCode.value = result.data },
      { returnDetailedScanResult: true },
    )

    qrScanner.start()
  })
}

function closeCamera () {
  cameraOpen.value = false
  qrScanner.destroy()
  qrScanner = null
  emit('close')
}

async function updateStatus (code) {
  if (loading.value || !code) return false
  clearTimeout(timeout)
  clearTimeout(cardTimeout)
  loadingTimeout = setTimeout(() => loading.value = true, 500)
  
  try {
    const endpoint = props.scanning === 'credentials' ? '/api/credentials/scan' : '/api/codes/scan'
    const { data } = await window.axios.post(endpoint, { mode: mode.value, qr_code: code })
    card.value = data

    if (mode.value === 'IN' && props.scanning === 'credentials') {
      window.Echo.private('Attendees.List').whisper('attendees_list_changed')
      window.Echo.private(`Attendee.Status.${data.attendee.id}`).whisper('checked_in', { checked_in: true })
    } else {
      window.Echo.private('Codes.List').whisper('codes_list_changed')
    }
  } catch (error) {
    card.value = error.response.data
  } finally {
    loading.value = false
    clearTimeout(loadingTimeout)
    timeout = setTimeout(() => {
      qrCode.value = ''
    }, 2000)

    cardTimeout = setTimeout(() => {
      card.value = null
    }, 30000)
  }
}

function clearCard () {
  card.value = null
  qrCode.value = ''
}
</script>

<template>
  <Transition name="card">
    <Teleport to="#teleports">
      <div v-if="cameraOpen" class="scanner-window">
        <div class="scanner-tools bg-black/50">
          <select class="scanner-mode bg-transparent text-white border-white focus:border-yellow focus:ring-yellow focus:ring-2 font-mono" disabled>
            <option value="credentials" :selected="scanning === 'credenials'">Badges</option>
            <option value="codes" :selected="scanning === 'codes'">Vote codes</option>
          </select>
          <select v-model="mode" name="mode" class="scanner-mode bg-transparent text-white border-white focus:border-yellow focus:ring-yellow focus:ring-2 font-mono">
            <option value="IN">IN</option>
            <option value="OUT">OUT</option>
          </select>
          <button @click="closeCamera" class="scanner-close" title="Close camera">
            <Icon icon="ri:close-fill" />
          </button>
        </div>
        <video ref="videoElem" class="scanner-video" />
        <Transition name="card">
          <div v-if="loading" class="scanner-card loading">
            <div class="scanner-card-status">
              <div class="scanner-card-message">
                Loading...
              </div>
            </div>
          </div>
        </Transition>
        <Transition name="card">
          <div v-if="card" :class="['scanner-card', `card-${card.type}`]" @click="clearCard">
            <div class="scanner-card-status">
              <div v-if="card.attendee" class="scanner-card-attendee">
                {{ card.attendee.first_name }} {{ card.attendee.last_name }}
              </div>
              <div v-if="card.message" class="scanner-card-message">
                <span class="icon">
                  <Icon icon="ri:check-fill" v-if="card.type === 'OK'" />
                  <Icon icon="ri:check-double-fill" v-else-if="card.type === 'DOUBLE'" />
                  <span v-else-if="card.type === 'WARNING'">!</span>
                  <Icon icon="ri:close-fill" v-else-if="card.type === 'FAIL'" />
                </span>
                {{ card.message }}
              </div>
              <div v-if="card.attendee" class="scanner-card-group truncate mt-auto">
                {{ card.attendee.group }}
              </div>
            </div>
            <div v-if="card.attendee" :class="['scanner-card-type', `type-${card.attendee.color}`]">
              {{ card.attendee.type }}
            </div>
          </div>
        </Transition>
      </div>
    </Teleport>
  </Transition>
  <div v-if="hasCamera">
    <InputButton @click="openCamera" icon="ri:qr-scan-2-line">
      {{ label }}
    </InputButton>
  </div>
</template>

<style lang="scss" scoped>
.scanner {
  &-window {
    position: fixed;
    inset: 0;
    background: black;
    z-index: 100000;
    display: flex;
  }

  &-tools {
    display: flex;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 10;
    padding: var(--spacer-4);
    justify-content: space-between;
  }

  &-video {
    height: 100%;
    width: 100%;
  }

  &-close {
    color: var(--egp-white);
    font-size: 2.5rem;
  }

  &-card {
    position: fixed;
    bottom: var(--spacer-4);
    left: var(--spacer-4);
    right: var(--spacer-4);

    &-status {
      display: flex;
      flex-direction: column;
      padding: var(--spacer-4);
      background: var(--card-background, var(--egp-white));
      color: var(--card-text, var(--egp-green-pine));
      min-height: 12rem;
      gap: var(--spacer-4);
      line-height: 1;
    }

    &-type {
      padding: var(--spacer-4);
      background: var(--type-background, var(--egp-white));
      color: var(--type-text, var(--egp-green-pine));
      margin-top: var(--spacer-4);
      text-transform: uppercase;
      font-family: var(--font-mono);
      font-weight: bold;
    }

    &-attendee {
      font-size: var(--text-xl);
      font-weight: bold;
    }

    &-group {
      font-family: var(--font-mono);
      font-size: var(--text-sm);
      text-transform: uppercase;
    }

    &-message {
      font-size: var(--text-base);
      text-transform: uppercase;
      font-family: var(--font-mono);
      font-weight: bold;
      display: flex;
      align-items: center;
      gap: var(--spacer-2);

      .icon {
        height: 1.25em;
        width: 1.25em;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 100%;
        background: var(--card-text);
        color: var(--card-background);
      }
    }

    &.card-OK {
      --card-background: var(--egp-green-dark);
      --card-text: var(--egp-white);
    }

    &.card-DOUBLE {
      --card-background: var(--egp-green-pine);
      --card-text: var(--egp-white);
    }

    &.card-WARNING {
      --card-background: var(--egp-orange);
      --card-text: var(--egp-green-pine);
    }

    &.card-FAIL {
      --card-background: var(--egp-red);
      --card-text: var(--egp-white);
    }

    .type-green {
      --type-background: var(--egp-green);
      --type-text: var(--egp-white);
    }

    .type-purple {
      --type-background: var(--egp-purple);
      --type-text: var(--egp-white);
    }

    .type-pink {
      --type-background: var(--egp-pink);
      --type-text: var(--egp-green-pine);
    }

    .type-red {
      --type-background: var(--egp-red);
      --type-text: var(--egp-white);
    }

    .type-gray {
      --type-background: var(--egp-gray-300);
      --type-text: var(--egp-green-pine);
    }

    .type-yellow {
      --type-background: var(--egp-yellow);
      --type-text: var(--egp-green-pine);
    }

    .type-green-dark {
      --type-background: var(--egp-green-pine);
      --type-text: var(--egp-white);
    }
  }
}
</style>