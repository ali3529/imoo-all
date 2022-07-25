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
        The publication costs on immoall.ch are charged on a weekly flat rate basis. This begins on the day of
        publication, regardless of the time, and lasts for 7 days. The weekly flat rate will be charged in full as soon
        as the new invoicing period has started.
      </p>
      <a class="btn btn-outline-primary btn-lg" type="button" @click="$router.go(-1)">
        back
      </a>
      <v-button :loading="loading" class="btn btn-primary btn-lg" type="button" @click.native="publish">
        publish
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
