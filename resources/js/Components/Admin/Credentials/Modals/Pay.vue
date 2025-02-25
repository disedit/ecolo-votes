<script setup>
import { ref, onMounted } from 'vue'
import GlobalModal from '@/Components/Global/Modal.vue'
import FeeSelect from '@/Components/Inputs/FeeSelect.vue'
import CheckboxInput from '@/Components/Inputs/CheckboxInput.vue'

const props = defineProps({
  attendeeId: { type: Number, required: true }
})

const emit = defineEmits(['close', 'refresh'])

const attendee = ref(null)
const selectedFee = ref(null)
const notify = ref(true)
const pending = ref(false)

onMounted(async () => {
  const { data } = await window.axios.get('/api/credential/' + props.attendeeId)
  attendee.value = data
  selectedFee.value = attendee.value.fees[0]
})

async function markAsPaid() {
  const { data } = await window.axios.post(`/api/credential/${props.attendeeId}/pay`, { fee: selectedFee.value, notify: notify.value, pending: pending.value })
  emit('refresh')
}
</script>

<template>
  <GlobalModal v-if="attendee" :width="600" @close="emit('close')">
    <template #title>
      <h1>Pay</h1>
    </template>
    <div>
      <h2 class="font-bold mt-6">Pay by credit card or direct debit</h2>
      <p class="my-4">
        Copy and paste the following URL on an incognito window of your browser.
        This URL will log you in as <strong>{{ attendee.user.first_name }} {{ attendee.user.last_name }}</strong>
      </p>
      <input
        type="url"
        class="bg-gray-200 font-mono text-sm w-full"
        :value="`https://congress.europeangreens.eu/auto/${attendee.loginToken.token}`"
      />
      <hr class="my-6" />
      <h2 class="font-bold">Pay in cash</h2>
      <FeeSelect
        :fees="attendee.fees"
        v-model="selectedFee"
        class="my-4"
      />
      <div class="my-4">
        <CheckboxInput
          name="notify"
          label="Send ticket via email"
          v-model="notify"
        />
        <CheckboxInput
          name="pending"
          label="Mark as pending"
          v-model="pending"
        />
      </div>
      <InputButton @click="markAsPaid()" flat>
        Issue ticket
      </InputButton>
    </div>
  </GlobalModal>
</template>
