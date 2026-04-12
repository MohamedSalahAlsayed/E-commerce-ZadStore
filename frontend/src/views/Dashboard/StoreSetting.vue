<template>
  <v-locale-provider :rtl="locale === 'ar'">
    <v-container fluid class="pa-6 bg-grey-lighten-5 min-h-screen">
      <v-card class="mb-6 rounded-xl glass-card border-0" elevation="0">
        <v-card-text
          class="d-flex align-center justify-space-between flex-wrap gap-4 pa-4"
        >
          <div class="d-flex align-center gap-3">
            <v-avatar
              color="rgb(var(--v-theme-primary))"
              size="48"
              class="rounded-lg"
            >
              <v-icon color="white" size="28">mdi-cog-outline</v-icon>
            </v-avatar>
            <h2
              class="text-h5 font-weight-black"
              style="color: rgb(var(--v-theme-primary))"
            >
              {{ $t("settings.title") }}
            </h2>
          </div>

          <v-btn
            color="primary"
            prepend-icon="mdi-content-save-outline"
            class="font-weight-bold px-8 rounded-lg shadow-btn text-h6"
            :class="{ 'pulse-btn': hasChanges }"
            height="48"
            :variant="hasChanges ? 'elevated' : 'tonal'"
            :loading="isSaving"
            @click="saveSettings"
          >
            <v-badge
              v-if="hasChanges"
              color="error"
              dot
              location="top right"
              offset-x="-4"
              offset-y="-4"
            >
              {{ $t("settings.save_btn") }}
            </v-badge>
            <span v-else>{{ $t("settings.save_btn") }}</span>
          </v-btn>
        </v-card-text>
      </v-card>

      <v-card
        class="rounded-xl glass-card border-0 overflow-hidden"
        elevation="0"
      >
        <div class="d-flex flex-column flex-md-row">
          <v-tabs
            v-model="activeTab"
            color="primary"
            direction="vertical"
            class="border-right bg-white/50 py-4 settings-tabs"
            style="min-width: 250px"
          >
            <v-tab
              value="general"
              class="text-right font-weight-bold d-flex justify-start px-6 py-3"
            >
              <v-icon right class="ml-3">mdi-storefront-outline</v-icon>
              {{ $t("settings.tabs.general") }}
            </v-tab>
            <v-tab
              value="contact"
              class="text-right font-weight-bold d-flex justify-start px-6 py-3"
            >
              <v-icon right class="ml-3">mdi-phone-outline</v-icon>
              {{ $t("settings.tabs.contact") }}
            </v-tab>
            <v-tab
              value="social"
              class="text-right font-weight-bold d-flex justify-start px-6 py-3"
            >
              <v-icon right class="ml-3">mdi-share-variant-outline</v-icon>
              {{ $t("settings.tabs.social") }}
            </v-tab>
            <v-tab
              value="landing"
              class="text-right font-weight-bold d-flex justify-start px-6 py-3"
            >
              <v-icon right class="ml-3">mdi-view-dashboard-variant</v-icon>
              {{ $t("settings.tabs.landing") }}
            </v-tab>
            <v-tab
              value="advanced"
              class="text-right font-weight-bold d-flex justify-start px-6 py-3"
            >
              <v-icon right class="ml-3">mdi-tune-vertical</v-icon>
              {{ $t("settings.tabs.advanced") }}
            </v-tab>
          </v-tabs>

          <v-window v-model="activeTab" class="flex-grow-1 pa-6">
            <v-window-item
              v-for="tab in [
                'general',
                'contact',
                'social',
                'landing',
                'advanced',
              ]"
              :key="tab"
              :value="tab"
              class="pa-0"
            >
              <v-container fluid class="pa-0">
                <div v-if="activeTab === 'general'">
                  <h3
                    class="text-h6 font-weight-bold mb-6"
                    style="color: rgb(var(--v-theme-primary))"
                  >
                    {{ $t("settings.general.title") }}
                  </h3>
                  <v-row v-if="isLoading">
                    <v-col cols="12" md="6"
                      ><v-skeleton-loader type="list-item"
                    /></v-col>
                    <v-col cols="12" md="6"
                      ><v-skeleton-loader type="list-item"
                    /></v-col>
                    <v-col cols="12"
                      ><v-skeleton-loader type="paragraph"
                    /></v-col>
                    <v-col cols="12" md="6">
                      <div class="d-flex align-center gap-4">
                        <v-skeleton-loader type="avatar" size="80" />
                        <v-skeleton-loader
                          type="list-item"
                          class="flex-grow-1"
                        />
                      </div>
                    </v-col>
                    <v-col cols="12">
                      <v-divider class="my-4"></v-divider>
                      <v-skeleton-loader
                        type="heading"
                        width="200"
                        class="mb-4"
                      />
                      <div class="d-flex flex-wrap gap-4">
                        <v-skeleton-loader
                          v-for="i in 4"
                          :key="i"
                          type="card"
                          width="180"
                          height="100"
                        />
                      </div>
                    </v-col>
                  </v-row>
                  <v-row v-else>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="settings.storeName"
                        :label="$t('settings.general.store_name')"
                        variant="outlined"
                        color="primary"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="settings.storeSlogan"
                        :label="$t('settings.general.slogan')"
                        variant="outlined"
                        color="primary"
                        :placeholder="$t('settings.general.slogan_placeholder')"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <v-textarea
                        v-model="settings.storeDescription"
                        :label="$t('settings.general.description')"
                        variant="outlined"
                        color="primary"
                        rows="3"
                      ></v-textarea>
                    </v-col>

                    <v-col cols="12" md="6">
                      <p class="font-weight-bold mb-2">
                        {{ $t("settings.general.logo_url") }}
                      </p>
                      <div class="d-flex align-center gap-4">
                        <v-avatar
                          size="80"
                          class="bg-grey-lighten-3 border rounded-lg"
                          rounded
                        >
                          <v-img
                            v-if="settings.logoPreview"
                            :src="settings.logoPreview"
                            cover
                          ></v-img>
                          <v-icon v-else size="40" color="grey"
                            >mdi-image</v-icon
                          >
                        </v-avatar>
                        <v-file-input
                          v-model="logoFile"
                          variant="outlined"
                          density="compact"
                          hide-details
                          prepend-icon=""
                          prepend-inner-icon="mdi-camera"
                          :label="$t('settings.general.logo_upload')"
                          accept="image/*"
                          class="w-100"
                          @update:modelValue="handleLogoUpload"
                        ></v-file-input>
                      </div>
                    </v-col>

                    <!-- Color Themes Palette -->
                    <v-col cols="12">
                      <v-divider class="my-4"></v-divider>
                      <h3
                        class="text-h6 font-weight-bold mb-4"
                        style="color: rgb(var(--v-theme-primary))"
                      >
                        <v-icon left>mdi-palette</v-icon>
                        {{ $t("settings.general.theme_title") }}
                      </h3>
                      <p class="text-subtitle-2 text-grey-darken-1 mb-6">
                        {{ $t("settings.general.theme_desc") }}
                      </p>

                      <div
                        class="d-flex flex-column align-center justify-center mt-4"
                      >
                        <v-color-picker
                          v-model="settings.primaryColor"
                          elevation="3"
                          mode="hex"
                          show-swatches
                          swatches-max-height="200"
                          class="rounded-xl border border-grey-lighten-2"
                        ></v-color-picker>

                        <!-- Custom HEX Input -->
                        <div
                          class="mt-6 d-flex align-center gap-4 w-100"
                          style="max-width: 300px"
                        >
                          <v-text-field
                            v-model="settings.primaryColor"
                            :label="$t('settings.general.custom_color')"
                            variant="outlined"
                            density="compact"
                            prepend-inner-icon="mdi-palette"
                            hide-details
                            dir="ltr"
                            class="text-center font-weight-bold"
                          ></v-text-field>
                          <v-avatar
                            :color="settings.primaryColor"
                            size="40"
                            class="border shadow-sm"
                          ></v-avatar>
                        </div>
                      </div>
                    </v-col>
                  </v-row>
                </div>
                <div v-else-if="activeTab === 'contact'">
                  <h3
                    class="text-h6 font-weight-bold mb-6"
                    style="color: rgb(var(--v-theme-primary))"
                  >
                    {{ $t("settings.contact.title") }}
                  </h3>
                  <v-row v-if="isLoading">
                    <v-col cols="12" md="6" v-for="i in 3" :key="i">
                      <v-skeleton-loader type="list-item" />
                    </v-col>
                    <v-col cols="12">
                      <v-skeleton-loader type="list-item" />
                    </v-col>
                  </v-row>
                  <v-row v-else>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="settings.email"
                        :label="$t('settings.contact.email')"
                        prepend-inner-icon="mdi-email-outline"
                        variant="outlined"
                        color="primary"
                        type="email"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <PhoneInput
                        v-model="settings.phone"
                        :label="$t('settings.contact.phone')"
                      />
                    </v-col>
                    <v-col cols="12" md="6">
                      <PhoneInput
                        v-model="settings.whatsapp"
                        :label="$t('settings.contact.whatsapp')"
                      />
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="settings.address"
                        :label="$t('settings.contact.address')"
                        prepend-inner-icon="mdi-map-marker-outline"
                        variant="outlined"
                        color="primary"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </div>
                <div v-else-if="activeTab === 'social'">
                  <h3
                    class="text-h6 font-weight-bold mb-6"
                    style="color: rgb(var(--v-theme-primary))"
                  >
                    {{ $t("settings.social.title") }}
                  </h3>
                  <v-row v-if="isLoading">
                    <v-col cols="12" md="6" v-for="i in 4" :key="i">
                      <v-skeleton-loader type="list-item" />
                    </v-col>
                  </v-row>
                  <v-row v-else>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="settings.facebook"
                        :label="$t('settings.social.facebook')"
                        prepend-inner-icon="mdi-facebook"
                        variant="outlined"
                        color="#1877F2"
                        dir="ltr"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="settings.instagram"
                        :label="$t('settings.social.instagram')"
                        prepend-inner-icon="mdi-instagram"
                        variant="outlined"
                        color="#E4405F"
                        dir="ltr"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="settings.tiktok"
                        :label="$t('settings.social.tiktok')"
                        prepend-inner-icon="mdi-music-note"
                        variant="outlined"
                        color="black"
                        dir="ltr"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="settings.twitter"
                        :label="$t('settings.social.twitter')"
                        prepend-inner-icon="mdi-twitter"
                        variant="outlined"
                        color="#1DA1F2"
                        dir="ltr"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </div>
                <div
                  v-else-if="activeTab === 'landing'"
                  class="landing-preview-tab"
                >
                  <div class="d-flex align-center justify-space-between mb-6">
                    <h3
                      class="text-h6 font-weight-bold"
                      style="color: rgb(var(--v-theme-primary))"
                    >
                      {{ $t("settings.landing.title") }}
                    </h3>
                  </div>

                  <!-- Template Selection -->
                  <v-card
                    variant="flat"
                    class="template-picker-card pa-6 mb-8 shadow-sm"
                  >
                    <div class="d-flex align-center mb-6">
                      <v-icon color="primary" class="mr-3"
                        >mdi-pencil-ruler</v-icon
                      >
                      <span class="text-h6 font-weight-bold">{{
                        $t("settings.landing.layout_title")
                      }}</span>
                    </div>
                    <v-row>
                      <v-col
                        v-for="temp in ['modern', 'neo', 'classic']"
                        :key="temp"
                        cols="12"
                        md="4"
                      >
                        <v-card
                          @click="settings.guestHome.activeTemplate = temp"
                          :class="{
                            'active-template border-primary':
                              settings.guestHome.activeTemplate === temp,
                          }"
                          class="theme-card cursor-pointer pa-4 text-center h-100 d-flex flex-column align-center"
                          variant="outlined"
                        >
                          <div
                            v-if="settings.guestHome.activeTemplate === temp"
                            class="active-badge"
                          >
                            <v-icon color="white" size="14">mdi-check</v-icon>
                          </div>
                          <v-icon
                            size="48"
                            :color="
                              settings.guestHome.activeTemplate === temp
                                ? 'primary'
                                : 'grey-lighten-1'
                            "
                            class="mb-4"
                          >
                            {{
                              temp === "modern"
                                ? "mdi-lightning-bolt-outline"
                                : temp === "neo"
                                ? "mdi-blur"
                                : "mdi-dock-window"
                            }}
                          </v-icon>
                          <span class="font-weight-bold text-subtitle-1">{{
                            $t(`settings.landing.layouts.${temp}`)
                          }}</span>
                        </v-card>
                      </v-col>
                    </v-row>
                  </v-card>

                  <v-row>
                    <v-col cols="12" lg="8">
                      <p class="text-subtitle-2 text-grey-darken-1 mb-6">
                        {{ $t("settings.landing.desc") }}
                      </p>

                      <div v-if="isLoading">
                        <v-skeleton-loader type="paragraph" />
                      </div>

                      <template v-else>
                        <v-list class="bg-grey-lighten-4 rounded-xl pa-2 mb-8">
                          <v-list-item
                            v-for="(section, index) in settings.guestHome
                              .sectionsOrder"
                            :key="section"
                            class="mb-2 rounded-lg bg-white shadow-sm"
                            @mouseenter="hoveredSection = section"
                            @mouseleave="hoveredSection = null"
                          >
                            <template v-slot:prepend>
                              <v-chip
                                size="small"
                                color="primary"
                                class="mr-2 font-weight-bold"
                              >
                                {{ index + 1 }}
                              </v-chip>
                            </template>

                            <v-list-item-title class="font-weight-bold py-1">
                              {{ getSectionLabel(section) }}
                            </v-list-item-title>

                            <template v-slot:append>
                              <div class="d-flex align-center gap-2">
                                <!-- Visibility Toggle -->
                                <v-btn
                                  icon
                                  variant="text"
                                  :color="
                                    settings.guestHome[
                                      getSectionToggleKey(section)
                                    ]
                                      ? 'primary'
                                      : 'grey'
                                  "
                                  size="small"
                                  @click="
                                    settings.guestHome[
                                      getSectionToggleKey(section)
                                    ] =
                                      !settings.guestHome[
                                        getSectionToggleKey(section)
                                      ]
                                  "
                                >
                                  <v-icon>{{
                                    settings.guestHome[
                                      getSectionToggleKey(section)
                                    ]
                                      ? "mdi-eye"
                                      : "mdi-eye-off"
                                  }}</v-icon>
                                  <v-tooltip activator="parent" location="top">
                                    {{
                                      settings.guestHome[
                                        getSectionToggleKey(section)
                                      ]
                                        ? "إظهار"
                                        : "إخفاء"
                                    }}
                                  </v-tooltip>
                                </v-btn>

                                <v-divider
                                  vertical
                                  class="mx-2"
                                  style="height: 24px"
                                ></v-divider>

                                <!-- Move Buttons -->
                                <v-btn
                                  icon="mdi-chevron-up"
                                  size="x-small"
                                  variant="tonal"
                                  :disabled="index === 0"
                                  @click="moveSection(index, -1)"
                                ></v-btn>
                                <v-btn
                                  icon="mdi-chevron-down"
                                  size="x-small"
                                  variant="tonal"
                                  :disabled="
                                    index ===
                                    settings.guestHome.sectionsOrder.length - 1
                                  "
                                  @click="moveSection(index, 1)"
                                ></v-btn>
                              </div>
                            </template>
                          </v-list-item>
                        </v-list>

                        <!-- Custom Text Fields -->
                        <h4 class="text-subtitle-1 font-weight-bold mb-6 mt-8">
                          <v-icon size="20" class="ml-2"
                            >mdi-format-text</v-icon
                          >
                          {{
                            $t("settings.landing.texts_title") ||
                            "تخصيص نصوص الأقسام"
                          }}
                        </h4>

                        <v-row>
                          <!-- Hero -->
                          <v-col cols="12" md="6">
                            <v-text-field
                              v-model="settings.guestHome.heroTitle"
                              :label="$t('settings.landing.hero_title_label')"
                              variant="outlined"
                              placeholder="اتركه فارغاً للاستعراض التلقائي"
                              color="primary"
                              density="compact"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12" md="6">
                            <v-text-field
                              v-model="settings.guestHome.heroSubtitle"
                              :label="
                                $t('settings.landing.hero_subtitle_label')
                              "
                              variant="outlined"
                              placeholder="اتركه فارغاً للاستعراض التلقائي"
                              color="primary"
                              density="compact"
                            ></v-text-field>
                          </v-col>

                          <!-- CTA -->
                          <v-col cols="12" md="4">
                            <v-text-field
                              v-model="settings.guestHome.ctaTitle"
                              label="عنوان قسم الانضمام (CTA)"
                              variant="outlined"
                              color="primary"
                              density="compact"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12" md="4">
                            <v-text-field
                              v-model="settings.guestHome.ctaDesc"
                              label="وصف قسم الانضمام"
                              variant="outlined"
                              color="primary"
                              density="compact"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12" md="4">
                            <v-text-field
                              v-model="settings.guestHome.registerFree"
                              label="نص زر التسجيل"
                              variant="outlined"
                              color="primary"
                              density="compact"
                            ></v-text-field>
                          </v-col>

                          <!-- Product Titles -->
                          <v-col cols="12" md="4">
                            <v-text-field
                              v-model="settings.guestHome.flashTitle"
                              :label="$t('settings.landing.flash_title_label')"
                              variant="outlined"
                              color="primary"
                              density="compact"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12" md="4">
                            <v-text-field
                              v-model="settings.guestHome.bestSellersTitle"
                              :label="
                                $t('settings.landing.best_sellers_title_label')
                              "
                              variant="outlined"
                              color="primary"
                              density="compact"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12" md="4">
                            <v-text-field
                              v-model="settings.guestHome.featuredTitle"
                              :label="
                                $t('settings.landing.featured_title_label')
                              "
                              variant="outlined"
                              color="primary"
                              density="compact"
                            ></v-text-field>
                          </v-col>

                          <!-- Other Section Limits -->
                          <v-col cols="12" md="6">
                            <v-slider
                              v-model="settings.guestHome.featuredLimit"
                              min="4"
                              max="24"
                              step="4"
                              thumb-label
                              label="عدد منتجات الأكثر مبيعاً"
                              color="primary"
                            ></v-slider>
                          </v-col>
                          <v-col cols="12" md="6">
                            <v-slider
                              v-model="settings.guestHome.brandsLimit"
                              min="4"
                              max="48"
                              step="4"
                              thumb-label
                              label="عدد الماركات المعروضة"
                              color="primary"
                            ></v-slider>
                          </v-col>
                        </v-row>
                      </template>
                    </v-col>

                    <!-- Landing Page Mockup Preview -->
                    <v-col cols="12" lg="4" class="d-none d-lg-block">
                      <div class="sticky-preview pt-4">
                        <!-- Phone Frame -->
                        <div class="phone-frame">
                          <div class="phone-notch"></div>
                          <div class="phone-screen">
                            <!-- Mockup Content -->
                            <v-card
                              :key="
                                'mockup-' + settings.guestHome.guestPrimaryColor
                              "
                              variant="flat"
                              class="h-100 rounded-0"
                            >
                              <v-toolbar
                                density="compact"
                                color="white"
                                flat
                                border
                              >
                                <v-avatar
                                  color="primary"
                                  size="24"
                                  class="ml-2"
                                ></v-avatar>
                                <v-spacer></v-spacer>
                                <v-icon
                                  v-for="i in 3"
                                  :key="i"
                                  size="18"
                                  icon="mdi-circle-small"
                                  color="grey"
                                ></v-icon>
                              </v-toolbar>

                              <div
                                class="mockup-content pa-4 bg-grey-lighten-4"
                              >
                                <!-- Hero Mock -->
                                <div
                                  v-if="settings.guestHome.showHeroSlider"
                                  class="mock-hero rounded-lg mb-2 d-flex align-center justify-center text-caption"
                                  :class="{
                                    'highlight-pulse':
                                      hoveredSection === 'BannerHome',
                                  }"
                                  :style="{
                                    backgroundColor:
                                      settings.guestHome.guestPrimaryColor +
                                      '15',
                                    border:
                                      '1px solid ' +
                                      settings.guestHome.guestPrimaryColor,
                                  }"
                                >
                                  <span
                                    :style="{
                                      color:
                                        settings.guestHome.guestPrimaryColor,
                                    }"
                                    >Slider Active</span
                                  >
                                </div>

                                <!-- Features -->
                                <div
                                  v-if="settings.guestHome.showTopFeaturesStrip"
                                  class="mock-strip rounded mb-2 bg-primary opacity-20"
                                  :class="{
                                    'highlight-pulse':
                                      hoveredSection === 'QualityFeature',
                                  }"
                                  style="height: 10px"
                                ></div>

                                <!-- Categories -->
                                <div
                                  v-if="settings.guestHome.showCategoury"
                                  class="mock-categories mb-2 d-flex gap-1"
                                  :class="{
                                    'highlight-pulse':
                                      hoveredSection === 'Categoury',
                                  }"
                                >
                                  <div
                                    v-for="i in 4"
                                    :key="i"
                                    class="mock-cat rounded bg-grey-lighten-2 flex-grow-1"
                                    style="height: 20px"
                                  ></div>
                                </div>

                                <!-- Deals -->
                                <div
                                  v-if="settings.guestHome.showFlashDeals"
                                  class="mock-grid mb-2"
                                  :class="{
                                    'highlight-pulse':
                                      hoveredSection === 'FlashDeal',
                                  }"
                                >
                                  <v-row dense>
                                    <v-col v-for="i in 3" :key="i" cols="4">
                                      <div
                                        class="mock-item rounded bg-grey-lighten-2"
                                        style="height: 30px"
                                      ></div>
                                    </v-col>
                                  </v-row>
                                </div>

                                <!-- Products -->
                                <div
                                  v-if="settings.guestHome.showNewProduct"
                                  class="mock-grid mb-2"
                                  :class="{
                                    'highlight-pulse':
                                      hoveredSection === 'NewProduct',
                                  }"
                                >
                                  <v-row dense>
                                    <v-col v-for="i in 2" :key="i" cols="6">
                                      <div
                                        class="mock-item rounded bg-grey-lighten-2"
                                        style="height: 40px"
                                      ></div>
                                    </v-col>
                                  </v-row>
                                </div>

                                <!-- Steps -->
                                <div
                                  v-if="settings.guestHome.showSteps"
                                  class="mock-steps rounded mb-2 bg-grey-lighten-3"
                                  :class="{
                                    'highlight-pulse':
                                      hoveredSection === 'ShopWithUs',
                                  }"
                                  style="height: 15px"
                                ></div>

                                <!-- Brands -->
                                <div
                                  v-if="settings.guestHome.showBrands"
                                  class="mock-brands d-flex gap-1"
                                  :class="{
                                    'highlight-pulse':
                                      hoveredSection === 'BrandS',
                                  }"
                                >
                                  <div
                                    v-for="i in 4"
                                    :key="i"
                                    class="mock-brand rounded-circle bg-grey-lighten-2"
                                    style="width: 15px; height: 15px"
                                  ></div>
                                </div>
                              </div>

                              <v-footer
                                class="bg-grey-darken-4 text-white pa-2"
                                :class="{
                                  'highlight-pulse':
                                    hoveredSection === 'CtaSection',
                                }"
                              >
                                <span style="font-size: 8px"
                                  >ZadStore &copy; 2024</span
                                >
                              </v-footer>
                            </v-card>
                          </div>
                        </div>
                        <p class="text-center text-caption text-grey mt-4">
                          <v-icon size="small" class="mr-1"
                            >mdi-cellphone</v-icon
                          >
                          {{
                            locale === "ar"
                              ? "معاينة حية للهاتف"
                              : "Live Mobile Preview"
                          }}
                        </p>
                      </div>
                    </v-col>
                  </v-row>
                </div>
                <div v-else-if="activeTab === 'advanced'">
                  <h3
                    class="text-h6 font-weight-bold mb-6"
                    style="color: rgb(var(--v-theme-primary))"
                  >
                    {{ $t("settings.advanced.title") }}
                  </h3>
                  <v-row v-if="isLoading">
                    <v-col cols="12" md="6"
                      ><v-skeleton-loader type="list-item"
                    /></v-col>
                    <v-col cols="12" md="6">
                      <div class="d-flex gap-4">
                        <v-skeleton-loader type="text" class="flex-grow-1" />
                        <v-skeleton-loader type="text" class="flex-grow-1" />
                      </div>
                    </v-col>
                    <v-col cols="12" v-for="i in 2" :key="i">
                      <v-skeleton-loader
                        type="image"
                        height="80"
                        class="mb-4"
                      />
                    </v-col>
                  </v-row>
                  <v-row v-else>
                    <v-col cols="12" md="6">
                      <v-select
                        v-model="settings.currency"
                        :items="[
                          $t('currency.egp'),
                          $t('currency.usd'),
                          $t('currency.sar'),
                        ]"
                        :label="$t('settings.advanced.currency')"
                        prepend-inner-icon="mdi-cash"
                        variant="outlined"
                        color="primary"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" md="4">
                      <v-text-field
                        v-model.number="settings.taxRate"
                        :label="$t('settings.advanced.tax_rate')"
                        type="number"
                        prepend-inner-icon="mdi-percent"
                        variant="outlined"
                        color="primary"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4">
                      <v-text-field
                        v-model="settings.orderPrefix"
                        :label="$t('settings.advanced.order_prefix')"
                        variant="outlined"
                        color="primary"
                        prepend-inner-icon="mdi-format-prefix"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4">
                      <v-text-field
                        v-model.number="settings.lowStockThreshold"
                        :label="$t('settings.advanced.low_stock')"
                        type="number"
                        variant="outlined"
                        color="primary"
                        prepend-inner-icon="mdi-alert-box-outline"
                      ></v-text-field>
                    </v-col>
                  </v-row>

                  <v-divider class="my-6"></v-divider>

                  <v-card
                    variant="tonal"
                    color="info"
                    class="rounded-lg pa-4 mb-4"
                  >
                    <div class="d-flex justify-space-between align-center">
                      <div>
                        <div
                          class="d-flex align-center gap-2 mb-1 text-info font-weight-bold"
                        >
                          <v-icon>mdi-cash-marker</v-icon>
                          <span>{{ $t("settings.advanced.cod_enabled") }}</span>
                        </div>
                        <p class="text-caption text-grey-darken-1 mb-0">
                          {{ $t("settings.advanced.cod_desc") }}
                        </p>
                      </div>
                      <v-switch
                        v-model="settings.codEnabled"
                        color="info"
                        hide-details
                        inset
                      ></v-switch>
                    </div>
                  </v-card>

                  <v-divider class="my-6"></v-divider>

                  <v-card
                    variant="tonal"
                    :color="
                      settings.maintenanceMode ? 'warning' : 'grey-lighten-1'
                    "
                    class="rounded-lg pa-4"
                  >
                    <div class="d-flex justify-space-between align-center">
                      <div>
                        <div class="d-flex align-center gap-2 mb-1">
                          <v-icon
                            :color="
                              settings.maintenanceMode ? 'warning' : 'grey'
                            "
                            >mdi-alert-octagon-outline</v-icon
                          >
                          <span
                            class="text-h6 font-weight-bold"
                            :class="
                              settings.maintenanceMode
                                ? 'text-warning'
                                : 'text-grey-darken-2'
                            "
                            >{{
                              $t("settings.advanced.maintenance_mode")
                            }}</span
                          >
                        </div>
                        <p class="text-caption text-grey-darken-1 mb-0">
                          {{ $t("settings.advanced.maintenance_desc") }}
                        </p>
                      </div>
                      <v-switch
                        v-model="settings.maintenanceMode"
                        color="warning"
                        hide-details
                        inset
                      ></v-switch>
                    </div>
                  </v-card>
                </div>
              </v-container>
            </v-window-item>
          </v-window>
        </div>
      </v-card>

      <v-snackbar
        v-model="snackbar.show"
        :color="snackbar.color"
        timeout="3000"
        location="top center"
        class="font-weight-bold"
      >
        {{ snackbar.text }}
        <template v-slot:actions>
          <v-btn variant="text" @click="snackbar.show = false">{{
            $t("close")
          }}</v-btn>
        </template>
      </v-snackbar>
    </v-container>
  </v-locale-provider>
