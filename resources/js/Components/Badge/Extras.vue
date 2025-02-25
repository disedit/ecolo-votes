<script setup>
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import HoverButton from '@/Components/Inputs/HoverButton.vue'

const page = usePage()
const extras = computed(() => page.props.extras)

function signUp(extraId) {
  router.post(`/extras/${extraId}/add`, null, { preserveScroll: true })
}

function cancel(extraId) {
  router.post(`/extras/${extraId}/remove`, null, { preserveScroll: true })
}
</script>

<template>
  <div class="">
    <p class="text-sm">Would you like to add an extra?</p>
    <ul class="border-t border-b border-gray-400 font-mono uppercase mt-2">
      <li v-for="extra in extras" :key="extra.id" class="flex items-center">
        <span>{{ extra.name }}</span>
        <span class="ms-auto">
          <HoverButton
            v-if="extra.user.signed_up"
            @click="cancel(extra.id)"
            flat
            :label="{ default: 'Added', hover: 'Cancel' }"
            :variant="{ default: 'pine', hover: 'red' }"
          />
          <InputButton v-else-if="extra.full" variant="gray" disabled>
            Full
          </InputButton>
          <InputButton
            v-else
            @click="signUp(extra.id)"
            flat
            icon="ri:user-add-line">
            Add
          </InputButton>
        </span>
      </li>
    </ul>
  </div>
</template>