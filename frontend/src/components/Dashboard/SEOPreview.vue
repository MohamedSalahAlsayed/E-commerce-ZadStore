<template>
  <v-card
    variant="outlined"
    class="seo-preview-card rounded-xl overflow-hidden mb-6"
  >
    <div
      class="pa-4 bg-grey-lighten-4 border-bottom d-flex align-center justify-space-between"
    >
      <div class="d-flex align-center gap-2">
        <v-icon color="primary">mdi-google</v-icon>
        <span class="text-subtitle-2 font-weight-bold">{{
          $t("seo.preview.google_title")
        }}</span>
      </div>
      <v-chip size="x-small" color="primary" variant="flat">{{
        $t("seo.preview.live_version")
      }}</v-chip>
    </div>

    <v-card-text class="pa-6">
      <div class="google-snippet shadow-sm rounded-lg pa-4 bg-white border">
        <!-- Google Header: Breadcrumb & URL -->
        <div class="d-flex align-center mb-1 overflow-hidden">
          <div class="google-favicon mr-2">
            <v-icon size="14" color="grey-darken-1">mdi-earth</v-icon>
          </div>
          <div class="google-url text-caption text-grey-darken-3 text-truncate">
            https://zadstore.com › {{ breadcrumb }} › ...
          </div>
        </div>

        <!-- Google Title -->
        <h3 class="google-title text-h6 mb-1">
          {{ displayTitle || $t("seo.preview.empty_title") }}
        </h3>

        <!-- Google Description -->
        <p class="google-description text-body-2 mb-0">
          {{ displayDescription || $t("seo.preview.empty_desc") }}
        </p>
      </div>

      <!-- Analysis Bar -->
      <v-row class="mt-4" dense>
        <v-col cols="12" md="6">
          <div class="d-flex align-center justify-space-between mb-1">
            <span class="text-caption font-weight-bold">{{
              $t("seo.preview.title_length", { count: titleLen })
            }}</span>
            <v-icon :color="titleColor" size="16">{{ titleIcon }}</v-icon>
          </div>
          <v-progress-linear
            :model-value="titleProgress"
            :color="titleColor"
            height="6"
            rounded
          ></v-progress-linear>
          <div class="text-caption mt-1" :class="`text-${titleColor}`">
            {{ titleMsg }}
          </div>
        </v-col>

        <v-col cols="12" md="6">
          <div class="d-flex align-center justify-space-between mb-1">
            <span class="text-caption font-weight-bold">{{
              $t("seo.preview.desc_length", { count: descLen })
            }}</span>
            <v-icon :color="descColor" size="16">{{ descIcon }}</v-icon>
          </div>
          <v-progress-linear
            :model-value="descProgress"
            :color="descColor"
            height="6"
            rounded
          ></v-progress-linear>
          <div class="text-caption mt-1" :class="`text-${descColor}`">
            {{ descMsg }}
          </div>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script setup>
import { computed } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps({
  title: String,
  description: String,
  breadcrumb: {
    type: String,
    default: "Products",
  },
});

const displayTitle = computed(() => props.title);
const displayDescription = computed(() => props.description);

const titleLen = computed(() => (props.title || "").length);
const descLen = computed(() => (props.description || "").length);

const titleProgress = computed(() =>
  Math.min((titleLen.value / 60) * 100, 100)
);
const descProgress = computed(() => Math.min((descLen.value / 160) * 100, 100));

const titleColor = computed(() => {
  if (titleLen.value < 30) return "warning";
  if (titleLen.value <= 60) return "success";
  return "error";
});

const titleIcon = computed(() => {
  if (titleLen.value < 30) return "mdi-alert-circle";
  if (titleLen.value <= 60) return "mdi-check-circle";
  return "mdi-alert";
});

const titleMsg = computed(() => {
  if (titleLen.value < 30) return t("seo.preview.title_short");
  if (titleLen.value <= 60) return t("seo.preview.title_perfect");
  return t("seo.preview.title_long");
});

const descColor = computed(() => {
  if (descLen.value < 80) return "warning";
  if (descLen.value <= 160) return "success";
  return "error";
});

const descIcon = computed(() => {
  if (descLen.value < 80) return "mdi-alert-circle";
  if (descLen.value <= 160) return "mdi-check-circle";
  return "mdi-alert";
});

const descMsg = computed(() => {
  if (descLen.value < 80) return t("seo.preview.desc_short");
  if (descLen.value <= 160) return t("seo.preview.desc_perfect");
  return t("seo.preview.desc_long");
});
</script>

<style scoped>
.seo-preview-card {
  border: 1px solid rgba(var(--v-theme-primary), 0.1);
  background: #fff;
}

.google-snippet {
  font-family: Arial, sans-serif;
  max-width: 600px;
  background: white;
}

.google-favicon {
  width: 20px;
  height: 20px;
  background: #f1f3f4;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.google-title {
  color: #1a0dab;
  font-size: 20px;
  line-height: 1.3;
  font-weight: 400;
  cursor: pointer;
}

.google-title:hover {
  text-decoration: underline;
}

.google-description {
  color: #4d5156;
  line-height: 1.58;
}

.google-url {
  font-size: 14px;
}
</style>
