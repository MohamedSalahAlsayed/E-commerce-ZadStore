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
      :slides-per-view="1"
      :space-between="20"
      :navigation="true"
      :autoplay="{ delay: 4500, disableOnInteraction: false }"
      :breakpoints="{
        600: { slidesPerView: 2, spaceBetween: 20 },
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
          <v-card class="product-card h-100 d-flex flex-column" elevation="0">
            <div class="card-media-wrapper">
              <v-img
                :src="product.thumbnail"
                height="220"
                cover
                class="bg-grey-lighten-4 product-img"
              />

              <!-- Overlay -->
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

              <!-- Floating Actions -->
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

              <!-- Badges -->
              <div class="badge-container" v-if="product.discountPercentage">
                <div class="discount-badge">
                  -{{ Math.round(product.discountPercentage) }}%
                </div>
              </div>
            </div>

            <v-card-text class="flex-grow-1 d-flex flex-column pa-4 text-right">
              <div class="category-text mb-1">
                {{ product.brand || "ماركة عالمية" }}
              </div>
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
                  >({{ product.rating || 0 }})</span
                >
              </div>

              <div class="mt-auto">
                <div class="price-wrapper d-flex align-end gap-2">
                  <span class="current-price">
                    {{
                      Math.ceil(
                        product.price -
                          product.price *
                            ((product.discountPercentage || 0) / 100)
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
                  {{ isItemInCart(product.id) ? "تمت الإضافة" : "أضف للسلة" }}
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
  </v-container>

  <PopUp />
</template>

<script setup>
import { onMounted, watch, inject } from "vue";
import { ProductModule } from "@/store/Products";
import { AddFavProduct } from "../store/Favourate";
import { AddInCart } from "../store/Cart";
import PopUp from "../components/PopUp.vue";
import { usePopUpQuickView } from "@/store/PopUp";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import { Navigation, Autoplay } from "swiper/modules";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";

const { locale } = useI18n();
const router = useRouter();
const brandStore = ProductModule();
const modules = [Navigation, Autoplay];
const QuickVeiw = usePopUpQuickView();
const cartStore = AddFavProduct();
const FavItem = cartStore.FavItem;

const CartItemStore = AddInCart();
const emitter = inject("emitter");

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

const showDetails = (product) => {
  router.push({ name: "ProductDetail", params: { productId: product.id } });
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

watch(
  () => FavItem,
  (newVal) => {
    localStorage.setItem("cart-favourate", JSON.stringify(newVal));
  },
  { deep: true }
);

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

/* ================= Product Card ================= */
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

/* ================= Badge ================= */
.badge-container {
  position: absolute;
  top: 12px;
  right: 12px;
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

/* ================= Content Section ================= */
.category-text {
  font-size: 11px;
  color: #999;
  font-weight: 700;
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

/* ================= Buttons ================= */
.add-to-cart-btn {
  border-radius: 8px !important;
  font-weight: 900 !important;
  transition: all 0.2s ease !important;
  text-transform: none !important;
}

.details-icon-btn {
  border: 1px solid rgba(var(--v-theme-primary), 0.1) !important;
}

.gap-3 {
  gap: 12px;
}
</style>
