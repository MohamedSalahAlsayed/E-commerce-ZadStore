<template>
  <div>
    <v-navigation-drawer
      v-model="mobileMenu"
      temporary
      :location="isRtl ? 'right' : 'left'"
      class="mobile-drawer"
      width="300"
    >
      <div class="user-drawer-header pa-4 mb-2">
        <div class="d-flex align-center gap-3 mb-3">
          <v-avatar size="50" border>
            <v-img :src="userData.avatar" cover></v-img>
          </v-avatar>
          <div>
            <div class="font-weight-bold text-subtitle-1">
              {{ userData.name }}
            </div>
            <div class="text-caption text-grey">{{ userTier }}</div>
          </div>
        </div>
        <v-btn
          size="small"
          variant="outlined"
          block
          color="primary"
          prepend-icon="mdi-account-edit"
          @click="
            $router.push('/User/UserProfile/EditProfilePage');
            mobileMenu = false;
          "
        >
          {{ $t("nav.edit_profile") }}
        </v-btn>
      </div>

      <v-divider></v-divider>

      <v-list density="compact">
        <v-list-subheader class="text-uppercase font-weight-bold">{{
          $t("nav.activity")
        }}</v-list-subheader>

        <v-list-group value="Notifications">
          <template v-slot:activator="{ props }">
            <v-list-item v-bind="props" color="primary">
              <template v-slot:prepend>
                <v-icon icon="mdi-bell-outline" color="error"></v-icon>
              </template>
              <v-list-item-title>{{
                $t("nav.notifications")
              }}</v-list-item-title>
              <template v-slot:append>
                <v-chip size="x-small" color="error" variant="flat">{{
                  notificationList.length
                }}</v-chip>
              </template>
            </v-list-item>
          </template>
          <v-list-item
            v-for="(note, i) in notificationList"
            :key="i"
            :subtitle="note.time"
            :title="note.title"
          >
            <template v-slot:prepend
              ><v-icon size="small" :icon="note.icon"></v-icon
            ></template>
          </v-list-item>
        </v-list-group>

        <v-list-item
          value="wishlist"
          @click="
            $router.push('/User/FavourateProduct');
            mobileMenu = false;
          "
          color="primary"
        >
          <template v-slot:prepend>
            <v-icon icon="mdi-heart-outline"></v-icon>
          </template>
          <v-list-item-title>{{ $t("nav.wishlist") }}</v-list-item-title>
        </v-list-item>
      </v-list>

      <v-divider></v-divider>

      <v-list density="compact" nav>
        <v-list-subheader class="text-uppercase font-weight-bold">
          {{ $t("nav.menu") }}
        </v-list-subheader>

        <template v-for="(item, i) in menuItems" :key="i">
          <v-list-group
            v-if="item.children && item.children.length > 0"
            :value="item.text"
          >
            <template v-slot:activator="{ props }">
              <v-list-item v-bind="props" color="primary" rounded="lg">
                <template v-slot:prepend>
                  <v-icon icon="mdi-circle-small" size="small"></v-icon>
                </template>
                <v-list-item-title class="font-weight-medium">
                  {{ item.text }}
                </v-list-item-title>
              </v-list-item>
            </template>

            <v-list-item
              v-for="(child, j) in item.children"
              :key="j"
              :value="child.text"
              :to="child.link"
              color="primary"
              rounded="lg"
              class="ms-4"
            >
              <template v-slot:prepend>
                <v-icon icon="mdi-minus" size="small"></v-icon>
              </template>
              <v-list-item-title class="font-weight-medium">
                {{ child.text }}
              </v-list-item-title>
            </v-list-item>
          </v-list-group>

          <v-list-item
            v-else
            :value="item.text"
            :to="item.link"
            color="primary"
            rounded="lg"
          >
            <template v-slot:prepend>
              <v-icon icon="mdi-circle-small" size="small"></v-icon>
            </template>
            <v-list-item-title class="font-weight-medium">
              {{ item.text }}
            </v-list-item-title>
          </v-list-item>
        </template>
      </v-list>
      <div class="pa-4">
        <v-select
          v-model="currentLang"
          :items="availableLangs"
          item-title="name"
          item-value="code"
          :label="$t('language')"
          variant="outlined"
          density="compact"
          return-object
          class="mb-2"
        >
          <template v-slot:selection="{ item }">
            <div class="d-flex align-center">
              <img :src="item.raw.flag" width="20" class="ml-2" />
              {{ item.raw.name }}
            </div>
          </template>
          <template v-slot:item="{ props, item }">
            <v-list-item
              v-bind="props"
              :prepend-avatar="item.raw.flag"
              :title="item.raw.name"
            ></v-list-item>
          </template>
        </v-select>

        <!-- Mobile Theme Switcher -->
        <v-select
          label="المظهر / Theme"
          :items="availableThemes"
          item-title="name"
          item-value="value"
          variant="outlined"
          density="compact"
          class="mb-2"
          @update:model-value="changeTheme"
        >
          <template v-slot:item="{ props, item }">
            <v-list-item v-bind="props">
              <template v-slot:prepend>
                <v-icon :color="item.raw.color" size="small" class="me-2"
                  >mdi-circle</v-icon
                >
              </template>
            </v-list-item>
          </template>
        </v-select>

        <v-btn
          block
          color="error"
          variant="tonal"
          prepend-icon="mdi-logout"
          class="mt-2"
          @click="$router.push('/Auth/LogOut')"
          >{{ $t("logout") }}</v-btn
        >
      </div>
    </v-navigation-drawer>

    <v-app-bar flat height="40" class="main-navbar" absolute>
      <v-container fluid class="d-flex align-center justify-space-between py-0">
        <div class="d-none d-md-flex align-center gap-6 right-actions">
          <div
            class="d-flex align-center justify-between gap-2 text-white phone-info"
          >
            <span class="d-flex align-center">
              <v-icon size="18" class="ml-2">mdi-phone</v-icon>
              <span
                class="small-text font-weight-bold"
                style="direction: ltr"
                >{{ settingsStore.phone || "+20 123 456 7890" }}</span
              >
            </span>

            <v-divider
              vertical
              class="mx-3"
              color="white"
              style="opacity: 0.4; height: 18px"
            ></v-divider>

            <div class="d-flex align-center gap-1">
              <v-btn
                v-if="settingsStore.facebook"
                icon
                size="x-small"
                variant="text"
                color="white"
                style="opacity: 0.8"
                :href="settingsStore.facebook"
                target="_blank"
                ><v-icon size="18">mdi-facebook</v-icon></v-btn
              >
              <v-btn
                v-if="settingsStore.instagram"
                icon
                size="x-small"
                variant="text"
                color="white"
                style="opacity: 0.8"
                :href="settingsStore.instagram"
                target="_blank"
                ><v-icon size="18">mdi-instagram</v-icon></v-btn
              >
              <v-btn
                v-if="settingsStore.twitter"
                icon
                size="x-small"
                variant="text"
                color="white"
                style="opacity: 0.8"
                :href="settingsStore.twitter"
                target="_blank"
                ><v-icon size="18">mdi-twitter</v-icon></v-btn
              >
              <v-btn
                v-if="settingsStore.tiktok"
                icon
                size="x-small"
                variant="text"
                color="white"
                style="opacity: 0.8"
                :href="settingsStore.tiktok"
                target="_blank"
                ><v-icon size="18">mdi-music-note</v-icon></v-btn
              >
            </div>

            <v-btn
              v-if="authStore.currentUser?.role === 'admin'"
              @click="$router.push('/Dashboard/AdminDashboard')"
              class="mr-6 text-none font-weight-bold"
              variant="tonal"
              size="small"
              color="white"
              >{{ $t("nav.administration_panel") }}
              <v-icon class="ml-1" size="small"
                >mdi-shield-crown-outline</v-icon
              ></v-btn
            >
          </div>
        </div>

        <v-app-bar-nav-icon
          class="d-md-none text-white"
          variant="text"
          density="compact"
          @click.stop="mobileMenu = !mobileMenu"
        ></v-app-bar-nav-icon>
      </v-container>
    </v-app-bar>

    <v-app-bar
      :elevation="isScrolled ? 4 : 0"
      color="primary"
      height="65"
      class="px-2 px-md-4 position-fixed transition-app-bar border-bottom"
      :style="{ top: isScrolled ? '0px' : '40px', zIndex: 999 }"
    >
      <div class="d-flex justify-space-between align-center w-100">
        <div
          class="d-flex align-center cursor-pointer ml-4"
          @click="$router.push('/')"
        >
          <img
            v-if="settingsStore.logoPreview"
            :src="settingsStore.logoPreview"
            alt="Store Logo"
            height="35"
            style="object-fit: contain; margin-left: 8px"
          />
          <h2 class="font-weight-bold text-white logo-text" v-else>
            {{ settingsStore.storeName || "ZadStore+" }}
          </h2>
        </div>

        <div
          class="d-none d-md-flex align-center justify-start gap-items flex-grow-1"
        >
          <div v-for="(item, i) in menuItems" :key="i" class="menu-item">
            <div v-if="item.badge" class="badge-container">
              <span :class="['custom-badge', `badge-${item.badge.color}`]">
                {{ item.badge.text }}
                <span
                  :class="['badge-arrow', `arrow-${item.badge.color}`]"
                ></span>
              </span>
            </div>

            <v-menu
              v-if="item.children"
              open-on-hover
              open-delay="0"
              activator="parent"
              offset-y
            >
              <template v-slot:activator="{ props }">
                <span
                  class="nav-text cursor-pointer d-flex align-center"
                  v-bind="props"
                >
                  {{ item.text }}
                  <v-icon
                    icon="mdi-chevron-down"
                    size="small"
                    class="ms-1"
                  ></v-icon>
                </span>
              </template>

              <v-list density="compact" elevation="2">
                <v-list-item
                  v-for="(child, c) in item.children"
                  :key="c"
                  :to="child.link"
                  link
                  color="primary"
                >
                  <v-list-item-title>{{ child.text }}</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>

            <span
              v-else
              class="nav-text cursor-pointer"
              @click="$router.push(item.link)"
            >
              {{ item.text }}
            </span>
          </div>
        </div>

        <div class="d-flex align-center justify-end gap-1">
          <div class="d-flex align-center">
            <div :class="['search-wrapper', { 'search-open': searchOpen }]">
              <input
                ref="searchInput"
                v-model="searchQuery"
                @keyup.enter="performSearch"
                type="text"
                :placeholder="$t('nav.search_placeholder')"
                class="custom-search-input"
              />
            </div>
            <v-btn icon color="white" variant="text" @click="toggleSearch">
              <v-icon>{{ searchOpen ? "mdi-close" : "mdi-magnify" }}</v-icon>
            </v-btn>
          </div>

          <div class="d-none d-md-flex align-center">
            <v-btn
              icon
              color="white"
              to="/User/FavourateProduct"
              variant="text"
            >
              <v-icon>mdi-heart-outline</v-icon>
              <v-tooltip activator="parent" location="bottom">{{
                $t("nav.wishlist")
              }}</v-tooltip>
            </v-btn>

            <v-menu
              location="bottom end"
              :offset="14"
              transition="slide-y-transition"
              width="300"
            >
              <template v-slot:activator="{ props }">
                <v-btn icon color="white" variant="text" v-bind="props">
                  <v-badge
                    :content="unreadNotificationCount"
                    v-if="unreadNotificationCount > 0"
                    color="error"
                    offset-x="5"
                    offset-y="5"
                  >
                    <v-icon>mdi-bell-outline</v-icon>
                  </v-badge>
                  <v-icon v-else>mdi-bell-outline</v-icon>
                </v-btn>
              </template>
              <v-card class="rounded-lg shadow-lg">
                <v-card-title
                  class="text-subtitle-1 font-weight-bold pa-3 border-b d-flex justify-space-between align-center"
                >
                  <span>{{ $t("nav.notifications") }}</span>
                  <v-btn
                    size="x-small"
                    variant="text"
                    color="primary"
                    @click="markAllAsRead"
                    >{{ $t("nav.mark_as_read") }}</v-btn
                  >
                </v-card-title>
                <v-list lines="two" density="compact" class="pa-0">
                  <v-list-item
                    v-for="(note, i) in notificationList"
                    :key="i"
                    :value="i"
                  >
                    <template v-slot:prepend>
                      <v-avatar color="grey-lighten-4" size="35">
                        <v-icon :color="note.color" size="small">{{
                          note.icon
                        }}</v-icon>
                      </v-avatar>
                    </template>
                    <v-list-item-title class="text-body-2 font-weight-medium">{{
                      note.title
                    }}</v-list-item-title>
                    <v-list-item-subtitle class="text-caption">{{
                      note.subtitle
                    }}</v-list-item-subtitle>
                  </v-list-item>
                  <v-list-item
                    v-if="notificationList.length === 0"
                    class="text-center py-4"
                  >
                    <v-list-item-title class="text-grey">{{
                      $t("nav.no_notifications")
                    }}</v-list-item-title>
                  </v-list-item>
                </v-list>
                <v-divider></v-divider>
                <v-btn
                  block
                  variant="text"
                  color="primary"
                  class="text-capitalize"
                  @click="$router.push('/User/UserProfile/NotificationPage')"
                  >{{ $t("dashboard.view_all") }}</v-btn
                >
              </v-card>
            </v-menu>

            <!-- Messages Counter -->
            <v-btn
              icon
              color="white"
              variant="text"
              to="/User/UserProfile/MessagePage"
            >
              <v-badge
                :content="unreadMessageCount"
                v-if="unreadMessageCount > 0"
                color="info"
                offset-x="5"
                offset-y="5"
                class="pulse-info"
              >
                <v-icon>mdi-message-outline</v-icon>
              </v-badge>
              <v-icon v-else>mdi-message-outline</v-icon>
              <v-tooltip activator="parent" location="bottom">{{
                $t("nav.messages")
              }}</v-tooltip>
            </v-btn>
          </div>

          <v-menu
            location="bottom end"
            transition="scale-transition"
            content-class="d-none d-md-block"
          >
            <template v-slot:activator="{ props }">
              <v-btn
                icon
                color="white"
                variant="text"
                v-bind="props"
                class="ml-2 d-none d-md-flex"
              >
                <v-icon>mdi-palette-outline</v-icon>
              </v-btn>
            </template>
            <v-list density="compact" nav elevation="3" class="rounded-lg">
              <v-list-item
                v-for="themeOption in availableThemes"
                :key="themeOption.value"
                @click="changeTheme(themeOption.value)"
                :active="theme.global.name.value === themeOption.value"
                color="primary"
              >
                <template v-slot:prepend>
                  <v-icon :color="themeOption.color" size="small" class="me-3"
                    >mdi-circle</v-icon
                  >
                </template>
                <v-list-item-title class="font-weight-bold">{{
                  themeOption.name
                }}</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>

          <v-btn
            icon
            color="white"
            variant="text"
            @click="toggleCart"
            class="ml-2"
          >
            <v-badge
              :content="cartStore.CartItem.length"
              v-if="cartStore.CartItem.length > 0"
              color="#E53935"
              offset-x="5"
              offset-y="5"
            >
              <v-icon>mdi-cart-outline</v-icon>
            </v-badge>
            <v-icon v-else>mdi-cart-outline</v-icon>
          </v-btn>

          <v-divider
            vertical
            color="white"
            class="mx-2 d-none d-md-flex"
            style="opacity: 0.3; height: 25px"
          ></v-divider>

          <v-menu
            location="bottom end"
            transition="scale-transition"
            :offset="10"
          >
            <template v-slot:activator="{ props }">
              <div
                class="d-flex align-center cursor-pointer profile-trigger pa-1 rounded-pill"
                v-bind="props"
                v-ripple
              >
                <v-avatar size="35" class="border-sm">
                  <v-img :src="userData.avatar" cover alt="User"></v-img>
                </v-avatar>
                <div class="d-none d-lg-block mr-2 ml-1 text-white">
                  <div
                    class="text-subtitle-2 font-weight-bold"
                    style="line-height: 1"
                  >
                    {{ userData.name }}
                  </div>
                  <div
                    class="text-caption"
                    style="opacity: 0.7; font-size: 10px"
                  >
                    {{ $t("nav.my_account") }}
                  </div>
                </div>
                <v-icon color="white" size="small" class="d-none d-lg-block"
                  >mdi-chevron-down</v-icon
                >
              </div>
            </template>
            <v-list
              width="200"
              density="compact"
              elevation="3"
              class="rounded-lg mt-2"
            >
              <v-list-item :title="userData.name" :subtitle="userTier">
                <template v-slot:prepend
                  ><v-avatar size="30"
                    ><v-img :src="userData.avatar"></v-img></v-avatar
                ></template>
              </v-list-item>
              <v-divider class="my-1"></v-divider>
              <v-list-item
                value="profile"
                @click="router.push({ path: '/User/UserProfile/ProfilePage' })"
                prepend-icon="mdi-account-circle-outline"
                :title="$t('profile')"
              ></v-list-item>
              <v-list-item
                value="edit"
                @click="
                  router.push({ path: '/User/UserProfile/EditProfilePage' })
                "
                prepend-icon="mdi-cog-outline"
                :title="$t('nav.edit_profile')"
              ></v-list-item>
              <v-list-item
                value="orders"
                @click="router.push({ path: '/User/ControlPannel/OrdersPage' })"
                prepend-icon="mdi-package-variant-closed"
                :title="$t('nav.orders')"
              ></v-list-item>
              <v-divider class="my-1"></v-divider>
              <v-list-item
                value="logout"
                @click="router.push({ path: '/Auth/LogOut' })"
                prepend-icon="mdi-logout"
                :title="$t('logout')"
                color="error"
              ></v-list-item>
            </v-list>
          </v-menu>

          <v-menu location="bottom end" transition="scale-transition">
            <template v-slot:activator="{ props }">
              <v-btn
                color="white"
                variant="text"
                class="text-capitalize px-1 mr-1"
                v-bind="props"
                rounded
                style="min-width: auto"
              >
                <v-avatar size="22" class="ml-1">
                  <v-img :src="currentLang.flag" cover></v-img>
                </v-avatar>
                <span class="d-none d-sm-inline">{{ currentLang.code }}</span>
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
        </div>
      </div>
    </v-app-bar>
  </div>

  <!-- الروبوت الذكي والدعم الفني -->
  <v-fade-transition>
    <div
      v-if="authStore.isAuthenticated"
      class="support-fab-container d-flex flex-column align-end"
    >
      <!-- الشات بوت الذكي -->
      <AIChatbot />

      <!-- زر الدعم الفني العائم -->
      <v-btn
        color="primary"
        class="support-fab elevation-8"
        size="large"
        @click="router.push('/User/UserProfile/MessagePage')"
        rounded="pill"
      >
        <div class="d-flex align-center px-1">
          <span class="font-weight-bold ml-2 support-text">{{
            $t("nav.technical_support")
          }}</span>
          <v-badge
            v-if="unreadMessageCount > 0"
            :content="unreadMessageCount"
            color="error"
            offset-x="8"
            offset-y="-2"
            class="message-pulsate"
          >
            <v-icon size="28">mdi-chat-processing</v-icon>
          </v-badge>
          <v-icon v-else size="28">mdi-chat-processing-outline</v-icon>
        </div>
      </v-btn>
    </div>
  </v-fade-transition>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick, inject, computed } from "vue";
