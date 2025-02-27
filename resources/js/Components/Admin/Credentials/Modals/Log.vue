<script setup>
import { ref, onMounted } from 'vue'
import { formatDateTime } from '@/Composables/useDate.js'
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
  <GlobalModal v-if="attendee" :width="600" @close="emit('close')">
    <template #title>
      <h1>{{ attendee.first_name }} {{ attendee.last_name }} / Access Log</h1>
    </template>
    <table class="table w-full mt-6">
      <colgroup>
        <col width="200" />
        <col />
      </colgroup>
      <thead>
        <tr>
          <th>Time</th>
          <th>Type</th>
          <th>Client</th>
          <th>Logged by</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="event in attendee.access_log" :key="event.id">
          <th>{{ formatDateTime(event.created_at) }}</th>
          <td>
            <span :class="['px-3 text-white font-bold block text-center', { 'bg-green': event.type === 'IN', 'bg-red': event.type === 'OUT' }]">
              {{ event.type }}
            </span>
          </td>
          <td>{{ event.client }}</td>
          <td>{{ event.logged_by_user_id }}</td>
        </tr>
      </tbody>
    </table>
  </GlobalModal>
</template>
