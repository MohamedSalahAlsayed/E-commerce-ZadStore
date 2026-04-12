<template>
  <div :dir="locale === 'ar' ? 'rtl' : 'ltr'">
    <v-sheet
      color="primary"
      elevation="0"
      rounded="lg"
      class="header-container px-4 py-3 mb-6 mx-2 mx-md-0 border"
    >
      <div class="d-flex flex-wrap align-center justify-end w-100 gap-3">
        <v-menu location="bottom end">
          <template v-slot:activator="{ props }">
            <v-btn
              v-bind="props"
              variant="text"
              :color="
                selectedPrice ? '#white' : 'rgb(var(--v-theme-on-primary))'
              "
              class="text-button font-weight-black"
              append-icon="mdi-chevron-down"
            >
              {{
                selectedPrice
                  ? selectedPrice.title
                  : $t("products.sort_by_price")
              }}
            </v-btn>
          </template>
          <v-list density="compact" rounded="lg">
            <v-list-item
              v-if="selectedPrice"
              @click="selectedPrice = null"
              :title="$t('products.cancel_sort')"
              color="error"
              class="text-error"
            >
              <template v-slot:prepend>
                <v-icon icon="mdi-close" color="error"></v-icon>
              </template>
            </v-list-item>
            <v-list-item
              v-for="item in priceOptions"
              :key="item.value"
              :title="item.title"
              :value="item.value"
              @click="selectedPrice = item"
              :active="selectedPrice?.value === item.value"
              color="primary"
            ></v-list-item>
          </v-list>
        </v-menu>
      </div>
    </v-sheet>

    <div class="Product-section">
      <div v-if="loading" class="pa-0">
        <v-row>
          <v-col
            cols="12"
            sm="6"
            md="6"
            lg="4"
            v-for="n in 6"
            :key="'skeleton-' + n"
          >
            <PerformanceSkeleton />
          </v-col>
        </v-row>
      </div>

      <v-row
        v-else-if="paginatedProducts.length === 0"
        justify="center"
        class="py-15"
      >
        <v-col cols="12" class="text-center">
          <v-icon size="80" color="grey-lighten-2" class="mb-4"
            >mdi-package-variant-closed</v-icon
          >
          <h3 class="text-h4 font-weight-bold text-grey-darken-1">
            {{ $t("products.no_products_found") }}
          </h3>
          <p class="text-grey mt-2">
            {{ $t("products.try_reset_filter") }}
          </p>
        </v-col>
      </v-row>

      <v-container v-else class="pa-0">
        <v-row>
          <v-col
            cols="12"
            sm="6"
            md="6"
            lg="4"
            v-for="product in paginatedProducts"
            :key="product.id"
          >
            <v-card class="product-card h-100 d-flex flex-column" elevation="0">
              <div class="card-media-wrapper">
                <v-img
                  :src="product.thumbnail"
                  height="220"
                  cover
                  loading="lazy"
                  class="bg-grey-lighten-4 product-img"
                />

                <div class="media-overlay">
                  <v-btn
                    class="quick-view-btn"
                    @click="QuickVeiw.open(product.id)"
                    variant="flat"
                  >
                    <v-icon size="18" class="ml-1">mdi-eye</v-icon>
                    {{ $t("products.quick_view") }}
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

              <v-card-text class="flex-grow-1 d-flex flex-column pa-4">
                <div class="category-text mb-1">
                  {{ product.category || "المنتج" }}
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
                            product.price * (product.discountPercentage / 100)
                        ).toLocaleString()
                      }}
                      <small>ج.م</small>
                    </span>
                    <del v-if="product.discountPercentage" class="old-price">
                      {{ Number(product.price).toLocaleString() }} ج.م
                    </del>
                  </div>
                  <div class="stock-info mt-1">
                    {{ $t("products.stock") }}:
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
          </v-col>
        </v-row>

        <v-row justify="center" class="mt-8 mb-4" v-if="totalPages > 1">
          <v-col cols="auto">
            <v-pagination
              v-model="currentPage"
              :length="totalPages"
              :total-visible="7"
              active-color="primary"
              color="grey-lighten-2"
              rounded="circle"
              elevation="0"
            ></v-pagination>
          </v-col>
        </v-row>
      </v-container>
    </div>

    <PopUp />
  </div>
</template>

<script setup>
import { ProductModule } from "@/store/Products.js";
import PopUp from "./PopUp.vue";
import PerformanceSkeleton from "./Skeletons/PerformanceSkeleton.vue";
import { useI18n } from "vue-i18n";
import { usePopUpQuickView } from "@/store/PopUp";
import { onMounted, watch, ref, computed, inject } from "vue";
import { AddFavProduct } from "../store/Favourate";
import { AddInCart } from "../store/Cart";
import { useRouter } from "vue-router";

const { locale, t } = useI18n();
const router = useRouter();

// --- Setup Stores ---
const QuickVeiw = usePopUpQuickView();
const productStore = ProductModule();
const cartStore = AddFavProduct();
const FavItem = cartStore.FavItem;
const CartItemStore = AddInCart();
const emitter = inject("emitter");

const loading = ref(false);

// --- Helper Functions ---
const shortTitle = (text) => {
  if (!text) return "";
  return text.length > 25 ? text.substring(0, 25) + "..." : text;
};

// --- Action Logic ---
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

// --- Filter Logic States ---
const selectedPrice = ref(null);
const priceOptions = computed(() => [
  { title: t("products.price_low_high"), value: "asc" },
  { title: t("products.price_high_low"), value: "desc" },
]);

// --- Pagination Logic ---
const currentPage = ref(1);
const itemsPerPage = 12;

const filteredProducts = computed(() => {
  let products = productStore.products ? [...productStore.products] : [];

  if (selectedPrice.value) {
    const isAsc = selectedPrice.value.value === "asc";
    products.sort((a, b) => {
      const priceA = a.price * (1 - a.discountPercentage / 100);
      const priceB = b.price * (1 - b.discountPercentage / 100);
      return isAsc ? priceA - priceB : priceB - priceA;
    });
  }

  return products;
});

watch([selectedPrice], () => {
  currentPage.value = 1;
});

const totalPages = computed(() => {
  return Math.ceil(filteredProducts.value.length / itemsPerPage);
});

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredProducts.value.slice(start, end);
});

// --- Favourites Logic ---
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
  if (!productStore.products || productStore.products.length === 0) {
    loading.value = true;
    await productStore.getProducts();
    loading.value = false;
  }
});
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
