<template>
  <div
    :dir="locale === 'ar' ? 'rtl' : 'ltr'"
    class="py-10 bg-grey-lighten-4"
    style="min-height: 100vh"
  >
    <v-container>
      <div class="d-flex align-center justify-space-between mb-6">
        <h2 class="text-h4 font-weight-black text-primary text-capitalize">
          {{ formattedCategoryName }}
        </h2>
      </div>

      <v-sheet
        color="primary"
        elevation="2"
        rounded="lg"
        class="header-container px-4 py-3 mb-6"
      >
        <div class="d-flex flex-wrap align-center justify-end w-100 gap-3">
          <v-menu location="bottom end">
            <template v-slot:activator="{ props }">
              <v-btn
                v-bind="props"
                variant="text"
                :color="selectedPrice ? '#ffca28' : 'white'"
                class="text-body-2 font-weight-medium"
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
        <v-row v-if="loading" justify="center" class="py-15">
          <v-progress-circular
            indeterminate
            color="primary"
            size="64"
          ></v-progress-circular>
        </v-row>

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
          </v-col>
        </v-row>

        <v-row v-else>
          <v-col
            cols="12"
            sm="6"
            md="6"
            lg="4"
            v-for="product in paginatedProducts"
            :key="product.id"
          >
            <v-card class="product-card h-100 d-flex flex-column" elevation="1">
              <div class="customBtnPopUp">
                <v-img
                  :src="product.thumbnail"
                  height="200"
                  cover
                  class="bg-grey-lighten-4"
                />

                <div class="image-overlay"></div>

                <v-btn
                  class="QuickVeiw"
                  @click="QuickVeiw.open(product.id)"
                  elevation="2"
                >
                  {{ $t("products.quick_view") }}
                </v-btn>

                <button
                  class="favourate-btn"
                  @click.stop="toggleFavourite(product)"
                >
                  <v-icon
                    size="22"
                    :color="
                      isFavourite(product.id)
                        ? 'rgb(var(--v-theme-error))'
                        : 'rgba(var(--v-theme-on-surface), 0.5)'
                    "
                  >
                    {{
                      isFavourite(product.id)
                        ? "mdi-heart"
                        : "mdi-heart-outline"
                    }}
                  </v-icon>
                </button>

                <div class="sale-badge" v-if="product.discountPercentage">
                  {{
                    $t("products.discount", {
                      percentage: Math.round(product.discountPercentage),
                    })
                  }}
                </div>
              </div>

              <v-card-text
                class="flex-grow-1 d-flex flex-column pt-4 pb-2 text-center"
              >
                <h4 class="product-title mb-1" :title="product.title">
                  {{ shortTitle(product.title) }}
                </h4>

                <p class="product-desc text-grey-darken-1 mb-2">
                  {{ shortDescription(product.description) }}
                </p>

                <div class="d-flex align-center justify-center mb-3">
                  <v-rating
                    :model-value="product.rating"
                    readonly
                    color="amber"
                    density="compact"
                    half-increments
                    size="small"
                  ></v-rating>
                  <span class="text-caption text-grey ml-2"
                    >({{ product.rating }})</span
                  >
                </div>

                <div class="mt-auto">
                  <div class="d-flex align-center justify-center mb-1 gap-2">
                    <span class="text-h6 font-weight-black text-primary-dark">
                      {{
                        Math.ceil(
                          product.price -
                            product.price * (product.discountPercentage / 100)
                        )
                      }}
                      {{ $t("products.currency") }}
                    </span>
                    <del
                      v-if="product.discountPercentage"
                      class="text-caption text-grey"
                      :class="locale === 'ar' ? 'ml-2' : 'mr-2'"
                    >
                      {{ product.price }} {{ $t("products.currency") }}
                    </del>
                  </div>
                  <div
                    class="text-caption text-grey-darken-1 font-weight-medium mt-1"
                  >
                    {{ $t("products.stock") }}
                    <span
                      :class="
                        product.stock > 10
                          ? 'text-success font-weight-bold'
                          : 'text-error font-weight-bold'
                      "
                    >
                      {{ product.stock }}
                    </span>
                  </div>
                </div>
              </v-card-text>

              <div class="px-3 pb-4 pt-3 d-flex flex-column gap-2">
                <v-btn
                  color="white"
                  :class="
                    isItemInCart(product.id) ? 'text-success' : 'text-primary'
                  "
                  :style="{
                    border:
                      '1px solid ' +
                      (isItemInCart(product.id)
                        ? 'rgb(var(--v-theme-success))'
                        : 'rgb(var(--v-theme-primary))'),
                  }"
                  variant="flat"
                  block
                  rounded="lg"
                  class="font-weight-bold mb-1 mx-0"
                  @click.stop="toggleCart(product)"
                >
                  <v-icon left :class="locale === 'ar' ? 'mr-2' : 'ml-2'">{{
                    isItemInCart(product.id)
                      ? "mdi-cart-check"
                      : "mdi-cart-plus"
                  }}</v-icon>
                  {{
                    isItemInCart(product.id)
                      ? $t("products.added_to_cart")
                      : $t("products.add_to_cart")
                  }}
                </v-btn>
                <v-btn
                  color="primary"
                  variant="outlined"
                  block
                  rounded="lg"
                  class="font-weight-bold mx-0"
                  @click.stop="
                    $router.push({
                      name: 'ProductDetail',
                      params: { productId: product.id },
                    })
                  "
                >
                  <v-icon left :class="locale === 'ar' ? 'mr-2' : 'ml-2'"
                    >mdi-eye-outline</v-icon
                  >
                  {{ $t("products.details") }}
                </v-btn>
              </div>
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
              elevation="1"
            ></v-pagination>
          </v-col>
        </v-row>
      </div>
    </v-container>

    <PopUp />
  </div>
