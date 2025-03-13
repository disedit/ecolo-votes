<script setup>
import { nextTick, ref } from 'vue'
import { useModal } from 'vue-final-modal'
import TextInput from '@/Components/Inputs/TextInput.vue'
import SelectInput from '@/Components/Inputs/SelectInput.vue'
import CheckboxInput from '@/Components/Inputs/CheckboxInput.vue'
import CloneVote from '@/Components/Admin/Votes/Modals/CloneVote.vue'

const props = defineProps({
  regions: { type: Array, required: true }
})

const options = defineModel()
const optionRefs = ref(null)
const optionDescRefs = ref(null)
const no = defineModel('no')
const abstain = defineModel('abstain')

function addOption() {
  options.value.push({ name: '', description: '', gender: null, region: null, enabled: true })
  nextTick(() => {
    optionRefs.value[optionRefs.value.length - 1].$refs.input.focus()
  })
}

function removeOption(i) {
  options.value.splice(i, 1)
}

function moveUp(i) {
  options.value.splice(i - 1, 0, options.value.splice(i, 1)[0])
}

function moveDown(i) {
  options.value.splice(i + 1, 0, options.value.splice(i, 1)[0])
}

function handleKeydown(e, i, field) {
  const ref = field === 'description' ? optionDescRefs.value : optionRefs.value
  if (e.key === 'Enter') {
    e.preventDefault()
    addOption()
  } else if (e.key === 'Backspace' && options.value[i].name === '') {
    if (options.value.length > 1) removeOption(i)
    if (i > 0) ref[i - 1].$refs.input.focus()
  } else if (e.key === 'ArrowDown' && i < ref.length - 1) {
    ref[i + 1].$refs.input.focus()
  } else if (e.key === 'ArrowUp' && i > 0) {
    ref[i - 1].$refs.input.focus()
  }
}

const { open: openOptionsModal, close } = useModal({
  component: CloneVote,
  attrs: {
    onClose () {
      close()
    },
    onClone (newOptions) {
      options.value = newOptions.filter(option => !option.is_abstain && !option.is_no)
        .map(option => ({ name: option.name, description: option.description, gender: option.gender, region: option.region, enabled: true }))
      close()
    }
  }
})

const regionDropdown = props.regions.map(region => ({ value: region.id, label: region.name }))
</script>

<template>
  <div>
    <label for="voteOptions" class="font-mono uppercase text-gray-900 flex gap-2 items-center">
      {{ $t('admin.votes.options.label') }}
      <span class="ms-auto text-gray-600">
        {{ $t('inputs.required') }}
      </span>
    </label>
    <div class="flex flex-col gap-2 my-2">
      <div v-for="(option, i) in options" :key="i" class="flex gap-2">
        <CheckboxInput
          :name="`voteOptionDisabled[${i}]`"
          :label="`Vote option ${i + 1} is enabled`"
          label-sr-only
          v-model="option.enabled"
          label-class="px-4 bg-gray-200"
        />
        <TextInput
          :name="`voteOptionName[${i}]`"
          :label="`Vote option ${i + 1}`"
          label-sr-only
          v-model="option.name"
          :disabled="!option.enabled"
          class="grow"
          ref="optionRefs"
          @keydown="handleKeydown($event, i, 'name')"
          placeholder="Name"
        />
        <TextInput
          :name="`voteOptionDescription[${i}]`"
          :label="`Vote description ${i + 1}`"
          label-sr-only
          v-model="option.description"
          :disabled="!option.enabled"
          class="grow"
          ref="optionDescRefs"
          @keydown="handleKeydown($event, i, 'description')"
          placeholder="Description"
        />
        <SelectInput
          :name="`voteOptionRegion[${i}]`"
          :label="`Vote option ${i + 1} Region`"
          label-sr-only
          v-model="option.region"
          :options="regionDropdown"
          :disabled="!option.enabled"
        />
        <SelectInput
          :name="`voteOptionGender[${i}]`"
          :label="`Vote option ${i + 1} Gender`"
          label-sr-only
          v-model="option.gender"
          :options="[
            { value: null, label: $t('genders.N') },
            { value: 'F', label: $t('genders.F') },
            { value: 'M', label: $t('genders.M') },
            { value: 'O', label: $t('genders.O') }
          ]"
          :disabled="!option.enabled"
        />
        <InputButton :disabled="i === 0" type="button" icon="ri:arrow-up-line" variant="gray" flat @click="moveUp(i)" title="Move up">
          <span class="sr-only">
            {{ $t('admin.votes.actions.move_up') }}
          </span>
        </InputButton>
        <InputButton :disabled="i === options.length - 1" type="button" icon="ri:arrow-down-line" variant="gray" flat @click="moveDown(i)" title="Move down">
          <span class="sr-only">
            {{ $t('admin.votes.actions.move_down') }}
          </span>
        </InputButton>
        <InputButton :disabled="options.length === 1" type="button" icon="ri:delete-bin-2-line" variant="soft-red" flat @click="removeOption(i)" title="Remove">
          <span class="sr-only">
            {{ $t('admin.votes.actions.remove') }}
          </span>
        </InputButton>
      </div>
      <div class="flex gap-2">
        <CheckboxInput
          :name="`voteOptionDisabled[No]`"
          label="No is enabled"
          label-sr-only
          v-model="no"
          label-class="px-4 bg-gray-200"
        />
        <div class="border border-gray-500 p-2 bg-gray-100 grow">
          {{ $t('options.no') }}
        </div>
      </div>
      <div class="flex gap-2">
        <CheckboxInput
          :name="`voteOptionDisabled[Abstain]`"
          label="Abstain is enabled"
          label-sr-only
          v-model="abstain"
          label-class="px-4 bg-gray-200"
        />
        <div class="border border-gray-500 p-2 bg-gray-100 grow">
          {{ $t('options.abstain') }}
        </div>
      </div>
    </div>
    <div class="flex gap-4 justify-between">
      <InputButton type="button" size="sm" variant="gray" flat icon="ri:add-fill" @click="addOption">
        {{ $t('admin.votes.actions.add_option') }}
      </InputButton>
      <InputButton @click="openOptionsModal" type="button" size="sm" variant="gray" flat icon="ri:folder-upload-line">
        {{ $t('admin.votes.actions.load_options') }}
      </InputButton>
    </div>
  </div>
</template>