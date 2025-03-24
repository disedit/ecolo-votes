<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'
import { Vue3Marquee } from 'vue3-marquee'
import JSConfetti from '../confetti.js'
import GrayLayout from '@/Layouts/GrayLayout.vue'
import Badge from '@/Components/Badge/Badge.vue'

defineOptions({
    layout: GrayLayout
})

const props = defineProps({
    edition: { type: Object, required: true },
    errors: { type: Object, default: () => null },
    auth: { type: Object, required: true },
    flash: { type: Object, default: () => null },
    attendee: { type: Object, required: true }
})

const showWelcome = ref(false)
const showCheckedIn = ref(false)
const jsConfetti = new JSConfetti()

const rainbow = new Image(100, 100)
rainbow.src = "/images/confetti/rainbow.svg"
const moon = new Image(100, 100)
moon.src = "/images/confetti/moon.svg"
const star = new Image(100, 100)
star.src = "/images/confetti/star.svg"
const heart = new Image(100, 100)
heart.src = "/images/confetti/heart.svg"
const shamrock = new Image(100, 100)
shamrock.src = "/images/confetti/shamrock.svg"

onMounted(() => {
    if (props.attendee.checked_in) {
        showCheckedIn.value = true
    }

    window.Echo.channel(`Attendee.Status.${props.attendee.id}`)
        .listen('.attendee.checked_in_status_changed', ({ mode }) => {
            if (mode === 'in') {
                showWelcome.value = true
                showCheckedIn.value = true
                throwConfetti()

                setTimeout(() => {
                    showWelcome.value = false
                    router.visit('/code')
                }, 6000)
            } else {
                showCheckedIn.value = false
                showWelcome.value = false
            }
        })
})

function throwConfetti () {
    jsConfetti.addConfetti({
        emojis: [rainbow, moon, shamrock, star, heart],
        emojiSize: 100,
        confettiNumber: 40,
    })
}
</script>

<template>
    <Head title="Badge" />

    <div class="overflow-clip lg:h-16 h-12 relative z-20" aria-live="assertive">
        <Transition name="swipe" mode="out-in">
            <Vue3Marquee v-if="showCheckedIn" clone class="bg-yellow font-bold">
                <div class="flex gap-2 items-center p-2 lg:p-4">
                    <Icon icon="tabler:confetti" />
                    {{ $t('badge.welcome.O') }}
                </div>
                <div class="flex gap-2 items-center p-2 lg:p-4">
                    <Icon icon="mdi:emoji-excited-outline" />
                    {{ $t('badge.checked_in') }}
                </div>
            </Vue3Marquee>
        </Transition>
    </div>

    <Transition name="swipe">
        <div v-if="showWelcome" class="fixed bg-pink inset-0 z-10 p-site" @click="showWelcome = false">
            <h2 class="font-headline font-bold pt-32 text-3xl text-balance leading-tight">
                {{ $t('badge.welcome_long.O', { name: edition.title }) }}
            </h2>
        </div>
    </Transition>

    <div class="lg:min-h-fill bg-sand flex flex-col items-center justify-center p-4">
        <Transition name="slide">
            <div v-if="!attendee.checked_in && !showCheckedIn" :class="['px-site pb-4 text-sm opacity-75', { '-mt-10': !showCheckedIn }]">
                {{ $t('badge.show_ticket') }}
            </div>
        </Transition>
        <div class="grid grid-cols-1 gap-4 w-full max-w-[1000px]">
            <Badge class="mx-auto" />
        </div>
    </div>
</template>
