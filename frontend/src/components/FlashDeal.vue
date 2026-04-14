<template>
  <div
    class="flashdeal bg-grey-lighten-4 py-8"
    :dir="locale === 'ar' ? 'rtl' : 'ltr'"
  >
    <v-container>
      <div
        class="d-flex align-center justify-space-between mb-10 w-100 flex-wrap gap-4"
      >
        <div class="d-flex align-center">
          <v-icon color="error" size="36" class="me-3 tilt-shake"
            >mdi-lightning-bolt</v-icon
          >
          <h2 class="text-h4 font-weight-black text-primary">
            {{ $t("home.flash_deal.title") }}
          </h2>
        </div>

        <div
          class="countdown-wrapper d-flex align-center bg-error-lighten-5 px-4 py-2 rounded-xl border border-error"
        >
          <span class="text-body-2 font-weight-bold text-error me-3">{{
            $t("home.flash_deal.ends_in")
          }}</span>
          <div class="d-flex align-center gap-2">
            <div
              class="timer-box bg-error text-white font-weight-black rounded"
            >
              {{ hours }}
            </div>
            <span class="text-error font-weight-bold">:</span>
            <div
              class="timer-box bg-error text-white font-weight-black rounded"
            >
              {{ minutes }}
            </div>
            <span class="text-error font-weight-bold">:</span>
            <div
              class="timer-box bg-error text-white font-weight-black rounded"
            >
              {{ seconds }}
            </div>
          </div>
        </div>
      </div>

      <v-row
        v-if="!flashDeals.flashDeals || flashDeals.flashDeals.length === 0"
      >
        <v-col cols="12" sm="6" md="4" lg="3" v-for="num in 4" :key="num">
          <v-skeleton-loader
            type="image, article, button"
            elevation="1"
            class="rounded-xl"
          ></v-skeleton-loader>
        </v-col>
      </v-row>

      <swiper
        v-else
        :modules="modules"
        :slides-per-view="1.2"
        :space-between="15"
        :navigation="true"
        :autoplay="{ delay: 3500, disableOnInteraction: false }"
        :breakpoints="{
          480: { slidesPerView: 1.5, spaceBetween: 15 },
          768: { slidesPerView: 2.5, spaceBetween: 20 },
          960: { slidesPerView: 3, spaceBetween: 24 },
          1280: { slidesPerView: 4, spaceBetween: 24 },
        }"
        class="product-swiper px-2"
        :dir="locale === 'ar' ? 'rtl' : 'ltr'"
      >
        <swiper-slide
          v-for="product in flashDeals.flashDeals"
          :key="product.id"
          class="pb-5 pt-2 px-2"
        >
          <div style="max-width: 280px; width: 100%; margin: 0 auto">
            <ProductCard :product="product" />
          </div>
        </swiper-slide>
      </swiper>
    </v-container>
  </div>
  <PopUp />
</template>

<script setup>
import { ProductModule } from "@/store/Products.js";
import { VSkeletonLoader } from "vuetify/lib/components";
import PopUp from "./PopUp.vue";
import ProductCard from "./ProductCard.vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import { Navigation, Autoplay } from "swiper/modules";
import { onMounted, ref } from "vue";
import { useI18n } from "vue-i18n";

const { locale } = useI18n();
const flashDeals = ProductModule();
const modules = [Navigation, Autoplay];

const hours = ref("00");
const minutes = ref("00");
const seconds = ref("00");

const updateTimer = () => {
  const now = new Date();
  const endOfDay = new Date();
  endOfDay.setHours(23, 59, 59, 999);

  const diff = endOfDay - now;
  if (diff > 0) {
    hours.value = String(Math.floor((diff / (1000 * 60 * 60)) % 24)).padStart(
      2,
      "0"
    );
    minutes.value = String(Math.floor((diff / 1000 / 60) % 60)).padStart(
      2,
      "0"
    );
    seconds.value = String(Math.floor((diff / 1000) % 60)).padStart(2, "0");
  }
};

let timerInterval;

onMounted(async () => {
  await flashDeals.getFlashDeals();
  updateTimer();
  timerInterval = setInterval(updateTimer, 1000);
});

import { onUnmounted } from "vue";
onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval);
});
</script>

<style scoped>
/* Swiper styles */
.product-swiper {
  padding-bottom: 20px;
}
:deep(.swiper-button-next),
:deep(.swiper-button-prev) {
  color: rgb(var(--v-theme-primary)) !important;
  background: white;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

/* ================= Timer Styling ================= */
.timer-box {
  width: 38px;
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}

.countdown-wrapper {
  box-shadow: 0 4px 12px rgba(var(--v-theme-error), 0.1);
}
</style>
