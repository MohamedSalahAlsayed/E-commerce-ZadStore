<template>
  <div
    class="checkout-page bg-grey-lighten-4 py-8"
    :dir="locale === 'ar' ? 'rtl' : 'ltr'"
  >
    <v-container>
      <!-- العنوان العلوي -->
      <div class="mb-6 d-flex align-center justify-space-between">
        <h1 class="text-h4 font-weight-black text-primary-dark">
          {{ $t("checkout.title") }}
        </h1>
        <v-icon size="30" color="primary">mdi-lock</v-icon>
      </div>

      <v-row>
        <!-- العمود الأيمن (نموذج البيانات ومراجعة الطلب) -->
        <v-col cols="12" lg="8" md="7">
          <v-expansion-panels v-model="panel" multiple>
            <!-- 1. عنوان الشحن -->
            <v-expansion-panel
              value="address"
              elevation="1"
              class="mb-4 rounded-lg"
            >
              <v-expansion-panel-title class="text-h6 font-weight-bold">
                <span
                  class="step-number"
                  :class="locale === 'ar' ? 'me-3' : 'ml-3'"
                  >1</span
                >
                {{ $t("checkout.shipping_address") }}
              </v-expansion-panel-title>
              <v-expansion-panel-text class="pt-4 pb-4 bg-white">
                <v-form ref="orderForm" v-model="isFormValid">
                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="orderData.name"
                        :label="$t('checkout.full_name')"
                        variant="outlined"
                        density="comfortable"
                        :rules="[(v) => !!v || $t('checkout.required')]"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <PhoneInput
                        v-model="orderData.phone"
                        :label="$t('checkout.phone')"
                        :rules="[(v) => !!v || $t('checkout.required')]"
                      />
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="orderData.address"
                        :label="$t('checkout.detailed_address')"
                        variant="outlined"
                        density="comfortable"
                        :rules="[(v) => !!v || $t('checkout.required')]"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-select
                        v-model="orderData.governorate_id"
                        :items="governorates"
                        :item-title="locale === 'ar' ? 'name_ar' : 'name_en'"
                        item-value="id"
                        :label="$t('checkout.governorate')"
                        variant="outlined"
                        density="comfortable"
                        :rules="[(v) => !!v || $t('checkout.required')]"
                      ></v-select>
                    </v-col>

                    <v-col cols="12" md="6" v-if="orderData.governorate_id">
                      <v-select
                        v-model="orderData.shipping_method_id"
                        :items="shippingMethods"
                        :item-title="locale === 'ar' ? 'name_ar' : 'name_en'"
                        item-value="id"
                        :label="$t('checkout.shipping_method')"
                        variant="outlined"
                        density="comfortable"
                        :rules="[(v) => !!v || $t('checkout.required')]"
                        :hint="selectedMethod?.delivery_time"
                        persistent-hint
                      >
                        <template v-slot:item="{ props, item }">
                          <v-list-item
                            v-bind="props"
                            :subtitle="item.raw.delivery_time"
                          >
                            <template v-slot:append>
                              <span class="font-weight-bold text-primary"
                                >{{ item.raw.fee }}
                                {{ $t("products.currency") }}</span
                              >
                            </template>
                          </v-list-item>
                        </template>
                      </v-select>
                    </v-col>

                    <v-col cols="12" v-if="totalWeight > 0" class="pt-0">
                      <v-chip
                        size="small"
                        variant="tonal"
                        color="grey"
                        class="text-caption"
                      >
                        <v-icon start size="14">mdi-weight-kilogram</v-icon>
                        {{ $t("checkout.weight_total") }}
                        {{ totalWeight.toFixed(2) }} {{ $t("checkout.kg") }}
                      </v-chip>
                    </v-col>
                    <v-col cols="12">
                      <v-btn
                        color="primary"
                        @click="
                          async () => {
                            if (orderForm) {
                              const { valid } = await orderForm.validate();
                              if (valid) panel = ['payment'];
                            }
                          }
                        "
                        class="rounded-lg text-none px-6"
                        elevation="0"
                        >{{ $t("checkout.use_this_address") }}</v-btn
                      >
                    </v-col>
                  </v-row>
                </v-form>
              </v-expansion-panel-text>
            </v-expansion-panel>

            <!-- 2. طريقة الدفع -->
            <v-expansion-panel
              value="payment"
              elevation="1"
              class="mb-4 rounded-lg"
            >
              <v-expansion-panel-title class="text-h6 font-weight-bold">
                <span
                  class="step-number"
                  :class="locale === 'ar' ? 'me-3' : 'ml-3'"
                  >2</span
                >
                {{ $t("checkout.payment_method") }}
              </v-expansion-panel-title>
              <v-expansion-panel-text class="pt-4 pb-4 bg-white">
                <v-radio-group v-model="paymentMethod" column>
                  <v-radio value="cod" color="primary">
                    <template v-slot:label>
                      <div class="d-flex align-center">
                        <span
                          class="font-weight-medium text-body-1 text-black"
                          >{{ $t("checkout.cod_label") }}</span
                        >
                        <v-icon
                          color="success"
                          :class="locale === 'ar' ? 'ms-2' : 'ml-2'"
                          >mdi-cash</v-icon
                        >
                      </div>
                    </template>
                  </v-radio>
                  <v-radio value="card" color="primary">
                    <template v-slot:label>
                      <div class="d-flex align-center">
                        <span
                          class="font-weight-medium text-body-1 text-black"
                          >{{ $t("checkout.card_label") }}</span
                        >
                        <v-img
                          src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png"
                          width="40"
                          :class="locale === 'ar' ? 'ms-3' : 'ml-3'"
                        ></v-img>
                        <v-img
                          src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg"
                          width="30"
                          :class="locale === 'ar' ? 'ms-2' : 'ml-2'"
                        ></v-img>
                      </div>
                    </template>
                  </v-radio>
                </v-radio-group>

                <!-- محاكاة نموذج البطاقة -->
                <v-expand-transition>
                  <div
                    v-if="paymentMethod === 'card'"
                    class="pa-4 bg-grey-lighten-4 rounded-lg border mt-2"
                  >
                    <v-row>
                      <v-col cols="12">
                        <v-text-field
                          :label="$t('checkout.card_number')"
                          variant="outlined"
                          density="compact"
                          hide-details
                          prepend-inner-icon="mdi-credit-card-outline"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="6">
                        <v-text-field
                          :label="$t('checkout.expiry_date')"
                          placeholder="MM/YY"
                          variant="outlined"
                          density="compact"
                          hide-details
                        ></v-text-field>
                      </v-col>
                      <v-col cols="6">
                        <v-text-field
                          :label="$t('checkout.cvv')"
                          variant="outlined"
                          density="compact"
                          hide-details
                        ></v-text-field>
                      </v-col>
                    </v-row>
                  </div>
                </v-expand-transition>

                <v-btn
                  color="primary"
                  @click="panel = ['review']"
                  class="rounded-lg text-none px-6 mt-4"
                  elevation="0"
                  >{{ $t("checkout.use_payment_method") }}</v-btn
                >
              </v-expansion-panel-text>
            </v-expansion-panel>

            <!-- 3. مراجعة العناصر والشحن -->
            <v-expansion-panel value="review" elevation="1" class="rounded-lg">
              <v-expansion-panel-title class="text-h6 font-weight-bold">
                <span
                  class="step-number"
                  :class="locale === 'ar' ? 'me-3' : 'ml-3'"
                  >3</span
                >
                {{ $t("checkout.review_items") }}
              </v-expansion-panel-title>
              <v-expansion-panel-text class="pt-4 pb-4 bg-white">
                <div class="border rounded-lg pa-4 mb-4">
                  <h3
                    class="text-success font-weight-bold mb-3 text-subtitle-1"
                  >
                    {{ $t("checkout.expected_delivery") }}
                  </h3>

                  <div v-if="cartItems.length === 0" class="text-center py-4">
                    <v-icon color="grey" size="40">mdi-cart-off</v-icon>
                    <p class="mt-2 text-grey-darken-1">
                      {{ $t("checkout.empty_cart") }}
                    </p>
                  </div>

                  <v-list v-else lines="two" class="bg-transparent pa-0">
                    <v-list-item
                      v-for="(item, i) in cartItems"
                      :key="i"
                      class="px-0 border-bottom border-light pb-4 mb-4"
                    >
                      <template v-slot:prepend>
                        <v-img
                          :src="item.thumbnail"
                          width="100"
                          height="100"
                          class="rounded border bg-white me-4"
                          cover
                        ></v-img>
                      </template>
                      <v-list-item-title
                        class="font-weight-bold text-body-1 mb-1"
                        style="white-space: normal"
                        >{{ item.title }}</v-list-item-title
                      >
                      <v-list-item-subtitle
                        class="text-primary font-weight-black text-h6 mb-2 opacity-100"
                      >
                        {{
                          Math.ceil(
                            item.price *
                              (1 - (item.discountPercentage || 0) / 100)
                          )
                        }}
                        {{ $t("products.currency") }}
                      </v-list-item-subtitle>
                      <div class="d-flex align-center">
                        <span
                          class="text-body-2 text-grey-darken-2"
                          :class="locale === 'ar' ? 'me-2' : 'ml-2'"
                          >{{ $t("checkout.quantity") }}</span
                        >
                        <v-chip
                          size="small"
                          variant="flat"
                          color="grey-lighten-3"
                          class="font-weight-bold"
                          >{{ item.quantity }}</v-chip
                        >
                      </div>
                    </v-list-item>
                  </v-list>
                </div>
              </v-expansion-panel-text>
            </v-expansion-panel>
          </v-expansion-panels>
        </v-col>

        <!-- العمود الأيسر (ملخص الطلب) -->
        <v-col cols="12" lg="4" md="5">
          <v-card
            class="order-summary-card rounded-lg border bg-surface"
            elevation="0"
          >
            <v-card-text class="pa-5">
              <v-btn
                color="#FFC107"
                class="amazon-btn w-100 font-weight-bold mb-4"
                height="45"
                elevation="0"
                :loading="loading"
                :disabled="!isFormValid || cartItems.length === 0"
                @click="submitOrder"
              >
                {{ $t("checkout.confirm_order_btn") }}
              </v-btn>

              <p class="text-caption text-center text-grey-darken-1 mb-4">
                {{ $t("checkout.terms_note") }}
              </p>

              <v-divider class="mb-4"></v-divider>

              <h3 class="text-h6 font-weight-bold mb-4">
                {{ $t("cart.order_summary") }}
              </h3>

              <div class="d-flex align-center mb-4 mt-2">
                <v-text-field
                  v-model="couponCodeInput"
                  :label="$t('checkout.promo_code')"
                  hide-details
                  variant="outlined"
                  density="compact"
                ></v-text-field>
                <v-btn
                  color="grey-darken-3"
                  class="font-weight-bold ms-2 text-none"
                  height="40"
                  :loading="isVerifyingCoupon"
                  @click="applyCoupon"
                  >{{ $t("checkout.apply_btn") }}</v-btn
                >
              </div>

              <div class="d-flex justify-space-between mb-2">
                <span class="text-body-2">{{
                  $t("checkout.subtotal_items", { count: cartItems.length })
                }}</span>
                <span class="text-body-2 font-weight-bold"
                  >{{ subtotal }} {{ $t("products.currency") }}</span
                >
              </div>

              <div
                v-if="discountAmount > 0"
                class="d-flex justify-space-between mb-2 text-success font-weight-bold"
              >
                <span class="text-body-2">{{
                  $t("checkout.discount_amount")
                }}</span>
                <span class="text-body-2"
                  >-{{ discountAmount }} {{ $t("products.currency") }}</span
                >
              </div>

              <div class="d-flex justify-space-between mb-2">
                <span class="text-body-2">{{
                  $t("checkout.shipping_delivery")
                }}</span>
                <div class="d-flex flex-column align-end">
                  <span
                    class="text-body-2 font-weight-bold"
                    :class="{ 'text-success': isFreeShipping }"
                  >
                    <template v-if="isFreeShipping">
                      {{ locale === "ar" ? "شحن مجاني" : "Free Shipping" }}
                    </template>
                    <template v-else-if="selectedMethod">
                      {{ shippingCost }} {{ $t("products.currency") }}
                    </template>
                    <template v-else>
                      {{ $t("checkout.select_gov") }}
                    </template>
                  </span>
                  <span
                    v-if="
                      !isFreeShipping && settingsStore.freeShippingThreshold
                    "
                    class="text-caption text-grey"
                  >
                    {{
                      locale === "ar"
                        ? `أضف بقيمة ${
                            settingsStore.freeShippingThreshold - subtotal
                          } ج.م للحصول على شحن مجاني`
                        : `Add ${
                            settingsStore.freeShippingThreshold - subtotal
                          } EGP for free shipping`
                    }}
                  </span>
                </div>
              </div>

              <v-divider class="mb-4"></v-divider>

              <div class="d-flex justify-space-between align-center mb-1">
                <span class="text-h6 font-weight-black text-error">{{
                  $t("checkout.grand_total")
                }}</span>
                <span class="text-h5 font-weight-black text-error"
                  >{{ total }} {{ $t("products.currency") }}</span
                >
              </div>

              <!-- رسالة إضافية -->
              <div
                class="mt-4 pa-3 bg-amber-lighten-5 border-amber rounded text-caption"
              >
                <v-icon color="warning" size="small" class="me-1"
                  >mdi-alert-circle-outline</v-icon
                >
                {{ $t("checkout.review_warning") }}
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useI18n } from "vue-i18n";
const { locale, t } = useI18n();
import { useRouter } from "vue-router";
import { AddInCart } from "../../store/Cart";
import api from "../../axios";
import { useAuthStore } from "@/store/auth/LogIn";
import { useSettingsStore } from "@/store/Settings";
import { inject } from "vue";
import PhoneInput from "@/components/PhoneInput.vue";

