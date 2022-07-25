import Vue from 'vue'
import { config, library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

// import { } from '@fortawesome/free-regular-svg-icons'
import {
  faCog, faEdit,
  faEnvelope,
  faList,
  faLock,
  faMobile,
  faSignOutAlt,
  faUser
} from '@fortawesome/free-solid-svg-icons'

import { faApple, faFacebook, faGithub, faGoogle, faTwitter } from '@fortawesome/free-brands-svg-icons'

config.autoAddCss = false

library.add(
  faUser, faLock, faSignOutAlt, faCog, faGithub, faGoogle, faApple, faFacebook, faTwitter, faMobile, faEnvelope, faList, faEdit
)

Vue.component('Fa', FontAwesomeIcon)