</template>
<script setup>
import { ref, onMounted, computed, watch } from "vue";
import { useI18n } from "vue-i18n";
import { useTheme } from "vuetify";
import axios from "@/axios";
import { mergeGuestHome } from "@/utils/guestHomeDefaults";
import PhoneInput from "@/components/PhoneInput.vue";

const { locale } = useI18n();
const theme = useTheme();
const hoveredSection = ref(null);
const activeTab = ref("general");
const isSaving = ref(false);
const isLoading = ref(true);
const initialSettings = ref(null);
const settings = ref({
  storeName: "",
  storeSlogan: "",
  storeDescription: "",
  logoPreview: "",
  email: "",
  phone: "",
  whatsapp: "",
  address: "",
  facebook: "",
  instagram: "",
  twitter: "",
  tiktok: "",
  currency: "جنيه مصري (EGP)",
  taxRate: 0,
  orderPrefix: "ZAD-",
  lowStockThreshold: 5,
  codEnabled: true,
  maintenanceMode: false,
  primaryColor: "#f97316",
  guestHome: mergeGuestHome(null),
});

const hasChanges = computed(() => {
  if (!initialSettings.value) return false;
  return JSON.stringify(settings.value) !== initialSettings.value;
});

const hexToRgb = (hex) => {
  if (!hex || hex.length < 7) return "0, 0, 0";
  let r = parseInt(hex.slice(1, 3), 16),
    g = parseInt(hex.slice(3, 5), 16),
    b = parseInt(hex.slice(5, 7), 16);
  return `${r}, ${g}, ${b}`;
};

