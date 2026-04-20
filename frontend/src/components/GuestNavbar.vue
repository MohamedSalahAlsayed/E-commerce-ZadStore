<template>
  <div class="guest-navbar-wrapper">
    <v-app-bar
      flat
      :elevation="isHome ? 0 : 2"
      :color="isHome ? 'transparent' : 'white'"
      :class="[
        'guest-navbar',
        {
          'border-bottom': !isHome,
          'position-absolute top-0 w-100': isHome,
          'premium-mobile-bar': $vuetify.display.xs || $vuetify.display.sm,
        },
      ]"
      height="75"
    >
      <v-container
        fluid
        class="d-flex align-center justify-space-between py-0 h-100 px-md-10"
      >
        <!-- Logo -->
        <div
          class="d-flex align-center cursor-pointer ml-4"
          @click="$router.push('/')"
        >
          <img
            v-if="settingsStore.logoPreview"
            :src="settingsStore.logoPreview"
            alt="Store Logo"
            height="45"
            style="object-fit: contain"
          />
          <h2 class="font-weight-black text-primary logo-text" v-else>
            {{ settingsStore.storeName || "زاّد ستور" }}
          </h2>
        </div>

        <!-- Centered Links -->
        <div class="d-none d-md-flex gap-6 justify-center flex-grow-1">
          <v-btn variant="text" class="font-weight-bold" to="/">{{
            $t("guest_nav.home")
          }}</v-btn>
          <v-btn variant="text" class="font-weight-bold" to="/BlogPage">{{
            $t("guest_nav.blog")
          }}</v-btn>
          <v-btn variant="text" class="font-weight-bold" to="/AboutPage">{{
            $t("guest_nav.about_us")
          }}</v-btn>
          <v-btn variant="text" class="font-weight-bold" to="/ContactPage">{{
            $t("guest_nav.contact")
          }}</v-btn>
        </div>

        <!-- Action Buttons & Lang Switcher -->
        <div class="d-none d-sm-flex align-center gap-3">
          <v-btn
            variant="outlined"
            color="primary"
            class="font-weight-bold rounded-lg px-6 h-40"
            to="/Auth/LogIn"
          >
            {{ $t("guest_nav.login") }}
          </v-btn>
          <v-btn
            variant="flat"
            color="primary"
            class="font-weight-bold rounded-lg px-6 h-40 elevation-2"
            to="/Auth/RegisterNow"
          >
            {{ $t("guest_nav.register") }}
          </v-btn>

          <v-divider
            vertical
            class="mx-1"
            style="height: 25px; align-self: center"
          ></v-divider>

          <v-menu location="bottom end" transition="scale-transition">
            <template v-slot:activator="{ props }">
              <v-btn
                color="primary"
                variant="text"
                class="text-capitalize px-1 font-weight-bold rounded-lg"
                v-bind="props"
                style="min-width: auto"
              >
                <v-avatar size="22" class="ml-1 border">
                  <v-img :src="currentLang.flag" cover></v-img>
                </v-avatar>
                <span>{{ currentLang.name }}</span>
                <v-icon size="small">mdi-chevron-down</v-icon>
              </v-btn>
            </template>

            <v-list density="compact" nav elevation="3" class="rounded-lg mt-2">
              <v-list-item
                v-for="(lang, i) in availableLangs"
                :key="i"
                :value="lang.code"
                @click="changeLang(lang)"
                :active="currentLang.code === lang.code"
                color="primary"
              >
                <template v-slot:prepend>
                  <img
                    :src="lang.flag"
                    width="20"
                    class="ml-3"
                    style="border-radius: 2px"
                  />
                </template>
                <v-list-item-title>{{ lang.name }}</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </div>

        <!-- Mobile Menu Button (Improved Visibility) -->
        <v-btn
          icon
          class="d-md-none mobile-menu-btn"
          @click="mobileMenu = !mobileMenu"
          variant="flat"
          :color="isHome ? 'rgba(255,255,255,0.2)' : 'grey-lighten-4'"
        >
          <v-icon :color="isHome ? 'white' : 'primary'">mdi-menu</v-icon>
        </v-btn>
      </v-container>
    </v-app-bar>

    <!-- Mobile Drawer (Moved outside v-app-bar for better stability) -->
    <v-navigation-drawer
      v-model="mobileMenu"
      temporary
      :location="currentLang.code === 'ar' ? 'right' : 'left'"
      width="280"
      class="guest-mobile-drawer"
    >
      <div class="d-flex flex-column h-100 mobile-drawer-content">
        <!-- Premium Drawer Header -->
        <div class="user-drawer-card pa-6">
          <div class="d-flex justify-end mb-n6">
            <v-btn
              icon="mdi-close"
              variant="text"
              color="white"
              size="small"
              @click="mobileMenu = false"
            ></v-btn>
          </div>
          <div class="d-flex align-center gap-4 pt-4 mb-2">
            <v-avatar size="64" color="white-opacity-20" class="shadow-lg">
              <v-icon color="white" size="32">mdi-store-outline</v-icon>
            </v-avatar>
            <div class="user-info-text text-white">
              <div class="text-h6 font-weight-black lh-1 mb-1">
                {{ settingsStore.storeName || "ZadStore" }}
              </div>
              <div class="text-caption opacity-80">
                {{ $t("nav.welcome_guest") || "نورت متجرنا" }}
              </div>
            </div>
          </div>
        </div>

        <div class="drawer-body pa-4 overflow-y-auto">
          <div class="d-flex justify-space-between align-center mb-6 d-none">
            <h3 class="font-weight-black text-primary">{{ $t("nav.menu") }}</h3>
            <v-btn
              icon="mdi-close"
              variant="text"
              size="small"
              @click="mobileMenu = false"
            ></v-btn>
          </div>

          <!-- NEW: Primary Mobile Actions At TOP -->
          <div class="auth-buttons-mobile mb-4 px-1">
            <v-btn
              block
              color="primary"
              variant="tonal"
              to="/Auth/LogIn"
              class="mb-3 rounded-lg font-weight-black h-48"
              @click="mobileMenu = false"
              >{{ $t("guest_nav.login") }}</v-btn
            >
            <v-btn
              block
              color="primary"
              variant="flat"
              to="/Auth/RegisterNow"
              class="rounded-lg font-weight-black h-48 elevation-2"
              @click="mobileMenu = false"
              >{{ $t("guest_nav.register") }}</v-btn
            >
          </div>

          <!-- NEW: Language Grid TOP -->
          <div class="mb-4 px-1">
            <div
              class="text-caption font-weight-black text-uppercase opacity-40 mb-3 ps-1"
            >
              {{ $t("language") }}
            </div>
            <v-item-group
              v-model="currentLangCode"
              mandatory
              class="d-flex gap-2"
              @update:model-value="(val) => onLangChange(val)"
            >
              <v-item
                v-for="lang in availableLangs"
                :key="lang.code"
                :value="lang.code"
                v-slot="{ isSelected, toggle }"
              >
                <v-btn
                  :variant="isSelected ? 'flat' : 'tonal'"
                  :color="isSelected ? 'primary' : 'grey-lighten-3'"
                  class="flex-grow-1 rounded-lg font-weight-bold"
                  @click="toggle"
                  size="small"
                  height="40"
                >
                  <v-img
                    :src="lang.flag"
                    width="18"
                    class="me-2 rounded-xs"
                  ></v-img>
                  {{ lang.name }}
                </v-btn>
              </v-item>
            </v-item-group>
          </div>

          <v-divider class="my-6"></v-divider>

          <!-- NAVIGATION LINKS -->
          <v-list density="comfortable" nav class="mb-4">
            <v-list-item
              to="/"
              prepend-icon="mdi-home"
              class="rounded-lg mb-1"
              :title="$t('guest_nav.home')"
              @click="mobileMenu = false"
            ></v-list-item>
            <v-list-item
              to="/BlogPage"
              prepend-icon="mdi-post-outline"
              class="rounded-lg mb-1"
              :title="$t('guest_nav.blog')"
              @click="mobileMenu = false"
            ></v-list-item>
            <v-list-item
              to="/AboutPage"
              prepend-icon="mdi-information-outline"
              class="rounded-lg mb-1"
              :title="$t('guest_nav.about_us')"
              @click="mobileMenu = false"
            ></v-list-item>
            <v-list-item
              to="/ContactPage"
              prepend-icon="mdi-phone-outline"
              class="rounded-lg mb-1"
              :title="$t('guest_nav.contact')"
              @click="mobileMenu = false"
            ></v-list-item>
          </v-list>
        </div>
      </div>
    </v-navigation-drawer>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useSettingsStore } from "@/store/Settings";
