<template>
  <div class="newproduct py-10 bg-white">
    <v-container>
      <div class="d-flex align-center mb-10">
        <div class="section-title-wrapper">
          <h2 class="text-h4 font-weight-black text-primary mb-1">
            أحدث المنتجات
          </h2>
          <div class="title-underline"></div>
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
            :slides-per-view="1.2"
            :space-between="15"
            :navigation="true"
            :breakpoints="{
              480: { slidesPerView: 1.5, spaceBetween: 15 },
              768: { slidesPerView: 2.5, spaceBetween: 20 },
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
              <div style="max-width: 280px; width: 100%; margin: 0 auto">
                <v-card
                  class="product-card d-flex flex-column h-100"
                  elevation="0"
                >
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
                      <div class="new-tag">جديد</div>
                      <div
                        class="discount-badge"
                        v-if="product.discountPercentage"
                      >
                        -{{ Math.round(product.discountPercentage) }}%
                      </div>
                    </div>
                  </div>

                  <v-card-text class="flex-grow-1 d-flex flex-column pa-4">
                    <div class="category-text mb-1">وصل حديثاً</div>
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
                        size="14"
                      ></v-rating>
                      <span class="rating-count mr-2"
                        >({{ product.rating }})</span
                      >
                    </div>

                    <div class="mt-auto">
                      <div class="price-wrapper d-flex align-end gap-2">
                        <span class="current-price">
                          {{
                            Math.ceil(
                              product.price -
                                product.price *
                                  (product.discountPercentage / 100)
                            ).toLocaleString()
                          }}
                          <small>ج.م</small>
                        </span>
                        <del
                          v-if="product.discountPercentage"
                          class="old-price"
                        >
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
                      height="44"
                      @click.stop="toggleCart(product)"
                    >
                      <v-icon start size="18" class="ml-2">
                        {{
                          isItemInCart(product.id)
                            ? "mdi-cart-check"
                            : "mdi-cart-plus"
                        }}
                      </v-icon>
                      <span class="text-caption font-weight-black">
                        {{
                          isItemInCart(product.id) ? "تمت الإضافة" : "أضف للسلة"
                        }}
                      </span>
                    </v-btn>

                    <v-btn
                      icon
                      variant="tonal"
                      color="primary"
                      height="44"
                      width="44"
                      class="details-icon-btn rounded-lg"
                      @click.stop="showDetails(product)"
                    >
                      <v-icon size="20">mdi-eye-outline</v-icon>
                    </v-btn>
                  </v-card-actions>
                </v-card>
              </div>
            </swiper-slide>
          </swiper>
        </v-col>
      </v-row>
    </v-container>
    <PopUp />
  </div>
</template>

<script setup>
import { onMounted, inject } from "vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import { ProductModule } from "@/store/Products.js";
import { usePopUpQuickView } from "@/store/PopUp";
import { AddFavProduct } from "../store/Favourate";
import { AddInCart } from "../store/Cart";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation, Autoplay } from "swiper/modules";
import PopUp from "./PopUp.vue";
import "swiper/css";
import "swiper/css/navigation";

const { locale } = useI18n();
const router = useRouter();
const QuickVeiw = usePopUpQuickView();
const flashDeals = ProductModule();
const cartStore = AddFavProduct();
const CartItemStore = AddInCart();
const emitter = inject("emitter");
const modules = [Navigation, Autoplay];

const shortTitle = (text) => {
  if (!text) return "";
  return text.length > 25 ? text.substring(0, 22) + "..." : text;
};

const isItemInCart = (id) =>
  CartItemStore.CartItem.some((item) => item.id === id);

const toggleCart = (product) => {
  if (!localStorage.getItem("auth_token")) {
    router.push("/Auth/LogIn");
    return;
  }
  if (isItemInCart(product.id)) {
    CartItemStore.removeFromCart(product.id);
  } else {
    CartItemStore.AddInCartItem({ ...product, quantity: 1 });
    emitter.emit("toggleCart");
    emitter.emit("showMessage", { text: product.title, color: "success" });
  }
};

const isFavourite = (id) => cartStore.FavItem.some((p) => p.id === id);

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

const showDetails = (product) => {
  router.push({ name: "ProductDetail", params: { productId: product.id } });
};

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

/* ================= Section Header ================= */
.section-title-wrapper {
  position: relative;
}

.title-underline {
  width: 60px;
  height: 4px;
  background: rgb(var(--v-theme-primary));
  border-radius: 2px;
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
  padding: 0 16px !important;
  height: 36px !important;
  font-size: 12px !important;
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
  padding: 3px 8px;
  border-radius: 6px;
  font-weight: 800;
  font-size: 11px;
}

.new-tag {
  background: #2ecc71;
  color: white;
  padding: 3px 8px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 900;
  text-align: center;
}

/* ================= Floating Actions ================= */
.floating-actions {
  position: absolute;
  top: 12px;
  left: 12px;
  z-index: 3;
}

.action-btn {
  width: 36px;
  height: 36px;
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
  font-size: 15px;
  font-weight: 800;
  color: #111;
  line-height: 1.4;
  height: 42px;
}

.current-price {
  font-size: 18px;
  font-weight: 900;
  color: rgb(var(--v-theme-primary));
}

.old-price {
  font-size: 13px;
  color: #aaa;
  text-decoration: line-through;
}

/* ================= Professional Solid Buttons ================= */
.add-to-cart-btn {
  border-radius: 8px !important;
  font-weight: 900 !important;
  transition: all 0.2s ease !important;
  text-transform: none !important;
}

.add-to-cart-btn:hover {
  transform: translateY(-2px);
}

.details-icon-btn {
  border: 1px solid rgba(var(--v-theme-primary), 0.1) !important;
}

/* ================= Promo Banner ================= */
.promo-img {
  transition: all 0.5s ease;
  cursor: pointer;
}

.promo-overlay {
  background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, transparent 100%);
  height: 100%;
}

.opacity-80 {
  opacity: 0.8;
}

.w-fit {
  width: fit-content;
}

.gap-3 {
  gap: 12px;
}

/* ================= Slide Group Fixes ================= */
:deep(.v-slide-group__content) {
  padding: 10px 0;
}
</style>
