<template>
  <div class="row">
    <div class="col-lg-7 m-auto">
      <card :title="$t('reset_password')">
        <form @submit.prevent="send" @keydown="form.onKeydown($event)">
          <alert-success :form="form" :message="status" />

          <!-- Email -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{ $t('email') }}</label>
            <div class="col-md-7">
              <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" class="form-control" type="email" name="email">
              <has-error :form="form" field="email" />
            </div>
          </div>
          <div class="mb-3 row">
            <div class="col-md-7 offset-md-3 d-flex">
              <vue-recaptcha id="recaptcha" :sitekey="recaptchaKey" :load-recaptcha-script="true"
                             @verify="onVerify"
              />
            </div>
            <has-error :form="form" field="recaptcha" />
          </div>

          <!-- Submit Button -->
          <div class="mb-3 row">
            <div class="col-md-9 ms-md-auto">
              <v-button :loading="form.busy">
                {{ $t('send_password_reset_link') }}
              </v-button>
            </div>
          </div>
        </form>
      </card>
    </div>
  </div>
</template>

<script>
import Form from 'vform'
import VueRecaptcha from 'vue-recaptcha'

export default {

  components: {
    VueRecaptcha
  },
  middleware: 'guest',

  data: () => ({
    status: '',
    form: new Form({
      email: ''
    })
  }),
  computed: {
    recaptchaKey: () => window.config.recaptchaKey
  },
  metaInfo () {
    return { title: this.$t('reset_password') }
  },

  methods: {
    async send () {
      const { data } = await this.form.post('password/email')

      this.status = data.status

      this.form.reset()
    },

    onVerify: function (response) {
      if (response) this.form.recaptcha = response
    }
  }
}
</script>
