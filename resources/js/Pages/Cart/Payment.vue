<script setup>
import { useScriptTag } from '@vueuse/core'
import { Head } from '@inertiajs/vue3'
import { onMounted, onUnmounted } from 'vue'

const { load } = useScriptTag('https://js.stripe.com/v3/')

const props = defineProps({
    auth: { type: Object, required: true },
    payment: { type: Object, required: true },
    stripeKey: { type: String, required: true }
})

let stripe
let checkout

onMounted(async () => {
    await load()
    stripe = Stripe(props.stripeKey)
    const fetchClientSecret = () => {
        return props.payment.checkout_client_secret
    }

    checkout = await stripe.initEmbeddedCheckout({ fetchClientSecret })
    checkout.mount('#checkoutForm')
})

onUnmounted(() => {
    checkout.destroy()
})
</script>

<template>
    <Head title="Payment" />
    <div class="container padded" style="--container-max-width: 1200px">
        <div id="checkoutForm" />
    </div>
</template>
