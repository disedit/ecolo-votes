<script setup>
import { ref, onMounted } from 'vue'
import GlobalModal from '@/Components/Global/Modal.vue'
import SelectInput from '@/Components/Inputs/SelectInput.vue'
const emit = defineEmits(['close', 'clone'])

const votes = ref(null)
const loading = ref(false)
onMounted(async () => {
  loading.value = true
  const { data } = await window.axios.get('/api/admin/votes_to_import')
  votes.value = data.votes
  loading.value = false
})

const selectedVote = ref(null)
async function submit () {
  emit('clone', selectedVote.value.options)
}
</script>

<template>
  <GlobalModal :width="650" @close="emit('close')">
    <template #title>
      <h1>{{ $t('admin.votes.importer.title') }}</h1>
    </template>
    <form @submit.prevent="submit" class="flex flex-col gap-4 mt-6">
      <div v-if="loading">
        {{ $t('global.loading') }}
      </div>
      <div v-else-if="votes">
        <SelectInput
          name="votesToClone"
          :label="$t('admin.votes.importer.fields.select_vote')"
          required
          v-model="selectedVote"
          :options="votes.map((vote) => ({ value: vote, label: vote.name + ' ' + (vote.subtitle || '') }))"
        />
        <div v-if="selectedVote">
          <ul class="mt-4 list-disc ms-4">
            <template v-for="option in selectedVote.options" :key="option.id"> 
              <li v-if="!option.is_abstain && !option.is_no">
                {{ option.name }}
              </li>
            </template>
          </ul>
        </div>
        <div class="flex mt-6 justify-between">
          <InputButton type="submit" variant="purple" flat>
            {{ $t('admin.votes.importer.button') }}
          </InputButton>
          <InputButton type="button" variant="gray" @click="emit('close')" flat>
            {{ $t('global.cancel') }}
          </InputButton>
        </div>
      </div>
    </form>
  </GlobalModal>
</template>
