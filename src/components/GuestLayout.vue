<template>
  <v-app>
    <v-locale-provider rtl>
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
import GuestNavbar from "./GuestNavbar.vue";
import GuestFooter from "./GuestFooter.vue";
</script>

<style scoped>
/* Any common layout styling can go here */
</style>
