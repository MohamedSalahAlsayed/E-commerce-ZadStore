<template>
  <v-card class="product-card h-100 d-flex flex-column" elevation="0">
    <!-- Image & Overlays -->
    <div class="card-media-wrapper">
      <v-img
        :src="getImageUrl(product.thumbnail || product.image)"
        :alt="product.title || product.name"
        height="240"
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
          <v-icon size="18" class="ml-1">mdi-eye</v-icon>
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
          <v-icon size="20">
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
    <v-card-text class="product-content pa-4 d-flex flex-column">
      <div class="category-text mb-1">{{ product.category || "العام" }}</div>

      <h3 class="product-title mb-2" @click="showDetails(product)">
        {{ product.title || product.name }}
      </h3>

      <div class="d-flex align-center mb-3">
        <v-rating
          :model-value="Number(product.rating || product.avg_rating || 0)"
          readonly
          color="amber-darken-1"
          active-color="amber-darken-2"
          density="compact"
          half-increments
          size="16"
        ></v-rating>
        <span class="rating-count mr-2">
          ({{ Number(product.rating || 0).toFixed(1) }})
        </span>
      </div>

      <!-- Price Section -->
      <div class="mt-auto pt-2">
        <div class="price-wrapper d-flex align-end gap-2">
          <span class="current-price">
            {{ getDiscountedPrice(product) }} <small>ج.م</small>
          </span>
          <span
            v-if="product.discountPercentage || product.discount_percentage"
            class="old-price"
          >
            {{ Number(product.price).toLocaleString() }} ج.م
          </span>
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
  transition: all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  border: 1px solid rgba(0, 0, 0, 0.05) !important;
  overflow: hidden;
}

.product-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08) !important;
  border-color: rgba(var(--v-theme-primary), 0.2) !important;
}

/* ================= Media Section ================= */
.card-media-wrapper {
  position: relative;
  overflow: hidden;
}

.product-img {
  transition: transform 1s cubic-bezier(0, 0, 0.2, 1);
}

.product-card:hover .product-img {
  transform: scale(1.08);
}

.media-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.15);
  backdrop-filter: blur(2px);
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
  color: #111 !important;
  border-radius: 50px !important;
  font-weight: 800 !important;
  padding: 0 24px !important;
  height: 44px !important;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
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
  padding: 4px 12px;
  border-radius: 8px;
  font-weight: 900;
  font-size: 11px;
  letter-spacing: 0.5px;
}

.new-badge {
  background: #2ecc71;
  color: white;
  padding: 4px 12px;
  border-radius: 8px;
  font-weight: 900;
  font-size: 11px;
  letter-spacing: 0.5px;
}

/* ================= Floating Actions ================= */
.floating-actions {
  position: absolute;
  top: 12px;
  left: 12px;
  z-index: 3;
}

.action-btn {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  background: white;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
  transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  color: #444;
}

.action-btn:hover {
  transform: scale(1.15) rotate(5deg);
  color: #ff4757;
  box-shadow: 0 12px 20px rgba(255, 71, 87, 0.15);
}

.action-btn.fav-btn.active {
  color: #ff4757;
}

/* ================= Content Section ================= */
.category-text {
  font-size: 10px;
  color: #999;
  font-weight: 800;
  letter-spacing: 1px;
  text-transform: uppercase;
}

.product-title {
  font-size: 16px;
  font-weight: 800;
  color: #1a1a1a;
  line-height: 1.4;
  height: 44px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  cursor: pointer;
  transition: color 0.2s ease;
}

.product-title:hover {
  color: rgb(var(--v-theme-primary));
}

.current-price {
  font-size: 22px;
  font-weight: 900;
  color: rgb(var(--v-theme-primary));
  letter-spacing: -0.5px;
}

.old-price {
  font-size: 14px;
  color: #bbb;
  text-decoration: line-through;
  margin-bottom: 2px;
}

/* ================= Professional Solid Buttons ================= */
.add-to-cart-btn {
  border-radius: 10px !important;
  font-weight: 900 !important;
  transition: all 0.2s ease !important;
  text-transform: none !important;
}

.add-to-cart-btn:hover {
  transform: translateY(-3px) scale(1.02);
  box-shadow: 0 10px 20px rgba(var(--v-theme-primary), 0.3) !important;
}

.details-icon-btn {
  border: 1px solid rgba(var(--v-theme-primary), 0.1) !important;
}

/* Tooltip */
.custom-tooltip {
  font-weight: 700 !important;
}
</style>
