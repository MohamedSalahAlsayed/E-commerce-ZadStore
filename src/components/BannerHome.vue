<template>
  <v-container fluid class="pa-0 mb-8 px-4 position-relative">
    <div class="banner-wrapper rounded-xl overflow-hidden custom-shadow">
      <swiper
        :modules="modules"
        :slides-per-view="1"
        :loop="true"
        :effect="'fade'"
        :pagination="{ clickable: true, dynamicBullets: true }"
        :navigation="true"
        :autoplay="{ delay: 5000, disableOnInteraction: false }"
        class="custom-swiper rounded-xl"
        :dir="locale === 'ar' ? 'rtl' : 'ltr'"
      >
        <swiper-slide v-for="(item, i) in items" :key="i">
          <div
            class="slide-content d-flex align-center justify-center position-relative"
          >
            <v-img :src="item.src" cover height="100%" width="100%">
              <div class="overlay"></div>
            </v-img>

            <div class="text-content text-center px-4">
              <h1
                class="text-h3 text-md-h2 text-white font-weight-black mb-4 title-animation"
                style="text-shadow: 0 4px 12px rgba(0, 0, 0, 0.5)"
              >
                {{ item.title }}
              </h1>
              <p
                class="text-h6 text-md-h5 text-grey-lighten-2 mb-8 subtitle-animation"
                style="text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5)"
              >
                {{ item.subtitle }}
              </p>
              <v-btn
                v-if="item.link"
                :href="item.link"
                color="primary"
                size="x-large"
                class="rounded-pill font-weight-bold px-10 btn-animation"
                elevation="8"
              >
                {{ $t("home.banners.shop_now") }}
                <v-icon class="mr-2">mdi-cart-arrow-right</v-icon>
              </v-btn>
            </div>
          </div>
        </swiper-slide>
      </swiper>
    </div>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/effect-fade";
import "swiper/css/pagination";
import "swiper/css/navigation";
import { useI18n } from "vue-i18n";
import { EffectFade, Pagination, Navigation, Autoplay } from "swiper/modules";
import api from "@/axios";

const { locale, t } = useI18n();
const modules = [EffectFade, Pagination, Navigation, Autoplay];

const items = ref([]);
const loading = ref(true);

onMounted(async () => {
  try {
    const res = await api.get("/banners");
    if (res.data && res.data.length > 0) {
      items.value = res.data.map((b) => ({
        src: b.image,
        title: b.title || "",
        subtitle: "", // Admin UI doesn't provide subtitle natively right now
        link: b.link,
      }));
    } else {
      // Fallback
      items.value = [
        {
          src: "https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80",
          title: t("home.banners.default_title"),
          subtitle: t("home.banners.default_subtitle"),
        },
      ];
    }
  } catch (e) {
    console.error("Error fetching banners:", e);
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.custom-shadow {
  box-shadow: 0 16px 40px -10px rgba(0, 0, 0, 0.2) !important;
}

.banner-wrapper {
  max-width: 1400px; /* Slightly wider */
  margin: 0 auto;
}

.custom-swiper {
  height: 500px;
  width: 100%;
}

@media (max-width: 960px) {
  .custom-swiper {
    height: 400px;
  }
}

@media (max-width: 600px) {
  .custom-swiper {
    height: 300px;
  }
}

.slide-content {
  height: 100%;
  width: 100%;
}

.overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    135deg,
    rgba(0, 0, 0, 0.65) 0%,
    rgba(30, 30, 30, 0.25) 100%
  );
  z-index: 1;
}

.text-content {
  position: absolute;
  z-index: 2;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 90%;
  max-width: 800px;
}

/* Animations that trigger when a slide becomes active */
.swiper-slide-active .title-animation {
  animation: slideUpFade 1s cubic-bezier(0.16, 1, 0.3, 1) forwards 0.3s;
  opacity: 0;
  transform: translateY(40px);
}

.swiper-slide-active .subtitle-animation {
  animation: slideUpFade 1s cubic-bezier(0.16, 1, 0.3, 1) forwards 0.5s;
  opacity: 0;
  transform: translateY(40px);
}

.swiper-slide-active .btn-animation {
  animation: slideUpFade 1s cubic-bezier(0.16, 1, 0.3, 1) forwards 0.7s;
  opacity: 0;
  transform: translateY(40px);
}

@keyframes slideUpFade {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Swiper custom overrides */
:deep(.swiper-pagination-bullet) {
  background: white !important;
  opacity: 0.5 !important;
  width: 10px !important;
  height: 10px !important;
  transition: all 0.3s ease !important;
}
:deep(.swiper-pagination-bullet-active) {
  background: rgb(var(--v-theme-primary)) !important;
  opacity: 1 !important;
  width: 24px !important;
  border-radius: 5px !important;
  box-shadow: 0 0 8px rgba(var(--v-theme-primary), 0.6) !important;
}

:deep(.swiper-button-next),
:deep(.swiper-button-prev) {
  color: white !important;
  background: rgba(255, 255, 255, 0.1) !important;
  backdrop-filter: blur(4px) !important;
  width: 50px !important;
  height: 50px !important;
  border-radius: 50% !important;
  transition: all 0.3s ease !important;
  border: 1px solid rgba(255, 255, 255, 0.2);
}
:deep(.swiper-button-next:hover),
:deep(.swiper-button-prev:hover) {
  background: rgb(var(--v-theme-primary)) !important;
  border-color: rgb(var(--v-theme-primary)) !important;
  transform: scale(1.1) !important;
}
:deep(.swiper-button-next::after),
:deep(.swiper-button-prev::after) {
  font-size: 20px !important;
  font-weight: bold !important;
}
</style>
