<template>
  <div class="product-details-page" :dir="locale === 'ar' ? 'rtl' : 'ltr'">
    <!-- Breadcrumb -->
    <v-container v-if="productDetails.productDetails?.id">
      <v-breadcrumbs :items="breadcrumbs" class="px-0 pro-breadcrumbs">
        <template v-slot:divider>
          <v-icon size="14">{{
            locale === "ar" ? "mdi-chevron-left" : "mdi-chevron-right"
          }}</v-icon>
        </template>
      </v-breadcrumbs>
    </v-container>

    <v-container>
      <!-- Main Product Section -->
      <v-row v-if="productDetails.productDetails?.id" class="mb-12">
        <!-- Image Gallery -->
        <v-col cols="12" md="6" lg="5">
          <div class="image-gallery-container sticky-gallery">
            <div
              class="main-image-wrapper elevation-1 rounded-xl overflow-hidden mb-6 bg-white border"
            >
              <!-- Badges -->
              <div class="gallery-badges">
                <v-chip
                  v-if="productDetails.productDetails.discountPercentage > 0"
                  color="error"
                  variant="flat"
                  size="small"
                  class="font-weight-black rounded-lg px-3"
                >
                  -{{
                    Math.round(
                      productDetails.productDetails.discountPercentage
                    )
                  }}%
                </v-chip>
              </div>

              <!-- Shared Actions -->
              <v-btn
                icon
                variant="flat"
                color="white"
                size="small"
                class="fav-btn shadow-md"
                @click="toggleFav"
              >
                <v-icon :color="isFav ? 'red' : 'grey-darken-1'" size="22">
                  {{ isFav ? "mdi-heart" : "mdi-heart-outline" }}
                </v-icon>
              </v-btn>

              <v-img
                :src="selectedImage || productDetails.productDetails.thumbnail"
                :alt="productDetails.productDetails.title"
                aspect-ratio="1"
                cover
                class="main-image"
              >
                <template v-slot:placeholder>
                  <div
                    class="d-flex align-center justify-center fill-height bg-grey-lighten-4"
                  >
                    <v-progress-circular
                      indeterminate
                      color="primary"
                    ></v-progress-circular>
                  </div>
                </template>
              </v-img>
            </div>

            <!-- Thumbnails -->
            <div
              v-if="allImages.length > 1"
              class="thumbnails-row d-flex gap-3 pb-2"
            >
              <v-card
                v-for="(img, i) in allImages"
                :key="i"
                class="thumb-item overflow-hidden"
                :class="{
                  'thumb-active':
                    selectedImage === img ||
                    (!selectedImage &&
                      img === productDetails.productDetails.thumbnail),
                }"
                @click="selectedImage = img"
                elevation="0"
                width="80"
                height="80"
              >
                <v-img :src="img" cover aspect-ratio="1"></v-img>
              </v-card>
            </div>
          </div>
        </v-col>

        <!-- Product Info -->
        <v-col cols="12" md="6" lg="7" class="ps-md-10">
          <div class="product-info-panel h-100 d-flex flex-column">
            <!-- Brand & Tags -->
            <div class="d-flex align-center mb-4">
              <span
                class="brand-text text-uppercase font-weight-black text-primary mr-3"
              >
                {{
                  typeof productDetails.productDetails.brand === "object"
                    ? productDetails.productDetails.brand.name
                    : productDetails.productDetails.brand || "ZADSTORE"
                }}
              </span>
              <v-divider vertical class="mx-3 d-none d-sm-block"></v-divider>
              <div
                class="category-path text-caption text-grey-darken-1 font-weight-medium"
              >
                {{
                  typeof productDetails.productDetails.category === "object"
                    ? productDetails.productDetails.category.name
                    : productDetails.productDetails.category
                }}
              </div>
            </div>

            <!-- Title -->
            <h1 class="product-detail-title mb-4">
              {{ productDetails.productDetails.title }}
            </h1>

            <!-- Rating Summary -->
            <div
              class="d-flex align-center mb-6 py-2 px-3 bg-amber-lighten-5 rounded-lg w-fit"
            >
              <v-rating
                :model-value="productDetails.productDetails.rating"
                readonly
                color="amber-darken-2"
                density="compact"
                size="small"
                half-increments
              ></v-rating>
              <span class="text-subtitle-2 font-weight-black mx-2">{{
                productDetails.productDetails.rating || 0
              }}</span>
              <v-divider vertical class="mx-2"></v-divider>
              <span class="text-caption text-grey-darken-2 px-2">
                {{ productDetails.productDetails.reviews?.length || 0 }} مراجعة
              </span>
            </div>

            <!-- Price Display -->
            <div
              class="price-box pa-6 rounded-xl bg-grey-lighten-5 border mb-8"
            >
              <div class="d-flex align-end gap-3 flex-wrap mb-2">
                <span class="final-display-price">
                  {{ (discountedPrice * quantity).toLocaleString() }}
                  <small>ج.م</small>
                </span>
                <del
                  v-if="productDetails.productDetails.discountPercentage > 0"
                  class="original-display-price"
                >
                  {{
                    Number(
                      productDetails.productDetails.price * quantity
                    ).toLocaleString()
                  }}
                  ج.م
                </del>
              </div>
              <div
                class="text-caption text-success font-weight-black"
                v-if="productDetails.productDetails.discountPercentage > 0"
              >
                وفرت
                {{
                  (
                    (productDetails.productDetails.price - discountedPrice) *
                    quantity
                  ).toLocaleString()
                }}
                ج.م من السعر الأصلي
              </div>
            </div>

            <!-- Description -->
            <div class="mb-8">
              <h3 class="pro-section-label mb-3">تفاصيل المنتج</h3>
              <p class="product-description-text">
                {{ productDetails.productDetails.description }}
              </p>
            </div>

            <div class="mt-auto">
              <!-- Stock Info -->
              <div class="stock-panel mb-6 d-flex align-center gap-2">
                <div
                  :class="
                    productDetails.productDetails.stock > 0
                      ? 'dot-success'
                      : 'dot-error'
                  "
                ></div>
                <span
                  class="font-weight-black text-body-2"
                  :class="
                    productDetails.productDetails.stock > 0
                      ? 'text-success'
                      : 'text-error'
                  "
                >
                  {{
                    productDetails.productDetails.stock > 0
                      ? "متوفر بالمخزون"
                      : "نفذت الكمية"
                  }}
                </span>
                <span
                  v-if="productDetails.productDetails.stock > 0"
                  class="text-caption text-grey"
                >
                  ({{ productDetails.productDetails.stock }} قطعة متبقية)
                </span>
              </div>

              <!-- Action Bar -->
              <div class="action-bar-pro d-flex align-center gap-4 flex-wrap">
                <div
                  class="qty-control d-flex align-center bg-white border rounded-xl px-2 shadow-sm"
                >
                  <v-btn
                    icon="mdi-minus"
                    variant="text"
                    @click="decrease"
                    :disabled="quantity <= 1"
                    size="small"
                  ></v-btn>
                  <span class="mx-5 font-weight-black text-h6">{{
                    quantity
                  }}</span>
                  <v-btn
                    icon="mdi-plus"
                    variant="text"
                    @click="increase"
                    :disabled="quantity >= productDetails.productDetails.stock"
                    size="small"
                  ></v-btn>
                </div>

                <v-btn
                  :color="
                    isItemInCart(productDetails.productDetails?.id)
                      ? 'success'
                      : 'primary'
                  "
                  variant="flat"
                  height="64"
                  class="flex-grow-1 add-to-cart-big rounded-xl font-weight-black"
                  elevation="1"
                  :loading="loading"
                  @click="toggleCart(productDetails.productDetails)"
                >
                  <v-icon size="26" class="ml-2">
                    {{
                      isItemInCart(productDetails.productDetails?.id)
                        ? "mdi-cart-check"
                        : "mdi-cart-plus"
                    }}
                  </v-icon>
                  {{
                    isItemInCart(productDetails.productDetails?.id)
                      ? "تمت الإضافة للسلة"
                      : "أضف للسلة الآن"
                  }}
                </v-btn>
              </div>

              <!-- Features Summary -->
              <div class="pro-features-grid mt-10">
                <div class="pro-feature-card">
                  <v-icon color="primary" size="24" class="mb-2"
                    >mdi-truck-fast</v-icon
                  >
                  <span>توصيل سريع</span>
                </div>
                <div class="pro-feature-card">
                  <v-icon color="primary" size="24" class="mb-2"
                    >mdi-shield-check</v-icon
                  >
                  <span>أصلي 100%</span>
                </div>
                <div class="pro-feature-card">
                  <v-icon color="primary" size="24" class="mb-2"
                    >mdi-refresh</v-icon
                  >
                  <span>إرجاع سهل</span>
                </div>
                <div class="pro-feature-card">
                  <v-icon color="primary" size="24" class="mb-2"
                    >mdi-headset</v-icon
                  >
                  <span>دعم فني</span>
                </div>
              </div>
            </div>
          </div>
        </v-col>
      </v-row>

      <!-- Bottom Tabs/Sections -->
      <template v-if="productDetails.productDetails?.id">
        <v-divider class="mb-10"></v-divider>

        <v-row>
          <v-col cols="12" lg="8">
            <h2 class="text-h5 font-weight-black mb-8 d-flex align-center">
              <v-icon color="primary" class="ml-3">mdi-message-text</v-icon>
              آراء ومراجعات العملاء
            </h2>

            <div class="reviews-list-v2">
              <div v-if="productDetails.productDetails.reviews?.length > 0">
                <v-card
                  v-for="review in productDetails.productDetails.reviews"
                  :key="review.id"
                  class="review-node mb-6 pa-6 border-0 bg-white shadow-sm"
                  variant="flat"
                  rounded="xl"
                >
                  <div class="d-flex gap-4">
                    <v-avatar color="primary" size="48">
                      <v-img
                        v-if="review.user?.avatar"
                        :src="review.user.avatar"
                      ></v-img>
                      <span v-else class="text-white font-weight-black">{{
                        review.user?.[0]?.toUpperCase() || "C"
                      }}</span>
                    </v-avatar>
                    <div class="flex-grow-1">
                      <div
                        class="d-flex justify-space-between align-center mb-1"
                      >
                        <span class="font-weight-black">{{
                          review.user?.name || "عميل زاد"
                        }}</span>
                        <span class="text-caption text-grey">{{
                          new Date(review.created_at).toLocaleDateString(
                            "ar-EG"
                          )
                        }}</span>
                      </div>
                      <v-rating
                        :model-value="review.rating"
                        readonly
                        color="amber-darken-1"
                        density="compact"
                        size="14"
                        class="mb-3"
                      ></v-rating>
                      <p class="review-comment text-grey-darken-2">
                        {{ review.comment }}
                      </p>
                    </div>
                  </div>
                </v-card>
              </div>
              <v-sheet
                v-else
                class="empty-reviews text-center pa-10 bg-grey-lighten-5 rounded-xl border-dashed"
              >
                <v-icon size="64" color="grey-lighten-1" class="mb-4"
                  >mdi-comment-off-outline</v-icon
                >
                <p class="text-h6 text-grey-darken-1 mb-0">
                  لا توجد مراجعات لهذا المنتج بعد.
                </p>
                <p class="text-body-2 text-grey">
                  كن أول من يشارك تجربته مع الآخرين!
                </p>
              </v-sheet>
            </div>
          </v-col>

          <v-col cols="12" lg="4">
            <v-card
              class="review-submission pa-8 rounded-xl bg-primary-lighten-5 border-0 shadow-sm"
              elevation="0"
            >
              <h3 class="text-h5 font-weight-black text-primary mb-2">
                اترك مراجعتك
              </h3>
              <p class="text-body-2 text-grey-darken-1 mb-6">
                رأيك يهمنا ويساعد الآخرين في اتخاذ القرار
              </p>

              <div v-if="isLoggedIn">
                <v-form @submit.prevent="submitReview" v-model="isReviewValid">
                  <div class="text-center mb-6 pa-4 bg-white rounded-xl border">
                    <div class="text-caption font-weight-black mb-1">
                      قيم المنتج
                    </div>
                    <v-rating
                      v-model="reviewData.rating"
                      color="amber-darken-2"
                      hover
                      size="large"
                    ></v-rating>
                  </div>

                  <v-textarea
                    v-model="reviewData.comment"
                    placeholder="اكتب رأيك هنا بكل صراحة..."
                    variant="flat"
                    bg-color="white"
                    rows="4"
                    rounded="xl"
                    class="mb-6 pro-textarea"
                    hide-details="auto"
                  ></v-textarea>

                  <v-btn
                    color="primary"
                    block
                    height="56"
                    class="rounded-xl font-weight-black"
                    type="submit"
                    :loading="reviewLoading"
                    :disabled="!isReviewValid || reviewData.rating === 0"
                  >
                    نشر المراجعة
                  </v-btn>
                </v-form>
              </div>
              <div v-else class="text-center py-6">
                <v-icon size="48" color="grey-lighten-1" class="mb-4"
                  >mdi-lock</v-icon
                >
                <p class="font-weight-black mb-4 text-grey-darken-2">
                  سجل دخولك لتتمكن من إضافة مراجعة
                </p>
                <v-btn
                  color="primary"
                  variant="flat"
                  block
                  height="50"
                  class="rounded-xl font-weight-black"
                  to="/Auth/LogIn"
                >
                  تسجيل الدخول
                </v-btn>
              </div>
            </v-card>
          </v-col>
        </v-row>
      </template>

      <!-- Skeleton -->
      <div v-else class="pa-10">
        <v-skeleton-loader type="image, article, button" />
      </div>
    </v-container>
    <PopUp />
  </div>
