<script setup>
import { computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import HeaderLayout from '@/Layouts/HeaderLayout.vue'
import GlobalCard from '@/Components/Global/Card.vue'
import SingleCart from '@/Components/Cart/SingleCart.vue'
import MultipleCart from '@/Components/Cart/MultipleCart.vue'
import InvoicingOptions from '@/Components/Cart/InvoicingOptions.vue'
import TotalAmount from '@/Components/Cart/TotalAmount.vue'

defineOptions({ layout: HeaderLayout })

const props = defineProps({
    auth: { type: Object, required: true },
    attendee: { type: Object, required: true },
    fees: { type: Object, required: true },
    edition: { type: Object, required: true },
    subdelegates: { type: Array, default: null },
    unassignedDelegates: { type: Array, default: null }
})

const form = useForm({
    cart: null,
    invoice: null,
})

function submitCart() {
    form.post('/cart/payment')
}

const total = computed(() => form.cart ? form.cart.reduce((sum, item) => {
    const amount = parseFloat(item.amount) || 0
    return sum + amount
}, 0) : 0)

const errors = computed(() => form.cart?.filter(item => !item.amount).length > 0)
const buyingInBulk = computed(() => props.subdelegates && props.subdelegates.length > 0)
</script>

<template>
    <Head title="Cart" />
    <GlobalCard title="Registration" class="mb-16">
        <form @submit.prevent="submitCart">
            <SingleCart
                v-if="!buyingInBulk"
                :user="auth.user"
                :attendee="attendee"
                :fees="fees"
                @updated="(cart) => form.cart = cart"
            />
            <MultipleCart
                v-else
                :user="auth.user"
                :attendee="attendee"
                :fees="fees"
                :subdelegates="subdelegates"
                :unassigned-delegates="unassignedDelegates"
                @updated="(cart) => form.cart = cart"
            />
            <TotalAmount :amount="total" />
            <InvoicingOptions
                :user="auth.user"
                :default="!buyingInBulk ? 'myself' : 'organisation'"
                @updated="(invoice) => form.invoice = invoice"
            />
            <div v-if="!errors" class="flex flex-col md:flex-row mt-10 md:mt-20">
                <p class="text-sm md:self-end text-gray-700 max-w-[200px] order-2 md:order-1 mt-2 md:mt-0">Payment will be handled by our payment processor <strong>Stripe</strong>.</p>
                <InputButton type="submit" size="lg" variant="yellow" :loading="form.processing" :disabled="form.processing" class="ms-auto shrink-0 focus-black order-1 md:order-2 w-full md:w-auto">
                    Proceed to payment -&gt;

                    <template #loading>
                        Submitting...
                    </template>
                </InputButton>
            </div>
            <div v-else class="text-red font-bold mt-10">
                Please review the errors highlighted in red before proceeding to the payment.
            </div>
        </form>
    </GlobalCard>
</template>