import { useI18n } from "vue-i18n";
import { useRoute } from "vue-router";

const settingsStore = useSettingsStore();
const route = useRoute();
const mobileMenu = ref(false);

const isHome = computed(() => route.path === "/");

const { locale } = useI18n();

const availableLangs = [
  { code: "en", name: "English", flag: "https://flagcdn.com/w40/us.png" },
  { code: "ar", name: "العربية", flag: "https://flagcdn.com/w40/eg.png" },
];

const currentLangCode = ref(locale.value);
const currentLang = computed(
  () =>
    availableLangs.find((l) => l.code === currentLangCode.value) ||
    availableLangs[1]
);

const changeLang = (lang) => {
  currentLangCode.value = lang.code;
  locale.value = lang.code;
  localStorage.setItem("lang", lang.code);
};

const onLangChange = (code) => {
  const lang = availableLangs.find((l) => l.code === code);
  if (lang) changeLang(lang);
};

onMounted(() => {
  settingsStore.fetchSettings();
  const savedLang = localStorage.getItem("lang");
  if (savedLang && ["ar", "en"].includes(savedLang)) {
    locale.value = savedLang;
    currentLangCode.value = savedLang;
  }
});
</script>

<style scoped>
.logo-text {
  font-family: "Cairo", sans-serif;
  letter-spacing: -0.5px;
}
.gap-6 {
  gap: 24px;
}
.gap-3 {
  gap: 12px;
}
.h-40 {
  height: 40px !important;
}
.h-48 {
  height: 48px !important;
}
.mobile-menu-btn {
  backdrop-filter: blur(4px);
  transition: all 0.3s ease;
}
.guest-mobile-drawer {
  border-radius: 20px 0 0 20px !important;
}

.user-drawer-card {
  background: linear-gradient(
    135deg,
    color-mix(in srgb, rgb(var(--v-theme-primary)) 80%, black) 0%,
    rgb(var(--v-theme-primary)) 100%
  );
  position: relative;
  overflow: hidden;
}

.user-drawer-card::before {
  content: "";
  position: absolute;
  top: -20%;
  right: -10%;
  width: 150px;
  height: 150px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  z-index: 0;
}

.white-opacity-20 {
  background-color: rgba(255, 255, 255, 0.2) !important;
}

.lh-1 {
  line-height: 1.2;
}

.premium-mobile-bar {
  background: linear-gradient(
    135deg,
    #0d1117 0%,
    rgb(var(--v-theme-primary)) 100%
  ) !important;
}
.premium-mobile-bar :deep(.v-btn),
.premium-mobile-bar :deep(.logo-text) {
  color: white !important;
}
</style>
