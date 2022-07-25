<template>
  <div class="row">
    <div class="">
      <div class="position-relative d-flex align-items-center justify-content-center align-items-center">
        <b-card
          class="p-2 text-center justify-content-center align-items-center col-12 col-sm-10 col-md-10 col-lg-10 col-xl-7"
        >
          <h6>{{ $t('Please enter the validation code') }}<br></h6>
          <div><span> {{ $t('sent to') }}</span> <small>{{ number }}</small></div>
          <form name="form-code" class="inputs row d-flex flex-row justify-content-center align-items-center mt-2"
                @submit.prevent="verify" @keydown="form.onKeydown($event)"
          >
            <input v-model="form.phone" type="hidden">
            <input :disabled="form.busy" name="input1" class="m-2 text-center form-control rounded" type="number"
                   maxlength="1"
            >
            <input :disabled="form.busy" name="input2" class="m-2 text-center form-control rounded" type="number"
                   maxlength="1"
            >
            <input :disabled="form.busy" name="input3" class="m-2 text-center form-control rounded" type="number"
                   maxlength="1"
            >
            <span class="w-auto m-2">-</span>
            <input :disabled="form.busy" name="input4" class="m-2 text-center form-control rounded" type="number"
                   maxlength="1"
            >
            <input :disabled="form.busy" name="input5" class="m-2 text-center form-control rounded" type="number"
                   maxlength="1"
            >
            <input :disabled="form.busy" name="input6" class="m-2 text-center form-control rounded" type="number"
                   maxlength="1"
            >

            <div>{{ $t('code expires in') }}: <span class="text-danger">{{ timer | minutesAndSeconds }}</span></div>
            <div class="mt-3 row">
              <div class="col-md-12">
                <!-- Submit Button -->
                <v-button ref="submit" :disabled="!resending" type="submit" :loading="form.busy"
                          class="btn btn-primary btn-block w-50"
                >
                  {{ $t('verify') }}
                </v-button>
              </div>
            </div>
          </form>
          <div class="mt-3 content d-flex justify-content-center align-items-center">
            <div />
            <span class="small">{{ $t("didnt get the code?") }}</span>
            <v-button :disabled="resending || !validPage" type="linkType" :loading="loadingResend"
                      class="btn btn-link small text-decoration-none" @click.native="onResendBtnClick"
            >
              {{ $t('resend') }}({{ trySendCount }}/{{ maxTrySend }})
            </v-button>
          </div>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>

import Form from 'vform'
import VButton from '../../components/Button'
import axios from 'axios'
import router from '../../router'

export default {
  components: { VButton },
  filters: {
    minutesAndSeconds (value) {
      // cal expire timer
      const minutes = Math.floor(parseInt(value, 10) / 60)
      const seconds = parseInt(value, 10) - minutes * 60
      return `${minutes}:${seconds}`
    }
  },
  middleware: 'auth',

  metaInfo () {
    return { title: this.$t('verify_phone') }
  },
  data () {
    // user phone number
    const num = this.$route.params.number
    // expire limit
    const expire = this.$route.params.expire
    return {
      number: num,
      timer: 0 || expire,
      maxTrySend: 3,
      trySendCount: 1,
      loadingResend: false,
      resending: true,
      validPage: num !== undefined,
      form: new Form({
        code: '',
        phone: ''
      })
    }
  },

  created () {
    // optional
    if (this.$route.query.phone) {
      this.form.phone = this.$route.query.phone
    }
  },
  mounted () {
    this.inputenter()
    setInterval(() => {
      if (this.timer > 0) {
        this.resending = true
        this.timer -= 1
      } else {
        this.resending = false
      }
    }, 1000)
  },

  methods: {
    async verify () {
      // Submit the form.
      const code = this.generateCode()
      if (code.length < 6) return
      this.form.code = code
      const { data } = await this.form.post('user/phone/verify')
      this.status = data.status
      this.form.reset()

      // Fetch the user.
      await this.$store.dispatch('auth/fetchUser')
      // go to setting page
      await router.push({ name: 'settings.profile' })
    },

    inputenter () {
      const inputs = document.querySelectorAll('form[name=form-code] input[type=number]')
      const mThis = this
      for (let i = 0; i < inputs.length; i++) {
        inputs[i].addEventListener('keydown', function (event) {
          if (event.key === 'Backspace') {
            inputs[i].value = ''
            if (i !== 0) inputs[i - 1].focus()
          } else {
            if (i === inputs.length - 1 && inputs[i].value !== '') {
              return true
            } else if ((event.keyCode > 47 && event.keyCode < 58) || (event.keyCode > 95 && event.keyCode < 106)) {
              inputs[i].value = event.key
              if (i !== inputs.length - 1) {
                inputs[i + 1].focus()
              } else if (i === inputs.length - 1) {
                mThis.verify()
              }
              event.preventDefault()
            } else if (event.keyCode > 64 && event.keyCode < 91) {
              // inputs[i].value = String.fromCharCode(event.keyCode)
              // if (i !== inputs.length - 1) inputs[i + 1].focus()
              // event.preventDefault()
            }
          }
        })
      }
    },

    onResendBtnClick () {
      // send phone verification
      if (this.trySendCount >= this.maxTrySend) return
      this.loadingResend = true
      axios.post('user/phone/verify/send').then((res) => res.data).then((res) => {
        this.number = res.data.phone
        this.timer = res.data.expire
        this.trySendCount++
      }).catch((err) => console.log(err))
        .finally(() => {
          this.loadingResend = false
        })
    },

    generateCode () {
      let code = ''
      const inputs = document.querySelectorAll('form[name=form-code] input[type=number]')
      inputs.forEach(function (index) {
        code += index.value
      })
      return code
    }
  }
}
</script>

<style scoped>

.inputs input {
  width: 40px;
  height: 40px
}

input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  margin: 0
}

.form-control:focus {
  box-shadow: none;
  border: 2px solid #6F1667
}

</style>
