<template>
  <div
    class="cart-page bg-grey-lighten-4 py-10"
    :dir="locale === 'ar' ? 'rtl' : 'ltr'"
  >
    <v-container>
      <v-row>
        <v-col cols="12">
          <h2 class="text-h4 font-weight-black mb-5 text-grey-darken-4">
            {{ $t("cart.title", { count: cartItems.length }) }}
          </h2>
        </v-col>
      </v-row>

      <v-row v-if="cartItems.length === 0" justify="center" class="py-16">
        <v-col cols="12" md="6" class="text-center">
          <v-card class="pa-12 rounded-xl elevation-2 shadow-premium">
            <v-icon size="120" color="grey-lighten-2" class="mb-6"
              >mdi-cart-off</v-icon
            >
            <h3 class="text-h5 font-weight-bold text-grey-darken-2 mb-2">
              {{ $t("cart.empty_title") }}
            </h3>
            <p class="text-body-1 text-grey mb-8">
              {{ $t("cart.empty_subtitle") }}
            </p>
            <v-btn
              color="primary"
              to="/User/ProductsPage"
              height="55"
              class="px-12 rounded-lg font-weight-bold"
              elevation="4"
              prepend-icon="mdi-shopping"
            >
              {{ $t("cart.start_shopping") }}
            </v-btn>
          </v-card>
        </v-col>
      </v-row>

      <v-row v-else>
        <v-col cols="12" md="8">
          <v-card
            v-for="item in cartItems"
            :key="item.id"
            class="mb-4 pa-4 rounded-xl card-hover"
            elevation="1"
          >
            <v-row align="center">
              <v-col cols="4" sm="2">
                <v-hover v-slot="{ isHovering, props }">
                  <v-img
                    v-bind="props"
                    :src="item.thumbnail"
                    aspect-ratio="1"
                    cover
                    class="rounded-lg bg-grey-lighten-3 elevation-1"
                    :class="{ 'scale-up': isHovering }"
                  ></v-img>
                </v-hover>
              </v-col>

              <v-col cols="8" sm="5">
                <div class="text-h6 font-weight-black text-truncate">
                  {{ item.title }}
                </div>
                <div class="text-caption text-grey-darken-1 font-weight-bold">
                  {{ item.brand }}
                </div>
                <div class="d-flex align-center mt-2">
                  <span class="text-h6 text-primary font-weight-black"
                    >{{
                      Math.ceil(
                        item.price * (1 - (item.discountPercentage || 0) / 100)
                      )
                    }}
                    {{ $t("products.currency") }}</span
                  >
                </div>
              </v-col>

              <v-col
                cols="6"
                sm="3"
                class="d-flex align-center justify-center mt-3 mt-sm-0"
              >
                <div
                  class="qty-control d-flex align-center border-2 rounded-lg px-2 py-1 bg-white shadow-sm"
                >
                  <v-btn
                    icon="mdi-minus"
                    variant="text"
                    color="primary"
                    density="compact"
                    size="small"
                    @click="updateQuantity(item, -1)"
                    :disabled="item.quantity <= 1"
                  ></v-btn>
                  <span class="mx-4 font-weight-black text-body-1">{{
                    item.quantity
                  }}</span>
                  <v-btn
                    icon="mdi-plus"
                    variant="text"
                    color="primary"
                    density="compact"
                    size="small"
                    @click="updateQuantity(item, 1)"
                    :disabled="item.quantity >= (item.stock || 999)"
                  ></v-btn>
                </div>
              </v-col>

              <v-col
                cols="6"
                sm="2"
                class="d-flex align-center justify-end mt-3 mt-sm-0"
              >
                <v-btn
                  icon="mdi-delete-outline"
                  variant="tonal"
                  color="error"
                  class="rounded-lg"
                  @click="removeItem(item.id)"
                  :title="$t('cart.remove_item')"
                ></v-btn>
              </v-col>
            </v-row>
          </v-card>
        </v-col>

        <v-col cols="12" md="4">
          <v-card class="pa-6 rounded-xl position-sticky-top shadow-premium">
            <h3 class="text-h5 font-weight-black mb-6 border-b pb-2">
              {{ $t("cart.order_summary") }}
            </h3>

            <div class="d-flex justify-space-between mb-4 text-body-1">
              <span class="text-grey-darken-1">{{ $t("cart.subtotal") }}</span>
              <span class="font-weight-bold"
                >{{ cartTotal }} {{ $t("products.currency") }}</span
              >
            </div>

            <div class="d-flex justify-space-between mb-4 text-body-1">
              <span class="text-grey-darken-1">{{ $t("cart.shipping") }}</span>
              <v-chip
                v-if="freeShippingReached"
                size="small"
                color="success"
                variant="flat"
                >{{ $t("cart.free_shipping") }}</v-chip
              >
              <span v-else class="text-caption text-grey-darken-1">{{
                locale === "ar" ? "يُحسب عند الدفع" : "Calculated at checkout"
              }}</span>
            </div>

            <!-- Free Shipping Progress -->
            <div class="mt-4 mb-2">
              <div
                class="d-flex justify-space-between text-caption font-weight-bold mb-1"
                :class="freeShippingReached ? 'text-success' : 'text-primary'"
              >
                <span>{{
                  freeShippingReached
                    ? locale === "ar"
                      ? "تهانينا! لقد حصلت على شحن مجاني"
                      : "Congratulations! You get Free Shipping"
                    : locale === "ar"
                    ? `أضف ${amountNeededForFreeShipping} ${$t(
                        "products.currency"
                      )} للشحن المجاني`
                    : `Add ${amountNeededForFreeShipping} ${$t(
                        "products.currency"
                      )} for Free Shipping`
                }}</span>
              </div>
              <v-progress-linear
                :model-value="freeShippingProgress"
                :color="freeShippingReached ? 'success' : 'primary'"
                height="8"
                rounded
                striped
              ></v-progress-linear>
            </div>

            <v-divider class="my-6"></v-divider>

            <div class="d-flex justify-space-between mb-8">
              <span class="text-h5 font-weight-black">{{
                $t("cart.total_amount")
              }}</span>
              <span class="text-h5 font-weight-black text-primary"
                >{{ finalTotal }} {{ $t("products.currency") }}</span
              >
            </div>

            <v-btn
              block
              color="primary"
              height="60"
              class="rounded-lg text-h6 font-weight-bold elevation-4 mt-4"
              @click="handleCheckoutAction"
              :loading="isLoading"
              prepend-icon="mdi-credit-card-check"
            >
              {{ $t("cart.checkout_now") }}
            </v-btn>

            <div class="text-center mt-6">
              <v-icon
                size="small"
                color="success"
                icon="mdi-shield-check"
                class="me-2"
              ></v-icon>
              <span class="text-caption text-grey-darken-1 font-weight-bold">{{
                $t("cart.secure_shopping")
              }}</span>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from "vue";