</template>

<script setup>
import { ProductModule } from "@/store/Products";
import { onMounted, ref, watch, inject, computed } from "vue";
import { useI18n } from "vue-i18n";
const { locale } = useI18n();
import { useRoute } from "vue-router";
import { AddInCart } from "../../store/Cart";
const CartItem = AddInCart();
const emitter = inject("emitter");
const loading = ref(false);

const route = useRoute();
const productDetails = ProductModule();
const selectedImage = ref(null);
const quantity = ref(1);
const isFav = ref(false);

import api from "@/axios";
const isLoggedIn = ref(!!localStorage.getItem("auth_token"));
const isReviewValid = ref(false);
const reviewLoading = ref(false);
const reviewData = ref({
  rating: 0,
  comment: "",
});

// Computed
const discountedPrice = computed(() => {
  const p = productDetails.productDetails;
  if (!p?.price) return 0;
  return Math.ceil(p.price - p.price * ((p.discountPercentage || 0) / 100));
});

const allImages = computed(() => {
  const p = productDetails.productDetails;
  if (!p) return [];
  const imgs = [...(p.images || [])];
  if (p.thumbnail && !imgs.includes(p.thumbnail)) {
    imgs.unshift(p.thumbnail);
  }
  return imgs;
});

const breadcrumbs = computed(() => {
  const p = productDetails.productDetails;
  const items = [
    { title: "الرئيسية", to: "/home" },
    { title: "المنتجات", to: "/Products" },
  ];
  if (p?.title) {
    items.push({ title: p.title, disabled: true });
  }
  return items;
});