import { useRouter } from "vue-router";
import { useI18n } from "vue-i18n";
import { useLocale, useTheme } from "vuetify";
import { ProductModule } from "@/store/Products";
import { AddInCart } from "@/store/Cart";
import { useSettingsStore } from "@/store/Settings";
import { useAuthStore } from "@/store/auth/LogIn";
import AIChatbot from "./AIChatbot.vue";

const { t, locale } = useI18n();
const { current: vuetifyLocale } = useLocale();
const router = useRouter();
const emitter = inject("emitter");
const productStore = ProductModule();
const cartStore = AddInCart();
const settingsStore = useSettingsStore();
const authStore = useAuthStore();

const menuItems = computed(() => [
  { text: t("nav.home"), link: "/", badge: null },
  { text: t("nav.products"), link: "/User/ProductsPage", badge: null },
  {
    text: t("nav.orders"),
    link: "/User/ControlPannel/OrdersPage",
    badge: null,
  },
  { text: t("nav.add_order"), link: "/User/AddOrder", badge: null },
]);

const mobileMenu = ref(false);
const searchOpen = ref(false);
const searchQuery = ref("");
const isScrolled = ref(false);
const searchInput = ref(null);

const isRtl = computed(() => locale.value === "ar");
import api from "@/axios";

// --- User Data ---
const userData = computed(() => ({
  name: authStore.currentUser?.name || "زائر",
  avatar: authStore.currentUser?.avatar
    ? `http://localhost:8000${authStore.currentUser.avatar}`
    : "https://i.pravatar.cc/150?img=12",
}));
const userTier = computed(() => {
  if (!authStore.isAuthenticated) return "زائر";
  const role = authStore.currentUser?.role;
  if (role === "admin") return "مدير النظام";
  if (role === "moderator") return "مشرف";
  // Tier based on user metadata (backend should ideally provide this)
  const orderCount = authStore.currentUser?.orders_count || 0;
  if (orderCount >= 20) return "عضو ذهبي";
  if (orderCount >= 5) return "عضو فضي";
  return "عضو برونزي";
});

