<template>
  <div class="col-md-12">
    <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
      <div class="col-auto d-none d-lg-block">
        <svg v-if="property.images.lenght" class="bd-placeholder-img" width="250" height="250" xmlns="http://www.w3.org/2000/svg" role="img"
             aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false"
        >
          <title>
            Placeholder
          </title>
          <rect width="100%" height="100%" fill="#55595c" />
        </svg>

        <img v-else :src="property.images[0]" width="250" height="250">
      </div>
      <div class="col p-4 d-flex flex-column position-static">
        <strong class="d-inline-block mb-2 text-primary">{{ property.name }}</strong>
        <h3 class="mb-0">
          {{ new Date(property.created_at).toLocaleDateString() }}
        </h3>
        <div class="mb-1 text-muted">
          {{ property.type }} . {{ property.status }}
        </div>
        <p class="card-text mb-auto">
          {{ property.description }}
        </p>
        <div>
          <button href="#" class="btn btn-primary" @click="editProperty">
            {{ $t('edit') }}
          </button>
          <button href="#" class="btn btn-danger" @click.prevent="deleteProperty(property.id)">
            {{ $t('delete') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import VButton from './Button'
import Swal from 'sweetalert2'
import axios from 'axios'
import i18n from '../plugins/i18n'
export default {
  name: 'MyProperty',
  components: { VButton },
  props: {
    property: {
      type: Object,
      default: null
    }
  },
  methods: {
    editProperty () {
      const intendedUrl = '/properties/' + this.property.reference + '/details'

      this.$router.push({ path: intendedUrl })
    },
    deleteProperty: function (property) {
      Swal.fire({
        title: i18n.t('are you sure?'),
        text: i18n.t('you wont be able to revert this!'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: i18n.t('yes, delete it!'),
        cancelButtonText: i18n.t('cancel')
      }).then((result) => {
        if (result.isConfirmed) {
          axios.post(`properties/${property}/delete`).then((res) => res.data).then((res) => {
          }).finally((d) => {
            this.$parent.getProperties()
          })
        }
      })
    }
  }
}
</script>
