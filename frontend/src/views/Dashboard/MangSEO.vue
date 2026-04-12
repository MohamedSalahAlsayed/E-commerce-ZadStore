<template>
  <v-container fluid class="pa-6" :dir="locale === 'ar' ? 'rtl' : 'ltr'">
    <div class="mb-8">
      <h1 class="text-h4 font-weight-black text-primary-darken-3 mb-1">
        {{ $t("seo.title") }}
      </h1>
      <p class="text-subtitle-1 text-grey-darken-1">
        {{ $t("seo.subtitle") }}
      </p>
    </div>

    <v-row>
      <!-- Global Search Preview -->
      <v-col cols="12" lg="8">
        <SEOPreview
          :title="
            settings.site_name +
            (settings.seo_title ? ' | ' + settings.seo_title : '')
          "
          :description="settings.seo_description"
          :breadcrumb="$t('nav.home')"
        />

        <v-card
          class="rounded-xl mt-6 border-s-lg border-primary"
          elevation="2"
        >
          <v-card-title class="pa-6 pb-2 d-flex align-center">
            <v-icon color="primary" class="mr-2">mdi-cog-outline</v-icon>
            {{ $t("seo.identity_settings") }}
          </v-card-title>
          <v-card-text class="pa-6">
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="settings.site_name"
                  :label="$t('seo.site_name_label')"
                  variant="outlined"
                  color="primary"
                  :hint="$t('seo.site_name_hint')"
                  persistent-hint
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="settings.site_slogan"
                  :label="$t('seo.slogan_label')"
                  variant="outlined"
                  color="primary"
                  :placeholder="$t('seo.slogan_placeholder')"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>

        <v-card
          class="rounded-xl mt-6 border-s-lg border-secondary"
          elevation="2"
        >
          <v-card-title class="pa-6 pb-2 d-flex align-center">
            <v-icon color="secondary" class="mr-2"
              >mdi-home-search-outline</v-icon
            >
            {{ $t("seo.home_meta_tags") }}
          </v-card-title>
          <v-card-text class="pa-6">
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="settings.seo_title"
                  :label="$t('seo.home_title_label')"
                  variant="outlined"
                  color="secondary"
                  counter="60"
                  persistent-hint
                  :hint="$t('seo.home_title_hint')"
                ></v-text-field>
              </v-col>

              <v-col cols="12">
                <v-textarea
                  v-model="settings.seo_description"
                  :label="$t('seo.home_desc_label')"
                  variant="outlined"
                  color="secondary"
                  rows="3"
                  counter="160"
                  persistent-hint
                  :hint="$t('seo.home_desc_hint')"
                ></v-textarea>
              </v-col>

              <v-col cols="12">
                <v-combobox
                  v-model="seoKeywordsArray"
                  :label="$t('seo.keywords_label')"
                  variant="outlined"
                  color="secondary"
                  multiple
                  chips
                  closable-chips
                  :hint="$t('seo.keywords_hint')"
                  persistent-hint
                  prepend-inner-icon="mdi-tag-multiple-outline"
                ></v-combobox>
              </v-col>
            </v-row>
          </v-card-text>
          <v-divider></v-divider>
          <v-card-actions class="pa-6 bg-grey-lighten-5">
            <v-spacer></v-spacer>
            <v-btn
              color="primary"
              variant="flat"
              height="48"
              class="px-12 font-weight-bold rounded-xl shadow-btn"
              :loading="saving"
              @click="saveSettings"
              size="large"
            >
              <v-icon start>mdi-content-save-check</v-icon>
              {{ $t("seo.save_all") }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>

      <!-- Side Utilities -->
      <v-col cols="12" lg="4">
        <!-- Advanced SEO Files -->
        <v-card class="rounded-xl mb-6 shadow-sm" elevation="1">
          <v-card-title
            class="pa-4 pb-0 text-subtitle-1 font-weight-bold d-flex align-center"
          >
            <v-icon color="grey-darken-1" class="mr-2" size="20"
              >mdi-xml</v-icon
            >
            {{ $t("seo.advanced_files") }}
          </v-card-title>
          <v-card-text class="pa-4">
            <v-list density="comfortable" class="bg-transparent">
              <v-list-item
                class="rounded-lg mb-2 bg-grey-lighten-4 border"
                lines="two"
              >
                <template v-slot:prepend>
                  <v-avatar color="white" size="40" class="border">
                    <v-icon color="primary">mdi-sitemap</v-icon>
                  </v-avatar>
                </template>
                <v-list-item-title class="font-weight-bold"
                  >Sitemap.xml</v-list-item-title
                >
                <v-list-item-subtitle>{{
                  $t("seo.sitemap_hint")
                }}</v-list-item-subtitle>
                <template v-slot:append>
                  <v-btn
                    size="small"
                    variant="text"
                    color="primary"
                    @click="viewSitemap"
                    >{{ $t("dashboard.view_all") }}</v-btn
                  >
                </template>
              </v-list-item>

              <v-list-item
                class="rounded-lg bg-grey-lighten-4 border"
                lines="two"
              >
                <template v-slot:prepend>
                  <v-avatar color="white" size="40" class="border">
                    <v-icon color="grey-darken-3">mdi-robot-confused</v-icon>
                  </v-avatar>
                </template>
                <v-list-item-title class="font-weight-bold"
                  >Robots.txt</v-list-item-title
                >
                <v-list-item-subtitle>{{
                  $t("seo.robots_hint")
                }}</v-list-item-subtitle>
                <template v-slot:append>
                  <v-btn
                    size="small"
                    variant="text"
                    color="primary"
                    @click="editRobots"
                    >{{ $t("dashboard.edit") }}</v-btn
                  >
                </template>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>

        <!-- Social Media Meta (Open Graph) -->
        <v-card class="rounded-xl mb-6 shadow-sm" elevation="1">
          <v-card-title
            class="pa-4 pb-0 text-subtitle-1 font-weight-bold d-flex align-center"
          >
            <v-icon color="blue" class="mr-2" size="20"
              >mdi-share-variant</v-icon
            >
            {{ $t("seo.og_title") }}
          </v-card-title>
          <v-card-text class="pa-4">
            <v-switch
              v-model="settings.enable_og_tags"
              :label="$t('seo.og_enable')"
              color="primary"
              hide-details
              density="comfortable"
            ></v-switch>
            <p class="text-caption text-grey-darken-1 mt-2">
              {{ $t("seo.og_desc") }}
            </p>
          </v-card-text>
        </v-card>

        <v-alert
          type="info"
          variant="tonal"
          :title="$t('seo.daily_tip')"
          class="rounded-xl"
        >
          <template v-slot:text>
            {{ $t("seo.daily_tip_desc") }}
          </template>
        </v-alert>
      </v-col>
    </v-row>

    <!-- Robots.txt Dialog -->
    <v-dialog v-model="robotsDialog" max-width="600">
      <v-card class="rounded-xl">
        <v-card-title class="pa-4 bg-primary text-white d-flex align-center">
          <v-icon class="mr-2">mdi-robot</v-icon>
          {{ $t("seo.edit_robots") }}
          <v-spacer></v-spacer>
          <v-btn
            icon
            color="white"
            variant="text"
            @click="robotsDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text class="pa-6">
          <v-textarea
            v-model="settings.robots_txt"
            variant="outlined"
            rows="10"
            :label="$t('seo.file_content')"
            :hint="$t('seo.robots_warning')"
            persistent-hint
            class="font-monospace"
          ></v-textarea>
        </v-card-text>
        <v-card-actions class="pa-4 d-flex justify-end">
          <v-btn variant="text" @click="robotsDialog = false">{{
            $t("dashboard.cancel")
          }}</v-btn>
          <v-btn
            color="primary"
            variant="flat"
            class="px-8 rounded-lg"
            @click="saveRobots"
            >{{ $t("seo.save_file") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Snackbar -->
    <v-snackbar v-model="snackbar.show" :color="snackbar.color" timeout="3000">
      <v-icon start size="20" v-if="snackbar.color === 'success'"
        >mdi-check-circle</v-icon
      >
      {{ snackbar.text }}
    </v-snackbar>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import api from "@/axios";
import SEOPreview from "@/components/Dashboard/SEOPreview.vue";
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();

const saving = ref(false);
const robotsDialog = ref(false);
const snackbar = ref({ show: false, text: "", color: "success" });

const settings = ref({
  site_name: "ZadStore",
  site_slogan: "",
  seo_title: "",
  seo_description: "",
  seo_keywords: "",
  enable_og_tags: true,
  robots_txt:
    "User-agent: *\nAllow: /\nSitemap: https://zadstore.com/sitemap.xml",
});

// Convert comma separated keywords to array for combobox and back
const seoKeywordsArray = computed({
  get: () =>
    settings.value.seo_keywords
      ? settings.value.seo_keywords.split(",").map((k) => k.trim())
      : [],
  set: (val) => {
    settings.value.seo_keywords = val.join(", ");
  },
});

const fetchSettings = async () => {
  try {
    const res = await api.get("/admin/store-settings");
    const settingsData = res.data;
    Object.keys(settingsData).forEach((key) => {
      if (settings.value[key] !== undefined) {
        // Handle boolean conversion if needed
        if (key === "enable_og_tags" && typeof settingsData[key] === "string") {
          settings.value[key] = settingsData[key] === "1";
        } else {
          settings.value[key] = settingsData[key];
        }
      }
    });
  } catch (e) {
    console.error(e);
  }
};

const saveSettings = async () => {
  saving.value = true;
  try {
    const payload = Object.keys(settings.value).map((key) => ({
      key,
      value:
        typeof settings.value[key] === "boolean"
          ? settings.value[key]
            ? "1"
            : "0"
          : settings.value[key],
    }));

    await api.post("/admin/store-settings/bulk", { settings: payload });

    snackbar.value = {
      show: true,
      text: t("seo.save_success"),
      color: "success",
    };
  } catch (e) {
    snackbar.value = {
      show: true,
      text: t("seo.save_error"),
      color: "error",
    };
  } finally {
    saving.value = false;
  }
};

const viewSitemap = () => {
  window.open("/", "_blank");
  snackbar.value = {
    show: true,
    text: t("seo.sitemap_info"),
    color: "info",
  };
};

const editRobots = () => {
  robotsDialog.value = true;
};

const saveRobots = async () => {
  await saveSettings();
  robotsDialog.value = false;
};

onMounted(fetchSettings);
</script>
