<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import ScreenLayout from '@/Layouts/ScreenLayout.vue'
import ScreenCover from '@/Components/Screen/Cover.vue'
import ScreenVote from '@/Components/Screen/Vote.vue'
import ScreenStatus from '@/Components/Screen/Status.vue'
import ScreenFull from '@/Components/Screen/Full.vue'

defineOptions({
    layout: ScreenLayout
})

const props = defineProps({
    auth: { type: Object, required: true },
    errors: { type: Object, default: () => null },
    flash: { type: Object, default: () => null },
    edition: { type: Object, required: true },
    vote: { type: Object, default: null },
    code: { type: Object, default: null },
    willHideIn: { type: Number, default: null }
})

onMounted(() => {
    window.Echo.private('Votes').listenForWhisper('refreshVotes', () => {
        router.reload()
    })

    refreshScreen()
})

const ongoingVote = computed(() => !!props.vote)

let interval
let timeout
watch(ongoingVote, () => refreshScreen())
watch(() => props.vote?.debate, () => refreshScreen())
watch(() => props.vote?.recentlyClosed, () => refreshScreen())

function refreshScreen() {
    if (ongoingVote.value && !props.vote.recentlyClosed && !props.vote.debate) {
        if (!interval) {
            interval = setInterval(() => {  
                console.log('Refreshing...')
                router.reload()
            }, 1000)
        }
    } else  {
        clearInterval(interval)
        interval = null

        if (props.vote?.recentlyClosed) {
            timeout = setTimeout(() => router.reload(), props.willHideIn)
        }
    }
}
</script>

<template>
    <div class="screen">
        <ScreenStatus />
        <ScreenFull />
        <ScreenCover :edition="edition" :active="ongoingVote" />
        <Transition name="fade">
            <ScreenVote v-if="ongoingVote" :vote="vote" :code="code" />
        </Transition>
    </div>
</template>

<style lang="scss">
.screen {
    --screen-padding: 1.5vw;
    --screen-nav-height: 11.5vh;
}
</style>