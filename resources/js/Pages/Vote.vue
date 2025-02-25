<script setup>
import { ref, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import GrayLayout from '@/Layouts/GrayLayout.vue'
import GlobalMenu from '@/Components/Global/Menu.vue'
import VoteForm from '@/Components/Vote/Form.vue'
import VoteAnnouncer from '@/Components/Vote/Announcer.vue'
import VoteStatus from '@/Components/Vote/Status.vue'
import VoteWaiting from '@/Components/Vote/Waiting.vue'
import VoteDebating from '@/Components/Vote/Debating.vue'
import VoteSubmitted from '@/Components/Vote/Submitted.vue'

defineOptions({
    layout: GrayLayout
})

const props = defineProps({
    auth: { type: Object, required: true },
    flash: { type: Object, default: () => null },
    vote: { type: Object, default: null }
})

const loading = ref(false)

onMounted(() => {
  window.Echo.private('Votes').listenForWhisper('refreshVotes', () => refresh())
  window.Echo.connector.pusher.connection.bind('connected', () => refresh())
  router.on('start', () => loading.value = true)
  router.on('finish', () => loading.value = false)
})

function refresh() {
  router.reload({ only: ['vote'] })
}
</script>

<template>
    <Head title="Vote" />
    <div class="min-h-full-minus-nav flex flex-col pt-10">
      <VoteStatus @refresh="refresh" :loading="loading" class="fixed top-navbar left-0 right-0 z-50" />
      <VoteAnnouncer :vote="vote" class="fixed top-[calc(var(--navbar-safe-area)_+_2.5rem)] left-0 right-0 z-10" />
      <div class="container padded grow flex items-center justify-center">
        <VoteWaiting v-if="!vote" />
        <VoteSubmitted v-else-if="vote.voted_at" :vote="vote" />
        <VoteDebating v-else-if="vote.debate" :vote="vote" />
        <VoteForm v-else :vote="vote" :code-exception="!!auth.user.code_exception" @refresh="refresh" />
      </div>
      <Transition name="fly-bottom">
        <GlobalMenu v-if="!vote || vote.voted_at || vote.debate" :show="['badge', 'votes', 'menu']" />
      </Transition>
    </div>
</template>
