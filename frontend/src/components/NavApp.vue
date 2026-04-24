<template>
  <div>
    <v-navigation-drawer
      v-model="mobileMenu"
      temporary
      :location="isRtl ? 'right' : 'left'"
      class="mobile-drawer"
      width="300"
    >
      <div class="drawer-premium-wrapper d-flex flex-column h-100">
        <!-- Modern Profile Header (Authenticated) -->
        <div v-if="authStore.isAuthenticated" class="user-drawer-card pa-6">
          <div class="d-flex justify-end mb-n6">
            <v-btn
              icon="mdi-close"
              variant="text"
              color="white"
              size="small"
              @click="mobileMenu = false"
            ></v-btn>
          </div>
          <div class="d-flex align-center gap-4 pt-4">
            <v-avatar size="64" class="profile-avatar-premium shadow-lg">
              <v-img :src="userData.avatar" cover></v-img>
            </v-avatar>
            <div class="user-info-text text-white">
              <div class="text-h6 font-weight-black lh-1 mb-1">
                {{ userData.name }}
              </div>
              <v-chip
                size="x-small"
                color="white"
                variant="tonal"
                class="font-weight-bold cursor-pointer"
                label
                @click="
                  ['admin', 'moderator'].includes(authStore.currentUser?.role)
                    ? $router.push('/Dashboard/AdminDashboard')
                    : null
                "
              >
                {{ userTier }}
              </v-chip>
            </div>
          </div>
          <v-btn
            size="small"
            variant="flat"
            block
            color="white"
            class="mt-6 font-weight-black rounded-lg text-primary"
            prepend-icon="mdi-account-circle"
            @click="
              $router.push('/User/UserProfile/EditProfilePage');
              mobileMenu = false;
            "
          >
            {{ $t("profile") }}
          </v-btn>
        </div>

        <!-- Guest Welcome Header -->
        <div v-else class="user-drawer-card pa-6">
          <div class="d-flex justify-end mb-n6">
            <v-btn
              icon="mdi-close"
              variant="text"
              color="white"
              size="small"
              @click="mobileMenu = false"
            ></v-btn>
          </div>
          <div class="d-flex align-center gap-4 pt-4 mb-4">
            <v-avatar size="64" color="white-opacity-20" class="shadow-lg">
              <v-icon color="white" size="32">mdi-account-outline</v-icon>
            </v-avatar>
            <div class="user-info-text text-white">
              <div class="text-h6 font-weight-black lh-1 mb-1">
                {{ $t("nav.guest") }}
              </div>
              <div class="text-caption opacity-80">
                {{ $t("nav.welcome_guest") || "أهلاً بك في متجرنا" }}
              </div>
            </div>
          </div>
          <div class="d-flex gap-2">
            <v-btn
              size="small"
              variant="flat"
              block
              color="white"
              class="font-weight-black rounded-lg text-primary flex-grow-1"
              @click="
                $router.push('/Auth/LogIn');
                mobileMenu = false;
              "
            >
              {{ $t("guest_nav.login") }}
            </v-btn>
            <v-btn
              size="small"
              variant="tonal"
              block
              color="white"
              class="font-weight-black rounded-lg flex-grow-1"
              @click="
                $router.push('/Auth/RegisterNow');
                mobileMenu = false;
              "
            >
              {{ $t("guest_nav.register") }}
            </v-btn>
          </div>
        </div>

        <v-divider class="border-opacity-10"></v-divider>

        <!-- Dynamic Content List -->
        <v-list class="px-2" density="compact" nav>
          <v-list-subheader
            class="text-uppercase font-weight-bold opacity-60 ps-4 pt-4"
          >
            {{ $t("nav.activity") }}
          </v-list-subheader>

          <v-list-item
            v-if="authStore.isAuthenticated"
            value="notifications"
            color="primary"
            class="rounded-xl mb-1"
            @click="
              $router.push('/User/UserProfile/NotificationPage');
              mobileMenu = false;
            "
          >
            <template v-slot:prepend>
              <v-icon icon="mdi-bell-outline" color="primary"></v-icon>
            </template>
            <v-list-item-title class="font-weight-bold">{{
              $t("nav.notifications")
            }}</v-list-item-title>
            <template v-slot:append>
              <v-chip
                size="x-small"
                color="error"
                variant="flat"
                class="font-weight-bold"
              >
                {{ notificationList.length }}
              </v-chip>
            </template>
          </v-list-item>

          <v-list-item
            v-if="authStore.isAuthenticated"
            value="wishlist"
            @click="
              $router.push('/User/FavourateProduct');
              mobileMenu = false;
            "
            color="primary"
            class="rounded-xl mb-1"
          >
            <template v-slot:prepend>
              <v-icon icon="mdi-heart-outline" color="error"></v-icon>
            </template>
            <v-list-item-title class="font-weight-bold">{{
              $t("nav.wishlist")
            }}</v-list-item-title>
          </v-list-item>

          <v-list-item
            v-if="isAdmin && authStore.isAuthenticated"
            @click="
              $router.push('/Dashboard/AdminDashboard');
              mobileMenu = false;
            "
            color="primary"
            class="rounded-xl mb-1 admin-item-premium"
          >
            <template v-slot:prepend>
              <v-icon icon="mdi-shield-crown-outline" color="warning"></v-icon>
            </template>
            <v-list-item-title class="font-weight-black">{{
              $t("nav.administration_panel")
            }}</v-list-item-title>
          </v-list-item>

          <v-divider class="my-4 border-opacity-10"></v-divider>

          <v-list-subheader
            class="text-uppercase font-weight-bold opacity-60 ps-4"
          >
            {{ $t("nav.menu") }}
          </v-list-subheader>

          <template v-for="(item, i) in menuItems" :key="i">
            <v-list-group
              v-if="item.children && item.children.length > 0"
              :value="item.text"
            >
              <template v-slot:activator="{ props }">
                <v-list-item
                  v-bind="props"
                  color="primary"
                  class="rounded-xl mb-1"
                >
                  <template v-slot:prepend>
                    <v-icon
                      :icon="item.icon || 'mdi-circle-medium'"
                      color="primary"
                    ></v-icon>
                  </template>
                  <v-list-item-title class="font-weight-bold">{{
                    item.text
                  }}</v-list-item-title>
                </v-list-item>
              </template>

              <v-list-item
                v-for="(child, j) in item.children"
                :key="j"
                :to="child.link"
                color="primary"
                class="rounded-xl mb-1 ps-8"
              >
                <v-list-item-title class="font-weight-medium">{{
                  child.text
                }}</v-list-item-title>
              </v-list-item>
            </v-list-group>

            <v-list-item
              v-else
              :to="item.link"
              color="primary"
              class="rounded-xl mb-1"
            >
              <template v-slot:prepend>
                <v-icon
                  :icon="item.icon || 'mdi-circle-medium'"
                  color="primary"
                ></v-icon>
              </template>
              <v-list-item-title class="font-weight-bold">{{
                item.text
              }}</v-list-item-title>
            </v-list-item>
          </template>
        </v-list>

        <!-- Fixed Footer Actions -->
        <div class="pa-4 drawer-footer border-t bg-white">
          <div class="mb-4">
            <div
              class="text-caption font-weight-black text-uppercase opacity-40 mb-2 ps-1"
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
                  class="flex-grow-1 rounded-lg font-weight-black h-40"
                  @click="toggle"
                  size="small"
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
          <v-btn
            block
            color="error"
            variant="flat"
            prepend-icon="mdi-logout"
            class="rounded-lg font-weight-black shadow-none h-48"
            @click="
              logoutDialog = true;
              mobileMenu = false;
            "
          >
            {{ $t("logout") }}
          </v-btn>
          <div
            class="text-center mt-4 opacity-30 text-caption font-weight-bold"
          >
            ZADSTORE v2.5.0
          </div>
        </div>
      </div>
    </v-navigation-drawer>

    <v-app-bar flat height="40" class="main-navbar" absolute>
      <v-container fluid class="d-flex align-center justify-space-between py-0">
        <div class="d-none d-md-flex align-center gap-6 right-actions">
          <div
            class="d-flex align-center justify-space-between w-100 text-white phone-info"
          >
            <span class="d-flex align-center">
              <v-icon size="16" class="ml-1">mdi-phone</v-icon>
              <span
                class="small-text font-weight-bold"
                style="direction: ltr; font-size: 11px"
                >{{ settingsStore.phone || "+20 123 456 7890" }}</span
              >
            </span>

            <v-divider
              vertical
              class="mx-3"
              color="white"
              style="opacity: 0.4; height: 18px"
            ></v-divider>

            <v-btn
              v-if="
                ['admin', 'moderator'].includes(authStore.currentUser?.role)
              "
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

        <!-- Social Media Icons & Search -->
        <div class="d-flex align-center gap-1">
          <!-- New: Top Bar Search (Desktop Only) -->
          <div class="d-none d-md-flex align-center top-search-wrapper ms-4">
            <input
              v-model="searchQuery"
              @keyup.enter="performSearch"
              type="text"
              :placeholder="$t('nav.search_placeholder')"
              class="top-search-input"
            />
            <v-btn
              icon="mdi-magnify"
              size="x-small"
              variant="text"
              color="white"
              @click="performSearch"
            ></v-btn>
          </div>

          <v-btn
            icon
            size="x-small"
            variant="text"
            color="white"
            class="social-btn-hover"
            :href="settingsStore.facebook || 'https://facebook.com'"
            target="_blank"
          >
            <v-icon size="18">mdi-facebook</v-icon>
          </v-btn>
          <v-btn
            icon
            size="x-small"
            variant="text"
            color="white"
            class="social-btn-hover"
            :href="settingsStore.instagram || 'https://instagram.com'"
            target="_blank"
          >
            <v-icon size="18">mdi-instagram</v-icon>
          </v-btn>
          <v-btn
            icon
            size="x-small"
            variant="text"
            color="white"
            class="social-btn-hover"
            :href="settingsStore.twitter || 'https://twitter.com'"
            target="_blank"
          >
            <v-icon size="18">mdi-twitter</v-icon>
          </v-btn>
          <v-btn
            v-if="settingsStore.tiktok"
            icon
            size="x-small"
            variant="text"
            color="white"
            class="social-btn-hover"
            :href="settingsStore.tiktok"
            target="_blank"
          >
            <v-icon size="18">mdi-music-note</v-icon>
          </v-btn>
        </div>
      </v-container>
    </v-app-bar>

    <v-app-bar
      :elevation="isScrolled ? 4 : 0"
      color="primary"
      height="65"
      class="px-2 px-md-4 position-fixed transition-app-bar border-bottom premium-app-bar"
      :style="{ top: isScrolled ? '0px' : '40px', zIndex: 999 }"
    >
      <div class="d-flex justify-space-between align-center w-100">
        <div
          class="d-flex align-center cursor-pointer ml-4"
          @click="$router.push('/')"
        >
          <v-app-bar-nav-icon
            class="d-md-none text-white mr-2"
            variant="text"
            density="compact"
            @click.stop="mobileMenu = !mobileMenu"
          ></v-app-bar-nav-icon>
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
              v-if="item.children && item.children.length > 0"
              open-on-hover
              open-delay="0"
              close-delay="100"
              location="bottom"
            >
              <template v-slot:activator="{ props }">
                <span
                  class="nav-text cursor-pointer d-flex align-center"
                  v-bind="props"
                >
                  <v-icon
                    v-if="item.icon"
                    :icon="item.icon"
                    size="18"
                    class="me-1"
                  ></v-icon>
                  {{ item.text }}
                  <v-icon
                    icon="mdi-chevron-down"
                    size="small"
                    class="ms-1"
                  ></v-icon>
                </span>
              </template>

              <v-list
                density="compact"
                elevation="3"
                class="dropdown-scroll-list rounded-lg"
                max-width="260"
              >
                <v-list-item
                  v-for="(child, c) in item.children"
                  :key="c"
                  :to="child.link"
                  link
                  color="primary"
                  class="dropdown-item"
                >
                  <v-list-item-title
                    class="text-capitalize font-weight-medium"
                    >{{ child.text }}</v-list-item-title
                  >
                </v-list-item>
              </v-list>
            </v-menu>

            <span
              v-else
              class="nav-text cursor-pointer"
              @click="$router.push(item.link || '/User/ProductsPage')"
            >
              <v-icon
                v-if="item.icon"
                :icon="item.icon"
                size="18"
                class="me-1"
              ></v-icon>
              {{ item.text }}
            </span>
          </div>
        </div>

        <div class="d-flex align-center justify-end gap-1">
          <!-- Main Search Button (Mobile Only) -->
          <v-btn
            icon
            color="white"
            variant="text"
            @click="toggleSearch"
            class="d-md-none"
          >
            <v-icon>mdi-magnify</v-icon>
          </v-btn>

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

          <!-- Theme dropdown removed -->
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
              <v-list-item
                :title="userData.name"
                :subtitle="userTier"
                class="user-profile-item"
                @click="
                  ['admin', 'moderator'].includes(authStore.currentUser?.role)
                    ? $router.push('/Dashboard/AdminDashboard')
                    : null
                "
              >
                <template v-slot:prepend>
                  <v-avatar size="30">
                    <v-img :src="userData.avatar"></v-img>
                  </v-avatar>
                </template>
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
                @click="logoutDialog = true"
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
                <span class="d-none d-sm-inline">{{ currentLang.name }}</span>
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

  <!-- Premium Search Overlay -->
  <v-fade-transition>
    <div v-if="searchOpen" class="search-full-overlay">
      <v-container class="h-100 d-flex flex-column justify-center align-center">
        <div class="d-flex justify-end w-100 mb-4 px-4">
          <v-btn
            icon="mdi-close"
            color="white"
            variant="text"
            size="large"
            @click="searchOpen = false"
            class="close-btn-shadow"
          ></v-btn>
        </div>

        <div class="search-input-container w-100 px-4 max-width-800">
          <div class="d-flex align-center overlay-search-box flex-nowrap">
            <input
              ref="searchInput"
              v-model="searchQuery"
              @keyup.enter="performSearch"
              type="text"
              :placeholder="$t('nav.search_placeholder')"
              class="overlay-search-field"
              autofocus
            />
            <!-- Mobile Specific Search Button -->
            <v-btn
              class="d-sm-none ms-3 rounded-lg font-weight-black shadow-none"
              color="warning"
              min-width="80"
              height="50"
              @click="performSearch"
            >
              {{ $t("nav.search_btn") || "بحث" }}
            </v-btn>
          </div>
          <div class="search-line"></div>
          <p class="text-white opacity-60 mt-4 text-center">
            {{ $t("search_hint") || "اضغط Enter للبحث عن منتجك المفضل..." }}
          </p>
        </div>
      </v-container>
    </div>
  </v-fade-transition>

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
          class="rounded-lg px-6 font-weight-bold shadow-none"
          @click="performLogout"
        >
          {{ $t("auth.logout_confirm_btn") }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick, inject, computed } from "vue";
