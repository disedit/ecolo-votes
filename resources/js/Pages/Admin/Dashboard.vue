<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'
import GrayLayout from '@/Layouts/GrayLayout.vue'

defineOptions({ layout: GrayLayout })

defineProps({
  auth: { type: Object, required: true }
})
</script>

<template>
  <Head :title="$t('admin.title')" />

  <div class="container container-lg padded">
    <h1 class="font-headline text-green text-2xl">
      {{ $t('admin.title') }}
    </h1>
    <div class="grid md:grid-cols-3 gap-4 mt-5" v-if="auth.user.role === 'admin'">
      <Link href="/admin/credentials" class="dashboard-link">
        <Icon icon="carbon:credentials" class="text-xl" />
        {{ $t('admin.dashboard.access_control') }}
      </Link>
      <Link href="/admin/codes" class="dashboard-link">
        <Icon icon="ri:qr-code-line" class="text-xl" />
        {{ $t('admin.dashboard.codes') }}
      </Link>
      <Link href="/admin/votes" class="dashboard-link">
        <Icon icon="ri:hand" class="text-xl" />
        {{ $t('admin.dashboard.vote_manager') }}
      </Link>
      <Link href="/admin/screen" class="dashboard-link">
        <Icon icon="ri:fullscreen-line" class="text-xl" />
        {{ $t('admin.dashboard.screen') }}
      </Link>
    </div>
    <div class="grid md:grid-cols-3 gap-4 mt-5" v-if="auth.user.role === 'credentials'">
      <Link href="/admin/scanner" class="dashboard-link">
        <Icon icon="carbon:credentials" class="text-xl" />
        {{ $t('admin.dashboard.badge_scanner') }}
      </Link>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.dashboard-link {
  @apply bg-white rounded-lg shadow-lg flex flex-col p-4 text-lg no-underline
          font-bold min-h-44 justify-between transition hover:-translate-y-1 leading-tight;
}
</style>