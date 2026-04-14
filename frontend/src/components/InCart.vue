<template>
  <div
    class="cart-container d-flex flex-column h-100"
    :dir="locale === 'ar' ? 'rtl' : 'ltr'"
  >
    <!-- Header -->
    <div
      class="cart-header px-4 py-4 d-flex align-center justify-space-between border-b"
    >
      <h3
        class="text-h6 font-weight-bold d-flex align-center gap-2 m-0 text-primary"
      >
        <v-icon color="primary" size="28">mdi-cart-outline</v-icon>
        <span>{{ locale === "ar" ? "عربة التسوق" : "Shopping Cart" }}</span>
        <v-chip
          size="small"
          color="primary"
          variant="flat"
          class="font-weight-bold ms-2 px-2"
        >
          {{ totalItems }}
        </v-chip>
      </h3>
      <v-btn
        icon
        variant="tonal"
        size="small"
        @click="$emit('closeDrawer')"
        color="grey-darken-2"
        elevation="0"
      >
        <v-icon size="20">mdi-close</v-icon>
      </v-btn>
    </div>

    <!-- Free Shipping Progress -->
    <div class="px-4 py-4 bg-grey-lighten-4 border-b">
      <div
        class="d-flex justify-space-between text-caption font-weight-bold mb-3"
      >
        <span v-if="freeShippingRemaining > 0" class="text-grey-darken-2">
          {{ locale === "ar" ? "أضف" : "Add" }}
          <strong class="text-error mx-1 font-weight-black"
            >{{ freeShippingRemaining }} ج.م</strong
          >
          {{ locale === "ar" ? "للحصول على شحن مجاني" : "for free shipping" }}
        </span>
        <span v-else class="text-success font-weight-black">
          🎉
          {{
            locale === "ar"
              ? "مبروك! لقد حصلت على شحن مجاني"
              : "Congrats! Free shipping unlocked"
          }}
        </span>
      </div>

      <!-- Interactive Progress Bar -->
      <div class="progress-wrap">
        <div class="progress-bg">
          <div
            class="progress-fill bg-primary"
            :style="`width: ${progressPercent}%`"
          ></div>
        </div>
        <div class="progress-icon" :style="iconPositionStyle">
          <v-icon
            size="22"
            :color="progressPercent === 100 ? 'success' : 'primary'"
            >mdi-truck-delivery</v-icon
          >
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-if="!CartItem.length"
      class="empty-cart d-flex flex-column align-center justify-center flex-grow-1 px-4"
    >
      <v-icon size="90" color="grey-lighten-2" class="mb-5"
        >mdi-cart-remove</v-icon
      >
      <h3 class="text-h6 text-grey-darken-2 font-weight-bold mb-2">
        {{ locale === "ar" ? "السلة فارغة" : "Your cart is empty" }}
      </h3>
      <p class="text-body-2 text-grey text-center mb-6">
        {{
          locale === "ar"
            ? "تبدو سلتك فارغة، اكتشف منتجاتنا المميزة وأضفها هنا!"
            : "Discover our premium products and add them here!"
        }}
      </p>
      <v-btn
        color="primary"
        rounded="lg"
        elevation="0"
        to="/User/ProductsPage"
        size="large"
        class="px-8 font-weight-bold"
        @click="$emit('closeDrawer')"
      >
        {{ locale === "ar" ? "متابعة التسوق" : "Continue Shopping" }}
      </v-btn>
    </div>

    <!-- Cart Items -->
    <div
      v-else
      class="cart-items flex-grow-1 overflow-y-auto px-4 py-5 bg-white"
    >
      <v-card
        v-for="item in CartItem"
        :key="item.id"
        class="cart-item-card mb-4 pa-3 d-flex align-center rounded-xl"
        elevation="0"
        border
      >
        <v-avatar
          rounded="lg"
          size="75"
          class="bg-grey-lighten-4"
          :class="locale === 'ar' ? 'ml-3' : 'mr-3'"
        >
          <v-img :src="item.thumbnail" cover></v-img>
        </v-avatar>

        <div class="item-details flex-grow-1 d-flex flex-column h-100">
          <div class="d-flex justify-space-between align-start">
            <h4
              class="text-body-2 font-weight-bold text-truncate"
              style="max-width: 160px; line-height: 1.4"
              :title="item.title"
            >
              {{ item.title }}
            </h4>
            <v-btn
              icon
              size="24"
              variant="text"
              color="grey-lighten-1"
              class="delete-btn"
              @click="removeItem(item.id)"
            >
              <v-icon size="18" color="error">mdi-trash-can-outline</v-icon>
            </v-btn>
          </div>

          <div class="text-primary font-weight-black mt-1 text-body-1">
            {{
              Math.ceil(item.price * (1 - (item.discountPercentage || 0) / 100))
            }}
            ج.م
          </div>

          <div class="d-flex align-center justify-space-between mt-auto pt-2">
            <!-- Quantity Control -->
            <div
              class="quantity-control d-flex align-center bg-grey-lighten-4 rounded-lg px-1 py-1"
            >
              <v-btn
                icon
                size="24"
                variant="text"
                color="grey-darken-2"
                @click="decrease(item)"
              >
                <v-icon size="16">mdi-minus</v-icon>
              </v-btn>
              <span
                class="mx-2 text-body-2 font-weight-bold"
                style="min-width: 16px; text-align: center"
                >{{ item.quantity }}</span
              >
              <v-btn
                icon
                size="24"
                variant="text"
                color="primary"
                @click="increase(item)"
              >
                <v-icon size="16">mdi-plus</v-icon>
              </v-btn>
            </div>

            <div class="text-caption text-grey-darken-1 font-weight-bold">
              {{
                Math.ceil(
                  item.price *
                    (1 - (item.discountPercentage || 0) / 100) *
                    item.quantity
                )
              }}
              ج.م
            </div>
          </div>
        </div>
      </v-card>
    </div>

    <!-- Footer Summary (Fixed Bottom) -->
    <div
      v-if="CartItem.length"
      class="cart-footer px-4 py-4 bg-surface border-t"
    >
      <div
        class="d-flex justify-space-between text-body-1 font-weight-bold mb-4"
      >
        <span class="text-grey-darken-2">{{
          locale === "ar" ? "الإجمالي الفرعي:" : "Subtotal:"
        }}</span>
        <span class="text-primary text-h5 font-weight-black"
          >{{ totalPrice }} ج.م</span
        >
      </div>

      <v-btn
        color="primary"
        block
        rounded="lg"
        to="/User/AddOrder"
        size="large"
        class="mb-3 font-weight-bold"
        elevation="0"
      >
        <v-icon :left="locale === 'en'" :right="locale === 'ar'" class="mx-2"
          >mdi-credit-card-outline</v-icon
        >
        {{ locale === "ar" ? "إتمام الطلب" : "Checkout" }}
      </v-btn>
      <v-btn
        variant="outlined"
        color="primary"
        block
        rounded="lg"
        size="large"
        class="font-weight-bold bg-white"
        to="/User/CartPage"
        @click="$emit('closeDrawer')"
      >
        {{ locale === "ar" ? "عـرض سلة التسوق" : "View Shopping Cart" }}
      </v-btn>
    </div>
  </div>