const updateDynamicBranding = (color) => {
  if (!color) return;

  const currentTheme = theme.global.name.value;
  if (
    theme.themes.value[currentTheme] &&
    theme.themes.value[currentTheme].colors &&
    theme.themes.value[currentTheme].colors.primary === color
  ) {
    return;
  }

  const rgbColor = hexToRgb(color);

  // Directly adjust global CSS properties
  document.documentElement.style.setProperty("--v-theme-primary", rgbColor);
  document.documentElement.style.setProperty("--v-theme-secondary", rgbColor);
  document.body.style.setProperty("--v-theme-primary", rgbColor);
  document.body.style.setProperty("--v-theme-secondary", rgbColor);

  // Update Vuetify active theme colors correctly
  if (theme.themes.value[currentTheme]) {
    theme.themes.value[currentTheme].colors.primary = color;
    theme.themes.value[currentTheme].colors.secondary = color;
  }
};

watch(
  () => settings.value.primaryColor,
  (newColor) => {
    if (newColor && /^#([0-9a-f]{3}){1,2}$/i.test(newColor)) {
      updateDynamicBranding(newColor);
    }
  },
  { immediate: true }
);

const snackbar = ref({
  show: false,
  text: "",
  color: "success",
});

const showMessage = (text, color = "success") => {
  snackbar.value.text = text;
  snackbar.value.color = color;
  snackbar.value.show = true;
};

