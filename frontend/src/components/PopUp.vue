<template>
  <div class="QuickVeiw">
    <v-dialog v-model="QuickVeiw.QuickVeiw" max-width="900" class="pro-dialog">
      <v-card class="rounded-xl overflow-hidden" elevation="0">
        <!-- Close icon -->
        <v-btn
          icon="mdi-close"
          variant="flat"
          color="white"
          size="small"
          class="close-btn elevation-2"
          @click="QuickVeiw.close()"
        ></v-btn>

        <v-container class="pa-0">
          <v-row v-if="productDetails.productDetails?.id" no-gutters>
            <!-- Left: Image Gallery -->
            <v-col cols="12" md="6" class="bg-grey-lighten-5 pa-6 pa-md-10">
              <div class="main-image-wrapper mb-6">
                <v-img
                  :src="tab ? tab : productDetails.productDetails.thumbnail"
                  class="main-product-image rounded-xl shadow-lg"
                  cover
                  aspect-ratio="1"
                >
                  <template v-slot:placeholder>
                    <div class="d-flex align-center justify-center fill-height">
                      <v-progress-circular
                        indeterminate
                        color="primary"
                      ></v-progress-circular>
                    </div>
                  </template>
                </v-img>
              </div>

              <v-slide-group
                v-model="tab"
                show-arrows
                center-active
                mandatory
                class="thumb-group"
              >
                <v-slide-group-item
                  v-for="(img, i) in productDetails.productDetails.images"
                  :key="i"
                  v-slot="{ isSelected, toggle }"
                  :value="img"
                >
                  <v-card
                    :class="[
                      'thumbnail-card mx-2',
                      isSelected ? 'selected-thumbnail' : '',
                    ]"
                    @click="toggle"
                    elevation="0"
                  >
                    <v-img :src="img" cover class="thumbnail-img"></v-img>
                  </v-card>
                </v-slide-group-item>
              </v-slide-group>
            </v-col>

            <!-- Right: Content -->
            <v-col cols="12" md="6" class="pa-6 pa-md-10 bg-white">
              <div class="product-content h-100 d-flex flex-column">
                <div class="category-chip mb-2">
                  {{ productDetails.productDetails.category || "المنتج" }}
                </div>
                <h1 class="text-h4 font-weight-black text-primary mb-4">
                  {{ productDetails.productDetails.title }}
                </h1>

                <div class="d-flex align-center gap-2 mb-6">
                  <v-rating
                    v-model="productDetails.productDetails.rating"
                    readonly
                    color="amber-darken-1"
                    density="compact"
                    half-increments
                    size="small"
                  ></v-rating>
                  <span class="text-caption text-grey font-weight-bold"
                    >({{ productDetails.productDetails.rating }})</span
                  >
                </div>

                <p
                  class="text-body-1 text-grey-darken-1 mb-8 line-height-relaxed"
                >
                  {{ productDetails.productDetails.description }}
                </p>

                <div class="mt-auto">
                  <div class="info-grid mb-8">
                    <div class="info-item">
                      <span class="label">الماركة:</span>
                      <span class="value">{{
                        productDetails.productDetails.brand || "غير محددة"
                      }}</span>
                    </div>
                    <div class="info-item">
                      <span class="label">المخزون:</span>
                      <span
                        class="value"
                        :class="
                          productDetails.productDetails.stock > 0
                            ? 'text-success'
                            : 'text-error'
                        "
                      >
                        {{
                          productDetails.productDetails.stock > 0
                            ? productDetails.productDetails.stock + " قطعة"
                            : "غير متوفر"
                        }}
                      </span>
                    </div>
                  </div>

                  <div
                    class="price-section mb-8 px-4 py-3 bg-grey-lighten-5 rounded-lg border"
                  >
                    <div class="d-flex align-end gap-3 mb-1">
                      <span class="text-h4 font-weight-black text-primary">
                        {{
                          Math.ceil(
                            (productDetails.productDetails.price -
                              productDetails.productDetails.price *
                                (productDetails.productDetails
                                  .discountPercentage /
                                  100)) *
                              quantity
                          ).toLocaleString()
                        }}
                        <small class="text-h6">ج.م</small>
                      </span>
                      <del
                        v-if="productDetails.productDetails.discountPercentage"
                        class="text-h6 text-grey opacity-60"
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
                      class="text-caption text-success font-weight-bold"
                      v-if="productDetails.productDetails.discountPercentage"
                    >
                      أنت توفر
                      {{
                        Math.ceil(
                          productDetails.productDetails.price *
                            (productDetails.productDetails.discountPercentage /
                              100) *
                            quantity
                        ).toLocaleString()
                      }}
                      ج.م
                    </div>
                  </div>

                  <div class="action-row d-flex align-center gap-4">
                    <div
                      class="qty-selector d-flex align-center bg-white border rounded-lg px-2"
                    >
                      <v-btn
                        icon="mdi-minus"
                        variant="text"
                        size="small"
                        @click="decrease"
                        :disabled="quantity <= 1"
                      ></v-btn>
                      <span class="mx-4 font-weight-black text-h6">{{
                        quantity
                      }}</span>
                      <v-btn
                        icon="mdi-plus"
                        variant="text"
                        size="small"
                        @click="increase"
                      ></v-btn>
                    </div>

                    <v-btn
                      :color="
                        isItemInCart(productDetails.productDetails?.id)
                          ? 'success'
                          : 'primary'
                      "
                      variant="flat"
                      height="56"
                      class="add-btn flex-grow-1 font-weight-black rounded-lg"
                      :loading="loading"
                      @click="toggleCart(productDetails.productDetails)"
                    >
                      <v-icon size="24" class="ml-2">
                        {{
                          isItemInCart(productDetails.productDetails?.id)
                            ? "mdi-cart-check"
                            : "mdi-cart-plus"
                        }}
                      </v-icon>
                      {{
                        isItemInCart(productDetails.productDetails?.id)
                          ? "تمت الإضافة"
                          : "أضف للسلة"
                      }}
                    </v-btn>
                  </div>
                </div>
              </div>
            </v-col>
          </v-row>

          <div v-else class="pa-10">
            <v-skeleton-loader type="image, article, button" />
          </div>
        </v-container>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref, watch, inject } from "vue";
