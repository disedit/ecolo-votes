<template>
  <footer class="footer flex flex-col xl:flex-row p-4 text-xs md:text-sm xl:items-end gap-8 leading-tight">
    <div class="flex flex-col lg:flex-row xl:ms-auto gap-1 lg:gap-2">
      <strong class="shrink-0">
        &copy; Ecolo
      </strong>
      <a href="https://ecolo.be/mentions-legales-et-protection-de-la-vie-privee/" class="underline hover:text-green-pine shrink-0">
        {{ $t('footer.privacy_policy') }}
      </a>
    </div>
  </footer>
</template>