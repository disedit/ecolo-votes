<script setup>
import { Head } from '@inertiajs/vue3'
import AdminNavigation from '@/Components/Admin/Nav.vue'
import QrScanner from '@/Components/Admin/Credentials/QrScanner.vue'
import GrayLayout from '@/Layouts/GrayLayout.vue'

defineOptions({ layout: GrayLayout })
</script>

<template>
  <Head :title="$t('admin.scanner.title')" />
  <div class="container-xl">
    <AdminNavigation>
      {{ $t('admin.scanner.title') }}
    </AdminNavigation>

    <div class="container padded-x py-4 flex gap-4 items-center">  
      <QrScanner />
    </div>
  </div>
</template>
