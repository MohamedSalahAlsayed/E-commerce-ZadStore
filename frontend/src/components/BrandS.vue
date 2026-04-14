<template>
  <v-container class="px-10" :dir="locale === 'ar' ? 'rtl' : 'ltr'">
    <div class="d-flex align-center justify-center mb-10 mt-10">
      <div class="section-title-wrapper text-center">
        <h2 class="text-h4 font-weight-black text-primary mb-1">
          الماركات المميزة
        </h2>
        <div class="title-underline mx-auto"></div>
      </div>
    </div>

    <v-row v-if="!brandStore.products || brandStore.products.length === 0">
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
      :autoplay="{ delay: 4500, disableOnInteraction: false }"
      :breakpoints="{
        480: { slidesPerView: 1.5, spaceBetween: 15 },
        768: { slidesPerView: 2.5, spaceBetween: 20 },
        960: { slidesPerView: 3, spaceBetween: 24 },
        1280: { slidesPerView: 4, spaceBetween: 24 },
      }"
      class="product-swiper px-2 py-4"
      :dir="locale === 'ar' ? 'rtl' : 'ltr'"
    >
      <swiper-slide
        v-for="product in brandStore.products"
        :key="product.id"
        class="pb-10 pt-2 px-2"
      >
        <div style="max-width: 280px; width: 100%; margin: 0 auto">
          <ProductCard :product="product" />
        </div>
      </swiper-slide>
    </swiper>
  </v-container>

  <PopUp />
</template>

<script setup>
import { onMounted } from "vue";
import { useI18n } from "vue-i18n";
import { ProductModule } from "@/store/Products";
import PopUp from "./PopUp.vue";
import ProductCard from "./ProductCard.vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import { Navigation, Autoplay } from "swiper/modules";

const { locale } = useI18n();
const brandStore = ProductModule();
const modules = [Navigation, Autoplay];

onMounted(async () => {
  await brandStore.getProducts();
});
</script>

<style scoped>
/* ================= Section Header ================= */
.title-underline {
  width: 60px;
  height: 4px;
  background: rgb(var(--v-theme-primary));
  border-radius: 2px;
}

/* ================= Swiper styles ================= */
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
</style>
