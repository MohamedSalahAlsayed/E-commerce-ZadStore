<template>
  <div class="main-website-layout">
    <DroWer />

    <NavApp v-if="!$route.meta.hideNavFooter" />

    <v-main style="padding-top: 100px; min-height: 100vh">
      <slot></slot>
    </v-main>

    <FooterApp v-if="!$route.meta.hideNavFooter" />
  </div>
</template>

<script>
import NavApp from "./NavApp.vue";
import FooterApp from "./FooterApp.vue";
import DroWer from "./DroWer.vue";

export default {
  components: {
    NavApp,
    FooterApp,
    DroWer,
  },
};
</script>