const emitter = inject("emitter");

const router = useRouter();
const cartStore = AddInCart();
const authStore = useAuthStore();
const settingsStore = useSettingsStore();

const governorates = ref([]);
const shippingMethods = ref([]);
const activeCoupon = ref(null);
const couponCodeInput = ref("");
const discountAmount = ref(0);
const isVerifyingCoupon = ref(false);

const panel = ref(["address"]); // Open first panel by default
const isFormValid = ref(false);
const loading = ref(false);
const orderForm = ref(null);
const paymentMethod = ref("cod");

const orderData = ref({
  name: "",
  phone: "",
  address: "",
  governorate_id: null,
  shipping_method_id: null,
});

onMounted(async () => {
  if (cartStore.CartItem.length === 0) {
    emitter.emit("showMessage", {
      text: t("checkout.empty_cart_msg"),
      color: "warning",
    });
    router.push("/User/CartPage");
    return;
  }

  if (authStore.currentUser) {
    orderData.value.name = authStore.currentUser.name || "";
    orderData.value.phone = authStore.currentUser.phone || "";
    orderData.value.address = authStore.currentUser.address || "";
  }

  try {
    await settingsStore.fetchSettings();
    const govRes = await api.get("/governorates");
    governorates.value = govRes.data;
  } catch (e) {
    console.error(e);
  }
});

