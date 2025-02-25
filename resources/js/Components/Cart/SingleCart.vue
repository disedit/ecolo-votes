<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import FeeSelect from '@/Components/Inputs/FeeSelect.vue'
import TicketInfo from '@/Components/Cart/TicketInfo.vue'

const props = defineProps({
    user: { type: Object, required: true },
    attendee: { type: Object, required: true },
    fees: { type: Object, required: true }
})

const emit = defineEmits(['updated'])

const selectedFee = ref(null)

const feeList = computed(() => Object.values(props.fees))

watch(selectedFee, (newFee) => {
  emit('updated', [{
    id: newFee.id,
    attendee_id: props.attendee.id,
    name: newFee.name,
    description: `${props.user.first_name} ${props.user.last_name}`,
    amount: newFee.amount
  }])
})

onMounted(() => {
  selectedFee.value = feeList.value[0]
})
</script>

<template>
  <div>
    <div class="text-base mb-6">
        You have registered for the <strong>{{ $page.props.edition.title }}</strong> in <strong>{{ $page.props.edition.location }}</strong>.
        To confirm your attendance, please purchase a ticket a below.
    </div>
    <TicketInfo
      :name="`${user.first_name} ${user.last_name}`"
      :email="user.email"
      :organisation="user.group.name"
      number="#1"
      :role="attendee.type.name"
    />
    <FeeSelect
      :fees="feeList"
      v-model="selectedFee"
    />
  </div>
</template>