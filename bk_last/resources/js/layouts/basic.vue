<template>
  <div class="basic-layout m-0">
    <div class="wrapper">
      <child />
    </div>
  </div>
</template>

<script>
export default {
  name: 'BasicLayout'
}
</script>

<style lang="scss">
.basic-layout {
  overflow: hidden;
  color: #636b6f;
  height: 100vh;
  font-weight: 100;
  position: relative;
  background-image: url('/images/bg-index.jpg');
  background-repeat: no-repeat;
  background-attachment: fixed;
  background-size: cover;
  background-position: center;
  .links > a {
    color: #636b6f;
    padding: 0 25px;
    font-size: 0.9rem;
    font-weight: 600;
    letter-spacing: .1rem;
    text-decoration: none;
    text-transform: uppercase;
  }
}
.wrapper{
  background: #0d1419e6;
}
</style>