// Watch governorate selection to fetch methods
watch(
  () => orderData.value.governorate_id,
  async (newVal) => {
    if (!newVal) {
      shippingMethods.value = [];
      orderData.value.shipping_method_id = null;
      return;
    }
    try {
      const res = await api.get(`/shipping-methods?governorate_id=${newVal}`);
      shippingMethods.value = res.data;
      if (shippingMethods.value.length > 0) {
        orderData.value.shipping_method_id = shippingMethods.value[0].id;
      }
    } catch (err) {
      console.error("Error fetching methods:", err);
    }
  }
);

const applyCoupon = async () => {
  if (!couponCodeInput.value) return;
  isVerifyingCoupon.value = true;
  try {
    const res = await api.post("/apply-coupon", {
      code: couponCodeInput.value,
      subtotal: subtotal.value,
    });
    activeCoupon.value = res.data.coupon;
    discountAmount.value = res.data.discount_amount;
    emitter.emit("showMessage", {
      text: res.data.message,
      color: "success",
    });
  } catch (error) {
    emitter.emit("showMessage", {
      text: error.response?.data?.message || t("checkout.invalid_code"),
      color: "error",
    });
    activeCoupon.value = null;
    discountAmount.value = 0;
  } finally {
    isVerifyingCoupon.value = false;
  }
};

