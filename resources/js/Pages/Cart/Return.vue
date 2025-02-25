<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'
import GlobalCard from '@/Components/Global/Card.vue'

const props = defineProps({
    status: { type: String, required: true },
    payment_status: { type: String, required: true },
    payment_declined: { type: Boolean, default: false }
})

const checking = ref(false)
const checked = ref(0)
let interval

onMounted(() => {
    interval = setInterval(() => {
        if (checked.value < 5) router.reload()
        checking.value = true
        checked.value++
    }, 5000)
})

onUnmounted(() => {
    clearInterval(interval)
})
</script>

<template>
    <Head title="Payment status" />
    <div class="payment-status">
        <GlobalCard
            v-if="status !== 'complete'"
            title="Payment Failed"
        >
            <h1>Oops...</h1>
            <p>There was a problem processing your payment. Please <Link href="/">try again</Link> or contact us at <a href="mailto:congress@europeangreens.eu">congress@europeangreens.eu</a></p>
        </GlobalCard>

        <GlobalCard
            v-else-if="status === 'complete' && payment_status === 'unpaid'"
            title="Processing Payment"
        >
            <template v-if="!checking">
                <div class="text-lg flex justify-center my-5">
                    <Icon icon="line-md:loading-loop" />
                </div>
                <p class="text-center">Checking payment status. This may take a few seconds...</p>
            </template>
            <template v-else-if="!payment_declined">
                <p class="mb-2">Your payment is still being processed. If you chose to pay via wire transfer, it may take some days for the payment to be confirmed.</p>
                <p class="mb-2 font-bold">We will email you as soon as your tickets are confirmed.</p>
                <p>If you have any questions, you can contact us at <a href="mailto:congress@europeangreens.eu">congress@europeangreens.eu</a></p>
            </template>
            <template v-else>
                <p class="mb-2"><strong>We had a problem processing with your payment.</strong></p>
                <p>Please go back and <Link href="/">complete the payment again</Link>.</p>
            </template>
        </GlobalCard>

        <GlobalCard
            v-else-if="status === 'complete' && payment_status === 'paid'"
            title="Registration complete"
        >
            <h1>Thank you!</h1>
            <p>We have recieved your payment and emailed you your ticket.</p>
        </GlobalCard>
    </div>
</template>

<style lang="scss" scoped>
.payment-status {
    min-height: calc(100vh - var(--navbar-safe-area) - var(--footer-safe-area));
    display: flex;
    align-items: center;
    justify-content: center;

    h1 {
        font-size: var(--text-lg);
        font-weight: bold;
        margin-bottom: var(--spacer-2);
    }
}
</style>