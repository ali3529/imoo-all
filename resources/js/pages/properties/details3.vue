<template>
  <div>
    <h4 class="mb-3">
      {{ $t('billing') }}
    </h4>
    <div class="container-fluid py-5">
      <h1 class="display-5 fw-bold">
        <span>{{ $t('Your selection:') }}</span> <b>{{ package.name }}</b>
      </h1>
      <p class="col-md-8 fs-4">
		{{ $t('The advertisement will be activated for publication after our quality check.The duration of advertisements is one month. If the advertisement is not canceled before the end of the monthly period 30 days , the term is automatically extended by a further month. The customer is responsible for canceling his ad before the 30-day period has expired.All prices are in Swiss Francs CHF exclusive of VAT') }}
        
      </p>
      <a class="btn btn-outline-primary btn-lg" type="button" @click="$router.go(-1)">
        back
      </a>
      <v-button :loading="loading" class="btn btn-primary btn-lg" type="button" @click.native="publish">
        {{ $t('publish') }}
      </v-button>

      
    </div>
  </div>
</template>

<script>

import axios from 'axios'
import VButton from '../../components/Button'

export default {
  components: { VButton },
  middleware: 'auth',
  scrollToTop: true,

  metaInfo () {
    return { title: this.$t('write a property') }
  },

  data: () => ({
    package: null,
    loading: false
  }),

  created () {
    this.propertyPackage()
  },

  methods: {
    async propertyPackage () {
      const { data } = await axios.post('properties/insertion/propertyPackage', {
        reference: this.$route.params.reference
      })
      this.package = data.data
    },

    async publish () {
      this.loading = true
      const { data } = await axios.post('properties/insertion/publish', {
        reference: this.$route.params.reference
      })
      this.loading = false
      // confirmation

      if (data.status) {
        if (data.data.status === 'free') {
          const intendedUrl = '/properties/' + data.data.url + '/details/confirmation'
          this.$router.push({ path: intendedUrl })
        } else {
          window.open(data.data.url, '_self')
        }
      } else { alert(data.message) }
    }
  }
}
</script>
