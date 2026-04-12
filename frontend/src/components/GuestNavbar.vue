<template>
  <v-app-bar
    flat
    :elevation="isHome ? 0 : 2"
    :color="isHome ? 'transparent' : 'white'"
    :class="[
      'guest-navbar',
      {
        'border-bottom': !isHome,
        'position-absolute top-0 w-100': isHome,
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
        <v-menu location="bottom end" transition="scale-transition">
          <template v-slot:activator="{ props }">
            <v-btn
              color="primary"
              variant="text"
              class="text-capitalize px-1 mr-1 font-weight-bold"
              v-bind="props"
              rounded
              style="min-width: auto"
            >
              <v-avatar size="22" class="ml-1 border">
                <v-img :src="currentLang.flag" cover></v-img>
              </v-avatar>
              <span>{{ currentLang.code }}</span>
              <v-icon size="small">mdi-chevron-down</v-icon>
            </v-btn>
          </template>

          <v-list density="compact" nav elevation="3" class="rounded-lg">
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

        <v-btn
          variant="outlined"
          color="primary"
          class="font-weight-bold rounded-lg px-6"
          to="/Auth/LogIn"
        >
          {{ $t("guest_nav.login") }}
        </v-btn>
        <v-btn
          variant="flat"
          color="primary"
          class="font-weight-bold rounded-lg px-6"
          elevation="2"
          to="/Auth/RegisterNow"
        >
          {{ $t("guest_nav.register") }}
        </v-btn>
      </div>

      <!-- Mobile Menu Button -->
      <v-btn icon class="d-md-none" @click="mobileMenu = !mobileMenu">
        <v-icon>mdi-menu</v-icon>
      </v-btn>
    </v-container>

    <!-- Mobile Drawer -->
    <v-navigation-drawer
      v-model="mobileMenu"
      temporary
      location="right"
      width="250"
    >
      <v-list density="comfortable" nav class="mt-4">
        <v-list-item
          to="/"
          prepend-icon="mdi-home"
          :title="$t('guest_nav.home')"
        ></v-list-item>
        <v-list-item
          to="/BlogPage"
          prepend-icon="mdi-post-outline"
          :title="$t('guest_nav.blog')"
        ></v-list-item>
        <v-list-item
          to="/AboutPage"
          prepend-icon="mdi-information-outline"
          :title="$t('guest_nav.about_us')"
        ></v-list-item>
        <v-list-item
          to="/ContactPage"
          prepend-icon="mdi-phone-outline"
          :title="$t('guest_nav.contact')"
        ></v-list-item>
      </v-list>
      <v-divider class="my-4"></v-divider>

      <!-- Mobile Language Switcher -->
      <div class="px-4 pb-2">
        <v-select
          v-model="currentLangCode"
          :items="availableLangs"
          item-title="name"
          item-value="code"
          :label="$t('guest_nav.language_label')"
          variant="outlined"
          density="compact"
          color="primary"
          @update:modelValue="onLangChange($event)"
        >
          <template v-slot:selection="{ item }">
            <div class="d-flex align-center">
              <img
                :src="item.raw.flag"
                width="20"
                class="ml-2"
                style="border-radius: 2px"
              />
              {{ item.raw.name }}
            </div>
          </template>
        </v-select>
      </div>

      <div class="px-4 pb-4">
        <v-btn
          block
          color="primary"
          variant="outlined"
          to="/Auth/LogIn"
          class="mb-3"
          >{{ $t("guest_nav.login") }}</v-btn
        >
        <v-btn block color="primary" variant="flat" to="/Auth/RegisterNow">{{
          $t("guest_nav.register")
        }}</v-btn>
      </div>
    </v-navigation-drawer>
  </v-app-bar>
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
</style>
