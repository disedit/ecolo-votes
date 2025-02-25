<script setup>
import { formatCurrency } from '@/Composables/useCurrency.js'

defineProps({
  name: { type: String, default: 'fee' },
  fees: { type: Array, required: true },
  quantity: { type: Number, default: 1 },
  abridged: { type: Boolean, default: false }
})

const selectedFee = defineModel()
</script>

<template>
  <div :class="[
    'fee-select font-mono text-base relative z-10 flex flex-col gap-[1px] bg-gray-300 border-t border-b border-gray-300',
    { 'single-choice': fees.length === 1 }
  ]">
    <label v-for="fee in fees" :key="fee.id" :class="['fee', { 'p-4': !abridged, 'py-2 px-4 text-rbase': abridged }]">
      <input
        type="radio"
        :name="name"
        :value="fee"
        v-model="selectedFee"
        class="fee-input absolute sr-only"
      />
      <div class="fee-radio" />
      <div class="fee-name uppercase">
        {{ fee.name }}
      </div>
      <div class="fee-quantity">
        <span v-if="quantity > 1">&times; {{ quantity }}</span>
      </div>
      <div class="fee-price">
        {{ formatCurrency(fee.amount) }}
      </div>
    </label>
  </div>
</template>

<style lang="scss" scoped>
.fee {
  display: grid;
  grid-template-columns: auto 1fr auto auto;
  align-items: center;
  gap: var(--spacer-4);
  background-color: var(--egp-white);

  &-radio {
    display: block;
    width: 1rem;
    height: 1rem;
    background-color: var(--egp-gray-300);
    border-radius: 100%;
  }

  &:hover {
    background-color: var(--egp-gray-50);
    cursor: pointer;
  }

  &:has(input:checked) {
    background: var(--egp-yellow);

    .fee-radio {
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: var(--egp-white);

      &::before {
        content: '';
        width: .5rem;
        height: .5rem;
        background: var(--egp-green-pine);
        border-radius: 100%;
      }
    }
  }

  &:focus-within {
    outline: 2px var(--egp-green-pine) solid;
    outline-offset: 2px;
    position: relative;
    z-index: 10;
  }
}

.single-choice .fee:has(input:checked) {
  background: var(--egp-white);
  grid-template-columns: auto 1fr auto;

  .fee-radio {
    display: none;
  }
}
</style>