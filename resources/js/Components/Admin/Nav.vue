<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="container padded">
    <Link href="/admin" class="font-bold">{{ $t('admin.title') }}</Link> / <slot />
  </div>
</template>