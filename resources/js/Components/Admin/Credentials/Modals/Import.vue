<script setup>
import { ref, reactive } from 'vue'
import GlobalModal from '@/Components/Global/Modal.vue'
import CheckboxInput from '@/Components/Inputs/CheckboxInput.vue'

const emit = defineEmits(['close', 'refresh'])

const form = reactive({
  file: null,
  wipe: false,
})
const errors = ref(null)
const submitting = ref(false)

async function upload() {
  const formData = new FormData()
  formData.append('file', form.file)
  formData.append('wipe', form.wipe ? '1' : '0')
  submitting.value = true

  try {
    const { data } = await window.axios.post('/api/admin/credentials/import', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    emit('refresh')
  } catch (e) {
    errors.value = e.response.data
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <GlobalModal :width="600" @close="emit('close')">
    <template #title>
      <h1>{{ $t('admin.credentials.import.title') }}</h1>
    </template>
    
    <p>{{ $t('admin.credentials.import.instructions') }}</p>

    <p class="my-6 font-bold">
      <a href="/docs/voters.xlsx" download>
        {{ $t('admin.credentials.import.download_sample') }}
      </a>
    </p>

    <hr class="my-6" />

    <form @submit.prevent="upload">
      <div v-if="errors">
        <ul v-for="field in errors.errors" class="text-red font-bold">
          <li v-for="error in field">
            {{ error }}
          </li>
        </ul>
      </div>

      <div class="my-6">
        <label for="file" class="sr-only">
          {{ $t('admin.credentials.import.file') }}
        </label>
        <input
          id="file"
          name="file"
          type="file"
          @input="form.file = $event.target.files[0]"
          required
        />
        <p class="text-sm opacity-75 mt-2">{{ $t('admin.credentials.import.formats') }}</p>
      </div>

      <div class="my-6">
        <CheckboxInput
          name="wipe"
          :label="$t('admin.credentials.import.wipe')"
          v-model="form.wipe"
        />
      </div>

      <InputButton type="submit" flat :loading="submitting">
        {{ $t('admin.credentials.import.action') }}

        <template #loading>
          {{ $t('global.loading') }}
        </template>
      </InputButton>
    </form>
  </GlobalModal>
</template>
