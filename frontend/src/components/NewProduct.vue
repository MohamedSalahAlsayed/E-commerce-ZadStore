<template>
  <div class="newproduct py-10 bg-white">
    <v-container>
      <div class="custom-section-header mb-10">
        <v-chip class="custom-badge" variant="flat" size="large">
          {{ $t("home.new_products.chip") }}
        </v-chip>
        <h2 class="custom-main-title">{{ $t("home.new_products.title") }}</h2>
        <div class="custom-divider">
          <span class="bar long"></span>
          <span class="bar short"></span>
          <span class="bar short"></span>
        </div>
      </div>

      <v-row>
        <v-col cols="12" md="8" v-if="!flashDeals.products.length">
          <v-row>
            <v-col cols="12" sm="6" md="4" v-for="num in 3" :key="num">
              <v-skeleton-loader
                type="image, article, button"
                elevation="1"
                class="rounded-xl"
              ></v-skeleton-loader>
            </v-col>
          </v-row>
        </v-col>
        <v-col cols="12" md="12" v-else>
          <swiper
            :modules="modules"
            :slides-per-view="1"
            :space-between="15"
            :navigation="true"
            :breakpoints="{
              600: { slidesPerView: 2, spaceBetween: 15 },
              960: { slidesPerView: 3, spaceBetween: 24 },
              1280: { slidesPerView: 4, spaceBetween: 24 },
            }"
            class="product-swiper px-2 pt-4 pb-12"
            :dir="locale === 'ar' ? 'rtl' : 'ltr'"
          >
            <swiper-slide
              v-for="product in flashDeals.products.slice(0, 10)"
              :key="product.id"
              class="pb-5 pt-2 px-2"
            >
              <ProductCard :product="product" />
            </swiper-slide>
          </swiper>
        </v-col>
      </v-row>
    </v-container>
    <PopUp />
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { useI18n } from "vue-i18n";
import { ProductModule } from "@/store/Products.js";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation, Autoplay } from "swiper/modules";
import ProductCard from "./ProductCard.vue";
import PopUp from "./PopUp.vue";
import "swiper/css";
import "swiper/css/navigation";

const { locale } = useI18n();
const flashDeals = ProductModule();
const modules = [Navigation, Autoplay];

onMounted(async () => {
  await flashDeals.getProducts();
});
</script>

<style scoped>
/* ================= Swiper settings ================= */
.product-swiper {
  padding-bottom: 40px !important;
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

/* ================= Slide Group Fixes ================= */
:deep(.v-slide-group__content) {
  padding: 10px 0;
}
</style>