const logoFile = ref(null);

const handleLogoUpload = (fileArray) => {
  const file = Array.isArray(fileArray) ? fileArray[0] : fileArray;
  if (file) {
    settings.value.logoPreview = URL.createObjectURL(file);
  }
};

const loadSettings = async () => {
  isLoading.value = true;
  try {
    const res = await axios.get("/admin/settings");
    const data = res.data || {};

    // Map db data to UI ref
    if (data.storeName) settings.value.storeName = data.storeName;
    if (data.storeSlogan) settings.value.storeSlogan = data.storeSlogan;
    if (data.storeDescription)
      settings.value.storeDescription = data.storeDescription;
    if (data.logoPreview) settings.value.logoPreview = data.logoPreview;
    if (data.email) settings.value.email = data.email;
    if (data.phone) settings.value.phone = data.phone;
    if (data.whatsapp) settings.value.whatsapp = data.whatsapp;
    if (data.address) settings.value.address = data.address;
    if (data.facebook) settings.value.facebook = data.facebook;
    if (data.instagram) settings.value.instagram = data.instagram;
    if (data.twitter) settings.value.twitter = data.twitter;
    if (data.tiktok) settings.value.tiktok = data.tiktok;
    if (data.currency) settings.value.currency = data.currency;
    if (data.taxRate) settings.value.taxRate = Number(data.taxRate);
    if (data.order_prefix) settings.value.orderPrefix = data.order_prefix;
    if (data.low_stock_threshold)
      settings.value.lowStockThreshold = Number(data.low_stock_threshold);
    if (data.cod_enabled)
      settings.value.codEnabled = data.cod_enabled === "true";
    if (data.maintenanceMode)
      settings.value.maintenanceMode = data.maintenanceMode === "true";
    if (data.primaryColor) settings.value.primaryColor = data.primaryColor;

    if (data.guest_home) {
      settings.value.guestHome = mergeGuestHome(data.guest_home);
    }

    // Capture initial state for change detection
    initialSettings.value = JSON.stringify(settings.value);
  } catch (error) {
    console.error("Error loading settings:", error);
  } finally {
    isLoading.value = false;
  }
};