// --- Dynamic Notification Data ---
const notificationList = ref([]);
const unreadNotificationCount = ref(0);
const unreadMessageCount = ref(0);
let pollInterval = null;

const fetchNotifications = async () => {
  if (!authStore.isAuthenticated) return;
  try {
    const res = await api.get("/user/header-data");
    unreadNotificationCount.value = res.data.unreadNotificationsCount;
    unreadMessageCount.value = res.data.unreadMessagesCount;

    notificationList.value = res.data.recentNotifications.map((note) => ({
      id: note.id,
      title: note.title,
      subtitle: note.description,
      icon: note.icon || "mdi-bell-outline",
      color: note.color || "primary",
      is_read: note.is_read,
      time: new Date(note.created_at).toLocaleString("ar-EG"),
    }));
  } catch (error) {
    console.error("Failed to load header data", error);
  }
};

const markAllAsRead = async () => {
  if (!authStore.isAuthenticated) return;
  try {
    await api.post("/user/notifications/mark-read");
    notificationList.value.forEach((note) => {
      note.is_read = true;
    });
    unreadNotificationCount.value = 0;
  } catch (error) {
    console.error("Failed to mark notifications as read", error);
  }
};

// --- Language & Theme Logic ---
const theme = useTheme();

const availableLangs = [
  { code: "en", name: "English", flag: "https://flagcdn.com/w40/us.png" },
  { code: "ar", name: "العربية", flag: "https://flagcdn.com/w40/eg.png" },
];

