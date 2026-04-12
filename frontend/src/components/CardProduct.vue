<template>
  <div class="favourateProduct pt-5" :dir="locale === 'ar' ? 'rtl' : 'ltr'">
    <v-container>
      <v-row>
        <v-col cols="12">
          <h3
            class="text-h5 font-weight-black mb-5 text-grey-darken-4 pro-title"
          >
            {{ isCheckout ? "إتمام الطلب" : `المفضلة (${FavItem.length})` }}
          </h3>
        </v-col>
      </v-row>

      <v-row
        v-if="FavItem.length === 0"
        justify="center"
        class="text-center py-10"
      >
        <v-col cols="12" md="6">
          <v-icon size="80" color="grey-lighten-2">mdi-heart-outline</v-icon>
          <h3 class="text-h5 text-grey-darken-1 mt-4">المفضلة فارغة</h3>
          <p class="text-body-1 text-grey mb-6">
            يبدو أنك لم تقم بإضافة أي منتجات إلى المفضلة بعد.
          </p>
          <v-btn
            color="primary"
            to="/"
            height="50"
            class="px-10 rounded-lg font-weight-black"
            flat
          >
            متابعة التسوق
          </v-btn>
        </v-col>
      </v-row>

      <v-row v-if="FavItem">
        <v-col
          cols="12"
          sm="6"
          md="4"
          lg="3"
          v-for="product in FavItem"
          :key="product.id"
        >
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
                  class="action-btn active"
                  @click.stop="toggleFavourite(product)"
                >
                  <v-icon size="20">mdi-heart</v-icon>
                </button>
              </div>

              <div class="badge-container">
                <div class="discount-badge" v-if="product.discountPercentage">
                  -{{ Math.round(product.discountPercentage) }}%
                </div>
              </div>
            </div>

            <v-card-text class="flex-grow-1 d-flex flex-column pa-4">
              <div class="category-text mb-1">المنتج</div>
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
                <div class="stock-info mt-1">
                  المخزون:
                  <span
                    :class="product.stock > 0 ? 'text-success' : 'text-error'"
                    class="font-weight-bold"
                  >
                    {{ product.stock }}
                  </span>
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
                  {{ isItemInCart(product.id) ? "تمت الإضافة" : "أضف للسلة" }}
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
        </v-col>
      </v-row>
    </v-container>

    <PopUp />
  </div>
</template>

<script setup>
import { inject, computed } from "vue";
import { AddFavProduct } from "../store/Favourate";
import { AddInCart } from "../store/Cart";
import PopUp from "../components/PopUp.vue";
import { usePopUpQuickView } from "@/store/PopUp";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";

const { locale } = useI18n();
const QuickVeiw = usePopUpQuickView();
const cartStore = AddFavProduct();
const FavItem = computed(() => cartStore.FavItem);
const router = useRouter();

const CartItemStore = AddInCart();
const emitter = inject("emitter");

const shortTitle = (text) => {
  if (!text) return "";
  return text.length > 25 ? text.substring(0, 25) + "..." : text;
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

const toggleFavourite = (product) => {
  if (!localStorage.getItem("auth_token")) {
    router.push("/Auth/LogIn");
    return;
  }
  cartStore.removeFromCart(product.id);
};

const showDetails = (product) => {
  router.push({ name: "ProductDetail", params: { productId: product.id } });
};
</script>

<style scoped>
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
  padding: 0 20px !important;
  height: 40px !important;
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
  color: #ff4757;
}

.action-btn:hover {
  transform: scale(1.1);
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
  color: rgb(var(--v-theme-primary));
}

.old-price {
  font-size: 14px;
  color: #aaa;
  text-decoration: line-through;
}

.stock-info {
  font-size: 12px;
  color: #777;
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
