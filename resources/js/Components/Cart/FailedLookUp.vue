<script setup>
import { ref } from 'vue'
import { Icon } from '@iconify/vue'

const props = defineProps({
    unassignedDelegates: { type: Array, default: () => [] },
    id: { type: Number, required: true }
})

const emit = defineEmits(['link'])

const delegate = ref(null)

function linkDelegate() {
  emit('link', delegate.value, props.id)
}

function removeDelegate() {
  emit('remove', props.id)
}
</script>

<template>
  <div class="p-4 text-rbase border-2 border-t-2 border-b-2 border-red mb-4 flex flex-col gap-2">
    <p><strong>We couldn’t match this address to any registered delegate.</strong></p>
    <template v-if="!unassignedDelegates || !unassignedDelegates.length">
      <p>Remember that any delegates whose fee you are paying must also register individually using the above-mentioned email address. You can come back to this page after they register to complete the payment, or you can remove them from your ticket.</p>
      <div class="my-4 flex items-center justify-evenly gap-4">
        <a :href="`https://form.jotform.com/${$page.props.edition.form_id}`" target="_blank" class="flex items-center gap-2 bg-green-dark font-mono uppercase py-2 px-4 text-white no-underline font-bold hover:bg-green-pine hover:text-white">
          <Icon icon="ri:file-edit-line" />
          Register delegate
        </a>
        <button type="button" @click="removeDelegate" class="flex items-center gap-2 font-mono uppercase py-2 px-4 text-red no-underline font-bold hover:bg-gray-100 hover:text-red">
          <Icon icon="ri:delete-bin-2-line" />
          Remove delegate
        </button>
      </div>
      <p>Contact us at <a href="mailto:congress@europeangreens.eu">congress@europeangreens.eu</a> if they have already registered but you still see this error.</p>
    </template>
    <template v-else>
      <p>Please select an unallocated member from the list below to pay for their ticket.</p>
      <div class="flex gap-2">
        <select v-model="delegate" class="focus:ring-2 focus:ring-green-pine focus:border-green-pine grow">
          <option :value="null">Select a delegate from the list</option>
          <option
            v-for="delegate in unassignedDelegates"
            :value="delegate.attendee_id"
            :key="delegate.id">
            {{ delegate.first_name }} {{ delegate.last_name }}
          </option>
        </select>
        <InputButton type="button" variant="pine" flat :disabled="!delegate" @click="linkDelegate">
          Link delegate
        </InputButton>
      </div>
      <p class="mt-4">If you can't find the correct delegate, you may
        <a :href="`https://form.jotform.com/${$page.props.edition.form_id}`" target="_blank">register them</a>
        and come back to this form to finish the payment. Otherwise,
        you can <a href="#" @click.prevent="removeDelegate" class="text-red hover:text-red">remove them</a>
        from your ticket.</p>
    </template>
  </div>
</template>