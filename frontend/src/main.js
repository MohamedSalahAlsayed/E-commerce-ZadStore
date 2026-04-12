import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
// import store from "./store";
import { createPinia } from "pinia";
import mitt from "mitt";
const emitter = mitt();

// i18n
import i18n from "./i18n";

// Vuetify
import "vuetify/styles";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import "@mdi/font/css/materialdesignicons.css";

import { en as vuetifyEn, ar as vuetifyAr } from "vuetify/locale";

const orangeTheme = {
  dark: false,
  colors: {
    background: "#f5f5f5",
    surface: "#ffffff",
    primary: "#f97316",
    secondary: "#ea580c",
    error: "#B00020",
    info: "#2196F3",
    success: "#4CAF50",
    warning: "#FB8C00",
  },
};

const blueTheme = {
  dark: false,
  colors: {
    background: "#f5f5f5",
    surface: "#ffffff",
    primary: "#2196F3",
    secondary: "#1976D2",
    error: "#B00020",
    info: "#2196F3",
    success: "#4CAF50",
    warning: "#FB8C00",
  },
};

const greenTheme = {
  dark: false,
  colors: {
    background: "#f8fafc",
    surface: "#ffffff",
    primary: "#10b981",
    secondary: "#059669",
    error: "#ef4444",
    info: "#3b82f6",
    success: "#22c55e",
    warning: "#f59e0b",
  },
};

const purpleTheme = {
  dark: false,
  colors: {
    background: "#f5f3ff",
    surface: "#ffffff",
    primary: "#8b5cf6",
    secondary: "#7c3aed",
    error: "#ef4444",
    info: "#3b82f6",
    success: "#22c55e",
    warning: "#f59e0b",
  },
};

const redTheme = {
  dark: false,
  colors: {
    background: "#fef2f2",
    surface: "#ffffff",
    primary: "#ef4444",
    secondary: "#dc2626",
    error: "#b91c1c",
    info: "#3b82f6",
    success: "#22c55e",
    warning: "#f59e0b",
  },
};

const vuetify = createVuetify({
  components,
  directives,
  theme: {
    defaultTheme: "orangeTheme",
    themes: {
      orangeTheme,
      blueTheme,
      greenTheme,
      purpleTheme,
      redTheme,
    },
  },
  locale: {
    locale: localStorage.getItem("lang") || "ar",
    fallback: "en",
    messages: {
      en: vuetifyEn,
      ar: vuetifyAr,
    },
    rtl: { ar: true, en: false },
  },
});

createApp(App)
  .use(createPinia())
  .provide("emitter", emitter)
  .use(i18n)
  .use(vuetify)
  .use(router)
  .mount("#app");
