<script setup>
import { Icon } from '@iconify/vue'
import RemoveButton from './RemoveButton.vue'

defineProps({
  id: { type: Number, default: null },
  name: { type: String, required: true },
  email: { type: String, required: true },
  organisation: { type: String },
  number: { type: String },
  role: { type: String },
  abridged: { type: Boolean, default: false },
  removable: { type: Boolean, default: false },
  error: { type: Boolean, default: false }
})

const emit = defineEmits(['remove'])
</script>

<template>
  <article :class="['ticket bg-gray-200 p-4', { abridged, error }]">
    <div class="ticket-icon">
      <Icon icon="ion:ticket-outline" />
    </div>
    <div class="ticket-info">
      <table class="-my-2">
        <tbody>
          <tr>
            <th>Name</th>
            <td><span class="truncate">{{ name }}</span></td>
          </tr>
          <tr>
            <th>Email</th>
            <td>
              <span :class="['truncate', { 'text-red font-bold flex gap-2 items-center': error }]">
                <Icon icon="ri:error-warning-line" v-if="error" />
                {{ email }}
              </span>
            </td>
          </tr>
          <tr v-if="organisation && !abridged">
            <th>Organisation</th>
            <td><span class="truncate">{{ organisation }}</span></td>
          </tr>
          <tr v-if="role && !abridged">
            <th>Role</th>
            <td><span class="truncate">{{ role }}</span></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div :class="['ticket-number flex gap-2', { 'items-center': abridged }]">
      <RemoveButton v-if="removable" type="button" @click="emit('remove', id)" />
      {{ number }}
    </div>
  </article>
</template>

<style lang="scss" scoped>
.ticket {
  display: grid;
  grid-template-columns: auto 1fr auto;
  color: var(--egp-green-pine);
  gap: var(--spacer-1);
  position: relative;

  &-icon {
    font-size: 1.5rem;
    translate: -.25rem -.25rem;
  }

  &-number {
    font-family: var(--font-mono);
    font-size: var(--text-base);
    translate: 0 -.4rem;
  }

  &-info {
    th {
      font-family: var(--font-mono);
      font-weight: 400;
      text-transform: uppercase;
      font-size: var(--text-sm);
      text-align: left;
      padding: var(--spacer-1);
    }

    td {
      padding: var(--spacer-1) var(--spacer-4);
      font-size: 1rem;
    }
  }

  &::before,
  &::after {
    content: '';
    display: block;
    position: absolute;
    top: 50%;
    left: 0;
    background-color: var(--egp-white);
    height: 1.1rem;
    width: 1.1rem;
    border-radius: 100%;
    translate: -50% -50%;
  }

  &::after {
    left: auto;
    right: 0;
    translate: 50% -50%;
  }

  &.abridged {
    .ticket-icon {
      translate: 0 .75rem;
    }

    .ticket-number {
      translate: 0 0;
    }
  }

  &.error {
    border: 2px var(--egp-red) solid;
    border-bottom: 0;
    margin-top: var(--spacer-4);

    &::before,
    &::after {
      display: none;
    }
  }
}

@include media('<md') {
  .ticket  {
    &-info {
      tr {
        display: flex;
        flex-direction: column;
      }

      td, th {
        padding: 0;
        font-size: var(--text-sm);
      }

      tr:not(:first-child) th {
        margin-block-start: var(--spacer-2);
      }
    }

    .truncate {
      max-width: 55vw;
    }
  }
}
</style>