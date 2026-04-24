<template>
  <v-layout
    :dir="isRtl ? 'rtl' : 'ltr'"
    class="dashboard-layout"
    style="height: 100vh; overflow: hidden"
  >
    <v-navigation-drawer
      v-model="drawer"
      :rail="rail && !isMobile"
      :permanent="!isMobile"
      :temporary="isMobile"
      :location="isRtl ? 'right' : 'left'"
      width="280"
      @click="rail = false"
      class="sidebar-menu custom-scrollbar border-0"
      elevation="4"
    >
      <v-list-item
        :prepend-avatar="getAvatarUrl(adminUser.avatar)"
        :title="adminUser.name || 'ZadStore Admin'"
        :subtitle="adminUser.email || $t('dashboard.admin_role')"
        class="py-4 text-white font-weight-black text-subtitle-1 border-b-0"
        nav
      >
        <template v-slot:append>
          <v-btn
            variant="text"
            icon="mdi-chevron-right"
            color="white"
            @click.stop="rail = !rail"
          ></v-btn>
        </template>
      </v-list-item>

      <v-divider color="white" class="mb-2 opacity-20"></v-divider>

      <v-list density="compact" nav class="px-0">
        <v-list-item
          to="/Dashboard/AdminDashboard"
          exact
          prepend-icon="mdi-view-dashboard-outline"
          :title="$t('dashboard.home')"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>

        <!-- Catalog Management -->
        <v-divider
          color="white"
          class="my-3 opacity-20"
          v-if="
            !rail &&
            (can('manage_products') ||
              can('manage_categories') ||
              can('manage_inventory') ||
              can('manage_reviews'))
          "
        ></v-divider>
        <v-list-subheader
          class="text-white opacity-50 font-weight-bold"
          v-if="
            !rail &&
            (can('manage_products') ||
              can('manage_categories') ||
              can('manage_inventory') ||
              can('manage_reviews'))
          "
          >{{ $t("dashboard.catalog") }}</v-list-subheader
        >

        <v-list-item
          v-if="can('manage_products')"
          to="/Dashboard/MangProductPage"
          prepend-icon="mdi-package-variant-closed"
          :title="$t('dashboard.products')"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>
        <v-list-item
          v-if="can('manage_categories')"
          to="/Dashboard/MangCategoury"
          prepend-icon="mdi-shape-outline"
          :title="$t('dashboard.categories_brands')"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>
        <v-list-item
          v-if="can('manage_inventory')"
          to="/Dashboard/InventoryManager"
          prepend-icon="mdi-warehouse"
          :title="$t('dashboard.inventory')"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>
        <v-list-item
          v-if="can('view_inventory_logs')"
          to="/Dashboard/InventoryLogs"
          prepend-icon="mdi-history"
          :title="$t('inventory_logs.title')"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>
        <v-list-item
          v-if="can('manage_reviews')"
          to="/Dashboard/RatingsReviews"
          prepend-icon="mdi-star-face"
          :title="$t('dashboard.reviews')"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>

        <!-- Sales & CRM -->
        <v-divider
          color="white"
          class="my-3 opacity-20"
          v-if="
            !rail &&
            (can('manage_orders') ||
              can('manage_returns') ||
              can('manage_users'))
          "
        ></v-divider>
        <v-list-subheader
          class="text-white opacity-50 font-weight-bold"
          v-if="
            !rail &&
            (can('manage_orders') ||
              can('manage_returns') ||
              can('manage_users'))
          "
          >{{ $t("dashboard.sales_crm") }}</v-list-subheader
        >

        <v-list-item
          v-if="can('manage_orders')"
          to="/Dashboard/MangOrder"
          prepend-icon="mdi-cart-check"
          :title="$t('dashboard.orders')"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>
        <v-list-item
          v-if="can('manage_returns')"
          to="/Dashboard/MangReturns"
          prepend-icon="mdi-keyboard-return"
          :title="$t('dashboard.returns')"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>
        <v-list-item
          v-if="can('manage_inventory')"
          to="/Dashboard/MangPurchases"
          prepend-icon="mdi-truck-cargo-container"
          :title="$t('dashboard.purchases')"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>
        <v-list-item
          v-if="can('manage_staff')"
          to="/Dashboard/MangUsers"
          prepend-icon="mdi-account-group-outline"
          :title="$t('dashboard.customers')"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>

        <!-- Marketing & Content -->
        <v-divider
          color="white"
          class="my-3 opacity-20"
          v-if="!rail && can('manage_marketing')"
        ></v-divider>
        <v-list-subheader
          class="text-white opacity-50 font-weight-bold"
          v-if="!rail && can('manage_marketing')"
          >{{ $t("dashboard.marketing") }}</v-list-subheader
        >

        <v-list-item
          v-if="can('manage_marketing')"
          to="/Dashboard/CouponsDiscounts"
          prepend-icon="mdi-ticket-percent"
          :title="$t('dashboard.coupons')"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>
        <v-list-item
          v-if="can('manage_marketing')"
          to="/Dashboard/MangBanner"
          prepend-icon="mdi-image-multiple-outline"
          :title="$t('dashboard.banners')"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>
        <v-list-item
          v-if="can('manage_marketing')"
          to="/Dashboard/MangBlog"
          prepend-icon="mdi-post-outline"
          :title="$t('dashboard.blog')"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>
        <v-list-item
          v-if="can('manage_marketing')"
          to="/Dashboard/Subscribers"
          prepend-icon="mdi-email-multiple-outline"
          :title="$t('dashboard.newsletter_subscribers') || 'المشتركين'"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>

        <!-- System & Administration -->
        <v-divider
          color="white"
          class="my-3 opacity-20"
          v-if="
            !rail &&
            (can('view_financials') ||
              can('manage_settings') ||
              can('manage_staff'))
          "
        ></v-divider>
        <v-list-subheader
          class="text-white opacity-50 font-weight-bold"
          v-if="
            !rail &&
            (can('view_financials') ||
              can('manage_settings') ||
              can('manage_staff'))
          "
          >{{ $t("dashboard.system") }}</v-list-subheader
        >

        <v-list-item
          v-if="can('view_financials')"
          to="/Dashboard/Financials"
          prepend-icon="mdi-finance"
          :title="$t('dashboard.financials')"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>
        <v-list-item
          v-if="can('view_financials')"
          to="/Dashboard/Payments"
          prepend-icon="mdi-credit-card-outline"
          :title="$t('dashboard.payments')"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>
        <v-list-item
          v-if="can('view_financials')"
          to="/Dashboard/AdvancedReports"
          prepend-icon="mdi-chart-line"
          :title="$t('dashboard.advanced_reports')"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>
        <v-list-item
          v-if="can('manage_settings')"
          to="/Dashboard/ShippingZone"
          prepend-icon="mdi-truck-delivery-outline"
          :title="$t('dashboard.shipping')"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>
        <v-list-item
          v-if="can('manage_staff')"
          to="/Dashboard/StaffManagement"
          prepend-icon="mdi-shield-account-outline"
          :title="$t('dashboard.staff')"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>
        <v-list-item
          v-if="can('manage_marketing')"
          to="/Dashboard/ContactMessage"
          prepend-icon="mdi-email-outline"
          :title="$t('dashboard.messages')"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>
        <v-list-item
          v-if="can('manage_settings')"
          to="/Dashboard/MangSEO"
          prepend-icon="mdi-google-analytics"
          :title="$t('dashboard.seo_settings')"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>
        <v-list-item
          v-if="can('manage_settings')"
          to="/Dashboard/StoreSetting"
          prepend-icon="mdi-cog-box"
          :title="$t('dashboard.settings')"
          active-color="#fb923c"
          class="text-white menu-item"
        ></v-list-item>
      </v-list>

      <template v-slot:append>
        <div class="pa-3">
          <v-btn
            block
            color="error"
            variant="tonal"
            prepend-icon="mdi-logout"
            class="font-weight-bold"
            @click="logoutAdmin"
            v-if="!rail"
            >{{ $t("logout") }}</v-btn
          >
          <v-btn
            icon="mdi-logout"
            color="error"
            variant="tonal"
            block
            @click="logoutAdmin"
            v-else
          ></v-btn>
        </div>
      </template>
    </v-navigation-drawer>
    <v-locale-provider :rtl="isRtl">
      <v-app-bar
        elevation="1"
        color="white"
        height="70"
        :dir="isRtl ? 'rtl' : 'ltr'"
      >
        <v-app-bar-nav-icon
          @click="drawer = !drawer"
          color="rgb(var(--v-theme-primary))"
        ></v-app-bar-nav-icon>
        <v-app-bar-title
          class="font-weight-black"
          style="color: rgb(var(--v-theme-primary))"
          >{{ $t("dashboard.title") }}</v-app-bar-title
        >
        <v-spacer></v-spacer>

        <!-- Social Media Links (dynamic from settings) -->
        <template v-for="social in navbarSocials" :key="social.key">
          <v-btn
            icon
            variant="tonal"
            class="mx-1 social-nav-btn"
            :class="social.key + '-btn'"
            :href="social.href"
            target="_blank"
            height="40"
            width="40"
          >
            <!-- TikTok SVG -->
            <svg
              v-if="social.key === 'tiktok'"
              xmlns="http://www.w3.org/2000/svg"
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="currentColor"
            >
              <path
                d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.29 6.29 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V9.05a8.16 8.16 0 0 0 4.77 1.52V7.14a4.85 4.85 0 0 1-1-.45z"
              />
            </svg>
            <!-- YouTube SVG -->
            <svg
              v-else-if="social.key === 'youtube'"
              xmlns="http://www.w3.org/2000/svg"
              width="22"
              height="22"
              viewBox="0 0 24 24"
              fill="currentColor"
            >
              <path
                d="M23.5 6.19a3.02 3.02 0 0 0-2.12-2.14C19.54 3.5 12 3.5 12 3.5s-7.54 0-9.38.55A3.02 3.02 0 0 0 .5 6.19 31.72 31.72 0 0 0 0 12a31.72 31.72 0 0 0 .5 5.81 3.02 3.02 0 0 0 2.12 2.14C4.46 20.5 12 20.5 12 20.5s7.54 0 9.38-.55a3.02 3.02 0 0 0 2.12-2.14A31.72 31.72 0 0 0 24 12a31.72 31.72 0 0 0-.5-5.81zM9.75 15.5v-7l6.5 3.5-6.5 3.5z"
              />
            </svg>
            <!-- MDI icon for others -->
            <v-icon v-else :color="social.color">{{ social.icon }}</v-icon>
            <v-tooltip activator="parent" location="bottom">{{
              social.label
            }}</v-tooltip>
          </v-btn>
        </template>

        <!-- Messages Menu -->
        <v-menu
          location="bottom end"
          :offset="14"
          transition="slide-y-transition"
          width="350"
        >
          <template v-slot:activator="{ props }">
            <v-btn icon color="grey-darken-2" class="mr-2" v-bind="props">
              <v-badge
                :content="messageList.length"
                :color="messageList.length > 0 ? 'info' : 'transparent'"
                offset-x="5"
                offset-y="5"
                :class="{ 'pulse-info': messageList.length > 0 }"
              >
                <v-icon>mdi-message-text-outline</v-icon>
              </v-badge>
            </v-btn>
          </template>
          <v-card class="rounded-lg shadow-lg">
            <v-card-title
              class="text-subtitle-1 font-weight-bold pa-3 border-b"
            >
              {{ $t("dashboard.unread_messages") }} ({{ messageList.length }})
            </v-card-title>

            <div
              v-if="messageList.length === 0"
              class="pa-4 text-center text-grey text-caption"
            >
              {{ $t("dashboard.no_messages") }}
            </div>

            <v-list lines="two" density="compact" class="pa-0" v-else>
              <v-list-item
                v-for="msg in messageList"
                :key="msg.id"
                :to="'/Dashboard/ContactMessage'"
                active-color="primary"
                class="hover-row"
              >
                <template v-slot:prepend>
                  <v-avatar size="40" color="primary-lighten-4">
                    <span class="text-primary font-weight-bold">{{
                      msg.name.substring(0, 2).toUpperCase()
                    }}</span>
                  </v-avatar>
                </template>
                <v-list-item-title class="font-weight-bold text-body-2">{{
                  msg.name
                }}</v-list-item-title>
                <v-list-item-subtitle
                  class="text-caption text-truncate"
                  style="max-width: 200px"
                  >{{ msg.message }}</v-list-item-subtitle
                >
                <template v-slot:append>
                  <div class="text-caption text-grey" style="font-size: 10px">
                    {{
                      new Date(msg.created_at).toLocaleDateString(
                        locale === "ar" ? "ar-EG" : "en-US"
                      )
                    }}
                  </div>
                </template>
              </v-list-item>
            </v-list>
            <v-divider></v-divider>
            <v-btn
              block
              variant="text"
              color="primary"
              class="text-capitalize"
              to="/Dashboard/ContactMessage"
              >{{ $t("dashboard.view_all") }}</v-btn
            >
          </v-card>
        </v-menu>

        <!-- Notifications Bell Dropdown -->
        <v-menu
          location="bottom end"
          :offset="14"
          transition="slide-y-transition"
          width="350"
          @update:modelValue="onNotificationsMenuOpen"
        >
          <template v-slot:activator="{ props }">
            <v-btn icon color="grey-darken-2" class="mr-2" v-bind="props">
              <v-badge
                :color="displayNotifCount > 0 ? 'error' : 'transparent'"
                :content="displayNotifCount"
                :class="{ 'pulse-error': displayNotifCount > 0 }"
                offset-x="5"
                offset-y="5"
              >
                <v-icon>mdi-bell-outline</v-icon>
              </v-badge>
            </v-btn>
          </template>
          <v-card class="rounded-lg shadow-lg">
            <v-card-title
              class="text-subtitle-1 font-weight-bold pa-3 border-b"
            >
              {{ $t("dashboard.notifications") }} ({{ notificationsCount }})
            </v-card-title>

            <div
              v-if="recentNotifications.length === 0"
              class="pa-4 text-center text-grey text-caption"
            >
              {{ $t("dashboard.no_notifications") }}
            </div>

            <v-list lines="two" density="compact" class="pa-0" v-else>
              <v-list-item
                v-for="note in recentNotifications"
                :key="note.id"
                :to="note.to"
                active-color="primary"
                class="hover-row border-b last-border-0"
              >
                <template v-slot:prepend>
                  <v-avatar size="40" :color="note.color + '-lighten-4'">
                    <v-icon :color="note.color" size="24">{{
                      note.icon
                    }}</v-icon>
                  </v-avatar>
                </template>
                <v-list-item-title class="font-weight-bold text-body-2">{{
                  note.title
                }}</v-list-item-title>
                <v-list-item-subtitle class="text-caption">{{
                  note.description
                }}</v-list-item-subtitle>
              </v-list-item>
            </v-list>
            <v-divider></v-divider>
            <v-btn
              block
              variant="text"
              color="primary"
              class="text-capitalize"
              to="/Dashboard/AdminDashboard"
              >{{ $t("dashboard.home") }}</v-btn
            >
          </v-card>
        </v-menu>

        <!-- Language Switcher -->
        <v-btn
          variant="tonal"
          color="primary"
          class="mx-1 rounded-lg font-weight-black"
          height="40"
          width="40"
          @click="toggleLanguage"
        >
          {{ isRtl ? "EN" : "AR" }}
          <v-tooltip activator="parent" location="bottom">{{
            isRtl ? "Switch to English" : "التحويل للغة العربية"
          }}</v-tooltip>
        </v-btn>

        <!-- View Website Button -->
        <v-btn
          variant="tonal"
          color="success"
          class="mx-1 rounded-lg"
          height="40"
          width="40"
          to="/"
          target="_blank"
        >
          <v-icon>mdi-open-in-new</v-icon>
          <v-tooltip activator="parent" location="bottom">{{
            $t("dashboard.view_website")
          }}</v-tooltip>
        </v-btn>

        <!-- Profile Avatar -->
        <v-menu location="bottom end" :offset="10">
          <template v-slot:activator="{ props }">
            <v-avatar
              color="primary"
              size="40"
              class="mr-4 ml-4 cursor-pointer elevation-1"
              v-bind="props"
            >
              <v-img :src="getAvatarUrl(adminUser.avatar)"></v-img>
            </v-avatar>
          </template>
          <v-card width="200" class="rounded-lg">
            <v-list density="compact">
              <v-list-item>
                <div class="font-weight-bold">{{ adminUser.name }}</div>
                <div class="text-caption text-grey">{{ adminUser.email }}</div>
              </v-list-item>
            </v-list>
            <v-divider></v-divider>
            <v-list density="compact">
              <v-list-item
                prepend-icon="mdi-account"
                :title="$t('profile')"
                to="/Dashboard/Profile"
              ></v-list-item>
              <v-list-item
                prepend-icon="mdi-logout"
                :title="$t('logout')"
                color="error"
                @click="logoutAdmin"
              ></v-list-item>
            </v-list>
          </v-card>
        </v-menu>
      </v-app-bar>

      <v-main class="bg-grey-lighten-4 dashboard-main">
        <v-container fluid class="pa-6" :style="layoutVars">
          <slot></slot>
        </v-container>
      </v-main>
    </v-locale-provider>

    <!-- Logout Confirmation Dialog -->
    <v-dialog v-model="logoutDialog" max-width="400" persistent>
      <v-card class="rounded-xl pa-4 text-center">
        <v-avatar color="error-lighten-5" size="70" class="mb-4 mx-auto">
          <v-icon color="error" size="40">mdi-logout-variant</v-icon>
        </v-avatar>
        <v-card-title class="text-h5 font-weight-bold mb-2">
          {{ $t("auth.logout_confirm_title") }}
        </v-card-title>
        <v-card-text class="text-grey-darken-1 text-body-1 pb-6">
          {{ $t("auth.logout_confirm_msg") }}
        </v-card-text>
        <v-card-actions class="justify-center gap-4">
          <v-btn
            color="grey-lighten-1"
            variant="flat"
            class="rounded-lg px-6 font-weight-bold"
            @click="logoutDialog = false"
          >
            {{ $t("dashboard.cancel") }}
          </v-btn>
          <v-btn
            color="error"
            variant="flat"
            class="rounded-lg px-6 font-weight-bold shadow-btn"
            @click="performLogout"
          >
            {{ $t("auth.logout_confirm_btn") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script setup>
import { ref, onMounted, onUnmounted, inject, computed } from "vue";
import { useRouter } from "vue-router";
import { useI18n } from "vue-i18n";
import { useLocale, useDisplay } from "vuetify";
import { useAuthStore } from "@/store/auth/LogIn";
import { useSettingsStore } from "@/store/Settings";
import axios from "@/axios";

const auth = useAuthStore();
const { can } = auth;
const settingsStore = useSettingsStore();
const { mobile: isMobile } = useDisplay();

const emitter = inject("emitter");
const { locale } = useI18n();
const { current } = useLocale();
const router = useRouter();

const drawer = ref(true);
const rail = ref(false);

const isRtl = computed(() => locale.value === "ar");
const layoutVars = computed(() => ({
  "--hover-translate": isRtl.value ? "-5px" : "5px",
}));

const toggleLanguage = () => {
  const newLang = locale.value === "ar" ? "en" : "ar";
  locale.value = newLang;
  current.value = newLang;
  localStorage.setItem("lang", newLang);
  document.documentElement.lang = newLang;
  document.documentElement.dir = newLang === "ar" ? "rtl" : "ltr";
};

const adminUser = ref({});
const defaultAvatar = "https://cdn-icons-png.flaticon.com/512/149/149071.png";

const getAvatarUrl = (path) => {
  if (!path) return defaultAvatar;
  if (path.startsWith("http")) return path;
  const baseUrl = process.env.VUE_APP_API_URL || "http://127.0.0.1:8000";
  return `${baseUrl}${path}`;
};
const messageList = ref([]);
const recentNotifications = ref([]);
const notificationsCount = ref(0);
const acknowledgedCount = ref(
  parseInt(localStorage.getItem("adminAckNotifCount") || "0")
);

const displayNotifCount = computed(() => {
  return Math.max(0, notificationsCount.value - acknowledgedCount.value);
});

const navbarSocials = computed(() => {
  const all = [
    {
      key: "facebook",
      icon: "mdi-facebook",
      color: "#1877F2",
      label: "Facebook",
      showKey: "showFacebook",
    },
    {
      key: "instagram",
      icon: "mdi-instagram",
      color: "#E4405F",
      label: "Instagram",
      showKey: "showInstagram",
    },
    {
      key: "youtube",
      icon: "youtube-svg",
      color: "#FF0000",
      label: "YouTube",
      showKey: "showYoutube",
    },
    {
      key: "tiktok",
      icon: "tiktok-svg",
      color: "#010101",
      label: "TikTok",
      showKey: "showTiktok",
    },
    {
      key: "twitter",
      icon: "mdi-twitter",
      color: "#1DA1F2",
      label: "Twitter",
      showKey: "showTwitter",
    },
  ];
  return all
    .filter((s) => settingsStore[s.showKey] && settingsStore[s.key])
    .map((s) => ({ ...s, href: settingsStore[s.key] }));
});

const onNotificationsMenuOpen = (isOpen) => {
  if (isOpen) {
    acknowledgedCount.value = notificationsCount.value;
    localStorage.setItem(
      "adminAckNotifCount",
      acknowledgedCount.value.toString()
    );
  }
};

const fetchHeaderData = async () => {
  try {
    const res = await axios.get("/admin/header-data");
    adminUser.value = res.data.user || {};
    messageList.value = res.data.unreadMessages || [];
    recentNotifications.value = res.data.recentNotifications || [];
    notificationsCount.value = res.data.notificationsCount || 0;

    // Adjust acknowledged count if total notifications decreased (e.g. order fulfilled)
    if (notificationsCount.value < acknowledgedCount.value) {
      acknowledgedCount.value = notificationsCount.value;
      localStorage.setItem(
        "adminAckNotifCount",
        acknowledgedCount.value.toString()
      );
    }
  } catch (error) {
    console.error("Error fetching header admin data:", error);
  }
};

const logoutDialog = ref(false);
const logoutAdmin = () => {
  logoutDialog.value = true;
};

const performLogout = async () => {
  logoutDialog.value = false;
  try {
    await axios.post("/logout");
  } catch (e) {
    console.error(e);
  } finally {
    localStorage.removeItem("auth_token");
    localStorage.removeItem("user");
    router.push("/Auth/LogIn");
  }
};

let polling = null;
onMounted(() => {
  fetchHeaderData();
  polling = setInterval(fetchHeaderData, 30000); // Poll every 30 seconds

  // Smart Polling: جلب البيانات فوراً عند العودة للتبويب
  document.addEventListener("visibilitychange", () => {
    if (!document.hidden) {
      fetchHeaderData();
    }
  });

  // استماع لتحديث البيانات عند قراءة رسائل أو إشعارات
  emitter.on("updateHeaderData", () => {
    fetchHeaderData();
  });
});

onUnmounted(() => {
  if (polling) clearInterval(polling);
  emitter.off("updateHeaderData");
});

onUnmounted(() => {
  if (polling) clearInterval(polling);
});
</script>

<style scoped>
.dashboard-layout {
  min-height: 100vh;
}
.sidebar-menu {
  background: linear-gradient(
    165deg,
    #18181b 0%,
    #27272a 45%,
    #1c1917 100%
  ) !important;
  color: #fafafa;
  border-inline-end: 1px solid rgba(249, 115, 22, 0.12);
}

/* =========================================
     تنسيق الـ Scrollbar بشكل أنيق
  ========================================= */
.sidebar-menu :deep(.v-navigation-drawer__content)::-webkit-scrollbar {
  width: 4px;
}
.sidebar-menu :deep(.v-navigation-drawer__content)::-webkit-scrollbar-track {
  background: transparent;
}
.sidebar-menu :deep(.v-navigation-drawer__content)::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.15);
  border-radius: 10px;
}
.sidebar-menu
  :deep(.v-navigation-drawer__content)::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 202, 40, 0.6);
}
.sidebar-menu :deep(.v-navigation-drawer__content) {
  scrollbar-width: thin;
  scrollbar-color: rgba(255, 255, 255, 0.15) transparent;
  padding-inline-end: 0px !important;
}

