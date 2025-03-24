<script setup>
import { computed, ref, onMounted } from 'vue'
import QRCode from 'qrcode'
import { usePage, Link } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'

const page = usePage()
const attendee = computed(() => page.props.attendee)
const QrCode = ref()

onMounted(() => {
  QRCode.toDataURL(attendee.value.qr_code.toString(), {
    color: {
        dark: '#000000',
        light: '#ffffff'
    }
  })
  .then(url => { QrCode.value = url })
})
</script>

<template>
  <section class="badge bg-white px-6 pt-6 rounded-2xl shadow-xl">
    <div class="badge-logo">
      <img src="../../../images/logos/ecolo.svg" alt="Ecolo" />
    </div>
    <div class="badge-name leading-tight">
      <div class="text-xl font-bold">
        {{ attendee.first_name }}
      </div>
      <div class="text-xl font-bold">
        {{ attendee.last_name }}
      </div>
    </div>
    <div class="badge-qrcode" aria-hidden="true">
        <img :src="QrCode" alt="" />
    </div>

    <div :class="['badge-color rounded-t-xl', `color-${attendee.type.color}`]">
      {{ attendee.type.name }}
    </div>
  </section>
</template>

<style lang="scss" scoped>
.badge {
  display: flex;
  flex-direction: column;
  gap: var(--spacer-4);
  width: 100%;
  max-width: 450px;
  aspect-ratio: 1 / 1.35;
  justify-content: space-between;

  &-logo {
    img {
      height: 2.5rem;
    }
  }

  &-qrcode {
    display: flex;
    justify-content: center;
  }

  &-color {
    position: relative;
    background: var(--color);
    height: 3rem;
    color: var(--egp-white);
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  &-delegate {
    display: flex;
    align-items: center;
    padding: var(--spacer-4);
    gap: var(--spacer-4);
    background: var(--egp-green);

    &:hover {
      background: var(--egp-green-dark);
      color: var(--egp-white);
    }
  }
}
</style>