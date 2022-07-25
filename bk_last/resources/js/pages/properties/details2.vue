<template>
  <div>
    <h4 class="mb-3">
      {{ $t('Select your service package') }}
    </h4>
    <div class="row d-flex align-items-center align-content-center justify-content-center my-5">
      <div class="row row-cols-1 row-cols-md-3 mb-3 text-center">
        <div v-for="pack in packages" class="col-">
          <div class="card mb-4 rounded-3 shadow-sm">
            <div class="card-header py-3"
                 :class="{'border-primary':pack.is_default,'bg-primary':pack.is_default,'text-white':pack.is_default}"
            >
              <h4 class="my-0 fw-normal">
                {{ $t(pack.name) }}
              </h4>
            </div>
            <div class="card-body">
              <h1 v-if="pack.price === 0" class="card-title pricing-card-title">
                {{ $t('free') }}
              </h1>
              <h1 v-else class="card-title pricing-card-title">
                {{ pack.price }} {{ pack.currency.symbol }}<small class="text-muted fw-light">/mo</small>
              </h1>

              <v-button :loading="isLoading(pack.id)" :block="isBlock(pack.id)" type="button" class="w-100 btn btn-lg"
                        :class="{'btn-primary':pack.is_default,'btn-outline-primary':!pack.is_default}"
                        @click.native="updateProperty(pack.id,this)"
              >
                {{ $t('continue') }}
              </v-button>
            </div>
          </div>
        </div>
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
    packages: [],
    loading: 0
  }),

  created () {
    this.propertyPackages()
  },

  methods: {
    async updateProperty (packageId, e) {
      this.loading = packageId
      const { data } = await axios.post('properties/insertion/package-update', {
        reference: this.$route.params.reference,
        packageId: packageId
      })
      if (data.status) {
        const intendedUrl = '/properties/' + this.$route.params.reference + '/details/step3'
        this.$router.push({ path: intendedUrl })
      } else { alert(data.message) }
      this.loading = 0
    },

    async propertyPackages () {
      const { data } = await axios.post('details/propertyPackages')
      this.packages = data.data
    },

    isLoading (id) {
      return this.loading !== 0 && this.loading === id
    },

    isBlock (id) {
      return this.loading !== 0 && this.loading !== id
    }
  }
}
</script>