const currentLang = computed(() => {
  return (
    availableLangs.find((l) => l.code === locale.value) || availableLangs[1]
  );
});

const availableThemes = [
  { name: "برتقالي - Orange", value: "orangeTheme", color: "#f97316" },
  { name: "أزرق - Blue", value: "blueTheme", color: "#2196F3" },
  { name: "أخضر المرج - Emerald", value: "greenTheme", color: "#10b981" },
  { name: "بنفسجي داكن - Purple", value: "purpleTheme", color: "#8b5cf6" },
  { name: "أحمر نابض - Red", value: "redTheme", color: "#ef4444" },
];

const changeLang = (lang) => {
  locale.value = lang.code;
  vuetifyLocale.value = lang.code;
  localStorage.setItem("lang", lang.code);
  document.documentElement.lang = lang.code;
  document.documentElement.dir = lang.code === "ar" ? "rtl" : "ltr";
};

const changeTheme = (themeName) => {
  theme.global.name.value = themeName;
  localStorage.setItem("selectedTheme", themeName);

  // If it's a fixed theme, remove custom overrides
  if (themeName !== "orangeTheme") {
    document.documentElement.style.removeProperty("--v-theme-primary");
    document.body.style.removeProperty("--v-theme-primary");
  }
};

// Methods
const toggleCart = () => emitter.emit("toggleCart");
const toggleSearch = async () => {
  searchOpen.value = !searchOpen.value;
  if (searchOpen.value) {
    await nextTick();
    searchInput.value?.focus();
  }
};
const performSearch = () => {
  if (searchQuery.value.trim() !== "") {
    router.push({ path: "/search", query: { q: searchQuery.value } });
    searchQuery.value = "";
    searchOpen.value = false;
  }
};
const handleScroll = () => {
  isScrolled.value = window.scrollY > 10;
};

