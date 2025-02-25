<script setup>
import { ref, onMounted } from 'vue'
import GlobalModal from '@/Components/Global/Modal.vue'
import TextInput from '@/Components/Inputs/TextInput.vue'
import SelectInput from '@/Components/Inputs/SelectInput.vue'
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
  const { data } = await window.axios.post('/api/payment/' + props.invoiceId + '/update', payment.value)
  emit('refresh')
}
</script>

<template>
  <GlobalModal v-if="payment" :width="600" @close="emit('close')">
    <template #title>
      <h1>Edit invoice</h1>
    </template>
    <InvoiceSummary :payment="payment" />
    <form @submit.prevent="submit" class="flex flex-col gap-4 mt-6">
      <TextInput
        name="invoiceName"
        label="Invoice Name"
        v-model="payment.receipt.invoice.name"  
      />
      <TextInput
        name="invoiceAddress"
        label="Invoice Address"
        v-model="payment.receipt.invoice.address"  
      />
      <TextInput
        name="invoiceVAT"
        label="Invoice VAT"
        v-model="payment.receipt.invoice.vat"  
      />
      <SelectInput
        name="invoiceStatus"
        label="Payment status"
        :options="[
          { value: 'pending', label: 'Pending' },
          { value: 'paid', label: 'Paid' },
          { value: 'refunded', label: 'Refunded' },
          { value: 'declined', label: 'Declined' }
        ]"
        v-model="payment.status"
      />
      <div class="flex mt-6 justify-between">
        <InputButton type="submit" flat>
          Update invoice
        </InputButton>
        <InputButton type="button" variant="gray" @click="emit('close')" flat>
          Cancel
        </InputButton>
      </div>
    </form>
  </GlobalModal>
</template>
