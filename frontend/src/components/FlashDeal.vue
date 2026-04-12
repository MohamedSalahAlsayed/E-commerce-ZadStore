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
            <v-card class="product-card h-100 d-flex flex-column" elevation="0">
              <div class="card-media-wrapper">
                <v-img
                  :src="product.thumbnail"
                  height="220"
                  cover
                  class="bg-grey-lighten-4 product-img"
                />

                <div class="media-overlay">
                  <v-btn
                    class="quick-view-btn"
                    @click="QuickVeiw.open(product.id)"
                    variant="flat"
                  >
                    <v-icon size="18" class="ml-1">mdi-eye</v-icon>
                    نظرة سريعة
                  </v-btn>
                </div>

                <div class="floating-actions">
                  <button
                    class="action-btn"
                    :class="{ active: isFavourite(product.id) }"
                    @click.stop="toggleFavourite(product)"
                  >
                    <v-icon size="20">
                      {{
                        isFavourite(product.id)
                          ? "mdi-heart"
                          : "mdi-heart-outline"
                      }}
                    </v-icon>
                  </button>
                </div>

                <div class="badge-container">
                  <div class="discount-badge" v-if="product.discountPercentage">
                    -{{ Math.round(product.discountPercentage) }}%
                  </div>
                  <div class="flash-tag">عرض فلاش</div>
                </div>
              </div>

              <v-card-text class="flex-grow-1 d-flex flex-column pa-4">
                <div class="category-text mb-1">عرض محدود</div>
                <h3 class="product-title mb-2" :title="product.title">
                  {{ shortTitle(product.title) }}
                </h3>

                <div class="d-flex align-center mb-3">
                  <v-rating
                    :model-value="product.rating"
                    readonly
                    color="amber-darken-1"
                    density="compact"
                    half-increments
                    size="16"
                  ></v-rating>
                  <span class="rating-count mr-2">({{ product.rating }})</span>
                </div>

                <div class="mt-auto">
                  <div class="price-wrapper d-flex align-end gap-2">
                    <span class="current-price text-error">
                      {{
                        Math.ceil(
                          product.price -
                            product.price * (product.discountPercentage / 100)
                        ).toLocaleString()
                      }}
                      <small>ج.م</small>
                    </span>
                    <del v-if="product.discountPercentage" class="old-price">
                      {{ Number(product.price).toLocaleString() }} ج.م
                    </del>
                  </div>
                </div>
              </v-card-text>

              <v-card-actions class="pa-4 pt-0 d-flex gap-3">
                <v-btn
                  class="add-to-cart-btn flex-grow-1"
                  :color="isItemInCart(product.id) ? 'success' : 'primary'"
                  variant="flat"
                  height="48"
                  @click.stop="toggleCart(product)"
                >
                  <v-icon start size="20" class="ml-2">
                    {{
                      isItemInCart(product.id)
                        ? "mdi-cart-check"
                        : "mdi-cart-plus"
                    }}
                  </v-icon>
                  <span class="text-button font-weight-black">
                    {{
                      isItemInCart(product.id)
                        ? $t("products.added_to_cart")
                        : $t("products.add_to_cart")
                    }}
                  </span>
                </v-btn>

                <v-btn
                  icon
                  variant="tonal"
                  color="primary"
                  height="48"
                  width="48"
                  class="details-icon-btn rounded-lg"
                  @click.stop="showDetails(product)"
                >
                  <v-icon size="22">mdi-eye-outline</v-icon>
                </v-btn>
              </v-card-actions>
            </v-card>
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
import { usePopUpQuickView } from "@/store/PopUp";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import { Navigation, Autoplay } from "swiper/modules";
import { onMounted, watch, inject, ref } from "vue";
import { AddFavProduct } from "../store/Favourate";
import { AddInCart } from "../store/Cart";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";

const { locale } = useI18n();
const router = useRouter();
const QuickVeiw = usePopUpQuickView();
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

const shortTitle = (text) => {
  if (!text) return "";
  return text.length > 25 ? text.substring(0, 25) + "..." : text;
};

