<script setup>
import { computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import GlobalHeader from '@/Components/Global/Header.vue'
import GlobalCard from '@/Components/Global/Card.vue'
import TextInput from '@/Components/Inputs/TextInput.vue'
import ButtonInput from '@/Components/Inputs/Button.vue'

defineProps({
    flash: { type: Object, required: true }
})

defineOptions({
    layout: GuestLayout
})

const form = useForm({
  email: ''
})

function login() {
    form.post('/login', {
        preserveScroll: true,
        //onSuccess: () => form.reset('email'),
    })
}

const inboxUrl = computed(() => {
    if (!form.email) return null;
    const [name, domain] = form.email.split('@')
    return `https://${domain}`
})
</script>

<template>
    <Head title="Log in" />
    <GlobalHeader />
    <GlobalCard title="Log in" class="container-sm">
        <div v-if="flash.status === 'sent'">
            <p>
                If the email address was on our database, you will receive an email with a link to access the platform.
            </p>
            <p v-if="inboxUrl" class="mt-2 font-bold">
                <a :href="inboxUrl" target="_blank" rel="noopener" class="flex items-center gap-2 underline text-green-pine">
                    <Icon icon="ri:mail-send-line" />
                    Check your inbox
                </a>
            </p>
        </div>
        <form v-else @submit.prevent="login" class="flex flex-col gap-4">
            <p v-if="flash.message" class="font-bold bg-yellow px-4 py-2">
                {{ flash.message }}
            </p>
            <p v-else class="text-base">
                If you have registered for the congress, enter your email below to access the platform.
            </p>
            <TextInput
                type="email"
                name="email"
                label="Email address"
                placeholder="Email address"
                variant="gray"
                label-sr-only
                size="lg"
                autofocus
                required
                v-model="form.email"
                :error="form.errors?.email"
            />

            <div class="flex justify-end">
                <ButtonInput type="submit" size="md" :loading="form.processing" :disabled="form.processing">
                    Send me a link -&gt;

                    <template #loading>
                        Submitting request...
                    </template>
                </ButtonInput>
            </div>
        </form>
    </GlobalCard>
</template>
