<template>
  <v-app>
    <!-- Maintenance Mode Overlay -->
    <v-overlay
      v-model="showMaintenance"
      persistent
      class="align-center justify-center text-center bg-white"
      style="opacity: 1; z-index: 9999"
    >
      <v-container>
        <v-img
          :src="
            settingsStore.logoPreview ||
            'https://cdn-icons-png.flaticon.com/512/2596/2596956.png'
          "
          width="150"
          class="mx-auto mb-6"
        ></v-img>
        <h1 class="text-h3 font-weight-black mb-4 text-primary">
          {{ $t("maintenance.title") }}
        </h1>
        <p class="text-h6 text-grey-darken-1 mb-6">
          {{ $t("maintenance.subtitle") }}
        </p>
        <v-btn
          color="primary"
          variant="tonal"
          prepend-icon="mdi-phone"
          class="font-weight-bold"
          :href="'tel:' + settingsStore.phone"
          v-if="settingsStore.phone"
        >
          {{ $t("maintenance.contact_us", { phone: settingsStore.phone }) }}
        </v-btn>
      </v-container>
    </v-overlay>

    <component :is="currentLayout" v-if="!showMaintenance || isAdmin">
      <router-view />

      <v-snackbar
        v-model="bar"
        location="top center"
        :color="snackColor"
        elevation="24"
        rounded="pill"
        class="mb-6"
        min-width="200"
        :timeout="3000"
      >
        <div class="d-flex align-center gap-2">
          <v-icon v-if="snackIcon" size="20" color="white">{{
            snackIcon
          }}</v-icon>
          <span class="font-weight-bold">{{ itemTitle }}</span>
        </div>
        <template v-slot:actions>
          <v-btn
            icon="mdi-close"
            variant="text"
            size="small"
            color="white"
            @click="bar = false"
          ></v-btn>
        </template>
      </v-snackbar>
    </component>
  </v-app>
</template>

<script setup>
import { onMounted, ref, inject, computed, watch } from "vue";
import { useRoute } from "vue-router";
import { useI18n } from "vue-i18n";
import { useLocale, useTheme } from "vuetify";
import { useSettingsStore } from "@/store/Settings";
import { useAuthStore } from "@/store/auth/LogIn";

import LayOut from "@/components/LayOut.vue";
import DashboardLayout from "@/components/DashboardLayout.vue";
import GuestLayout from "@/components/GuestLayout.vue";

import { AddInCart } from "@/store/Cart";
import { AddFavProduct } from "@/store/Favourate";

const route = useRoute();
const emitter = inject("emitter");
const bar = ref(false);
const itemTitle = ref("");
const snackColor = ref("primary");
const snackIcon = ref("");

const cartStore = AddInCart();
const favStore = AddFavProduct();
const settingsStore = useSettingsStore();
const authStore = useAuthStore();

const { locale: i18nLocale } = useI18n();
const { current: vuetifyLocale } = useLocale();
const theme = useTheme();

// Derived from auth and settings
const isAdmin = computed(() =>
  ["admin", "moderator"].includes(authStore.currentUser?.role)
);
const showMaintenance = computed(() => {
  // Only show maintenance if mode is active and user is NOT an admin
  return (
    settingsStore.maintenanceMode &&
    !isAdmin.value &&
    route.path !== "/Auth/LogIn"
  );
});

watch(
  i18nLocale,
  (newLocale) => {
    vuetifyLocale.value = newLocale;
    document.dir = newLocale === "ar" ? "rtl" : "ltr";
  },
  { immediate: true }
);

const layouts = {
  LayOut,
  DashboardLayout,
  GuestLayout,
};

const currentLayoutName = computed(() => {
  return route.meta.layout || "LayOut";
});

const currentLayout = computed(() => {
  return layouts[currentLayoutName.value];
});

watch(currentLayoutName, () => {
  applyTheme(settingsStore.$state);
});

// Watch for store changes (like after a fetch) and apply theme reactively
watch(
  () => settingsStore.primaryColor,
  () => {
    applyTheme(settingsStore.$state);
  }
);

onMounted(async () => {
  emitter.on("showMessage", (data) => {
    if (typeof data === "string") {
      itemTitle.value = data;
      snackColor.value = "primary";
      snackIcon.value = "mdi-information";
    } else {
      itemTitle.value = data.text || "";
      snackColor.value = data.color || "primary";
      snackIcon.value =
        data.icon ||
        (data.color === "success"
          ? "mdi-check-circle"
          : data.color === "error"
          ? "mdi-alert-circle"
          : "mdi-information");
    }
    bar.value = true;
  });

  // Persist cart and favorites
  if (authStore.isAuthenticated) {
    cartStore.fetchCart();
    favStore.fetchFavorites();
  }

  // Centralized Settings & Theme Application
  try {
    const data = await settingsStore.fetchSettings();
    applyTheme(data);
  } catch (err) {
    console.error("Failed to load settings:", err);
  }
});

const hexToRgb = (hex) => {
  if (!hex || hex.length < 7) return "0,0,0";
  let r = parseInt(hex.slice(1, 3), 16),
    g = parseInt(hex.slice(3, 5), 16),
    b = parseInt(hex.slice(5, 7), 16);
  return `${r}, ${g}, ${b}`;
};

const applyTheme = (data) => {
  if (!data) return;

  if (data.primaryColor) {
    const pColor = data.primaryColor;
    theme.themes.value.orangeTheme.colors = {
      ...theme.themes.value.orangeTheme.colors,
      primary: pColor,
      secondary: pColor,
    };
    theme.global.name.value = "orangeTheme";
    const pRgb = hexToRgb(pColor);
    document.documentElement.style.setProperty("--v-theme-primary", pRgb);
    document.documentElement.style.setProperty("--v-theme-secondary", pRgb);
    document.body.style.setProperty("--v-theme-primary", pRgb);
    document.body.style.setProperty("--v-theme-secondary", pRgb);
  }
};
</script>

<style lang="scss">
/* نفس الـ CSS بتاعك بدون أي تغيير */
/* Theme Overrides for hardcoded orange colors */
.bg-primary,
.bg-orange,
.v-btn.bg-orange,
.v-btn.text-orange {
  background-color: rgb(var(--v-theme-primary)) !important;
  color: rgb(var(--v-theme-on-primary)) !important;
}

.text-primary,
.text-orange,
.text-orange-darken-2,
.v-icon.text-orange {
  color: rgb(var(--v-theme-primary)) !important;
}

.border-primary,
.border-orange {
  border-color: rgb(var(--v-theme-primary)) !important;
}

/* Ensure the wrapper respects the theme background */
.v-application {
  background-color: rgb(var(--v-theme-background)) !important;
}

#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
}
body {
  overflow: hidden;
  font-family: "Cairo", sans-serif;
}
nav {
  padding: 30px;

  a {
    font-weight: bold;
    color: rgba(var(--v-theme-on-surface), 0.8);

    &.router-link-exact-active {
      color: rgb(var(--v-theme-primary));
    }
  }
}
.nav-text {
  font-size: 0.8rem !important;
}
/* تنسيق عنوان القسم */
.section-title {
  position: relative;
  display: inline-block;
  padding-bottom: 15px;
  margin-bottom: 10px;
  font-weight: 800;
  color: rgb(var(--v-theme-primary));
  letter-spacing: 0.5px;
}

.section-title::before {
  content: "";
  position: absolute;
  left: 0;
  bottom: 0;
  width: 60px; /* تقصير الخط ليكون أكثر أناقة */
  height: 4px;
  background: rgb(var(--v-theme-primary));
  border-radius: 10px;
}
.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}
</style>
