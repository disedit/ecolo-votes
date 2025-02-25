<script setup>
import { ref, onMounted } from 'vue'
import GlobalModal from '@/Components/Global/Modal.vue'
import InvoiceSummary from './InvoiceSummary.vue'

const props = defineProps({
  invoiceId: { type: Number, required: true }
})

const emit = defineEmits(['close', 'refresh'])

const payment = ref(null)

onMounted(async () => {
  const { data } = await window.axios.get('/api/payment/' + props.invoiceId)
  payment.value = data
})

async function submit () {
  const { data } = await window.axios.post('/api/payment/' + props.invoiceId + '/delete')
  emit('refresh')
}
</script>

<template>
  <GlobalModal v-if="payment" :width="600" @close="emit('close')">
    <template #title>
      <h1 class="text-red">Delete invoice</h1>
    </template>
    <InvoiceSummary :payment="payment" />
    <form @submit.prevent="submit" class="flex flex-col gap-4 mt-6">
      <p>
        <strong>
          Are you sure you want to delete this invoice?
          The ticket will be invalidated.
          This action cannot be undone.
        </strong>
      </p>
      <div class="flex mt-6 justify-between">
        <InputButton type="submit" variant="red" flat>
          Delete invoice
        </InputButton>
        <InputButton type="button" variant="gray" @click="emit('close')" flat>
          Cancel
        </InputButton>
      </div>
    </form>
  </GlobalModal>
</template>