/* Pulse Animations */
@keyframes pulse-error {
  0% {
    box-shadow: 0 0 0 0 rgba(255, 82, 82, 0.7);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(255, 82, 82, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(255, 82, 82, 0);
  }
}
@keyframes pulse-info {
  0% {
    box-shadow: 0 0 0 0 rgba(33, 150, 243, 0.7);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(33, 150, 243, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(33, 150, 243, 0);
  }
}

.pulse-error :deep(.v-badge__badge) {
  animation: pulse-error 2s infinite;
}
.pulse-info :deep(.v-badge__badge) {
  animation: pulse-info 2s infinite;
}

/* =========================================
     Social Media Buttons in Navbar
  ========================================= */
.social-nav-btn {
  border-radius: 10px !important;
  transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
  color: #616161 !important;
}
.social-nav-btn:hover {
  transform: translateY(-3px) scale(1.08);
}
.youtube-btn:hover {
  background: rgba(255, 0, 0, 0.12) !important;
  color: #ff0000 !important;
  box-shadow: 0 4px 14px rgba(255, 0, 0, 0.2);
}
.tiktok-btn:hover {
  background: rgba(1, 1, 1, 0.08) !important;
  color: #010101 !important;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
}

/* =========================================
     تنسيق الأزرار وعناصر القائمة
  ========================================= */
.sidebar-menu :deep(.v-list-item-title),
.sidebar-menu :deep(.v-list-item__content) {
  color: #f4f4f5 !important;
  opacity: 1 !important;
  font-size: 11px !important;
}

.sidebar-menu :deep(.v-list-item__prepend) {
  margin-inline-end: 2px !important;
}
.sidebar-menu :deep(.v-list-item__prepend) :deep(.v-icon) {
  font-size: 18px !important;
}
.sidebar-menu :deep(.v-list-item__content) {
  padding-inline-start: 0px !important;
}

.menu-item {
  transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
  border-radius: 8px !important;
  margin-bottom: 4px !important;
  padding: 6px 10px !important;
  position: relative;
  overflow: hidden;
  min-height: 40px !important;
}

.menu-item :deep(.v-list-item-title) {
  color: #f4f4f5 !important;
  opacity: 1 !important;
}

.sidebar-menu .menu-item :deep(.v-icon) {
  color: rgba(255, 255, 255, 0.92) !important;
  opacity: 1 !important;
}

.menu-item::before {
  content: "";
  position: absolute;
  top: 0;
  inset-inline-start: 0;
  width: 0%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.03));
  transition: width 0.4s ease;
  z-index: 0;
}

