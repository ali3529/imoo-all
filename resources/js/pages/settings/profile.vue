<template>
  <div class="row">
    <div class="col-md-3 order-md-last mb-3">
      <div class="avatar-view mt-card-avatar">
        <img :src="user.photo_url" alt="avatar" height="200" width="200" class="rounded-3">
        <input ref="photo" type="file" accept="image/*" hidden @change="updateAvatar">
        <div class="mt-overlay" @click="$refs.photo.click()">
          <fa :icon="['fa', 'edit']" class="text-white" />
        </div>
      </div>

      <div v-if="showUploadProgress">
        Uploading: {{ uploadPercent }} %
      </div>
    </div>

    <div class="col-md-7 col-lg-8">
      <h4 class="mb-3">
        {{ $t('your_info') }}
      </h4>
      <form class="needs-validation" novalidate="" @submit.prevent="update" @keydown="form.onKeydown($event)">
        <alert-success :form="form" :message="$t('info_updated')" />
        <div class="row gy-3">
          <!-- first name -->
          <div class="col-sm-6">
            <label for="firstName" class="form-label">{{ $t('first_name') }}</label>
            <input id="firstName" v-model="form.first_name" :class="{ 'is-invalid': form.errors.has('first_name') }"
                   class="form-control" type="text"
                   name="first_name" placeholder="" spellcheck="false" data-ms-editor="true"
            >
            <has-error :form="form" field="first_name" />
          </div>

          <!-- last name -->
          <div class="col-sm-6">
            <label for="lastName" class="form-label">{{ $t('last_name') }}</label>
            <input id="lastName" v-model="form.last_name" :class="{ 'is-invalid': form.errors.has('last_name') }"
                   class="form-control" type="text"
                   name="last_name" placeholder="" spellcheck="false" data-ms-editor="true"
            >
            <has-error :form="form" field="last_name" />
          </div>

          <!-- username -->
          <div class="col-12">
            <label for="username" class="form-label">{{ $t('username') }}</label>
            <div class="input-group has-validation">
              <span class="input-group-text">@</span>
              <input id="username" v-model="form.username"
                     class="form-control" type="text"
                     name="username" placeholder="Username" spellcheck="false" data-ms-editor="true"
                     readonly required
              >
              <has-error :form="form" field="username" />
            </div>
          </div>

          <!-- phone -->
          <div class="col-12">
            <label class="form-label">{{ $t('phone') }}</label>
            <div class="input-group has-validation">
              <span class="input-group-text"><fa icon="mobile" fixed-width /></span>
              <b-form-input v-model="form.phone" :class="{ 'is-invalid': form.errors.has('phone') }"
                            class="form-control" type="tel" name="phone" placeholder=""
              >
                <has-error :form="form" field="phone" />
              </b-form-input>
            </div>
            <b-alert :show="user.confirmed_phone_at === null && user.phone" variant="warning"
                     class="my-1 px-1 py-0"
            >
              {{ $t('please_confirm_your_phone:') }}
              <v-button type="linkType" :loading="loadingPhoneValidation" class="btn btn-link small p-0 m-0"
                        @click.native="onSendPhoneVerificationClick"
              >
                {{ $t('phone_verification') }}
              </v-button>
            </b-alert>
          </div>

          <!-- email -->
          <div class="col-12">
            <label for="email" class="form-label">{{ $t('email') }}</label>
            <div class="input-group has-validation">
              <span class="input-group-text"><fa icon="envelope" fixed-width /></span>
              <input id="email" v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }"
                     class="form-control" type="email" name="email" placeholder="you@example.com"
                     :readonly="user.confirmed_at"
              >
              <has-error :form="form" field="email" />
            </div>
            <b-alert :show="user.confirmed_at === null" variant="warning" class="my-1 px-1 py-0">
              {{ $t('Please confirm your email:') }}
              <v-button type="linkType" :loading="loadingEmailValidation" class="btn btn-link small p-0 m-0"
                        @click.native="onSendActivationBtnClick"
              >
                {{ $t('email_verification') }}
              </v-button>
            </b-alert>
          </div>

          <!-- description OR address -->
          <div class="col-12">
            <label for="address" class="form-label">{{ $t('address') }}</label>
            <input id="address" v-model="form.description" :class="{ 'is-invalid': form.errors.has('description') }"
                   type="text" class="form-control" placeholder="1234 Main St" spellcheck="false"
                   data-ms-editor="true"
            >
            <has-error :form="form" field="description" />
          </div>

          <!-- dob -->
          <div class="col-md-12">
            <label class="form-label">{{ $t('birthday') }}</label>
            <b-form-input v-model="form.dob"
                          :class="{ 'is-invalid': form.errors.has('gender') }"
                          class="form-select"
                          type="date"
                          placeholder="YYYY-MM-DD"
                          autocomplete="off"
            />

            <has-error :form="form" field="dob" />
          </div>

          <!-- gender -->
          <div class="col-md-12">
            <label class="form-label">{{ $t('gender') }}</label>
            <b-form-select v-model="form.gender" :options="genderOptions"
                           :class="{ 'is-invalid': form.errors.has('gender') }" class="form-select"
            />
            <has-error :form="form" field="gender" />
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
  </div>
