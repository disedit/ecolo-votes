<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'
import { Vue3Marquee } from 'vue3-marquee'
import JSConfetti from '../confetti.js'
import GrayLayout from '@/Layouts/GrayLayout.vue'
import GlobalMenu from '@/Components/Global/Menu.vue'
import GlobalLinks from '@/Components/Global/Links.vue'
import Badge from '@/Components/Badge/Badge.vue'
import Venue from '@/Components/Badge/Venue.vue'

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

const showMessage = ref(false)
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
    if (!props.attendee.first_checked_in) {
        showMessage.value = true
        throwConfetti()
    } else if (props.attendee.checked_in) {
        showCheckedIn.value = true
    }

    window.Echo.private(`Attendee.Status.${props.attendee.id}`)
        .listenForWhisper('checked_in', () => {
            showWelcome.value = true
            showCheckedIn.value = true
            throwConfetti()

            setTimeout(() => {
                showWelcome.value = false
                if (props.attendee.votes > 0) {
                    router.visit('/vote')
                } else {
                    router.visit('/info')
                }
            }, 6000)
        })

    window.Echo.private(`Attendee.Status.${props.attendee.id}`)
        .listenForWhisper('checked_out', () => {
            showCheckedIn.value = false
            showWelcome.value = false
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
            <Vue3Marquee v-if="showMessage && !showWelcome" clone class="bg-yellow font-bold">
                <div class="flex gap-2 items-center p-2 lg:p-4">
                    <Icon icon="tabler:confetti" />
                    Your ticket has been confirmed
                </div>
                <div class="flex gap-2 items-center p-2 lg:p-4">
                    <Icon icon="mdi:emoji-excited-outline" />
                    See you in Dublin!
                </div>
            </Vue3Marquee>
            <Vue3Marquee v-else-if="showCheckedIn" clone class="bg-yellow font-bold">
                <div class="flex gap-2 items-center p-2 lg:p-4">
                    <Icon icon="tabler:confetti" />
                    Welcome
                </div>
                <div class="flex gap-2 items-center p-2 lg:p-4">
                    <Icon icon="mdi:emoji-excited-outline" />
                    You have checked in
                </div>
            </Vue3Marquee>
        </Transition>
    </div>

    <Transition name="swipe">
        <div v-if="showWelcome" class="fixed bg-pink inset-0 z-10 p-site" @click="showWelcome = false">
            <h2 class="font-headline uppercase pt-28 text-3xl text-balance  leading-tight">
                Welcome to the 39th Congress
                of the European Green Party
            </h2>
            <div v-if="attendee.votes > 0" class="pt-6 text-lg">
                <Link href="/vote" class="bg-green-pine text-white no-underline py-1 px-4 font-mono font-bold hover:text-white">
                    Vote
                </Link>
            </div>
            <div v-else class="pt-6 text-lg">
                <Link href="/info" class="bg-green-pine text-white no-underline py-1 px-4 font-mono font-bold hover:text-white">
                    Continue
                </Link>
            </div>
        </div>
    </Transition>

    <div class="lg:min-h-fill bg-gray-200 flex flex-col items-center justify-center p-4">
        <Transition name="slide">
            <div v-if="!attendee.checked_in && !showCheckedIn" :class="['px-site pb-4 text-sm opacity-75', { '-mt-10': !showCheckedIn && !showMessage }]">
                Show this ticket at the entrance to check in
            </div>
        </Transition>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 w-full max-w-[1000px]">
            <Badge class="mx-auto" />
            <Venue class="mx-auto" />
        </div>
    </div>

    <div class="md:hidden p-site pt-0">
        <GlobalLinks :links="edition.links" :include="{ votes: true }" />
    </div>

    <div class="text-center my-10 p-site">
        <button @click="throwConfetti" class="text-center text-sm opacity-75 transition hover:opacity-100 text-balance">
            <Icon icon="tabler:confetti" class="inline-block mr-1" />
            <span v-if="showMessage">May I have more environmentally-friendly confetti, please?</span>
            <span v-else>Environmentally-friendly confetti!</span>
        </button>
    </div>

    <Transition name="fly-bottom">
        <GlobalMenu v-if="attendee.checked_in" :show="attendee.votes > 0 ? ['vote', 'votes', 'menu'] : ['menu']" />
    </Transition>
</template>