const toggleFav = async () => {
  if (!localStorage.getItem("auth_token")) {
    window.location.hash = "#/Auth/LogIn";
    return;
  }
  try {
    const res = await api.post("/user/favorites", {
      product_id: productDetails.productDetails.id,
    });
    isFav.value = res.data.added;
    emitter.emit("showMessage", { text: res.data.message, color: "success" });
  } catch (err) {
    console.error(err);
  }
};

const checkFavStatus = async () => {
  if (!localStorage.getItem("auth_token") || !productDetails.productDetails?.id)
    return;
  try {
    const res = await api.get("/user/favorites");
    isFav.value = res.data.some(
      (f) => f.product_id === productDetails.productDetails.id
    );
  } catch (err) {
    console.error(err);
  }
};

const submitReview = async () => {
  if (reviewData.value.rating === 0) return;
  reviewLoading.value = true;
  try {
    const res = await api.post(
      `/products/${route.params.productId}/reviews`,
      reviewData.value
    );
    emitter.emit("showMessage", { text: res.data.message, color: "success" });
    reviewData.value = { rating: 0, comment: "" };
    await loadProductData(); // Refresh reviews
  } catch (error) {
    emitter.emit("showMessage", {
      text: "فشل إضافة المراجعة. تأكد من إتمام الشراء.",
      color: "error",
    });
  } finally {
    reviewLoading.value = false;
  }
};

