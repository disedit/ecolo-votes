<script setup>
import EGPLogo from '@/Components/Global/EGPLogo.vue'

defineProps({
  active: { type: Boolean, default: false },
  edition: { type: Object, required: true }
})
</script>
<template>
  <div :class="['screen-cover', { active }]">
    <div class="topbar">
      <EGPLogo class="topbar-logo" />
      <h1 class="topbar-title">
        <span>{{ edition.title }}</span>
      </h1>
    </div>
    <h1 class="cover-title">
      <span>{{ edition.title }}</span>
    </h1>
    <h2 class="cover-info">
      <span class="dates">{{ edition.dates }}</span>
      <span class="location">{{ edition.location }}</span>
    </h2>
    <img src="../../../images/illustrations/shamrock.svg" alt="" class="cover-illustration" />
  </div>
</template>

<style lang="scss" scoped>
.screen-cover {
  color: var(--egp-white);
  height: 100vh;
  display: flex;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  flex-direction: column;
  align-items: flex-start;
  background: var(--egp-green);
  transition: 1s ease;
  overflow: clip;
  z-index: 1000;

  .topbar {
    display: flex;
    padding: 2vw;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 10;
    width: 100%;
    align-items: center;
    transition: 1s ease;

    &-logo {
      height: 12vh;
      transition: 1s ease;
    }

    &-title {
      margin-left: auto;
      font-family: var(--font-headline);
      text-transform: uppercase;
      font-size: 4.25vh;
      line-height: 1.1;
      overflow: clip;

      span {
        display: block;
        transition: 1s ease;
        transform: translateY(100%);
      }
    }
  }

  .cover-title {
    position: relative;
    font-size: 20vh;
    font-family: var(--font-headline);
    text-transform: uppercase;
    margin-top: auto;
    z-index: 10;
    line-height: 1;
    text-wrap: balance;
    transition: 1s ease;
    overflow: clip;

    span {
      display: block;
      transition: 1s ease;
      padding: 0 2vw;
    }
  }

  .cover-info {
    margin-bottom: auto;
    display: flex;
    font-family: var(--font-mono);
    text-transform: uppercase;
    font-weight: bold;
    font-size: 4vh;
    overflow: clip;

    span {
      padding: 0 2vw;
      transition: .5s ease;
    }

    .dates {
      transition-delay: 0;
    }

    .location {
      transition-delay: .1s;
    }
  }

  .cover-illustration {
    position: absolute;
    top: 50%;
    right: -20%;
    transform: translate(0, -50%);
    z-index: 1;
    width: 70vw;
    transition: 1s ease;
  }

  &.active {
    height: var(--screen-nav-height);

    .topbar {
      padding: 1vw var(--screen-padding);

      &-logo {
        height: 8vh;
      }

      &-title span {
        transform: translateY(0);
      }
    }

    .cover-title {
      transform: translateY(120%);

      span {
        transform: translateY(-100%);
      }
    }

    .cover-info {
      span {
        transform: translateY(100%);
      }
    }

    .cover-illustration {
      transform: translate(40%, -25%) scale(1) rotate(20deg);
    }
  }
}
</style>