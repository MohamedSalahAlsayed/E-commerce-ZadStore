<template>
  <section class="why-shop-section py-16">
    <v-container>
      <h2
        class="section-titl text-h3 font-weight-bold text-center text-white mb-12"
      >
        {{ $t("shop_with_us.why_shop") }}
      </h2>

      <v-row justify="center">
        <v-col
          cols="12"
          sm="6"
          md="3"
          v-for="feature in features"
          :key="feature.id"
        >
          <v-card
            class="feature-card h-100 py-8 px-4 text-center rounded-xl elevation-3"
          >
            <div class="icon-container mb-4">
              <v-icon size="50" color="amber-darken-1">
                {{ feature.icon }}
              </v-icon>
            </div>

            <v-card-title
              class="card-title text-subtitle-1 font-weight-black text-primary text-wrap pb-2"
            >
              {{ feature.title }}
            </v-card-title>

            <v-card-text class="text-grey-darken-1 text-body-2 px-2">
              {{ feature.description }}
            </v-card-text>

            <a
              :href="feature.url"
              class="learn-more font-weight-bold mt-2 d-inline-block"
            >
              {{ feature.linkText }}
              <v-icon
                size="small"
                class="mx-1"
                :style="isRtl ? 'transform: rotate(180deg)' : ''"
                >mdi-arrow-right</v-icon
              >
            </a>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </section>
</template>

<script setup>
import { computed } from "vue";
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();

const isRtl = computed(() => locale.value === "ar");

// تعريب البيانات مع إضافة نصوص واقعية
const features = computed(() => [
  {
    id: 1,
    icon: "mdi-parachute",
    title: t("shop_with_us.feature_1_title"),
    description: t("shop_with_us.feature_1_desc"),
    linkText: t("shop_with_us.learn_more"),
    url: "#",
  },
  {
    id: 2,
    icon: "mdi-flash",
    title: t("shop_with_us.feature_2_title"),
    description: t("shop_with_us.feature_2_desc"),
    linkText: t("shop_with_us.learn_more"),
    url: "#",
  },
  {
    id: 3,
    icon: "mdi-calendar-check",
    title: t("shop_with_us.feature_3_title"),
    description: t("shop_with_us.feature_3_desc"),
    linkText: t("shop_with_us.learn_more"),
    url: "#",
  },
  {
    id: 4,
    icon: "mdi-hand-coin",
    title: t("shop_with_us.feature_4_title"),
    description: t("shop_with_us.feature_4_desc"),
    linkText: t("shop_with_us.learn_more"),
    url: "#",
  },
]);
</script>

<style scoped>
/* 1. تنسيق القسم بالكامل والخلفية */
.why-shop-section {
  background-color: #fafafa;
  position: relative;
  overflow: hidden;
}

.why-shop-section::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: radial-gradient(
    circle at 20% 30%,
    rgba(255, 152, 0, 0.05) 0%,
    transparent 100%
  );
  pointer-events: none;
}

/* 2. تنسيق الكارت وتأثير الـ Hover */
.feature-card {
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  border: 1px solid rgba(0, 0, 0, 0.05) !important;
  background-color: #ffffff;
}

.feature-card:hover {
  transform: translateY(-12px);
  box-shadow: 0 20px 40px rgba(255, 152, 0, 0.15) !important;
  border-color: rgba(255, 152, 0, 0.3) !important;
}

.icon-container {
  width: 80px;
  height: 80px;
  background: #fff3e0;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
  transition: all 0.3s ease;
}

.feature-card:hover .icon-container {
  background: #ff9800;
  transform: rotate(10deg);
}

.feature-card:hover .icon-container .v-icon {
  color: white !important;
}

/* 3. إصلاح مشكلة نزول العنوان على سطرين بشكل سيء */
.card-title {
  white-space: normal;
  line-height: 1.4;
  font-family: "Outfit", "Cairo", sans-serif;
}

/* 4. تنسيق زر "اعرف المزيد" */
.learn-more {
  color: #ff9800;
  text-decoration: none;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0.8;
}

.learn-more:hover {
  opacity: 1;
  gap: 8px;
}
</style>
