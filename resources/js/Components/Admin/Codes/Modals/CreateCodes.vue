<script setup>
import { reactive, ref, watch, onMounted } from 'vue'
import GlobalModal from '@/Components/Global/Modal.vue'
import TextInput from '@/Components/Inputs/TextInput.vue'

const emit = defineEmits(['close', 'refresh'])

const form = reactive({
  amount: 300,
})

const errors = ref(null)
const submitting = ref(false)
async function createCodes () {
  submitting.value = true
  try {
    const { data } = await window.axios.post('/api/admin/codes/create', form)
    emit('refresh')
  } catch (e) {
    errors.value = e.response.data
  } finally {
    submitting.value = false
  }
}

const numberInput = ref(null)
function focusNumberInput () {
  numberInput.value.$refs.input.focus()
}
</script>

<template>
  <GlobalModal :width="700" @close="emit('close')" @opened="focusNumberInput">
    <template #title>
      <h1>{{ $t('admin.codes.create.title') }}</h1>
    </template>
    <form @submit.prevent="createCodes" class="flex flex-col gap-4 text-rbase">
      <TextInput
        type="number"
        name="name"
        :label="$t('admin.codes.create.amount')"
        required
        v-model="form.amount"
        input-class="text-lg"
        ref="numberInput"
      />
      <ul v-if="errors" class="text-red font-bold mb-4">
        <li v-for="error in errors.errors">{{ error[0] }}</li>
      </ul>
      <InputButton type="submit" flat :disabled="submitting">
        {{ submitting ? $t('admin.codes.create.generating') : $t('admin.codes.create.generate') }}
      </InputButton>
    </form>
  </GlobalModal>
</template>
