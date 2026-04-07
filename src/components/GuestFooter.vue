<template>
  <v-footer class="bg-grey-darken-4 text-white pb-3 pt-8">
    <v-container>
      <v-row class="mb-4">
        <!-- Brand Info -->
        <v-col cols="12" md="4" class="text-center text-md-start mb-4">
          <h2 class="text-h5 font-weight-black mb-3">
            {{ settingsStore.storeName || "زاّد ستور" }}
          </h2>
          <p class="text-grey-lighten-1 text-body-2 lh-tight pe-md-10">
            أفضل تجربة تسوق إلكترونية. منتجات مضمونة، عروض هائلة، وتوصيل سريع
            لكل مكان. نسعى دائماً لرضاكم.
          </p>
        </v-col>

        <!-- Quick Links -->
        <v-col cols="12" sm="6" md="4" class="text-center text-md-start mb-4">
          <h4
            class="text-subtitle-1 font-weight-bold mb-4 text-primary-lighten-2"
          >
            روابط هامة
          </h4>
          <div class="d-flex flex-column gap-2">
            <router-link to="/BlogPage" class="footer-link"
              >المدونة</router-link
            >
            <router-link to="/AboutPage" class="footer-link"
              >عن الموقع</router-link
            >
            <router-link to="/ContactPage" class="footer-link"
              >تواصل معنا</router-link
            >
          </div>
        </v-col>

        <!-- Social Media & Contact -->
        <v-col cols="12" sm="6" md="4" class="text-center text-md-start mb-4">
          <h4
            class="text-subtitle-1 font-weight-bold mb-4 text-primary-lighten-2"
          >
            تواصل معنا
          </h4>
          <div
            class="d-flex align-center justify-center justify-md-start mb-3 text-grey-lighten-1"
          >
            <v-icon size="small" class="me-2">mdi-email-outline</v-icon>
            {{ settingsStore.email || "info@store.com" }}
          </div>
          <div
            class="d-flex align-center justify-center justify-md-start mb-4 text-grey-lighten-1"
          >
            <v-icon size="small" class="me-2">mdi-phone-outline</v-icon>
            {{ settingsStore.phone || "+20 123 456 7890" }}
          </div>

          <div class="d-flex justify-center justify-md-start gap-2">
            <v-btn
              icon
              size="small"
              variant="tonal"
              v-if="settingsStore.facebook"
              :href="settingsStore.facebook"
              target="_blank"
              color="blue"
              ><v-icon>mdi-facebook</v-icon></v-btn
            >
            <v-btn
              icon
              size="small"
              variant="tonal"
              v-if="settingsStore.twitter"
              :href="settingsStore.twitter"
              target="_blank"
              color="light-blue"
              ><v-icon>mdi-twitter</v-icon></v-btn
            >
            <v-btn
              icon
              size="small"
              variant="tonal"
              v-if="settingsStore.instagram"
              :href="settingsStore.instagram"
              target="_blank"
              color="pink"
              ><v-icon>mdi-instagram</v-icon></v-btn
            >
            <v-btn
              icon
              size="small"
              variant="tonal"
              v-if="settingsStore.tiktok"
              :href="settingsStore.tiktok"
              target="_blank"
              color="white"
              ><v-icon>mdi-music-note</v-icon></v-btn
            >
          </div>
        </v-col>
      </v-row>

      <v-divider class="my-4 border-opacity-25" color="white"></v-divider>

      <v-row>
        <v-col cols="12" class="text-center text-caption text-grey">
          جميع الحقوق محفوظة {{ new Date().getFullYear() }} ©
          {{ settingsStore.storeName || "ZadStore" }}
        </v-col>
      </v-row>
    </v-container>
  </v-footer>
</template>

<script setup>
import { useSettingsStore } from "@/store/Settings";

const settingsStore = useSettingsStore();
</script>

<style scoped>
.lh-tight {
  line-height: 1.6;
}
.footer-link {
  color: #bdbdbd;
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.3s;
}
.footer-link:hover {
  color: #fff;
  padding-right: 4px; /* subtle shift */
}
.gap-2 {
  gap: 8px;
}
</style>
