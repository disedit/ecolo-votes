<script setup>
import { onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'
import { useModal } from 'vue-final-modal'
import { optionClasses } from '@/Composables/useColors.js'
import GrayLayout from '@/Layouts/GrayLayout.vue'
import GlobalMenu from '@/Components/Global/Menu.vue'
import ResultsModal from '@/Components/Votes/Modals/Results.vue'

defineOptions({
    layout: GrayLayout
})

const props = defineProps({
    auth: { type: Object, required: true },
    flash: { type: Object, default: () => null },
    votes: { type: Array, required: true },
    code: { type: Object, required: true }
})

onMounted(() => {
  window.Echo.private('Votes').listenForWhisper('refreshVotes', () => refresh())
})

function refresh() {
  router.reload({ only: ['votes'] })
}

const { open, close, patchOptions } = useModal({
  component: ResultsModal,
  attrs: { onClose () { close() } }
})

function showVote (vote) {
  patchOptions({ attrs: { vote } })
  open()
}
</script>

<template>
    <Head title="Vote record" />
    <div class="container padded">
      <h1 class="font-headline text-xl mb-12">
        {{ $t('results.page.title') }}
      </h1>
      <div class="votes-header uppercase">
        <div>
          {{ $t('results.page.columns.vote') }}
        </div>
        <div>
          {{ $t('results.page.columns.result') }}
        </div>
        <div />
      </div>
      <ul class="votes mb-6">
        <li v-for="vote in votes" :key="vote.id">
          <button @click="showVote(vote)" :class="['vote', `vote-${vote.type}`, { secret: vote.secret }]">
            <div class="vote-name">
              <span class="font-bold">{{ vote.name }}</span>
              <span v-if="vote.subtitle" class="text-gray-600 ms-2">{{ vote.subtitle }}</span>
            </div>
            <div class="vote-result">
              <div v-if="vote.winner" :class="['option winner', optionClasses(vote.winner, vote)]">
                <Icon icon="ri:check-fill" class="shrink-0" />
                <span>{{ vote.winner.name }}</span>
              </div>
              <div v-else-if="vote.closed_at" class="option no-winner">
                --
              </div>
              <div v-else-if="vote.open" class="option ongoing">
                {{ $t('voter.status.ongoing_short') }}
              </div>
              <div v-else class="option upcoming">
                {{ $t('voter.status.upcoming_short') }}
              </div>
            </div>
            <div class="vote-more">
              +
              <span class="sr-only">{{ $t('results.page.more') }}</span>
            </div>
          </button>
        </li>
      </ul>
      <GlobalMenu :show="['vote']" />
    </div>
</template>

<style lang="scss" scoped>
.container {
  --container-max-width: 1000px;
}

.votes {
  display: grid;
  grid-template-columns: 1fr;
  gap: var(--spacer-2);
}

.vote,
.votes-header {
  display: grid;
  grid-template-columns: auto 175px 40px;
  gap: var(--spacer-4);
  align-items: center;
}

.votes-header {
  font-family: var(--font-mono);
  text-transform: uppercase;
  font-size: 1rem;
  color: var(--egp-gray-600);
  padding: var(--spacer-2) var(--spacer-4);
}

.vote {
  width: 100%;
  background-color: var(--egp-white);
  padding: var(--spacer-3);
  transition: .25s ease;
  border: 2px var(--egp-white) solid;
  border-radius: .5rem;
  --focus-color: var(--egp-green-pine);

  &-name {
    text-align: left;
  }

  &-more {
    font-size: var(--text-md);
    border-radius: .25rem;
  }

  @media (hover: hover) {
    &:hover {
      box-shadow: .5rem .5rem 0 0 var(--egp-green-pine);
      translate: -.5rem -.5rem;
      border-color: var(--egp-green-pine);

      .vote-more {
        background: var(--egp-yellow);
      }
    }
  }
}

.option {
  display: flex;
  gap: var(--spacer-2);
  background: var(--egp-gray-100);
  padding: var(--spacer-2);
  align-items: flex-start;
  line-height: 1.1;
  border-radius: .25rem;

  &-circle {
    width: 1.25em;
    height: 1.25em;
    background: var(--color, var(--egp-blue));
    border-radius: 100%;
    flex-shrink: 0;
    margin-block-start: -.1em;
    margin-block-end: -.25em;
  }
}

.vote.secret .vote-ballot .option:not(.not-voted) {
  --color: var(--egp-blue);
}

.vote-result .option.winner {
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  background-color: var(--color, var(--egp-blue));
  color: var(--egp-white);

  span {
    flex-grow: 1;
    text-align: center;
  }

  :deep(path) {
    stroke: var(--egp-white);
    stroke-width: 1.5px;
  }
}

.vote-ballot .option:not(.not-voted) {
  font-weight: bold;
}

.ongoing {
  color: var(--egp-green-dark);
  font-weight: bold;
}

.upcoming,
.not-voted,
.no-winner {
  --color: var(--egp-gray-400);
  color: var(--egp-gray-700);
}

@include media('<=md') {
  .votes-header {
    display: none;
  }

  .vote {
    grid-template-columns: 1fr 2rem;
    gap: var(--spacer-2);

    &-name {
      grid-column: span 2;
      padding-block-end: var(--spacer-4);
    }

    &-ballot {
      grid-column: span 2;

      &::before {
        content: 'My vote';
      }
    }

    &-result {
      &::before {
        content: 'Result';
      }
    }

    &-ballot,
    &-result {
      display: grid;
      grid-template-columns: 5rem 1fr;
      align-items: center;
      gap: var(--spacer-2);

      &::before {
        font-family: var(--font-mono);
        text-transform: uppercase;
        font-size: var(--text-sm);
        text-align: left;
        color: var(--egp-gray-700);
      }
    }

    &-more {
      background-color: var(--egp-gray-100);
      padding-inline: .5em;
      height: 100%;
      display: grid;
      place-items: center;
    }
  }
}
</style>