import { useI18n } from "vue-i18n";
const { locale } = useI18n();
import { AddInCart } from "@/store/Cart";
import { useSettingsStore } from "@/store/Settings";
import { useRouter } from "vue-router";

// 1. Store Setup
const cartStore = AddInCart();
const settingsStore = useSettingsStore();
const cartItems = computed(() => cartStore.CartItem || []);
const router = useRouter();

onMounted(async () => {
  if (!settingsStore.freeShippingThreshold) {
    await settingsStore.fetchSettings();
  }
});

// 2. State Variables
const isLoading = ref(false);

// 3. Cart Actions
const updateQuantity = (item, amount) => {
  cartStore.AddInCartItem({ id: item.id, quantity: amount });
};

const removeItem = (id) => {
  cartStore.removeFromCart(id);
};

// 4. Checkout Logic
const handleCheckoutAction = () => {
  router.push("/User/AddOrder");
};

// 5. Calculations
const cartTotal = computed(() => {
  return cartItems.value.reduce(
    (total, item) =>
      total +
      Math.ceil(item.price * (1 - (item.discountPercentage || 0) / 100)) *
        item.quantity,
    0
  );
});

const finalTotal = computed(() => {
  return parseFloat(cartTotal.value).toFixed(2);
});

const freeShippingThreshold = computed(
  () => settingsStore.freeShippingThreshold || 0
);

const freeShippingProgress = computed(() => {
  if (freeShippingThreshold.value <= 0) return 100;
  return Math.min(100, (cartTotal.value / freeShippingThreshold.value) * 100);
});

const freeShippingReached = computed(() => freeShippingProgress.value >= 100);

const amountNeededForFreeShipping = computed(() => {
  const needed = freeShippingThreshold.value - cartTotal.value;
  return needed > 0 ? parseFloat(needed).toFixed(2) : 0;
});
</script>

<style scoped>
.cart-page {
  font-family: "Cairo", "Tajawal", sans-serif;
}
.qty-control {
  border: 2px solid #eee;
  transition: border-color 0.3s ease;
}
.qty-control:hover {
  border-color: rgb(var(--v-theme-primary));
}
.position-sticky-top {
  position: sticky !important;
  top: 100px;
  z-index: 10;
}
.shadow-premium {
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05) !important;
}
.card-hover {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card-hover:hover {
  transform: translateY(-4px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1) !important;
}
.scale-up {
  transform: scale(1.05);
  transition: transform 0.3s ease;
}
.border-2 {
  border-width: 2px !important;
}
.shadow-sm {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05) !important;
}
</style>