import { useRouter } from "vue-router";
import { useI18n } from "vue-i18n";
import { useLocale } from "vuetify";
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
const logoutDialog = ref(false);
const performLogout = () => {
  logoutDialog.value = false;
  router.push("/Auth/LogOut");
};

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
  vuetifyLocale.value = lang.code;
  document.dir = lang.code === "ar" ? "rtl" : "ltr";
  document.documentElement.lang = lang.code;
  document.documentElement.dir = lang.code === "ar" ? "rtl" : "ltr";
};

const onLangChange = (code) => {
  const lang = availableLangs.find((l) => l.code === code);
  if (lang) changeLang(lang);
};

// Dynamic categories/brands for dropdown
const categoryChildren = computed(() =>
  (productStore.categories || []).map((cat) => ({
    text: (cat.name || cat.slug || cat).replace(/-/g, " "),
    link: `/User/category/${cat.id || cat.slug || cat}`,
  }))
);

const brandChildren = computed(() =>
  (productStore.brand || []).map((b) => ({
    text: (b.name || b.slug || b).replace(/-/g, " "),
    link: `/User/brand/${b.id || b.slug || b}`,
  }))
);

const menuItems = computed(() => [
  { text: t("nav.home"), link: "/", badge: null },
  { text: t("nav.products"), link: "/User/ProductsPage", badge: null },
  {
    text: t("products.all_categories"),
    icon: "mdi-format-list-bulleted-type",
    badge: null,
    children: categoryChildren.value,
  },
  {
    text: t("products.all_brands"),
    icon: "mdi-tag-multiple",
    badge: null,
    children: brandChildren.value,
  },
  {
    text: t("nav.orders"),
    link: "/User/ControlPannel/OrdersPage",
    badge: null,
  },
  { text: t("nav.add_order"), link: "/User/AddOrder", badge: null },
]);

