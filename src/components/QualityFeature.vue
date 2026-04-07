<template>
  <v-container fluid class="px-10 text-center">
    <v-container>
      <h3
        class="my-10 py-10 section-titl text-h4 font-weight-bold text-primary"
      >
        {{ $t("features.title") }}
      </h3>
    </v-container>

    <v-row class="justify-center">
      <v-col
        cols="12"
        sm="6"
        md="4"
        lg="2"
        v-for="item in features"
        :key="item.id"
      >
        <v-card class="cardFeature py-10 h-100 elevation-2 rounded-xl">
          <v-icon style="color: #0c1d65 !important" size="40" class="mb-3">
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
  transform: translateY(-10px); /* طلوع لفوق بدل التكبير عشان بيكون أشيك */
  background-color: rgb(139, 222, 247);
  border-color: #0c1d65;
  box-shadow: 0 10px 25px rgba(12, 29, 101, 0.15) !important;
}

.section-title {
  position: relative;
  display: inline-block;
  padding-bottom: 12px;
  color: rgb(var(--v-theme-primary));
}

.section-title::before {
  content: "";
  position: absolute;
  right: 0; /* تم التعديل لـ right بدل left عشان العربي من اليمين للشمال */
  /* أو ممكن نخليها في النص لو العنوان متوسطن */
  left: 50%;
  transform: translateX(-50%);
  bottom: 0;
  width: 60px; /* تقصير الخط ليكون أكثر أناقة */
  height: 4px; /* سُمك الخط */
  background: rgb(var(--v-theme-primary)); /* لون الخط */
  border-radius: 4px;
}
</style>
