<template>
  <div class="col-sm-12 d-grid">
    <label class="form-label">{{ $t('location') }}<span class="small text-danger">*</span></label>
    <GmapAutocomplete id="auto_complete_map" ref="location" v-validate="'required|min:5'"
                      class="form-control"
                      name="location"
                      :value="location"
                      :placeholder="$t('enter a location')"
                      aria-required="true" :disabled="$parent.canEditProperty()"
                      @place_changed="setPlace"
                      @keydown.enter.prevent
    />
    <GmapMap
      ref="map"
      class="my-3"
      :center="center"
      :zoom="zoom"
      style="width:100%;  height: 300px;"
    >
      <GmapMarker
        v-for="(m, index) in markers"
        :key="index"
        :position="m.position"
        @click="center=m.position"
      />
    </gmapmap>
  </div>
</template>

<script>

import Autocomplete from 'vue2-google-maps/dist/components/autocomplete'

export default {
  name: 'GoogleMap',
  props: {
    location: { type: String, default: '' }
  },

  data () {
    return {
      center: {
        lat: 45.508,
        lng: -73.587
      },
      currentPlace: null,
      markers: [],
      places: [],
      zoom: 12
    }
  },
  mounted () {
    this.geolocate()
  },
  methods: {
    setPlace (place) {
      this.markers.splice(0, this.markers.length)
      this.places.splice(0, this.places.length)
      this.currentPlace = place
      this.location = place.formatted_address
      this.addMarker()
    },

    addMarker () {
      if (this.currentPlace) {
        const marker = {
          lat: this.currentPlace.geometry.location.lat(),
          lng: this.currentPlace.geometry.location.lng()
        }
        this.markers.push({ position: marker })
        this.places.push(this.currentPlace)
        this.center = marker
        this.zoom = 17
        this.currentPlace = null
      }
    },

    geolocate: function () {
      const oldLocation = this.location
      if (oldLocation.length > 0) {
        const _this = this
        const geocoder = new google.maps.Geocoder()
        geocoder.geocode({ address: oldLocation }, function (results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            // const lat = results[0].geometry.location.lat()
            // const lng = results[0].geometry.location.lng()
            // const placeName = results[0].address_components[0].long_name
            if (results.length > 0) { _this.setPlace(results[0]) }
          }
        })
      } else {
        navigator.geolocation.getCurrentPosition(position => {
          this.center = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          }
        })
      }
    }
  }
}
</script>