const increase = () => {
  if (quantity.value < productDetails.productDetails.stock) quantity.value++;
};
const decrease = () => {
  if (quantity.value > 1) quantity.value--;
};

const loadProductData = async () => {
  selectedImage.value = null;
  productDetails.productDetails = {};
  quantity.value = 1;
  await productDetails.getSingleProducts(route.params.productId);
  checkFavStatus();
};

const isItemInCart = (id) =>
  id && CartItem.CartItem.some((item) => item.id === id);

const toggleCart = (item) => {
  if (!localStorage.getItem("auth_token")) {
    window.location.hash = "#/Auth/LogIn";
    return;
  }
  if (isItemInCart(item.id)) {
    CartItem.removeFromCart(item.id);
  } else {
    loading.value = true;
    const product = { ...item, quantity: quantity.value };
    setTimeout(() => {
      CartItem.AddInCartItem(product);
      loading.value = false;
      emitter.emit("toggleCart");
      emitter.emit("showMessage", { text: product.title, color: "success" });
    }, 500);
  }
};

// SEO & Structured Data
const updateSEO = (product) => {
  if (!product || !product.id) return;

  const title = `${product.title} | ZadStore`;
  const description =
    product.description?.substring(0, 160) || "تسوق أفضل المنتجات من زاد ستور";
  const url = window.location.href;
  const image =
    product.thumbnail || (product.images?.[0] ? product.images[0] : "");

  // Update DOM
  document.title = title;
  const metaDesc = document.querySelector('meta[name="description"]');
  if (metaDesc) metaDesc.setAttribute("content", description);

  const updateMeta = (property, content) => {
    let el = document.querySelector(`meta[property="${property}"]`);
    if (el) el.setAttribute("content", content);
  };

  updateMeta("og:title", title);
  updateMeta("og:description", description);
  updateMeta("og:image", image);
  updateMeta("og:url", url);

  // JSON-LD Structured Data
  let script = document.getElementById("product-jsonld");
  if (!script) {
    script = document.createElement("script");
    script.id = "product-jsonld";
    script.type = "application/ld+json";
    document.head.appendChild(script);
  }

  const jsonLd = {
    "@context": "https://schema.org/",
    "@type": "Product",
    name: product.title,
    image: product.images || [product.thumbnail],
    description: product.description,
    brand: {
      "@type": "Brand",
      name:
        typeof product.brand === "object"
          ? product.brand.name
          : product.brand || "ZadStore",
    },
    offers: {
      "@type": "Offer",
      url: url,
      priceCurrency: "EGP",
      price: discountedPrice.value,
      availability:
        product.stock > 0
          ? "https://schema.org/InStock"
          : "https://schema.org/OutOfStock",
    },
    aggregateRating: product.rating
      ? {
          "@type": "AggregateRating",
          ratingValue: product.rating,
          reviewCount: product.reviews?.length || 1,
        }
      : undefined,
  };

  script.text = JSON.stringify(jsonLd);
};

