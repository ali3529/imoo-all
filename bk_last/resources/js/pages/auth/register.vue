<template>
  <div class="d-flex justify-content-center align-items-center align-content-center mb-5">
    <div class="row w-100 mt-5">
      <card v-if="mustVerifyEmail" :title="$t('register')">
        <div class="alert alert-success" role="alert">
          {{ $t('verify_email_address') }}
        </div>
      </card>
      <div v-else :title="$t('register')" class="col-7 m-auto">
        <div class="text-center mb-4">
          <img class="mb-4" src="/images/person-circle.svg" alt="" width="72"
               height="72"
          >
          <h1 class="h3 mb-3 font-weight-normal">
            {{ $t('register') }}
          </h1>
          <!--          <p>Build form controls with floating labels via the <code>:placeholder-shown</code> pseudo-element. <a-->
          <!--            href="https://caniuse.com/#feat=css-placeholder-shown">Works in latest Chrome, Safari, and Firefox.</a></p>-->
        </div>
        <form @submit.prevent="register" @keydown="form.onKeydown($event)">
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end" />
            <div class="col-md-7">
              <!-- GitHub Login Button -->
              <login-with-github />
              <!-- Google Login Button -->
              <login-with-google />
              <!-- Apple Login Button -->
              <login-with-apple />
              <!-- Facebook Login Button -->
              <login-with-facebook />
              <!-- Twitter Login Button -->
              <login-with-twitter />
            </div>
            <label class="col-md-3 col-form-label text-md-end" />
            <div class="col-md-7">
              <div class="d-flex">
                <hr class="w-100">
                <span class="text-uppercase mx-3">{{ $t('or') }}</span>
                <hr class="w-100">
              </div>
            </div>
          </div>

          <!-- first name -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{ $t('first_name') }}</label>
            <div class="col-md-7">
              <input v-model="form.first_name" :class="{ 'is-invalid': form.errors.has('first_name') }" class="form-control"
                     type="text" name="first_name"
              >
              <has-error :form="form" field="first_name" />
            </div>
          </div>

          <!-- last name -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{ $t('last_name') }}</label>
            <div class="col-md-7">
              <input v-model="form.last_name" :class="{ 'is-invalid': form.errors.has('last_name') }" class="form-control"
                     type="text" name="last_name"
              >
              <has-error :form="form" field="last_name" />
            </div>
          </div>

          <!-- phone -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{ $t('phone') }}</label>
            <div class="col-md-7">
              <input v-model="form.phone" :class="{ 'is-invalid': form.errors.has('phone') }" class="form-control"
                     type="tel" name="phone"
              >
              <has-error :form="form" field="phone" />
            </div>
          </div>

          <!-- Email -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{ $t('email') }}</label>
            <div class="col-md-7">
              <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" class="form-control"
                     type="email" name="email"
              >
              <has-error :form="form" field="email" />
            </div>
          </div>

          <!-- Password -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{ $t('password') }}</label>
            <div class="col-md-7">
              <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }" class="form-control"
                     type="password" name="password"
              >
              <has-error :form="form" field="password" />
            </div>
          </div>

          <!-- Password Confirmation -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{ $t('confirm_password') }}</label>
            <div class="col-md-7">
              <input v-model="form.password_confirmation"
                     :class="{ 'is-invalid': form.errors.has('password_confirmation') }" class="form-control"
                     type="password" name="password_confirmation"
              >
              <has-error :form="form" field="password_confirmation" />
            </div>
          </div>

          <div class="mb-3 row">
            <div class="col-md-7 offset-md-3 d-flex">
              <!-- Submit Button -->
              <v-button :loading="form.busy" class="btn btn-primary btn-block w-100">
                {{ $t('register') }}
              </v-button>
            </div>
          </div>

          <div class="mb-3 row">
            <div class="col-md-7 offset-md-3">
              {{ $t('By clicking Register, I agree that I have read and accepted the IMMO ALL') }}
              <router-link :to="{ name:'terms'}" class="small ms-auto">
                {{ $t('Terms of Use and Privacy Policy') }}
              </router-link>
            </div>
          </div>

          <div class="row">
            <div class="col-md-7 offset-md-3">
              {{ $t('Already have login and password?') }}
              <router-link :to="{ name:'login' }" class="small ms-auto">
                {{ $t('Sign in') }}
              </router-link>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import Form from 'vform'
import LoginWithGithub from '~/components/LoginWithGithub'
import LoginWithGoogle from '~/components/LoginWithGoogle'
import LoginWithFacebook from '~/components/LoginWithFacebook'
import LoginWithTwitter from '~/components/LoginWithTwitter'
import LoginWithApple from '~/components/LoginWithApple'

export default {
  components: {
    LoginWithGithub,
    LoginWithGoogle,
    LoginWithFacebook,
    LoginWithTwitter,
    LoginWithApple
  },

  middleware: 'guest',

  metaInfo () {
    return { title: this.$t('register') }
  },

  data: () => ({
    form: new Form({
      email: '',
      password: '',
      password_confirmation: ''
    }),
    mustVerifyEmail: false
  }),

  methods: {
    async register () {
      // Register the user.
      const { data } = await this.form.post('register')
      if (data.status) {
        // Must verify email fist.
        // this.mustVerifyEmail = true
        await this.goToHome(data.data)
      } else {
        // Log in the user.
        const data = await this.form.post('login')
        await this.goToHome(data)
      }
    },

    async goToHome (data) {
      // Save the token.
      await this.$store.dispatch('auth/saveToken', { token: data.token })

      // Update the user.
      await this.$store.dispatch('auth/updateUser', { user: data })

      // Redirect home.
      await this.$router.push({ name: 'home' })
    }
  }
}
</script>