</template>

<script setup>
import { computed, defineEmits } from "vue";
import { useI18n } from "vue-i18n";
import { AddInCart } from "@/store/Cart";
import { useSettingsStore } from "@/store/Settings";

const { locale } = useI18n();
const cartStore = AddInCart();
const settingsStore = useSettingsStore();

// Using computed so we don't lose reactivity when Pinia overwrites the array during fetch!
const CartItem = computed(() => cartStore.CartItem);

// Threshold for free shipping
const FREE_SHIPPING_THRESHOLD = computed(
  () => settingsStore.freeShippingThreshold || 1000
);

// زيادة كمية
const increase = (item) => {
  item.quantity++;
};

// تقليل كمية
const decrease = (item) => {
  if (item.quantity > 1) {
    item.quantity--;
  }
};

// حذف منتج
const removeItem = (id) => {
  cartStore.removeFromCart(id);
};

// إجمالي عدد المنتجات
const totalItems = computed(() =>
  CartItem.value.reduce((acc, item) => acc + item.quantity, 0)
);

// إجمالي السعر
const totalPrice = computed(() =>
  CartItem.value.reduce(
    (acc, item) =>
      acc +
      Math.ceil(item.price * (1 - (item.discountPercentage || 0) / 100)) *
        item.quantity,
    0
  )
);

// Progress bar logic
const freeShippingRemaining = computed(() => {
  return Math.max(0, FREE_SHIPPING_THRESHOLD.value - totalPrice.value);
});

const progressPercent = computed(() => {
  return Math.min(
    100,
    (totalPrice.value / FREE_SHIPPING_THRESHOLD.value) * 100
  );
});

// Dynamic icon position style depending on language RTL/LTR
const iconPositionStyle = computed(() => {
  if (locale.value === "ar") {
    return `right: calc(${progressPercent.value}% - 14px)`;
  } else {
    return `left: calc(${progressPercent.value}% - 14px)`;
  }
});

defineEmits(["closeDrawer"]);
</script>

<style scoped>
/* Container full height */
.cart-container {
  height: 100vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

/* Custom Scrollbar for Cart Items */
.cart-items::-webkit-scrollbar {
  width: 6px;
}
.cart-items::-webkit-scrollbar-thumb {
  background-color: rgba(var(--v-theme-primary), 0.2);
  border-radius: 10px;
}
.cart-items::-webkit-scrollbar-track {
  background: transparent;
}

/* Card Hover Animation */
.cart-item-card {
  transition: all 0.3s ease;
  border-color: rgba(var(--v-theme-on-surface), 0.08) !important;
}
.cart-item-card:hover {
  border-color: rgba(var(--v-theme-primary), 0.3) !important;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05) !important;
}

/* Delete Button */
.delete-btn {
  opacity: 0.6;
  transition: opacity 0.2s;
}
.delete-btn:hover {
  opacity: 1;
}

/* Progress Bar Design */
.progress-wrap {
  position: relative;
  height: 24px;
  display: flex;
  align-items: center;
}

.progress-bg {
  width: 100%;
  height: 8px;
  background-color: rgba(var(--v-theme-on-surface), 0.1);
  border-radius: 10px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  border-radius: 10px;
  transition: width 0.8s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.progress-icon {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  transition: left 0.8s cubic-bezier(0.25, 0.8, 0.25, 1),
    right 0.8s cubic-bezier(0.25, 0.8, 0.25, 1);
  background-color: #fff;
  border-radius: 50%;
  padding: 4px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2;
}

.cart-footer {
  box-shadow: 0 -10px 20px -5px rgba(0, 0, 0, 0.05);
  z-index: 10;
}
</style>