onMounted(loadProductData);
watch(() => route.params.productId, loadProductData);
watch(
  () => productDetails.productDetails,
  (newVal) => {
    if (newVal && newVal.id) {
      updateSEO(newVal);
    }
  },
  { deep: true }
);
</script>

<style scoped>
.product-details-page {
  background: #fdfdfd;
  min-height: 100vh;
}

/* --- Breadcrumbs --- */
.pro-breadcrumbs {
  font-weight: 700;
  color: #888;
}

/* --- Image Gallery --- */
.sticky-gallery {
  position: sticky;
  top: 100px;
}

.main-image-wrapper {
  background: white;
  transition: all 0.3s ease;
}

.main-image {
  min-height: 500px;
}

.fav-btn {
  position: absolute;
  top: 20px;
  right: 20px;
  z-index: 10;
  border-radius: 12px;
}

.gallery-badges {
  position: absolute;
  top: 20px;
  left: 20px;
  z-index: 10;
}

.thumb-item {
  border-radius: 14px !important;
  border: 2px solid transparent;
  cursor: pointer;
  transition: 0.2s;
}

.thumb-active {
  border-color: rgb(var(--v-theme-primary)) !important;
}

/* --- Product Info --- */
.brand-text {
  letter-spacing: 1px;
  font-size: 14px;
}

