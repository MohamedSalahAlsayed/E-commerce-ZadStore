<template>
  <v-card class="product-card h-100 d-flex flex-column" elevation="0">
    <!-- Image & Overlays -->
    <div class="card-media-wrapper">
      <v-img
        :src="getImageUrl(product.thumbnail || product.image)"
        :alt="product.title || product.name"
        height="260"
        cover
        class="bg-grey-lighten-4 product-img"
      >
        <template v-slot:placeholder>
          <div class="d-flex align-center justify-center h-100 bg-light">
            <v-progress-circular
              indeterminate
              color="primary"
              width="2"
              size="24"
            />
          </div>
        </template>
      </v-img>

      <!-- Minimalist Overlay on Hover -->
      <div class="media-overlay">
        <v-btn
          class="quick-view-btn"
          @click.stop="QuickVeiw.open(product.id)"
          variant="flat"
        >
          <v-icon size="20" class="ml-2">mdi-eye-outline</v-icon>
          نظرة سريعة
        </v-btn>
      </div>

      <!-- Action Floating Buttons -->
      <div class="floating-actions">
        <button
          class="action-btn fav-btn"
          :class="{ active: isFavourite(product.id) }"
          @click.stop="toggleFavourite(product)"
        >
          <v-icon size="22">
            {{ isFavourite(product.id) ? "mdi-heart" : "mdi-heart-outline" }}
          </v-icon>
        </button>
      </div>

      <!-- Clean Badges -->
      <div class="badge-container">
        <div
          class="discount-badge"
          v-if="product.discountPercentage || product.discount_percentage"
        >
          -{{
            Math.round(
              product.discountPercentage || product.discount_percentage
            )
          }}%
        </div>
        <div class="new-badge" v-if="isNewProduct(product)">جديد</div>
      </div>
    </div>

    <!-- Details Content -->
    <v-card-text class="product-content pa-4 pt-5 d-flex flex-column">
      <div class="category-text mb-2">{{ product.category || "العام" }}</div>

      <h3 class="product-title mb-2" @click="showDetails(product)">
        {{ product.title || product.name }}
      </h3>

      <div class="d-flex align-center mb-4">
        <v-rating
          :model-value="Number(product.rating || product.avg_rating || 0)"
          readonly
          color="amber-darken-2"
          density="compact"
          half-increments
          size="14"
        ></v-rating>
        <span class="rating-count mr-2" style="font-size: 11px; color: #777">
          ({{ Number(product.rating || 0).toFixed(1) }})
        </span>
      </div>

      <!-- Price Section -->
      <div
        class="mt-auto pt-3"
        style="border-top: 1px solid rgba(0, 0, 0, 0.05)"
      >
        <div class="price-wrapper d-flex flex-column">
          <del
            v-if="product.discountPercentage || product.discount_percentage"
            class="old-price mb-1"
            style="font-size: 13px; color: #999"
          >
            {{ Number(product.price).toLocaleString() }} ج.م
          </del>
          <div
            class="current-price d-flex align-end gap-1"
            style="line-height: 1"
          >
            <span style="font-size: 24px; font-weight: 900">
              {{ getDiscountedPrice(product) }}
            </span>
            <small style="font-size: 14px; font-weight: 800; margin-bottom: 2px"
              >ج.م</small
            >
          </div>
        </div>
      </div>
    </v-card-text>

    <!-- Footer Actions -->
    <v-card-actions class="pa-4 pt-0 d-flex gap-3">
      <v-btn
        class="add-to-cart-btn flex-grow-1"
        :color="isItemInCart(product.id) ? 'success' : 'primary'"
        variant="flat"
        height="50"
        :loading="cartLoading"
        @click.stop="toggleCart(product)"
      >
        <v-icon start size="22" class="ml-2">
          {{ isItemInCart(product.id) ? "mdi-cart-check" : "mdi-cart-plus" }}
        </v-icon>
        <span class="text-button font-weight-black">
          {{ isItemInCart(product.id) ? "تمت الإضافة" : "أضف للسلة" }}
        </span>
      </v-btn>

      <v-btn
        icon
        variant="tonal"
        color="primary"
        height="50"
        width="50"
        class="details-icon-btn rounded-lg"
        @click.stop="showDetails(product)"
      >
        <v-icon size="24">mdi-eye-outline</v-icon>
        <v-tooltip activator="parent" location="top" class="custom-tooltip"
          >تفاصيل المنتج</v-tooltip
        >
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script setup>
import { inject, ref, defineProps } from "vue";
import { useRouter } from "vue-router";
import { usePopUpQuickView } from "@/store/PopUp";
import { AddFavProduct } from "@/store/Favourate";
import { AddInCart } from "@/store/Cart";