const saveSettings = async () => {
  isSaving.value = true;
  try {
    const payload = new FormData();
    Object.entries(settings.value).forEach(([key, val]) => {
      if (key === "guestHome") {
        payload.append("guest_home", JSON.stringify(val));
        return;
      }
      let keyMapping = {
        orderPrefix: "order_prefix",
        lowStockThreshold: "low_stock_threshold",
        codEnabled: "cod_enabled",
      };
      let finalKey = keyMapping[key] || key;

      payload.append(
        finalKey,
        key === "maintenanceMode" || key === "codEnabled"
          ? val
            ? "true"
            : "false"
          : val
      );
    });

    const file = Array.isArray(logoFile.value)
      ? logoFile.value[0]
      : logoFile.value;
    if (file) {
      payload.append("logo_file", file);
    }

    await axios.post("/admin/settings", payload, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    showMessage(
      locale.value === "ar"
        ? "تم حفظ الإعدادات بنجاح! سيتم تحديث الموقع..."
        : "Settings saved successfully! Refreshing..."
    );
    setTimeout(() => {
      window.location.reload();
    }, 1500);
  } catch (error) {
    console.error("Error saving settings:", error);
    showMessage(
      locale.value === "ar"
        ? "حدث خطأ أثناء حفظ الإعدادات"
        : "Error saving settings",
      "error"
    );
  } finally {
    isSaving.value = false;
  }
};

const getSectionLabel = (section) => {
  const labels = {
    BannerHome:
      locale.value === "ar" ? "البانر الرئيسي (Slider)" : "Main Banner",
    TopOffer:
      locale.value === "ar" ? "عروض اليوم (Top Offer)" : "Today's Offer",
    FlashDeal: locale.value === "ar" ? "العروض (Offers)" : "Offers",
    NewProduct:
      locale.value === "ar" ? "أحدث المنتجات (New Products)" : "New Products",
    Categoury: locale.value === "ar" ? "التصنيفات (Categories)" : "Categories",
    BestSeller: locale.value === "ar" ? "الأكثر مبيعاً" : "Best Sellers",
    QualityFeature:
      locale.value === "ar" ? "مميزات الجودة" : "Quality Features",
    FaqSection: locale.value === "ar" ? "الأسئلة الشائعة" : "FAQ Section",
    ShopWithUs: locale.value === "ar" ? "لماذا تشتري منا؟" : "Shop With Us",
    BrandS: locale.value === "ar" ? "الماركات" : "Brands",
    CtaSection:
      locale.value === "ar" ? "قسم دعوة التسجيل (CTA)" : "Registration CTA",
  };
  return labels[section] || section;
};

const getSectionToggleKey = (section) => {
  const mapping = {
    BannerHome: "showHeroSlider",
    FlashDeal: "showFlashDeals",
    BestSeller: "showBestSellers",
    FaqSection: "showFaq",
    CtaSection: "showCta",
    BrandS: "showBrands",
  };
  return mapping[section] || `show${section}`;
};

const moveSection = (index, direction) => {
  const newIndex = index + direction;
  if (newIndex < 0 || newIndex >= settings.value.guestHome.sectionsOrder.length)
    return;

  const arr = [...settings.value.guestHome.sectionsOrder];
  const item = arr.splice(index, 1)[0];
  arr.splice(newIndex, 0, item);
  settings.value.guestHome.sectionsOrder = arr;
};

onMounted(() => {
  loadSettings();
});
</script>

<style scoped>
.shadow-btn {
  box-shadow: 0 4px 12px rgba(var(--v-theme-primary), 0.3) !important;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.shadow-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(var(--v-theme-primary), 0.4) !important;
}

.glass-card {
  background: rgba(255, 255, 255, 0.7) !important;
  backdrop-filter: blur(12px) saturate(180%) !important;
  -webkit-backdrop-filter: blur(12px) saturate(180%) !important;
  border: 1px solid rgba(255, 255, 255, 0.3) !important;
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07) !important;
}