// Lifecycle
onMounted(async () => {
  window.addEventListener("scroll", handleScroll);

  const savedTheme = localStorage.getItem("selectedTheme");
  if (savedTheme) theme.global.name.value = savedTheme;

  // Fetch dynamic categories and settings
  await Promise.all([
    settingsStore.fetchSettings(),
    productStore.getCategories(),
    fetchNotifications(),
  ]);

  // Smart Polling: يتوقف عندما التبويب مخفي ويبدأ فوراً عند العودة
  pollInterval = setInterval(fetchNotifications, 15000); // Reduced to 15s for better reactivity

  // Page Visibility API — نوقف polling عندما المستخدم مش شايف الصفحة
  document.addEventListener("visibilitychange", () => {
    if (document.hidden) {
      clearInterval(pollInterval);
    } else {
      fetchNotifications();
      pollInterval = setInterval(fetchNotifications, 15000);
    }
  });

  // استماع لتحديث البيانات من صفحات أخرى (الإشعارات والرسائل)
  emitter.on("updateHeaderData", () => {
    fetchNotifications();
  });
});

onUnmounted(() => {
  window.removeEventListener("scroll", handleScroll);
  if (pollInterval) clearInterval(pollInterval);
  emitter.off("updateHeaderData");
});
</script>

<style scoped>
/* Main Colors */
.main-navbar {
  /* Using primary colors handled by vuetify instead of hardcoded dark blue, but if need a fallback: */
  background-color: rgb(var(--v-theme-secondary)) !important;
}
.logo-text {
  font-family: serif;
  font-style: italic;
  font-size: 1.6rem;
}

