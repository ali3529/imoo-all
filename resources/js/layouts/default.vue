<template>
  <div>
    <main role="main" class="main-layout">
      <navbar />
      <div class="container-lg mt-4">
        <child />
      </div>
    </main>
    <footer class="footer text-center mx-5 my-2">
      <div class="container">
        <span class="text-muted">Copyright © {{ new Date().getFullYear() }} ImmoAll Swiss, Inc</span>
      </div>
    </footer>
  </div>
</template>

<script>
import Navbar from '~/components/Navbar'

export default {
  name: 'MainLayout',

  components: {
    Navbar
  }
}
</script>