.dark-glass-card {
  background: rgba(30, 30, 30, 0.8) !important;
  backdrop-filter: blur(12px) !important;
  border: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.phone-frame {
  width: 280px;
  height: 580px;
  background: #111;
  border-radius: 40px;
  border: 8px solid #333;
  padding: 10px;
  position: relative;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
  margin: 0 auto;
}

.phone-screen {
  background: white;
  width: 100%;
  height: 100%;
  border-radius: 25px;
  overflow-y: auto;
  overflow-x: hidden;
  position: relative;
}

.phone-screen::-webkit-scrollbar {
  display: none;
}

.phone-notch {
  width: 120px;
  height: 20px;
  background: #333;
  position: absolute;
  top: 0;
  left: 50%;
  transform: translateX(-50%);
  border-bottom-left-radius: 15px;
  border-bottom-right-radius: 15px;
  z-index: 10;
}

.highlight-pulse {
  outline: 3px solid rgb(var(--v-theme-primary));
  outline-offset: -3px;
  animation: pulse-highlight 1.5s infinite;
  z-index: 5;
}

@keyframes pulse-highlight {
  0% {
    box-shadow: 0 0 0 0 rgba(var(--v-theme-primary), 0.7);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(var(--v-theme-primary), 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(var(--v-theme-primary), 0);
  }
}

.pulse-btn {
  animation: pulse-border 2s infinite;
}

@keyframes pulse-border {
  0% {
    box-shadow: 0 0 0 0 rgba(var(--v-theme-primary), 0.4);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(var(--v-theme-primary), 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(var(--v-theme-primary), 0);
  }
}

.border-right {
  border-left: 1px solid rgba(var(--v-theme-on-surface), 0.12);
}

.settings-tabs .v-btn {
  text-transform: none !important;
  letter-spacing: normal !important;
  border-radius: 0;
  border-right: 4px solid transparent;
}
.settings-tabs .v-tab--selected {
  background-color: rgb(var(--v-theme-surface));
  border-right: 4px solid rgb(var(--v-theme-primary));
  color: rgb(var(--v-theme-primary)) !important;
}

.gap-3 {
  gap: 12px;
}
.gap-4 {
  gap: 16px;
}
.gap.h-10 {
  height: 40px;
}

.text-right-input :deep(input) {
  text-align: right;
}

@media (max-width: 959px) {
  .border-right {
    border-left: none;
    border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.12);
  }
  .settings-tabs .v-tab--selected {
    border-right: none;
    border-bottom: 4px solid rgb(var(--v-theme-primary));
  }
}

.cursor-pointer {
  cursor: pointer;
  transition: all 0.3s ease;
}
.cursor-pointer:hover {
  transform: translateY(-5px);
}
.border-md {
  border: 2px solid transparent;
}
.color-selected {
  outline: 3px solid #000;
  outline-offset: 2px;
  transform: scale(1.15);
}

/* Theme Gallery Circular Design */
.palette-circles {
  display: flex;
  position: relative;
}

.palette-circle {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 3px solid #ffffff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.primary-circle {
  z-index: 3;
}

.secondary-circle {
  z-index: 2;
  margin-left: -15px;
}

.background-circle {
  z-index: 1;
  margin-left: -15px;
  border: 1px solid #eeeeee;
}

.theme-card {
  transition: all 0.3s ease !important;
  border-radius: 16px !important;
  overflow: hidden;
  border-color: rgba(0, 0, 0, 0.05) !important;
}

.theme-card:hover {
  border-color: rgb(var(--v-theme-primary)) !important;
  background-color: rgba(var(--v-theme-primary), 0.02);
}

.theme-card:hover .palette-circle {
  transform: scale(1.1);
}

.theme-card:hover .secondary-circle {
  margin-left: -10px;
}

.theme-card:hover .background-circle {
  margin-left: -10px;
}

.theme-selected {
  border: 2px solid rgb(var(--v-theme-primary)) !important;
  background-color: rgba(var(--v-theme-primary), 0.05);
  box-shadow: 0 8px 20px rgba(var(--v-theme-primary), 0.15) !important;
}

.theme-gallery-container {
  padding: 10px 5px;
}

.template-picker-card {
  border: 1px solid rgba(0, 0, 0, 0.05) !important;
  border-radius: 16px !important;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  background: #fafafa;
}

.template-picker-card:hover {
  transform: translateY(-4px);
  border-color: rgba(var(--v-theme-primary), 0.3) !important;
  background: white;
}

.active-template {
  box-shadow: 0 8px 24px rgba(var(--v-theme-primary), 0.1) !important;
}

.active-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background: rgb(var(--v-theme-primary));
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid white;
  z-index: 2;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.sticky-preview {
  position: sticky;
  top: 20px;
}

.mockup-card {
  transition: all 0.3s ease;
  overflow: hidden;
  background: white !important;
}

.mock-hero {
  height: 60px;
  transition: all 0.3s ease;
}

.mock-item,
.mock-brand,
.mock-steps,
.mock-strip {
  transition: all 0.2s ease;
}

.landing-preview-tab :deep(.v-switch) {
  margin-top: 0;
}
</style>
