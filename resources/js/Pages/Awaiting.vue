<script setup>
import { Head } from '@inertiajs/vue3'
import HeaderLayout from '@/Layouts/HeaderLayout.vue'
import GlobalCard from '@/Components/Global/Card.vue'

defineOptions({ layout: HeaderLayout })
</script>

<template>
    <Head title="Registration under review" />
    <GlobalCard title="Registration">
        <p class="text-md">
            We are still processing your registration. As soon as we review it, you will receive a notification on your email.
        </p>
    </GlobalCard>
</template>

