<template>
  <button :type="nativeType" :disabled="loading" :class="{
    [`btn-${type}`]: true,
    'btn-block': block,
    'disabled': block,
    'btn-lg': large,
    'btn-loading': loading
  }" class="btn"
  >
    <slot />
  </button>
</template>

<script>
export default {
  name: 'VButton',

  props: {
    type: {
      type: String,
      default: 'primary'
    },

    nativeType: {
      type: String,
      default: 'submit'
    },

    linkType: {
      type: String,
      default: 'link'
    },

    loading: {
      type: Boolean,
      default: false
    },

    block: {
      type: Boolean,
      default: false
    },

    large: {
      type: Boolean,
      default: false
    }
  }
}
</script>