import { usePopUpQuickView } from "@/store/PopUp";
import { ProductModule } from "@/store/Products";
import { AddInCart } from "@/store/Cart";

const QuickVeiw = usePopUpQuickView();
const productDetails = ProductModule();
const CartItem = AddInCart();
const loading = ref(false);
const emitter = inject("emitter");
const tab = ref("");
const quantity = ref(1);

const isItemInCart = (id) => {
  if (!id) return false;
  return CartItem.CartItem.some((item) => item.id === id);
};

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

const increase = () => quantity.value++;
const decrease = () => {
  if (quantity.value > 1) quantity.value--;
};

watch(
  () => QuickVeiw.productId,
  async (id) => {
    if (id) {
      productDetails.productDetails = null;
      tab.value = "";
      quantity.value = 1;
      await productDetails.getSingleProducts(id);
    }
  }
);
</script>

<style scoped>
.pro-dialog :deep(.v-overlay__content) {
  border-radius: 24px;
  overflow: hidden;
}

.close-btn {
  position: absolute;
  top: 15px;
  right: 15px;
  z-index: 10;
  border-radius: 12px;
}

/* --- Content Styling --- */
.category-chip {
  text-transform: uppercase;
  font-size: 11px;
  font-weight: 800;
  color: #999;
  letter-spacing: 1px;
}

.line-height-relaxed {
  line-height: 1.8;
}

/* --- Info Grid --- */
.info-grid {
  display: flex;
  gap: 24px;
}

.info-item {
  display: flex;
  flex-direction: column;
}

.info-item .label {
  font-size: 12px;
  color: #999;
  font-weight: 700;
}

.info-item .value {
  font-weight: 800;
  color: #111;
}

/* --- Image Section --- */
.main-image-wrapper {
  transition: transform 0.3s ease;
}

.thumbnail-card {
  width: 70px;
  height: 70px;
  border-radius: 12px !important;
  cursor: pointer;
  border: 2px solid transparent;
  transition: all 0.2s ease;
  overflow: hidden;
}

.selected-thumbnail {
  border-color: rgb(var(--v-theme-primary));
}

.shadow-lg {
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
}

/* --- Actions --- */
.qty-selector {
  height: 56px;
}

.add-btn {
  font-size: 18px !important;
  transition: all 0.2s ease;
}

.add-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 15px rgba(var(--v-theme-primary), 0.25);
}

.gap-2 {
  gap: 8px;
}
.gap-3 {
  gap: 12px;
}
.gap-4 {
  gap: 16px;
}

/* Placeholder fix */
:deep(.v-skeleton-loader) {
  background: transparent;
}
</style>
