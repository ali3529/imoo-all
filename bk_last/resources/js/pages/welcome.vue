<template>
  <div class="">
    <div class="d-flex justify-content-between align-items-start p-3">
      <div class="links d-sm-block d-none">
        <router-link class="text-uppercase" :to="{ name: 'welcome' }">
          <span class="logo-text fw-bold">
            {{ title }}
          </span>
        </router-link>
      </div>
      <div class="links">
        <template v-if="authenticated">
          <router-link class="btn btn-danger btn-lg py-2 text-uppercase" :to="{ name: 'home' }">
            {{ $t('home') }}
          </router-link>
        </template>
        <template v-else>
          <router-link class="btn btn-danger btn-lg py-2 text-uppercase" :to="{ name: 'login' }">
            {{ $t('login') }}
          </router-link>
          <router-link class="btn btn-outline-danger btn-lg py-2 text-uppercase" :to="{ name: 'register' }">
            {{ $t('register') }}
          </router-link>
        </template>
      </div>
    </div>

    <div class="box d-flex text-center align-items-center justify-content-center flex-column py-3 mx-5">
      <div class="title">
        {{ $t('home title') }}
      </div>
      <div class="d-flex align-items-center justify-content-center align-content-center mb-4">
        <span class="description col-sm-11 col-md-7 col-lg-10">
          {{ $t('home description') }}
        </span>
      </div>
      <div class="links">
        <router-link class="btn btn-danger btn-lg py-2 text-uppercase" :to="{ name: 'login' }">
          {{ $t('get started') }}
        </router-link>
        <router-link class="btn btn-outline-danger btn-lg py-2" :to="{ name: 'home' }">
          {{ $t('home') }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import VButton from '../components/Button'

export default {
  components: { VButton },
  layout: 'basic',

  metaInfo () {
    return { title: this.$t('home') }
  },

  data: () => ({
    title: window.config.appName
  }),

  computed: mapGetters({
    authenticated: 'auth/check'
  })
}
</script>

<style lang="scss" scoped>
.box{
  height: 95vh;
  //overflow: scroll;
}
.logo-text{
  font-size: 1.3rem;
}
.title {
  font-size: 2.9rem;
  color: white;
}
.description {
  font-size: 1.3rem;
  color: white;
}
.links{
  a{
    color: white;
  }
}
</style>
