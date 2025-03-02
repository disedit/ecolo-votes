<script setup>
const props = defineProps({
  name: { type: String, required: true },
  id: { type: String, default: null },
  label: { type: String, required: true },
  type: { type: String, default: 'text' },
  placeholder: { type: String, default: '' },
  pattern: { type: String, default: null },
  required: { type: Boolean, default: false },
  variant: { type: String, default: 'default' },
  size: { type: String, default: 'default' },
  labelSrOnly: { type: Boolean, default: false },
  autofocus: { type: Boolean, default: false },
  autocomplete: { type: [String, Boolean], default: false },
  error: { type: [String, Boolean], default: false },
  inputClass: { type: String, default: '' },
  disabled: { type: Boolean, default: false },
  maxlength: { type: Number, default: null }
})

const value = defineModel()
const fieldId = props.id || props.name
</script>

<template>
  <div :class="['flex flex-col gap-1', `input-${variant}`, `input-${size}`, { error }]">
    <label :for="id || name" :class="['font-mono uppercase text-gray-900 flex gap-2 items-center', { 'sr-only': labelSrOnly }]">
      {{ label }}
      <span v-if="required" class="ms-auto text-gray-600">
        {{ $t('inputs.required') }}
      </span>
    </label>
    <input
      :type="type"
      :id="fieldId"
      :name="name"
      :required="required"
      :placeholder="placeholder"
      :aria-invalid="!!error ? 'true' : null"
      :aria-describedby="!!error ? `${fieldId}error` : null"
      :autofocus="autofocus"
      :disabled="disabled"
      :maxlength="maxlength"
      :autocomplete="autocomplete"
      :pattern="pattern"
      v-model="value"
      :class="['input-control focus:ring-green-pine focus:border-green-pine disabled:bg-gray-100', inputClass]"
      ref="input"
    />
    <div :id="`${fieldId}error`" v-if="error" class="text-red font-bold">
      {{ error }}
    </div>
  </div>
</template>

<style lang="scss" scoped>
.input-gray .input-control {
  @apply bg-gray-100 border-gray-300 border-2 focus:border-green-pine;
}

.input-lg .input-control {
  @apply text-md;
}

.input-xl .input-control {
  @apply text-lg;
}

.error .input-control {
  @apply border-red;
}
</style>