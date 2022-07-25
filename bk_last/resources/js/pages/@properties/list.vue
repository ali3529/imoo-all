<template>
  <div :property="property" class="row d-flex align-items-center align-content-center justify-content-center my-5">
    <div class="col-xs-12 col-md-8 col-xl-8 col-lg-10 p-4 pb-0 pt-lg-5 align-items-center">
      <h3 class="pb-4 mb-4 fst-italic border-bottom">
        {{ $t('my-properties') }}
      </h3>
      <div v-for="property in properties" class="row mb-2">
        <my-property :property="property" />
      </div>
    </div>
  </div>
</template>

<script>

import axios from 'axios'
// import i18n from '~/plugins/i18n'
import MyProperty from '../../components/Property'

export default {
  components: { MyProperty },
  middleware: 'auth',
  scrollToTop: true,

  metaInfo () {
    return { title: this.$t('write a property') }
  },

  data: () => ({
    properties: []
  }),

  created () {
    this.getProperties()
  },
  methods: {
    async getProperties () {
      const { data } = await axios.post('properties/my')
      this.properties = data.data
    }
  }
}
</script>
