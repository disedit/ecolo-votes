<script setup>
const props = defineProps({
  options: { type: Array, required: true },
  name: { type: String, required: true },
  id: { type: String, default: null },
  label: { type: String, required: true },
  type: { type: String, default: 'text' },
  placeholder: { type: String, default: '' },
  required: { type: Boolean, default: false },
  variant: { type: String, default: 'default' },
  size: { type: String, default: 'default' },
  labelSrOnly: { type: Boolean, default: false },
  autofocus: { type: Boolean, default: false },
  error: { type: [String, Boolean], default: false },
  disabled: { type: Boolean, default: false },
})

const value = defineModel()
const fieldId = props.id || props.name
</script>

<template>
  <div :class="['flex flex-col gap-1', `input-${variant}`, `input-${size}`, { error }]">
    <label :for="id || name" :class="['font-mono uppercase text-gray-900 flex items-center gap-2', { 'sr-only': labelSrOnly }]">
      {{ label }}
      <span v-if="required" class="ms-auto text-gray-600">
        {{ $t('inputs.required') }}
      </span>
    </label>
    <select
      :id="fieldId"
      :name="name"
      :required="required"
      :placeholder="placeholder"
      :aria-invalid="!!error ? 'true' : null"
      :aria-describedby="!!error ? `${fieldId}error` : null"
      :autofocus="autofocus"
      :disabled="disabled"
      v-model="value"
      class="input-control focus:ring-green-pine focus:border-green-pine disabled:bg-gray-100"
      ref="input"
    >
      <option v-for="option in options" :key="option.value" :value="option.value">
        {{ option.label }}
      </option>
    </select>
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
  @apply text-base;
}

.error .input-control {
  @apply border-red;
}
</style>