.menu-item:hover::before {
  width: 100%;
}

.menu-item:hover {
  transform: translateX(var(--hover-translate, -5px));
  color: #fdba74 !important;
}

.menu-item:hover :deep(.v-icon) {
  color: #fdba74 !important;
  transform: scale(1.1);
  transition: transform 0.3s ease;
}

.v-list-item--active {
  background: linear-gradient(
    90deg,
    rgba(249, 115, 22, 0.18),
    rgba(249, 115, 22, 0.04)
  ) !important;
  box-shadow: 0 4px 15px rgba(249, 115, 22, 0.08) !important;
  color: #fdba74 !important;
}

.v-list-item--active::after {
  content: "";
  position: absolute;
  top: 50%;
  inset-inline-start: 0;
  transform: translateY(-50%);
  height: 50%;
  width: 4px;
  background-color: #f97316;
  border-radius: 4px;
  box-shadow: 0 0 10px rgba(249, 115, 22, 0.45);
}

.v-list-item--active :deep(.v-icon) {
  color: #fb923c !important;
  text-shadow: 0 0 10px rgba(249, 115, 22, 0.25);
}

/* =========================================
     النصوص الإضافية
  ========================================= */
.opacity-20 {
  opacity: 0.15;
}
.opacity-50 {
  opacity: 0.6;
  letter-spacing: 0.5px;
  font-size: 10px;
}
.v-list-item__content,
.v-list-item--nav .v-list-item-title {
  text-align: justify !important;
  text-align-last: justify !important;
  white-space: normal !important;
  font-weight: 700;
  z-index: 1;
  position: relative;
  letter-spacing: 0.2px;
}

.hover-row {
  transition: background-color 0.2s;
}
.hover-row:hover {
  background-color: #f8fafc;
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(var(--v-theme-primary), 0.2);
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(var(--v-theme-primary), 0.4);
}

/* Scrollable main content */
.dashboard-main {
  height: 100vh;
  overflow-y: auto !important;
  scroll-behavior: smooth;
}
</style>