</template>

<script setup>
import { onMounted, watch, ref, computed, inject } from "vue";
import { useI18n } from "vue-i18n";
const { locale, t } = useI18n();
import { useRoute } from "vue-router";
import api from "@/axios";
import PopUp from "@/components/PopUp.vue";
import { usePopUpQuickView } from "@/store/PopUp";
import { AddFavProduct } from "@/store/Favourate";
import { AddInCart } from "@/store/Cart";
import { ProductModule } from "@/store/Products";

const route = useRoute();
const QuickVeiw = usePopUpQuickView();
const cartStore = AddFavProduct();
const FavItem = cartStore.FavItem;
const CartItemStore = AddInCart();
const productStore = ProductModule();
const emitter = inject("emitter");

const categoryProducts = ref([]);
const loading = ref(false);

const formattedCategoryName = computed(() => {
  const slug = route.params.slug || "";
  const category = (productStore.categories || []).find(
    (c) => String(c.id) === String(slug)
  );
  return category ? category.name : t("products.category_browse");
});

const loadCategoryProducts = async (slug) => {
  if (!slug) return;
  loading.value = true;
  categoryProducts.value = [];
  try {
    const res = await api.get(`/products?category_id=${slug}`);
    categoryProducts.value = res.data?.data || res.data || [];
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};

watch(
  () => route.params.slug,
  async (newSlug) => {
    currentPage.value = 1;
    await loadCategoryProducts(newSlug);
  }
);

onMounted(async () => {
  await loadCategoryProducts(route.params.slug);
});

// --- Helper Functions ---
const shortDescription = (text) => {
  if (!text) return "";
  return text.length > 60 ? text.substring(0, 55) + "..." : text;
};
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
    window.location.hash = "#/Auth/LogIn";
    return;
  }
  if (isItemInCart(product.id)) {
    CartItemStore.removeFromCart(product.id);
  } else {
    const item = { ...product, quantity: 1 };
    CartItemStore.AddInCartItem(item);
    emitter.emit("toggleCart");
    emitter.emit("showMessage", product.title);
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
const itemsPerPage = 12; // Adjusted to be multiple of 3

const filteredProducts = computed(() => {
  let products = categoryProducts.value ? [...categoryProducts.value] : [];

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
    window.location.hash = "#/Auth/LogIn";
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

watch(
  () => FavItem,
  (newVal) => {
    localStorage.setItem("cart-favourate", JSON.stringify(newVal));
  },
  { deep: true }
);
</script>

<style scoped>
/* ================= ألوان المتغيرات ================= */
.text-primary-dark {
  color: rgb(14, 22, 103) !important;
}

/* ================= تنسيق الكروت ================= */
.product-card {
  width: 100%;
  border-radius: 14px;
  overflow: hidden;
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  border: 1px solid rgba(var(--v-theme-on-surface), 0.12);
  background-color: rgb(var(--v-theme-surface));
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
  border-color: rgba(var(--v-theme-primary), 0.15);
}

/* حاوية الصورة وتأثير الزووم */
.customBtnPopUp {
  position: relative;
  overflow: hidden;
  border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.05);
  background-color: rgb(var(--v-theme-surface));
}

.customBtnPopUp :deep(.v-img__img) {
  transition: transform 0.8s cubic-bezier(0.25, 0.8, 0.25, 1), opacity 0.3s ease;
}

.product-card:hover .customBtnPopUp :deep(.v-img__img) {
  transform: scale(1.12);
  opacity: 0.9;
}

/* طبقة الأوفرلاي */
.image-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    to top,
    rgba(var(--v-theme-primary), 0.5) 0%,
    rgba(var(--v-theme-on-surface), 0.1) 100%
  );
  opacity: 0;
  z-index: 2;
  transition: opacity 0.4s ease-in-out;
  pointer-events: none;
}

