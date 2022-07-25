<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
      <router-link :to="{ name: user ? 'home' : 'welcome' }" class="navbar-brand">
<!--        {{ appName }}-->
        <b-img src="/images/immoall-logo.png" height="30" />
      </router-link>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar">
        <span class="navbar-toggler-icon" />
      </button>

      <div id="navbar" class="collapse navbar-collapse">
      <div v-if="user.confirmed_at === null">
Verify Email!
        </div>
     <div v-else>

        <ul class="navbar-nav">
          <li v-if="user" class="nav-item">
            <router-link :to="{ name: 'properties.create'}" class="nav-link">
              {{ $t('submit_ad') }}
            </router-link>
          </li>
        </ul>

     </div>
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <router-link :to="{ name: 'welcome' }" class="nav-link" active-class="">
              {{ $t('home page') }}
            </router-link>
          </li>
          <!-- Authenticated -->
          <template v-if="user">
            <li class="nav-item">
              <router-link :to="{ name: 'settings.profile' }" class="nav-link" active-class="active">
<!--                <fa fixed-width icon="cog" />-->
                {{ $t('settings') }}
              </router-link>
            </li>
            <li class="nav-item">
              <router-link :to="{ name: 'properties.my' }" class="nav-link" active-class="active">
<!--                <fa fixed-width icon="list" />-->
                {{ $t('my-properties') }}
              </router-link>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-dark"
                 href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
              >
                {{ user.full_name }}
                <img :src="user.photo_url" :tag="user.full_name" class="rounded-circle profile-photo me-1">
              </a>
              <div class="dropdown-menu">
<!--                <div class="dropdown-divider" />-->
                <a href="#" class="dropdown-item ps-3" @click.prevent="logout">
                  <fa icon="sign-out-alt" fixed-width />
                  {{ $t('logout') }}
                </a>
              </div>

            </li>
          </template>
          <!-- Guest -->
          <template v-else>
            <li class="nav-item">
              <router-link :to="{ name: 'login' }" class="nav-link" active-class="active">
                {{ $t('login') }}
              </router-link>
            </li>
            <li class="nav-item">
              <router-link :to="{ name: 'register' }" class="nav-link" active-class="active">
                {{ $t('register') }}
              </router-link>
            </li>
          </template>

          <locale-dropdown />
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
import { mapGetters } from 'vuex'
import LocaleDropdown from './LocaleDropdown'

export default {
  components: {
    LocaleDropdown
  },

  data: () => ({
    appName: window.config.appName
  }),

  computed: mapGetters({
    user: 'auth/user'
  }),

  methods: {
    async logout () {
      // Log out the user.
      await this.$store.dispatch('auth/logout')

      // Redirect to login.
      this.$router.push({ name: 'login' })
    }
  }
}
</script>

<style scoped>
.profile-photo {
  width: 2rem;
  height: 2rem;
  margin: -.375rem 0;
}

.container {
  max-width: 1100px;
}
</style>
