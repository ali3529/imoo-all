<template>
  <div class="row d-flex align-items-center align-content-center justify-content-center my-5">
    <div
      class="col-xs-12 col-sm-8 col-lg-7 col-xl-6 p-4 pb-0 pe-lg-0 pt-lg-5 align-items-center rounded-3 border shadow-lg"
    >
      <h1 class="fw-bold">
        {{ $t('place your ad') }}
      </h1>
      <div class="d-grid gap-2 d-md-flex justify-content-md-start mb-4 mb-lg-3">
        <form class="w-100" @submit.prevent="createProperty" @keydown="form.onKeydown($event)">
          <div class="row col-md-12 my-2">
            <!-- advertising type -->
            <div class="col-md-6">
              <label class="form-label fw-bold w-100">{{ $t('iam advertising as') }}
                <v-select v-model="form.advertisingType" :options="advertisingTypes" label="name" class=""
                          :class="{ 'is-invalid': form.errors.has('advertisingType') }"
                >
                  <template #search="{attributes, events}">
                    <input
                      class="vs__search"
                      :required="!form.advertisingType"
                      v-bind="attributes"
                      v-on="events"
                    >
                  </template>
                </v-select>
              </label>
              <has-error :form="form" field="category" />
            </div>

            <div class="col-md-6" />
          </div>
          <!-- type -->
          <label class="fw-bold">{{ $t('property for') }}
            <div class="d-flex my-1">
              <div class="form-check">
                <input id="rent" v-model="form.type" name="propertyType" type="radio" class="form-check-input"
                       value="rent"
                >
                <label class="form-check-label" for="rent">{{ $t('rent') }}</label>
              </div>
              <div class="form-check ms-2">
                <input id="sale" v-model="form.type" name="propertyType" type="radio" class="form-check-input"
                       value="sale"
                >
                <label class="form-check-label" for="sale">{{ $t('sale') }}</label>
              </div>
            </div>
          </label>
          <div class="row col-md-12 my-2">
            <!-- category -->
            <div class="col-md-6">
              <label class="form-label fw-bold w-100">{{ $t('category') }}
                <v-select v-model="form.category" :options="categories" label="name" class="my-1"
                          :class="{ 'is-invalid': form.errors.has('category') }"
                >
                  <template #search="{attributes, events}">
                    <input
                      class="vs__search"
                      :required="!form.category"
                      v-bind="attributes"
                      v-on="events"
                    >
                  </template>
                </v-select>
              </label>
              <has-error :form="form" field="category" />
            </div>

            <div class="col-md-6" />
          </div>
          <div class="row col-md-12 my-2">
            <!-- country -->
            <div class="col-md-6">
              <label class="form-label fw-bold w-100">{{ $t('country') }}
                <v-select v-model="form.country" :options="countries" label="name" class="my-1"
                          :class="{ 'is-invalid': form.errors.has('country') }"
                >  <template #search="{attributes, events}">
                  <input
                    class="vs__search"
                    :required="!form.country"
                    v-bind="attributes"
                    v-on="events"
                  >
                </template>
                </v-select>
              </label>
              <has-error :form="form" field="country" />
            </div>

            <!-- city -->
            <div class="col-md-6">
              <label class="form-label fw-bold w-100">{{ $t('post code, place, canton') }}
                <v-select v-model="form.postCode" :disabled="!form.country" label="name" :filterable="false"
                          class="my-1"
                          :options="postCodes"
                          :class="{ 'is-invalid': form.errors.has('postCode') }"
                          @search="onSearch"
                >
                  <template slot="no-options">
                    Please enter 2 or more characters
                  </template>
                  <template #search="{attributes, events}">
                    <input
                      class="vs__search"
                      :required="!form.postCode"
                      v-bind="attributes"
                      v-on="events"
                    >
                  </template>
                </v-select>
              </label>
              <has-error :form="form" field="postCode" />
            </div>
          </div>

          <!-- Submit Button -->
          <v-button :loading="form.busy" class="btn btn-primary px-4 my-3 me-md-2">
            {{ $t('continue') }}
          </v-button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>

import Form from 'vform'
import axios from 'axios'
import i18n from '../../plugins/i18n'

export default {
  // middleware: 'guest',
  scrollToTop: true,
  metaInfo () {
    return { title: this.$t('write a property') }
  },

  data: () => ({
    form: new Form({
      type: 'rent',
      category: '',
      country: '',
      postCode: '',
      advertisingType: ''
    }),
    categories: [],
    countries: [],
    postCodes: [],
    advertisingTypes: [
      { value: 'type_tenant', name: i18n.t('type_tenant') },
      { value: 'type_owner', name: i18n.t('type_owner') },
      { value: 'type_agent', name: i18n.t('type_agent') },
      { value: 'type_other', name: i18n.t('type_other') }
    ]
  }),

  created () {
    this.getCategories()
    this.getCountries()
  },

  methods: {
    async createProperty () {
      // Submit the form.
      const { data } = await this.form.post('properties/insertion/create')

      // Redirect details.
      if (data.status) {
        this.redirect(data.data.reference)
      }
    },

    async getCategories () {
      const { data } = await axios.post('details/categories')
      if (data.status) {
        this.categories = data.data
      }
    },

    async getCountries () {
      const { data } = await axios.post('details/countries')
      if (data.status) {
        this.countries = data.data
      }
    },

    onSearch (search, loading) {
      if (search.length > 1) {
        loading(true)
        this.search(loading, search, this)
      }
    },

    search: (loading, search, vm) => {
      axios.post('details/cities', { countryId: vm.form.country.id, search: search }).then((res) => res.data)
        .then((res) => {
          vm.postCodes = res.data
        })
        .catch((e) => {
          console.log(e)
        })
        .finally(() => {
          loading(false)
        })
    },

    redirect (ref) {
      const intendedUrl = '/properties/' + ref + '/details'

      this.$router.push({ path: intendedUrl })
    }
  }
}
</script>

<style lang="scss">
@import '~vue-select/dist/vue-select.css';
</style>
