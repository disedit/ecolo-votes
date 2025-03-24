<script setup>
import { ref, reactive, onMounted } from 'vue'
import GlobalModal from '@/Components/Global/Modal.vue'
import TextInput from '@/Components/Inputs/TextInput.vue'
import TextareaInput from '@/Components/Inputs/TextareaInput.vue'
import CheckboxInput from '@/Components/Inputs/CheckboxInput.vue'

const emit = defineEmits(['close', 'refresh'])

const form = reactive({
  mail_notification_subject: '',
  mail_notification_body: '',
  sms: false,
  sms_notification: '',
  only_unnotified: true
})

onMounted(async () => {
  const { data } = await window.axios.get('/api/admin/credentials/notification')
  form.mail_notification_subject = data.mail_notification_subject
  form.mail_notification_body = data.mail_notification_body
  form.sms_notification = data.sms_notification
})

const saving = ref(false)
const sending = ref(false)
const saved = ref(false)
const errors = ref(null)
const sent = ref(false)

async function save() {
  saving.value = true
  try {
    const { data } = await window.axios.post('/api/admin/credentials/notify/?save', form)
    saved.value = true
    setTimeout(() => saved.value = false, 3000)
  } catch (e) {
    errors.value = e.response.data
  } finally {
    saving.value = false
  }
}

async function send() {
  const confirmMessage = form.only_unnotified ? 'Notifications will be sent. Wish to continue?' : 'All participants will be notified. Wish to continue?'
  const confirmed = confirm(confirmMessage)
  if (!confirmed) return

  sending.value = true
  try {
    const { data } = await window.axios.post('/api/admin/credentials/notify/?send=true', form)
    sent.value = true
  } catch (e) {
    errors.value = e.response.data
  } finally {
    sending.value = false
  }
}
</script>

<template>
  <GlobalModal :width="600" @close="emit('close')">
    <template #title>
      <h1>{{ $t('admin.credentials.notify.title') }}</h1>
    </template>

    <div v-if="sent">
      <p class="text-green mb-6 font-bold">{{ $t('admin.credentials.notify.sent') }}</p>

      <InputButton type="button" @click="emit('close')" variant="gray" flat>
        {{ $t('admin.credentials.notify.close') }}
      </InputButton>
    </div>
    <form v-else @submit.prevent="save">
      <TextInput
        type="text"
        name="mail_notifiation_subject"
        :label="$t('admin.credentials.notify.mail_notification_subject')"
        required
        v-model="form.mail_notification_subject"
        help="Placeholers: [firstName] [lastName]"
        class="mb-4"
      />
      
      <TextareaInput
        type="textarea"
        name="mail_notifiation_body"
        :label="$t('admin.credentials.notify.mail_notification_body')"
        required
        v-model="form.mail_notification_body"
        help="Placeholers: [firstName] [lastName]. Accepts Markdown."
      />

      <div class="my-6">
        <CheckboxInput
          name="sms"
          :label="$t('admin.credentials.notify.sms')"
          v-model="form.sms"
        />
      </div>

      <TextareaInput
        v-if="form.sms"
        type="textarea"
        name="sms_notifiation"
        :label="$t('admin.credentials.notify.sms_notification')"
        required
        v-model="form.sms_notification"
        :maxlength="100"
        :help="`Placeholers: [firstName] [lastName]. Characters: ${form.sms_notification.length}/100`"
      />

      <div class="my-6">
        <CheckboxInput
          name="only_unnotified"
          :label="$t('admin.credentials.notify.only_unnotified')"
          v-model="form.only_unnotified"
        />
      </div>

      <div class="flex items-center gap-2 mt-6">  
        <InputButton type="submit" icon="ri:save-3-fill" variant="gray" flat :loading="saving">
          {{ $t('admin.credentials.notify.save') }}

          <template #loading>
            {{ $t('global.loading') }}
          </template>
        </InputButton>
        <div v-if="saved" class="text-green font-bold">
          {{ $t('admin.credentials.notify.saved') }}
        </div>
        <InputButton type="button" @click="send" icon="ri:send-plane-fill" flat :loading="sending" class="ms-auto">
          {{ $t('admin.credentials.notify.send') }}

          <template #loading>
            {{ $t('global.loading') }}
          </template>
        </InputButton>
      </div>
    </form>
  </GlobalModal>
</template>