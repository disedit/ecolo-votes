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
  code: ''
})

function login() {
    form.post('/code', {
        preserveScroll: true,
        onSuccess: () => form.reset('code'),
    })
}
</script>

<template>
    <Head title="Log in" />
    <GlobalHeader />
    <GlobalCard title="Log in" class="container-sm">
        <form @submit.prevent="login" class="flex flex-col gap-4">
            <p v-if="flash.message" class="font-bold bg-yellow px-4 py-2">
                {{ flash.message }}
            </p>
            <p v-else class="text-base">
                {{ $t('code_login.label') }}
            </p>
            <TextInput
                type="text"
                name="code"
                label="Code"
                placeholder="Code"
                variant="gray"
                label-sr-only
                size="lg"
                autofocus
                required
                v-model="form.code"
                :error="form.errors?.code"
            />

            <div class="flex justify-end">
                <ButtonInput type="submit" size="md" :loading="form.processing" :disabled="form.processing">
                    {{ $t('code_login.button') }} -&gt;

                    <template #loading>
                        {{ $t('code_login.submitting') }}
                    </template>
                </ButtonInput>
            </div>
        </form>
    </GlobalCard>
</template>