const cartStore = AddFavProduct();
const FavItem = cartStore.FavItem;

const CartItemStore = AddInCart();
const emitter = inject("emitter");

const isItemInCart = (id) => {
  return CartItemStore.CartItem.some((item) => item.id === id);
};

const toggleCart = (product) => {
  if (!localStorage.getItem("auth_token")) {
    router.push("/Auth/LogIn");
    return;
  }
  if (isItemInCart(product.id)) {
    CartItemStore.removeFromCart(product.id);
  } else {
    const item = { ...product, quantity: 1 };
    CartItemStore.AddInCartItem(item);
    emitter.emit("toggleCart");
    emitter.emit("showMessage", { text: product.title, color: "success" });
  }
};

const toggleFavourite = (product) => {
  if (!localStorage.getItem("auth_token")) {
    router.push("/Auth/LogIn");
    return;
  }
  const exists = cartStore.FavItem.find((p) => p.id === product.id);
  if (exists) {
    cartStore.removeFromCart(product.id);
  } else {
    cartStore.AddFavProductItem({ ...product, quantity: 1 });
  }
};

const isFavourite = (id) => cartStore.FavItem.some((p) => p.id === id);

const showDetails = (product) => {
  router.push({ name: "ProductDetail", params: { productId: product.id } });
};

watch(
  () => FavItem,
  (newVal) => {
    localStorage.setItem("cart-favourate", JSON.stringify(newVal));
  },
  { deep: true }
);

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

/* ================= Base Styles ================= */
.product-card {
  position: relative;
  background: white;
  border-radius: 16px !important;
  transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
  border: 1px solid #eee !important;
  overflow: hidden;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08) !important;
}

/* ================= Media Section ================= */
.card-media-wrapper {
  position: relative;
  overflow: hidden;
}

.product-img {
  transition: transform 0.8s ease;
}

.product-card:hover .product-img {
  transform: scale(1.05);
}

.media-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: all 0.3s ease;
  z-index: 2;
}

.product-card:hover .media-overlay {
  opacity: 1;
}

.quick-view-btn {
  background: white !important;
  color: black !important;
  border-radius: 50px !important;
  font-weight: 800 !important;
  padding: 0 24px !important;
  height: 42px !important;
}

/* ================= Badge System ================= */
.badge-container {
  position: absolute;
  top: 12px;
  right: 12px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  z-index: 3;
}

.discount-badge {
  background: #ff4757;
  color: white;
  padding: 4px 10px;
  border-radius: 6px;
  font-weight: 800;
  font-size: 12px;
}

.flash-tag {
  background: #ff9f43;
  color: white;
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 10px;
  font-weight: 900;
  text-transform: uppercase;
}

/* ================= Floating Actions ================= */
.floating-actions {
  position: absolute;
  top: 12px;
  left: 12px;
  z-index: 3;
}

.action-btn {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: white;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  transition: all 0.2s ease;
  color: #555;
}

.action-btn:hover {
  transform: scale(1.1);
}

.action-btn.active {
  color: #ff4757;
}

/* ================= Content Section ================= */
.category-text {
  font-size: 11px;
  color: #999;
  font-weight: 700;
  text-transform: uppercase;
}

.product-title {
  font-size: 16px;
  font-weight: 800;
  color: #111;
  line-height: 1.4;
  height: 44px;
}

.current-price {
  font-size: 20px;
  font-weight: 900;
}

.old-price {
  font-size: 14px;
  color: #aaa;
  text-decoration: line-through;
}

/* ================= Professional Solid Buttons ================= */
.add-to-cart-btn {
  border-radius: 10px !important;
  font-weight: 900 !important;
  transition: all 0.2s ease !important;
  text-transform: none !important;
}

.add-to-cart-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(var(--v-theme-primary), 0.25) !important;
}

.details-icon-btn {
  border: 1px solid rgba(var(--v-theme-primary), 0.1) !important;
}

.gap-3 {
  gap: 12px;
}
</style>
