<script setup>
const props = defineProps({
  name: { type: String, required: true },
  id: { type: String, default: null },
  label: { type: String, required: true },
  labelSrOnly: { type: Boolean, default: false },
  required: { type: Boolean, default: false },
  error: { type: [String, Boolean], default: false },
  labelClass: { type: String, default: '' },
  inputClass: { type: String, default: '' }
})

const value = defineModel()
const fieldId = props.id || props.name
</script>

<template>
  <label :class="['flex gap-2 items-center', labelClass]">
    <input
      :id="fieldId"
      :name="name"
      type="checkbox"
      :required="required"
      v-model="value"
      :aria-invalid="!!error ? 'true' : null"
      :aria-describedby="!!error ? `${fieldId}error` : null"
      ref="input"
      :class="['text-green-dark focus:ring-green-pine rounded', inputClass]"
    />
    <span v-if="!labelSrOnly">{{ label }}</span>
  </label>
  <div :id="`${fieldId}error`" v-if="error" class="text-red font-bold">
    {{ error }}
  </div>
</template>