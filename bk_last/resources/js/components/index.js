import Vue from 'vue'
import Card from './Card.vue'
import Child from './Child.vue'
import Button from './Button.vue'
import Checkbox from './Checkbox.vue'
import { HasError, AlertError, AlertSuccess } from 'vform/components/bootstrap5'
import vSelect from 'vue-select'
import VueRecaptcha from 'vue-recaptcha';

// Components that are registered globaly.
[
  Card,
  Child,
  Button,
  Checkbox,
  HasError,
  AlertError,
  AlertSuccess
].forEach(Component => {
  Vue.component(Component.name, Component)
})
Vue.component('VSelect', vSelect)
Vue.component('VueRecaptcha', VueRecaptcha)
