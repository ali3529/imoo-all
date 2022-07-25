<template>
  <div class="d-flex justify-content-center align-items-center align-content-center mb-5">
    <div class="row w-100 mt-5">
      <div class="col-xl-6 col-lg-7 col-md-9 col-sm-10 m-auto">
        <div class="text-center mb-4">
          <img class="mb-4" src="/images/person-circle.svg" alt="" width="72"
               height="72"
          >
          <h1 class="h3 mb-3 font-weight-normal">
            {{ $t('login') }}
          </h1>
          <!--          <p>Build form controls with floating labels via the <code>:placeholder-shown</code> pseudo-element. <a-->
          <!--            href="https://caniuse.com/#feat=css-placeholder-shown">Works in latest Chrome, Safari, and Firefox.</a></p>-->
        </div>
        <form @submit.prevent="login" @keydown="form.onKeydown($event)">
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

          <!-- Remember Me -->
          <div class="mb-3 row">
            <div class="col-md-3" />
            <div class="col-md-7 d-flex">
              <checkbox v-model="remember" name="remember">
                {{ $t('remember_me') }}
              </checkbox>

              <router-link :to="{ name: 'password.request' }" class="small ms-auto my-auto">
                {{ $t('forgot_password') }}
              </router-link>
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

          <div class="mb-3 row">
            <div class="col-md-7 offset-md-3 d-flex">
              <!-- Submit Button -->
              <v-button :loading="form.busy" class="btn btn-primary btn-block w-100">
                {{ $t('login') }}
              </v-button>
            </div>
          </div>

          <div class="row">
            <div class="col-md-7 offset-md-3">
              {{ $t('Don`t have an account yet?') }}
              <router-link :to="{ name: 'register' }" class="small ms-auto">
                {{ $t('register_now') }}
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
import Cookies from 'js-cookie'
import LoginWithGithub from '~/components/LoginWithGithub'
import LoginWithGoogle from '../../components/LoginWithGoogle'
import LoginWithApple from '../../components/LoginWithApple'
import LoginWithFacebook from '../../components/LoginWithFacebook'
import LoginWithTwitter from '../../components/LoginWithTwitter'
import VueRecaptcha from 'vue-recaptcha'

export default {
  components: {
    LoginWithTwitter,
    LoginWithFacebook,
    LoginWithApple,
    LoginWithGoogle,
    LoginWithGithub,
    VueRecaptcha
  },

  middleware: 'guest',

  metaInfo () {
    return { title: this.$t('login') }
  },

  data: () => ({
    form: new Form({
      email: '',
      password: '',
      recaptcha: ''
    }),
    remember: false
  }),

  computed: {
    recaptchaKey: () => window.config.recaptchaKey
  },

  methods: {
    async login () {
      // Submit the form.
      const { data } = await this.form.post('login')

      // Save the token.
      this.$store.dispatch('auth/saveToken', {
        token: data.token,
        remember: this.remember
      })

      // Fetch the user.
      await this.$store.dispatch('auth/fetchUser')

      // Redirect home.
      this.redirect()
    },

    redirect () {
      const intendedUrl = Cookies.get('intended_url')

      if (intendedUrl) {
        Cookies.remove('intended_url')
        this.$router.push({ path: intendedUrl })
      } else {
        this.$router.push({ name: 'home' })
      }
    },

    onVerify: function (response) {
      if (response) this.form.recaptcha = response
    }
  }
}
</script>
