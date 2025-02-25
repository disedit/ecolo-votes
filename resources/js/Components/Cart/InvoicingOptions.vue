<script setup>
import { computed, watch, ref, reactive, onMounted, nextTick } from 'vue'
import TextInput from '@/Components/Inputs/TextInput.vue'

const props = defineProps({
    user: { type: Object, required: true },
    default: { type: String, default: 'myself' }
})

const emit = defineEmits(['updated'])

const fullName = computed(() => `${props.user.first_name} ${props.user.last_name}`)

const invoiceTo = ref('myself')
const organisation = reactive({
  type: 'organisation',
  name: '',
  address: '',
  vat: ''
})

watch(invoiceTo, (newInvoiceTo) => {
  if (newInvoiceTo === 'myself') {
    emit('updated', { type: 'myself', name: fullName, address: '', vat: '' })
  } else {
    emit('updated', { ...organisation })
  }
})

watch(organisation, (newOrganisation) => {
  if (invoiceTo.value !== 'myself') {
    emit('updated', { ...newOrganisation })
  }
})

onMounted(() => {
  if (props.default === 'myself') {
    emit('updated', { type: 'myself', name: fullName, address: '', vat: '' })
  } else {
    invoiceTo.value = 'organisation'
    // organisation.name = props.user.group.name
    emit('updated', { ...organisation })
  }
})

const invoicingToOrganisation = computed(() => invoiceTo.value === 'organisation')
const organisationName = ref(null)
function selectOrganisation () {
  invoiceTo.value = 'organisation'
  organisationName.value && organisationName.value.$refs.input.focus()
}
</script>

<template>
  <div class="invoicing-options mt-10">
    <p class="mb-2">Invoice to:</p>

    <div class="grid md:grid-cols-2 gap-4">
      <div class="invoicing-option" :class="{ active: invoiceTo === 'myself' }">
        <label class="invoicing-option-label">
          <input
            type="radio"
            name="invoiceTo"
            value="myself"
            v-model="invoiceTo"
            class="sr-only"
          />
          <div class="flex items-center gap-2">
            <span class="invoicing-option-radio" />
            <span class="invoicing-option-name">
              {{ fullName }}
            </span>
          </div>
        </label>
      </div>
      <div class="invoicing-option" :class="{ active: invoicingToOrganisation }">
        <label class="invoicing-option-label" @click="selectOrganisation">
          <input
            type="radio"
            name="invoiceTo"
            value="organisation"
            v-model="invoiceTo"
            class="sr-only"
          />
          <div class="flex items-center gap-2">
            <span class="invoicing-option-radio" />
            <span class="invoicing-option-name">
              Organisation
            </span>
          </div>
        </label>
        <Transition name="slide">
          <div v-if="invoicingToOrganisation">
            <div class="form">
              <TextInput
                label="Name of organisation"
                name="organisationName"
                v-model="organisation.name"
                :required="invoicingToOrganisation"
                ref="organisationName"
              />
              <TextInput
                label="Address"
                name="organisationAddress"
                v-model="organisation.address"
                :required="invoicingToOrganisation"
              />
              <TextInput
                label="VAT Number"
                name="organisationVAT"
                v-model="organisation.vat"
              />
            </div>
          </div>
        </Transition>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.invoicing-option {
  display: flex;
  background: var(--egp-gray-200);
  flex-direction: column;

  &-label {
    display: flex;
    padding: var(--spacer-4);
    flex-grow: 1;
    align-items: flex-start;
  }

  input:disabled {
    pointer-events: none;
  }

  &-radio {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 1rem;
    height: 1rem;
    background-color: var(--egp-white);
    border-radius: 100%;
  }

  &:focus-within {
    background: var(--egp-pink);
    outline: 2px var(--egp-green-pine) solid;
    outline-offset: 2px;
  }

  &.active {
    outline: 2px var(--egp-green-pine) solid;

    .invoicing-option-radio::before {
      content: '';
      width: .5rem;
      height: .5rem;
      background: var(--egp-green-pine);
      border-radius: 100%;
    }
  }

  &:not(.active) .form {
    opacity: .5;
  }

  .form {
    display: flex;
    flex-direction: column;
    gap: var(--spacer-2);
    flex-grow: 1;
    margin: var(--spacer-4);
    margin-block-start: 0;
    font-size: var(--text-sm);
  }
}

@include media('<md') {
  .invoicing-option {
    &-label {
      padding: var(--spacer-3);
    }

    .form {
      margin: var(--spacer-3);
      margin-block-start: 0;
    }
  }
}
</style>