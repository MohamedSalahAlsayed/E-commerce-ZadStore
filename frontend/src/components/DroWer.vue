<template>
  <v-navigation-drawer
    v-model="drawer"
    location="end"
    width="380"
    temporary
    elevation="10"
    class="premium-drawer"
    :dir="locale === 'ar' ? 'rtl' : 'ltr'"
  >
    <InCart @closeDrawer="drawer = false" />
  </v-navigation-drawer>
</template>

<script setup>
import { ref, onMounted, inject } from "vue";
import { useI18n } from "vue-i18n";
import InCart from "../components/InCart.vue";

const { locale } = useI18n();
const emitter = inject("emitter");
const drawer = ref(false);

onMounted(() => {
  emitter.on("toggleCart", () => {
    drawer.value = !drawer.value;
  });
});
</script>

<style>
/* Remove default scrolling to allow internal Flexbox scroll in InCart.vue */
.premium-drawer .v-navigation-drawer__content {
  overflow: hidden !important;
  display: flex !important;
  flex-direction: column !important;
}
</style>
