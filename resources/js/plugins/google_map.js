import Vue from 'vue'
import * as VueGoogleMaps from 'vue2-google-maps'

// import 'vue-select/src/scss/vue-select.scss'

Vue.use(VueGoogleMaps, {
  load: {
    key: window.config.googleApiKey,
    libraries: 'places'
  }
})
