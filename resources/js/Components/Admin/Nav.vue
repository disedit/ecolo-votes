<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
</script>

<template>
  <div class="container padded">
    <Link href="/admin" class="font-bold">Admin</Link> / <slot />
  </div>
</template>