</template>

<script>
import Form from 'vform'
import { mapGetters } from 'vuex'
import axios from 'axios'
import router from '../../router'
import Swal from 'sweetalert2/dist/sweetalert2.js'
import i18n from '~/plugins/i18n'
import VButton from '../../components/Button'

export default {
  components: { VButton },
  scrollToTop: false,

  metaInfo () {
    return { title: this.$t('settings') }
  },

  data: () => ({
    form: new Form({
      first_name: '',
      last_name: '',
      username: '',
      email: '',
      description: '',
      gender: '',
      dob: '',
      phone: ''
    }),
    showAlertEmailValidation: false,
    loadingEmailValidation: false,
    loadingPhoneValidation: false,
    genderOptions: [
      { value: null, text: i18n.t('please select an option') },
      { value: 'male', text: i18n.t('male') },
      { value: 'female', text: i18n.t('female') },
      { value: 'other', text: i18n.t('other') }
    ],
    uploadPercent: 0,
    avatarImageUrl: '',
    showUploadProgress: false,
    processingUpload: false
  }),

  computed: mapGetters({
    user: 'auth/user'
  }),

  created () {
    // Fill the form with user data.
    this.form.keys().forEach(key => {
      this.form[key] = this.user[key]
    })
  },

  methods: {
    async update () {
      const { data } = await this.form.patch('settings/profile')

      this.$store.dispatch('auth/updateUser', { user: data })
    },

    onSendActivationBtnClick () {
      // send email verification
      this.loadingEmailValidation = true
      axios.post('user/email/verify').then((res) => res.data).then((res) => {
        Swal.fire({
          icon: 'success',
          text: res.message,
          reverseButtons: true,
          confirmButtonText: i18n.t('ok')
        })
      }).catch((err) => console.log(err))
        .finally(() => {
          this.loadingEmailValidation = false
        })
    },
    onSendPhoneVerificationClick () {
      // send phone verification
      this.loadingPhoneValidation = true
      axios.post('user/phone/verify/send').then((res) => res.data).then((res) => {
        // Swal.fire({
        //   icon: 'success',
        //   text: res.message,
        //   reverseButtons: true,
        //   confirmButtonText: i18n.t('ok')
        // })
        router.push({ name: 'settings.verifyPhone', params: { number: res.data.phone, expire: res.data.expire } })
      }).catch((err) => console.log(err))
        .finally(() => {
          this.loadingPhoneValidation = false
        })
    },
    updateAvatar (e) {
      if (this.$refs.photo) {
        this.showUploadProgress = true
        this.processingUpload = true
        this.uploadPercent = 0
        const formData = new FormData()
        const files = e.target.files || e.dataTransfer.files
        if (!files.length) {
          return
        }
        formData.append('avatar', files[0])
        axios.post('settings/profile/upload_avatar', formData, {
          onUploadProgress: (progressEvent) => {
            this.uploadPercent = progressEvent.lengthComputable ? Math.round((progressEvent.loaded * 100) / progressEvent.total) : 0
          }
        })
          .then((res) => {
            this.$store.dispatch('auth/updateUser', { user: res.data })
            this.showUploadProgress = false
            this.processingUpload = false
          })
          .catch((error) => {
            Swal.fire({
              icon: 'error',
              text: error.response.data.message,
              reverseButtons: true,
              confirmButtonText: i18n.t('ok')
            })
            this.showUploadProgress = false
            this.processingUpload = false
          })
      }
    }
  }
}
</script>

<style scoped>
.avatar-view{
  position: relative;
  display: flex;
}
.mt-overlay{
  width: 200px;
  height: 200px;
  position: absolute;
  overflow: hidden;
  display: flex;
  align-content: center;
  align-items: center;
  justify-content: center;
  top: 0;
  left: 0;
  opacity: 0;
  background-color: rgba(0,0,0,.7);
  transition: all .4s ease-in-out;
  cursor: pointer;
}
.mt-overlay:hover{
  opacity: 1;
}
</style>
