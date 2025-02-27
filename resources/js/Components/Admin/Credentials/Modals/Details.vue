<script setup>
import { ref, onMounted } from 'vue'
import GlobalModal from '@/Components/Global/Modal.vue'

const props = defineProps({
  attendeeId: { type: Number, required: true }
})

const emit = defineEmits(['close', 'refresh'])

const attendee = ref(null)

onMounted(async () => {
  const { data } = await window.axios.get('/api/credential/' + props.attendeeId)
  attendee.value = data
})
</script>

<template>
  <GlobalModal v-if="attendee" :width="1000" @close="emit('close')">
    <template #title>
      <h1>{{ attendee.first_name }} {{ attendee.last_name }} / Details</h1>
    </template>
    <table class="table w-full mt-6">
      <colgroup>
        <col width="300" />
        <col />
      </colgroup>
      <thead>
        <tr>
          <th>Field</th>
          <th>Value</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="detail in attendee.details" :key="detail.field_key">
          <th>{{ detail.field_label }}</th>
          <td>{{ detail.field_value }}</td>
        </tr>
      </tbody>
    </table>
  </GlobalModal>
</template>