.product-detail-title {
  font-size: var(--step-2);
  font-weight: 900;
  line-height: 1.2;
  color: #111;
}

.price-box {
  border: 1px solid rgba(0, 0, 0, 0.05);
  box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04);
}

.final-display-price {
  font-size: var(--step-3);
  font-weight: 900;
  color: rgb(var(--v-theme-primary));
}

.original-display-price {
  font-size: var(--step-0);
  color: #aaa;
  text-decoration: line-through;
}

.pro-section-label {
  font-weight: 900;
  font-size: 18px;
  color: #222;
}

.product-description-text {
  font-size: var(--step-0);
  color: #555;
  line-height: 1.8;
}

/* --- Status Dots --- */
.dot-success,
.dot-error {
  width: 10px;
  height: 10px;
  border-radius: 50%;
}
.dot-success {
  background: #2ecc71;
  box-shadow: 0 0 8px rgba(46, 204, 113, 0.4);
}
.dot-error {
  background: #ff4757;
}

/* --- Action Bar --- */
.qty-control {
  height: 64px;
  min-width: 160px;
}

.add-to-cart-big {
  font-size: 18px !important;
  letter-spacing: 0;
}

/* --- Features Grid --- */
.pro-features-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 15px;
}

.pro-feature-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 15px;
  background: #f9f9f9;
  border-radius: 16px;
  text-align: center;
}

.pro-feature-card span {
  font-size: 11px;
  font-weight: 800;
  color: #777;
}

/* --- Reviews --- */
.shadow-sm {
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04) !important;
}

.review-node {
  border: 1px solid #f0f0f0 !important;
}

.review-comment {
  font-size: 14px;
  line-height: 1.6;
}

.pro-textarea :deep(.v-field__input) {
  padding: 16px !important;
}

.w-fit {
  width: fit-content;
}

@media (max-width: 960px) {
  .product-detail-title {
    font-size: 24px;
  }
  .final-display-price {
    font-size: 32px;
  }
  .pro-features-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .main-image {
    min-height: 350px;
  }
}
</style>
