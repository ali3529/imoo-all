<template>
  <div class="row justify-content-center g-5">
    <!--    <div class="col-md-5">-->
    <div class="col-md-3 order-md-first mb-3">
      <h4 class="d-flex justify-content-between align-items-center mb-3">
        <span class="">{{ $t('settings') }}</span>
        <span class="badge bg-primary rounded-pill" />
      </h4>
      <ul class="nav flex-column nav-pills">
        <li v-for="tab in tabs" :key="tab.route" class="nav-item">
          <router-link :to="{ name: tab.route }" class="nav-link" active-class="active">
            <fa :icon="tab.icon" fixed-width />
            {{ tab.name }}
          </router-link>
        </li>
      </ul>
    </div>
    <div class="col-md-7 col-lg-8">
      <transition name="fade" mode="out-in">
        <router-view />
      </transition>
    </div>
  </div>
</template>

<script>
export default {
  middleware: 'auth',

  computed: {
    tabs () {
      return [
        {
          icon: 'user',
          name: this.$t('profile'),
          route: 'settings.profile'
        },
        {
          icon: 'lock',
          name: this.$t('password'),
          route: 'settings.password'
        }
      ]
    }
  }
}
</script>

<style>
.settings-card .card-body {
  padding: 0;
}
</style>
