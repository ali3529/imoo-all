<template>
  <div v-if="property" class="row d-flex align-items-center align-content-center justify-content-center my-5">
    <div class="col-xs-12 col-md-8 col-xl-8 col-lg-10 p-4 pb-0 pt-lg-5 align-items-center">
      <div class="text-center border my-3">
        <h4 class="fw-bolder rounded mt-2">
          {{ getPropertyTitle() }}
        </h4>
      </div>
      <form class="w-100" enctype="multipart/form-data" @submit.prevent="updateProperty">
        <div class="row g-3">
          <h4 class="mb-3">
            {{ $t('description') }}
          </h4>
          <div class="col-sm-12">
            <label for="name" class="form-label">{{ $t('title') }}<span class="small text-danger m-1">*</span></label>
            <input id="name" v-model="form.name"
                   :class="{ 'is-invalid': form.errors.has('name') }"
                   class="form-control" type="text"
                   name="name" placeholder="" spellcheck="false" data-ms-editor="true"
                   :disabled="canEditProperty()"
            >
            <has-error :form="form" field="name" />
          </div>
          <div class="col-sm-12">
            <label for="description" class="form-label">{{ $t('description') }}<span
              class="small text-danger m-1"
            >*</span></label>
            <textarea id="description" v-model="form.description"
                      :class="{ 'is-invalid': form.errors.has('description') }"
                      class="form-control" type="number"
                      name="description" placeholder="" spellcheck="false" data-ms-editor="true"
                      :disabled="canEditProperty()"
            />
            <has-error :form="form" field="description" />
          </div>
          <div class="col-sm-12">
            <label for="content" class="form-label">{{ $t('content') }}<span
              class="small text-danger m-1"
            >*</span></label>
            <textarea id="content" v-model="form.content"
                      :class="{ 'is-invalid': form.errors.has('content') }"
                      class="form-control" type="text"
                      style="min-height: 155px"
                      name="content" placeholder="" spellcheck="false" data-ms-editor="true"
                      :disabled="canEditProperty()"
            />
            <has-error :form="form" field="content" />
          </div>

          <h4 class="mb-3">
            {{ $t('main features') }}
          </h4>
          <div class="col-sm-6">
            <label for="number_bedroom" class="form-label">{{ $t('number_bedroom') }}<span
              class="small text-danger m-1"
            >*</span></label>
            <input id="number_bedroom" v-model="form.number_bedroom"
                   :class="{ 'is-invalid': form.errors.has('number_bedroom') }"
                   class="form-control" type="number"
                   name="number_bedroom" placeholder="" spellcheck="false" data-ms-editor="true"
                   required
                   :disabled="canEditProperty()"
            >
            <has-error :form="form" field="number_bedroom" />
          </div>
          <div class="col-sm-6">
            <label for="square" class="form-label">{{ $t('square') }}<span
              class="small text-danger m-1"
            >*</span></label>
            <div class="position-relative">
              <input id="square" v-model="form.square" :class="{ 'is-invalid': form.errors.has('square') }"
                     class="form-control" type="number"
                     required
                     name="square" placeholder="" spellcheck="false" data-ms-editor="true"
                     :disabled="canEditProperty()"
              >
              <span class="position-absolute end-0 top-0 bottom-0 m-1 ms-auto">m<sup>2</sup></span>
            </div>
            <has-error :form="form" field="square" />
          </div>
          <GoogleMap :location="form.location" />

          <h4 class="mb-3">
            {{ $t('details') }}
          </h4>
          <div class="col-sm-6">
            <label for="available_date" class="form-label">{{ $t('available_date') }}</label>
            <input id="available_date" v-model="form.available_date"
                   :class="{ 'is-invalid': form.errors.has('available_date') }"
                   class="form-control" type="text"
                   name="available_date" placeholder="" spellcheck="false" data-ms-editor="true"
                   :disabled="canEditProperty()"
            >
            <has-error :form="form" field="available_date" />
          </div>
          <div class="col-sm-6">
            <label for="number_floor" class="form-label">{{ $t('number_floor') }}<span
              class="small text-danger m-1"
            >*</span></label>
            <input id="number_floor" v-model="form.number_floor"
                   :class="{ 'is-invalid': form.errors.has('number_floor') }"
                   class="form-control" type="number" required
                   name="number_floor" placeholder="" spellcheck="false" data-ms-editor="true"
                   :disabled="canEditProperty()"
            >
            <has-error :form="form" field="number_floor" />
          </div>
          <div class="col-sm-6">
            <label for="construction_year" class="form-label">{{ $t('construction_year') }}<span
              class="small text-danger m-1"
            >*</span></label>
            <input id="construction_year" v-model="form.construction_year"
                   :class="{ 'is-invalid': form.errors.has('construction_year') }"
                   class="form-control" type="number" min="1111" max="2999" required
                   name="construction_year" placeholder="" spellcheck="false" data-ms-editor="true"
                   :disabled="canEditProperty()"
            >
            <has-error :form="form" field="construction_year" />
          </div>
          <div class="col-sm-6">
            <label for="last_reconstruction" class="form-label">{{ $t('last_reconstruction') }}</label>
            <input id="last_reconstruction" v-model="form.last_reconstruction"
                   :class="{ 'is-invalid': form.errors.has('last_reconstruction') }"
                   class="form-control" type="number" min="1111" max="2999"
                   name="last_reconstruction" placeholder="" spellcheck="false" data-ms-editor="true"
                   :disabled="canEditProperty()"
            >
            <has-error :form="form" field="last_reconstruction" />
          </div>
          <div class="col-sm-6">
            <label for="last_renovation" class="form-label">{{ $t('last_renovation') }}</label>
            <input id="last_renovation" v-model="form.last_renovation"
                   :class="{ 'is-invalid': form.errors.has('last_renovation') }"
                   class="form-control" type="number" min="1111" max="2999"
                   name="last_renovation" placeholder="" spellcheck="false" data-ms-editor="true"
                   :disabled="canEditProperty()"
            >
            <has-error :form="form" field="last_renovation" />
          </div>
          <div class="col-sm-6">
            <label for="number_bathroom" class="form-label">{{ $t('number_bathroom') }}<span
              class="small text-danger m-1"
            >*</span></label>
            <input id="number_bathroom" v-model="form.number_bathroom"
                   :class="{ 'is-invalid': form.errors.has('number_bathroom') }"
                   class="form-control" type="number" required
                   name="number_bathroom" placeholder="" spellcheck="false" data-ms-editor="true"
                   :disabled="canEditProperty()"
            >
            <has-error :form="form" field="number_bathroom" />
          </div>
          <div class="col-sm-6">
            <label for="number_wc" class="form-label">{{ $t('number_wc') }}<span class="small text-danger m-1">*</span></label>
            <input id="number_wc" v-model="form.number_wc"
                   :class="{ 'is-invalid': form.errors.has('number_wc') }"
                   class="form-control" type="number" required
                   name="number_wc" placeholder="" spellcheck="false" data-ms-editor="true"
                   :disabled="canEditProperty()"
            >
            <has-error :form="form" field="number_wc" />
          </div>

          <h4 class="mb-3">
            {{ $t('costs') }}
          </h4>
          <div class="col-sm-4">
            <label v-if="hasRent()" for="price" class="form-label">{{ $t('gross rent') }}</label>
            <label v-else for="price" class="form-label">{{ $t('price') }}</label>
            <div class="position-relative">
              <input id="price" v-model="form.price"
                     :class="{ 'is-invalid': form.errors.has('price') }"
                     class="form-control" type="number"
                     name="price" placeholder="" spellcheck="false" data-ms-editor="true"
                     :disabled="canEditProperty()"
              >
              <span class="position-absolute end-0 top-0 bottom-0 m-1 ms-auto">CHF</span>
            </div>
            <has-error :form="form" field="price" />
          </div>
          <div v-if="hasRent()" class="col-sm-4">
            <label for="additional_costs" class="form-label">{{ $t('additional_costs') }}</label>
            <input id="additional_costs" v-model="form.additional_costs"
                   :class="{ 'is-invalid': form.errors.has('additional_costs') }"
                   class="form-control" type="number"
                   name="additional_costs" placeholder="" spellcheck="false" data-ms-editor="true"
                   :disabled="canEditProperty()"
            >
            <has-error :form="form" field="additional_costs" />
          </div>
          <div v-if="hasRent()" class="col-sm-4">
            <label for="net_rent" class="form-label">{{ $t('net_rent') }}</label>
            <input id="net_rent" v-model="form.net_rent"
                   :class="{ 'is-invalid': form.errors.has('net_rent') }"
                   class="form-control" type="number"
                   name="net_rent" placeholder="" spellcheck="false" data-ms-editor="true"
                   :disabled="canEditProperty()"
            >
            <has-error :form="form" field="net_rent" />
          </div>

          <h4 class="mb-3">
            {{ $t('properties - equipment and features') }}
          </h4>
          <div class="col-sm-12">
            <div class="row">
              <div v-for="feature in features" :key="feature.id" class="col-sm-5">
                <label>
                  <input :id="feature.id" v-model="form.features" :reduce="f => f.id" type="checkbox"
                         :value="feature.id" :name="feature.id"
                         :disabled="canEditProperty()"
                  >
                  {{ feature.name }}
                </label>
              </div>
            </div>
          </div>

          <h4 class="mb-3">
            {{ $t('location and surroundings') }}
          </h4>
          <div class="col-sm-12">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>
                    {{ $t('facility') }}
                  </th>
                  <th>
                    {{ $t('distance') }}
                  </th>
                  <th style="width: 130px;" />
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, i) in form.facilities">
                  <td>
                    <v-select v-model="row.facility_id" :options="facilities" name="facility_id" label="name"
                              :reduce="f => f.id"
                              class="my-1"
                              :disabled="canEditProperty()"
                    />
                  </td>
                  <td>
                    <b-input v-model="row.distance" :placeholder="$t('distance')" type="number" step="0.01" name="distance"
                             class="form-control" spellcheck="false" data-ms-editor="true"
                             :disabled="canEditProperty()"
                    />
                    <b-input v-model="row.reference_type" type="text" hidden name="reference_type"
                             class="form-control" spellcheck="false" data-ms-editor="true"
                             :disabled="canEditProperty()"
                    />
                  </td>
                  <td>
                    <div v-if="!canEditProperty()" class="btn btn-danger btn-xs"
                         @click="removeRow(i)"
                    >
                      {{ $t('remove') }}
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>

            <b-button type="button" class="btn btn-success mt-2" @click="addRow">
              {{ $t('add') }}
            </b-button>
          </div>

          <h4 class="mb-3">
            {{ $t('photos and videos') }}
          </h4>
          <div class="col-sm-12">
            <file-management ref="fileManager" />
            <has-error :form="form" field="pFiles.0" />
            <has-error :form="form" field="pImages.0" />
            <has-error :form="form" field="pVideos.0" />
          </div>

          <h4 class="mb-3">
            {{ $t('contact') }}
          </h4>
          <div class="col-sm-6">
            <label for="contact_name" class="form-label">{{ $t('contact_name') }}<span
              class="small text-danger m-1"
            >*</span></label>
            <input id="contact_name" v-model="form.contact_name"
                   :class="{ 'is-invalid': form.errors.has('contact_name') }"
                   class="form-control" type="text"
                   name="contact_name" placeholder="" spellcheck="false" data-ms-editor="true"
                   required
                   :disabled="canEditProperty()"
            >
          </div>
          <div class="col-sm-6">
            <label for="contact_phone_number" class="form-label">{{ $t('contact_phone_number') }}<span
              class="small text-danger m-1"
            >*</span></label>
            <input id="contact_phone_number" v-model="form.contact_phone_number"
                   :class="{ 'is-invalid': form.errors.has('contact_phone_number') }"
                   class="form-control" type="tel"
                   name="contact_phone_number" placeholder="" spellcheck="false" data-ms-editor="true" required
                   :disabled="canEditProperty()"
            >
          </div>

          <h4 class="mb-3">
            {{ $t('publish') }}
          </h4>
          <div class="col-sm-6">
            <label for="status" class="form-label">{{ $t('status') }}<span
              class="small text-danger m-1"
            >*</span></label>
            <v-select id="status" v-model="form.status" :reduce="status => status.name" :options="propertyStatus"
                      label="value"
            />

            <has-error :form="form" field="status" />
          </div>
        </div>

        <hr class="my-4">

        <!-- Submit Button -->
        <v-button :loading="form.busy" class="btn btn-primary px-4 my-3 me-md-2">
          {{ property.payed ? $t('update') : $t('Next and Choose Package') }}
        </v-button>

        <p v-if="errorMessage" class="text-danger">
          {{ errorMessage }}
        </p>
      </form>
    </div>
  </div>
