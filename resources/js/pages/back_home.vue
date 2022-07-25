<template>
  <div class="row">
    <div class="col-lg-10 m-auto">

      <div class="col-md-12">
              <b-alert :show="showAlertEmailValidation" dismissible variant="warning">
                 {{ $t('Please verify email address, If you do not receive our email, please check your junk or spam folder') }}
          <v-button type="linkType" :loading="loadingEmailValidation" class="btn btn-link small p-0 m-0" @click.native="onSendActivationBtnClick">
            {{ $t('email_verification') }}
            
          </v-button><br />
           </b-alert>
              
      </div>
      <card :title="$t('home')">
        <router-link class="btn btn-info btn-lg py-2 text-uppercase" :to="{ name: 'properties.create'}">
          {{ $t('submit_ad') }}
        </router-link>
      </card>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import store from '~/store'
import VButton from '../components/Button'
import Swal from 'sweetalert2/dist/sweetalert2.js'
import i18n from '~/plugins/i18n'

export default {
  components: { VButton },
  middleware: 'auth',

  data () {
    this.fetchUser()
    return {
      user: null,
      showAlertEmailValidation: false,
      loadingEmailValidation: false
    }
  },

  methods: {
    onSendActivationBtnClick () {
      // send email verification
      this.loadingEmailValidation = true
      axios.post('user/email/verify').then((res) => {
        return res.data
      }).then((res) => {
        if (res.status) {
          Swal.fire({
            icon: 'success',
            text: res.message,
            reverseButtons: true,
            confirmButtonText: i18n.t('ok')
          })
        } else {
          Swal.fire({
            icon: 'error',
            title: i18n.t('error_alert_title'),
            text: res.message,
            reverseButtons: true,
            confirmButtonText: i18n.t('ok')
          })
        }
      })
        .catch((err) => console.log(err)).finally(() => {
          this.loadingEmailValidation = false
        })
    },

    async fetchUser () {
      // Fetch the user.

      this.$store.dispatch('auth/fetchUser')
        .then((res) => {
          this.updateUser(store.getters['auth/user'])
        }).catch((er) => console.error(er))
    },

    updateShowAlertState (show) {
      this.showAlertEmailValidation = show
    },

    updateUser (user) {
      this.user = user
      this.updateShowAlertState(user.confirmed_at === null)
    }
  },
  metaInfo () {
    return { title: this.$t('home') }
  }
}
</script>

<style scope>
.close {
  float: right;
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1;
  color: #000;
  text-shadow: 0 1px 0 #fff;
  opacity: .5;
}

.alert-dismissible .close {
  position: absolute;
  top: 0;
  right: 0;
  z-index: 2;
  padding: .75rem 1.25rem;
  color: inherit;
}

button.close {
  padding: 0;
  background-color: transparent;
  border: 0;
}
</style>
