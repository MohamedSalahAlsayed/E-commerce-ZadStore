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
                </div>
              </div>

              <v-card-text class="flex-grow-1 d-flex flex-column pa-4 pt-5">
                <div class="category-text mb-2">عرض محدود</div>
                <h3 class="product-title mb-2" :title="product.title">
                  {{ shortTitle(product.title) }}
                </h3>

                <div class="d-flex align-center mb-4">
                  <v-rating
                    :model-value="product.rating"
                    readonly
                    color="amber-darken-2"
                    density="compact"
                    half-increments
                    size="14"
                  ></v-rating>
                  <span
                    class="rating-count mr-2"
                    style="font-size: 11px; color: #777"
                    >({{ product.rating }})</span
                  >
                </div>

                <div
                  class="mt-auto pt-3"
                  style="border-top: 1px solid rgba(0, 0, 0, 0.05)"
                >
                  <div class="d-flex justify-space-between align-end">
                    <div class="price-wrapper d-flex flex-column">
                      <del
                        v-if="product.discountPercentage"
                        class="old-price mb-1"
                        style="font-size: 13px; color: #999"
                      >
                        {{ Number(product.price).toLocaleString() }} ج.م
                      </del>
                      <div
                        class="current-price text-error d-flex align-end gap-1"
                        style="line-height: 1"
                      >
                        <span style="font-size: 24px; font-weight: 900">
                          {{
                            Math.ceil(
                              product.price -
                                product.price *
                                  (product.discountPercentage / 100)
                            ).toLocaleString()
                          }}
                        </span>
                        <small
                          style="
                            font-size: 14px;
                            font-weight: 800;
                            margin-bottom: 2px;
                          "
                          >ج.م</small
                        >
                      </div>
                    </div>
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
  border-radius: 20px !important;
  transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
  border: 1px solid rgba(0, 0, 0, 0.03) !important;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02) !important;
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08) !important;
  border-color: rgba(var(--v-theme-primary), 0.15) !important;
}

/* ================= Media Section ================= */
.card-media-wrapper {
  position: relative;
  overflow: hidden;
  border-radius: 20px 20px 0 0;
}

.product-img {
  transition: transform 1.2s cubic-bezier(0.23, 1, 0.32, 1);
}

.product-card:hover .product-img {
  transform: scale(1.1);
}

.media-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  backdrop-filter: blur(2px);
  transition: all 0.4s ease;
  z-index: 2;
}

.product-card:hover .media-overlay {
  opacity: 1;
}

.quick-view-btn {
  background: white !important;
  color: black !important;
  border-radius: 50px !important;
  font-weight: 900 !important;
  padding: 0 28px !important;
  height: 46px !important;
  font-size: 13px !important;
  transform: translateY(20px);
  transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2) !important;
}

.product-card:hover .quick-view-btn {
  transform: translateY(0);
}

/* ================= Badge System ================= */
.badge-container {
  position: absolute;
  top: 15px;
  right: 15px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  z-index: 3;
}

.discount-badge {
  background: linear-gradient(135deg, #ff4757, #ff6b81);
  color: white;
  padding: 6px 12px;
  border-radius: 8px;
  font-weight: 900;
  font-size: 13px;
  box-shadow: 0 5px 15px rgba(255, 71, 87, 0.3);
}

.flash-tag {
  background: linear-gradient(45deg, #ff4757, #ff6b81);
  color: white;
  padding: 5px 14px;
  border-radius: 50px;
  font-size: 11px;
  font-weight: 900;
  text-transform: uppercase;
  box-shadow: 0 5px 15px rgba(255, 71, 87, 0.3);
  letter-spacing: 0.5px;
}

/* ================= Floating Actions ================= */
.floating-actions {
  position: absolute;
  top: 15px;
  left: 15px;
  z-index: 3;
}

.action-btn {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(0, 0, 0, 0.05);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
  transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
  color: #444;
}

.action-btn:hover {
  transform: rotate(10deg) scale(1.1);
  background: white;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.action-btn.active {
  color: #ff4757;
  background: white;
  border-color: rgba(255, 71, 87, 0.2);
}

/* ================= Content Section ================= */
.category-text {
  font-size: 10px;
  color: #fb923c;
  font-weight: 900;
  text-transform: uppercase;
  background: rgba(251, 146, 60, 0.1);
  padding: 3px 10px;
  border-radius: 6px;
  letter-spacing: 0.5px;
  display: inline-block;
  width: fit-content;
}

.product-title {
  font-size: 17px;
  font-weight: 900;
  color: #1a1a1a;
  line-height: 1.5;
  height: 50px;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

.current-price {
  color: #ff4757 !important;
}

/* ================= Buttons ================= */
.add-to-cart-btn {
  border-radius: 14px !important;
  font-weight: 900 !important;
  font-size: 14px !important;
  letter-spacing: 0.5px !important;
  transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1) !important;
  text-transform: none !important;
  box-shadow: 0 4px 15px rgba(var(--v-theme-primary), 0.2) !important;
}

.add-to-cart-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 25px rgba(var(--v-theme-primary), 0.35) !important;
}

.details-icon-btn {
  border: 1px solid rgba(var(--v-theme-primary), 0.1) !important;
  background: rgba(var(--v-theme-primary), 0.03) !important;
  border-radius: 14px !important;
}

.details-icon-btn:hover {
  background: rgba(var(--v-theme-primary), 0.08) !important;
  transform: translateY(-2px);
}

.gap-3 {
  gap: 12px;
}
</style>