defineProps({
  product: {
    type: Object,
    required: true,
  },
});

const router = useRouter();
const emitter = inject("emitter");
const QuickVeiw = usePopUpQuickView();
const favStore = AddFavProduct();
const cartStore = AddInCart();

const cartLoading = ref(false);

// --- Helpers ---
const getImageUrl = (img) => {
  if (!img) return "https://via.placeholder.com/600x400?text=No+Image";
  if (img.startsWith("http")) return img;
  const baseUrl = process.env.VUE_APP_API_URL || "http://localhost:8000";
  return `${baseUrl}${img}`;
};

const getDiscountedPrice = (product) => {
  const price = Number(product.price) || 0;
  const discount = Number(
    product.discountPercentage || product.discount_percentage || 0
  );
  const finalPrice = price - (price * discount) / 100;
  return finalPrice.toLocaleString("ar-EG", { maximumFractionDigits: 0 });
};

const isNewProduct = (product) => {
  if (!product.created_at) return false;
  const createdDate = new Date(product.created_at);
  const now = new Date();
  const diffDays = (now - createdDate) / (1000 * 60 * 60 * 24);
  return diffDays < 7; // Consider product new if added in the last 7 days
};

// --- Logic ---
const isItemInCart = (id) => cartStore.CartItem.some((item) => item.id === id);
const isFavourite = (id) => favStore.FavItem.some((p) => p.id === id);

const toggleCart = async (product) => {
  if (!localStorage.getItem("auth_token")) {
    router.push("/Auth/LogIn");
    return;
  }
  cartLoading.value = true;
  if (isItemInCart(product.id)) {
    await cartStore.removeFromCart(product.id);
  } else {
    await cartStore.AddInCartItem({ ...product, quantity: 1 });
    emitter?.emit("toggleCart");
    emitter?.emit("showMessage", {
      text: `تمت إضافة ${product.title || product.name} للسلة`,
      color: "success",
    });
  }
  cartLoading.value = false;
};

const toggleFavourite = async (product) => {
  if (!localStorage.getItem("auth_token")) {
    router.push("/Auth/LogIn");
    return;
  }
  const exists = isFavourite(product.id);
  if (exists) {
    await favStore.removeFromCart(product.id);
    emitter?.emit("showMessage", {
      text: "تمت الإزالة من المفضلة",
      color: "info",
    });
  } else {
    await favStore.AddFavProductItem({ ...product, quantity: 1 });
    emitter?.emit("showMessage", {
      text: "تمت الإضافة للمفضلة",
      color: "success",
    });
  }
};

const showDetails = (product) => {
  router.push(`/User/ProductDetails/${product.id}`);
};
</script>

<style scoped>
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

.new-badge {
  background: linear-gradient(135deg, #2ecc71, #27ae60);
  color: white;
  padding: 6px 12px;
  border-radius: 8px;
  font-weight: 900;
  font-size: 13px;
  box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
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
  cursor: pointer;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

.product-title:hover {
  color: rgb(var(--v-theme-primary));
}

.current-price {
  font-size: 24px;
  font-weight: 900;
  color: #ff4757 !important;
}

.old-price {
  font-size: 14px;
  color: #999;
  text-decoration: line-through;
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

/* Tooltip */
.custom-tooltip {
  font-weight: 700 !important;
}
</style>
