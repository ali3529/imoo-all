<template>
  <li v-if="Object.keys(locales).length > 1" class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" role="button"
       data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
    >
      <img :src="'/images/flag/'+locale+'.svg'" alt="flag" width="20" height="20" class="me-1">
      <!--      {{ locales[locale] }}-->
    </a>
    <div class="dropdown-menu  dropdown-menu-end">
      <a v-for="(value, key) in locales" :key="key" class="dropdown-item" href="#"
         @click.prevent="setLocale(key)"
      >
        <img :src="'/images/flag/'+key+'.svg'" alt="flag" width="20" height="20">
        {{ value }}
      </a>
    </div>
  </li>
</template>

<script>
import { mapGetters } from 'vuex'
import { loadMessages } from '~/plugins/i18n'

export default {
  computed: mapGetters({
    locale: 'lang/locale',
    locales: 'lang/locales'
  }),

  methods: {
    setLocale (locale) {
      if (this.$i18n.locale !== locale) {
        loadMessages(locale)

        this.$store.dispatch('lang/setLocale', { locale })
      }
    }
  }
}
</script>