.product-card:hover .image-overlay {
  opacity: 1;
}

/* ================= أيقونة المفضلة ================= */
.favourate-btn {
  position: absolute;
  top: 12px;
  left: 12px;
  width: 38px;
  height: 38px;
  background-color: rgb(var(--v-theme-surface));
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 5;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border: none;
  cursor: pointer;
  transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275),
    background-color 0.2s ease;
}

.favourate-btn:hover {
  transform: scale(1.15);
  background-color: rgba(var(--v-theme-error), 0.1);
}

/* ================= التاج والخصم ================= */
.sale-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  background: rgb(var(--v-theme-error));
  color: white;
  padding: 5px 14px;
  font-size: 13px;
  font-weight: 800;
  border-radius: 25px;
  z-index: 4;
  box-shadow: 0 4px 10px rgba(var(--v-theme-error), 0.3);
  letter-spacing: 0.5px;
}

/* ================= زرار Quick View ================= */
.QuickVeiw {
  background-color: rgba(var(--v-theme-surface), 0.95) !important;
  height: 38px !important;
  padding: 0 24px !important;
  font-size: 13px !important;
  font-weight: 700 !important;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%) translateY(20px);
  opacity: 0;
  color: rgb(var(--v-theme-primary)) !important;
  border-radius: 20px !important;
  transition: all 0.3s ease-in-out;
  z-index: 5;
  text-transform: none;
}

.product-card:hover .QuickVeiw {
  opacity: 1;
  transform: translate(-50%, -50%) translateY(0);
}

/* ================= النصوص ================= */
.product-title {
  color: rgb(var(--v-theme-on-surface));
  line-height: 1.4;
  font-size: 15px;
  font-weight: 800;
}

.product-desc {
  font-size: 13px;
  line-height: 1.5;
  min-height: 40px;
}

/* ================= الموبايل ================= */
@media (max-width: 960px) {
  .QuickVeiw {
    opacity: 1 !important;
    transform: translate(-50%, -50%) translateY(0);
    top: auto;
    bottom: 10px;
    height: 32px !important;
    font-size: 12px !important;
    padding: 0 16px !important;
  }
}
</style>