</template>

<script>

import Form from 'vform'
import axios from 'axios'
import i18n from '~/plugins/i18n'
import GoogleMap from '~/components/GoogleMap.vue'
import FileManagement from '../../components/FileManagement'

export default {
  components: {
    FileManagement,
    GoogleMap
  },
  middleware: 'auth',
  scrollToTop: true,

  metaInfo () {
    return { title: this.$t('write a property') }
  },

  data: () => ({
    property: null,
    mLocation: '',
    form: new Form({
      id: '',
      type: '',
      number_bedroom: '',
      available_date: '',
      last_reconstruction: '',
      last_renovation: '',
      number_bathroom: '',
      number_wc: '',
      price_unit: '',
      additional_costs: '',
      net_rent: '',
      price: '',
      name: '',
      content: '',
      description: '',
      construction_year: '',
      number_floor: '',
      square: '',
      location: '',
      features: [],
      facilities: [],
      status: '',
      consent_protection: true,
      package: 0,
      contact_name: '',
      contact_phone_number: '',
      data1: '',
      images: [],
      videos: [],
      pImages: [],
      pVideos: [],
      pFiles: []
    }),
    features: [],
    facilities: [],
    propertyStatus: [],
    // You can store all your files here
    attachments: [],
    errorMessage: ''
  }),

  created () {
    this.getFeatures()
    this.getFacilities()
    this.getPropertyStatus()
    this.getProperty()
  },

  methods: {
    async updateProperty () {
      // Submit the form.
      this.errorMessage = ''
      if (!document.getElementById('auto_complete_map').value) {
        const field = document.getElementById('auto_complete_map')
        field.scrollIntoView()
        field.focus()
        return
      }
      this.form.location = document.getElementById('auto_complete_map').value
      this.form.facilities = this.form.facilities.filter(facility => facility.distance > 0).map((item) => {
        item.reference_id = this.property.id
        return item
      })

      try {
        const { data } = await this.form.post('properties/insertion/update')
        if (data.status) {
          if (this.property.payed) {
            const intendedUrl = '/properties/my'
            this.$router.push({ path: intendedUrl })
          } else {
            this.prepareFields()
            this.form.post('properties/insertion/updateFiles').then((res) => res.data).then((res) => {
              if (data.status) {
                const intendedUrl = '/properties/' + this.property.reference + '/details/step2'
                this.$router.push({ path: intendedUrl })
              } else {
                this.errorMessage = data.message
              }
            }).catch((err) => {
              console.log(err)
              this.form.pImages = []
              this.form.pVideos = []
              this.form.pFiles = []
              this.errorMessage = `${err} ${err.response.statusText}`
            })
          }
        } else {
          this.errorMessage = data.message
        // const el = document.querySelector(".v-messages.error--text:first-of-type");
        // el.scrollIntoView();
        }
      } catch (e) {
        const ce = e.response.data
        const keys = Object.keys(ce.errors)
        if (keys.length > 0) {
          // const selector = 'input[name=' + keys[0] + ']'
          // const field = document.querySelector(selector)
          const field = document.getElementById(keys[0])
          field.scrollIntoView()
        }

        console.log('error ===>' + JSON.stringify(ce))
      }
    },

    async getProperty () {
      const { data } = await axios.post('properties/insertion/details', { reference: this.$route.params.reference })
      this.property = data.data

      if (data.data.facilities.length === 0) {
        data.data.facilities.push({
          facility_id: 1,
          distance: '',
          reference_type: 'Botble\\RealEstate\\Models\\Property'
        })
      }
      // Fill the form with user data.
      this.form.keys().forEach(key => {
        if (key === 'features') {
          this.form[key] = this.property[key].map(x => x.id)
        } else if (key === 'facilities') {
          try {
            this.form[key] = this.property[key].map(x => x.hasOwnProperty('pivot') ? x.pivot : x)
          } catch (e) {
            this.form[key] = this.property[key]
          }
        } else this.form[key] = this.property[key]
      })
      this.attachments = this.property.videos.concat(this.property.images)

      // debugger
      // document.getElementById('auto_complete_map').value = (data.data.location)
    },

    async getFeatures () {
      const { data } = await axios.post('details/features')
      this.features = data.data
    },

    async getFacilities () {
      const { data } = await axios.post('details/facilities')
      this.facilities = data.data
    },

    async getPropertyStatus () {
      const { data } = await axios.post('details/propertyStatus')
      this.propertyStatus = data.data
    },

    getPropertyTitle () {
      if (this.property) {
        const type = i18n.t(this.property.type)
        const categoryName = this.property.category.name
        const cityName = this.property.city.name
        return `${type} :: ${categoryName} :: ${cityName}`
      }
    },

    addRow: function () {
      if (this.property.payed) return
      try {
        this.form.facilities.push({
          facility_id: 1,
          distance: 0,
          reference_type: 'Botble\\RealEstate\\Models\\Property'
        })
      } catch (e) {
        console.log(e)
      }
    },

    removeRow: function (index) {
      if (this.property.payed) return
      this.form.facilities.splice(index, 1)
    },

    hasRent: function () {
      return this.form.type === 'rent'
    },

    prepareFields () {
      this.form.images = []
      this.form.videos = []
      const images = []
      const videos = []
      const files = []
      for (let i = this.attachments.length - 1; i >= 0; i--) {
        if (this.attachments[i] instanceof File) {
          if (this.$refs.fileManager.isImage(this.attachments[i].type)) {
            images.push(this.attachments[i])
          } else if (this.$refs.fileManager.isVideo(this.attachments[i].type)) {
            videos.push(this.attachments[i])
          } else if (this.$refs.fileManager.isOtherFile(this.attachments[i].type)) {
            files.push(this.attachments[i])
          }
        } else {
          const ext = this.$refs.fileManager.getExt(this.attachments[i])
          if (this.$refs.fileManager.isImage(ext)) {
            this.form.images.push(this.attachments[i])
          } else {
            this.form.videos.push(this.attachments[i])
          }
        }
      }
      this.form.pImages = images
      this.form.pVideos = videos
      this.form.pFiles = files
    },

    canEditProperty () {
      return this.property.payed === 1
    }
  }
}
</script>

<style lang="scss">
@import '~vue-select/dist/vue-select.css';
</style>
