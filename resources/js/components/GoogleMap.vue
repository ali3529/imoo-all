<template>
  <div no-body class="mb-1 w-100">
    <div class="body-bg shadow-none" id="test2">
      <div
        :class="{
          'is-invalid':
            $parent.form.errors.has('auto_complete_map') ||
            $parent.form.errors.has('location'),
        }"
        class="d-flex flex-row justify-content-center border border-1 rounded border-dark"
      >
        <h4 block v-b-toggle.accordion-2 variant="info">Location</h4>
      </div>
    </div>
    <b-collapse
      class="navbar-fixed-top"
      id="accordion-2"
      accordion="my-accordion"
    >
      <div class="d-flex flex-row gap-2 justify-content-between mt-2">
        <div class="col-sm-6">
          <label for="latitude" class="form-label"
            >{{ $t("latitude")
            }}<span class="small text-danger m-1">*</span></label
          >
          <input
            id="latitude"
            :value="latitude"
            class="form-control bg-transparent"
            name="latitude"
            placeholder=""
            spellcheck="false"
            data-ms-editor="true"
            :disabled="true"
          />
          <has-error :form="form" field="latitude" />
        </div>
        <div class="col-sm-6">
          <label for="longitude" class="form-label">{{
            $t("longitude")
          }}</label>
          <input
            id="longitude"
            class="form-control bg-transparent"
            :value="longitude"
            name="longitude"
            placeholder=""
            spellcheck="false"
            data-ms-editor="true"
            :disabled="true"
          />
          <has-error :form="form" field="longitude" />
        </div>
      </div>
      <label class="form-label mt-2"
        >{{ $t("location") }}<span class="small text-danger">*</span></label
      >
      <GmapAutocomplete
        id="auto_complete_map"
        ref="location"
        v-validate="'required|min:5'"
        class="form-control"
        name="location"
        :value="location"
        :placeholder="$t('enter a location')"
        aria-required="true"
        :disabled="$parent.canEditProperty()"
        @place_changed="setPlace"
        @keydown.enter.prevent
      />
       <has-error :form="form" field="auto_complete_map" />
      <GmapMap
        ref="map"
        class="my-3"
        :center="center"
        :zoom="zoom"
        style="width: 100%; height: 300px"
        @click="clickMap"
      >
        <GmapMarker
          v-for="(m, index) in markers"
          :key="index"
          :position="m.position"
          @click="center = m.position"
        />
      </GmapMap>

      <!-- <div class="d-flex flex-row justify-content-end m-2">
          <b-button class="rounded-lg px-4 text-lg" block v-b-toggle.accordion-3 variant="info">next</b-button>
     </div> -->

      <div class="d-flex flex-row justify-content-end gap-3 m-2 mt-3">
        <b-button
          class="rounded-lg px-4 col-sm-2 text-white text-lg border border-dark"
          block
          v-b-toggle.accordion-1
          variant="dark"
          >previous</b-button
        >
        <b-button
          class="rounded-lg px-6 col-sm-2 text-lg border border-dark"
          block
          v-b-toggle.accordion-3
          variant="light"
          >Next Step</b-button
        >
      </div>
    </b-collapse>
  </div>
</template>

<script>
import Autocomplete from "vue2-google-maps/dist/components/autocomplete";

export default {
  name: "GoogleMap",
  props: {
    location: { type: String, default: "" },
    latitude: { type: String, default: "" },
    longitude: { type: String, default: "" },
  },

  data() {
    return {
      center: {
        lat: 45.508,
        lng: -73.587,
      },
      currentPlace: null,
      markers: [],
      places: [],
      zoom: 12,
    };
  },
  mounted() {
    this.geolocate();
  },
  methods: {
    setPlace(place) {
      this.markers.splice(0, this.markers.length);
      this.places.splice(0, this.places.length);
      this.currentPlace = place;
      this.updateLocation(place.formatted_address);

      this.addMarker();
    },

    addMarker() {
      if (this.currentPlace) {
        const marker = {
          lat: this.currentPlace.geometry.location.lat(),
          lng: this.currentPlace.geometry.location.lng(),
        };

        this.markers.push({ position: marker });
        this.places.push(this.currentPlace);
        this.center = marker;
        this.zoom = 17;
        this.currentPlace = null;
      }
    },

    clickMap(location) {
      const marker = location.latLng;
      // this.markers.clear()
      this.markers.splice(0, this.markers.length);
      this.places.splice(0, this.places.length);
      this.markers.push({ position: marker });
      // this.places.push(this.currentPlace)
      // this.center = marker
      // this.zoom = 17
      this.currentPlace = null;
      const geocoder = new google.maps.Geocoder();
      geocoder
        .geocode({ location: location.latLng })
        .then((response) => {
          if (response.results[0]) {
            this.updateLocation(response.results[0].formatted_address);
            this.getLoc(location.latLng);
          } else {
            window.alert("No results found");
          }
        })
        .catch((e) => window.alert("Geocoder failed due to: " + e));
    },

    geolocate: function () {
      const oldLocation = this.location;
      if (oldLocation.length > 0) {
        const _this = this;
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ address: oldLocation }, function (results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            // const lat = results[0].geometry.location.lat()
            // const lng = results[0].geometry.location.lng()
            // const placeName = results[0].address_components[0].long_name
            if (results.length > 0) {
              _this.setPlace(results[0]);
            }
          }
        });
      } else {
        navigator.geolocation.getCurrentPosition((position) => {
          this.center = {
            lat: position.coords.latitude,
            lng: position.coords.longitude,
          };
        });
      }
    },

    updateLocation: function (newLocation) {
      this.location = newLocation;
      this.$emit("eventname", newLocation);
    },
    getLoc: function (location) {
      // this.location =  this.markers
      this.longitude = location.lng();
      this.latitude = location.lat();
      this.$emit("getlog", location);
    },
  },
};
</script>
