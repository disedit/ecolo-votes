<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import FeeSelect from '@/Components/Inputs/FeeSelect.vue'
import TicketInfo from '@/Components/Cart/TicketInfo.vue'
import FailedLookUp from '@/Components/Cart/FailedLookUp.vue'

const props = defineProps({
    user: { type: Object, required: true },
    attendee: { type: Object, required: true },
    fees: { type: Object, required: true },
    subdelegates: { type: Array, required: true },
    unassignedDelegates: { type: Array, default: null }
})

const emit = defineEmits(['updated'])

const delegates = ref([])
const feeList = computed(() => Object.values(props.fees))

const cart = computed(() => {
  return delegates.value.map(delegate => ({
    id: delegate.fee?.id,
    attendee_id: delegate.attendee_id,
    name: delegate.fee?.name,
    description: `${delegate.first_name} ${delegate.last_name}`,
    amount: delegate.fee?.amount
  }))
})

watch(cart, (newCart) => {
  emit('updated', newCart)
})

watch(() => props.subdelegates, () => {
  setDelegates()
})

onMounted(() => {
  setDelegates()
  emit('updated', cart.value)
})

function setDelegates() {
  delegates.value = [
    {
      id: 'myself',
      first_name: props.user.first_name,
      last_name: props.user.last_name,
      email: props.user.email,
      attendee_id: props.attendee.id,
      fee: feeList.value[0]
    },
    ...props.subdelegates.map(subdelegate => ({
      ...subdelegate,
      fee: subdelegate.attendee_id ? subdelegate.fees[0] : null
    }))
  ]
}

function linkDelegate(delegate, id) {
  router.post('/cart/link-delegate', { delegate: delegate, row_id: id }, { preserveScroll: true })
}

function removeDelegate(id) {
  router.post('/cart/remove-delegate', { row_id: id }, { preserveScroll: true })
}
</script>

<template>
  <div>
    <div class="text-base mb-6">
      Buying tickets in bulk for <strong>{{ user.group.name }}</strong> delegates.
    </div>
    <div v-for="(delegate, i) in delegates" :key="delegate.id">
      <TicketInfo
        :id="delegate.id"
        :name="`${delegate.first_name} ${delegate.last_name}`"
        :email="delegate.email"
        :number="delegate.email === user.email ? 'Yourself' : `#${i + 1}`"
        :error="!delegate.attendee_id"
        abridged
        :removable="delegate.id !== 'myself'"
        @remove="removeDelegate"
      />
      <template v-if="delegate.attendee_id">
        <FeeSelect
          :name="`fee[${i}]`"
          :fees="delegate.fees || feeList"
          v-model="delegate.fee"
          abridged
        />
      </template>
      <FailedLookUp
        v-else
        :id="delegate.id"
        :unassigned-delegates="unassignedDelegates"
        @link="linkDelegate"
        @remove="removeDelegate"
      />
    </div>
  </div>
</template>