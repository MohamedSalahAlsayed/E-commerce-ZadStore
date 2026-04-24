<template>
  <div
    class="flashdeal bg-grey-lighten-4 py-4"
    :dir="locale === 'ar' ? 'rtl' : 'ltr'"
  >
    <v-container>
      <div class="custom-section-header mb-10">
        <v-chip class="custom-badge" variant="flat" size="large">
          <v-icon start size="20" class="me-2 tilt-shake" color="error"
            >mdi-lightning-bolt</v-icon
          >
          {{ locale === "ar" ? "عروض لفترة محدودة" : "Limited Time Offers" }}
        </v-chip>
        <h2 class="custom-main-title">{{ $t("home.flash_deal.title") }}</h2>
        <div class="custom-divider">
          <span class="bar long"></span>
          <span class="bar short"></span>
          <span class="bar short"></span>
        </div>

        <div
          class="countdown-wrapper d-flex align-center bg-error-lighten-5 px-6 py-3 rounded-xl border border-error mt-8"
        >
          <span
            class="text-body-1 font-weight-black text-error me-4 d-none d-sm-inline"
            >{{ $t("home.flash_deal.ends_in") }}</span
          >
          <div class="d-flex align-center gap-3">
            <div
              class="timer-box bg-error text-white font-weight-black rounded-lg"
            >
              {{ hours }}
            </div>
            <span class="text-error font-weight-black">:</span>
            <div
              class="timer-box bg-error text-white font-weight-black rounded-lg"
            >
              {{ minutes }}
            </div>
            <span class="text-error font-weight-black">:</span>
            <div
              class="timer-box bg-error text-white font-weight-black rounded-lg"
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
        :slides-per-view="1"
        :space-between="15"
        :navigation="true"
        :autoplay="{ delay: 3500, disableOnInteraction: false }"
        :breakpoints="{
          600: { slidesPerView: 2, spaceBetween: 15 },
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
          <ProductCard :product="product" />
        </swiper-slide>
      </swiper>
    </v-container>
  </div>
  <PopUp />
</template>

<script setup>
import { ProductModule } from "@/store/Products.js";
import { useSettingsStore } from "@/store/Settings.js";
import { VSkeletonLoader } from "vuetify/lib/components";
import PopUp from "./PopUp.vue";
import ProductCard from "./ProductCard.vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import { Navigation, Autoplay } from "swiper/modules";
import { onMounted, ref, onUnmounted } from "vue";
import { useI18n } from "vue-i18n";

const { locale } = useI18n();
const flashDeals = ProductModule();
const settingsStore = useSettingsStore();
const modules = [Navigation, Autoplay];

const hours = ref("00");
const minutes = ref("00");
const seconds = ref("00");

const updateTimer = () => {
  const now = new Date();
  let endOfDay;

  if (settingsStore.guestHome?.flashDealEndsAt) {
    endOfDay = new Date(settingsStore.guestHome.flashDealEndsAt);
    if (isNaN(endOfDay.getTime())) {
      endOfDay = new Date();
      endOfDay.setHours(23, 59, 59, 999);
    }
  } else {
    endOfDay = new Date();
    endOfDay.setHours(23, 59, 59, 999);
  }

  const diff = endOfDay - now;
  if (diff > 0) {
    // Calculate total hours to handle dates more than 24 hours away
    hours.value = String(Math.floor(diff / (1000 * 60 * 60))).padStart(2, "0");
    minutes.value = String(Math.floor((diff / 1000 / 60) % 60)).padStart(
      2,
      "0"
    );
    seconds.value = String(Math.floor((diff / 1000) % 60)).padStart(2, "0");
  } else {
    hours.value = "00";
    minutes.value = "00";
    seconds.value = "00";
  }
};

let timerInterval;

onMounted(async () => {
  await flashDeals.getFlashDeals();
  // Ensure settings are loaded if not already
  if (!settingsStore.guestHome) {
    await settingsStore.fetchSettings();
  }
  updateTimer();
  timerInterval = setInterval(updateTimer, 1000);
});

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval);
});
</script>

<style scoped>
/* Swiper styles */
.product-swiper {
  padding-bottom: 30px;
}

:deep(.swiper-button-next),
:deep(.swiper-button-prev) {
  color: rgb(var(--v-theme-primary)) !important;
  background: white !important;
  width: 50px !important;
  height: 50px !important;
  border-radius: 50%;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12) !important;
  transition: all 0.3s ease !important;
  border: 1px solid rgba(0, 0, 0, 0.05) !important;
}

:deep(.swiper-button-next:hover),
:deep(.swiper-button-prev:hover) {
  transform: scale(1.1);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15) !important;
}

:deep(.swiper-button-next::after),
:deep(.swiper-button-prev::after) {
  font-size: 20px !important;
  font-weight: bold;
}

/* ================= Timer Styling ================= */
.timer-box {
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  letter-spacing: -0.5px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.countdown-wrapper {
  box-shadow: 0 8px 24px rgba(var(--v-theme-error), 0.15) !important;
  backdrop-filter: blur(4px);
}
</style>
