<template>
  <v-container fluid class="px-10 text-center">
    <div class="custom-section-header mb-10">
      <v-chip class="custom-badge" variant="flat" size="large">
        {{ $t("features.chip") }}
      </v-chip>
      <h2 class="custom-main-title">{{ $t("features.title") }}</h2>
      <div class="custom-divider">
        <span class="bar long"></span>
        <span class="bar short"></span>
        <span class="bar short"></span>
      </div>
    </div>

    <v-row class="justify-center">
      <v-col
        cols="12"
        sm="6"
        md="4"
        lg="2"
        v-for="item in features"
        :key="item.id"
      >
        <v-card class="cardFeature py-6 h-100 elevation-2 rounded-xl">
          <v-icon color="primary" size="40" class="mb-3">
            {{ item.icon }}
          </v-icon>
          <v-card-title class="py-2 text-subtitle-1 font-weight-black">
            {{ item.title }}
          </v-card-title>
          <v-card-text class="text-grey-darken-1">
            {{ shortText(item.description) }}
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { computed } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const features = computed(() => [
  {
    id: 1,
    icon: "mdi-star-outline",
    title: t("features.f1_title"),
    description: t("features.f1_desc"),
  },
  {
    id: 2,
    icon: "mdi-warehouse",
    title: t("features.f2_title"),
    description: t("features.f2_desc"),
  },
  {
    id: 3,
    icon: "mdi-truck-fast-outline",
    title: t("features.f3_title"),
    description: t("features.f3_desc"),
  },
  {
    id: 4,
    icon: "mdi-shield-check-outline",
    title: t("features.f4_title"),
    description: t("features.f4_desc"),
  },
  {
    id: 5,
    icon: "mdi-help-circle-outline",
    title: t("features.f5_title"),
    description: t("features.f5_desc"),
  },
]);

// تعديل بسيط عشان النص العربي يظهر بشكل أفضل (زودنا الرقم لـ 45)
const shortText = (text) => {
  return text.length > 45 ? text.substring(0, 45) + "..." : text;
};
</script>

<style scoped>
/* تحسين تأثير الـ Hover */
.cardFeature {
  transition: all 0.4s ease-in-out;
  border: 1px solid transparent;
}

.cardFeature:hover {
  transform: translateY(-10px);
  background-color: #fff7ed;
  border-color: rgb(var(--v-theme-primary));
  box-shadow: 0 10px 25px rgba(249, 115, 22, 0.12) !important;
}
</style>