import api from "@/axios";

const mobileMenu = ref(false);
const searchOpen = ref(false);
const searchQuery = ref("");
const isScrolled = ref(false);
const searchInput = ref(null);

// --- User Data ---
const userData = computed(() => {
  const baseUrl = process.env.VUE_APP_API_URL || "http://localhost:8000";
  return {
    name: authStore.currentUser?.name || t("nav.guest"),
    avatar: authStore.currentUser?.avatar
      ? `${baseUrl}${authStore.currentUser.avatar}`
      : "https://i.pravatar.cc/150?img=12",
  };
});
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

  // Fetch dynamic categories and settings
  await Promise.all([
    settingsStore.fetchSettings(),
    productStore.getCategories(),
    productStore.getBrands(),
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

/* ================= Top Bar Search (Desktop) ================= */
.top-search-wrapper {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 20px;
  padding: 0px 12px; /* Thinner */
  display: flex;
  align-items: center;
  width: 350px; /* Wider start */
  transition: all 0.3s ease;
  height: 28px; /* Fixed thinner height */
}

.top-search-wrapper:focus-within {
  background: rgba(255, 255, 255, 0.2);
  border-color: #ffca28;
  width: 450px; /* Wider expanded */
}

.top-search-input {
  border: none;
  background: transparent;
  color: white;
  width: 100%;
  outline: none;
  font-size: 12px; /* Slightly smaller font for thinner bar */
  padding: 0;
}

.top-search-input::placeholder {
  color: rgba(255, 255, 255, 0.6);
}

/* ================= Premium Search Overlay ================= */
.search-full-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(var(--v-theme-primary), 0.95);
  backdrop-filter: blur(20px);
  z-index: 10000; /* Over everything */
  color: white;
}

.overlay-search-box {
  gap: 8px;
  background: rgba(255, 255, 255, 0.05);
  padding: 8px 16px;
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.max-width-800 {
  max-width: 800px;
}

.overlay-search-field {
  flex-grow: 1;
  background: transparent;
  border: none;
  color: white;
  font-size: clamp(18px, 4vw, 32px);
  font-weight: 700;
  padding: 10px 0;
  outline: none;
  text-align: inherit;
}

.search-line {
  height: 2px;
  width: 0;
  background: #ffca28;
  margin: 5px 0 0;
  transition: width 0.6s cubic-bezier(0.19, 1, 0.22, 1);
}

.overlay-search-field:focus + .search-line {
  width: 100%;
}

.overlay-search-field::placeholder {
  color: rgba(255, 255, 255, 0.3);
}

@media (max-width: 600px) {
  .overlay-search-field {
    font-size: 20px;
  }
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

/* Scrollable Dropdown for Categories & Brands */
.dropdown-scroll-list {
  max-height: 350px;
  overflow-y: auto;
  min-width: 220px;
}
.dropdown-scroll-list::-webkit-scrollbar {
  width: 5px;
}
.dropdown-scroll-list::-webkit-scrollbar-track {
  background: #f5f5f5;
  border-radius: 4px;
}
.dropdown-scroll-list::-webkit-scrollbar-thumb {
  background: #bdbdbd;
  border-radius: 4px;
}
.dropdown-scroll-list::-webkit-scrollbar-thumb:hover {
  background: #9e9e9e;
}
.dropdown-item {
  transition: all 0.2s ease;
  border-radius: 8px !important;
  margin: 2px 4px;
}
.dropdown-item:hover {
  background-color: rgba(var(--v-theme-primary), 0.08);
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
  z-index: 20000; /* Much higher */
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

.profile-avatar-premium {
  border: 3px solid rgba(255, 255, 255, 0.2);
  position: relative;
  z-index: 1;
}

.lh-1 {
  line-height: 1.2;
}

.admin-item-premium {
  background: rgba(var(--v-theme-primary), 0.05);
}

.h-40 {
  height: 40px !important;
}

.h-48 {
  height: 48px !important;
}

.drawer-footer {
  position: sticky;
  bottom: 0;
  z-index: 2;
}

/* Scrollbar styling */
.overflow-y-auto::-webkit-scrollbar {
  width: 4px;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
  background: rgba(var(--v-theme-primary), 0.2);
  border-radius: 10px;
}

.premium-app-bar {
  background: linear-gradient(
    90deg,
    rgb(var(--v-theme-primary)) 0%,
    #1a237e 100%
  ) !important;
}

@media (max-width: 600px) {
  .premium-app-bar {
    background: linear-gradient(
      135deg,
      #0d1117 0%,
      rgb(var(--v-theme-primary)) 100%
    ) !important;
  }
  .support-fab-container {
    bottom: 20px;
    inset-inline-end: 15px;
  }
  .support-text {
    display: none;
  }
  .support-fab {
    width: 52px !important;
    padding: 0 !important;
    border-radius: 50% !important;
  }
}
.social-btn-hover {
  opacity: 0.9;
  transition: all 0.2s ease;
  flex-shrink: 0;
  margin: 0 2px;
}
.social-btn-hover:hover {
  opacity: 1;
  transform: translateY(-2px);
  background-color: rgba(255, 255, 255, 0.1);
}
.phone-info {
  flex-shrink: 0;
}
</style>
