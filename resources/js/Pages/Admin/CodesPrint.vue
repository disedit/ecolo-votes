<script setup>
import { computed, onMounted, getCurrentInstance, nextTick } from 'vue'
import { Icon } from '@iconify/vue'
import QRCode from 'qrcode'
import NoLayout from '@/Layouts/NoLayout.vue'
defineOptions({ layout: NoLayout })

const props = defineProps({
  edition: { type: Object, required: true },
  codes: { type: Array, required: true },
  base_url: { type: String, required: true }
})

async function generateQrs () {
  await Promise.all(props.codes.map(async (code) => {
    code.qr = await QRCode.toDataURL(props.base_url + '/code/' + code.code, { errorCorrectionLevel: 'H' })
    return code
  }))

  const instance = getCurrentInstance()
  instance?.proxy?.$forceUpdate()
  setTimeout(() => {
    window.print()
  }, 1000)
}

const domain = computed(() => new URL(props.base_url).hostname)

onMounted(() => {
  generateQrs()
})
</script>

<template>
  <div class="bg-white text-black">
    <div class="grid bg-white">
      <div v-for="code in codes" :key="code.code" class="ref">
        <p class="text-md">{{ edition.title }}</p>
        <div class="flex flex-col items-center justify-center my-auto">
          <img :src="code.qr" />
          <h1 class="font-mono font-bold text-lg">{{ code.code }}</h1>
        </div>

        <div class="instructions my-auto flex flex-col gap-4">
          <p>
            <Icon icon="ri:camera-2-line" />
            {{ $t('print.codes.instructions') }}</p>
          <p>
            <Icon icon="ri:keyboard-box-line" />
            <i18n-t keypath="print.codes.alternative" tag="span">
              <span class="underline">{{ domain }}</span>
              <span class="font-mono font-bold">{{ code.code }}</span>
            </i18n-t>
          </p>
          <p class="font-bold">
            <Icon icon="ri:alert-line" />
            {{ $t('print.codes.do_not_share') }}
          </p>
        </div>
        <div class="absolute inset-0 border-b border-r" />
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
  .grid {
    display: grid;
    width: 297mm;
    grid-template-columns: repeat(3, 1fr);
    grid-auto-rows: 210mm;
  }

  .ref {
    display: flex;
    padding: 10mm;
    flex-direction: column;
    position: relative;
  }

  .instructions {
    p {
      display: flex;
      gap: .75em;
    }

    svg {
      flex-shrink: 0;
      position: relative;
      top: .25em;
    }
  }
</style>