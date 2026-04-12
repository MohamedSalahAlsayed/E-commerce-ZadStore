<template>
  <v-app>
    <v-locale-provider :rtl="isRtl">
      <!-- Guest Navbar -->
      <GuestNavbar />

      <!-- Main Content mapped to Router -->
      <v-main>
        <router-view />
      </v-main>

      <!-- Guest Footer -->
      <GuestFooter />
    </v-locale-provider>
  </v-app>
</template>

<script setup>
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import GuestNavbar from "./GuestNavbar.vue";
import GuestFooter from "./GuestFooter.vue";

const { locale } = useI18n();
const isRtl = computed(() => locale.value === "ar");
</script>

<style scoped>
/* Any common layout styling can go here */
</style>