// Calculate items from cart
const cartItems = computed(() => cartStore.CartItem || []);

const subtotal = computed(() => {
  return cartItems.value.reduce((total, item) => {
    const discountedPrice = Math.ceil(
      item.price * (1 - (item.discountPercentage || 0) / 100)
    );
    return total + discountedPrice * item.quantity;
  }, 0);
});

const totalWeight = computed(() => {
  return cartItems.value.reduce((total, item) => {
    return total + (Number(item.weight_kg) || 0) * item.quantity;
  }, 0);
});

const selectedMethod = computed(() => {
  return (
    shippingMethods.value.find(
      (m) => m.id === orderData.value.shipping_method_id
    ) || null
  );
});

const isFreeShipping = computed(() => {
  return subtotal.value >= (settingsStore.freeShippingThreshold || 1000);
});

const shippingCost = computed(() => {
  if (!selectedMethod.value || isFreeShipping.value) return 0;

  let cost = Number(selectedMethod.value.fee);

  if (selectedMethod.value.is_weight_aware) {
    const extraWeight = Math.max(
      0,
      totalWeight.value - Number(selectedMethod.value.base_weight)
    );
    cost += extraWeight * Number(selectedMethod.value.extra_weight_fee);
  }

  return Math.ceil(cost);
});

const total = computed(
  () => Math.max(0, subtotal.value - discountAmount.value) + shippingCost.value
);

