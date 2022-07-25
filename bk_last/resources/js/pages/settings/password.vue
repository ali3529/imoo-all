<template>
  <div>
    <h4 class="mb-3">
      {{ $t('your_password') }}
    </h4>
    <form class="needs-validation" novalidate="" @submit.prevent="update" @keydown="form.onKeydown($event)">
      <alert-success :form="form" :message="$t('password_updated')" />
      <div class="row gy-3">
        <!-- Password -->
        <div class="col-12">
          <label for="password" class="form-label">{{ $t('new_password') }}</label>

          <input id="password" v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }" class="form-control" type="password" name="password" spellcheck="false"
                 data-ms-editor="true"
          >
          <has-error :form="form" field="password" />
        </div>

        <!-- Password Confirmation -->
        <div class="col-12">
          <label for="rePassword" class="form-label">{{ $t('confirm_password') }}</label>

          <input id="rePassword" v-model="form.password_confirmation" :class="{ 'is-invalid': form.errors.has('password_confirmation') }" class="form-control" type="password" name="password_confirmation" spellcheck="false"
                 data-ms-editor="true"
          >
          <has-error :form="form" field="password_confirmation" />
        </div>
      </div>

      <hr class="my-4">

      <!-- Submit Button -->
      <div class="mb-3 row">
        <div class="col-md-12 ms-md-auto">
          <v-button :loading="form.busy" type="success" class="w-100 btn btn-primary btn-lg">
            {{ $t('update') }}
          </v-button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import Form from 'vform'

export default {
  scrollToTop: false,

  metaInfo () {
    return { title: this.$t('settings') }
  },

  data: () => ({
    form: new Form({
      password: '',
      password_confirmation: ''
    })
  }),

  methods: {
    async update () {
      await this.form.patch('settings/password')

      this.form.reset()
    }
  }
}
</script>