/* Profile Trigger */
.profile-trigger {
  transition: background 0.3s;
}
.profile-trigger:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

/* Transitions */
.transition-app-bar {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Search Animation */
.search-wrapper {
  width: 0;
  opacity: 0;
  overflow: hidden;
  transition: width 0.4s ease, opacity 0.3s ease;
}
.search-wrapper.search-open {
  width: 200px;
  opacity: 1;
  margin-right: 8px;
}
.custom-search-input {
  width: 100%;
  background: rgba(255, 255, 255, 0.15);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 20px;
  padding: 6px 15px;
  color: white;
  outline: none;
}
.custom-search-input:focus {
  background: rgba(255, 255, 255, 0.25);
  border-color: #ffca28;
}

/* Navigation Items */
.menu-item {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  height: 100%;
  margin: 0 12px;
}
.nav-text {
  color: white;
  font-weight: 700;
  font-size: 14px;
  transition: 0.2s;
}
.menu-item:hover .nav-text {
  color: #ffca28;
}

/* Badges */
.badge-container {
  position: absolute;
  top: 12px;
  width: 100%;
  display: flex;
  justify-content: center;
}
.custom-badge {
  font-size: 9px;
  font-weight: bold;
  color: white;
  padding: 2px 5px;
  border-radius: 2px;
  position: relative;
  text-transform: uppercase;
}
.badge-blue {
  background-color: #00bcd4;
}
.badge-orange {
  background-color: #ff9800;
}
.badge-arrow {
  position: absolute;
  bottom: -4px;
  left: 50%;
  transform: translateX(-50%);
  border-top: 4px solid;
  border-left: 3px solid transparent;
  border-right: 3px solid transparent;
}
.arrow-blue {
  border-top-color: #00bcd4;
}
.arrow-orange {
  border-top-color: #ff9800;
}

/* Mobile Sidebar Styling */
.user-drawer-header {
  background-color: #f5f5f5;
  border-bottom: 1px solid #e0e0e0;
}

/* Responsive */
/* Badges */
.pulse-error :deep(.v-badge__badge) {
  animation: pulse-error 2s infinite;
}
.pulse-info :deep(.v-badge__badge) {
  animation: pulse-info 2s infinite;
}

@keyframes pulse-error {
  0% {
    box-shadow: 0 0 0 0 rgba(229, 57, 53, 0.7);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(229, 57, 53, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(229, 57, 53, 0);
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

@media (max-width: 960px) {
  .gap-items {
    gap: 15px;
  }
  .search-wrapper.search-open {
    width: 140px;
  }
}
.border-bottom {
  border-bottom: 1px solid rgb(255 255 255);
}
.v-list-item__content {
  text-align: inherit !important;
}
.v-list-group__items .v-list-item {
  padding-inline-start: calc(-44px + var(--indent-padding)) !important;
}
.v-list--nav .v-list-item:not(:first-child),
.v-list--nav .v-list-group:not(:first-child) > .v-list-item,
.v-list--nav .v-list-group__items > .v-list-item,
.v-list--nav .v-list-group__items > .v-list-group {
  margin-top: 4px;
  text-align: inherit !important;
}

/* ================= الدعم الفني العائم (Premium FAB) ================= */
.support-fab-container {
  position: fixed;
  bottom: 25px;
  inset-inline-end: 25px;
  z-index: 999;
  gap: 12px;
}

.support-fab {
  height: 52px !important;
  padding: 0 20px !important;
  background: linear-gradient(
    135deg,
    rgb(var(--v-theme-primary)) 0%,
    #1a237e 100%
  ) !important;
  border: 2px solid rgba(255, 255, 255, 0.15) !important;
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
}

.support-fab:hover {
  transform: translateY(-5px) scale(1.05);
  box-shadow: 0 12px 25px rgba(var(--v-theme-primary), 0.4) !important;
  background: linear-gradient(
    135deg,
    #1a237e 0%,
    rgb(var(--v-theme-primary)) 100%
  ) !important;
}

.support-text {
  font-family: "Cairo", sans-serif;
  font-size: 14px;
  letter-spacing: 0;
}

/* حركة النبض للرسايل الجديدة */
.message-pulsate :deep(.v-badge__wrapper) {
  animation: pulse-animation 1.5s infinite;
}

@keyframes pulse-animation {
  0% {
    transform: scale(1);
    box-shadow: 0 0 0 0 rgba(var(--v-theme-error), 0.7);
  }
  70% {
    transform: scale(1.15);
    box-shadow: 0 0 0 10px rgba(var(--v-theme-error), 0);
  }
  100% {
    transform: scale(1);
    box-shadow: 0 0 0 0 rgba(var(--v-theme-error), 0);
  }
}

@media (max-width: 600px) {
  .support-fab-container {
    bottom: 20px;
    inset-inline-end: 15px;
  }
  .support-text {
    display: none; /* إخفاء النص في الموبايل لتقليل الزحمة */
  }
  .support-fab {
    width: 52px !important;
    padding: 0 !important;
    border-radius: 50% !important;
  }
  .support-fab :deep(.v-btn__content) {
    justify-content: center;
  }
}
</style>