const submitOrder = async () => {
  if (!orderForm.value) {
    panel.value = ["address", ...panel.value];
    return;
  }

  const { valid } = await orderForm.value.validate();
  if (!valid) {
    if (!panel.value.includes("address")) {
      panel.value = ["address", ...panel.value];
    }
    return;
  }

  loading.value = true;

  const payload = {
    customer_name: orderData.value.name,
    phone: orderData.value.phone,
    address: orderData.value.address,
    governorate_id: orderData.value.governorate_id,
    shipping_method_id: orderData.value.shipping_method_id,
    coupon_code: activeCoupon.value ? activeCoupon.value.code : null,
    items: cartItems.value.map((item) => ({
      product_id: item.id,
      quantity: item.quantity,
    })),
    total_shipping_cost: shippingCost.value,
    total_weight: totalWeight.value,
  };

  try {
    const res = await api.post("/user/orders", payload);
    cartStore.clearCart();
    emitter.emit("showMessage", {
      text: t("checkout.order_success_msg"),
      color: "success",
    });
    router.push({
      path: "/User/OrderSuccess",
      query: { order_number: res.data.order.order_number },
    });
  } catch (error) {
    emitter.emit("showMessage", {
      text:
        error.response?.data?.message || t("checkout.login_required_checkout"),
      color: "error",
    });
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.checkout-page {
  min-height: 100vh;
}

.text-primary-dark {
  color: #111 !important;
}

.step-number {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  background-color: rgb(var(--v-theme-primary));
  color: white;
  border-radius: 4px;
  font-size: 16px;
}

.order-summary-card {
  border-color: #ddd !important;
}

.amazon-btn {
  background-color: #ffd814 !important;
  color: #0f1111 !important;
  border: 1px solid #fcd200 !important;
  border-radius: 8px !important;
  box-shadow: 0 2px 5px 0 rgba(213, 217, 217, 0.5) !important;
  text-transform: none;
}
.amazon-btn:hover {
  background-color: #f7ca00 !important;
  border-color: #f2c200 !important;
}

.border-light {
  border-color: #eee !important;
}
.border-amber {
  border: 1px solid #ffcc80 !important;
}

.opacity-100 {
  opacity: 1 !important;
}

:deep(.v-expansion-panel) {
  background-color: transparent !important;
}
:deep(.v-expansion-panel-title) {
  padding: 16px 24px;
}
</style>
