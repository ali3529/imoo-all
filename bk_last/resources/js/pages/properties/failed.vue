<template>
  <div id="featured-3" class="container px-4 py-5">
    <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
      <div class="feature col">
        <div class="feature-icon bg-danger bg-gradient">
          <svg class="bi" width="1em" height="1em">
            <use xlink:href="#collection" />
          </svg>
        </div>
        <h2>failed.</h2>
        <p />
        <p>Your object reference: <b>{{ $route.params.reference }}</b></p>

        <router-link :to="{ name: 'properties.my'}" class="nav-link">
          Continue to my advertisements
        </router-link>
      </div>
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
    package: null
  }),

  created () {
    this.propertyPackage()
  },

  methods: {
    async propertyPackage () {
      const { data } = await axios.post('details/propertyPackage', { packageId: this.$route.params.package })
      this.package = data.data
    },

    async publish () {
      const { data } = await axios.post('properties/insertion/publish', {
        reference: this.$route.params.reference,
        packageId: this.package.id
      })
      // confirmation

      if (data.status) {
        const intendedUrl = '/properties/' + this.$route.params.reference + '/details/confirmation'
        this.$router.push({ path: intendedUrl })
      } else {
        alert(data.message)
      }
    }
  }
}
</script>
